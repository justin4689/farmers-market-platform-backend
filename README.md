# Farmers Market Platform — Backend API

> **XpertBot Academy — Full Stack Developer Technical Assessment**

A production-grade RESTful API powering an agricultural marketplace in Côte d'Ivoire. Farmers can purchase pesticides, fertilizers, and seeds from distribution points — paying cash or on credit — and repay outstanding debts using agricultural commodities (cacao by weight).

[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel)](https://laravel.com)
[![Sanctum](https://img.shields.io/badge/Auth-Laravel%20Sanctum-orange)](https://laravel.com/docs/sanctum)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Business Rules](#business-rules)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [API Documentation](#api-documentation)
- [Authentication](#authentication)
- [Response Format](#response-format)
- [Demo Credentials](#demo-credentials)
- [Folder Structure](#folder-structure)
- [Deployment](#deployment)
- [AI Usage Report](#ai-usage-report)
- [Future Improvements](#future-improvements)
- [Author](#author)

---

## Overview

This backend API serves as the core engine for the Farmers Market Platform — a digital tool that helps agricultural cooperatives and distribution points manage product sales, farmer credit accounts, and commodity-based debt repayments in real-time.

The system supports three distinct user roles with strictly scoped access, enforces configurable credit limits per farmer, calculates interest on credit purchases, and applies a FIFO algorithm to automatically settle the oldest outstanding debts first when a farmer makes a commodity repayment.

---

## Features

- **Role-based access control** — Admin, Supervisor, and Operator with layered permissions enforced via middleware
- **Product catalogue management** — Products organized in a recursive self-referential category tree (minimum 2 levels)
- **Farmer registry** — Unique identifier and phone number per farmer, configurable credit limit in FCFA
- **Cash & credit transactions** — Full order recording with per-item unit prices and quantities
- **Interest calculation** — Configurable interest rate applied automatically to credit purchases
- **Credit limit enforcement** — Transactions blocked when a farmer's outstanding debt would exceed their credit ceiling
- **FIFO debt repayment** — Commodity (kg of cacao) repayments settled against the oldest debts first
- **Partial repayment support** — Remaining balance tracked per debt with `open`, `partial`, and `paid` statuses
- **Interactive API docs** — Swagger UI served directly from the application root
- **Postman collection** — Pre-configured collection included for immediate testing

---

## Business Rules

### 1. Role Hierarchy

```
admin  →  creates supervisors
supervisor  →  creates operators
operator  →  records transactions and repayments
```

All roles can read all data. Category and product writes are restricted to Admin and Supervisor.

### 2. Credit Limit Enforcement

Before any credit transaction is persisted, the system validates:

```
current_open_debt + new_transaction_total  ≤  farmer.credit_limit_fcfa
```

If the check fails the API returns HTTP `422` with a descriptive message and the available credit remaining.

### 3. Interest Calculation

```
interest_amount  =  round(subtotal × interest_rate)
total_fcfa       =  subtotal + interest_amount
```

The `interest_rate` is submitted per transaction (e.g. `0.15` for 15%) and stored alongside the transaction record.

### 4. FIFO Debt Repayment

When a farmer delivers cacao:

1. Convert weight to FCFA: `credited = round(kg_received × commodity_rate_fcfa)`
2. Fetch all outstanding debts ordered by creation date (oldest first)
3. Apply the credited amount sequentially until exhausted
4. Each debt transitions: `open` → `partial` (partial coverage) or `paid` (fully settled)
5. Any surplus beyond all debts is silently discarded

### 5. Debt Status Reference

| Status | Meaning |
|---|---|
| `open` | Full original amount still owed |
| `partial` | Partially repaid — a remaining balance is tracked |
| `paid` | Fully settled |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.3+ |
| Framework | Laravel 13 |
| Database | MySQL 8.0+ |
| Authentication | Laravel Sanctum (Bearer token) |
| API Documentation | Swagger UI (static) + L5-Swagger / OpenAPI 3.0 |
| Testing | Pest PHP |
| Code Style | Laravel Pint |
| Architecture | Controllers → FormRequests → Services → Repositories → Models |

---

## Architecture

The application follows a strict layered architecture to separate concerns and keep each class focused on a single responsibility.

```
Request → Middleware (auth + role) → FormRequest (validation)
        → Controller → Service (business logic) → Repository (DB)
        → API Resource → JSON response
```

```
app/
├── Http/
│   ├── Controllers/      ← Thin HTTP layer: receive, delegate, respond
│   ├── Requests/         ← Input validation via FormRequest classes
│   ├── Resources/        ← JSON shaping via API Resource classes
│   └── Middleware/       ← IsAdmin | IsSupervisor | IsAdminOrSupervisor | IsOperatorOrAbove
├── Services/             ← All business logic (interest, FIFO, credit checks)
├── Repositories/
│   ├── Interfaces/       ← Contracts for every model's data access layer
│   └── Eloquent/         ← Eloquent implementations of those contracts
├── Models/               ← Eloquent models with relationships defined
├── Exceptions/           ← Domain exceptions (CreditLimitExceededException)
└── Providers/
    └── RepositoryServiceProvider.php  ← Binds interfaces → implementations
```

**Why this matters:** Controllers contain zero business logic. Services contain zero database queries. Repositories are the only layer that touches Eloquent. This makes the codebase fully testable at every layer independently.

---

## Database Schema

```
users               id, name, email, password, role(admin|supervisor|operator), created_by, timestamps
categories          id, name, parent_id (self-referential FK), timestamps
products            id, name, category_id, price_fcfa, description, timestamps
farmers             id, identifier(unique), firstname, lastname, phone_number(unique), credit_limit_fcfa, timestamps
transactions        id, farmer_id, operator_id, total_fcfa, payment_method(cash|credit), interest_rate, interest_amount_fcfa, timestamps
transaction_items   id, transaction_id, product_id, quantity, unit_price_fcfa, timestamps
debts               id, transaction_id, farmer_id, amount_fcfa, remaining_fcfa, status(open|partial|paid), timestamps
repayments          id, farmer_id, operator_id, kg_received, commodity_rate_fcfa, total_fcfa_credited, timestamps
repayment_debt      id, repayment_id, debt_id, amount_applied_fcfa, timestamps  ← pivot table
```

### Key Relationships

| Model | Relationship |
|---|---|
| `Category` | Belongs to `Category` (parent); has many `Category` (children) — recursive |
| `Product` | Belongs to `Category` |
| `Transaction` | Belongs to `Farmer`; belongs to `User` (operator); has many `TransactionItem` |
| `TransactionItem` | Belongs to `Transaction`; belongs to `Product` |
| `Debt` | Belongs to `Transaction`; belongs to `Farmer`; belongs to many `Repayment` (via pivot) |
| `Repayment` | Belongs to `Farmer`; belongs to `User` (operator); belongs to many `Debt` (via pivot) |

---

## Installation

### Requirements

- PHP 8.3+
- Composer 2.x
- MySQL 8.0+
- PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`

### Step-by-step setup

```bash
# 1. Clone the repository
git clone https://github.com/justin4689/farmers-market-platform-backend.git
cd farmers-market-platform-backend

# 2. Install PHP dependencies
composer install

# 3. Copy the environment file and generate the application key
cp .env.example .env
php artisan key:generate

# 4. Configure your database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farmers_market_platform_backend
DB_USERNAME=root
DB_PASSWORD=

# 5. Run database migrations and seed demo data
php artisan migrate --seed

# 6. Start the development server
php artisan serve
```

The API is now available at: `http://localhost:8000/api`

The Swagger UI is available at: `http://localhost:8000/swagger/index.html`

> **Tip:** The application root (`/`) redirects automatically to the Swagger UI.

---

## API Documentation

Interactive API documentation is served via Swagger UI and reflects the full OpenAPI 3.0 specification.

| Environment | URL |
|---|---|
| Local | `http://localhost:8000/swagger/index.html` |
| Production | `http://13.51.177.195/swagger/index.html` |

The OpenAPI spec file is located at `public/swagger/openapi.json` and covers all endpoints, request schemas, response envelopes, and authentication requirements.

### Endpoint Summary

#### Auth

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `POST` | `/api/login` | Public | Authenticate and receive a Sanctum bearer token |
| `POST` | `/api/logout` | Any role | Revoke the current session token |

#### User Management

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `POST` | `/api/admin/supervisors` | Admin | Create a supervisor account |
| `POST` | `/api/supervisor/operators` | Supervisor | Create an operator account |

#### Categories

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/api/categories` | Any role | Full recursive category tree |
| `POST` | `/api/categories` | Admin / Supervisor | Create a category |
| `PUT` | `/api/categories/{id}` | Admin / Supervisor | Update a category |
| `DELETE` | `/api/categories/{id}` | Admin / Supervisor | Delete a category |

#### Products

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/api/products` | Any role | List all products with category |
| `GET` | `/api/products/{id}` | Any role | Get a single product |
| `POST` | `/api/products` | Admin / Supervisor | Create a product |
| `PUT` | `/api/products/{id}` | Admin / Supervisor | Update a product |
| `DELETE` | `/api/products/{id}` | Admin / Supervisor | Delete a product |

#### Farmers

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/api/farmers/search?q=` | Any role | Search by identifier or phone number |
| `GET` | `/api/farmers/{id}` | Any role | Farmer profile with total outstanding debt |
| `POST` | `/api/farmers` | Any role | Register a new farmer |

#### Transactions, Debts & Repayments

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `POST` | `/api/transactions` | Any role | Record a cash or credit purchase |
| `GET` | `/api/farmers/{id}/debts` | Any role | List open/partial debts (FIFO order) |
| `POST` | `/api/repayments` | Any role | Record a commodity repayment |

---

## Authentication

This API uses **Laravel Sanctum** token-based authentication.

```bash
# 1. Login to get a token
POST /api/login
Content-Type: application/json

{
  "email": "admin@xpertbot.com",
  "password": "password"
}

# 2. Use the token in all subsequent requests
Authorization: Bearer <your_token_here>
Content-Type: application/json
```

Tokens are per-session and revoked on logout. There is no token expiry configured for development — set `SANCTUM_EXPIRATION` in `.env` for production.

---

## Response Format

All API responses share a consistent envelope structure.

**Success (2xx)**
```json
{
  "success": true,
  "data": { "..." },
  "message": "Human-readable description"
}
```

**Validation Error (422)**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Validation message"]
  }
}
```

**Business Rule Violation (422)**
```json
{
  "success": false,
  "message": "Credit limit exceeded. Available: 45 000 FCFA"
}
```

**Unauthorized (401) / Forbidden (403)**
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

---

## Demo Credentials

Seed data is included for all roles. All accounts use the same password.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@xpertbot.com` | `password` |
| Supervisor | `supervisor1@xpertbot.com` | `password` |
| Supervisor | `supervisor2@xpertbot.com` | `password` |
| Operator | `operator1@xpertbot.com` | `password` |
| Operator | `operator2@xpertbot.com` | `password` |
| Operator | `operator3@xpertbot.com` | `password` |

### Seeded Farmers

| Identifier | Name | Credit Limit | Debt Status |
|---|---|---|---|
| CI-2024-001 | Konan Kouassi | 100 000 FCFA | 6 400 FCFA remaining (`partial`) |
| CI-2024-002 | Adjoua Bamba | 150 000 FCFA | 0 FCFA (`paid`) |
| CI-2024-003 | Koffi Assouman | 50 000 FCFA | No debt |
| CI-2024-004 | Amenan Coulibaly | 200 000 FCFA | 72 000 FCFA (`open`) |
| CI-2024-005 | Yao N'Goran | 500 000 FCFA | No debt |

### Seeded Categories

```
Pesticides  →  Herbicides, Insecticides, Fongicides
Engrais     →  NPK, Urée, Engrais organiques
Semences    →  Maïs, Cacao, Riz
```

---

## Folder Structure

```
farmers-market-platform-backend/
├── app/
│   ├── Exceptions/               ← Domain-specific exceptions
│   ├── Http/
│   │   ├── Controllers/          ← Route handlers (thin delegation layer)
│   │   ├── Middleware/           ← Auth + role enforcement
│   │   ├── Requests/             ← Input validation (FormRequest classes)
│   │   └── Resources/            ← API response shaping
│   ├── Models/                   ← Eloquent models
│   ├── Providers/                ← Service and repository bindings
│   ├── Repositories/
│   │   ├── Eloquent/             ← Database query implementations
│   │   └── Interfaces/           ← Repository contracts
│   └── Services/                 ← Business logic (credit checks, FIFO, interest)
├── database/
│   ├── migrations/               ← Schema definitions
│   └── seeders/                  ← Demo data (Ivorian context)
├── public/
│   └── swagger/                  ← Swagger UI + OpenAPI 3.0 spec
├── postman/
│   └── farmers-market-api.json   ← Postman collection
├── routes/
│   └── api.php                   ← All API route definitions
├── tests/                        ← Pest PHP test suite
├── .env.example                  ← Environment template
└── README.md
```

---

## Deployment

### Environment Variables (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://13.51.177.195/api

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=farmers_market
DB_USERNAME=farmers_user
DB_PASSWORD=Laravel@2026


SANCTUM_EXPIRATION=1440
```

### Production Commands

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

### Live URLs

| Resource | URL |
|---|---|
| API Base | `http://13.51.177.195/api` |
| Swagger UI | `http://13.51.177.195/swagger/index.html` |
| GitHub Repository | `https://github.com/justin4689/farmers-market-platform-backend` |

---

## AI Usage Report

AI assistance (Claude) was used during this project in the following capacities:

| Task | AI Involvement |
|---|---|
| Boilerplate generation | Repository interfaces and Eloquent implementations scaffolded with AI assistance |
| FIFO algorithm | Logic designed independently; AI used to review edge cases |
| Swagger / OpenAPI spec | Initial structure generated with AI, then manually extended |
| Seeder data | Realistic Ivorian names and identifiers suggested by AI |
| README | Structure and content drafted with AI assistance, reviewed and adjusted manually |

All business rules, architectural decisions, and final code review were performed by the author. AI was used as an accelerator, not a replacement for understanding.

---

## Future Improvements

- [ ] **Refresh token support** — Implement token expiry with silent refresh for long-lived frontend sessions
- [ ] **Pagination** — Add cursor-based pagination to list endpoints for large datasets
- [ ] **Audit log** — Track all write operations (who changed what and when) for compliance
- [ ] **Notifications** — SMS or push alerts when a farmer's debt approaches their credit limit
- [ ] **Multi-commodity repayment** — Support repayment in currencies other than cacao (yams, rubber, etc.)
- [ ] **Analytics endpoints** — Aggregate reports: total credit outstanding by region, repayment rates by commodity
- [ ] **Docker setup** — `docker-compose.yml` for fully containerized local development
- [ ] **CI/CD pipeline** — GitHub Actions workflow for automated testing and deployment

---

## Author

**Justin TRAH**
Full Stack Developer — XpertBot Academy Technical Assessment, 2026

| | |
|---|---|
| Email | justintrah8@gmail.com |
| GitHub | [github.com/justin4689](https://github.com/justin4689) |
| Assessment platform | [xpertbotacademy.online](https://xpertbotacademy.online) |

---

*Built with Laravel 13 · PHP 8.3 · MySQL · Laravel Sanctum · Swagger UI*
