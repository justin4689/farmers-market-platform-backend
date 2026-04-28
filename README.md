# Farmers Market Platform — Backend API

REST API for an agricultural marketplace in Côte d'Ivoire. Farmers purchase pesticides, fertilizers, and seeds from points of sale, paying cash or on credit. Credit debts are repaid with agricultural commodities (cacao in kg).

Built for the **XpertBot Academy Technical Test**.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.2+) |
| Database | MySQL 8.0+ |
| Authentication | Laravel Sanctum (Bearer token) |
| Architecture | Controllers → Services → Repositories → Models |

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/      ← Thin HTTP layer (receive request, call service, return resource)
│   ├── Requests/         ← All input validation via FormRequest classes
│   ├── Resources/        ← JSON response shaping (API Resources)
│   └── Middleware/       ← Role-based access control (IsAdmin, IsSupervisor, etc.)
├── Services/             ← All business logic (credit limits, FIFO, interest calc)
├── Repositories/
│   ├── Interfaces/       ← Contracts for each model's data access
│   └── Eloquent/         ← Eloquent implementations of those contracts
├── Models/               ← Eloquent models with relationships
├── Exceptions/           ← Domain exceptions (CreditLimitExceededException)
└── Providers/
    └── RepositoryServiceProvider.php  ← Binds interfaces to implementations
```

**Request lifecycle:**
```
Request → Middleware (auth + role) → FormRequest (validation)
        → Controller → Service (business logic) → Repository (DB)
        → API Resource → JSON response
```

Every response uses the same envelope — see [Response Format](#response-format).

---

## Requirements

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+
- PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`

---

## Installation

```bash
# 1. Clone
git clone https://github.com/YOUR_USERNAME/farmers-market-platform-backend.git
cd farmers-market-platform-backend

# 2. Install PHP dependencies
composer install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farmers_market
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Run migrations and seed demo data
php artisan migrate --seed

# 6. Start the development server
php artisan serve
```

The API is available at `http://localhost:8000/api`.

---

## Response Format

**Success**
```json
{
  "success": true,
  "data": { "..." },
  "message": "Human-readable description"
}
```

**Error / Validation failure**
```json
{
  "success": false,
  "message": "Error description",
  "errors": { "field": ["message"] }
}
```

---

## Demo Credentials

| Role | Email | Password |
|---|---|---|
| Admin | admin@xpertbot.com | password |
| Supervisor 1 | supervisor1@xpertbot.com | password |
| Supervisor 2 | supervisor2@xpertbot.com | password |
| Operator 1 | operator1@xpertbot.com | password |
| Operator 2 | operator2@xpertbot.com | password |
| Operator 3 | operator3@xpertbot.com | password |

---

## API Endpoints

All authenticated endpoints require:
```
Authorization: Bearer <token>
Content-Type: application/json
```

### Auth

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `POST` | `/api/login` | None | Authenticate, receive Sanctum token |
| `POST` | `/api/logout` | Any role | Revoke current token |

### User Management

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `POST` | `/api/admin/supervisors` | Admin only | Create a supervisor account |
| `POST` | `/api/supervisor/operators` | Supervisor only | Create an operator account |

### Categories

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `GET` | `/api/categories` | Any role | List categories as a full recursive tree |
| `POST` | `/api/categories` | Admin / Supervisor | Create a category (optionally nested) |
| `PUT` | `/api/categories/{id}` | Admin / Supervisor | Update a category |
| `DELETE` | `/api/categories/{id}` | Admin / Supervisor | Delete a category |

### Products

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `GET` | `/api/products` | Any role | List all products with their category |
| `GET` | `/api/products/{id}` | Any role | Get a single product |
| `POST` | `/api/products` | Admin / Supervisor | Create a product |
| `PUT` | `/api/products/{id}` | Admin / Supervisor | Update a product |
| `DELETE` | `/api/products/{id}` | Admin / Supervisor | Delete a product |

### Farmers

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `GET` | `/api/farmers/search?q=` | Any role | Search by identifier or phone number |
| `GET` | `/api/farmers/{id}` | Any role | Farmer profile + total outstanding debt |
| `POST` | `/api/farmers` | Any role | Register a new farmer |

### Transactions

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `POST` | `/api/transactions` | Any role | Place a cash or credit order |

### Debts

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `GET` | `/api/farmers/{id}/debts` | Any role | List open/partial debts, oldest first (FIFO order) |

### Repayments

| Method | URL | Auth Required | Description |
|---|---|---|---|
| `POST` | `/api/repayments` | Any role | Record a commodity repayment (FIFO debt settlement) |

---

## Key Business Rules

### 1. Role Hierarchy
```
admin > supervisor > operator
```
Admin creates supervisors; supervisors create operators. Category and product writes are restricted to admin and supervisor. All roles can read all data and record transactions / repayments.

### 2. Credit Limit Enforcement
Before a credit transaction is saved:
```
current_open_debt + new_total <= farmer.credit_limit_fcfa
```
If the check fails → HTTP 422 `"Credit limit exceeded. Available: {n} FCFA"`.

### 3. Interest Calculation
The operator provides `interest_rate` (decimal 0–1, e.g. `0.15` = 15%):
```
interest_amount = round(subtotal × interest_rate)
total           = subtotal + interest_amount
```

### 4. FIFO Debt Repayment
When a farmer repays with cacao:
1. Convert kg → FCFA: `credited = round(kg × commodity_rate_fcfa)`
2. Apply credited amount to debts **oldest first**
3. Each debt becomes `partial` (if partially covered) or `paid` (if fully covered)
4. Any surplus beyond all outstanding debts is silently discarded

### 5. Debt Status Values

| Status | Meaning |
|---|---|
| `open` | Full amount still owed |
| `partial` | Partially repaid — balance remains |
| `paid` | Fully settled |

---

## Seeded Demo Data

### Farmers

| Identifier | Name | Credit Limit | Debt Status (after seed) |
|---|---|---|---|
| CI-2024-001 | Konan Kouassi | 100 000 FCFA | 6 400 remaining (`partial`) |
| CI-2024-002 | Adjoua Bamba | 150 000 FCFA | 0 remaining (`paid`) |
| CI-2024-003 | Koffi Assouman | 50 000 FCFA | No debt |
| CI-2024-004 | Amenan Coulibaly | 200 000 FCFA | 72 000 remaining (`open`) |
| CI-2024-005 | Yao N'Goran | 500 000 FCFA | No debt |

### Categories (3 roots × 3 children)

```
Pesticides  → Herbicides, Insecticides, Fongicides
Engrais     → NPK, Urée, Engrais organiques
Semences    → Maïs, Cacao, Riz
```

---

## Database Schema

```
users               id, name, email, password, role(admin|supervisor|operator), created_by
categories          id, name, parent_id (self-ref FK)
products            id, name, category_id, price_fcfa, description
farmers             id, identifier(unique), firstname, lastname, phone_number(unique), credit_limit_fcfa
transactions        id, farmer_id, operator_id, total_fcfa, payment_method(cash|credit), interest_rate, interest_amount_fcfa
transaction_items   id, transaction_id, product_id, quantity, unit_price_fcfa
debts               id, transaction_id, farmer_id, amount_fcfa, remaining_fcfa, status(open|partial|paid)
repayments          id, farmer_id, operator_id, kg_received, commodity_rate_fcfa, total_fcfa_credited
repayment_debt      id, repayment_id, debt_id, amount_applied_fcfa  ← pivot
```

---

## Postman Collection

Import `postman/farmers-market-api.json` into Postman.

Set up an environment with:
- `base_url` → `http://localhost:8000`
- `token` → (auto-filled by the Login request's test script)

---

## License

MIT — XpertBot Academy Technical Test, 2026.
