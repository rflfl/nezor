# Central de Suporte — Nezor

Bem-vindo à documentação de suporte do **Nezor**, a plataforma de gestão para salão de beleza. Aqui você encontra o passo a passo completo de todas as funcionalidades do sistema.

---

## Sumário

1. [Primeiros Passos](#primeiros-passos)
2. [Dashboard](#dashboard)
3. [Agenda](#agenda)
4. [Clientes](#clientes)
5. [Serviços](#serviços)
6. [Lançamentos Diários](#lancamentos-diarios)
7. [Caixa](#caixa)
8. [Comissões](#comissoes)
9. [Relatórios](#relatorios)
10. [Dúvidas Frequentes FAQ](#duvidas-frequentes)

---

## Primeiros Passos

Antes de começar a operar o salão no Nezor, é necessário realizar a configuração inicial. Siga o checklist abaixo na ordem indicada.

### Checklist de Configuração Inicial

- [ ] **Cadastrar os serviços** com valores e percentuais de comissão
- [ ] **Cadastrar profissionais** e definir permissões
- [ ] **Cadastrar as primeiras clientes**
- [ ] **Abrir o primeiro caixa** do dia
- [ ] **Realizar o primeiro lançamento** de atendimento

### 1. Cadastrar Serviços

Acesse o menu **Serviços** e clique em "Novo Serviço". Preencha:

- **Nome do serviço**: ex: Corte, Escova, Mechas
- **Categoria**: opcional, para organização
- **Duração**: tempo médio em minutos
- **Valor**: preço de venda ao cliente
- **Percentual da profissional**: quanto a profissional recebe (ex: 40%)
- **Percentual do salão**: o sistema calcula automaticamente o restante (ex: 60%)

> **Importante**: a soma dos percentuais deve ser exatamente 100%.

Serviços podem ser ativados ou inativados a qualquer momento sem perder o histórico.

### 2. Cadastrar Profissionais

Acesse o menu **Profissionais** e clique em "Nova Profissional". Preencha nome, telefone, e-mail e, se desejar, um percentual padrão de comissão. Esse percentual será sugerido automaticamente nos lançamentos, mas pode ser alterado caso a caso.

### 3. Cadastrar Clientes

Acesse o menu **Clientes** e clique em "Nova Cliente". O telefone é obrigatório e deve ser único por salão. O sistema não permite duplicidade de telefone para evitar cadastros repetidos.

### 4. Abrir o Caixa

Acesse o menu **Caixa** e clique em "Abrir Caixa" no início do expediente. Informe o **fundo inicial** (valor em dinheiro disponível no caixa). O caixa precisa estar aberto para que os lançamentos de pagamentos sejam registrados automaticamente.

### 5. Realizar o Primeiro Lançamento

Acesse o menu **Lançamentos** e registre um atendimento: selecione cliente, serviço, profissional, forma de pagamento e confirme. O sistema atualiza o caixa e a comissão automaticamente.

---

## Dashboard

O **Dashboard** é a tela inicial que aparece assim que você faz login. Ele concentra os números mais importantes do dia e do mês para facilitar a tomada de decisão rápida.

### Indicadores Exibidos

| Indicador | O que significa |
|-----------|-----------------|
| **Atendimentos do dia** | Quantidade total de atendimentos registrados hoje (agenda + lançamentos) |
| **Caixa** | Status do caixa: **Aberto** ou **Fechado**. Mostra também o valor em caixa no momento |
| **Faturamento diário** | Soma de todos os atendimentos pagos no dia atual |
| **Comissões do dia** | Total que deve ser repassado às profissionais pelos atendimentos de hoje |
| **Lucro estimado do mês** | Receitas do mês menos comissões e despesas operacionais |

### Alertas Operacionais

O dashboard também exibe alertas como:
- Caixa ainda não aberto no dia
- Atendimentos pendentes de pagamento
- Profissionais sem lançamentos no dia

### Como Usar

1. Faça login no sistema
2. O dashboard carrega automaticamente
3. Verifique se o caixa está aberto
4. Acompanhe o faturamento ao longo do dia
5. Use os números para planejar a agenda do dia seguinte

---

## Agenda

A **Agenda** é o centro da operação do salão. É por aqui que você organiza os horários das clientes e acompanha o status de cada atendimento.

### Visualizações Disponíveis

- **Diária**: hora a hora do dia selecionado
- **Semanal**: visão de toda a semana
- **Mensal**: calendário completo do mês

### Como Agendar um Atendimento

1. Clique no dia desejado no calendário
2. Selecione o **horário** disponível
3. Escolha a **cliente** (busque por nome ou telefone)
4. Selecione o **serviço**
5. Escolha a **profissional**
6. O sistema preenche automaticamente o **valor** e a **regra de comissão**
7. Clique em "Salvar"

O atendimento fica registrado na agenda do dia com status **Agendado**.

### Status dos Atendimentos

| Status | Significado |
|--------|-------------|
| **Agendado** | Horário reservado, cliente ainda não chegou |
| **Confirmado** | Cliente confirmou presença |
| **Em atendimento** | Cliente está sendo atendida no momento |
| **Finalizado** | Serviço concluído |
| **Cancelado** | Atendimento cancelado (não gera comissão) |
| **Faltou** | Cliente não compareceu (não gera comissão) |

### Ações na Agenda

- **Remarcar**: arraste o atendimento para outro horário ou edite a data
- **Cancelar**: mude o status para Cancelado. A comissão é revertida automaticamente se o caixa ainda não foi fechado
- **Encaixe**: adicione um atendimento em horário já ocupado (será exibido com alerta visual)
- **Filtrar por profissional**: use o filtro lateral para ver apenas a agenda de uma profissional específica

### Dicas

- Use a visualização semanal para planejar a semana toda de uma vez
- Atualize o status para "Em atendimento" quando a cliente chegar
- Marque como "Finalizado" imediatamente após o pagamento para manter o caixa atualizado

---

## Clientes

O cadastro de **Clientes** garante que você tenha sempre à mão os dados pessoais e o histórico de atendimentos de quem frequenta o salão.

### Campos do Cadastro

- **Nome completo**
- **Telefone** (obrigatório, deve ser único)
- **E-mail** (opcional)
- **Data de nascimento** (opcional, útil para enviar ofertas de aniversário)
- **Observações** (opcional, ex: alergias, preferências)

### Buscar Cliente

Na tela de Clientes, use a barra de busca para procurar por:
- **Nome**: digite parte do nome
- **Telefone**: digite os números (com ou sem máscara)

### Histórico de Serviços

Ao clicar no nome de uma cliente, você acessa o histórico completo:
- Todos os atendimentos realizados
- Valor total gasto no salão
- Serviços mais frequentes
- Última visita

### Editar Cadastro

Você pode editar os dados pessoais a qualquer momento sem perder o histórico de atendimentos. O telefone só pode ser alterado se não houver outra cliente com o mesmo número.

### Como Evitar Duplicidade

O sistema **impede o cadastro de dois clientes com o mesmo telefone** dentro do mesmo salão. Se tentar cadastrar um telefone já existente, uma mensagem de erro será exibida com o nome da cliente já cadastrada.

---

## Serviços

O **Catálogo de Serviços** centraliza tudo o que o salão oferece, com valores de venda e regras de repasse para as profissionais.

### Cadastrar um Serviço

1. Acesse **Serviços** e clique em "Novo Serviço"
2. Preencha os campos:
   - **Nome**: nome do serviço (ex: Corte Feminino)
   - **Categoria**: grupo do serviço (ex: Corte, Coloração, Tratamento)
   - **Duração**: tempo médio em minutos (ex: 60)
   - **Valor**: preço cobrado do cliente (ex: R$ 120,00)
   - **% Profissional**: quanto a profissional ganha (ex: 40%)
   - **% Salão**: quanto fica para o salão (ex: 60%)
3. O sistema calcula automaticamente os valores em reais
4. Clique em "Salvar"

### Ativar e Inativar Serviços

- Serviços **ativos** aparecem na agenda e nos lançamentos
- Serviços **inativos** ficam ocultos para novos agendamentos, mas mantêm todo o histórico financeiro
- Use inativação para serviços sazonais ou descontinuados

### Alterar Valores

Você pode alterar o valor de um serviço a qualquer momento. **Importante**: a alteração só afeta atendimentos futuros. Atendimentos já realizados mantêm o valor original para preservar o histórico financeiro.

### Percentuais Específicos

Se uma profissional tiver uma negociação diferente para um serviço específico, você pode alterar o percentual diretamente no lançamento ou no agendamento, sem precisar reconfigurar o serviço no catálogo.

---

## Lançamentos Diários

Os **Lançamentos Diários** são a forma rápida de registrar atendimentos realizados, principalmente para:
- Encaixes de última hora
- Atendimentos sem pré-agendamento (walk-ins)
- Rotina operacional corrida no balcão

### Como Lançar um Atendimento

1. Acesse o menu **Lançamentos**
2. Clique em "Novo Lançamento"
3. Selecione a **cliente** (ou cadastre rapidamente uma nova)
4. Escolha o **serviço**
5. Selecione a **profissional**
6. Confirme o **valor** do atendimento
7. O sistema calcula automaticamente a **comissão** da profissional e a **parte do salão**
8. Selecione a **forma de pagamento**:
   - **Dinheiro**
   - **Cartão**
   - **PIX**
   - **Misto** (mais de uma forma)
9. Marque se está **Pago** ou **Pendente**
10. Clique em "Salvar"

### Pagamento Pendente

Se o atendimento for marcado como **Pendente**:
- O valor não entra no caixa imediatamente
- A comissão da profissional é calculada, mas o repasse só deve ser feito após o recebimento
- Você pode filtrar os pendentes e baixá-los posteriormente

### Edição Antes do Fechamento

Lançamentos podem ser editados ou excluídos **até o fechamento do caixa do dia**. Após o fechamento, o registro fica bloqueado para alteração para garantir a integridade financeira.

### Vínculo com Caixa

Se houver um caixa aberto, o sistema associa automaticamente o lançamento ao caixa do dia. Se não houver caixa aberto, o sistema exibe um alerta.

---

## Caixa

O controle de **Caixa** é obrigatório para acompanhar as entradas e saídas do salão ao longo do dia.

### Abrir o Caixa

1. Acesse **Caixa** no início do expediente
2. Clique em "Abrir Caixa"
3. Informe o **fundo inicial** (valor em dinheiro que existe no caixa no momento da abertura)
4. Confirme

> **Atenção**: sem caixa aberto, os recebimentos em dinheiro não serão registrados automaticamente.

### Recebimentos

Durante o dia, os lançamentos de atendimentos pagos alimentam o caixa automaticamente. O sistema separa por forma de pagamento:
- **Dinheiro**: soma ao saldo físico do caixa
- **Cartão**: registrado como receita, mas não entra no saldo físico
- **PIX**: registrado como receita, mas não entra no saldo físico
- **Misto**: cada parte é contabilizada na forma correspondente

### Saídas, Sangrias e Suprimentos

Além dos recebimentos, você pode registrar manualmente:

| Tipo | Quando usar |
|------|-------------|
| **Saída** | Pagamento de despesas do dia (ex: entrega de material, lanche) |
| **Sangria** | Retirada de dinheiro do caixa para segurança ou depósito |
| **Suprimento** | Adição de dinheiro ao caixa (ex: troco adicional) |

Para registrar, clique em "Nova Transação" no caixa e selecione o tipo.

### Fechar o Caixa

Ao final do expediente:

1. Acesse **Caixa** e clique em "Fechar Caixa"
2. O sistema mostra o **saldo esperado** (fundos iniciais + entradas em dinheiro - saídas em dinheiro)
3. Conte o dinheiro físico e informe o **saldo real contado**
4. O sistema calcula a **diferença** (saldo real - saldo esperado)

#### Se houver divergência

- Se a diferença for diferente de zero, o sistema **exige uma justificativa**
- Exemplos de justificativa: erro de troco, pagamento esquecido de saída, nota rasgada
- A divergência fica registrada no histórico para conferência futura

#### Após o fechamento

- O caixa do dia é **bloqueado** para novas alterações
- Lançamentos não podem mais ser editados ou excluídos
- O relatório do dia fica disponível em **Relatórios**

---

## Comissões

O cálculo de **Comissões** é automático e é parte central do Nezor. Ele garante transparência sobre quanto cada profissional deve receber e quanto fica para o salão.

### Como Funciona

1. Quando um atendimento é lançado, o sistema busca o serviço e seus percentuais
2. Calcula o valor da profissional: `valor do serviço × % da profissional`
3. Calcula o valor do salão: `valor do serviço - valor da profissional`
4. Registra os dois valores no atendimento

### Exemplo Prático

| Serviço | Valor | % Profissional | Valor Profissional | Valor Salão |
|---------|-------|----------------|--------------------|-------------|
| Escova | R$ 80,00 | 40% | R$ 32,00 | R$ 48,00 |
| Corte | R$ 60,00 | 50% | R$ 30,00 | R$ 30,00 |
| Mechas | R$ 200,00 | 35% | R$ 70,00 | R$ 130,00 |

### Acumulado por Profissional

Acesse **Relatórios** e filtre por profissional para ver:
- Total de atendimentos no período
- Valor total de comissões acumuladas
- Serviços mais realizados

### Revisão Antes do Repasse

O sistema permite revisar as comissões antes de efetuar o pagamento à profissional. Você pode:
- Filtrar por período (dia, semana, mês)
- Conferir atendimento por atendimento
- Ajustar valores específicos se houver acordo diferente

### Reversão de Comissão

Se um atendimento for **cancelado** ou **excluído** antes do fechamento do caixa:
- A comissão é automaticamente removida do acumulado da profissional
- O valor volta para o salão
- O histórico do cancelamento fica registrado

> **Importante**: cancelamentos após o fechamento do caixa não revertem comissões automaticamente. Nesse caso, faça um ajuste manual no próximo repasse.

---

## Relatórios

A área de **Relatórios** oferece visão consolidada dos resultados financeiros do salão.

### Tipos de Relatórios

#### Faturamento

- **Faturamento bruto do dia**: soma de todos os atendimentos pagos no dia
- **Faturamento bruto do mês**: soma de todos os atendimentos pagos no mês selecionado
- Comparativo entre períodos

#### Comissões

- Total de comissões do período
- Detalhamento por profissional
- Ranking de profissionais por receita gerada

#### Despesas Operacionais

Você pode registrar despesas do mês diretamente nos relatórios:
- Aluguel
- Contas de água, luz, internet
- Material de consumo
- Marketing
- Outras despesas

#### Lucro Mensal

O lucro é calculado automaticamente:

```
Lucro = Faturamento Bruto - Total de Comissões - Despesas Operacionais
```

Esse é o indicador principal de saúde financeira do salão.

### Filtros Disponíveis

- **Período**: dia, semana, mês, período customizado
- **Profissional**: veja os números de uma profissional específica
- **Serviço**: veja qual serviço gera mais receita
- **Forma de pagamento**: resumo de dinheiro, cartão e PIX

### Resumo por Forma de Pagamento

O relatório mostra a distribuição das receitas:
- Quanto entrou em dinheiro (relevante para o caixa físico)
- Quanto entrou em cartão (relevante para conciliação bancária)
- Quanto entrou em PIX (relevante para conciliação bancária)

### Exportação

Os relatórios podem ser visualizados em tela ou exportados para planilha (quando disponível na versão).

---

## Dúvidas Frequentes — FAQ

### Cadastro e Acesso

**P: Esqueci minha senha. O que fazer?**  
R: Na tela de login, clique em "Esqueci minha senha" e informe seu e-mail. Você receberá um link para redefinição.

**P: Posso ter mais de um usuário no mesmo salão?**  
R: Sim. Cada salão (organização) pode ter vários usuários com diferentes papéis: proprietária, recepcionista e profissional.

**P: Como troco de salão/organização?**  
R: No menu superior direito, clique no nome da organização atual e selecione outra (se você pertencer a mais de uma).

### Agenda

**P: Posso agendar para um horário já ocupado?**  
R: Sim, o sistema permite encaixes. O atendimento será exibido com um alerta visual indicando sobreposição.

**P: O que acontece se eu cancelar um agendamento?**  
R: O status muda para "Cancelado". Se o caixa ainda não foi fechado, a comissão é revertida automaticamente.

**P: Posso alterar a profissional de um agendamento?**  
R: Sim, edite o agendamento e selecione outra profissional. O sistema recalcula a comissão automaticamente.

### Caixa

**P: O que acontece se eu tentar lançar um pagamento sem caixa aberto?**  
R: O sistema exibe um alerta e não associa o recebimento ao caixa. Recomenda-se abrir o caixa antes de começar os atendimentos.

**P: Posso reabrir um caixa já fechado?**  
R: Não. O fechamento de caixa é irreversível para garantir a integridade financeira. Se houver erro, registre a correção no caixa do dia seguinte.

**P: O que é sangria?**  
R: É a retirada de dinheiro do caixa durante o dia, geralmente por segurança ou para fazer depósito. Registre como transação do tipo "Sangria".

### Comissões

**P: Alterei o percentual de um serviço. Os atendimentos antigos mudam?**  
R: Não. Atendimentos já realizados mantêm o percentual e o valor original. Apenas atendimentos futuros usarão o novo percentual.

**P: Uma profissional fez um atendimento com valor promocional. Como lançar?**  
R: No lançamento, altere o valor do atendimento para o valor promocional. O sistema recalcula a comissão proporcionalmente.

**P: O que acontece se o cliente não pagar?**  
R: Marque o atendimento como "Pendente". A comissão é calculada, mas o repasse à profissional deve ser avaliado pela gestão.

### Relatórios

**P: O lucro do mês considera atendimentos pendentes?**  
R: Não. O cálculo de lucro considera apenas atendimentos com status "Pago".

**P: Posso ver o relatório de uma profissional específica?**  
R: Sim. Use o filtro "Profissional" nos relatórios para ver apenas os atendimentos e comissões dela.

**P: Como cadastro uma despesa?**  
R: Na tela de **Relatórios**, clique em "Nova Despesa" e preencha a data, categoria, descrição e valor.

### Performance e Dicas

**P: A agenda está demorando para carregar. O que fazer?**  
R: Use a visualização diária quando possível. A visualização mensal carrega mais dados e pode demorar alguns segundos a mais.

**P: Como evitar erros no fechamento de caixa?**  
R: Registre todas as saídas e sangrias imediatamente. Não deixe para registrar no final do dia.

**P: Posso usar o Nezor no celular?**  
R: Sim, o sistema é responsivo e funciona em desktop, tablet e smartphone. O foco operacional é em desktop/tablet no balcão, mas todas as funcionalidades estão disponíveis no celular.

### Segurança

**P: Quem pode fechar o caixa?**  
R: Apenas usuários com perfil de **proprietária** ou **gerente** podem fechar o caixa. Recepcionistas e profissionais não têm essa permissão.

**P: Quem pode alterar percentuais de comissão?**  
R: Apenas a **proprietária** pode alterar percentuais de serviços já criados.

**P: Meus dados estão seguros?**  
R: Sim. O Nezor usa autenticação via Laravel Jetstream, validação de acesso por organização, proteção de rotas e criptografia de dados sensíveis.

---

## Precisa de mais ajuda?

Se não encontrou a resposta para sua dúvida aqui, entre em contato com o suporte técnico pelo e-mail **suporte@nezor.com.br** ou fale com a responsável pela gestão do sistema no seu salão.
