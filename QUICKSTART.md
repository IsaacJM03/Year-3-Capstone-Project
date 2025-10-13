# Quick Start Guide

This guide will help you get the DRA API up and running quickly.

## Prerequisites

- PHP 8.2 or higher
- Composer
- SQLite, MySQL, or PostgreSQL

## Installation Steps

1. **Clone and install**
```bash
git clone https://github.com/IsaacJM03/Year-3-Capstone-Project.git
cd Year-3-Capstone-Project
composer install
```

2. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Set up database**
```bash
# For SQLite (default - no configuration needed)
# Database file will be created automatically at database/database.sqlite

# For MySQL/PostgreSQL, edit .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=dra
# DB_USERNAME=root
# DB_PASSWORD=
```

4. **Run migrations and seed**
```bash
php artisan migrate --seed
```

5. **Set up Passport**
```bash
php artisan passport:keys
php artisan passport:client --personal --name="DRA Personal Access Client"
```

6. **Start the server**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## Test the API

### Register a new user
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "donor",
    "phone": "0700123456",
    "organization": "My Organization"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "donor1@dra.com",
    "password": "password"
  }'
```

Save the token from the response and use it in subsequent requests:

```bash
TOKEN="your-token-here"

# Get user info
curl -X GET http://localhost:8000/api/v1/user \
  -H "Authorization: Bearer $TOKEN"

# List donations
curl -X GET http://localhost:8000/api/v1/donations \
  -H "Authorization: Bearer $TOKEN"
```

## Test Accounts (After Seeding)

**Admin:**
- Email: admin@dra.com
- Password: password

**Donors:**
- donor1@dra.com / password
- donor2@dra.com / password
- donor3@dra.com / password

**Receivers:**
- receiver1@dra.com / password
- receiver2@dra.com / password
- receiver3@dra.com / password

## Running Tests

```bash
php artisan test
```

## Troubleshooting

**Issue: Personal access client not found**
```bash
php artisan passport:client --personal --name="DRA Personal Access Client"
```

**Issue: Database not found**
```bash
# For SQLite
touch database/database.sqlite
php artisan migrate
```

**Issue: Permissions error**
```bash
chmod -R 775 storage bootstrap/cache
```

## Next Steps

- Read the full [README.md](README.md) for detailed API documentation
- Explore the available endpoints
- Customize the application for your needs
