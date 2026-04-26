# Histórico Técnico — Nezor

> Controle interno de atualizações, alterações e decisões arquiteturais do projeto.

---

## [0.1.0] — 2025-04-25

### Commit principal
`ad1d1959` — feat: first commit  
`d5c422e1` — chore: remove workflows from codebase

### Resumo
Primeira versão funcional do sistema Nezor com base no Laravel 13 + Vue 3, migrada do template padrão `laravel/laravel` para a estrutura de negócio do salão de beleza. O repositório foi publicado em https://github.com/rflfl/nezor.git e o `README.md` foi reescrito para refletir o escopo do produto.

---

### Infraestrutura & Configuração

| Alteração | Descrição |
|-----------|-----------|
| Laravel Jetstream (Livewire/Inertia) | Adicionado para autenticação, organizações (multi-tenancy) e gerenciamento de tokens de API. |
| Laravel Fortify | Provider de autenticação backend com suporte a 2FA, recuperação de senha e verificação de e-mail. |
| Sanctum | Instalado para autenticação de SPA e tokens de API. |
| Tailwind CSS + PostCSS | Configurado com paleta de cores customizada conforme design system (primary `#01696F`, accent `#D19900`, etc.). |
| Vite | Build tool mantido; aliases `@` e `ziggy` configurados. |
| GitHub Workflows | Removidos `issues.yml`, `pull-requests.yml`, `tests.yml` e `update-changelog.yml` do repositório remoto para evitar conflitos de permissão com PAT. Arquivos preservados localmente em `.github/workflows/`. |

### Banco de Dados

| Tabela | Objetivo |
|--------|----------|
| `organizations` | Tenant raiz; cada salão é uma organização. |
| `users` | Usuários do sistema com `current_organization_id` e campos de 2FA. |
| `organization_user` | Relação N:N entre usuários e organizações com papel (`role`). |
| `professionals` | Profissionais do salão com percentual padrão de comissão. |
| `customers` | Clientes com telefone único por organização. |
| `services` | Catálogo de serviços com preço e percentuais de divisão (profissional/salão). |
| `appointments` | Agendamentos com status (`scheduled`, `confirmed`, `in_progress`, `completed`, `cancelled`, `no_show`). |
| `cash_registers` | Caixa diário com abertura, fechamento e cálculo de diferença. |
| `daily_service_entries` | Lançamentos rápidos de atendimentos do dia (encaixes e walk-ins). |
| `cash_transactions` | Movimentações financeiras vinculadas ao caixa (entrada, saída, sangria, suprimento). |
| `monthly_expenses` | Despesas operacionais mensais para cálculo de lucro. |

**Convenções aplicadas:**
- Todas as tabelas multi-tenant possuem `organization_id` com índice composto.
- `SoftDeletes` em todas as entidades de negócio.
- Foreign keys com `cascadeOnDelete` ou `restrictOnDelete` conforme criticidade.

### Backend — API & Regras

| Controller | Responsabilidade |
|------------|------------------|
| `Api\DashboardController` | Consolidado de indicadores: faturamento do dia, faturamento do mês, comissões, despesas, lucro mensal e caixa aberto. |
| `Api\CustomerController` | CRUD de clientes com paginação e busca por nome/telefone. |
| `Api\ServiceController` | CRUD de serviços com validação customizada (soma dos percentuais deve ser 100%). |
| `Api\ProfessionalController` | CRUD de profissionais. |
| `Api\AppointmentController` | CRUD de agendamentos com cálculo automático de comissão por atendimento. |
| `Api\DailyServiceEntryController` | Lançamentos diários com vínculo opcional a caixa e agendamento. |
| `Api\CashRegisterController` | Abertura, fechamento e transações de caixa. |

**Segurança:**
- Policies baseadas em `organization_id` para todos os recursos.
- Global Scope (`BelongsToTenant`) aplica filtro automático por `current_organization_id` em todos os models multi-tenant.
- Form Requests validam regras de negócio (ex: percentuais de comissão, datas, relacionamentos obrigatórios).

### Frontend — Vue 3 + Inertia

| Página/Componente | Função |
|-------------------|--------|
| `Dashboard.vue` | Cards de KPI (atendimentos do dia, caixa, faturamento, comissões, lucro estimado). |
| `Appointments.vue` | Calendário interativo com CRUD de agendamentos. |
| `Customers.vue` | Listagem e cadastro rápido de clientes. |
| `Services.vue` | Catálogo de serviços com cálculo de percentuais. |
| `Professionals.vue` | Gestão de profissionais e comissões padrão. |
| `DailyEntries.vue` | Lançamentos rápidos de atendimentos do dia. |
| `CashRegister.vue` | Controle de caixa (abertura, fechamento, transações). |
| `Reports.vue` | Relatórios financeiros por período com gráficos. |

**Bibliotecas adicionadas:**
- `vue-router` (via Inertia), `pinia` (state management), `dayjs` (datas), `apexcharts` (gráficos).
- Componentes UI baseados em shadcn/vue (`Button`, `Card`, `Dialog`, etc.).
- Composables `useApi.js` (axios centralizado) e `useMask.js` (máscaras de telefone, CPF, CNPJ, moeda).

### Testes

| Suite | Cobertura |
|-------|-----------|
| `tests/Feature/AuthenticationTest.php` | Login, registro, logout, 2FA. |
| `tests/Feature/EmailVerificationTest.php` | Verificação de e-mail. |
| `tests/Feature/PasswordResetTest.php` | Recuperação de senha. |
| `tests/Feature/ProfileInformationTest.php` | Atualização de perfil. |
| `tests/Feature/ApiTokenPermissionsTest.php` | Permissões de tokens Sanctum. |
| `tests/Feature/BrowserSessionsTest.php` | Encerramento de sessões ativas. |

### Decisões Técnicas Relevantes

1. **Multi-tenancy por Scoped Queries**  
   Optou-se por Global Scope manual (`TenantScope`) + trait `BelongsToTenant` em vez de packages de terceiros. Isso garante controle total sobre o isolamento de dados e integração nativa com Eloquent, mantendo a flexibilidade para futura migração para múltiplos bancos de dados.

2. **Daily Service Entries separado de Appointments**  
   Criou-se a tabela `daily_service_entries` além de `appointments` para atender o fluxo operacional real do balcão: muitos atendimentos são encaixes ou walk-ins que não passam pela agenda prévia. Isso evita poluir o calendário com registros de última hora.

3. **Cálculo de comissão duplicado no banco**  
   Os campos `professional_amount` e `salon_amount` são persistidos em `appointments` e `daily_service_entries` (não calculados on-the-fly). Isso garante auditoria histórica mesmo se o percentual do serviço for alterado posteriormente.

4. **Remoção dos workflows do GitHub**  
   Os workflows padrão do Laravel foram removidos do commit inicial porque o token de acesso pessoal (PAT) utilizado não possui escopo `workflow`. Eles podem ser re-adicionados futuramente com um token adequado ou via GitHub App.

---

## Próximos Passos Planejados

- [ ] Configurar CI/CD real com GitHub Actions (requer PAT com escopo `workflow`).
- [ ] Implementar broadcasting real-time (Laravel Reverb) para agenda e caixa.
- [ ] Adicionar envio de e-mails transacionais (Resend + Vue Email).
- [ ] Criar seeders dedicados para demo/staging.
- [ ] Escrever testes de integração para os controllers de API.

---

*Documento gerado automaticamente em 25/04/2025.*
