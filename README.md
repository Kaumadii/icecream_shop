# Ice Cream Shop API (Laravel)

## Overview

A REST API for an ice cream shop that manages **inventory items** (organised by flavor category) and **customer records**. It uses **Laravel Sanctum** token authentication and **role-based access control** with three roles: Owner, Manager and Cashier.

## Tech Stack

- Laravel 11+ (PHP 8.2+)
- MySQL
- Laravel Sanctum (API token authentication)
- PHPUnit (feature tests, in-memory SQLite)

## Setup

1. Clone the repository
```bash
   git clone https://github.com/<your-username>/icecream-shop.git
   cd icecream-shop
```
2. Install dependencies
```bash
   composer install
```
3. Create the environment file
```bash
   cp .env.example .env
```
   (On Windows PowerShell: `copy .env.example .env`)
4. Create an empty MySQL database named `icecream_shop`, then set these values in `.env`:
```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=icecream_shop
   DB_USERNAME=root
   DB_PASSWORD=
```
5. Generate the app key
```bash
   php artisan key:generate
```
6. Run migrations and seeders
```bash
   php artisan migrate --seed
```
7. Start the server
```bash
   php artisan serve
```
   The API is now available at `http://127.0.0.1:8000/api`.

## Seeded Users

All seeded users have the password `password`.

| Role    | Email                 |
|---------|-----------------------|
| Owner   | owner@icecream.test   |
| Manager | manager@icecream.test |
| Cashier | cashier@icecream.test |

The seeder also creates 3 flavor categories (Chocolate, Vanilla, Strawberry), 3 inventory items and 2 customers.

## Permissions

| Action                       | Owner | Manager | Cashier |
|------------------------------|:-----:|:-------:|:-------:|
| Items: view                  | Yes   | Yes     | Yes     |
| Items: add                   | Yes   | Yes     | No      |
| Items: edit                  | Yes   | Yes     | Yes     |
| Items: delete                | Yes   | No      | No      |
| Customers: view              | Yes   | Yes     | Yes     |
| Customers: add               | Yes   | Yes     | Yes     |
| Customers: edit              | Yes   | Yes     | Yes     |
| Customers: delete            | Yes   | No      | No      |

Unauthorized actions return **403 Forbidden**. Missing or invalid tokens return **401 Unauthenticated**.

## API Endpoints

Base URL: `http://127.0.0.1:8000/api`

**Always send the header `Accept: application/json`** so errors are returned as JSON.
All endpoints except login require the header `Authorization: Bearer <token>`.

### Authentication

| Method | Endpoint       | Description                       |
|--------|----------------|-----------------------------------|
| POST   | /auth/login    | Log in and receive a token        |
| POST   | /auth/logout   | Log out (revokes the current token) |

**Login request**
```json
POST /api/auth/login
{
  "email": "owner@icecream.test",
  "password": "password"
}
```

**Login response (200)**
```json
{
  "token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Olivia Owner",
    "email": "owner@icecream.test",
    "role": "owner"
  }
}
```

### Inventory Items

| Method    | Endpoint     | Description                | Allowed roles           |
|-----------|--------------|----------------------------|-------------------------|
| GET       | /items       | List items (paginated)     | Owner, Manager, Cashier |
| GET       | /items/{id}  | Get one item               | Owner, Manager, Cashier |
| POST      | /items       | Create an item             | Owner, Manager          |
| PUT/PATCH | /items/{id}  | Update an item             | Owner, Manager, Cashier |
| DELETE    | /items/{id}  | Delete an item             | Owner                   |

Query parameters for `GET /items`:
- `search`: search by item name, e.g. `/items?search=choco`
- `category_id`: filter by flavor category
- `per_page`: page size (default 15)

**Create item request**
```json
POST /api/items
{
  "category_id": 1,
  "name": "Mint Choc Chip Cone",
  "description": "Mint ice cream with chocolate chips",
  "price": 3.75,
  "quantity": 30
}
```

**Create item response (201)**
```json
{
  "id": 4,
  "category_id": 1,
  "name": "Mint Choc Chip Cone",
  "description": "Mint ice cream with chocolate chips",
  "price": "3.75",
  "quantity": 30,
  "category": { "id": 1, "name": "Chocolate" }
}
```

### Customers

| Method    | Endpoint         | Description                 | Allowed roles           |
|-----------|------------------|-----------------------------|-------------------------|
| GET       | /customers       | List customers (paginated)  | Owner, Manager, Cashier |
| GET       | /customers/{id}  | Get one customer            | Owner, Manager, Cashier |
| POST      | /customers       | Create a customer           | Owner, Manager, Cashier |
| PUT/PATCH | /customers/{id}  | Update a customer           | Owner, Manager, Cashier |
| DELETE    | /customers/{id}  | Delete a customer           | Owner                   |

Query parameters for `GET /customers`:
- `search`: search by name, email or phone
- `per_page`: page size (default 15)

**Create customer request**
```json
POST /api/customers
{
  "name": "Amal Fernando",
  "email": "amal@example.com",
  "phone": "0765551234",
  "address": "Kandy"
}
```

### Error responses

| Status | Meaning                                      |
|--------|----------------------------------------------|
| 401    | Missing or invalid token                     |
| 403    | Your role is not allowed to do this action   |
| 404    | Record not found                             |
| 422    | Validation failed (response lists the errors)|

## Running Tests

Tests use an in-memory SQLite database, so your MySQL data is not touched.

```bash
php artisan test
```

The tests cover login/logout, protected routes, the full role permission matrix for items and customers, validation errors, 404 responses and item search.

