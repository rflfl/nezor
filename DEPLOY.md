# Deploy — Laravel Cloud

Este documento orienta o deploy do Nezor na plataforma Laravel Cloud.

---

## Pré-requisitos

- Conta no [Laravel Cloud](https://cloud.laravel.com)
- Repositório GitHub conectado ao Laravel Cloud
- Banco MySQL provisionado (Hostinger)

---

## Configuração no Painel Laravel Cloud

### 1. Environment Variables

No painel do Laravel Cloud, configure as seguintes variáveis de ambiente:

```env
APP_NAME=Nezor
APP_ENV=production
APP_KEY=[GERAR_NOVO_VIA_PAINEL]
APP_DEBUG=false
APP_URL=https://nezor.laravel.cloud

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR

DB_CONNECTION=mysql
DB_HOST=[HOST_DO_MYSQL_HOSTINGER]
DB_PORT=3306
DB_DATABASE=u186341430_nezor_mvp_db
DB_USERNAME=u186341430_nezor
DB_PASSWORD=^wjX&A1L

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="suporte@nezor.com.br"
MAIL_FROM_NAME="${APP_NAME}"

SANCTUM_STATEFUL_DOMAINS=nezor.laravel.cloud
```

> **Importante:** Gere uma nova `APP_KEY` exclusiva para produção no painel do Laravel Cloud ou via `php artisan key:generate --show`.

### 2. Build Command

```bash
npm ci && npm run build
```

### 3. Deploy Command

```bash
php artisan migrate --force && php artisan db:seed --class=ProductionSeeder --force
```

### 4. PHP Version

- PHP 8.3 ou superior

### 5. Node.js Version

- Node.js 22 ou superior

---

## Primeiro Acesso

Após o deploy bem-sucedido, acesse:

- **URL:** https://nezor.laravel.cloud
- **E-mail:** admin@nezor.com
- **Senha:** nezor2026

> **Atenção:** Altere a senha imediatamente após o primeiro login em Configurações > Perfil.

---

## Comandos Úteis

### Rodar migrations manualmente
```bash
php artisan migrate --force
```

### Seed de produção
```bash
php artisan db:seed --class=ProductionSeeder --force
```

### Limpar cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Verificar status
```bash
php artisan migrate:status
```

---

## Troubleshooting

### Erro de conexão MySQL
- Verifique se o IP do servidor Laravel Cloud está na whitelist da Hostinger
- Confirme as credenciais em Variables > Database

### Assets não carregam (404)
- Verifique se o build command executou `npm run build`
- Confirme se `public/build` está sendo servido

### Erro 500
- Verifique os logs em Laravel Cloud > Logs
- Confirme se `APP_DEBUG=false` e `APP_KEY` está definida

---

*Documento gerado em 26/04/2025.*
