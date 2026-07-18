# Enterprise Sales, Inventory & CRM System

A production-ready Laravel 11 business application implementing Multi-Branch Inventory and Event-Driven CRM logic.

## Completed Features
* **Multi-Branch Inventory Support:** Dynamic branch stock controls with pessimistic locking (`lockForUpdate`) to handle high-concurrency race conditions.
* **Service Layer Architecture:** Keeps controllers thin and fully decouples business domain logic.
* **Automated CRM Telemetry:** Native Eloquent scopes to track lost/inactive customers (>90 days) utilizing database indexing.
* **Event-Driven KPI & Notification:** Async automatic KPI allocation for successful employee re-engagements and transaction mail dispatches.
* **Third-Party REST API Resource:** Clean DTO serialization structures prepared for external e-commerce consumption.

## Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/your-username/your-repo.git](https://github.com/your-username/your-repo.git)
   cd your-repo

2. **Install Composer dependencies:**
   ```bash
    composer install

3. **Configure Environment:**
   ```bash
    cp .env.example .env
    php artisan key:generate

4. **Run Migrations & Seeders:**
   ```bash
    php artisan migrate:fresh --seed

5. **Start the Application & Worker Engines:**
   ```bash
   php artisan serve

6. **In another terminal tab, run the queue worker for emails:**
   ```bash
   php artisan queue:work