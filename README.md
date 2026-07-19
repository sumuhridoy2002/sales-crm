# Sales, Inventory & CRM System

Laravel 11 application for SinodTech technical assessment covering multi-branch inventory, sales processing, CRM workflows, and a secured e-commerce product API.

## Completed Features

### Core Requirements

- Product catalog with SKU, name, and price
- Branch-specific inventory with pessimistic locking during sales
- Sale recording with automatic stock deduction and insufficient-stock prevention
- Customer purchase history, purchase frequency, and last purchase date
- Configurable inactive customer detection (default: 90 days)
- Promotional re-engagement emails (queued)
- Employee assignment for inactive customers
- Automatic KPI increase when an assigned inactive customer purchases again



### Bonus Features

- Multi-branch inventory and branch-aware sales
- HTML invoice email after successful purchase (Mailtrap)
- Secured REST API for third-party e-commerce (`auth:sanctum`)



## Tech Stack

- Laravel 11 (PHP 8.3+)
- MySQL
- Blade + Laravel Breeze
- Laravel Sanctum
- Mailtrap SMTP
- Database queue driver



## Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+
- MySQL 8+
- Mailtrap account (optional, for email testing)



## Setup Instructions

1. Clone the repository and install dependencies:

```bash
git clone https://github.com/sumuhridoy2002/sales-crm.git
cd sales-crm
composer install
npm install
```

1. Configure environment:

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your MySQL credentials and Mailtrap SMTP settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sales_crm
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

Create the database:

```sql
CREATE DATABASE sales_crm;
```

1. Run migrations and seeders:

```bash
php artisan migrate:fresh --seed
npm run build
```

1. Start the application:

```bash
php artisan serve
```

1. Start the queue worker (required for re-engagement emails):

```bash
php artisan queue:work
```

Or use the combined dev script:

```bash
composer run dev
```



## Seed Data & Login Credentials

All seeded users use password: `password`


| Email                                       | Role     |
| ------------------------------------------- | -------- |
| [admin@system.com](mailto:admin@system.com) | admin    |
| [rahim@system.com](mailto:rahim@system.com) | employee |
| [sadia@system.com](mailto:sadia@system.com) | employee |


Sample customers include active, inactive, and never-purchased profiles with realistic sales history.

## E-Commerce API

Generate a Sanctum token:

```bash
php artisan tinker
>>> $token = \App\Models\User::where('email', 'admin@system.com')->first()->createToken('demo')->plainTextToken;
>>> $token
```

List products:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/ecommerce/products
```

Or from Tinker (after generating `$token` above):

```php
Http::withToken($token)->get('http://localhost:8000/api/ecommerce/products')->json();
```

Create a sale via API:

```bash
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d "{\"branch_id\":1,\"customer_id\":2,\"items\":[{\"product_id\":1,\"quantity\":1}]}"
```

Or from Tinker:

```php
Http::withToken($token)->post('http://localhost:8000/api/sales', [
    'branch_id' => 1,
    'customer_id' => 2,
    'items' => [
        ['product_id' => 1, 'quantity' => 1],
    ],
])->json();
```



## Architecture Notes

- **Service layer:** `[app/Services/SalesService.php](app/Services/SalesService.php)` handles transactional sales logic.
- **Event-driven CRM:** `SaleCompleted` triggers KPI updates, customer metrics, and invoice mail.
- **Concurrency:** Branch stock uses `lockForUpdate()` to prevent overselling.
- **Configuration:** Inactive threshold and KPI points live in `[config/crm.php](config/crm.php)`.



## Running Tests

```bash
php artisan test
```



## Project Structure

- `app/Http/Controllers` — Web and API controllers
- `app/Services` — Domain/business logic
- `app/Events` & `app/Listeners` — Post-sale CRM automation
- `app/Jobs` — Async re-engagement emails
- `database/migrations` — Schema
- `database/seeders` — Demo data
- `resources/views` — Blade UI



## License

MIT