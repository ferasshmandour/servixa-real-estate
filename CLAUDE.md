# CLAUDE.md — Servixa Project Guide

> This file is the single source of truth for Claude Code when working in this repository.
> **Rule:** Any time a new library, decision, or business rule is confirmed, update this file immediately.

---

## Project Summary

**Servixa** — "All Services in One Place"
A regulated digital marketplace for real estate and construction-related services, targeting the Syrian market with full Arabic/English bilingual support.

### What is being built here
| Layer | Technology | Purpose |
|---|---|---|
| REST API | Laravel 12 + Passport | Consumed by mobile app (iOS/Android — built separately) |
| Admin Dashboard | Laravel 12 + Blade | Web panel for admins to manage the system |
| Shared Backend | MySQL + Laravel | Database, business logic, auth, file storage |

### What is NOT built here
- The mobile app (Flutter/React Native — separate project)
- Any public-facing frontend for end users

---

## Commands

```bash
# Full dev environment (server + queue + Vite concurrently)
composer dev

# Run all tests
composer test

# Initial setup (install deps, key, migrate, build)
composer setup

# Frontend assets
npm run dev        # Vite dev server (watch)
npm run build      # Production build

# Artisan
php artisan serve
php artisan migrate
php artisan migrate:fresh --seed
php artisan queue:listen --tries=1
php artisan storage:link          # expose storage/app/public via public/storage
php artisan passport:install      # run ONCE after first migrate

# Single test
php artisan test --filter TestClassName
```

---

## Package Installation (run these in order the first time)

```bash
composer require laravel/passport
composer require spatie/laravel-permission
composer require spatie/laravel-translatable
composer require intervention/image
composer require kreait/laravel-firebase
composer require pusher/pusher-php-server
```

---

## Architecture

### Pattern: MVC + Service Layer

```
Request
  → Middleware (auth, permission, locale)
  → Controller (validate input, call service, return response)
  → Service Class (all business logic lives here)
  → Model / Eloquent (database)
  → API Resource / Blade View (output)
```

- **Controllers are thin** — no business logic, only validate + delegate
- **Services own business logic** — `OtpService`, `NotificationService`, `ChatService`, `BusinessAccountService`
- **API Resources** transform Eloquent models → JSON for mobile app
- **Blade views** are admin dashboard ONLY — never for API responses

### Directory Layout

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/                  ← All /api/v1/* REST controllers
│   │   │   ├── Auth/AuthController.php
│   │   │   ├── BusinessAccountController.php
│   │   │   ├── ServiceController.php
│   │   │   ├── OrderController.php
│   │   │   ├── RatingController.php
│   │   │   ├── FavoriteController.php
│   │   │   ├── ReportController.php
│   │   │   ├── ChatController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ProfileController.php
│   │   └── Admin/                ← All /admin/* dashboard controllers
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
│   │   ├── SetLocale.php         ← sets app locale from request header
│   │   └── CheckPermission.php
│   └── Requests/                 ← Form Request validation classes
├── Models/                       ← All 18 Eloquent models
├── Services/
│   ├── OtpService.php            ← UltraMsg WhatsApp OTP logic
│   ├── NotificationService.php   ← Firebase push notifications
│   └── ChatService.php           ← Pusher private channel chat (1-to-1)
└── Http/Resources/               ← API JSON transformers

resources/views/admin/            ← ALL Blade templates (admin only)
routes/
├── api.php                       ← /api/v1/* routes
└── web.php                       ← /admin/* routes
```

---

## Authentication — Two Separate Guards

### Guard 1: Mobile API Users → Laravel Passport (OAuth2)

```php
// config/auth.php
'guards' => [
    'api' => ['driver' => 'passport', 'provider' => 'users'],
]
```

- User registers → OTP sent via WhatsApp (UltraMsg) → verified → Passport issues `access_token` + `refresh_token`
- Login → phone + password → OTP sent again → verified → new tokens issued
- Mobile app sends: `Authorization: Bearer {access_token}` on every request
- Middleware on protected API routes: `auth:api`
- User model uses: `use Laravel\Passport\HasApiTokens;`
- Logout: `$request->user()->token()->revoke()`
- Token response shape: `{ access_token, refresh_token, token_type: "Bearer", expires_in, user }`

### Guard 2: Admin Dashboard → Laravel Session Auth

```php
// config/auth.php
'guards' => [
    'admin' => ['driver' => 'session', 'provider' => 'admins'],
]
'providers' => [
    'admins' => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
]
```

- Admin logs in with email + password — NO OTP
- Session-based, uses separate `admins` table
- Middleware: `auth:admin`

---

## OTP — WhatsApp via UltraMsg

- **No extra package** — uses Laravel's built-in `Http::post()`
- OTP stored in Laravel **cache** (NOT database): key = `"otp_{phone}"`, TTL = 10 minutes
- Send OTP:

```php
Http::post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
    'token' => config('services.ultramsg.token'),
    'to'    => $phone,   // international format: +963999000111
    'body'  => "Your Servixa OTP is: {$otp}",
]);
```

- `.env` keys: `ULTRAMSG_INSTANCE_ID`, `ULTRAMSG_TOKEN`

---

## State Machines — Enforced in Service Layer

### Business Account
```
[pending] → admin approves → [approved]  ← can post/request services
[pending] → admin rejects  → [rejected]
```

### Service (Ad)
```
[pending] → admin approves → [approved]  ← visible on mobile app
[pending] → admin rejects  → [rejected]
[approved] → user edits    → [pending]   ← AUTO reset on any edit
```

### Order
```
[pending] → provider accepts → [accepted]  ← requester can now leave a rating
[pending] → provider rejects → [rejected]
```

### Rating Gate
```
Rating allowed ONLY IF:
  - order.status = 'accepted'
  - no existing rating for that order_id (UNIQUE constraint on ratings.order_id)
```

---

## Engineering Standards (Final — Non-Negotiable)

### 1. Frontend Quality
- Design must be **creative, professional, and polished** — like a senior frontend engineer built it
- Every page must pixel-match the Figma design system (colors, radius, shadows, spacing)
- Responsive, clean typography, smooth hover/focus states on every interactive element
- No plain HTML — every element uses the component system defined in BLADE_STRUCTURE.md
- Sidebar active states, empty states, loading states — all handled

### 2. Form Request Validation
- **Every** controller method that accepts input MUST use a dedicated `FormRequest` class
- No inline `$request->validate()` inside controllers — ever
- All FormRequests live in `app/Http/Requests/`
- Organized by feature: `app/Http/Requests/API/Auth/RegisterRequest.php`, `app/Http/Requests/Admin/Category/StoreCategoryRequest.php`, etc.
- Authorization method in FormRequest must also enforce auth/permission checks

### 3. Service Layer Architecture
- **Zero business logic in controllers**
- Controllers only: validate (via FormRequest) → call service method → return response
- All business logic lives in `app/Services/`
- **Every feature MUST have a corresponding service class** — no exceptions, including authentication
- Each feature has its own service: `AuthService`, `BusinessAccountService`, `ServiceService`, `OrderService`, `RatingService`, `ChatService`, `OtpService`, `NotificationService`
- Services are injected via constructor dependency injection
- Example controller method — exactly this pattern:
```php
public function store(StoreServiceRequest $request, ServiceService $serviceService)
{
    $service = $serviceService->create($request->validated());
    return $this->success($service, 'Service submitted for review', 201);
}
```

### 4. Global Exception Handler
- A single `app/Exceptions/Handler.php` handles ALL exceptions for API endpoints
- Returns consistent JSON format: `{ success: false, message: "...", errors: {} }`
- Handles:
  - `ValidationException` → 422 with field errors
  - `AuthenticationException` → 401
  - `AuthorizationException` → 403
  - `ModelNotFoundException` → 404 with descriptive message
  - `HttpException` → appropriate HTTP status
  - Any other `Throwable` → 500 (with stack trace in debug mode only)
- API routes (`/api/*`) always get JSON responses — never HTML error pages
- Admin dashboard routes get Blade error pages (403, 404, 500 views)

---

## Key Business Rules

1. User cannot post OR request a service without an **approved** business account
2. One user can own **multiple** business accounts
3. When posting or requesting, user selects **which business account** to act as — like choosing a card in a wallet
4. The same business account can be **both provider and requester** simultaneously — no role distinction
5. Editing an approved service **automatically resets** it to `pending` for re-review
6. Rating requires an accepted order — enforced at service layer + `UNIQUE(order_id)` in `ratings` table
7. `requested_quantity` must NOT exceed `services.available_quantity` — validated on order creation
8. `available_quantity` defaults to `1` for services that don't need quantity tracking
9. All admin actions require a matching **Spatie permission** — no action executes without it

---

## Database

- **MySQL 8.x** via XAMPP — database name: `servixa`
- **18 tables** total (see SRS.md for full ERD)

### Key Design Notes

| Table | Special Note |
|---|---|
| `categories` | Self-referential — `parent_id = NULL` means main category, otherwise subcategory |
| `dynamic_fields` | Extra form fields defined per category by admin |
| `service_dynamic_values` | Stores user's answers to dynamic fields per service |
| `ratings` | `UNIQUE(order_id)` — one rating per accepted order |
| `favorites` | `UNIQUE(user_id, service_id)` |
| `conversations` | `UNIQUE(service_id, initiator_id, receiver_id)` |
| `business_accounts` | `status` ENUM: `pending`, `approved`, `rejected` |
| `services` | `status` ENUM: `pending`, `approved`, `rejected` |
| `orders` | `status` ENUM: `pending`, `accepted`, `rejected` |

- Bilingual columns use **JSON** type via `spatie/laravel-translatable` (e.g. `name JSON`, `title JSON`, `description JSON`) — NOT `_ar`/`_en` suffixes
- Images stored in `storage/app/public` → exposed via `php artisan storage:link`
- OTPs stored in **cache only**, NOT in database

---

## API Design

### Base URL
```
/api/v1/
```

### Response Envelope (ALL responses)
```json
{ "success": true, "message": "...", "data": {} }
```

### Error Response
```json
{ "success": false, "message": "Validation failed", "errors": { "field": ["message"] } }
```

### Auth Header (all protected routes)
```
Authorization: Bearer {access_token}
```

### Key Route Groups
```
POST   /api/v1/auth/register
POST   /api/v1/auth/verify-otp
POST   /api/v1/auth/login
POST   /api/v1/auth/login/verify
POST   /api/v1/auth/refresh
POST   /api/v1/auth/logout              [auth:api]
GET    /api/v1/profile                  [auth:api]
PUT    /api/v1/profile                  [auth:api]
GET|POST|PUT /api/v1/business-accounts  [auth:api]
GET|POST|PUT|DELETE /api/v1/services    [auth:api for write]
GET|POST|PATCH|DELETE /api/v1/orders    [auth:api]
POST   /api/v1/ratings                  [auth:api]
GET|POST|DELETE /api/v1/favorites       [auth:api]
POST   /api/v1/reports                  [auth:api]
GET|POST /api/v1/conversations          [auth:api]
GET|POST /api/v1/conversations/{id}/messages [auth:api]
GET    /api/v1/categories               [public]
GET    /api/v1/cities                   [public]
GET    /api/v1/services                 [public]
GET    /api/v1/services/{id}            [public]
```

---

## Admin Dashboard Routes

```
/admin/login                   (GET + POST)
/admin/dashboard               [auth:admin]
/admin/business-accounts/*     [auth:admin + permission]
/admin/services/*              [auth:admin + permission]
/admin/categories/*            [auth:admin + permission]
/admin/cities/*                [auth:admin + permission]
/admin/sliders/*               [auth:admin + permission]
/admin/reports/*               [auth:admin + permission]
/admin/roles/*                 [auth:admin + super-admin only]
/admin/admins/*                [auth:admin + super-admin only]
```

---

## External Services

| Service | Provider | Purpose | Config Keys |
|---|---|---|---|
| WhatsApp OTP | UltraMsg | Send OTP via WhatsApp | `ULTRAMSG_INSTANCE_ID`, `ULTRAMSG_TOKEN` |
| Push Notifications | Firebase FCM | Push alerts to mobile devices | `FIREBASE_CREDENTIALS` (path to JSON file) |
| Real-time Chat | Pusher | **Private channel** chat (1-to-1 only, NOT public channels) | `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER` |
| Maps | Google Maps embed | Location display (no backend key) | None |

---

## Bilingual Support — `spatie/laravel-translatable`

- Default locale: Arabic (`APP_LOCALE=ar`)
- Supported: `ar`, `en`
- **Uses `spatie/laravel-translatable`** — translatable columns stored as JSON, NOT separate `_ar`/`_en` columns
- Models use `HasTranslations` trait, declare `$translatable` array:
```php
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;
    public $translatable = ['name'];
}
```
- Database columns for translatable fields are `JSON` type (e.g. `name JSON` stores `{"ar": "معدات", "en": "Equipment"}`)
- Access: `$category->name` returns the value in current locale; `$category->getTranslation('name', 'en')` for specific locale
- Locale set per-request via `SetLocale` middleware (reads from request header `Accept-Language`)
- Lang files in `lang/ar/` and `lang/en/`

---

## Admin Dashboard UI Stack

| Tool | Purpose |
|---|---|
| Laravel Blade | Server-rendered HTML templates |
| Tailwind CSS | Styling (custom config — see tokens below) |
| Alpine.js | Dropdowns, modals, toggles (no SPA) |
| Heroicons | UI icons |
| Chart.js | Dashboard stats charts |

Traditional form submissions with redirects — no API calls from dashboard.

---

## Design System (from Figma)

| Token | Value | Usage |
|---|---|---|
| Primary | `#6B21A8` | Buttons, prices, active nav, accents |
| Primary hover | `#7C3AED` | Hover/focus states |
| Primary light | `#F5F3FF` | Page bg, category icon bg, table header |
| Primary border | `#DDD6FE` | Input borders, dividers |
| Page background | `#F8F7FF` | Body background |
| Text primary | `#1F2937` | Headings, labels |
| Text secondary | `#6B7280` | Subtitles, placeholders |
| Success | `#16A34A` / `#DCFCE7` | Approved badge |
| Danger | `#DC2626` / `#FEE2E2` | Rejected badge, delete |
| Warning | `#D97706` / `#FEF3C7` | Pending badge |
| Border radius | `12px` | Cards, inputs, buttons |
| Card shadow | `0 2px 8px rgba(107,33,168,0.08)` | All cards |
| Grid margin | `16px` | Page padding |
| Grid gutter | `8px` | Between columns |

---

## Blade Component Structure

### PHP Classes (`app/View/Components/`)
```
Badge.php        ← pending/approved/rejected/etc. colored pill
Button.php       ← primary, secondary, danger, outline variants
Card.php         ← card wrapper with optional title + actions slot
Input.php        ← text input with icon + label support
Select.php       ← dropdown with icon support
Textarea.php     ← multiline input
Modal.php        ← modal dialog wrapper
Alert.php        ← success/error/warning/info alerts
Avatar.php       ← user avatar with initials fallback
StatCard.php     ← dashboard stat (number + label + icon + trend)
DataTable.php    ← table wrapper shell
EmptyState.php   ← empty state with purple folder illustration
```

### Component Views (`resources/views/components/`)
```
badge.blade.php
button.blade.php
card.blade.php
input.blade.php
select.blade.php
textarea.blade.php
modal.blade.php
alert.blade.php
avatar.blade.php
stat-card.blade.php
data-table.blade.php
empty-state.blade.php
```

### Layouts (`resources/views/layouts/`)
```
app.blade.php    ← master: sidebar (fixed left) + topbar + main content
```

### Partials (`resources/views/partials/`)
```
sidebar.blade.php   ← purple sidebar with nav items + active state
header.blade.php    ← topbar: breadcrumb + admin name + logout
footer.blade.php    ← version / copyright
```

### Admin Pages (`resources/views/`)
```
auth/
└── login.blade.php

dashboard/
└── index.blade.php

business-accounts/
├── index.blade.php      ← table + status filter tabs (all/pending/approved/rejected)
├── show.blade.php       ← detail + documents + approve/reject buttons
└── _card.blade.php

services/
├── index.blade.php
├── show.blade.php
└── _card.blade.php

categories/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── dynamic-fields/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php

cities/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

sliders/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

reports/
├── index.blade.php
└── show.blade.php

roles/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

admins/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

### Every Page Uses This Pattern
```blade
@extends('layouts.app')
@section('content')
  {{-- Page header: title + action button --}}
  {{-- Status filter tabs (for approval pages) --}}
  {{-- x-card wrapping table or form --}}
@endsection
```

> Full details: see BLADE_STRUCTURE.md

---

## Full Requirements Reference (43 FR)

| Code | Requirement | Actor |
|---|---|---|
| FR-01 | Register user account | User |
| FR-02 | Login | User |
| FR-03 | Logout | User |
| FR-04 | Edit profile | User |
| FR-05 | Create business account | User |
| FR-06 | Edit business account | User |
| FR-07 | View my business accounts | User |
| FR-08 | Approve business account | Admin |
| FR-09 | Reject business account | Admin |
| FR-10 | Add service | User |
| FR-11 | Edit service | User |
| FR-12 | Delete service | User |
| FR-13 | View services | User |
| FR-14 | Approve service | Admin |
| FR-15 | Reject service | Admin |
| FR-16 | Add category | Admin |
| FR-17 | Edit category | Admin |
| FR-18 | Delete category | Admin |
| FR-19 | Add subcategory | Admin |
| FR-20 | Edit subcategory | Admin |
| FR-21 | Delete subcategory | Admin |
| FR-22 | Add dynamic fields | Admin |
| FR-23 | Edit dynamic fields | Admin |
| FR-24 | Delete dynamic fields | Admin |
| FR-25 | Request a service | User |
| FR-26 | View received orders | User |
| FR-27 | View sent orders | User |
| FR-28 | Accept order | User |
| FR-29 | Reject order | User |
| FR-30 | Delete order | User |
| FR-31 | Add rating | User |
| FR-32 | Add to favorites | User |
| FR-33 | Remove from favorites | User |
| FR-34 | Report a service | User |
| FR-35 | Manage ad sliders | Admin |
| FR-36 | Add city | Admin |
| FR-37 | Manage reports | Admin |
| FR-38 | Add role | Super Admin |
| FR-39 | Edit role | Super Admin |
| FR-40 | Delete role | Super Admin |
| FR-41 | Assign permissions to role | Super Admin |
| FR-42 | Create admin account | Super Admin |
| FR-43 | Edit admin permissions | Super Admin |

---

## Build Phases (from Time Planning document)

| Phase | Week | Deliverables |
|---|---|---|
| 1 — Foundation | Week 1 | All migrations, Passport auth (API + admin), bilingual setup |
| 2 — Roles + Business Accounts | Week 2 | Spatie permissions, cities, activity types, business account workflow |
| 3 — Services | Week 3 | Categories, subcategories, dynamic fields, services CRUD + admin approval |
| 4 — Orders + Ratings + Notifications | Week 4 | Orders lifecycle, rating gate, Firebase push |
| 5 — Chat | 3 days | Pusher real-time messaging, sent/read status |
| 6 — Polish | Week 6 | Favorites, reports, advanced filters, bilingual audit, permission audit |

---

## Evaluation Criteria (from Time Planning document)

The project will be graded on:
1. Correctness of database relationships
2. Proper application of permissions
3. Clear lifecycle for Accounts / Services / Orders
4. System stability — no logical errors
5. Correct bilingual support (AR + EN)

---

## .env Keys Required

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

ULTRAMSG_INSTANCE_ID=your_instance_id
ULTRAMSG_TOKEN=your_token

FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

BROADCAST_DRIVER=pusher
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```
