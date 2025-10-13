# Project Structure

This document provides an overview of the project structure and key files.

## Directory Structure

```
Year-3-Capstone-Project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── V1/
│   │   │   │       ├── AuthController.php        # Authentication endpoints
│   │   │   │       ├── DonationController.php    # Donation CRUD
│   │   │   │       ├── ClaimController.php       # Claim management
│   │   │   │       ├── CampaignController.php    # Campaign CRUD
│   │   │   │       └── ReportController.php      # Analytics & reports
│   │   │   └── Controller.php
│   │   └── Middleware/
│   │       └── CheckRole.php                     # Role-based access control
│   └── Models/
│       ├── User.php                              # User model with roles
│       ├── Donation.php                          # Donation model
│       ├── Claim.php                             # Claim model
│       └── Campaign.php                          # Campaign model
├── bootstrap/
│   └── app.php                                   # App configuration
├── config/
│   ├── auth.php                                  # Auth configuration
│   └── passport.php                              # Passport settings
├── database/
│   ├── migrations/                               # Database migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2025_10_13_092055_create_oauth_auth_codes_table.php
│   │   ├── 2025_10_13_092056_create_oauth_access_tokens_table.php
│   │   ├── 2025_10_13_092057_create_oauth_refresh_tokens_table.php
│   │   ├── 2025_10_13_092058_create_oauth_clients_table.php
│   │   ├── 2025_10_13_092059_create_oauth_device_codes_table.php
│   │   ├── 2025_10_13_092201_create_donations_table.php
│   │   ├── 2025_10_13_092208_create_claims_table.php
│   │   └── 2025_10_13_092209_create_campaigns_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php                    # Main seeder
│       ├── UserSeeder.php                        # User test data
│       ├── DonationSeeder.php                    # Donation test data
│       └── CampaignSeeder.php                    # Campaign test data
├── routes/
│   ├── api.php                                   # API routes (v1)
│   ├── web.php                                   # Web routes
│   └── console.php                               # Console routes
├── tests/                                         # Test files
├── .env.example                                   # Environment example
├── composer.json                                  # PHP dependencies
├── README.md                                      # Main documentation
├── API_TESTING_GUIDE.md                          # Testing guide
└── postman_collection.json                       # Postman collection
```

## Key Files Explained

### Controllers

#### AuthController.php
Handles user authentication:
- `register()` - Register new users
- `login()` - Authenticate users and issue tokens
- `logout()` - Revoke user tokens
- `user()` - Get authenticated user details

#### DonationController.php
Manages donations:
- `index()` - List all donations with filters
- `store()` - Create new donation (donors only)
- `show()` - Get donation details
- `update()` - Update donation (owner/admin only)
- `destroy()` - Delete donation (owner/admin only)
- `myDonations()` - Get user's own donations

#### ClaimController.php
Handles claim management:
- `index()` - List all claims
- `store()` - Create new claim (receivers only)
- `show()` - Get claim details
- `update()` - Update claim status (donor approves, receiver updates)
- `destroy()` - Delete claim (owner/admin only)
- `myClaims()` - Get user's own claims

#### CampaignController.php
Manages campaigns:
- `index()` - List all campaigns
- `store()` - Create new campaign (receivers only)
- `show()` - Get campaign details
- `update()` - Update campaign (owner/admin only)
- `destroy()` - Delete campaign (owner/admin only)
- `myCampaigns()` - Get user's own campaigns

#### ReportController.php
Provides analytics:
- `statistics()` - System-wide statistics (admin only)
- `donationsByFoodType()` - Donations grouped by food type
- `topDonors()` - Top donors by donation count
- `topReceivers()` - Top receivers by claims
- `donationsOverTime()` - Donation trends
- `userReport()` - User-specific statistics

### Models

#### User.php
- Extends Laravel's Authenticatable
- Uses HasApiTokens trait for Passport
- Has relationships: donations, claims, campaigns
- Methods: `hasRole()`, `hasAnyRole()`
- Fields: name, email, password, role, phone, address, latitude, longitude

#### Donation.php
- Represents food donations
- Relationships: donor (User), claims (Claim)
- Scopes: `available()`, `nearby()`
- Fields: donor_id, title, description, food_type, quantity, unit, expiry_date, pickup_address, pickup_latitude, pickup_longitude, status, image_url

#### Claim.php
- Represents claims on donations
- Relationships: donation (Donation), receiver (User)
- Fields: donation_id, receiver_id, status, pickup_time, notes

#### Campaign.php
- Represents fundraising campaigns
- Relationships: creator (User)
- Scopes: `active()`
- Fields: creator_id, title, description, goal_description, target_items, start_date, end_date, status, image_url

### Middleware

#### CheckRole.php
- Custom middleware for role-based access control
- Usage: `Route::middleware('role:admin,donor')`
- Checks if authenticated user has required role(s)
- Returns 403 Forbidden if unauthorized

### Routes (api.php)

All routes are prefixed with `/api/v1`:

**Public routes:**
- POST `/register` - User registration
- POST `/login` - User login

**Protected routes (require auth:api):**
- POST `/logout` - User logout
- GET `/user` - Get authenticated user
- Resource `/donations` - Donation CRUD
- Resource `/claims` - Claim CRUD
- Resource `/campaigns` - Campaign CRUD
- GET `/my-donations` - User's donations (donor role)
- GET `/my-claims` - User's claims (receiver role)
- GET `/my-campaigns` - User's campaigns (receiver role)
- GET `/reports/*` - Various report endpoints (most admin-only)

## Database Schema

### users
- id (primary key)
- name, email, password
- role (enum: admin, donor, receiver)
- phone, address
- latitude, longitude (for location matching)
- timestamps

### donations
- id (primary key)
- donor_id (foreign key → users)
- title, description, food_type
- quantity, unit
- expiry_date
- pickup_address, pickup_latitude, pickup_longitude
- status (enum: available, claimed, completed, expired)
- image_url
- timestamps

### claims
- id (primary key)
- donation_id (foreign key → donations)
- receiver_id (foreign key → users)
- status (enum: pending, approved, rejected, completed)
- pickup_time
- notes
- timestamps

### campaigns
- id (primary key)
- creator_id (foreign key → users)
- title, description, goal_description
- target_items
- start_date, end_date
- status (enum: draft, active, completed, cancelled)
- image_url
- timestamps

### OAuth tables (Passport)
- oauth_auth_codes
- oauth_access_tokens
- oauth_refresh_tokens
- oauth_clients
- oauth_device_codes

## API Response Format

All API responses follow this consistent format:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

**Paginated Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [ ... ],
    "first_page_url": "...",
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

## Authentication Flow

1. **Register/Login** → Receive access token
2. **Store token** → Save for subsequent requests
3. **Make API calls** → Include token in Authorization header
4. **Token expires** → Re-authenticate to get new token
5. **Logout** → Revoke token

## Role-Based Access

### Admin
- Full access to all endpoints
- Can manage any resource
- Access to all reports

### Donor
- Can create and manage donations
- Can approve/reject claims on their donations
- Can view donations and campaigns
- Access to user-specific reports

### Receiver
- Can create claims on available donations
- Can create and manage campaigns
- Can update their own claims
- Access to user-specific reports

## Testing

### Using curl
See `API_TESTING_GUIDE.md` for detailed curl examples.

### Using Postman
1. Import `postman_collection.json`
2. Set the `base_url` variable to your server URL
3. Login to get a token
4. Set the `token` variable
5. Test all endpoints

### Using Seeders
```bash
php artisan db:seed
```
Creates test users:
- admin@donation.app (Admin)
- donor@restaurant.com (Donor)
- donor@supermarket.com (Donor)
- receiver@charity.org (Receiver)
- receiver@orphanage.org (Receiver)

All with password: `password`

## Common Tasks

### Add New API Endpoint
1. Add route in `routes/api.php`
2. Create/update controller method
3. Test with curl or Postman

### Add New Model
1. Create migration: `php artisan make:model ModelName -m`
2. Define schema in migration
3. Define relationships and attributes in model
4. Run migration: `php artisan migrate`

### Add New Seeder
1. Create seeder: `php artisan make:seeder SeederName`
2. Implement `run()` method
3. Register in `DatabaseSeeder.php`
4. Run: `php artisan db:seed`

### Update Database Schema
1. Create new migration: `php artisan make:migration description`
2. Define changes in `up()` and `down()` methods
3. Run migration: `php artisan migrate`

## Security Considerations

- All passwords are hashed using Laravel's Hash facade
- API uses JWT tokens via Laravel Passport
- Role-based middleware protects sensitive endpoints
- Input validation on all request data
- CORS configured for API access
- Environment variables for sensitive data

## Performance Optimizations

- Database indexes on foreign keys
- Eager loading relationships with `with()`
- Pagination for large datasets (15 items per page)
- Efficient location search using Haversine formula

## Future Enhancements

Potential features to add:
- Email notifications for claims
- File upload for donation images
- Real-time notifications with WebSockets
- Mobile app integration
- Advanced search filters
- Donation history tracking
- Campaign progress tracking
- Rating system for donors/receivers
- SMS notifications
- Export reports to PDF/Excel
