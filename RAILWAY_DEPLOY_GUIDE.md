# 🚀 DEPLOY NO RAILWAY - ProjectSlides

## ✅ PROJETO CONFIGURADO PARA RAILWAY

### ARQUIVOS CRIADOS:
- ✅ `railway.json` - Configuração do Railway
- ✅ `Procfile` - Comando de inicialização
- ✅ `nixpacks.toml` - Configuração de build
- ✅ Estrutura corrigida para PHP

## 🎯 PASSO A PASSO - DEPLOY NO RAILWAY

### 1. ACESSAR RAILWAY
1. **Acesse [railway.app](https://railway.app)**
2. **Clique "Login" e conecte com GitHub**
3. **Autorize o acesso ao seu repositório**

### 2. CRIAR PROJETO
1. **Clique "New Project"**
2. **Selecione "Deploy from GitHub repo"**
3. **Escolha o repositório `slideQuestions`**
4. **Clique "Deploy Now"**

### 3. CONFIGURAR BANCO MYSQL
1. **No painel do Railway, clique "New"**
2. **Selecione "Database" > "MySQL"**
3. **Aguarde o banco ser criado**
4. **Copie as credenciais do banco**

### 4. CONFIGURAR VARIÁVEIS DE AMBIENTE
1. **No seu projeto, vá em "Variables"**
2. **Adicione as variáveis:**
   ```
   DB_HOST=seu-host-do-railway
   DB_PORT=3306
   DB_NAME=railway
   DB_USER=root
   DB_PASS=sua-senha-do-railway
   ```

### 5. EXECUTAR SCRIPT DO BANCO
1. **Acesse o banco MySQL no Railway**
2. **Execute o script `database/schema.sql`**
3. **Ou use o terminal do Railway para executar**

### 6. TESTAR APLICAÇÃO
1. **Acesse a URL fornecida pelo Railway**
2. **Teste o questionário**
3. **Teste o painel admin (⚙️ no canto superior direito)**
4. **Login: `DaviSena` / `197508`**

## 🔧 COMANDOS PARA EXECUTAR

```bash
# Fazer commit das mudanças
git add .
git commit -m "Configure project for Railway deployment"
git push
```

## 📱 RESULTADO FINAL

Após o deploy, você terá:
- URL: `https://slidequestions-production.up.railway.app`
- Questionário funcionando
- Painel admin operacional
- Banco MySQL incluído
- Deploy automático a cada push

## 🆘 PROBLEMAS COMUNS

### Erro de Conexão com Banco
- Verifique se as variáveis de ambiente estão corretas
- Confirme se o banco MySQL está rodando

### Erro de Dependências
- O Railway instala automaticamente via `composer install`

### Erro de Porta
- O Railway usa a variável `$PORT` automaticamente

---

**🎉 Agora sim! Railway é muito melhor para PHP!**
