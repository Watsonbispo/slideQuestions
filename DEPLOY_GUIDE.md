# 🚀 Guia de Deploy - ProjectSlides

## ⚠️ IMPORTANTE: GitHub Pages NÃO suporta PHP

O GitHub Pages só roda HTML, CSS e JavaScript estático. Para hospedar aplicações PHP, use uma das opções abaixo:

## 🎯 Opção 1: Vercel (RECOMENDADO)

### Passo 1: Instalar Git
```bash
sudo apt update
sudo apt install git
```

### Passo 2: Configurar Git
```bash
git config --global user.name "Seu Nome"
git config --global user.email "seu@email.com"
```

### Passo 3: Inicializar Repositório
```bash
cd /home/watson/stud/projectslides
git init
git add .
git commit -m "Primeiro commit - Sistema de Questionários"
```

### Passo 4: Criar Repositório no GitHub
1. Acesse [github.com](https://github.com)
2. Clique em "New repository"
3. Nome: `projectslides`
4. Descrição: `Sistema de Questionários Interativo em PHP`
5. Deixe público
6. NÃO marque "Add README" (já temos um)
7. Clique "Create repository"

### Passo 5: Conectar ao GitHub
```bash
git remote add origin https://github.com/SEU-USUARIO/projectslides.git
git branch -M main
git push -u origin main
```

### Passo 6: Deploy no Vercel
1. Acesse [vercel.com](https://vercel.com)
2. Clique "Sign up" e conecte com GitHub
3. Clique "Import Project"
4. Selecione seu repositório `projectslides`
5. Clique "Deploy"
6. ✅ Pronto! Sua aplicação estará online

## 🎯 Opção 2: Netlify (Alternativa)

### Passo 1-5: Igual ao Vercel

### Passo 6: Deploy no Netlify
1. Acesse [netlify.com](https://netlify.com)
2. Clique "Sign up" e conecte com GitHub
3. Clique "New site from Git"
4. Selecione seu repositório
5. Configure:
   - Build command: `composer install`
   - Publish directory: `public`
6. Clique "Deploy site"

## 🎯 Opção 3: Railway (Para PHP completo)

### Passo 1-5: Igual ao Vercel

### Passo 6: Deploy no Railway
1. Acesse [railway.app](https://railway.app)
2. Conecte com GitHub
3. Clique "Deploy from GitHub repo"
4. Selecione seu repositório
5. Railway detectará automaticamente que é PHP
6. Configure as variáveis de ambiente se necessário

## 📋 Arquivos Já Preparados

✅ `.gitignore` - Ignora arquivos desnecessários
✅ `vercel.json` - Configuração para Vercel
✅ `README.md` - Documentação completa
✅ `composer.json` - Dependências PHP

## 🔧 Configuração do Banco de Dados

Para produção, você precisará de um banco MySQL online:

### Opções Gratuitas:
1. **PlanetScale** (MySQL gratuito)
2. **Railway** (MySQL gratuito)
3. **Aiven** (MySQL gratuito)

### Configuração:
1. Crie uma conta em um dos serviços acima
2. Crie um banco MySQL
3. Execute o script `database/schema.sql`
4. Configure as variáveis de ambiente no Vercel/Netlify/Railway

## 🚀 URLs de Deploy

Após o deploy, você terá URLs como:
- Vercel: `https://projectslides-xxx.vercel.app`
- Netlify: `https://projectslides-xxx.netlify.app`
- Railway: `https://projectslides-xxx.railway.app`

## 📱 Teste Final

1. Acesse a URL do deploy
2. Teste o questionário
3. Teste o painel admin (⚙️ no canto superior direito)
4. Login: `DaviSena` / `197508`

## 🆘 Problemas Comuns

### Erro 500 no Deploy
- Verifique se o banco de dados está configurado
- Verifique as variáveis de ambiente

### Erro de Conexão com Banco
- Confirme as credenciais do banco
- Verifique se o banco aceita conexões externas

### Erro de Dependências
- Execute `composer install` localmente
- Commit o arquivo `composer.lock`

## 📞 Suporte

Se tiver problemas, abra uma issue no GitHub ou me chame!

---

**🎉 Boa sorte com o deploy!**
