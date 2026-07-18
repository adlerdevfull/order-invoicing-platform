# Order & Invoicing Platform


> **Languages / Idiomas / Idiomas:** [English](#-english) · [Español](#-español) · [Português](#-português)

---

## 🇬🇧 English

Robust, secure and scalable REST API for managing orders, inventory and electronic invoice generation.

### Stack

- **Backend**: PHP 8.2 + Laravel 11
- **Database**: PostgreSQL 16
- **Cache / Queues**: Redis 7
- **Auth**: JWT + RBAC (Spatie Permissions)
- **Infra**: Docker + docker-compose + GitHub Actions CI

### Architecture

Hexagonal (Ports & Adapters) with tactical DDD:

```
src/Domain/         → Pure business rules (framework-free)
src/Application/    → Use cases (Command Handlers)
src/Infrastructure/ → Eloquent, Redis, Queue Jobs
app/                → HTTP layer (Controllers, Requests, Resources)
```


### Delivery phases

This repository was built in progressive phases (see commit history):

| Phase | Focus |
|-------|--------|
| 1. Scaffold | Bootstrap Laravel API, tooling and ignore rules |
| 2. Domain | Business entities, value objects and repository ports |
| 3. Application | Use-case handlers (commands / services) |
| 4. Infrastructure | Eloquent adapters, Redis and providers |
| 5. API | HTTP controllers, requests, resources and routes |
| 6. Database | Migrations and seeders |
| 7. Config | App, auth, cache, queue configuration |
| 8. Docker | Dockerfile + docker-compose local stack |
| 9. Frontend tooling | Vite, Tailwind and SPA scaffold |
| 10. Frontend UI | Pages, hooks and API client |
| 11. Tests | Unit and feature tests |
| 12. Docs & CI | README multi-language and GitHub Actions |

### How to run

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed
```

- API: http://localhost:8000/api/v1

**Login**: admin@platform.test / password

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/v1/auth/login` | Login (returns JWT) |
| GET | `/v1/products` | List products |
| POST | `/v1/orders` | Create order |
| PATCH | `/v1/orders/{id}/transition` | Change order status |
| POST | `/v1/invoices` | Generate invoice for paid order |

### Domain flows

**Order**: `draft` → `confirmed` → `paid` → `shipped` / `cancelled`

**Invoice**: generated only for `paid` orders, immutable after creation

### Tests

```bash
docker compose exec app vendor/bin/pest --coverage --min=75
```

---

## 🇪🇸 Español

API REST robusta, segura y escalable para gestionar pedidos, inventario y emisión de facturas electrónicas.

### Stack

- **Backend**: PHP 8.2 + Laravel 11
- **Base de datos**: PostgreSQL 16
- **Caché / Colas**: Redis 7
- **Auth**: JWT + RBAC (Spatie Permissions)
- **Infra**: Docker + docker-compose + GitHub Actions CI

### Arquitectura

Hexagonal (Ports & Adapters) con DDD táctico:

```
src/Domain/         → Reglas de negocio puras (sin dependencia de framework)
src/Application/    → Casos de uso (Command Handlers)
src/Infrastructure/ → Eloquent, Redis, Queue Jobs
app/                → Capa HTTP (Controllers, Requests, Resources)
```


### Fases de entrega

Este repositorio se construyó en fases progresivas (ver historial de commits):

| Fase | Enfoque |
|-------|--------|
| 1. Scaffold | Bootstrap de la API Laravel, tooling e ignore rules |
| 2. Dominio | Entidades, value objects e interfaces de repositorio |
| 3. Aplicación | Handlers de casos de uso (commands / services) |
| 4. Infraestructura | Adapters Eloquent, Redis y providers |
| 5. API | Controllers HTTP, requests, resources y rutas |
| 6. Base de datos | Migraciones y seeders |
| 7. Config | Configuración de app, auth, cache y colas |
| 8. Docker | Dockerfile + docker-compose local |
| 9. Frontend tooling | Vite, Tailwind y scaffold SPA |
| 10. Frontend UI | Páginas, hooks y cliente API |
| 11. Tests | Tests unitarios y de feature |
| 12. Docs & CI | README multi-idioma y GitHub Actions |

### Cómo ejecutar

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed
```

- API: http://localhost:8000/api/v1

**Login**: admin@platform.test / password

### Endpoints de la API

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/v1/auth/login` | Login (devuelve JWT) |
| GET | `/v1/products` | Listar productos |
| POST | `/v1/orders` | Crear pedido |
| PATCH | `/v1/orders/{id}/transition` | Cambiar estado del pedido |
| POST | `/v1/invoices` | Generar factura para pedido pagado |

### Flujos de dominio

**Pedido**: `draft` → `confirmed` → `paid` → `shipped` / `cancelled`

**Factura**: generada solo para pedidos `paid`, inmutable tras su creación

### Tests

```bash
docker compose exec app vendor/bin/pest --coverage --min=75
```

---

## 🇧🇷 Português

API REST robusta, segura e escalável para gerenciamento de pedidos, estoque e emissão de faturas eletrônicas.

### Stack

- **Backend**: PHP 8.2 + Laravel 11
- **Banco de dados**: PostgreSQL 16
- **Cache / Filas**: Redis 7
- **Auth**: JWT + RBAC (Spatie Permissions)
- **Infra**: Docker + docker-compose + GitHub Actions CI

### Arquitetura

Hexagonal (Ports & Adapters) com DDD tático:

```
src/Domain/         → Regras de negócio puras (sem dependência de framework)
src/Application/    → Casos de uso (Command Handlers)
src/Infrastructure/ → Eloquent, Redis, Queue Jobs
app/                → Camada HTTP (Controllers, Requests, Resources)
```


### Fases de entrega

Este repositório foi construído em fases progressivas (ver histórico de commits):

| Fase | Foco |
|-------|--------|
| 1. Scaffold | Bootstrap da API Laravel, tooling e ignore rules |
| 2. Domínio | Entidades, value objects e portas de repositório |
| 3. Aplicação | Handlers de casos de uso (commands / services) |
| 4. Infraestrutura | Adapters Eloquent, Redis e providers |
| 5. API | Controllers HTTP, requests, resources e rotas |
| 6. Banco de dados | Migrations e seeders |
| 7. Config | Configuração de app, auth, cache e filas |
| 8. Docker | Dockerfile + docker-compose local |
| 9. Frontend tooling | Vite, Tailwind e scaffold SPA |
| 10. Frontend UI | Páginas, hooks e cliente da API |
| 11. Testes | Testes unitários e de feature |
| 12. Docs & CI | README multi-idioma e GitHub Actions |

### Como executar

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed
```

- API: http://localhost:8000/api/v1

**Login**: admin@platform.test / password

### Endpoints da API

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/v1/auth/login` | Login (retorna JWT) |
| GET | `/v1/products` | Listar produtos |
| POST | `/v1/orders` | Criar pedido |
| PATCH | `/v1/orders/{id}/transition` | Mudar status do pedido |
| POST | `/v1/invoices` | Gerar fatura para pedido pago |

### Fluxos de domínio

**Pedido**: `draft` → `confirmed` → `paid` → `shipped` / `cancelled`

**Fatura**: gerada apenas para pedidos `paid`, imutável após criação

### Testes

```bash
docker compose exec app vendor/bin/pest --coverage --min=75
```
