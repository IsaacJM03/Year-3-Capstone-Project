# Donation Redistribution App - Laravel 11 REST API

A comprehensive REST API built with Laravel 11 and Passport authentication for connecting food donors (restaurants/supermarkets) with receivers (charities/orphanages). The system enables donation management, location-based matching, claims processing, campaigns, and detailed reporting.

## Features

- **Authentication**: JWT-based authentication using Laravel Passport
- **Role-Based Access Control**: Three user roles (Admin, Donor, Receiver)
- **Donation Management**: Create, update, list, and track food donations
- **Location-Based Matching**: Find nearby donations using latitude/longitude
- **Claims System**: Receivers can claim available donations
- **Campaigns**: Receivers can create and manage fundraising campaigns
- **Reports & Analytics**: Comprehensive statistics and reports
- **Clean Architecture**: Organized under `/api/v1/` namespace

## Requirements

- PHP 8.3+
- Composer
- MySQL/PostgreSQL/SQLite
- Laravel 11

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/IsaacJM03/Year-3-Capstone-Project.git
   cd Year-3-Capstone-Project
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** (edit `.env`)
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/path/to/database.sqlite
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Setup Passport**
   ```bash
   php artisan passport:keys
   php artisan passport:client --password
   ```

7. **Seed database** (optional)
   ```bash
   php artisan db:seed
   ```

8. **Start the server**
   ```bash
   php artisan serve
   ```

## API Endpoints

Base URL: `http://localhost:8000/api/v1`

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/register` | Register new user | No |
| POST | `/login` | Login user | No |
| POST | `/logout` | Logout user | Yes |
| GET | `/user` | Get authenticated user | Yes |

**Register Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "donor",
  "phone": "+1234567890",
  "address": "123 Main St",
  "latitude": 40.7128,
  "longitude": -74.0060
}
```

**Login Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Donations

| Method | Endpoint | Description | Role Access |
|--------|----------|-------------|-------------|
| GET | `/donations` | List all donations | All |
| POST | `/donations` | Create donation | Donor |
| GET | `/donations/{id}` | Get donation details | All |
| PUT | `/donations/{id}` | Update donation | Donor (owner) |
| DELETE | `/donations/{id}` | Delete donation | Donor (owner), Admin |
| GET | `/my-donations` | Get user's donations | Donor |

**Query Parameters for Listing:**
- `status`: Filter by status (available, claimed, completed, expired)
- `food_type`: Filter by food type
- `latitude`, `longitude`, `radius`: Location-based search

**Create Donation Request:**
```json
{
  "title": "Fresh Vegetables",
  "description": "Assorted vegetables",
  "food_type": "Vegetables",
  "quantity": 50,
  "unit": "kg",
  "expiry_date": "2025-10-20",
  "pickup_address": "123 Restaurant St",
  "pickup_latitude": 40.7128,
  "pickup_longitude": -74.0060,
  "image_url": "https://example.com/image.jpg"
}
```

### Claims

| Method | Endpoint | Description | Role Access |
|--------|----------|-------------|-------------|
| GET | `/claims` | List all claims | All |
| POST | `/claims` | Create claim | Receiver |
| GET | `/claims/{id}` | Get claim details | All |
| PUT | `/claims/{id}` | Update claim status | Donor (approve/reject), Receiver (update) |
| DELETE | `/claims/{id}` | Delete claim | Receiver (owner), Admin |
| GET | `/my-claims` | Get user's claims | Receiver |

**Create Claim Request:**
```json
{
  "donation_id": 1,
  "pickup_time": "2025-10-15 14:00:00",
  "notes": "Will pick up in the afternoon"
}
```

**Update Claim (Donor - Approve/Reject):**
```json
{
  "status": "approved"
}
```

### Campaigns

| Method | Endpoint | Description | Role Access |
|--------|----------|-------------|-------------|
| GET | `/campaigns` | List all campaigns | All |
| POST | `/campaigns` | Create campaign | Receiver |
| GET | `/campaigns/{id}` | Get campaign details | All |
| PUT | `/campaigns/{id}` | Update campaign | Receiver (owner), Admin |
| DELETE | `/campaigns/{id}` | Delete campaign | Receiver (owner), Admin |
| GET | `/my-campaigns` | Get user's campaigns | Receiver |

**Create Campaign Request:**
```json
{
  "title": "Winter Food Drive",
  "description": "Collecting food for families in need",
  "goal_description": "Feed 100 families",
  "target_items": "Canned goods, dry foods",
  "start_date": "2025-10-15",
  "end_date": "2026-01-15",
  "status": "active",
  "image_url": "https://example.com/campaign.jpg"
}
```

### Reports

| Method | Endpoint | Description | Role Access |
|--------|----------|-------------|-------------|
| GET | `/reports/statistics` | System statistics | Admin |
| GET | `/reports/donations-by-food-type` | Donations grouped by food type | Admin |
| GET | `/reports/top-donors` | Top donors by donation count | Admin |
| GET | `/reports/top-receivers` | Top receivers by completed claims | Admin |
| GET | `/reports/donations-over-time` | Donation trends over time | Admin |
| GET | `/reports/user-report` | User-specific report | All (own data) |

**Query Parameters:**
- `limit`: Number of results (for top donors/receivers)
- `period`: Time period (day, week, month, year) for donations over time

## Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {your_access_token}
```

Get the token from login/register response:
```json
{
  "success": true,
  "data": {
    "user": {...},
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
  }
}
```

## Roles & Permissions

### Admin
- Full access to all endpoints
- Can view all reports and statistics
- Can manage any resource

### Donor
- Can create and manage their own donations
- Can approve/reject claims on their donations
- Can view all donations and campaigns

### Receiver
- Can create claims on available donations
- Can create and manage campaigns
- Can view their own statistics

## Database Schema

### Users Table
- id, name, email, password
- role (admin, donor, receiver)
- phone, address
- latitude, longitude
- timestamps

### Donations Table
- id, donor_id
- title, description, food_type
- quantity, unit
- expiry_date
- pickup_address, pickup_latitude, pickup_longitude
- status (available, claimed, completed, expired)
- image_url, timestamps

### Claims Table
- id, donation_id, receiver_id
- status (pending, approved, rejected, completed)
- pickup_time, notes
- timestamps

### Campaigns Table
- id, creator_id
- title, description, goal_description
- target_items
- start_date, end_date
- status (draft, active, completed, cancelled)
- image_url, timestamps

## Testing

**Test Users** (password: `password`):
- Admin: `admin@donation.app`
- Donor 1: `donor@restaurant.com`
- Donor 2: `donor@supermarket.com`
- Receiver 1: `receiver@charity.org`
- Receiver 2: `receiver@orphanage.org`

## Response Format

All responses follow a consistent JSON structure:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

## Location-Based Matching

The API supports location-based donation search using the Haversine formula to calculate distances:

```
GET /api/v1/donations?latitude=40.7128&longitude=-74.0060&radius=10
```

This returns donations within a 10km radius of the specified coordinates.

## Technologies Used

- **Laravel 11**: PHP framework
- **Laravel Passport**: OAuth2 authentication
- **Eloquent ORM**: Database interactions
- **MySQL/SQLite**: Database
- **RESTful API**: Clean API architecture

## License

This project is open-source and available under the MIT License.

## Support

For issues and questions, please open an issue on GitHub.
