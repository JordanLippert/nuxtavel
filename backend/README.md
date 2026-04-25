# Nuxtavel — Backend

API REST do painel de gerenciamento de usuários, construída com **Laravel 13** e autenticação via **Laravel Sanctum**.

---

## Tecnologias

| Camada | Tecnologia |
|---|---|
| Framework | Laravel 13 |
| Linguagem | PHP 8.2+ |
| Banco de dados | PostgreSQL |
| Autenticação | Laravel Sanctum (token-based) |
| Testes | Pest PHP |
| Armazenamento | Laravel Storage (disco `public`) |

---

## Estrutura

```
app/
├── DTOs/                  # Objetos de transferência de dados (imutáveis, readonly)
│   ├── CreateUserDTO.php
│   ├── UpdateUserDTO.php
│   ├── LoginDTO.php
│   └── RegisterDTO.php
│
├── Http/
│   ├── Controllers/       # Recebem a requisição, delegam para Services
│   │   ├── AuthController.php
│   │   └── UserController.php
│   │
│   ├── Requests/          # Validação e autorização de entrada
│   │   ├── LoginRequest.php
│   │   ├── RegisterRequest.php
│   │   ├── StoreUserRequest.php
│   │   ├── UpdateUserRequest.php
│   │   ├── ForgotPasswordRequest.php
│   │   └── ChangePasswordRequest.php
│   │
│   └── Resources/         # Serialização da saída (UserResource)
│       └── UserResource.php
│
├── Models/
│   └── User.php
│
└── Services/              # Regras de negócio isoladas dos Controllers
    └── UserService.php
```

---

## Rotas

### Públicas

| Método | Endpoint | Descrição |
|---|---|---|
| `POST` | `/api/login` | Autenticação, retorna token Sanctum |
| `POST` | `/api/register` | Cadastro de novo usuário |
| `POST` | `/api/forgot-password` | Redefinição de senha por e-mail |

### Autenticadas (`Bearer token`)

| Método | Endpoint | Descrição |
|---|---|---|
| `POST` | `/api/logout` | Invalida o token atual |
| `GET` | `/api/users/me` | Retorna o usuário logado |
| `POST` | `/api/change-password` | Altera a senha do usuário logado |
| `GET` | `/api/users` | Lista usuários (busca, filtro, paginação) |
| `POST` | `/api/users` | Cria novo usuário |
| `GET` | `/api/users/{id}` | Exibe um usuário |
| `POST` | `/api/users/{id}/update` | Atualiza usuário (suporta upload de avatar) |
| `DELETE` | `/api/users/{id}` | Remove usuário |
| `GET` | `/api/users/export` | Exporta lista de usuários em CSV |

> `PUT`/`PATCH` não suportam `multipart/form-data` em alguns clientes, por isso o update usa `POST /users/{id}/update`.

---

## Executando localmente

```bash
# Instalar dependências
composer install

# Configurar variáveis de ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco e rodar migrations
php artisan migrate

# Criar link simbólico para avatars
php artisan storage:link

# Iniciar servidor de desenvolvimento
php artisan serve
```

---

## Testes

```bash
php artisan test
# ou
./vendor/bin/pest
```

Os testes cobrem os fluxos de autenticação (`AuthTest`) com os cenários de login, registro e redefinição de senha.

---

## Decisões de arquitetura

- **DTOs imutáveis** (`readonly`) — garantem que os dados validados não sejam mutados entre Controller e Service.
- **Form Requests** — toda validação fica fora dos Controllers, que ficam responsáveis apenas por orquestrar.
- **Services** — regras de negócio (hash de senha, upload de avatar, filtros de listagem) isoladas e testáveis.
- **UserResource** — padroniza a saída da API, expondo apenas os campos necessários e convertendo `avatar` em URL absoluta.
