# DLC App - Backend Setup Guide

This repository contains the Laravel backend and PostgreSQL database configuration for the DLC application.

##  Prerequisites
Ensure the following tools are installed:
* **Git:** To download the project repository.
* **PHP 8.1+:** Required by Laravel 10.
* **Composer:** To manage Laravel dependencies.
* **PostgreSQL (16):** Database engine.

## 1. Clone the Project
Open your terminal and clone the repository.

**Using SSH:**
```bash
git clone git@github.com:Indus-Action-Initiatives/dlc-app.git backend
```

**Using HTTPS:**
```bash
git clone https://github.com/Indus-Action-Initiatives/dlc-app.git backend
```

## 2. Install Dependencies
Navigate into your backend directory and ensure the bootstrap cache folders exist:

```bash
cd backend
mkdir bootstrap
Cd bootstrap 
mkdir cache 
Cd ..

composer install
```

## 3. Setup the Database
Login to your PostgreSQL instance:
```bash
psql -U $(whoami) postgres
# OR if the above fails:
psql -U postgres -d postgres
```
*(Default password is usually `postgres`)*

Run the following SQL commands:
```sql
CREATE DATABASE labourchowk;
CREATE USER postgres WITH PASSWORD 'postgres';
GRANT ALL PRIVILEGES ON DATABASE labourchowk TO postgres;
ALTER DATABASE labourchowk OWNER TO postgres;
GRANT ALL ON SCHEMA public TO postgres;
ALTER SCHEMA public OWNER TO postgres;
```

## 4. Configure Environment (.env)
Create a `.env` file in the root of the `/backend` folder and paste the following:

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

VITE_MAP_SECRET_KEY=323636d60cb7037d21b710ee3d1716d8
VITE_API_BASE_URL=http://localhost:8000/api/

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=labourchowk
DB_USERNAME=postgres
DB_PASSWORD=postgres

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

UPLOAD_PATH_WORKER="upload/worker"
UPLOAD_PATH_EMPLOYER="upload/employer"

APP_KEY=base64:w2s9a18R0uQ3qYgb+fnDlw4E6TFkpiewfV8rvVr2hi4=
JWT_SECRET=8IU9YcL1gDLfNG1juGB1mC5zSO292AUkithnYmOLMUMlMwqtbnkkmMWBLpM2mGOD
```

## 5. Database Migrations & SQL Imports
Run the basic migrations:
```bash
php artisan migrate
php artisan db:seed
```

**Import States & Districts:**
1. Download `states.sql` and `districts.sql` from the provided Drive links and place them in the backend root.
2. Run these commands:
```bash
# For States
set PGPASSWORD=postgres
psql -U postgres -d labourchowk -f states.sql

# For Districts
set PGPASSWORD=postgres
psql -U postgres -d labourchowk -f districts.sql
```

## 6. Run the Application
Start the Laravel server:
```bash
php artisan serve
```

---

## Troubleshooting
* **Authentication Failed:** Verify `DB_PASSWORD` in `.env` matches your Postgres password.
* **SQLSTATE[08006]:** Ensure the PostgreSQL service is running on port 5432.
