# Donation Redistribution Application (DRA) - Laravel API

A Laravel 11 RESTful API backend for connecting food donors (supermarkets, restaurants) with receivers (charities, orphanages, low-income families) to minimize food waste and fight hunger in Uganda.

## 🚀 Features

- **Laravel Passport Authentication** - OAuth2-based API authentication
- **Role-Based Access Control** - Donor, Receiver, and Admin and (Donor-Receiver) roles
- **Donation Management** - Create, update, and manage food donations
- **Claiming System** - Receivers can claim donations with approval workflow
- **Location-Based Matching** - Find nearby donations using geo-filtering (surplus to requirements)
- **Campaign Management** - Create and manage awareness campaigns (removed)
- **Admin Analytics** - Reports and statistics for monitoring activities

## 📋 Requirements

- PHP 8.2+
- Composer
- MySQL or PostgreSQL (SQLite for development)
- Laravel 11

## 🔧 Installation

1. **Clone the repository**
```bash
git clone https://github.com/IsaacJM03/Year-3-Capstone-Project.git
cd Year-3-Capstone-Project
```

2. **Install dependencies**
```bash
composer install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=sqlite
# Or for MySQL/PostgreSQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=dra
# DB_USERNAME=root
# DB_PASSWORD=
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Install Passport**
```bash
php artisan passport:keys
php artisan passport:client --personal --name="DRA Personal Access Client"
```

7. **Seed the database (optional)**
```bash
php artisan db:seed
```

This will create:
- 1 admin user
- 3 donor users
- 3 receiver users
- 5 sample donations
- 2 sample campaigns

## 🔐 Test Accounts

After seeding, you can use these accounts:

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

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication Endpoints

#### Register
```http
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "donor",
  "phone": "0700000000",
  "address": "Kampala, Uganda",
  "organization": "Supermarket Ltd"
}
```

#### Login
```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Get User
```http
GET /api/v1/user
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

### Donation Endpoints

#### Create Donation (Donor only)
```http
POST /api/v1/donations
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Fresh Bread",
  "description": "End of day bread and pastries",
  "category": "Bakery",
  "quantity": 50,
  "unit": "packs",
  "expiry_date": "2025-10-20",
  "pickup_location": "Kampala Road, Kampala",
  "latitude": 0.3476,
  "longitude": 32.5825,
  "image_url": "https://example.com/image.jpg"
}
```

#### List Donations
```http
GET /api/v1/donations
Authorization: Bearer {token}
```

#### Get Donation Details
```http
GET /api/v1/donations/{id}
Authorization: Bearer {token}
```

#### Update Donation (Donor only)
```http
PUT /api/v1/donations/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "available",
  "quantity": 40
}
```

#### Delete Donation (Donor or Admin)
```http
DELETE /api/v1/donations/{id}
Authorization: Bearer {token}
```

#### Get Nearby Donations
```http
GET /api/v1/donations/nearby?lat=0.3476&lng=32.5825&radius=5
Authorization: Bearer {token}
```

### Claim Endpoints

#### Create Claim (Receiver only)
```http
POST /api/v1/claims
Authorization: Bearer {token}
Content-Type: application/json

{
  "donation_id": 1,
  "pickup_time": "2025-10-15 14:00:00",
  "notes": "Will pick up in the afternoon"
}
```

#### List Claims
```http
GET /api/v1/claims
Authorization: Bearer {token}
```

#### Approve Claim (Donor or Admin)
```http
PUT /api/v1/claims/{id}/approve
Authorization: Bearer {token}
```

#### Mark as Delivered (Donor or Admin)
```http
PUT /api/v1/claims/{id}/deliver
Authorization: Bearer {token}
```

### Campaign Endpoints

#### Create Campaign
```http
POST /api/v1/campaigns
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Feed 100 Families",
  "description": "Help us raise funds to feed 100 families",
  "goal_amount": 5000,
  "deadline": "2025-12-31"
}
```

#### List Campaigns
```http
GET /api/v1/campaigns
Authorization: Bearer {token}
```

#### Get Campaign Details
```http
GET /api/v1/campaigns/{id}
Authorization: Bearer {token}
```

#### Donate to Campaign
```http
POST /api/v1/campaigns/{id}/donate
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 100
}
```

### Admin Endpoints

#### Get Summary Statistics
```http
GET /api/v1/admin/reports/summary
Authorization: Bearer {admin-token}
```

#### Get Donations Per Category
```http
GET /api/v1/admin/reports/donations-per-category
Authorization: Bearer {admin-token}
```

## 🔄 Response Format

All API responses follow this standard format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

Run specific tests:
```bash
php artisan test --filter AuthenticationTest
php artisan test --filter DonationTest
```

## 🏗️ Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── API/
│   │       └── V1/
│   │           ├── AuthController.php
│   │           ├── DonationController.php
│   │           ├── ClaimController.php
│   │           ├── CampaignController.php
│   │           └── AdminController.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   ├── Requests/
│   └── Resources/
├── Models/
│   ├── User.php
│   ├── Donation.php
│   ├── DonationClaim.php
│   └── Campaign.php
database/
├── factories/
│   ├── UserFactory.php
│   ├── DonationFactory.php
│   ├── DonationClaimFactory.php
│   └── CampaignFactory.php
├── migrations/
│   ├── *_create_users_table.php
│   ├── *_add_role_and_fields_to_users_table.php
│   ├── *_create_donations_table.php
│   ├── *_create_donation_claims_table.php
│   └── *_create_campaigns_table.php
└── seeders/
    └── DatabaseSeeder.php
routes/
└── api.php
tests/
├── Feature/
│   ├── AuthenticationTest.php
│   └── DonationTest.php
└── Unit/
```

## 🛠️ Tech Stack

- **Framework:** Laravel 11
- **Authentication:** Laravel Passport (OAuth2)
- **Database:** MySQL/PostgreSQL/SQLite
- **ORM:** Eloquent
- **Testing:** PHPUnit
- **API:** RESTful with JSON responses

## 📝 Database Schema

### Users
- id, name, email, password
- role (donor, receiver, admin)
- phone, address, organization
- verified (boolean)

### Donations
- id, donor_id, title, description
- category, quantity, unit
- expiry_date, status
- pickup_location, latitude, longitude
- image_url

### Donation Claims
- id, donation_id, receiver_id
- claim_status (pending, approved, rejected, delivered)
- pickup_time, notes

### Campaigns
- id, title, description, creator_id
- goal_amount, raised_amount
- deadline, status

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is open-source and available under the MIT License.

## 🙋‍♂️ Support

For support, email support@dra.com or create an issue in the repository.
