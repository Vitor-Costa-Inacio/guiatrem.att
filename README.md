# Sistema Guia Trem - Backend PHP

Sistema completo de gerenciamento de manutenção de trens com autenticação de usuários, desenvolvido em PHP com MySQL.

## Funcionalidades

### Sistema de Autenticação
- ✅ Cadastro de usuário com validação completa
- ✅ Login com e-mail e senha
- ✅ Validação de e-mail (formato correto)
- ✅ Validação de senha (mínimo 8 caracteres, letras e números)
- ✅ Verificação de duplicidade de e-mail
- ✅ Criptografia de senhas com `password_hash()`
- ✅ Verificação de senhas com `password_verify()`
- ✅ Gerenciamento de sessões
- ✅ Logout seguro
- ✅ Mensagens de erro e sucesso

### APIs Disponíveis
- **Dashboard**: Estatísticas gerais e dados em tempo real
- **Monitoramento**: Gerenciamento de manutenções
- **Técnico**: Interface para técnicos agendarem e concluírem manutenções
- **Histórico**: Consulta ao histórico de manutenções
- **Gestão**: Gestão de rotas e status da frota
- **Notificações**: Sistema de notificações do sistema
- **Relatórios**: Geração de relatórios diversos

## Estrutura do Projeto

```
guiatrem-backend/
├── config/
│   ├── config.php          # Configurações gerais
│   └── database.php        # Conexão com banco de dados
├── database/
│   └── banco.sql          # Script de criação do banco
├── public/
│   ├── css/               # Arquivos CSS
│   ├── html/              # Páginas HTML
│   └── js/                # Arquivos JavaScript
├── src/
│   ├── api/               # APIs do sistema
│   ├── auth/              # Sistema de autenticação
│   └── models/            # Modelos de dados
└── index.php             # Ponto de entrada
```

## Instalação

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)

### Passo a Passo

1. **Clone ou baixe o projeto**
   ```bash
   git clone <repositorio>
   cd guiatrem-backend
   ```

2. **Configure o banco de dados**
   - Crie um banco de dados MySQL
   - Execute o script `database/banco.sql`
   ```sql
   mysql -u root -p < database/banco.sql
   ```

3. **Configure a conexão**
   - Edite o arquivo `config/database.php`
   - Ajuste as credenciais do banco de dados:
   ```php
   private $host = 'localhost';
   private $db_name = 'guiatrem';
   private $username = 'seu_usuario';
   private $password = 'sua_senha';
   ```

4. **Configure o servidor web**
   - Aponte o DocumentRoot para a pasta do projeto
   - Certifique-se de que o PHP está habilitado

5. **Teste a instalação**
   - Acesse `http://localhost/guiatrem-backend`
   - Você será redirecionado para a página de login

## Uso

### Primeiro Acesso
1. Acesse a aplicação
2. Clique em "CADASTRAR" na página de login
3. Preencha os dados:
   - Nome completo
   - E-mail válido
   - Senha (mínimo 8 caracteres, com letras e números)
   - Confirmação da senha
4. Após o cadastro, faça login com suas credenciais

### Navegação
- **Dashboard**: Visão geral do sistema
- **Monitoramento**: Acompanhar manutenções em tempo real
- **Técnico**: Agendar e gerenciar manutenções
- **Histórico**: Consultar manutenções passadas
- **Gestão**: Gerenciar rotas e frota
- **Notificações**: Ver alertas do sistema
- **Relatórios**: Gerar relatórios diversos

## APIs

### Autenticação

#### POST `/src/auth/register.php`
Cadastra um novo usuário.

**Parâmetros:**
```json
{
  "nome": "Nome Completo",
  "email": "email@exemplo.com",
  "senha": "senha123",
  "confirmar_senha": "senha123"
}
```

#### POST `/src/auth/login.php`
Realiza login do usuário.

**Parâmetros:**
```json
{
  "email": "email@exemplo.com",
  "senha": "senha123"
}
```

#### GET `/src/auth/check_session.php`
Verifica se o usuário está logado.

#### POST `/src/auth/logout.php`
Realiza logout do usuário.

### Funcionalidades

#### GET `/src/api/dashboard.php`
Retorna dados do dashboard.

#### GET `/src/api/monitoramento.php`
Lista manutenções com filtros opcionais.

**Parâmetros de consulta:**
- `linha`: Filtrar por linha
- `tipo`: Filtrar por tipo de manutenção
- `status`: Filtrar por status
- `data_inicio`: Data inicial
- `data_fim`: Data final

#### POST `/src/api/monitoramento.php`
Cria nova manutenção.

#### PUT `/src/api/monitoramento.php`
Atualiza status de manutenção.

## Segurança

### Medidas Implementadas
- ✅ Sanitização de dados de entrada
- ✅ Prepared statements (proteção contra SQL Injection)
- ✅ Hash seguro de senhas
- ✅ Validação de sessões
- ✅ Headers CORS configurados
- ✅ Validação de tipos de dados
- ✅ Escape de caracteres especiais

### Validações
- **Nome**: Mínimo 2 caracteres, máximo 100
- **E-mail**: Formato válido, máximo 100 caracteres, único no sistema
- **Senha**: Mínimo 8 caracteres, deve conter letras e números

## Banco de Dados

### Tabelas Principais

#### `usuario`
- `id_usuario`: Chave primária
- `nome_usuario`: Nome completo
- `email_usuario`: E-mail único
- `senha_usuario`: Senha criptografada
- `funcao`: Função do usuário (padrão: 'usuario')
- `data_criacao`: Data de criação
- `data_atualizacao`: Data de atualização

#### `trem`
- `id_trem`: Chave primária
- `linha_trem`: Nome da linha

#### `manutencao`
- `id_manutencao`: Chave primária
- `data_manutencao`: Data da manutenção
- `tipo_manutencao`: Tipo (Preventiva/Corretiva)
- `descricao_manutencao`: Descrição
- `observacao_manutencao`: Observações
- `status_manutencao`: Status atual
- `fk_trem`: Referência ao trem
- `fk_usuario`: Referência ao usuário responsável

## Desenvolvimento

### Estrutura de Arquivos
- **Models**: Classes para interação com banco de dados
- **APIs**: Endpoints REST para funcionalidades
- **Auth**: Sistema de autenticação
- **Config**: Configurações e utilitários

### Padrões Utilizados
- PSR-4 para autoload
- Prepared statements para queries
- Resposta JSON padronizada
- Tratamento de erros consistente

## Troubleshooting

### Problemas Comuns

1. **Erro de conexão com banco**
   - Verifique as credenciais em `config/database.php`
   - Certifique-se de que o MySQL está rodando

2. **Página em branco**
   - Verifique os logs de erro do PHP
   - Certifique-se de que todas as extensões necessárias estão instaladas

3. **Erro 404 nas APIs**
   - Verifique se o mod_rewrite está habilitado
   - Confirme os caminhos dos arquivos

4. **Problemas de CORS**
   - Verifique se os headers CORS estão configurados
   - Teste com diferentes navegadores

## Suporte

Para suporte técnico ou dúvidas sobre o sistema, consulte a documentação ou entre em contato com a equipe de desenvolvimento.

## Licença

Este projeto é proprietário e confidencial. Todos os direitos reservados.

