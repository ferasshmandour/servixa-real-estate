# Software Requirements Specification (SRS)
## Servixa — All Services in One Place

**Version:** 1.0
**Date:** March 2026
**Based on:** PRD v1.0 + Project Requirements PDF + Time Planning PDF + Figma Designs

---

## 1. System Design

### Overview
Servixa is a **three-tier system** composed of three independently operating layers that share one database and one backend:

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT LAYER                            │
│  Mobile App (iOS/Android)     Admin Dashboard (Web Browser) │
│  Consumes REST API            Uses Blade + Session Auth     │
└────────────────────┬────────────────────────┬───────────────┘
                     │ HTTPS / JSON           │ HTTPS / HTML
┌────────────────────▼────────────────────────▼───────────────┐
│                   APPLICATION LAYER                         │
│                  Laravel 12 (PHP 8.2)                       │
│                                                             │
│  ┌─────────────────┐     ┌──────────────────────────────┐  │
│  │   REST API       │     │     Admin Dashboard           │  │
│  │  /api/v1/*       │     │     /admin/*                  │  │
│  │  Passport OAuth2 │     │     Session Auth              │  │
│  └─────────────────┘     └──────────────────────────────┘  │
│                                                             │
│  Services: OtpService · NotificationService · ChatService   │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│                     DATA LAYER                              │
│         MySQL Database (via XAMPP)                          │
└─────────────────────────────────────────────────────────────┘

EXTERNAL SERVICES
  UltraMsg API       → WhatsApp OTP delivery
  Firebase FCM       → Push notifications to mobile
  Pusher             → Real-time chat broadcasting
```

### Key Design Decisions
- **One Laravel codebase** serves both the API and admin dashboard
- **Blade templates** are used exclusively for the admin dashboard
- **Mobile app** is a separate client that consumes the REST API (not built here)
- **Admin dashboard** is a traditional server-rendered web app (form submissions, redirects)
- **API** follows RESTful conventions with JSON responses

---

## 2. Architecture Pattern

### Pattern: MVC + Service Layer

```
Request
  │
  ▼
Middleware (Auth, Permission, Locale)
  │
  ▼
Controller (thin — validates input, calls service, returns response)
  │
  ▼
Service Class (business logic lives here)
  │
  ▼
Model / Eloquent (database interaction)
  │
  ▼
API Resource / Blade View (formats the output)
```

### Layer Responsibilities

| Layer | Responsibility | Examples |
|---|---|---|
| **Middleware** | Auth checks, permission gates, locale setting | `auth:api` (Passport), `role:admin`, `SetLocale` |
| **Controller** | Receive request, validate, call service, return | `ServiceController`, `OrderController` |
| **Service** | Business logic, rules enforcement | `OtpService`, `BusinessAccountService` |
| **Model** | Database relationships, scopes, casts | `User`, `Service`, `BusinessAccount` |
| **API Resource** | Transform model → JSON for mobile | `ServiceResource`, `OrderResource` |
| **Blade View** | Render HTML for admin dashboard | `admin/services/index.blade.php` |

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php
│   │   │   ├── BusinessAccountController.php
│   │   │   ├── ServiceController.php
│   │   │   ├── OrderController.php
│   │   │   ├── RatingController.php
│   │   │   ├── FavoriteController.php
│   │   │   ├── ReportController.php
│   │   │   ├── ChatController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ProfileController.php
│   │   └── Admin/
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── BusinessAccountController.php
│   │       ├── ServiceController.php
│   │       ├── CategoryController.php
│   │       ├── CityController.php
│   │       ├── SliderController.php
│   │       ├── ReportController.php
│   │       ├── RoleController.php
│   │       └── AdminUserController.php
│   ├── Middleware/
│   │   ├── SetLocale.php
│   │   └── CheckPermission.php
│   └── Requests/
│       ├── RegisterRequest.php
│       ├── CreateServiceRequest.php
│       └── CreateBusinessAccountRequest.php
├── Models/
│   ├── User.php
│   ├── Admin.php
│   ├── BusinessAccount.php
│   ├── BusinessAccountFile.php
│   ├── ActivityType.php
│   ├── Category.php
│   ├── DynamicField.php
│   ├── Slider.php
│   ├── City.php
│   ├── Service.php
│   ├── ServiceImage.php
│   ├── ServiceDynamicValue.php
│   ├── Order.php
│   ├── Rating.php
│   ├── Favorite.php
│   ├── Report.php
│   ├── Conversation.php
│   ├── Message.php
│   └── Notification.php
├── Services/
│   ├── OtpService.php
│   ├── NotificationService.php
│   └── ChatService.php
└── Http/Resources/
    ├── UserResource.php
    ├── BusinessAccountResource.php
    ├── ServiceResource.php
    ├── OrderResource.php
    ├── RatingResource.php
    ├── ConversationResource.php
    └── MessageResource.php
```

---

## 3. State Management

All state is **server-side**. The mobile app is stateless — it sends a Bearer token with every request.

### User Authentication State
```
Not Authenticated
      │ POST /api/v1/auth/register
      ▼
Phone Unverified (account created, OTP sent via UltraMsg)
      │ POST /api/v1/auth/verify-otp
      ▼
Authenticated (Passport OAuth2 access_token issued, expires based on config)
      │ POST /api/v1/auth/logout
      ▼
Not Authenticated
```

### Business Account State Machine
```
                  ┌─────────┐
   User submits   │         │  Admin rejects
   ──────────────►│ PENDING │──────────────► REJECTED
                  │         │
                  └────┬────┘
                       │ Admin approves
                       ▼
                   APPROVED  ◄──── (stays here unless admin rejects)
                       │
                       │ Can post services
                       │ Can request services
                       ▼
                  (Active in system)
```

### Service (Ad) State Machine
```
                  ┌─────────┐
   Provider posts │         │  Admin rejects
   ──────────────►│ PENDING │──────────────► REJECTED
                  │         │
                  └────┬────┘
                       │ Admin approves
                       ▼
                   APPROVED ──────────────► PENDING  (if provider edits)
                       │
                       │ Visible on mobile app
                       ▼
                  (Available for requests)
```

### Order State Machine
```
                  ┌─────────┐
  Requester sends │         │  Provider rejects
  ───────────────►│ PENDING │───────────────► REJECTED
                  │         │
                  └────┬────┘
                       │ Provider accepts
                       ▼
                   ACCEPTED
                       │
                       │ Requester can now leave a rating
                       ▼
                  (Completed)
```

### Rating Gate
```
Order must be ACCEPTED before rating is allowed
One rating per order (enforced at DB level with unique constraint)
```

---

## 4. Data Flow

### User Registration & OTP Flow
```
Mobile App                Laravel API               UltraMsg
    │                         │                         │
    │── POST /register ───────►│                         │
    │                         │── POST api.ultramsg.com ►│
    │                         │   {to: phone, body: OTP} │
    │                         │◄── 200 OK ───────────────│
    │◄── 201 {message: "OTP sent"} │                    │
    │                         │                         │
    │── POST /verify-otp ─────►│                         │
    │◄── 200 {token: "..."} ──│                         │
```

### Service Approval Flow
```
Mobile App       Laravel API          Admin Dashboard
    │                │                      │
    │── POST /services ►│                   │
    │                │ (status=pending)      │
    │◄── 201 OK ─────│                      │
    │                │                      │
    │                │◄── GET /admin/services (pending list)
    │                │                      │
    │                │◄── POST /admin/services/{id}/approve
    │                │ (status=approved)     │
    │                │── Firebase push ─────►│ (to user's device)
    │◄── push notification received          │
```

### Real-Time Chat Flow
```
User A (mobile)     Laravel API          Pusher           User B (mobile)
    │                   │                   │                   │
    │── POST /messages ─►│                  │                   │
    │                   │── broadcast ──────►│                  │
    │◄── 201 {message}  │   MessageSent      │                  │
    │                   │                   │── push to channel ►│
    │                   │                   │                   │ receives in real-time
```

### Service Request Flow
```
Mobile App                    Laravel API
    │                              │
    │── POST /orders ──────────────►│
    │   {service_id,                │ Validate: requester has approved
    │    requester_business_id,     │ business account
    │    needed_at, quantity,       │ Validate: quantity ≤ available
    │    details}                   │
    │                              │── Save order (status=pending)
    │◄── 201 {order}               │── Firebase push to provider
    │                              │
```

---

## 5. Technical Stack

### Backend

| Component | Technology | Version | Purpose |
|---|---|---|---|
| Framework | Laravel | 12.x | Core MVC framework |
| Language | PHP | 8.2+ | Server-side language |
| Database | MySQL | 8.x | Relational data storage |
| Local Server | XAMPP | Latest | Apache + MySQL on Windows |
| API Auth | Laravel Passport | 12.x | OAuth2 token-based mobile auth (Password Grant) |
| Admin Auth | Laravel built-in | — | Session-based web auth |
| Roles & Perms | spatie/laravel-permission | 6.x | Role/permission system |
| Image Handling | intervention/image | 3.x | Resize, store, optimize images |
| HTTP Client | Laravel Http (built-in) | — | UltraMsg WhatsApp OTP calls |
| Push Notifications | kreait/laravel-firebase | 7.x | Firebase FCM push |
| Real-time Chat | pusher/pusher-php-server | 7.x | Broadcast chat events |
| Broadcasting | Laravel Echo (server) | — | Pusher event broadcasting |

### Frontend (Admin Dashboard Only)

| Component | Technology | Purpose |
|---|---|---|
| Templating | Laravel Blade | Server-rendered HTML |
| CSS Framework | Tailwind CSS | Styling |
| JS (minimal) | Alpine.js | Dropdowns, modals, toggles |
| Icons | Heroicons | UI icons |
| Charts | Chart.js | Dashboard stats |

### External Services

| Service | Provider | Purpose |
|---|---|---|
| WhatsApp OTP | UltraMsg | Send OTP to user's phone via WhatsApp |
| Push Notifications | Firebase FCM | Push alerts to mobile app |
| Real-time | Pusher | WebSocket-based chat |
| Maps | Google Maps (embed) | Location display on service detail |

### Composer Packages to Install

```bash
composer require laravel/passport
composer require spatie/laravel-permission
composer require intervention/image
composer require kreait/laravel-firebase
composer require pusher/pusher-php-server
```

---

## 6. Authentication Process

### 6.1 User Authentication (Mobile API)

**Registration:**
```
POST /api/v1/auth/register
Body: { first_name, last_name, phone, email, country, password, password_confirmation }

1. Validate input
2. Create user (is_verified = false)
3. Generate 6-digit OTP → store in cache (key: "otp_{phone}", TTL: 10 min)
4. Send OTP via UltraMsg:
   Http::post("https://api.ultramsg.com/{instanceId}/messages/chat", [
       'token' => config('services.ultramsg.token'),
       'to'    => $phone,
       'body'  => "Your Servixa OTP is: {$otp}"
   ])
5. Return 201 { message: "OTP sent to your WhatsApp" }
```

**OTP Verification:**
```
POST /api/v1/auth/verify-otp
Body: { phone, otp }

1. Check cache for "otp_{phone}"
2. If matches → set user.is_verified = true, clear cache
3. Issue Passport OAuth2 access_token + refresh_token
4. Return 200 { access_token, refresh_token, token_type: "Bearer", expires_in, user }
```

**Login:**
```
POST /api/v1/auth/login
Body: { phone, password }

1. Validate credentials
2. If valid → generate OTP → send via UltraMsg
3. Return 200 { message: "OTP sent" }

POST /api/v1/auth/login/verify
Body: { phone, otp }

1. Verify OTP from cache
2. Issue Passport OAuth2 access_token + refresh_token
3. Return 200 { access_token, refresh_token, token_type: "Bearer", expires_in, user }
```

**Authenticated Requests:**
```
All protected API routes require:
Header: Authorization: Bearer {access_token}
Middleware: auth:api   ← Passport guard (not sanctum)
```

**Token Refresh:**
```
POST /api/v1/auth/refresh
Body: { refresh_token }

1. Validate refresh_token via Passport
2. Issue new access_token + refresh_token
3. Return 200 { access_token, refresh_token, expires_in }
```

**Logout:**
```
POST /api/v1/auth/logout
1. Revoke current Passport access_token (token->revoke())
2. Return 200 { message: "Logged out" }
```

---

### 6.2 Admin Authentication (Dashboard)

**Login:**
```
POST /admin/login
Body: { email, password }

1. Validate credentials against admins table
2. Start Laravel session
3. Redirect to /admin/dashboard
```

**Protected Admin Routes:**
```
Middleware stack: ['auth:admin', 'permission:{permission_name}']
Example: 'approve-business-accounts' permission required to approve accounts
```

**Auth Guards Config:**
```php
// config/auth.php
'guards' => [
    'api' => [
        'driver'   => 'passport',      // ← Passport handles mobile API tokens
        'provider' => 'users',
    ],
    'admin' => [
        'driver'   => 'session',       // ← Session handles admin dashboard
        'provider' => 'admins',
    ],
]
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model'  => App\Models\User::class,
    ],
    'admins' => [
        'driver' => 'eloquent',
        'model'  => App\Models\Admin::class,
    ],
]
```

**Passport Setup Commands (run once):**
```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

**User Model uses:**
```php
use Laravel\Passport\HasApiTokens;   // ← replaces Sanctum's HasApiTokens
```

---

## 7. Route Design

### API Routes (`routes/api.php`)

```
/api/v1/

├── auth/
│   ├── POST   register
│   ├── POST   verify-otp
│   ├── POST   login
│   ├── POST   login/verify
│   └── POST   logout                      [auth:api]

├── profile/                               [auth:api]
│   ├── GET    /                           get my profile
│   └── PUT    /                           update profile

├── business-accounts/                     [auth:api]
│   ├── GET    /                           list my business accounts
│   ├── POST   /                           create business account
│   ├── GET    /{id}                       show one
│   └── PUT    /{id}                       update

├── services/
│   ├── GET    /                           list approved services (public)
│   ├── GET    /{id}                       show service detail (public)
│   ├── POST   /                [auth]     create service
│   ├── PUT    /{id}            [auth]     edit service
│   └── DELETE /{id}            [auth]     delete service

├── orders/                                [auth:api]
│   ├── GET    /received                   received orders (as provider)
│   ├── GET    /sent                       sent orders (as requester)
│   ├── POST   /                           create order (request a service)
│   ├── PATCH  /{id}/accept                accept an order
│   ├── PATCH  /{id}/reject                reject an order
│   └── DELETE /{id}                       delete sent order

├── ratings/                               [auth:api]
│   └── POST   /                           submit rating (requires accepted order)

├── favorites/                             [auth:api]
│   ├── GET    /                           list my favorites
│   ├── POST   /                           add to favorites
│   └── DELETE /{service_id}              remove from favorites

├── reports/                               [auth:api]
│   └── POST   /                           report a service

├── conversations/                         [auth:api]
│   ├── GET    /                           list all conversations
│   ├── POST   /                           start conversation (from service detail)
│   ├── GET    /{id}/messages              get messages in conversation
│   └── POST   /{id}/messages              send a message

├── notifications/                         [auth:api]
│   ├── GET    /                           list notifications
│   └── PATCH  /{id}/read                  mark as read

├── categories/
│   ├── GET    /                           list all categories (public)
│   └── GET    /{id}/subcategories         list subcategories (public)

└── cities/
    └── GET    /                           list all cities (public)
```

---

### Admin Dashboard Routes (`routes/web.php`)

```
/admin/

├── GET    login                           show login form
├── POST   login                           process login
├── POST   logout                          logout

├── GET    dashboard                       overview stats

├── business-accounts/
│   ├── GET    /                           list (filter by status)
│   ├── GET    /{id}                       show detail + documents
│   ├── PATCH  /{id}/approve              approve
│   └── PATCH  /{id}/reject               reject

├── services/
│   ├── GET    /                           list (filter by status)
│   ├── GET    /{id}                       show detail
│   ├── PATCH  /{id}/approve              approve
│   └── PATCH  /{id}/reject               reject

├── categories/
│   ├── GET    /                           list all
│   ├── GET    /create                     create form
│   ├── POST   /                           store
│   ├── GET    /{id}/edit                  edit form
│   ├── PUT    /{id}                       update
│   ├── DELETE /{id}                       delete
│   └── /{id}/dynamic-fields/             CRUD for dynamic fields

├── cities/
│   ├── GET / POST / PUT / DELETE          full CRUD

├── sliders/
│   ├── GET / POST / PUT / DELETE          full CRUD

├── reports/
│   ├── GET    /                           list all reports
│   └── PATCH  /{id}/resolve              mark resolved

├── roles/                                 [super-admin only]
│   ├── GET / POST / PUT / DELETE          full CRUD
│   └── /{id}/permissions                 assign permissions

└── admins/                                [super-admin only]
    ├── GET / POST / PUT / DELETE          full CRUD
    └── /{id}/role                        assign role
```

---

## 8. API Design

All responses follow this structure:
```json
{
  "success": true | false,
  "message": "Human readable message",
  "data": { } | [ ]
}
```

Error responses:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field": ["error message"]
  }
}
```

---

### Auth Endpoints

**POST** `/api/v1/auth/register`
```json
Request:
{
  "first_name": "Ahmad",
  "last_name": "Ali",
  "phone": "+963999000111",
  "email": "ahmad@example.com",    // optional
  "country": "Syria",
  "password": "secret123",
  "password_confirmation": "secret123"
}

Response 201:
{ "success": true, "message": "OTP sent to your WhatsApp" }
```

**POST** `/api/v1/auth/verify-otp`
```json
Request:  { "phone": "+963999000111", "otp": "483920" }
Response: { "success": true, "data": { "token": "...", "user": { UserResource } } }
```

**POST** `/api/v1/auth/login`
```json
Request:  { "phone": "+963999000111", "password": "secret123" }
Response: { "success": true, "message": "OTP sent to your WhatsApp" }
```

---

### Business Account Endpoints

**POST** `/api/v1/business-accounts`
```json
Request (multipart/form-data):
{
  "activity_type_id": 1,
  "license_number": "SY-123456",
  "name_ar": "شركة النور",
  "name_en": "Al-Noor Company",
  "activities": "Construction and contracting",
  "details": "Specialized in residential projects",
  "city_id": 3,
  "address": "Damascus, Mazzeh",
  "latitude": 33.5102,
  "longitude": 36.2913,
  "files[]": [ file1.pdf, file2.jpg ]
}

Response 201:
{
  "success": true,
  "message": "Business account submitted for review",
  "data": { BusinessAccountResource }
}
```

**GET** `/api/v1/business-accounts`
```json
Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name_ar": "شركة النور",
      "name_en": "Al-Noor Company",
      "status": "approved",
      "activity_type": "Supplier",
      "city": "Damascus"
    }
  ]
}
```

---

### Service Endpoints

**POST** `/api/v1/services`
```json
Request (multipart/form-data):
{
  "business_account_id": 2,
  "category_id": 1,
  "subcategory_id": 4,
  "title_ar": "رافعة شوكية",
  "title_en": "Forklift Machine",
  "description_ar": "...",
  "description_en": "...",
  "available_quantity": 3,
  "type": "rent",                  // sale | rent
  "price": 500,
  "currency": "USD",               // USD | SYP
  "latitude": 33.5102,
  "longitude": 36.2913,
  "main_image": file,
  "images[]": [file1, file2],
  "dynamic_values": {              // dynamic fields from category
    "5": "Caterpillar",            // field_id: value
    "6": "5 tons"
  }
}

Response 201:
{ "success": true, "message": "Service submitted for review", "data": { ServiceResource } }
```

**GET** `/api/v1/services`
```json
Query params:
  ?search=forklift
  &category_id=1
  &subcategory_id=4
  &city_id=3
  &type=rent
  &min_price=100
  &max_price=1000
  &currency=USD
  &per_page=15

Response:
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [ ServiceResource ],
    "total": 48
  }
}
```

**GET** `/api/v1/services/{id}`
```json
Response:
{
  "success": true,
  "data": {
    "id": 5,
    "title": "Forklift Machine",
    "description": "...",
    "price": 500,
    "currency": "USD",
    "type": "rent",
    "available_quantity": 3,
    "main_image": "https://...",
    "images": ["https://...", "https://..."],
    "latitude": 33.5102,
    "longitude": 36.2913,
    "category": { "id": 1, "name": "Equipment" },
    "subcategory": { "id": 4, "name": "Heavy Vehicles" },
    "business_account": { "id": 2, "name": "Al-Noor Company" },
    "dynamic_values": [ { "label": "Brand", "value": "Caterpillar" } ],
    "average_rating": 4.2,
    "ratings_count": 13,
    "ratings": [ RatingResource ]
  }
}
```

---

### Order Endpoints

**POST** `/api/v1/orders`
```json
Request:
{
  "service_id": 5,
  "requester_business_id": 1,
  "needed_at": "2026-04-15",
  "quantity": 2,
  "details": "Need for 3 days at the Mazzeh site"
}

Response 201:
{ "success": true, "data": { OrderResource } }
```

**PATCH** `/api/v1/orders/{id}/accept`
```json
Response: { "success": true, "message": "Order accepted" }
// Side effect: Firebase push sent to requester
```

**PATCH** `/api/v1/orders/{id}/reject`
```json
Response: { "success": true, "message": "Order rejected" }
// Side effect: Firebase push sent to requester
```

---

### Rating Endpoint

**POST** `/api/v1/ratings`
```json
Request:
{
  "order_id": 12,
  "rating": 4,
  "comment": "Excellent service, on time"
}

Validation rules:
- order must belong to auth user's business account
- order status must be "accepted"
- no existing rating for this order_id

Response 201:
{ "success": true, "message": "Rating submitted" }
```

---

### Chat Endpoints

**POST** `/api/v1/conversations`
```json
Request: { "service_id": 5 }
// Creates conversation between auth user and service owner (or returns existing)

Response 201: { "success": true, "data": { ConversationResource } }
```

**POST** `/api/v1/conversations/{id}/messages`
```json
Request: { "content": "Is this available next week?" }

Response 201: { "success": true, "data": { MessageResource } }
// Side effect: Pusher broadcasts MessageSent event on channel conversation.{id}
```

---

## 9. Database Design (ERD)

### Table Definitions

---

**`users`**
```
id                  BIGINT PK AUTO_INCREMENT
first_name          VARCHAR(100)
last_name           VARCHAR(100)
phone               VARCHAR(20) UNIQUE
email               VARCHAR(150) UNIQUE NULLABLE
country             VARCHAR(100)
password            VARCHAR(255)
is_verified         BOOLEAN DEFAULT false
profile_image       VARCHAR(255) NULLABLE
device_token        VARCHAR(255) NULLABLE          ← Firebase push token
created_at / updated_at
```

---

**`admins`**
```
id                  BIGINT PK
name                VARCHAR(150)
email               VARCHAR(150) UNIQUE
password            VARCHAR(255)
role_id             BIGINT FK → roles.id NULLABLE
created_at / updated_at
```

---

**`roles`**
```
id                  BIGINT PK
name                VARCHAR(100) UNIQUE            ← e.g. "Content Reviewer"
guard_name          VARCHAR(50) DEFAULT 'admin'
created_at / updated_at
```

**`permissions`**
```
id                  BIGINT PK
name                VARCHAR(150) UNIQUE            ← e.g. "approve-services"
guard_name          VARCHAR(50)
created_at / updated_at
```

**`role_has_permissions`** (pivot)
```
permission_id       BIGINT FK
role_id             BIGINT FK
PRIMARY KEY (permission_id, role_id)
```

---

**`cities`**
```
id                  BIGINT PK
name_ar             VARCHAR(100)
name_en             VARCHAR(100)
created_at / updated_at
```

---

**`activity_types`**
```
id                  BIGINT PK
name_ar             VARCHAR(100)
name_en             VARCHAR(100)
created_at / updated_at
```

---

**`business_accounts`**
```
id                  BIGINT PK
user_id             BIGINT FK → users.id
activity_type_id    BIGINT FK → activity_types.id
city_id             BIGINT FK → cities.id
license_number      VARCHAR(100)
name_ar             VARCHAR(200)
name_en             VARCHAR(200)
activities          TEXT
details             TEXT
address             TEXT NULLABLE
latitude            DECIMAL(10,7) NULLABLE
longitude           DECIMAL(10,7) NULLABLE
status              ENUM('pending','approved','rejected') DEFAULT 'pending'
rejection_reason    TEXT NULLABLE
created_at / updated_at
```

---

**`business_account_files`**
```
id                  BIGINT PK
business_account_id BIGINT FK → business_accounts.id
file_path           VARCHAR(255)
file_type           ENUM('document','image')
created_at / updated_at
```

---

**`categories`**
```
id                  BIGINT PK
parent_id           BIGINT FK → categories.id NULLABLE   ← NULL = main category
name_ar             VARCHAR(150)
name_en             VARCHAR(150)
icon                VARCHAR(255) NULLABLE
sort_order          INT DEFAULT 0
created_at / updated_at
```

---

**`dynamic_fields`**
```
id                  BIGINT PK
category_id         BIGINT FK → categories.id
label_ar            VARCHAR(150)
label_en            VARCHAR(150)
field_type          ENUM('text','number','select','textarea','boolean')
options             JSON NULLABLE                  ← for select type: ["Option A","Option B"]
is_required         BOOLEAN DEFAULT false
sort_order          INT DEFAULT 0
created_at / updated_at
```

---

**`sliders`**
```
id                  BIGINT PK
image               VARCHAR(255)
link                VARCHAR(255) NULLABLE
sort_order          INT DEFAULT 0
is_active           BOOLEAN DEFAULT true
created_at / updated_at
```

---

**`services`**
```
id                  BIGINT PK
business_account_id BIGINT FK → business_accounts.id
category_id         BIGINT FK → categories.id
subcategory_id      BIGINT FK → categories.id NULLABLE
title_ar            VARCHAR(255)
title_en            VARCHAR(255)
description_ar      TEXT
description_en      TEXT
available_quantity  INT DEFAULT 1
main_image          VARCHAR(255)
type                ENUM('sale','rent')
price               DECIMAL(12,2)
currency            ENUM('USD','SYP')
latitude            DECIMAL(10,7) NULLABLE
longitude           DECIMAL(10,7) NULLABLE
status              ENUM('pending','approved','rejected') DEFAULT 'pending'
rejection_reason    TEXT NULLABLE
created_at / updated_at
```

---

**`service_images`**
```
id                  BIGINT PK
service_id          BIGINT FK → services.id
image_path          VARCHAR(255)
created_at / updated_at
```

---

**`service_dynamic_values`**
```
id                  BIGINT PK
service_id          BIGINT FK → services.id
dynamic_field_id    BIGINT FK → dynamic_fields.id
value               TEXT
created_at / updated_at
UNIQUE(service_id, dynamic_field_id)
```

---

**`orders`**
```
id                  BIGINT PK
service_id          BIGINT FK → services.id
requester_business_id  BIGINT FK → business_accounts.id
needed_at           DATE
quantity            INT DEFAULT 1
details             TEXT NULLABLE
status              ENUM('pending','accepted','rejected') DEFAULT 'pending'
created_at / updated_at
```

---

**`ratings`**
```
id                  BIGINT PK
order_id            BIGINT FK → orders.id UNIQUE    ← one rating per order
service_id          BIGINT FK → services.id
user_id             BIGINT FK → users.id
rating              TINYINT (1–5)
comment             TEXT NULLABLE
created_at / updated_at
```

---

**`favorites`**
```
id                  BIGINT PK
user_id             BIGINT FK → users.id
service_id          BIGINT FK → services.id
created_at
UNIQUE(user_id, service_id)
```

---

**`reports`**
```
id                  BIGINT PK
user_id             BIGINT FK → users.id
service_id          BIGINT FK → services.id
reason              TEXT
is_resolved         BOOLEAN DEFAULT false
created_at / updated_at
```

---

**`conversations`**
```
id                  BIGINT PK
service_id          BIGINT FK → services.id
initiator_id        BIGINT FK → users.id           ← who started the chat
receiver_id         BIGINT FK → users.id            ← service owner
created_at / updated_at
UNIQUE(service_id, initiator_id, receiver_id)
```

---

**`messages`**
```
id                  BIGINT PK
conversation_id     BIGINT FK → conversations.id
sender_id           BIGINT FK → users.id
content             TEXT
status              ENUM('sent','read') DEFAULT 'sent'
created_at / updated_at
```

---

**`notifications`**
```
id                  BIGINT PK
user_id             BIGINT FK → users.id
title               VARCHAR(255)
body                TEXT
data                JSON NULLABLE                  ← e.g. {type:"order", id:5}
read_at             TIMESTAMP NULLABLE
created_at / updated_at
```

---

### Entity Relationships Summary

```
users ──────────────< business_accounts
                              │
                              ├──────────< services
                              │                │
                              │                ├──< service_images
                              │                ├──< service_dynamic_values >── dynamic_fields
                              │                ├──< orders
                              │                │       │
                              │                │       └──< ratings
                              │                ├──< favorites >── users
                              │                └──< reports >── users
                              │
                    < orders (as requester_business_id)

categories ─────────< subcategories (self-referential parent_id)
categories ─────────< dynamic_fields
categories ─────────< services

users ──────────────< conversations (as initiator)
users ──────────────< conversations (as receiver)
conversations ──────< messages

users ──────────────< notifications

admins >────────── roles >────< permissions
```

---

### Key Constraints & Indexes

```sql
-- Business rule: only approved business accounts can create services
-- Enforced at application layer (Service layer validation)

-- Prevent duplicate rating per order
UNIQUE INDEX on ratings(order_id)

-- Prevent duplicate favorites
UNIQUE INDEX on favorites(user_id, service_id)

-- Prevent duplicate conversations for same service between same 2 users
UNIQUE INDEX on conversations(service_id, initiator_id, receiver_id)

-- Performance indexes
INDEX on services(status)
INDEX on services(category_id)
INDEX on services(business_account_id)
INDEX on orders(status)
INDEX on business_accounts(user_id)
INDEX on business_accounts(status)
INDEX on messages(conversation_id)
```

---

## 10. Environment Configuration (`.env` keys required)

```env
APP_NAME=Servixa
APP_URL=http://localhost:8000
APP_LOCALE=ar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=servixa
DB_USERNAME=root
DB_PASSWORD=

# UltraMsg WhatsApp OTP
ULTRAMSG_INSTANCE_ID=your_instance_id
ULTRAMSG_TOKEN=your_token

# Firebase Push Notifications
FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json

# Pusher Real-time Chat
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

BROADCAST_DRIVER=pusher
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

---

*End of SRS — Servixa v1.0*
