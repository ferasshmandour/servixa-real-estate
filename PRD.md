# Product Requirements Document (PRD)
## Servixa — All Services in One Place

**Version:** 1.0
**Date:** March 2026
**Status:** Final

---

## 1. Elevator Pitch

Servixa is a regulated digital marketplace for real estate and construction-related services that connects verified businesses with those who need them. Unlike unorganized classifieds, every business account on Servixa is admin-reviewed before activation, every service listing is moderated before going live, and every transaction follows a clear lifecycle — from request to acceptance to rating. Built for the Syrian market with full Arabic/English support, Servixa brings structure, transparency, and trust to an industry that has historically relied on word-of-mouth and informal networks. The platform consists of a mobile app for users, a backend API in Laravel, and a web-based admin dashboard — all working together as one controlled, scalable system.

---

## 2. Who Is This App For

### Primary Users — Mobile App

| User Type | Description |
|---|---|
| **Service Provider** | A business (contractor, supplier, engineer, real estate agency) that lists services on the platform for others to request |
| **Service Requester** | A business that browses listings and sends requests to other businesses for services they need |
| **Both** | The same user/business account can be a provider AND a requester simultaneously — there is no separate role |

### Secondary Users — Admin Dashboard (Web)

| Admin Type | Description |
|---|---|
| **Admin** | Reviews and approves/rejects business accounts and service listings; manages categories, cities, sliders, and reports |
| **Super Admin** | Everything an Admin can do, plus: create and manage other admin accounts, define roles and assign permissions |

### Target Market
- Businesses operating in the construction, equipment, maintenance, plumbing, electrical, HVAC, interior design, and real estate sectors
- Geographic focus: Syria (primary), with support for Syrian Pound (SYP) and USD currency
- Bilingual audience: Arabic (primary) and English

---

## 3. Functional Requirements

### 3.1 Authentication & User Management

| ID | Requirement | Actor |
|---|---|---|
| FR-01 | User registers with: First Name, Last Name, Phone or Email, Country, Password | User |
| FR-02 | After registration, user receives an OTP via WhatsApp (UltraMsg) to verify phone number | System |
| FR-03 | User logs in with phone number + password; receives OTP on WhatsApp to confirm session | User |
| FR-04 | Admin logs in with email + password (no OTP); session-based | Admin |
| FR-05 | User can log out | User |
| FR-06 | User can edit profile (name, photo, password) | User |

### 3.2 Business Account Management

| ID | Requirement | Actor |
|---|---|---|
| FR-07 | User can create one or more business accounts | User |
| FR-08 | Business account creation is a multi-step process: (1) Select business profile type → (2) Enter business details → (3) Enter contact information + map location → (4) Upload supporting documents | User |
| FR-09 | Business profile types (Supplier, Contractor, Engineering, Real Estate, etc.) are defined and managed by admin | Admin |
| FR-10 | Business account details include: license number, name in Arabic, name in English, activities description, details, city, address, GPS coordinates | User |
| FR-11 | Supporting documents include PDFs and images (max 5MB each) | User |
| FR-12 | After submission, business account status is set to **Pending** | System |
| FR-13 | Admin can approve a business account → status becomes **Approved** | Admin |
| FR-14 | Admin can reject a business account → status becomes **Rejected** | Admin |
| FR-15 | Only **Approved** business accounts can post or request services | System |
| FR-16 | User can view and manage all their business accounts | User |
| FR-17 | User can edit an existing business account | User |

### 3.3 Admin Dashboard — Core Management

| ID | Requirement | Actor |
|---|---|---|
| FR-18 | Admin can create, read, update, delete **Categories** (with icon, Arabic name, English name) | Admin |
| FR-19 | Admin can create, read, update, delete **Subcategories** linked to a parent category | Admin |
| FR-20 | Admin can define **Dynamic Fields** per category or subcategory (text, number, select, etc.) | Admin |
| FR-21 | Dynamic fields appear on the service creation form when that category is selected | System |
| FR-22 | Admin can create, read, update, delete **Cities** | Admin |
| FR-23 | Admin can manage **Ad Sliders** (image, link, sort order, active/inactive) | Admin |
| FR-24 | Super Admin can create roles with specific permissions | Super Admin |
| FR-25 | Super Admin can create new admin accounts and assign roles | Super Admin |
| FR-26 | Super Admin can update permissions of existing admins | Super Admin |

### 3.4 Service (Ad) Management

| ID | Requirement | Actor |
|---|---|---|
| FR-27 | User selects which approved business account to post a service under | User |
| FR-28 | Service creation is a multi-step process: (1) Select business account → (2) Select category → (3) Select subcategory (if any) → (4) Enter ad details → (5) Fill dynamic fields → (6) Set map location | User |
| FR-29 | Service details include: title, slug, description, main image, additional images, available quantity (default: 1), price, currency (SYP or USD), service type (sale or rent) | User |
| FR-30 | After submission, service status is **Pending** | System |
| FR-31 | Admin can approve a service → status becomes **Approved** | Admin |
| FR-32 | Admin can reject a service → status becomes **Rejected** | Admin |
| FR-33 | Only **Approved** services are visible in the mobile app | System |
| FR-34 | If a user edits an approved service, it automatically reverts to **Pending** for re-review | System |
| FR-35 | User can delete their own service | User |

### 3.5 Browsing & Discovery

| ID | Requirement | Actor |
|---|---|---|
| FR-36 | Home screen shows: ad sliders, categories grid, top tools & equipment, top construction services, top items | User |
| FR-37 | User can browse all categories and subcategories | User |
| FR-38 | User can view full service detail: images, price, location map, description, dynamic field values, rating, reviews | User |
| FR-39 | User can search services by name/title | User |
| FR-40 | User can filter services by: location, category, subcategory, min price, max price, service type (sale/rent) | User |
| FR-41 | User can toggle between grid and list view on listing pages | User |
| FR-42 | Empty state is shown (with illustration) when no results are found | System |

### 3.6 Requesting a Service

| ID | Requirement | Actor |
|---|---|---|
| FR-43 | To request a service, user must have at least one approved business account | System |
| FR-44 | User selects which business account to use as the requester | User |
| FR-45 | Request form includes: needed date/time, quantity (≤ available quantity), additional details | User |
| FR-46 | Request is sent to the service provider's business account | System |
| FR-47 | Provider sees incoming requests in **Received Orders** | User |
| FR-48 | Requester sees outgoing requests in **My Orders / Sent Orders** | User |
| FR-49 | Provider can accept or reject a received request | User |
| FR-50 | Requester can delete a sent request (if not yet accepted) | User |

### 3.7 Rating & Reviews

| ID | Requirement | Actor |
|---|---|---|
| FR-51 | A user can only rate a service if they sent a request AND it was accepted | System |
| FR-52 | Rating is 1–5 stars with an optional text comment | User |
| FR-53 | Service detail page shows average rating and all reviews | System |
| FR-54 | One rating is allowed per accepted order | System |

### 3.8 Favorites & Reports

| ID | Requirement | Actor |
|---|---|---|
| FR-55 | User can add any approved service to favorites (heart icon) | User |
| FR-56 | User can remove a service from favorites | User |
| FR-57 | User can report a service with a reason | User |
| FR-58 | Admin can view and manage all submitted reports | Admin |

### 3.9 Notifications

| ID | Requirement | Actor |
|---|---|---|
| FR-59 | System stores Firebase device_token per user | System |
| FR-60 | Push notification is sent when: business account is approved/rejected, service is approved/rejected, new order request is received | System |
| FR-61 | In-app notifications list is accessible from the bottom navigation | User |

### 3.10 Real-Time Chat

| ID | Requirement | Actor |
|---|---|---|
| FR-62 | User can start a chat with a service provider directly from the service detail page | User |
| FR-63 | No prior request is required to initiate a chat | System |
| FR-64 | Chat supports sending and receiving text messages | User |
| FR-65 | Messages have a status: sent / read | System |
| FR-66 | Each conversation is linked to a specific service for context | System |
| FR-67 | All conversations are accessible from a dedicated "Chat" section | User |
| FR-68 | Messages are delivered in real time via Pusher | System |

### 3.11 Additional Features

| ID | Requirement | Actor |
|---|---|---|
| FR-69 | App supports Arabic and English with instant language switching | User |
| FR-70 | Privacy policy and terms of use are accessible from settings | User |
| FR-71 | User can change password from settings | User |
| FR-72 | User can share the app | User |

---

## 4. User Stories

### Onboarding

> **As a new user**, I open the app and see a splash screen with the Servixa logo, followed by two onboarding slides explaining the platform. I can skip or swipe through to reach the register screen.

> **As a new user**, I fill in my first name, last name, phone number (or email), country, and password. I tap Register. I receive an OTP on WhatsApp. I enter it and my account is created.

> **As a returning user**, I enter my phone number and password. I receive an OTP on WhatsApp for session confirmation. I am taken to the home screen.

---

### Business Account

> **As a registered user**, I want to create a business account so I can start offering or requesting services. I go through 4 steps: I choose my business type (e.g., Supplier), enter my license number and business name in Arabic and English, enter my city and address on a map, and upload supporting documents. I submit and wait for admin approval.

> **As a user with a pending business account**, I can see it listed under my profile with a "Pending" badge. I cannot post or request services yet.

> **As a user with an approved business account**, I can start posting services and requesting others — using that business account as my active identity for each action.

> **As a user with multiple approved business accounts**, when I want to post a service or send a request, the app asks me: "Which business account do you want to use?" I select one and continue.

---

### Posting a Service

> **As a provider**, I tap the "+" button on the bottom navigation. I choose which business account to post under. I select a category (e.g., Equipment), then a subcategory (e.g., Heavy Vehicles). I fill in the ad title, description, add images, set price (in SYP or USD), set available quantity, choose service type (sale or rent), and pin the location on a map. I submit. My service is now Pending and awaits admin approval. I am notified when it is approved or rejected.

> **As a provider**, I edit an approved service. It automatically goes back to Pending status and is re-reviewed by the admin.

---

### Browsing & Discovery

> **As any user (logged in or not)**, I see the home screen with promotional banners at the top, a grid of categories below, then horizontal scroll sections showing top listings by type.

> **As a user**, I tap on a category and see all approved services in that category. I can switch to subcategories. I can filter by location, price range, and service type. I can search by name.

> **As a user**, I tap on a service listing and see the full detail page: images, price, location on map, description, seller's business account name, average rating (e.g. 4.0 stars), and all reviews.

---

### Requesting a Service

> **As a requester**, I find a service I need. I tap "Request". The app asks which business account I want to use. I select one (it must be approved). I specify the date I need it, the quantity I want, and any additional details. I submit the request.

> **As a provider**, I open the Orders section and see "Received Orders". I see the request with: date, requester's catalog/business name, their contact info. I tap "Accept" or "Decline".

> **As a requester**, I open Orders → "My Orders". I can see the status of my sent requests. I can delete a pending request.

---

### Rating

> **As a requester whose request was accepted**, I can now leave a rating for the service. I give 1–5 stars and an optional comment. This rating appears on the service detail page and contributes to the average.

---

### Chat

> **As a user**, I view a service and tap "Chat with Provider". A conversation window opens. I type a message and send it. The provider sees it in real time (via Pusher). The conversation is linked to that service. I can find all my conversations under the "Chat" tab in the bottom navigation.

---

### Admin Flow

> **As an admin**, I log in at the admin dashboard URL with my email and password. I see a sidebar with sections: Dashboard, Business Accounts, Services, Categories, Cities, Sliders, Reports.

> **As an admin**, I open "Business Accounts → Pending". I review the submitted details and documents. I approve or reject. The user gets a push notification.

> **As an admin**, I go to "Categories". I create a new category "HVAC" with an Arabic name and an icon. I add subcategories under it. I define dynamic fields: "Capacity (tons)" as a number field, "Brand" as a text field. These fields appear automatically when a user creates a service under HVAC.

> **As a Super Admin**, I create a new admin account with the role "Content Reviewer" which only has permissions to approve/reject services and categories — but not business accounts or roles.

---

## 5. User Interface

### Design System

| Property | Value |
|---|---|
| **Brand Name** | SERVIXA — ALL SERVICES IN ONE PLACE |
| **Primary Color** | Purple `#6B21A8` (deep violet) |
| **Accent Color** | Light purple / lavender for backgrounds |
| **Background** | White `#FFFFFF` with light purple tints on screens |
| **Typography** | Clean sans-serif; bold headings with colored accent word |
| **Style** | Modern, minimal, mobile-first |
| **Language Direction** | RTL for Arabic, LTR for English (auto-switch) |

---

### Screen Inventory — Mobile App

#### Onboarding & Auth
| Screen | Description |
|---|---|
| **Splash Screen** | Servixa logo centered on white background |
| **Onboarding 1** | Illustration + "Discover Services or Promote Your Own" + Previous/Next |
| **Onboarding 2** | Illustration + "Smart Solutions for Every Project" + Previous/Finish |
| **Login** | Email + Password fields, Login button, "Don't have an account? Register" link |
| **Register** | First Name, Last Name, Email/Phone, Country dropdown (flag), Password, Confirm Password, Terms checkbox, Register button |
| **OTP Verification** | 4 or 6-digit OTP input, "Resend" option |

#### Home
| Screen | Description |
|---|---|
| **Home** | Top: user avatar + name + search icon. Banner slider. Categories horizontal scroll. "Top Tools & Equipment" section (horizontal cards). "Top Construction Services" section. "Top Items" section. Bottom nav: Home, Chat, + (FAB), My Ads, Orders |

#### Business Account Creation (Multi-Step)
| Step | Screen |
|---|---|
| **Step 1** | Select Business Profile Type: Supplier, Contractor, Engineering, Real Estate (icon grid, progress bar) |
| **Step 2** | Enter Business Details: License #, Name AR, Name EN, Activities, Details |
| **Step 3** | Enter Contact Information: City dropdown, Address text, Map preview, "View Location" button |
| **Step 4** | Select Main Category (icon grid) |
| **Step 5** | Select Sub Category (icon grid) |
| **Step 6** | Add Location: map pin placement, "View Location" button |
| **Step 7** | Upload Supporting Documents: Upload Doc + Upload Image buttons, preview list |

#### Service (Ad) Creation (Multi-Step)
| Step | Screen |
|---|---|
| **Step 1** | Choose Business Account modal: My Account / My Business Accounts selector |
| **Step 2** | Select Main Category |
| **Step 3** | Select Sub Category |
| **Step 4** | Write Ad Details: Title, Slug, Description, Add Main Picture, Add Sub Pictures, Price, Type (sale/rent) |
| **Step 5** | Dynamic Fields (if any for the category) |
| **Step 6** | Add Location on Map |

#### Discovery & Listings
| Screen | Description |
|---|---|
| **Search** | Search bar + filter icon. Results list: thumbnail, title, location, price (bold purple), heart icon |
| **Filter Sheet** | Bottom sheet: Location, Category, Subcategory, Budget (Min/Max sliders), Service Type (sale/rent), Posted Since |
| **View All** | List of services with toggle between grid and list view. Search bar at top |
| **Service Detail** | Hero image, Price (bold), Title, Location pin text, Rating stars + count, Description, Map embed, "Chat" button, "Make an Offer / Request" button, Reviews section |
| **Empty State** | Purple folder illustration + "Nothing Here Yet" + subtitle (bilingual) |

#### Orders
| Screen | Description |
|---|---|
| **Orders** | Two tabs: "Received Orders" / "My Orders". Each order card: Request Date, Catalog icon + name, Name, Phone. Accept (green) / Decline (red outlined) buttons on received orders |

#### Profile & Settings
| Screen | Description |
|---|---|
| **Profile** | User avatar, name, location. "Business Account" switcher (Change button). Menu: My Ads, My Reviews, Notifications, Favorite, Popular Question, Rating, Share This App, Profile Detail. Settings: Change Password, Change Language, Logout |
| **Notifications** | List of push notifications with icon, title, body, time |
| **Chat List** | List of conversations showing service thumbnail, other party name, last message, time |
| **Chat Window** | Messages bubbles (sent right, received left), sent/read status indicator, text input + send button |

---

### Admin Dashboard — Web (Blade)

| Section | Pages |
|---|---|
| **Auth** | Login page (email + password) |
| **Dashboard** | Overview stats: total users, pending business accounts, pending services, total orders |
| **Business Accounts** | Table with filters (pending/approved/rejected). View details + approve/reject actions |
| **Services** | Table with filters (pending/approved/rejected). View details + approve/reject actions |
| **Categories** | CRUD table for main categories + subcategories. Manage dynamic fields per category |
| **Cities** | CRUD table |
| **Sliders** | Upload image, set link, reorder, toggle active |
| **Reports** | Table of reported services with reason. Mark as reviewed / remove service |
| **Roles & Permissions** | Create roles, assign permissions checkboxes, assign roles to admins |
| **Admin Accounts** | Create/edit admin users, assign roles (Super Admin only) |

---

## 6. Technical Architecture

### Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 12 (PHP 8.2) |
| Database | MySQL (via XAMPP) |
| API Authentication | Laravel Passport (OAuth2 — Password Grant) |
| Admin Auth | Laravel Session Auth |
| Roles & Permissions | `spatie/laravel-permission` |
| Bilingual Support | Laravel built-in localization (AR / EN) |
| WhatsApp OTP | UltraMsg API via `Http::post()` |
| Push Notifications | Firebase (`kreait/laravel-firebase`) |
| Real-Time Chat | Pusher (`pusher/pusher-php-server`) |
| Image Handling | `intervention/image` |
| Admin UI | Laravel Blade + Tailwind CSS |

### API Design
- All mobile endpoints under `/api/v1/`
- All admin dashboard routes under `/admin/`
- JSON responses follow consistent structure: `{ success, message, data }`
- Passport OAuth2 `access_token` + `refresh_token` returned on login, sent as `Authorization: Bearer {access_token}` header on all authenticated API calls

### Key Business Rules Enforced in Code

1. User cannot post or request a service without an **Approved** business account
2. When posting or requesting, the user explicitly selects which business account to act as
3. Editing an approved service automatically resets its status to **Pending**
4. A rating can only be submitted if the order for that service was **Accepted**
5. Requested quantity must not exceed the service's available quantity
6. Admin operations are protected by role-based permission middleware — no admin action executes without the corresponding permission

---

## 7. Non-Functional Requirements

| Requirement | Detail |
|---|---|
| **Bilingual** | All user-facing content (categories, notifications, errors) must be available in both Arabic and English |
| **RTL Support** | Arabic layout must be fully right-to-left |
| **Security** | All API routes are protected by Passport (`auth:api` guard). Admin routes protected by session + role middleware |
| **Scalability** | Database relationships correctly normalized; supports growth in users, business accounts, and services |
| **Stability** | No logical errors: lifecycle states (pending/approved/rejected) enforced at every entry point |
| **Image Storage** | Uploaded images stored in `storage/app/public` with symlink to `public/storage` |
| **Validation** | All input validated server-side; API returns structured error messages |

---

*End of PRD — Servixa v1.0*
