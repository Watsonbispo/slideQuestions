# Configuração do Banco de Dados no Railway

## Problema Atual
- Apenas 2 perguntas aparecem (dados hardcoded)
- Admin não funciona
- Banco de dados não está conectando

## Solução: Adicionar MySQL ao Railway

### 1. Adicionar Plugin MySQL
1. Acesse o painel do Railway: https://railway.app/dashboard
2. Selecione seu projeto `projectslides`
3. Clique em **"+ New"** ou **"Add Service"**
4. Selecione **"Database"** → **"MySQL"**

### 2. Configurar Variáveis de Ambiente
O Railway automaticamente criará estas variáveis:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`

### 3. Executar Schema do Banco
Após criar o MySQL, execute o schema:

1. Acesse o MySQL no Railway
2. Vá para a aba **"Query"**
3. Execute o conteúdo do arquivo `database/schema.sql`

Ou acesse: `https://web-6ceb6.up.railway.app/setup-database`

### 4. Verificar Conexão
Teste a conexão acessando:
`https://web-6ceb6.up.railway.app/debug-db.php`

### 5. Configuração Alternativa (se necessário)
Se o Railway não criar as variáveis automaticamente, adicione manualmente:

1. Vá em **"Variables"** no seu projeto
2. Adicione:
   - `DB_HOST` = valor de `MYSQLHOST`
   - `DB_PORT` = valor de `MYSQLPORT`
   - `DB_NAME` = valor de `MYSQLDATABASE`
   - `DB_USER` = valor de `MYSQLUSER`
   - `DB_PASS` = valor de `MYSQLPASSWORD`

## Arquivos de Teste
- `/debug-db.php` - Teste completo da conexão
- `/setup-database.php` - Executa o schema do banco
- `/test-admin.php` - Testa funcionalidades do admin

## Próximos Passos
1. Adicionar MySQL no Railway
2. Executar schema do banco
3. Testar com `/debug-db.php`
4. Verificar se todas as perguntas aparecem
5. Testar admin
