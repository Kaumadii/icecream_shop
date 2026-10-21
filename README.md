# Ice Cream Shop API (Laravel)

## Overview
REST API for managing inventory items and customers with Sanctum token
auth and role-based access (Owner / Manager / Cashier).

## Requirements
PHP 8.2+, Composer, MySQL

## Setup
1. git clone <repo-url> && cd icecream-shop
2. composer install
3. cp .env.example .env
4. Create MySQL database `icecream_shop` and set DB_* values in .env
5. php artisan key:generate
6. php artisan migrate --seed
7. php artisan serve   (API at http://127.0.0.1:8000/api)

## Seeded users (password: `password`)
| Role    | Email                  |
|---------|------------------------|
| Owner   | owner@icecream.test    |
| Manager | manager@icecream.test  |
| Cashier | cashier@icecream.test  |
