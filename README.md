# 📊 ProjectSlides - Sistema de Questionários Interativo

Um sistema moderno de questionários interativos desenvolvido em PHP com interface administrativa para gerenciamento de perguntas e respostas.

## ✨ Funcionalidades

- 🎯 **Questionário Interativo**: Interface moderna e responsiva para responder perguntas
- ⚙️ **Painel Administrativo**: Gerenciamento completo de perguntas e opções de resposta
- 🔐 **Sistema de Autenticação**: Login seguro para administradores
- 📱 **Design Responsivo**: Funciona perfeitamente em desktop e mobile
- 🎨 **Interface Moderna**: Design limpo e profissional

## 🚀 Deploy Rápido

### Opção 1: Vercel (Recomendado)

1. **Fork este repositório**
2. **Acesse [Vercel](https://vercel.com)**
3. **Conecte sua conta do GitHub**
4. **Importe este projeto**
5. **Configure as variáveis de ambiente** (se necessário)
6. **Deploy automático!**

[![Deploy with Vercel](https://vercel.com/button)](https://vercel.com/new/clone?repository-url=https://github.com/SEU-USUARIO/projectslides)

### Opção 2: Instalação Local

```bash
# Clone o repositório
git clone https://github.com/SEU-USUARIO/projectslides.git
cd projectslides

# Instale as dependências
composer install

# Configure o banco de dados
mysql -u root -p
# Execute o arquivo database/schema.sql

# Configure as variáveis de ambiente
cp .env.example .env
# Edite o arquivo .env com suas credenciais

# Inicie o servidor
php -S localhost:8000 -t public
```

## 🗄️ Banco de Dados

O sistema utiliza MySQL com as seguintes tabelas:

- **admins**: Usuários administrativos
- **questions**: Perguntas do questionário
- **question_options**: Opções de resposta
- **settings**: Configurações do sistema

### Credenciais Padrão do Admin

- **Usuário**: DaviSena
- **Senha**: 197508

## 🛠️ Tecnologias Utilizadas

- **PHP 8.0+**
- **MySQL**
- **Twig** (Template Engine)
- **Pecee SimpleRouter** (Roteamento)
- **Composer** (Gerenciamento de Dependências)
- **HTML5/CSS3/JavaScript**

## 📁 Estrutura do Projeto

```
projectslides/
├── app/
│   ├── Controllers/     # Controladores da aplicação
│   ├── Services/        # Serviços de negócio
│   ├── Support/         # Classes de suporte
│   └── Views/           # Templates Twig
├── database/
│   └── schema.sql       # Script de criação do banco
├── public/
│   ├── assets/          # Arquivos estáticos
│   └── index.php        # Ponto de entrada
├── storage/             # Cache e logs
└── vendor/              # Dependências do Composer
```

## 🔧 Configuração

### Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=projectslides
DB_USER=seu_usuario
DB_PASS=sua_senha
```

### Banco de Dados

Execute o script SQL localizado em `database/schema.sql` para criar as tabelas necessárias.

## 🎯 Como Usar

1. **Acesse a aplicação** no navegador
2. **Responda as perguntas** do questionário
3. **Para administrar**: Clique no ícone ⚙️ no canto superior direito
4. **Faça login** com as credenciais do admin
5. **Gerencie perguntas e respostas** através do painel

## 🔒 Segurança

- Senhas hashadas com `password_hash()`
- Sanitização de inputs
- Headers de segurança configurados
- Validação de dados

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🤝 Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para:

1. Fazer um Fork do projeto
2. Criar uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abrir um Pull Request

## 📞 Suporte

Se você encontrar algum problema ou tiver dúvidas, abra uma [issue](https://github.com/SEU-USUARIO/projectslides/issues) no GitHub.

---

**Desenvolvido com ❤️ para facilitar a criação de questionários interativos**
