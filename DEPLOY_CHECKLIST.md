# Checklist de Deploy — Laravel Cloud

## ✅ Pré-requisitos Confirmados

- [x] Repositório GitHub conectado ao Laravel Cloud
- [x] Código empurrado para `main`
- [x] Build do frontend funcionando (`npm run build`)
- [x] Testes de API aprovados (52/52)
- [x] Banco MySQL configurado na Hostinger

---

## 🚀 Passos no Painel Laravel Cloud

### 1. Criar Projeto
1. Acesse https://cloud.laravel.com
2. Clique em "Create Project"
3. Selecione o repositório `rflfl/nezor`
4. Branch: `main`

### 2. Configurar Ambiente

#### PHP Settings
- **PHP Version:** 8.3
- **Node.js Version:** 22

#### Build Command
```bash
npm install && npm run build
```

> **Nota:** Usamos `npm install` em vez de `npm ci` porque o ambiente do Laravel Cloud pode precisar instalar dependências nativas de plataforma (ex: `@emnapi/*`) que não estão no lock file gerado localmente.

#### Deploy Command (Release Command)
```bash
php artisan migrate --force && php artisan db:seed --class=ProductionSeeder --force
```

### 3. Configurar Variáveis de Ambiente

No painel Laravel Cloud, vá em **Environment** e adicione:

```
APP_NAME=Nezor
APP_ENV=production
APP_KEY=[GERAR_NOVO_VIA_PAINEL — CLIQUE EM "GENERATE"]
APP_DEBUG=false
APP_URL=https://nezor.laravel.cloud

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR

DB_CONNECTION=mysql
DB_HOST=[IP_OU_HOSTNAME_DO_MYSQL_HOSTINGER]
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

> **Importante sobre APP_KEY:** NUNCA use a chave que está no `.env` local. Sempre gere uma nova no painel do Laravel Cloud.

### 4. Configurar Banco de Dados Externo

Como estamos usando MySQL da Hostinger (não o banco gerenciado do Laravel Cloud):

1. Vá em **Database** ou **Services**
2. Selecione "External Database"
3. Preencha com os dados da Hostinger:
   - Host: `srv1234.hstgr.io` (ou o que a Hostinger forneceu)
   - Port: `3306`
   - Database: `u186341430_nezor_mvp_db`
   - Username: `u186341430_nezor`
   - Password: `^wjX&A1L`

4. **Whitelist:** No painel da Hostinger, adicione o IP do servidor Laravel Cloud à whitelist de acesso remoto.

### 5. Configurar Domínio

1. Vá em **Domains**
2. O domínio padrão será algo como `nezor.laravel.cloud` (já configurado)
3. Se tiver um domínio próprio, adicione e configure o DNS

### 6. Fazer Deploy

1. Clique em **Deploy** ou aguarde o deploy automático
2. Acompanhe os logs em tempo real
3. Verifique se o comando `migrate` e `seed` executaram com sucesso

---

## 🔍 Validação Pós-Deploy

Após o deploy bem-sucedido, verifique:

### Testar Login
- Acesse: `https://nezor.laravel.cloud/login`
- E-mail: `admin@nezor.com`
- Senha: `nezor2026`
- Se logar com sucesso: ✅ OK

### Testar Funcionalidades
- [ ] Dashboard carrega com gráficos
- [ ] Cadastrar um cliente
- [ ] Cadastrar um serviço
- [ ] Cadastrar um profissional
- [ ] Abrir o caixa
- [ ] Lançar um atendimento
- [ ] Fechar o caixa
- [ ] Página de Suporte funciona

### Verificar Erros
- [ ] Sem erros 500
- [ ] Sem erros de conexão com banco
- [ ] Assets (CSS/JS) carregam corretamente

---

## 🛠 Troubleshooting

### Erro: "Connection refused" ao conectar no MySQL
**Solução:** Adicione o IP do servidor Laravel Cloud à whitelist da Hostinger.

### Erro: "APP_KEY missing"
**Solução:** No painel Laravel Cloud, vá em Environment → clique em "Generate" ao lado de APP_KEY.

### Erro: "Table not found"
**Solução:** O comando de deploy não rodou as migrations. Vá em "Run Command" e execute:
```bash
php artisan migrate --force
```

### Erro: "Whoops, looks like something went wrong"
**Solução:** Verifique se `APP_DEBUG=false` e consulte os logs em Laravel Cloud > Logs.

### Assets não carregam (CSS/JS)
**Solução:** Verifique se o build command executou sem erros. Verifique se `public/build` existe nos logs.

---

## 📞 Próximos Passos

Após o deploy bem-sucedido:

1. **Alterar senha do admin:** Acesse Perfil > Alterar Senha
2. **Cadastrar dados reais:** Serviços, profissionais e clientes
3. **Configurar e-mail (opcional):** Para recuperação de senha, configure Resend ou SMTP
4. **Monitoramento:** Acompanhe logs e performance no painel Laravel Cloud

---

*Checklist gerado em 26/04/2025.*
*Código commitado em: https://github.com/rflfl/nezor/commit/c946fb34*
