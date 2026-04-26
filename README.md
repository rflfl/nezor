# Nezor

Plataforma SaaS de gestão para salão de beleza focada em operação diária, agenda, caixa, clientes, serviços, comissões e visão financeira do negócio.

## Stack

- **Frontend:** Vue 3 + TypeScript + Tailwind CSS + shadcn/vue + Pinia
- **Backend:** Laravel 13 + PostgreSQL + Eloquent ORM
- **Autenticação:** Laravel Jetstream (multi-tenancy)
- **Email:** Resend
- **Infraestrutura:** Laravel Forge + DigitalOcean
- **Realtime:** Laravel Reverb

## Funcionalidades

- **Agenda:** Calendário diário, semanal e mensal com status de atendimento
- **Clientes:** Cadastro, busca e histórico de serviços
- **Serviços:** Catálogo com valores e percentuais de comissão
- **Caixa:** Abertura, fechamento e controle de entradas/saídas
- **Comissões:** Cálculo automático por profissional
- **Relatórios:** Faturamento, despesas e lucro mensal
- **Dashboard:** Visão consolidada do dia e mês

## Requisitos

- PHP 8.3+
- Node.js 22+
- PostgreSQL 16+
- Redis

## Instalação

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
npm run build
```

## Variáveis de Ambiente

```env
APP_NAME=Nezor
DB_CONNECTION=pgsql
REDIS_HOST=127.0.0.1
QUEUE_CONNECTION=redis
MAIL_MAILER=resend
BROADCAST_CONNECTION=reverb
```

## Arquitetura Multi-Tenant

Dados isolados por `organization_id` com Global Scopes em todos os models multi-tenant. Usuários pertencem a organizações e consultas são automaticamente filtradas pelo contexto da sessão.

## Segurança

- Autenticação via Jetstream
- Policies de autorização por organization_id
- Middleware de tenancy
- Validação com Form Requests
- Soft deletes em entidades estratégicas

## Licença

MIT