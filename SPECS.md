# SPECS - Nezor

## STACK TECNOLÓGICA

### Frontend
- **Framework/Library:** VueJs 3+
- **Linguagem:** TypeScript 5+
- **UI Library:** shadcn/vue
- **Styling:** Tailwind CSS 3.4+
- **State Management:** 
  - Pinia
- **Bibliotecas específicas para o projeto:**
  - Vue Router
  - VueUse
  - vee-validate + zod
  - dayjs
  - ApexCharts (dashboards)
  - TanStack Query para Vue

### Backend & Database
- **Database:** PostgreSQL
- **ORM:** Eloquent ORM
- **API:** Laravel 13
- **Realtime:** Laravel Reverb / broadcasting para atualização de agenda e caixa em tempo real
- **Storage:** Laravel Storage (para arquivos)

### Autenticação
- **Provider:** Laravel Jetstream
- **Features:** Organizations (multi-tenant), OAuth, Session Management
- **Sync:** contexto da organização sincronizado com sessão ativa e current_organization_id no usuário

### Email
- **Provider:** Resend
- **Templates:** Vue Email
- **Tipos:** boas-vindas, redefinição de senha, notificação de cadastro concluído, aviso de atendimento agendado, aviso de alteração de agenda, resumo financeiro diário

### Infraestrutura
- **Hosting:** Laravel Forge + DigitalOcean
- **Repository:** GitHub
- **CI/CD:** GitHub Actions + Laravel Envoy
- **Monitoring:** Laravel Telescope + Sentry (opcional)
- **Queue:** Redis + Laravel Horizon
- **Cache:** Redis

---

## ARQUITETURA MULTI-TENANT

### Estratégia: Scoped Queries + Middleware (Laravel Multi-Tenancy)

**Por quê?**
- Isolamento garantido no nível da aplicação
- Integração nativa com Eloquent
- Flexibilidade para diferentes estratégias (single DB, multi DB)
- Suporte a packages robustos como Spatie Laravel-Multitenancy

### Tenant Context Flow
```
Request → Auth Middleware → Identify Tenant → Global Scope → Query Scoped
```

**Implementação:**

#### Opção 1: Spatie Laravel-Multitenancy (Single Database)
```php
// config/multitenancy.php
return [
    'tenant_model' => App\Models\Organization::class,
    'tenant_finder' => App\Multitenancy\UserCurrentOrganizationTenantFinder::class,
];
```

#### Opção 2: Global Scope Manual
```php
// app/Models/Scopes/TenantScope.php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->check() && auth()->user()->current_organization_id) {
            $builder->where(
                $model->getTable() . '.organization_id',
                auth()->user()->current_organization_id
            );
        }
    }
}
```

```php
// app/Models/Concerns/BelongsToTenant.php
namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (auth()->check() && empty($model->organization_id)) {
                $model->organization_id = auth()->user()->current_organization_id;
            }
        });
    }
}
```

---

## SCHEMA DO BANCO DE DADOS (LARAVEL MIGRATIONS)

### Convenções
- Todas as tabelas multi-tenant têm `organization_id` (BIGINT UNSIGNED)
- Global Scopes em TODOS os models multi-tenant
- Soft deletes: `SoftDeletes` trait (`deleted_at TIMESTAMP`)
- Audit trail: `timestamps()` (`created_at`, `updated_at`)
- UUIDs ou Auto-increment IDs (configurável)
- Foreign keys com `onDelete('cascade')` ou `onDelete('restrict')`

### Migration: organizations
```php
// database/migrations/2026_01_01_000001_create_organizations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('auth_provider_id')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
```

### Model: Organization
```php
// app/Models/Organization.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'auth_provider_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
```

### Migration: users add current organization
```php
// database/migrations/2026_01_01_000002_add_current_organization_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_organization_id')->nullable()->after('id')
                ->constrained('organizations')->nullOnDelete();
            $table->string('phone', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_organization_id');
            $table->dropColumn('phone');
        });
    }
};
```

### Migration: organization_user
```php
// database/migrations/2026_01_01_000003_create_organization_user_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30);
            $table->timestamps();
            $table->unique(['organization_id', 'user_id']);
            $table->index(['organization_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
```

### Migration: professionals
```php
// database/migrations/2026_01_01_000004_create_professionals_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->decimal('default_commission_percentage', 5, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
```

### Migration: customers
```php
// database/migrations/2026_01_01_000005_create_customers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'name']);
            $table->index(['organization_id', 'phone']);
            $table->unique(['organization_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
```

### Migration: services
```php
// database/migrations/2026_01_01_000006_create_services_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->decimal('price', 10, 2);
            $table->decimal('professional_percentage', 5, 2);
            $table->decimal('salon_percentage', 5, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
```

### Migration: cash_registers
```php
// database/migrations/2026_01_01_000007_create_cash_registers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opened_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_amount', 10, 2)->default(0);
            $table->decimal('expected_amount', 10, 2)->default(0);
            $table->decimal('counted_amount', 10, 2)->nullable();
            $table->decimal('difference_amount', 10, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('closing_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
```

### Migration: appointments
```php
// database/migrations/2026_01_01_000008_create_appointments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('professional_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->date('appointment_date');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->decimal('service_price', 10, 2);
            $table->decimal('professional_percentage', 5, 2);
            $table->decimal('professional_amount', 10, 2);
            $table->decimal('salon_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'appointment_date']);
            $table->index(['organization_id', 'professional_id', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
```

### Migration: daily_service_entries
```php
// database/migrations/2026_01_01_000009_create_daily_service_entries_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_service_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('professional_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('cash_register_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('service_date');
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('professional_percentage', 5, 2);
            $table->decimal('professional_amount', 10, 2);
            $table->decimal('salon_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->enum('payment_method', ['cash', 'pix', 'card', 'mixed'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'service_date']);
            $table->index(['organization_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_service_entries');
    }
};
```

### Migration: cash_transactions
```php
// database/migrations/2026_01_01_000010_create_cash_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_register_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daily_service_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['income', 'expense', 'withdrawal', 'supply']);
            $table->enum('payment_method', ['cash', 'pix', 'card', 'mixed'])->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->timestamps();
            $table->index(['organization_id', 'type']);
            $table->index(['organization_id', 'cash_register_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
```

### Migration: monthly_expenses
```php
// database/migrations/2026_01_01_000011_create_monthly_expenses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->date('expense_date');
            $table->string('category');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'expense_date']);
            $table->index(['organization_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_expenses');
    }
};
```

### Model base multi-tenant example
```php
// app/Models/Customer.php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    use BelongsToTenant;

    protected $fillable = [
        'organization_id',
        'name',
        'phone',
        'email',
        'birth_date',
        'notes',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function entries()
    {
        return $this->hasMany(DailyServiceEntry::class);
    }
}
```

### Relationships principais
- Organization hasMany Professionals, Customers, Services, CashRegisters, Appointments, DailyServiceEntries, MonthlyExpenses
- Professional hasMany Appointments, DailyServiceEntries
- Customer hasMany Appointments, DailyServiceEntries
- Service hasMany Appointments, DailyServiceEntries
- CashRegister hasMany CashTransactions, DailyServiceEntries
- DailyServiceEntry belongsTo Customer, Professional, Service, CashRegister, Appointment

### Policies para autorização
```php
// app/Policies/CustomerPolicy.php
namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function view(User $user, Customer $customer): bool
    {
        return $user->current_organization_id === $customer->organization_id;
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->current_organization_id === $customer->organization_id;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->current_organization_id === $customer->organization_id
            && in_array($user->membershipRole(), ['owner', 'manager']);
    }
}
```

### Form Requests
```php
// app/Http/Requests/StoreCustomerRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
```

```php
// app/Http/Requests/StoreServiceRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'price' => ['required', 'numeric', 'min:0'],
            'professional_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'salon_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $professional = (float) $this->input('professional_percentage', 0);
            $salon = (float) $this->input('salon_percentage', 0);

            if (round($professional + $salon, 2) !== 100.00) {
                $validator->errors()->add('salon_percentage', 'A soma dos percentuais deve ser 100%.');
            }
        });
    }
}
```

```php
// app/Http/Requests/StoreDailyServiceEntryRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyServiceEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'professional_id' => ['required', 'integer', 'exists:professionals,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'cash_register_id' => ['nullable', 'integer', 'exists:cash_registers,id'],
            'service_date' => ['required', 'date'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'professional_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'payment_status' => ['required', 'in:pending,paid'],
            'payment_method' => ['nullable', 'in:cash,pix,card,mixed'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
```

### Controllers principais
```php
// app/Http/Controllers/Api/CustomerController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::query()->latest()->paginate(20);
    }

    public function store(StoreCustomerRequest $request)
    {
        return Customer::create($request->validated());
    }
}
```

```php
// app/Http/Controllers/Api/DashboardController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\DailyServiceEntry;
use App\Models\MonthlyExpense;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = Carbon::today();
        $startMonth = $today->copy()->startOfMonth();
        $endMonth = $today->copy()->endOfMonth();

        $dayRevenue = DailyServiceEntry::whereDate('service_date', $today)
            ->where('payment_status', 'paid')
            ->sum('gross_amount');

        $monthRevenue = DailyServiceEntry::whereBetween('service_date', [$startMonth, $endMonth])
            ->where('payment_status', 'paid')
            ->sum('gross_amount');

        $monthCommission = DailyServiceEntry::whereBetween('service_date', [$startMonth, $endMonth])
            ->sum('professional_amount');

        $monthExpenses = MonthlyExpense::whereBetween('expense_date', [$startMonth, $endMonth])
            ->sum('amount');

        return response()->json([
            'day_revenue' => $dayRevenue,
            'month_revenue' => $monthRevenue,
            'month_commission' => $monthCommission,
            'month_expenses' => $monthExpenses,
            'month_profit' => $monthRevenue - $monthCommission - $monthExpenses,
            'open_cash_register' => CashRegister::where('status', 'open')->latest('opened_at')->first(),
        ]);
    }
}
```

---

## DESIGN SYSTEM

### Cores (Tailwind Config)
```javascript
colors: {
  primary: '#01696F',
  secondary: '#F3F0EC',
  accent: '#D19900',
  success: '#437A22',
  warning: '#964219',
  danger: '#A12C7B',
  text: '#28251D',
  muted: '#7A7974',
  border: '#D4D1CA',
  surface: '#F9F8F5',
}
```

### Typography
- Headings: Inter Tight, semibold, tracking -0.02em
- Body: Inter, regular 16px
- Small: Inter 14px para labels, badges e metadados

### Componentes Base (shadcn/vue)
- Button
- Card
- Dialog
- Drawer
- Input
- Label
- Select
- Combobox
- Table
- Tabs
- Badge
- Calendar
- Popover
- Tooltip
- Dropdown Menu
- Sheet
- Toaster
- Alert Dialog

### Componentes específicos do produto
- AppointmentCalendar
- CustomerQuickCreateDialog
- ServiceEntryForm
- CashRegisterOpenDialog
- CashRegisterCloseDialog
- ProfessionalCommissionCard
- MonthlyProfitSummary
- PaymentMethodSplitBadge

### Exemplo de componente Vue.js + Composition API
```vue
<script setup lang="ts">
import { computed, ref } from 'vue'

const grossAmount = ref(100)
const professionalPercentage = ref(40)

const professionalAmount = computed(() => {
  return Number((grossAmount.value * professionalPercentage.value / 100).toFixed(2))
})

const salonAmount = computed(() => {
  return Number((grossAmount.value - professionalAmount.value).toFixed(2))
})
</script>

<template>
  <div class="grid gap-4 md:grid-cols-3">
    <div class="rounded-lg border p-4">
      <p class="text-sm text-muted-foreground">Valor do serviço</p>
      <p class="text-2xl font-semibold">R$ {{ grossAmount.toFixed(2) }}</p>
    </div>
    <div class="rounded-lg border p-4">
      <p class="text-sm text-muted-foreground">Profissional</p>
      <p class="text-2xl font-semibold">R$ {{ professionalAmount.toFixed(2) }}</p>
    </div>
    <div class="rounded-lg border p-4">
      <p class="text-sm text-muted-foreground">Salão</p>
      <p class="text-2xl font-semibold">R$ {{ salonAmount.toFixed(2) }}</p>
    </div>
  </div>
</template>
```

---

## SEGURANÇA

### Checklist
✅ Global Scopes (TenantScope) em models críticos
✅ Policies validam acesso por organization_id
✅ Middleware de Auth/Tenancy em rotas protegidas
✅ Form Requests validam inputs
✅ Variáveis de ambiente seguras
✅ CORS & Sanctum configurados
✅ Rate limiting (Laravel Throttle)

### Regras adicionais
- Apenas owner e manager podem fechar caixa com divergência.
- Apenas owner pode alterar percentuais de comissão após criação do serviço.
- Apenas usuários da mesma organização podem ver clientes, profissionais e relatórios.
- Soft delete aplicado em entidades estratégicas para recuperação operacional.
- Auditoria obrigatória para atualização de valor de serviço, edição de lançamento pago e fechamento de caixa.

---

## PERFORMANCE

### Otimizações
- Índices compostos por `organization_id` + colunas de busca frequente
- Paginação padrão nas listagens de clientes e lançamentos
- Caching do dashboard por 30 segundos com invalidação em eventos críticos
- Lazy loading da agenda semanal/mensal
- Jobs em fila para envio de e-mails e resumos diários
- Query optimization com eager loading em relacionamentos da agenda
- Broadcast apenas de eventos essenciais no módulo realtime

### Metas
- First Contentful Paint: < 1.5s
- Time to Interactive: < 3s
- Lighthouse Score: > 90

---

## GITHUB WORKFLOW

### Branch Strategy
```
main (produção)
  └── develop (staging)
       └── feature/* (desenvolvimento)
```

### CI/CD Pipeline

```yaml
# .github/workflows/main.yml
name: CI/CD

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  build:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_DB: nezor_test
          POSTGRES_USER: postgres
          POSTGRES_PASSWORD: postgres
        ports:
          - 5432:5432
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - uses: actions/setup-node@v4
        with:
          node-version: '22'
      - run: cp .env.example .env
      - run: composer install --no-interaction --prefer-dist
      - run: npm ci
      - run: php artisan key:generate
      - run: php artisan migrate --force
      - run: npm run lint
      - run: npm run build
      - run: php artisan test
```

---

## DEPLOY CHECKLIST

**Variáveis de Ambiente:**
- APP_NAME=Nezor
- APP_ENV=production
- APP_URL=https://app.nezor.com.br
- DB_CONNECTION=pgsql
- DB_HOST=127.0.0.1
- DB_PORT=5432
- DB_DATABASE=nezor
- DB_USERNAME=nezor
- DB_PASSWORD=senha_segura
- REDIS_HOST=127.0.0.1
- QUEUE_CONNECTION=redis
- CACHE_STORE=redis
- SESSION_DRIVER=redis
- MAIL_MAILER=resend
- RESEND_KEY=...
- SENTRY_LARAVEL_DSN=...
- BROADCAST_CONNECTION=reverb
- REVERB_APP_KEY=...
- REVERB_APP_SECRET=...
- REVERB_APP_ID=...

**Passos:**
1. Provisionar servidor com Laravel Forge ou stack Docker.
2. Configurar variáveis de ambiente (DB, Mail, Redis).
3. Configurar Pipeline CI/CD (GitHub Actions).
4. Configurar domínios e SSL.
5. Executar Migrations e Seeders.
6. Configurar Queue Workers (Horizon).

---

## ROADMAP TÉCNICO

**Fase 1 - MVP (Semana 1-2):**
- Setup da infraestrutura Laravel + Vue + PostgreSQL
- Multi-tenancy com organizations
- Autenticação e papéis básicos
- CRUD de clientes, profissionais e serviços
- Agenda e lançamentos diários
- Caixa diário
- Dashboard inicial com lucro mensal

**Fase 2 - Melhorias (Semana 3-4):**
- Relatórios por profissional e período
- Fechamento de caixa com validações avançadas
- Broadcast realtime na agenda
- E-mails transacionais
- Testes, otimizações e hardening de segurança

**Fase 3 - Lançamento (Semana 5+):**
- Documentação final
- Monitoramento e analytics
- Feedback loop
- Base para futuras features: estoque, fidelidade e fiscal
