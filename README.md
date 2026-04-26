<p align="center">
  <img src="frontend/assets/logo.svg" width="96" alt="Nuxtavel Logo"/>
</p>

<h1 align="center">nuxtavel</h1>

<p align="center">
  Painel de gerenciamento de usuários — API REST em <strong>Laravel 13</strong> + SPA em <strong>Nuxt 3</strong>.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 13"/>
  <img src="https://img.shields.io/badge/Nuxt-3-00DC82?style=flat-square&logo=nuxt.js&logoColor=white" alt="Nuxt 3"/>
  <img src="https://img.shields.io/badge/PostgreSQL-4169E1?style=flat-square&logo=postgresql&logoColor=white" alt="PostgreSQL"/>
  <img src="https://img.shields.io/badge/Deploy-Render-46E3B7?style=flat-square&logo=render&logoColor=white" alt="Render"/>
</p>

<p align="center">
  <a href="https://nuxtavel.onrender.com"><strong>🌐 nuxtavel.onrender.com</strong></a>
</p>

---

## Sobre o projeto

O **nuxtavel** foi desenvolvido como desafio técnico para a NDS. O nome é um trocadilho deliberado entre as duas stacks principais — **Nuxt** e **Laravel** — que, lido em voz alta, soa próximo de *"Next Level"*. O logo segue a mesma lógica: a identidade visual do Laravel é baseada em cubos, enquanto o Nuxt usa um triângulo; o ícone do projeto mescla as duas referências em uma malha hexagonal.

O design do frontend foi esboçado no Pencil com base no PDF de referência do desafio, mantendo fidelidade ao protótipo com alguns ajustes de implementação.

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Autenticação | Laravel Sanctum (token-based) |
| Frontend | Nuxt 3 (SPA, `ssr: false`) |
| Estado global | Pinia |
| Banco de dados | PostgreSQL |
| Testes | Pest PHP |
| Deploy | Render (backend em Docker, frontend estático) |

---

## Estrutura do repositório

```
nuxtavel/
├── backend/                  # API Laravel 13
│   ├── app/
│   │   ├── DTOs/             # Objetos de transferência de dados (readonly)
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/     # Validação de entrada
│   │   │   └── Resources/    # Serialização de saída
│   │   ├── Models/
│   │   └── Services/         # Regras de negócio
│   ├── tests/
│   ├── Dockerfile
│   └── ...
├── frontend/                 # SPA Nuxt 3
│   ├── assets/css/           # Tokens CSS (design system)
│   ├── components/
│   ├── composables/
│   ├── pages/
│   ├── stores/               # Pinia
│   └── nuxt.config.ts
├── docker-compose.yml        # Ambiente local completo
└── README.md
```

---

## Rodando localmente

### Backend

```bash
cd backend

composer install

cp .env.example .env
php artisan key:generate

# Configurar DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD no .env

php artisan migrate
php artisan storage:link

php artisan serve
# → http://localhost:8000
```

### Backend via Docker

```bash
cd backend
docker build -t nuxtavel-backend .
docker run -p 8000:8000 --env-file .env nuxtavel-backend
```

### Frontend

```bash
cd frontend

npm install

# Criar .env com:
# NUXT_PUBLIC_API_URL=http://localhost:8000/api

npm run dev
# → http://localhost:3000
```

---

## Variáveis de ambiente

### Backend (`backend/.env`)

| Variável | Descrição |
|---|---|
| `APP_KEY` | Gerada com `php artisan key:generate` |
| `APP_URL` | URL pública do backend |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` | Conexão PostgreSQL |
| `DB_USERNAME` / `DB_PASSWORD` | Credenciais do banco |
| `FRONTEND_URL` | URL do frontend (CORS) |
| `SANCTUM_STATEFUL_DOMAINS` | Domínio do frontend sem protocolo |

### Frontend (`frontend/.env`)

| Variável | Descrição |
|---|---|
| `NUXT_PUBLIC_API_URL` | URL base da API (ex: `https://api.exemplo.com/api`) |

---

## Endpoints da API

### Públicos

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/login` | Autenticação, retorna token Sanctum |
| `POST` | `/api/register` | Cadastro de novo usuário |
| `POST` | `/api/forgot-password` | Redefinição de senha por e-mail |

### Autenticados (`Bearer token`)

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/logout` | Invalida o token atual |
| `GET` | `/api/users/me` | Retorna o usuário logado |
| `POST` | `/api/change-password` | Altera a senha do usuário logado |
| `GET` | `/api/users` | Lista usuários (busca, filtros, paginação) |
| `POST` | `/api/users` | Cria novo usuário |
| `GET` | `/api/users/{id}` | Exibe um usuário |
| `POST` | `/api/users/{id}/update` | Atualiza usuário (suporta upload de avatar) |
| `DELETE` | `/api/users/{id}` | Remove usuário |
| `GET` | `/api/users/export` | Exporta lista de usuários em CSV |

---

## Testes

```bash
cd backend
php artisan test
# ou
./vendor/bin/pest
```

---

## Deploy no Render

| Serviço | URL |
|---|---|
| Frontend (Static Site) | [nuxtavel.onrender.com](https://nuxtavel.onrender.com) |
| Backend (Web Service / Docker) | [nuxtavel-api.onrender.com](https://nuxtavel-api.onrender.com) |
| Banco de dados | PostgreSQL gerenciado pelo Render (free tier, 90 dias) |

### Configuração manual

1. Conecte o repositório ao [Render](https://render.com)
2. Crie um **Web Service** com:
   - Root Directory: `backend`
   - Dockerfile Path: `./Dockerfile`
   - Adicione as variáveis de `backend/.env.render`
3. Crie um **Static Site** com:
   - Root Directory: `frontend`
   - Build Command: `npm install && npm run generate`
   - Publish Directory: `dist`
   - Adicione as variáveis de `frontend/.env.render`
4. Após os deploys, preencha `APP_URL`, `FRONTEND_URL` e `SANCTUM_STATEFUL_DOMAINS` no backend

> **Nota:** o free tier hiberna após 15 min de inatividade, adicionando ~50s na primeira requisição.

### Limitações conhecidas

- **Avatares efêmeros:** o filesystem do Render é descartado a cada redeploy ou reinicialização do container. Avatares enviados são perdidos.

---

## Licença

Distribuído sob a [licença MIT](https://opensource.org/licenses/MIT).
