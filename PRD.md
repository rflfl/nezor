# PRD - Nezor

## 1. VISÃO DO PRODUTO
Nezor é uma plataforma SaaS de gestão para salão de beleza focada em operação diária, agenda, caixa, clientes, serviços, comissões e visão financeira do negócio. O produto foi desenhado para digitalizar a rotina do salão com lançamento rápido de atendimentos, cálculo automático da parte da profissional e da parte do salão, além do acompanhamento do lucro mensal.

## 2. OBJETIVOS DE NEGÓCIO
- Digitalizar a operação diária do salão com agenda, cadastro de clientes, lançamento de serviços e fechamento de caixa.
- Reduzir erros manuais no cálculo de comissões, faturamento e resultado financeiro mensal.
- Criar uma base confiável para expansão futura com multi-tenancy, múltiplas unidades e relatórios gerenciais.

## 3. PERSONAS
### Proprietária do Salão
- Administra o salão e precisa ter visão clara do faturamento, comissões e lucro.
- Quer registrar tudo em um único sistema, sem depender de caderno ou planilhas.
- Necessidade principal: saber exatamente quanto entra, quanto paga para cada profissional e quanto sobra para o negócio ao final do mês.

### Recepcionista
- Faz atendimento no balcão e organiza a agenda das clientes.
- Precisa lançar rapidamente clientes, serviços e pagamentos ao longo do dia.
- Necessidade principal: operar a agenda e o caixa com poucos cliques.

### Profissional do Salão
- Executa os serviços e precisa consultar sua agenda e seus valores a receber.
- Depende de regras claras de comissão por serviço.
- Necessidade principal: transparência sobre atendimentos realizados e comissão acumulada.

## 4. FUNCIONALIDADES CORE

### 4.1 Autenticação
- Login com Laravel Jetstream com suporte a multi-tenancy.
- Recuperação de senha por e-mail.
- Gestão de sessão ativa por dispositivo.
- Controle de acesso por perfil: proprietária, recepcionista e profissional.
- Troca de organização/unidade quando aplicável.

### 4.2 Gestão de Clientes
**Descrição:**
O sistema deve permitir o cadastro e manutenção dos dados pessoais das clientes, garantindo busca rápida, histórico e reutilização dos dados na agenda e nos lançamentos diários.

**Requisitos:**
- Cadastrar nome, telefone, e-mail, data de nascimento e observações.
- Buscar cliente por nome ou telefone.
- Evitar duplicidade por telefone.
- Visualizar histórico de serviços e gastos da cliente.
- Editar cadastro sem perder histórico.

**Fluxo do usuário:**
1. A recepcionista acessa a tela de clientes.
2. Busca a cliente existente ou cria um novo cadastro.
3. Salva os dados pessoais para usar na agenda e nos atendimentos.

### 4.3 Catálogo de Serviços
**Descrição:**
O sistema deve centralizar todos os serviços prestados pelo salão, com valor de venda e regra de repasse para a profissional. Esse catálogo será a base da agenda, dos lançamentos diários e dos relatórios financeiros.

**Requisitos:**
- Cadastrar nome do serviço, categoria, duração e valor.
- Definir porcentagem da profissional.
- Calcular automaticamente a porcentagem que fica para o salão.
- Permitir ativar ou inativar serviços.
- Permitir valores específicos por serviço sem reconfigurar toda a regra financeira.

**Fluxo do usuário:**
1. A proprietária acessa a tela de serviços.
2. Cadastra o serviço com valor e percentual da profissional.
3. O sistema calcula a parte do salão e deixa o serviço disponível para agenda e lançamentos.

### 4.4 Agenda
**Descrição:**
A agenda deve funcionar como o centro da operação do salão, permitindo clicar no dia desejado e lançar as clientes com horário, serviço e profissional. Esse comportamento atende diretamente ao fluxo operacional informado pela usuária e é coerente com o posicionamento de plataformas de mercado voltadas para salões.

**Requisitos:**
- Exibir calendário diário, semanal e mensal.
- Permitir clicar no dia e lançar um atendimento.
- Associar cliente, serviço, profissional, horário e valor.
- Permitir remarcação, cancelamento e encaixe.
- Exibir status do atendimento: agendado, confirmado, em atendimento, finalizado, cancelado e faltou.
- Filtrar agenda por profissional.

**Fluxo do usuário:**
1. A recepcionista clica no dia desejado na agenda.
2. Seleciona o horário e a cliente.
3. Escolhe o serviço e a profissional.
4. O sistema preenche valor e regra de comissão.
5. O atendimento fica registrado na agenda do dia.

### 4.5 Lançamento Diário de Clientes e Serviços
**Descrição:**
Além da agenda tradicional, o sistema deve oferecer uma forma rápida de registrar os serviços diários realizados, principalmente para encaixes, atendimentos sem pré-agendamento ou rotina operacional corrida.[web:26]

**Requisitos:**
- Registrar cliente, data, serviço, profissional e valor do atendimento.
- Marcar se o atendimento foi pago ou está pendente.
- Associar o lançamento ao caixa aberto do dia.
- Atualizar comissão da profissional automaticamente.
- Permitir edição antes do fechamento do dia.

**Fluxo do usuário:**
1. A usuária acessa a tela de lançamentos do dia.
2. Seleciona a cliente e o serviço realizado.
3. Confirma o valor e a profissional responsável.
4. Marca a forma de pagamento ou pendência.
5. O sistema registra o atendimento e atualiza os totais do dia.

### 4.6 Caixa
**Descrição:**
O sistema deve permitir abertura e fechamento de caixa diário, consolidando as entradas e saídas da operação. Esse módulo é obrigatório para atender o fluxo solicitado e acompanha a prática apresentada nos materiais públicos da Avec.

**Requisitos:**
- Abrir caixa com valor inicial.
- Registrar recebimentos por dinheiro, cartão e PIX.
- Registrar saídas, sangrias e suprimentos.
- Impedir lançamentos financeiros sem caixa aberto, salvo permissão especial.
- Fechar caixa com saldo esperado, saldo informado e diferença.
- Exigir justificativa quando houver divergência.

**Fluxo do usuário:**
1. No início do dia, a recepcionista ou proprietária abre o caixa.
2. Informa o fundo inicial.
3. Durante o dia, os recebimentos dos serviços alimentam o caixa.
4. Ao final do expediente, a usuária revisa entradas e saídas.
5. O sistema calcula o fechamento e registra a diferença, se houver.

### 4.7 Comissão
**Descrição:**
O sistema deve calcular automaticamente quanto deve ser pago para cada profissional e quanto permanece para o salão com base no valor do serviço e no percentual configurado. O cálculo de comissão é parte central do produto e também aparece como funcionalidade-chave em referências do setor.

**Requisitos:**
- Definir percentual da profissional por serviço.
- Calcular automaticamente valor da profissional por atendimento.
- Calcular automaticamente valor do salão por atendimento.
- Exibir acumulado por profissional no dia e no mês.
- Permitir revisão antes do repasse final.
- Reverter comissão em caso de cancelamento ou exclusão antes do fechamento.

**Fluxo do usuário:**
1. O atendimento é lançado com o serviço correspondente.
2. O sistema busca a regra percentual do serviço.
3. Calcula a comissão da profissional.
4. Calcula a parte restante do salão.
5. Atualiza os relatórios da profissional e da gestão.

### 4.8 Relatórios Financeiros e Lucro do Mês
**Descrição:**
O sistema deve mostrar resultados diários e mensais com foco em faturamento, comissão, despesas e lucro. O lucro do mês é um requisito explicitamente informado pela usuária e deve ser tratado como indicador principal do dashboard financeiro.

**Requisitos:**
- Mostrar faturamento bruto do dia e do mês.
- Mostrar total de comissões do período.
- Permitir registrar despesas operacionais do mês.
- Calcular lucro mensal gerencial.
- Filtrar relatórios por período, profissional e serviço.
- Exibir resumo por forma de pagamento.

**Fluxo do usuário:**
1. A proprietária acessa a área financeira.
2. Seleciona o mês desejado.
3. O sistema soma receitas, comissões e despesas.
4. Calcula o lucro mensal.
5. Exibe os indicadores consolidados do salão.

### 4.9 Dashboard Operacional
**Descrição:**
O dashboard inicial deve concentrar os números mais importantes do dia e do mês para facilitar tomada de decisão rápida.

**Requisitos:**
- Exibir atendimentos do dia.
- Exibir caixa aberto ou fechado.
- Exibir faturamento diário.
- Exibir comissões do dia.
- Exibir lucro estimado do mês.
- Exibir alertas operacionais básicos.

**Fluxo do usuário:**
1. A usuária faz login.
2. Acessa o dashboard principal.
3. Visualiza agenda, caixa e indicadores consolidados.

## 5. REQUISITOS NÃO-FUNCIONAIS
- Performance: agenda diária deve carregar em até 2 segundos para operação padrão; busca de clientes deve responder em até 1 segundo; fechamento de caixa deve ser concluído em até 3 segundos.[file:28]
- Segurança: autenticação com Laravel Jetstream, validação por policies, segregação por organization_id, proteção de rotas com auth e tenancy middleware, trilha de auditoria para alteração de valores e caixa.[file:28]
- Escalabilidade: arquitetura preparada para multi-tenancy com organizations e crescimento para múltiplas unidades e múltiplos usuários por salão.
- Responsividade: suporte completo para desktop, tablet e smartphone, com foco operacional em desktop/tablet no balcão.

## 6. FORA DO ESCOPO V1
❌ Emissão fiscal completa
❌ Estoque técnico avançado com lote e validade
❌ Programa de fidelidade e cashback

## 7. ONBOARDING
**Fluxo:**
1. Cadastro da conta com Jetstream/Fortify e criação da organização principal.
2. Configuração inicial do salão com nome da unidade, dados básicos e preferências.
3. Cadastro inicial de profissionais, serviços e abertura do primeiro caixa.

**Checklist de Primeiros Passos:**
- [ ] Cadastrar os serviços com valores e percentuais
- [ ] Cadastrar profissionais e permissões
- [ ] Cadastrar as primeiras clientes
- [ ] Abrir o primeiro caixa do dia
- [ ] Realizar o primeiro lançamento de atendimento

## 8. MÉTRICAS DE SUCESSO
- Taxa de registro digital dos atendimentos: 95 por cento dos atendimentos lançados no sistema até o final do primeiro mês de uso.
- Tempo médio de lançamento de atendimento: até 30 segundos por atendimento em operação padrão.
- Acurácia financeira: divergência máxima de 2 por cento entre valor esperado e valor fechado no caixa mensal.
