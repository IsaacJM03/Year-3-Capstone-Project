# API Testing Guide

This guide provides sample curl commands to test all API endpoints.

## Setup

1. Start the Laravel server:
```bash
php artisan serve
```

2. Base URL: `http://localhost:8000/api/v1`

## Authentication

### Register a New User
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Donor",
    "email": "john@donor.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "donor",
    "phone": "+1234567890",
    "address": "123 Main St, City",
    "latitude": 40.7128,
    "longitude": -74.0060
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "donor@restaurant.com",
    "password": "password"
  }'
```

**Note**: Save the `access_token` from the response for subsequent requests.

### Get Authenticated User
```bash
curl -X GET http://localhost:8000/api/v1/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Donations

### List All Donations
```bash
curl -X GET http://localhost:8000/api/v1/donations \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### List Donations with Filters
```bash
# Filter by status
curl -X GET "http://localhost:8000/api/v1/donations?status=available" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"

# Filter by food type
curl -X GET "http://localhost:8000/api/v1/donations?food_type=Vegetables" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"

# Location-based search (within 5km radius)
curl -X GET "http://localhost:8000/api/v1/donations?latitude=40.7580&longitude=-73.9855&radius=5" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Create a Donation (Donor only)
```bash
curl -X POST http://localhost:8000/api/v1/donations \
  -H "Authorization: Bearer YOUR_DONOR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Fresh Fruits",
    "description": "Assorted fresh fruits",
    "food_type": "Fruits",
    "quantity": 25,
    "unit": "kg",
    "expiry_date": "2025-10-20",
    "pickup_address": "456 Donor St, City",
    "pickup_latitude": 40.7128,
    "pickup_longitude": -74.0060,
    "image_url": "https://example.com/fruits.jpg"
  }'
```

### Get Donation Details
```bash
curl -X GET http://localhost:8000/api/v1/donations/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Update a Donation (Donor owner only)
```bash
curl -X PUT http://localhost:8000/api/v1/donations/1 \
  -H "Authorization: Bearer YOUR_DONOR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "completed",
    "quantity": 20
  }'
```

### Delete a Donation (Donor owner or Admin)
```bash
curl -X DELETE http://localhost:8000/api/v1/donations/1 \
  -H "Authorization: Bearer YOUR_DONOR_TOKEN" \
  -H "Accept: application/json"
```

### Get My Donations (Donor only)
```bash
curl -X GET http://localhost:8000/api/v1/my-donations \
  -H "Authorization: Bearer YOUR_DONOR_TOKEN" \
  -H "Accept: application/json"
```

## Claims

### List All Claims
```bash
curl -X GET http://localhost:8000/api/v1/claims \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Create a Claim (Receiver only)
```bash
curl -X POST http://localhost:8000/api/v1/claims \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "donation_id": 1,
    "pickup_time": "2025-10-15 14:00:00",
    "notes": "Will pick up in the afternoon"
  }'
```

### Get Claim Details
```bash
curl -X GET http://localhost:8000/api/v1/claims/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Approve/Reject Claim (Donor only)
```bash
# Approve
curl -X PUT http://localhost:8000/api/v1/claims/1 \
  -H "Authorization: Bearer YOUR_DONOR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "approved"
  }'

# Reject
curl -X PUT http://localhost:8000/api/v1/claims/1 \
  -H "Authorization: Bearer YOUR_DONOR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "rejected"
  }'
```

### Mark Claim as Completed (Receiver only)
```bash
curl -X PUT http://localhost:8000/api/v1/claims/1 \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "completed"
  }'
```

### Delete a Claim (Receiver owner or Admin)
```bash
curl -X DELETE http://localhost:8000/api/v1/claims/1 \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Accept: application/json"
```

### Get My Claims (Receiver only)
```bash
curl -X GET http://localhost:8000/api/v1/my-claims \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Accept: application/json"
```

## Campaigns

### List All Campaigns
```bash
curl -X GET http://localhost:8000/api/v1/campaigns \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### List Active Campaigns
```bash
curl -X GET "http://localhost:8000/api/v1/campaigns?active_only=true" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Create a Campaign (Receiver only)
```bash
curl -X POST http://localhost:8000/api/v1/campaigns \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Summer Food Drive",
    "description": "Help us feed families during summer",
    "goal_description": "Feed 50 families",
    "target_items": "Canned goods, rice, pasta",
    "start_date": "2025-10-15",
    "end_date": "2025-12-31",
    "status": "active",
    "image_url": "https://example.com/campaign.jpg"
  }'
```

### Get Campaign Details
```bash
curl -X GET http://localhost:8000/api/v1/campaigns/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Update a Campaign (Receiver owner or Admin)
```bash
curl -X PUT http://localhost:8000/api/v1/campaigns/1 \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "completed"
  }'
```

### Delete a Campaign (Receiver owner or Admin)
```bash
curl -X DELETE http://localhost:8000/api/v1/campaigns/1 \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Accept: application/json"
```

### Get My Campaigns (Receiver only)
```bash
curl -X GET http://localhost:8000/api/v1/my-campaigns \
  -H "Authorization: Bearer YOUR_RECEIVER_TOKEN" \
  -H "Accept: application/json"
```

## Reports

### Get System Statistics (Admin only)
```bash
curl -X GET http://localhost:8000/api/v1/reports/statistics \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### Get Donations by Food Type (Admin only)
```bash
curl -X GET http://localhost:8000/api/v1/reports/donations-by-food-type \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### Get Top Donors (Admin only)
```bash
curl -X GET "http://localhost:8000/api/v1/reports/top-donors?limit=10" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### Get Top Receivers (Admin only)
```bash
curl -X GET "http://localhost:8000/api/v1/reports/top-receivers?limit=10" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### Get Donations Over Time (Admin only)
```bash
curl -X GET "http://localhost:8000/api/v1/reports/donations-over-time?period=month" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### Get User Report (All authenticated users)
```bash
curl -X GET http://localhost:8000/api/v1/reports/user-report \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Test Credentials

The database seeder creates the following test accounts:

| Email | Password | Role |
|-------|----------|------|
| admin@donation.app | password | Admin |
| donor@restaurant.com | password | Donor |
| donor@supermarket.com | password | Donor |
| receiver@charity.org | password | Receiver |
| receiver@orphanage.org | password | Receiver |

## Response Format

All API responses follow this format:

**Success:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

## Tips

1. Always include `Accept: application/json` header
2. For authenticated requests, include `Authorization: Bearer TOKEN`
3. Use Postman or Insomnia for easier API testing
4. Check the main README.md for detailed API documentation
