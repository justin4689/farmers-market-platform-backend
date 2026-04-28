# Farmers Market API — XpertBot Technical Test

## Project context
REST API backend for an agricultural marketplace in Côte d'Ivoire.
Farmers buy products (pesticides, fertilizers, seeds) from points of sale.
They pay cash or on credit. Credit debts are repaid with agricultural commodities (cacao in kg).

## Stack
- Laravel 13, PHP 8.1+
- MySQL
- Laravel Sanctum (token authentication)
- Deployed on: github

## Architecture
Controllers → FormRequests → Services → Repositories → Models

app/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
├── Services/       ← all business logic here
├── Repositories/   ← all database access here
└── DTOs/           ← optional, for clean data transfer

## Key business rules (always respect these)

1. ROLES: admin > supervisor > operator. Each role accesses only its own endpoints.
2. CREDIT LIMIT: if new debt would exceed farmer credit_limit_fcfa, block the transaction.
3. INTEREST: credit price = cash price × (1 + interest_rate). Rate is configurable.
4. FIFO REPAYMENT: oldest debt is always settled first.
5. PARTIAL REPAYMENT: if repayment doesn't cover full debt, remaining balance stays open.
6. COMMODITY RATE: configurable (ex: 1 kg = 1000 FCFA). Applied at repayment time.
7. ALL DEBTS in FCFA, regardless of repayment method.

## Database schema
- users: id, name, email, password, role (admin/supervisor/operator), created_by, timestamps
- categories: id, name, parent_id (self-referential, min 2 levels), timestamps
- products: id, name, category_id, price_fcfa, description, timestamps
- farmers: id, identifier (unique), firstname, lastname, phone_number (unique), credit_limit_fcfa, timestamps
- transactions: id, farmer_id, operator_id, total_fcfa, payment_method (cash/credit), interest_rate, interest_amount_fcfa, timestamps
- transaction_items: id, transaction_id, product_id, quantity, unit_price_fcfa, timestamps
- debts: id, transaction_id, farmer_id, amount_fcfa, remaining_fcfa, status (open/partial/paid), timestamps
- repayments: id, farmer_id, operator_id, kg_received, commodity_rate_fcfa, total_fcfa_credited, timestamps
- repayment_debt (pivot): id, repayment_id, debt_id, amount_applied_fcfa, timestamps

## API response format (always use this format)
Success:
{
  "success": true,
  "data": {...},
  "message": "..."
}

Error:
{
  "success": false,
  "message": "...",
  "errors": {...}
}

## Coding rules
- Always use Services for business logic, never in Controllers
- Always use Repositories for DB queries, never raw queries in Services
- Always use Form Request classes for validation
- Always return proper HTTP status codes (200, 201, 400, 401, 403, 404, 422)
- Never expose sensitive fields (password, tokens) in responses
- Use API Resources for all JSON responses

## Deadline
Saturday, May 2, 2026

## Deliverables
1. Public GitHub repo (backend) with clean commit history + README
2. Public GitHub repo (Flutter frontend) with README
3. Flutter Web deployed on GitHub Pages
4. Laravel API deployed online (free tier)
5. YouTube walkthrough video (French or English)
6. Two submissions on https://xpertbotacademy.online/project-submission