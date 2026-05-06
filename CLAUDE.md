# CLAUDE.md — Servixa Project Guide

> This file is the single source of truth for Claude Code when working in this repository.
> **Rule:** Any time a new library, decision, or business rule is confirmed, update this file immediately.
> **Last synced with codebase:** 2026-05-05 (HEAD: `d06d0ab` — Phase 4 with notifications complete)

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

## Current Status (Phase Progress)

| Phase | Scope | Status |
|---|---|---|
| **1 — Foundation** | All migrations, Passport auth (API + admin), bilingual setup | ✅ Complete |
| **2 — Roles + Business Accounts** | Spatie permissions, cities, activity types, business account workflow | ✅ Complete |
| **3 — Services** | Categories, dynamic fields, services CRUD + admin approval | ✅ Complete |
| **4 — Orders + Ratings + Notifications** | Orders lifecycle, rating gate, Firebase FCM push, Event/Listener system | ✅ Complete |
| **5 — Chat** | API-only chat (no Pusher), sent/read status, FCM push on new message | ✅ Complete (API-only — Pusher deferred) |
| **6 — Polish** | Favorites API, Reports API, advanced filters, bilingual + permission audit | ❌ Not started (models exist, no controllers/routes) |

### Outstanding gaps in built features
- **Favorites** — model + migration exist, but no API controller, service, or routes
- **Reports** — model + migration exist, but no API controller, service, or routes (admin route is a placeholder redirect)
- **Pusher** — `pusher/pusher-php-server` is **not installed** by design (chat ships API-only). Adding it later only requires making `MessageSent` implement `ShouldBroadcast` + configuring `config/broadcasting.php` — no controller/service changes

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

# Performance — caches MUST be warm in production-like usage.
# Without these, every request reparses config/routes and recompiles Blade.
php artisan optimize          # caches config + routes + events
php artisan view:cache        # precompiles ALL Blade templates
php artisan optimize:clear    # clear all caches (do this BEFORE editing config/routes/.env)
```

**IMPORTANT:** After ANY change to `.env`, `config/*`, `routes/*`, or `bootstrap/app.php`, run `php artisan optimize:clear` first or the change will not be picked up. Re-run `php artisan optimize && php artisan view:cache` afterwards. Blade view edits are auto-detected — no clear needed.

---

## Installed Packages (composer.json)

| Package | Version | Purpose |
|---|---|---|
| `laravel/framework` | ^12.0 | Core framework |
| `laravel/passport` | ^13.7 | OAuth2 API auth |
| `spatie/laravel-permission` | ^6.25 | Roles + permissions for admin |
| `spatie/laravel-translatable` | ^6.0 | Bilingual JSON columns |
| `spatie/laravel-medialibrary` | ^11.21 | Image/file uploads (used in place of intervention/image) |
| `kreait/laravel-firebase` | ^6.2 | Firebase FCM push notifications |

**Not installed (required for Phase 5 chat):**
- `pusher/pusher-php-server` — install with `composer require pusher/pusher-php-server` when starting chat work

---

## Architecture

### Pattern: MVC + Service Layer

```
Request
  → Middleware (auth, permission, locale, tab id)
  → Controller (validate input, call service, return response)
  → Service Class (all business logic lives here)
  → Model / Eloquent (database)
  → Event → Listener → Notification (for side effects like FCM push)
  → API Resource / Blade View (output)
```

- **Controllers are thin** — no business logic, only validate + delegate
- **Services own business logic** — see "Services (built)" below
- **Events + Listeners** drive notifications (no inline `Notification::send()` calls inside services)
- **API Resources** transform Eloquent models → JSON for mobile app
- **Blade views** are admin dashboard ONLY — never for API responses

### Directory Layout (actual)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/
│   │   │   ├── Auth/AuthController.php
│   │   │   ├── ActivityTypeController.php
│   │   │   ├── BusinessAccountController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CityController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── OrderController.php
│   │   │   ├── RatingController.php
│   │   │   └── ServiceController.php
│   │   └── Admin/
│   │       ├── AuthController.php
│   │       ├── ActivityTypeController.php
│   │       ├── AdminDeviceTokenController.php
│   │       ├── AdminUserController.php
│   │       ├── BusinessAccountController.php
│   │       ├── CategoryController.php
│   │       ├── CityController.php
│   │       ├── DashboardController.php
│   │       ├── DynamicFieldController.php
│   │       ├── NotificationController.php
│   │       ├── RoleController.php
│   │       ├── ServiceController.php
│   │       └── SliderController.php
│   ├── Middleware/
│   │   ├── SetLocale.php           ← sets app locale from Accept-Language
│   │   ├── CheckPermission.php     ← Spatie permission gate
│   │   └── InjectTabId.php         ← appends `_tab` query param for multi-tab admin state
│   ├── Requests/                   ← FormRequest validation (organized API/* + Admin/*)
│   └── Resources/                  ← API JSON transformers
├── Models/                         ← 17 Eloquent models (see list below)
├── Services/                       ← 12 services (see list below)
├── Events/                         ← 13 domain events
├── Listeners/                      ← 13 listeners (one per event, all dispatch notifications)
├── Notifications/
│   ├── BaseNotification.php        ← shared FCM payload logic
│   ├── Channels/FcmChannel.php     ← custom Firebase FCM channel
│   └── *Notification.php           ← 13 concrete notification classes
└── View/Components/                ← 12 Blade component classes

resources/views/                    ← Admin dashboard Blade templates only
routes/
├── api.php                         ← /api/v1/* routes
└── web.php                         ← /admin/* routes
```

### Models (built — 17)

`ActivityType` · `Admin` · `AdminDeviceToken` · `BusinessAccount` · `Category` · `City` · `Conversation` · `DynamicField` · `Favorite` · `Message` · `Order` · `Rating` · `Report` · `Service` · `ServiceDynamicValue` · `Slider` · `User`

> **Note:** `service_images` migration exists but no `ServiceImage` model — `Service` uses Spatie MediaLibrary (`HasMedia`) for images instead. There is also no separate `Notification` Eloquent model; notifications are delivered via the FCM channel (not stored in the DB as Eloquent models — though the polymorphic `notifications` table exists for future Laravel notification storage).

### Services (built — 12)

`ActivityTypeService` · `AdminAuthService` · `AuthService` · `BusinessAccountService` · `CategoryService` · `ChatService` · `CityService` · `NotificationService` · `OrderService` · `OtpService` · `RatingService` · `ServiceService` · `SliderService`

**Pending services** (when those features are built): `FavoriteService`, `ReportService`

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

### Guard 2: Admin Dashboard → Custom `tab-session` Driver

```php
// config/auth.php
'guards' => [
    'admin' => ['driver' => 'tab-session', 'provider' => 'admins'],
]
'providers' => [
    'admins' => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
]
```

- Admin logs in with email + password — NO OTP
- Uses custom `tab-session` driver (extends standard session driver) to support **multiple admin tabs** with independent state, paired with `InjectTabId` middleware
- Separate `admins` table (Spatie `HasRoles` trait)
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

## Notifications — Firebase FCM via Events/Listeners

Notifications are NOT triggered inline from services. They flow through the event system:

```
Service action (e.g. OrderService::accept)
  → event(new OrderAccepted($order))
  → Listener (e.g. SendOrderAcceptedNotification)
  → Notification class (extends BaseNotification)
  → FcmChannel (custom channel) → Firebase FCM → user device
```

### Components
- **13 events** in `app/Events/` — one per domain action (BusinessAccount approved/rejected/submitted, Service approved/rejected/submitted/resubmitted/reported, Order received/accepted/rejected, RatingAdded, MessageSent)
- **13 listeners** in `app/Listeners/` — each subscribes to one event and dispatches one notification
- **13 notification classes** in `app/Notifications/` — all extend `BaseNotification` for shared FCM payload logic
- **`FcmChannel`** in `app/Notifications/Channels/FcmChannel.php` — custom channel that calls Firebase via `kreait/laravel-firebase`
- **Device tokens** — admins register their browser FCM tokens via `AdminDeviceTokenController`; stored in `admin_device_tokens` table. User device tokens stored on the `users` table directly.
- **Web push for admin dashboard** — `public/firebase-messaging-sw.js` is the service worker that receives FCM messages in the browser

### `.env` keys
- `FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json` (path to service account JSON)
- `config/services.php` → `fcm.project_id` = `servixa-1d1a5`

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
- Services are injected via constructor dependency injection
- Side effects (notifications, audit logs) fire via events from inside services — never inline calls
- Example controller method — exactly this pattern:
```php
public function store(StoreServiceRequest $request, ServiceService $serviceService)
{
    $service = $serviceService->create($request->validated());
    return $this->success($service, 'Service submitted for review', 201);
}
```

### 4. Global Exception Handler (Laravel 12 — `bootstrap/app.php`)
- Configured in `bootstrap/app.php` via `withExceptions(...)` — Laravel 12 has no `app/Exceptions/Handler.php`
- Returns consistent JSON format for API: `{ success: false, message: "...", errors: {} }`
- Handles:
  - `ValidationException` → 422 with field errors
  - `AuthenticationException` → 401
  - `AuthorizationException` → 403
  - `ModelNotFoundException` → 404 with descriptive message
  - `HttpException` → appropriate HTTP status
  - Any other `Throwable` → 500 (with stack trace in debug mode only)
- Detection: `$request->is('api/*') || $request->expectsJson()` → JSON response
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
10. Service images are managed via **Spatie MediaLibrary** (not direct storage); business account documents likewise

---

## Database

- **MySQL 8.x** via XAMPP — database name: `servixa`
- Migrations live in `database/migrations/` (39 files)
- Custom domain tables created together on **2026-03-30** (`2026_03_30_000001` … `2026_03_30_000019`)

### Table inventory (built)
Permission tables (Spatie) · `admins` · `cities` · `activity_types` · `business_accounts` · `business_account_files` · `categories` · `dynamic_fields` · `sliders` · `services` · `service_images` · `service_dynamic_values` · `orders` · `ratings` · `favorites` · `reports` · `conversations` · `messages` · `notifications` (polymorphic) · `media` (Spatie MediaLibrary) · `admin_device_tokens`

### Key Design Notes

| Table | Special Note |
|---|---|
| `categories` | Self-referential — `parent_id = NULL` means main category, otherwise subcategory |
| `dynamic_fields` | Extra form fields defined per category by admin |
| `service_dynamic_values` | Stores user's answers to dynamic fields per service |
| `services` | `price`/`currency` replaced with **dual-price** columns (see migration `2026_04_07_..._replace_price_currency_with_dual_price`) |
| `services` | Cascade delete to children added in `2026_04_27_..._add_cascade_delete_to_service_relations` |
| `ratings` | `UNIQUE(order_id)` — one rating per accepted order |
| `favorites` | `UNIQUE(user_id, service_id)` |
| `conversations` | `UNIQUE(service_id, initiator_id, receiver_id)` |
| `business_accounts` | `status` ENUM: `pending`, `approved`, `rejected` |
| `services` | `status` ENUM: `pending`, `approved`, `rejected` |
| `orders` | `status` ENUM: `pending`, `accepted`, `rejected` |
| `users` / `admins` | `locale` column added (`2026_05_01_..._add_locale_to_users_and_admins_table`) |
| `notifications` | Recreated as polymorphic in `2026_05_01_..._recreate_notifications_table_polymorphic` |
| `media` | Spatie MediaLibrary — replaces direct file paths on services |
| `admin_device_tokens` | FCM tokens for admin browser push |

- Bilingual columns use **JSON** type via `spatie/laravel-translatable` (e.g. `name JSON`, `title JSON`, `description JSON`) — NOT `_ar`/`_en` suffixes
- Files (images, documents) stored via **Spatie MediaLibrary** in the `media` table → exposed via `php artisan storage:link`
- OTPs stored in **cache only**, NOT in database

### Seeders (built — 6)
`DatabaseSeeder` · `RolesAndPermissionsSeeder` · `AdminSeeder` · `CitySeeder` · `CategorySeeder` · `ActivityTypeSeeder`

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

### Routes — Built

```
# Public
POST   /api/v1/auth/register
POST   /api/v1/auth/verify-otp
POST   /api/v1/auth/login
POST   /api/v1/auth/login/verify
POST   /api/v1/auth/refresh
GET    /api/v1/cities
GET    /api/v1/activity-types
GET    /api/v1/categories
GET    /api/v1/services                 (approved only)
GET    /api/v1/services/{id}

# Protected (auth:api)
POST   /api/v1/auth/logout
GET    /api/v1/profile
PUT    /api/v1/profile
GET|POST|PUT /api/v1/business-accounts
GET    /api/v1/my-services
POST|PUT|DELETE /api/v1/services
GET|POST|PATCH|DELETE /api/v1/orders
POST   /api/v1/ratings
GET    /api/v1/notifications
PATCH  /api/v1/notifications/{id}/read
POST   /api/v1/notifications/read-all
GET    /api/v1/notifications/unread-count
```

### Routes — Phase 5 Chat (built, API-only)
```
GET    /api/v1/conversations                  list my conversations
POST   /api/v1/conversations                  start (body: {service_id})
GET    /api/v1/conversations/{id}             show one conversation
POST   /api/v1/conversations/{id}/read        mark all incoming messages as read
GET    /api/v1/conversations/{id}/messages    paginated message history
POST   /api/v1/conversations/{id}/messages    send (body: {content})
```

### Routes — NOT yet built
```
GET|POST|DELETE /api/v1/favorites             ← Phase 6
POST   /api/v1/reports                        ← Phase 6
```

---

## Admin Dashboard Routes (built)

```
/admin/login                              (GET + POST)  — public
/admin/logout                             (POST)        — auth:admin
/admin/dashboard                          [auth:admin]
/admin/notifications/*                    [auth:admin]
/admin/device-tokens (store, destroy)     [auth:admin]  — FCM token registration

/admin/business-accounts/*                [auth:admin + view-/manage-business-accounts]
/admin/categories/*                       [auth:admin + view-/manage-categories]
/admin/categories/{id}/dynamic-fields/*   [auth:admin + view-/manage-categories]
/admin/cities/*                           [auth:admin + view-/manage-cities]
/admin/activity-types/*                   [auth:admin + view-/manage-cities]
/admin/services/*                         [auth:admin + view-/manage-services]
/admin/sliders/*                          [auth:admin + manage-sliders]
/admin/roles/*                            [auth:admin + manage-roles]      ← Super Admin
/admin/admins/*                           [auth:admin + manage-admins]     ← Super Admin

# Placeholder (redirects to dashboard — pending Phase 6)
/admin/reports                            [auth:admin]
```

---

## External Services

| Service | Provider | Purpose | Config Keys |
|---|---|---|---|
| WhatsApp OTP | UltraMsg | Send OTP via WhatsApp | `ULTRAMSG_INSTANCE_ID`, `ULTRAMSG_TOKEN` |
| Push Notifications | Firebase FCM | Push to mobile + admin browser (via `kreait/laravel-firebase` + custom `FcmChannel`) | `FIREBASE_CREDENTIALS` (path to JSON file) |
| Real-time Chat | Pusher | **Private channel** chat (1-to-1) — **NOT YET INSTALLED** | `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER` |
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
- Per-user persisted locale also stored in `users.locale` and `admins.locale`
- Lang files in `lang/ar/` and `lang/en/`

### Translatable models (built)
`ActivityType` · `BusinessAccount` · `Category` · `City` · `DynamicField` · `Service`

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

### PHP Classes (`app/View/Components/`) — 12 built
```
Alert.php · Avatar.php · Badge.php · Button.php · Card.php · DataTable.php
EmptyState.php · Input.php · Modal.php · Select.php · StatCard.php · Textarea.php
```

### Component Views (`resources/views/components/`) — 12 built
```
alert.blade.php · avatar.blade.php · badge.blade.php · button.blade.php
card.blade.php · data-table.blade.php · empty-state.blade.php · input.blade.php
modal.blade.php · select.blade.php · stat-card.blade.php · textarea.blade.php
```

### Layouts & Partials
```
layouts/app.blade.php          ← master: sidebar + topbar + main content
partials/sidebar.blade.php     ← purple sidebar with nav + active state
partials/header.blade.php      ← topbar: breadcrumb + admin name + notifications + logout
partials/footer.blade.php
```

### Admin Pages — Built

```
auth/login.blade.php
dashboard/index.blade.php
business-accounts/{index,show}.blade.php
services/{index,show}.blade.php
categories/{index,create,edit}.blade.php
categories/dynamic-fields/{index,create,edit}.blade.php       ← (under DynamicFieldController)
cities/{index,create,edit}.blade.php
activity-types/{index,create,edit}.blade.php
sliders/{index,create,edit}.blade.php
roles/{index,create,edit}.blade.php
admins/{index,create,edit}.blade.php
notifications/index.blade.php
```

### Admin Pages — NOT yet built
```
reports/{index,show}.blade.php   ← Phase 6
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

| Code | Requirement | Actor | Status |
|---|---|---|---|
| FR-01 | Register user account | User | ✅ |
| FR-02 | Login | User | ✅ |
| FR-03 | Logout | User | ✅ |
| FR-04 | Edit profile | User | ✅ |
| FR-05 | Create business account | User | ✅ |
| FR-06 | Edit business account | User | ✅ |
| FR-07 | View my business accounts | User | ✅ |
| FR-08 | Approve business account | Admin | ✅ |
| FR-09 | Reject business account | Admin | ✅ |
| FR-10 | Add service | User | ✅ |
| FR-11 | Edit service | User | ✅ |
| FR-12 | Delete service | User | ✅ |
| FR-13 | View services | User | ✅ |
| FR-14 | Approve service | Admin | ✅ |
| FR-15 | Reject service | Admin | ✅ |
| FR-16 | Add category | Admin | ✅ |
| FR-17 | Edit category | Admin | ✅ |
| FR-18 | Delete category | Admin | ✅ |
| FR-19 | Add subcategory | Admin | ✅ |
| FR-20 | Edit subcategory | Admin | ✅ |
| FR-21 | Delete subcategory | Admin | ✅ |
| FR-22 | Add dynamic fields | Admin | ✅ |
| FR-23 | Edit dynamic fields | Admin | ✅ |
| FR-24 | Delete dynamic fields | Admin | ✅ |
| FR-25 | Request a service | User | ✅ |
| FR-26 | View received orders | User | ✅ |
| FR-27 | View sent orders | User | ✅ |
| FR-28 | Accept order | User | ✅ |
| FR-29 | Reject order | User | ✅ |
| FR-30 | Delete order | User | ✅ |
| FR-31 | Add rating | User | ✅ |
| FR-32 | Add to favorites | User | ❌ Phase 6 |
| FR-33 | Remove from favorites | User | ❌ Phase 6 |
| FR-34 | Report a service | User | ❌ Phase 6 |
| FR-35 | Manage ad sliders | Admin | ✅ |
| FR-36 | Add city | Admin | ✅ |
| FR-37 | Manage reports | Admin | ❌ Phase 6 |
| FR-38 | Add role | Super Admin | ✅ |
| FR-39 | Edit role | Super Admin | ✅ |
| FR-40 | Delete role | Super Admin | ✅ |
| FR-41 | Assign permissions to role | Super Admin | ✅ |
| FR-42 | Create admin account | Super Admin | ✅ |
| FR-43 | Edit admin permissions | Super Admin | ✅ |

**Plus chat (out of FR list, but in scope):** Phase 5 — ❌ Not started.

---

## Build Phases (from Time Planning document)

| Phase | Week | Deliverables | Status |
|---|---|---|---|
| 1 — Foundation | Week 1 | All migrations, Passport auth (API + admin), bilingual setup | ✅ |
| 2 — Roles + Business Accounts | Week 2 | Spatie permissions, cities, activity types, business account workflow | ✅ |
| 3 — Services | Week 3 | Categories, subcategories, dynamic fields, services CRUD + admin approval | ✅ |
| 4 — Orders + Ratings + Notifications | Week 4 | Orders lifecycle, rating gate, Firebase push (events/listeners/FCM channel) | ✅ |
| 5 — Chat | 3 days | API-only chat (send/receive text, sent/read, conversation linked to service), FCM push on new message. Pusher deferred. | ✅ |
| 6 — Polish | Week 6 | Favorites, reports, advanced filters, bilingual audit, permission audit | ❌ |

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
FIREBASE_PROJECT_ID=servixa-1d1a5

# Pusher — required for Phase 5 (not yet wired)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

BROADCAST_DRIVER=pusher
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```
