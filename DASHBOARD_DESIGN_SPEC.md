# Servixa Admin Dashboard — Full Design Specification
## For use with Google Stitch (stitch.withgoogle.com)

> Copy this entire document into Stitch as your design prompt.
> It contains every page, component, color, spacing, and interaction detail needed
> to generate a complete, production-ready admin dashboard.

---

## PROJECT CONTEXT

**Product:** Servixa — "All Services in One Place"
**What this dashboard does:** Admin panel to manage a real estate & construction services marketplace
**Tech stack:** Laravel 12 + Blade templates + Tailwind CSS + Alpine.js
**Direction:** LTR (English interface for admin)
**Type:** Traditional server-rendered dashboard (form submissions, page reloads, no SPA)

---

## DESIGN SYSTEM — TOKENS & VARIABLES

### Color Palette

| Token Name | Hex Code | RGB | Usage |
|---|---|---|---|
| **Primary** | `#6B21A8` | rgb(107, 33, 168) | Buttons, sidebar background, active nav items, links, prices, table headers, accents |
| **Primary Hover** | `#7C3AED` | rgb(124, 58, 237) | Hover/focus states on all purple interactive elements |
| **Primary Light** | `#F5F3FF` | rgb(245, 243, 255) | Page background areas, category icon backgrounds, table header row background, selected row highlight |
| **Primary Border** | `#DDD6FE` | rgb(221, 214, 254) | Input field borders, card dividers, table cell borders, separator lines |
| **Page Background** | `#F8F7FF` | rgb(248, 247, 255) | Body/page background color (very light lavender) |
| **White** | `#FFFFFF` | rgb(255, 255, 255) | Card backgrounds, input backgrounds, modal backgrounds |
| **Text Primary** | `#1F2937` | rgb(31, 41, 55) | Headings (h1-h6), table text, labels, body text |
| **Text Secondary** | `#6B7280` | rgb(107, 114, 128) | Subtitles, descriptions, placeholders, timestamps, breadcrumb inactive |
| **Text Muted** | `#9CA3AF` | rgb(156, 163, 175) | Disabled text, footer text |
| **Success** | `#16A34A` | rgb(22, 163, 74) | Approved badge text, success alerts, checkmarks |
| **Success Light** | `#DCFCE7` | rgb(220, 252, 231) | Approved badge background, success alert background |
| **Danger** | `#DC2626` | rgb(220, 38, 38) | Rejected badge text, delete buttons, error alerts, error text |
| **Danger Light** | `#FEE2E2` | rgb(254, 226, 226) | Rejected badge background, error alert background |
| **Warning** | `#D97706` | rgb(217, 119, 6) | Pending badge text, warning alerts |
| **Warning Light** | `#FEF3C7` | rgb(254, 243, 199) | Pending badge background, warning alert background |
| **Info** | `#2563EB` | rgb(37, 99, 235) | Info badge text, info alerts, links |
| **Info Light** | `#DBEAFE` | rgb(219, 234, 254) | Info badge background |
| **Sidebar Text** | `#FFFFFF` | rgb(255, 255, 255) | Sidebar navigation text |
| **Sidebar Text Muted** | `#C4B5FD` | rgb(196, 181, 253) | Sidebar section headers, inactive icons (light purple) |
| **Sidebar Active BG** | `rgba(255,255,255,0.15)` | — | Active/hover nav item background in sidebar |

### Typography

| Element | Font Family | Size | Weight | Line Height | Color |
|---|---|---|---|---|---|
| **Page Title (h1)** | Inter, system-ui, sans-serif | 24px (1.5rem) | 700 (Bold) | 1.3 | #1F2937 |
| **Section Title (h2)** | Inter, system-ui, sans-serif | 20px (1.25rem) | 600 (Semibold) | 1.4 | #1F2937 |
| **Card Title (h3)** | Inter, system-ui, sans-serif | 16px (1rem) | 600 (Semibold) | 1.5 | #1F2937 |
| **Body Text** | Inter, system-ui, sans-serif | 14px (0.875rem) | 400 (Regular) | 1.5 | #1F2937 |
| **Small Text** | Inter, system-ui, sans-serif | 12px (0.75rem) | 400 (Regular) | 1.5 | #6B7280 |
| **Label** | Inter, system-ui, sans-serif | 14px (0.875rem) | 500 (Medium) | 1.5 | #1F2937 |
| **Button Text** | Inter, system-ui, sans-serif | 14px (0.875rem) | 600 (Semibold) | 1 | #FFFFFF |
| **Table Header** | Inter, system-ui, sans-serif | 12px (0.75rem) | 600 (Semibold) | 1.5 | #6B7280 |
| **Table Cell** | Inter, system-ui, sans-serif | 14px (0.875rem) | 400 (Regular) | 1.5 | #1F2937 |
| **Badge Text** | Inter, system-ui, sans-serif | 12px (0.75rem) | 500 (Medium) | 1 | Varies by type |
| **Stat Number** | Inter, system-ui, sans-serif | 28px (1.75rem) | 700 (Bold) | 1.2 | #1F2937 |
| **Stat Label** | Inter, system-ui, sans-serif | 13px (0.8125rem) | 400 (Regular) | 1.5 | #6B7280 |

### Spacing & Layout

| Token | Value | Usage |
|---|---|---|
| **Page Padding** | 24px (1.5rem) | Padding around the main content area |
| **Card Padding** | 24px (1.5rem) | Internal padding of cards |
| **Card Gap** | 24px (1.5rem) | Space between cards |
| **Section Gap** | 32px (2rem) | Space between major sections |
| **Form Group Gap** | 16px (1rem) | Space between form fields |
| **Input Padding** | 10px 14px | Padding inside inputs |
| **Button Padding** | 10px 20px | Padding inside buttons |
| **Table Cell Padding** | 12px 16px | Padding inside table cells |
| **Grid Margin** | 16px | Outer page margin |
| **Grid Gutter** | 8px | Between grid columns |

### Border & Shadow

| Token | Value | Usage |
|---|---|---|
| **Border Radius (Cards)** | 12px | All cards, modals, dropdowns |
| **Border Radius (Inputs)** | 12px | Text inputs, selects, textareas |
| **Border Radius (Buttons)** | 12px | All buttons |
| **Border Radius (Badges)** | 9999px (full pill) | Status badges |
| **Border Radius (Avatars)** | 50% (circle) | User/admin avatars |
| **Card Shadow** | `0 2px 8px rgba(107, 33, 168, 0.08)` | All card elements |
| **Dropdown Shadow** | `0 4px 16px rgba(107, 33, 168, 0.12)` | Dropdowns, modals |
| **Border Color** | `#DDD6FE` | Card borders, input borders, table borders, dividers |
| **Border Width** | 1px | All borders |

### Transitions

| Property | Duration | Easing |
|---|---|---|
| **Background Color** | 200ms | ease |
| **Border Color** | 200ms | ease |
| **Box Shadow** | 200ms | ease |
| **Transform** | 150ms | ease |
| **Opacity** | 150ms | ease |

---

## GLOBAL LAYOUT

### Structure

```
+---------------------------------------------------------------+
|                      FULL PAGE (bg: #F8F7FF)                   |
|  +----------+  +--------------------------------------------+  |
|  |          |  |  TOPBAR (h: 64px, bg: white, shadow)        |  |
|  |          |  |  [Breadcrumb]              [Admin ▾][Logout]|  |
|  |          |  +--------------------------------------------+  |
|  |          |  |                                              |  |
|  | SIDEBAR  |  |  MAIN CONTENT AREA                          |  |
|  | (w: 260px|  |  (padding: 24px)                            |  |
|  |  fixed)  |  |                                              |  |
|  | bg:      |  |  [Page Title + Action Button]                |  |
|  | #6B21A8  |  |  [Status Filter Tabs] (if applicable)        |  |
|  |          |  |  [Card with Table or Form]                   |  |
|  |          |  |                                              |  |
|  |          |  |                                              |  |
|  |          |  +--------------------------------------------+  |
|  |          |  |  FOOTER (text: #9CA3AF, 12px)               |  |
|  |          |  |  "Servixa v1.0 - All rights reserved"       |  |
|  +----------+  +--------------------------------------------+  |
+---------------------------------------------------------------+
```

### Sidebar (Fixed Left, 260px wide)

- **Background:** Solid `#6B21A8` (purple)
- **Width:** 260px, fixed position, full viewport height
- **Top Section:** Logo area
  - "Servixa" text: white, 22px, bold
  - "Admin Panel" text below: `#C4B5FD` (light purple), 12px
  - Padding: 24px, border-bottom: 1px solid `rgba(255,255,255,0.1)`
- **Navigation Items:** Vertical list
  - Each item: 44px height, padding: 12px 20px, border-radius: 8px, margin: 2px 12px
  - Icon (Heroicons outline, 20px) + Label text (14px, white, medium weight)
  - Icon and text gap: 12px
  - **Default state:** transparent background, white text
  - **Hover state:** background `rgba(255,255,255,0.1)`, smooth transition
  - **Active state:** background `rgba(255,255,255,0.15)`, white text, font-weight: 600, left border accent: 3px solid white
- **Section Headers:** Uppercase, 11px, letter-spacing: 0.05em, color: `#C4B5FD`, padding: 20px 20px 8px 20px
- **Navigation Groups:**
  ```
  MAIN
    Dashboard          (icon: home)

  MANAGEMENT
    Business Accounts  (icon: building-office)
    Services           (icon: wrench-screwdriver)
    Categories         (icon: squares-2x2)
    Cities             (icon: map-pin)
    Sliders            (icon: photo)
    Reports            (icon: flag)

  ADMINISTRATION
    Roles & Permissions (icon: shield-check)
    Admin Users         (icon: users)
  ```

### Topbar (Sticky Top, 64px height)

- **Background:** `#FFFFFF`
- **Border bottom:** 1px solid `#DDD6FE`
- **Shadow:** `0 1px 3px rgba(0,0,0,0.05)`
- **Height:** 64px
- **Layout:** Flex, space-between, align-center
- **Left side:** Breadcrumb
  - Format: "Dashboard / Business Accounts" with `>` separator
  - Inactive segments: `#6B7280`, 14px
  - Active segment (last): `#1F2937`, 14px, font-weight: 600
- **Right side:** Admin info
  - Admin avatar (32px circle, initials fallback with purple background)
  - Admin name: `#1F2937`, 14px, medium weight
  - Dropdown arrow icon
  - Logout button: text `#DC2626`, 14px, hover underline

### Footer

- **Padding:** 16px 24px
- **Border top:** 1px solid `#DDD6FE`
- **Text:** "Servixa Dashboard v1.0 &copy; 2026 All rights reserved"
- **Color:** `#9CA3AF`, 12px
- **Alignment:** Center

---

## REUSABLE COMPONENTS

### 1. Badge Component

Status pill badges used throughout the dashboard:

| Variant | Text Color | Background | Border | Example Text |
|---|---|---|---|---|
| **Pending** | `#D97706` | `#FEF3C7` | none | Pending |
| **Approved** | `#16A34A` | `#DCFCE7` | none | Approved |
| **Rejected** | `#DC2626` | `#FEE2E2` | none | Rejected |
| **Active** | `#16A34A` | `#DCFCE7` | none | Active |
| **Inactive** | `#6B7280` | `#F3F4F6` | none | Inactive |
| **Info** | `#2563EB` | `#DBEAFE` | none | Info |

- **Padding:** 4px 12px
- **Border Radius:** 9999px (full pill)
- **Font Size:** 12px, weight: 500
- **Display:** inline-flex, align-items: center

### 2. Button Component

| Variant | Background | Text | Border | Hover BG |
|---|---|---|---|---|
| **Primary** | `#6B21A8` | `#FFFFFF` | none | `#7C3AED` |
| **Secondary** | `#FFFFFF` | `#6B21A8` | 1px solid `#DDD6FE` | `#F5F3FF` |
| **Danger** | `#DC2626` | `#FFFFFF` | none | `#B91C1C` |
| **Danger Outline** | `transparent` | `#DC2626` | 1px solid `#DC2626` | `#FEE2E2` |
| **Success** | `#16A34A` | `#FFFFFF` | none | `#15803D` |

- **Padding:** 10px 20px
- **Border Radius:** 12px
- **Font Size:** 14px, weight: 600
- **Cursor:** pointer
- **Transition:** background 200ms ease
- **Icon + Text:** gap 8px, icon size 16px
- **Disabled:** opacity 0.5, cursor not-allowed

### 3. Card Component

- **Background:** `#FFFFFF`
- **Border:** 1px solid `#DDD6FE`
- **Border Radius:** 12px
- **Box Shadow:** `0 2px 8px rgba(107, 33, 168, 0.08)`
- **Padding:** 0 (content defines internal padding)
- **Card Header:** padding 20px 24px, border-bottom: 1px solid `#DDD6FE`, flex space-between
  - Title: 16px, semibold, `#1F2937`
  - Optional action button on the right
- **Card Body:** padding 24px
- **Card Footer:** padding 16px 24px, border-top: 1px solid `#DDD6FE`, background `#F9FAFB`

### 4. Input Component

- **Label:** 14px, weight 500, color `#1F2937`, margin-bottom 6px
- **Input field:**
  - Height: 42px
  - Padding: 10px 14px
  - Border: 1px solid `#DDD6FE`
  - Border Radius: 12px
  - Font Size: 14px
  - Background: `#FFFFFF`
  - Placeholder color: `#9CA3AF`
  - **Focus:** border-color `#6B21A8`, box-shadow `0 0 0 3px rgba(107, 33, 168, 0.1)`
  - **Error:** border-color `#DC2626`, error text below: 12px, `#DC2626`
- **With Icon:** icon 20px inside left side, padding-left 40px
- **Helper text:** 12px, `#6B7280`, margin-top 4px

### 5. Select Component

- Same styling as Input
- Custom dropdown arrow icon on right side (chevron-down, `#6B7280`)
- Padding-right: 40px to accommodate arrow

### 6. Textarea Component

- Same styling as Input
- Min-height: 100px
- Resize: vertical

### 7. Data Table Component

- **Wrapper:** Inside a Card component, no extra padding
- **Table Header Row:**
  - Background: `#F5F3FF` (primary light)
  - Text: 12px, semibold, uppercase, `#6B7280`, letter-spacing 0.05em
  - Padding: 12px 16px
  - Border-bottom: 1px solid `#DDD6FE`
- **Table Body Rows:**
  - Background: `#FFFFFF`
  - Text: 14px, regular, `#1F2937`
  - Padding: 12px 16px
  - Border-bottom: 1px solid `#F3F4F6`
  - **Hover:** background `#F5F3FF`
- **Actions Column:** Right-aligned, contains icon buttons
  - View (eye icon, `#6B7280`), Edit (pencil icon, `#6B21A8`), Delete (trash icon, `#DC2626`)
  - Icon buttons: 32px square, border-radius 8px, hover background `#F5F3FF`
- **Pagination:** Below table, flex space-between
  - Left: "Showing 1-10 of 50 results" (14px, `#6B7280`)
  - Right: Page numbers as buttons, active page has `#6B21A8` bg with white text

### 8. Modal Component

- **Overlay:** Fixed, full screen, background `rgba(0, 0, 0, 0.5)`, z-index 50
- **Modal Box:** Centered, background white, border-radius 12px, shadow `0 4px 16px rgba(107,33,168,0.12)`
  - Width: 480px (small), 640px (medium), 800px (large)
  - Max-height: 80vh, overflow-y: auto
- **Header:** padding 20px 24px, border-bottom 1px solid `#DDD6FE`
  - Title: 18px, semibold, `#1F2937`
  - Close button (X icon): top-right, 32px, `#6B7280`, hover `#1F2937`
- **Body:** padding 24px
- **Footer:** padding 16px 24px, border-top 1px solid `#DDD6FE`, flex justify-end, gap 12px

### 9. Alert Component

| Variant | Icon | Text Color | Background | Border-left |
|---|---|---|---|---|
| **Success** | check-circle | `#16A34A` | `#DCFCE7` | 4px solid `#16A34A` |
| **Error** | x-circle | `#DC2626` | `#FEE2E2` | 4px solid `#DC2626` |
| **Warning** | exclamation-triangle | `#D97706` | `#FEF3C7` | 4px solid `#D97706` |
| **Info** | information-circle | `#2563EB` | `#DBEAFE` | 4px solid `#2563EB` |

- **Padding:** 16px 20px
- **Border Radius:** 12px
- **Font Size:** 14px
- **Layout:** flex, icon (20px) + text, gap 12px
- **Dismissible:** Optional close button on right

### 10. Stat Card Component

- **Container:** White card, padding 24px
- **Layout:** Icon (left) + Content (right)
- **Icon Container:** 48px square, border-radius 12px, background `#F5F3FF`, center aligned
  - Icon: 24px, color `#6B21A8`
- **Content:**
  - Number: 28px, bold, `#1F2937`
  - Label: 13px, regular, `#6B7280`
  - Optional trend: 12px, green (positive) or red (negative) with arrow icon

### 11. Avatar Component

- **Sizes:** 32px (small), 40px (medium), 48px (large)
- **Shape:** Circle (border-radius 50%)
- **With Image:** object-fit cover
- **Without Image (Initials Fallback):**
  - Background: `#6B21A8`
  - Text: white, centered, font-weight 600
  - Font size: 50% of container size

### 12. Empty State Component

- **Container:** Centered, padding 48px
- **Illustration:** Purple-tinted folder/empty icon, 120px
  - SVG illustration: light purple (#F5F3FF) folder with `#6B21A8` accent
- **Title:** 18px, semibold, `#1F2937`, margin-top 16px
- **Description:** 14px, regular, `#6B7280`, margin-top 8px, max-width 320px, text-center
- **Optional CTA:** Primary button, margin-top 20px

### 13. Status Filter Tabs

Used on pages with status-based filtering (Business Accounts, Services):

- **Container:** flex, gap 0, margin-bottom 24px
- **Tab Items:**
  - Padding: 10px 20px
  - Font size: 14px, weight 500
  - Border-bottom: 2px solid transparent
  - Color: `#6B7280`
  - **Active tab:** color `#6B21A8`, border-bottom-color `#6B21A8`, font-weight 600
  - **Hover:** color `#6B21A8`
- **Tabs:** All | Pending | Approved | Rejected
  - Each tab shows count in parentheses: "Pending (12)"

### 14. Search Box

- **Container:** flex, gap 12px, margin-bottom 16px
- **Input:** Standard input with magnifying-glass icon on left
  - Width: 300px (or responsive)
  - Placeholder: "Search..."
- **Optional filter dropdown:** Select component next to search

---

## PAGE SPECIFICATIONS

---

### PAGE 1: Login Page (`/admin/login`)

**Layout:** No sidebar, no topbar. Full page centered.

```
+---------------------------------------------------------------+
|                     bg: #F8F7FF                                |
|                                                                |
|              +----------------------------+                    |
|              |     SERVIXA LOGO (purple)   |                   |
|              |     "Admin Dashboard"        |                   |
|              |                              |                   |
|              |  [Error Alert if any]        |                   |
|              |                              |                   |
|              |  Email          [input]      |                   |
|              |  Password       [input]      |                   |
|              |                              |                   |
|              |  [    Login Button    ]      |                   |
|              +----------------------------+                    |
|                   card: 400px max-width                        |
+---------------------------------------------------------------+
```

- Card: max-width 400px, padding 40px, centered vertically and horizontally
- Logo: "Servixa" in `#6B21A8`, 28px, bold. "Admin Dashboard" below in `#6B7280`, 14px
- Error box: `#FEE2E2` bg, `#DC2626` text, 12px border-radius 8px, shows validation errors
- Login button: Full width, Primary style

---

### PAGE 2: Dashboard (`/admin/dashboard`)

**Purpose:** Overview with key metrics and recent activity.

```
+--------------------------------------------+
|  Dashboard                                  |
+--------------------------------------------+
|                                              |
|  [Stat Card]  [Stat Card]  [Stat Card]  [Stat Card]
|  4-column grid, gap 24px                     |
|                                              |
|  +-------------------+  +----------------+  |
|  | Recent Business   |  | Recent         |  |
|  | Accounts          |  | Services       |  |
|  | (table, 5 rows)   |  | (table, 5 rows)|  |
|  +-------------------+  +----------------+  |
|  2-column grid, gap 24px                     |
+--------------------------------------------+
```

**Stat Cards (4 across):**

| # | Icon | Label | Example Value | Icon BG |
|---|---|---|---|---|
| 1 | building-office | Total Business Accounts | 142 | `#F5F3FF` |
| 2 | wrench-screwdriver | Total Services | 856 | `#F5F3FF` |
| 3 | clock | Pending Approvals | 23 | `#FEF3C7` |
| 4 | users | Total Users | 1,204 | `#F5F3FF` |

**Recent Business Accounts Table (left card):**
- Columns: Business Name | Owner | Status | Date
- Show 5 most recent
- Status column uses Badge component
- "View All" link in card header, color `#6B21A8`

**Recent Services Table (right card):**
- Columns: Service Title | Category | Status | Date
- Show 5 most recent
- Status column uses Badge component
- "View All" link in card header

---

### PAGE 3: Business Accounts List (`/admin/business-accounts`)

```
+------------------------------------------------+
|  Business Accounts              [+ Add] (hidden)|
+------------------------------------------------+
|  [All (50)] [Pending (12)] [Approved (35)] [Rejected (3)]  <- Status tabs
+------------------------------------------------+
|  [Search input]                 [Filter dropdown]|
+------------------------------------------------+
|  Card with Data Table                            |
|  +-----------+---------+--------+--------+-----+ |
|  | Business  | Owner   | City   | Status | Act | |
|  +-----------+---------+--------+--------+-----+ |
|  | ABC Corp  | Ahmed.. | Damascus| Pending| ... | |
|  | XYZ Ltd   | Omar..  | Aleppo | Approved|... | |
|  +-----------+---------+--------+--------+-----+ |
|  [Pagination]                                    |
+------------------------------------------------+
```

**Table Columns:**
| Column | Width | Content |
|---|---|---|
| Business Name | 25% | Name (translated, bold) + license number below in gray |
| Owner | 20% | User's first_name + last_name with avatar |
| Activity Type | 15% | Activity type name |
| City | 10% | City name |
| Status | 10% | Badge (pending/approved/rejected) |
| Created | 10% | Date formatted "Mar 30, 2026" |
| Actions | 10% | View (eye), Approve (check, green), Reject (x, red) |

---

### PAGE 4: Business Account Detail (`/admin/business-accounts/{id}`)

```
+--------------------------------------------------+
|  < Back to list    Business Account #123          |
+--------------------------------------------------+
|                                                    |
|  +---------------------+  +--------------------+  |
|  | Account Information  |  | Status & Actions   |  |
|  | Card                 |  | Card               |  |
|  |                      |  |                    |  |
|  | Business Name (AR):  |  | Status: [Badge]    |  |
|  |   معدات البناء       |  |                    |  |
|  | Business Name (EN):  |  | [Approve Button]   |  |
|  |   Construction Equip |  | [Reject Button]    |  |
|  | License: 12345       |  |                    |  |
|  | Activity Type: ...   |  | Rejection Reason:  |  |
|  | City: Damascus       |  | [textarea]         |  |
|  | Activities: ...      |  |                    |  |
|  | Details: ...         |  +--------------------+  |
|  | Location: [Map]      |                          |
|  +---------------------+                          |
|                                                    |
|  +---------------------------------------------+  |
|  | Documents & Images                            |  |
|  | [Image grid - thumbnails 120px, clickable]    |  |
|  +---------------------------------------------+  |
+--------------------------------------------------+
```

**Layout:** 2-column grid (70% / 30%) for top section.

**Account Information Card:**
- Each field: Label (12px, semibold, `#6B7280`, uppercase) + Value (14px, `#1F2937`)
- Field spacing: 16px between fields
- Map embed: 100% width, 200px height, border-radius 8px

**Status & Actions Card:**
- Current status shown as large Badge at top
- If pending: Show "Approve" (green button) and "Reject" (danger button)
- If rejecting: Show textarea for rejection reason
- Approval/rejection buttons: full width

**Documents Card:**
- Grid of uploaded files (3 columns)
- Image thumbnails: 120px square, border-radius 8px, object-fit cover
- Document files: shown as file icon with filename below
- Clickable to view full size in modal

---

### PAGE 5: Services List (`/admin/services`)

Same layout pattern as Business Accounts List.

**Table Columns:**
| Column | Content |
|---|---|
| Service | Title (translated) + main image thumbnail (40px square, rounded) |
| Category | Category name > Subcategory name |
| Business | Business account name |
| Price | Formatted price + currency (SYP/USD) |
| Type | Sale or Rent badge |
| Status | Badge (pending/approved/rejected) |
| Actions | View, Approve, Reject |

---

### PAGE 6: Service Detail (`/admin/services/{id}`)

```
+--------------------------------------------------+
|  < Back    Service: "Heavy Crane for Rent"        |
+--------------------------------------------------+
|                                                    |
|  +---------------------------+  +--------------+  |
|  | Image Gallery              |  | Status Card  |  |
|  | [Main image large]         |  | Status: Badge|  |
|  | [Thumbnail row below]      |  | [Approve]    |  |
|  +---------------------------+  | [Reject]     |  |
|                                  | [textarea]   |  |
|  +---------------------------+  +--------------+  |
|  | Service Details Card       |                    |
|  | Title (AR): ...            |                    |
|  | Title (EN): ...            |                    |
|  | Description: ...           |                    |
|  | Category: Equipment > Heavy|                    |
|  | Price: 500,000 SYP         |                    |
|  | Type: Rent                 |                    |
|  | Quantity: 3                |                    |
|  | Location: [Map]            |                    |
|  +---------------------------+                    |
|                                                    |
|  +---------------------------------------------+  |
|  | Dynamic Field Values                          |  |
|  | Weight: 50 tons                               |  |
|  | Year: 2020                                    |  |
|  | Condition: Used                                |  |
|  +---------------------------------------------+  |
+--------------------------------------------------+
```

---

### PAGE 7: Categories List (`/admin/categories`)

```
+------------------------------------------------+
|  Categories                      [+ Add Category]|
+------------------------------------------------+
|  Card with Data Table                            |
|  +------+-------+--------+-----------+-----+    |
|  | Icon | Name  | Sub-   | Dynamic   | Act |    |
|  |      |       | cats   | Fields    |     |    |
|  +------+-------+--------+-----------+-----+    |
|  | [ic] | Equipment| 5   | 3 fields  | ... |    |
|  |      | معدات    |      |           |     |    |
|  +------+-------+--------+-----------+-----+    |
|  | [ic] | Services | 8   | 2 fields  | ... |    |
|  |      | خدمات    |      |           |     |    |
|  +------+-------+--------+-----------+-----+    |
+------------------------------------------------+
```

**Table Columns:**
| Column | Content |
|---|---|
| Icon | Category icon image (40px, rounded, bg `#F5F3FF`) |
| Name | AR name + EN name below (smaller, gray) |
| Subcategories | Count with link "View (5)" |
| Dynamic Fields | Count with link "Manage (3)" |
| Sort Order | Number |
| Actions | Edit (pencil), Delete (trash) |

**Each category row is expandable** to show subcategories indented below.

---

### PAGE 8: Category Create/Edit (`/admin/categories/create`, `/admin/categories/{id}/edit`)

```
+------------------------------------------------+
|  Create Category / Edit Category                 |
+------------------------------------------------+
|  Card                                            |
|  +--------------------------------------------+ |
|  | Name (Arabic) *     [input: text RTL]       | |
|  | Name (English) *    [input: text LTR]       | |
|  | Parent Category     [select: optional]      | |
|  | Icon                [file upload]            | |
|  | Sort Order          [input: number]         | |
|  +--------------------------------------------+ |
|  | [Cancel]              [Save Category]        | |
|  +--------------------------------------------+ |
+------------------------------------------------+
```

- When "Parent Category" is selected, this becomes a subcategory.
- Arabic input field should have `dir="rtl"` attribute.
- File upload area: Dashed border `#DDD6FE`, border-radius 12px, padding 24px, centered icon + text "Click to upload or drag and drop". Shows preview after upload.

---

### PAGE 9: Dynamic Fields for Category (`/admin/categories/{id}/dynamic-fields`)

```
+------------------------------------------------+
|  Dynamic Fields for "Equipment"    [+ Add Field] |
+------------------------------------------------+
|  Card with Data Table                            |
|  +-------+------+---------+----------+-----+    |
|  | Label | Type | Required| Options  | Act |    |
|  +-------+------+---------+----------+-----+    |
|  | Weight| number| Yes    | -        | ... |    |
|  | الوزن |       |        |          |     |    |
|  +-------+------+---------+----------+-----+    |
|  | Color | select| No     | Red,Blue | ... |    |
|  | اللون |       |        |          |     |    |
|  +-------+------+---------+----------+-----+    |
+------------------------------------------------+
```

---

### PAGE 10: Dynamic Field Create/Edit

```
+------------------------------------------------+
|  Add Dynamic Field to "Equipment"                |
+------------------------------------------------+
|  Card                                            |
|  +--------------------------------------------+ |
|  | Label (Arabic) *    [input: text RTL]       | |
|  | Label (English) *   [input: text LTR]       | |
|  | Field Type *        [select: text/number/   | |
|  |                      select/textarea/boolean]| |
|  | Is Required?        [toggle switch]         | |
|  | Options             [input: comma-separated]| |
|  |   (shown only when type = "select")          | |
|  | Sort Order          [input: number]         | |
|  +--------------------------------------------+ |
|  | [Cancel]              [Save Field]           | |
|  +--------------------------------------------+ |
+------------------------------------------------+
```

---

### PAGE 11: Cities List (`/admin/cities`)

```
+------------------------------------------------+
|  Cities                            [+ Add City]  |
+------------------------------------------------+
|  Card with Data Table                            |
|  +------+--------+------------------+-----+     |
|  | #    | Name   | Business Accounts| Act |     |
|  +------+--------+------------------+-----+     |
|  | 1    | Damascus / دمشق  | 42     | ... |     |
|  | 2    | Aleppo / حلب     | 38     | ... |     |
|  +------+--------+------------------+-----+     |
+------------------------------------------------+
```

---

### PAGE 12: City Create/Edit (`/admin/cities/create`, `/admin/cities/{id}/edit`)

```
+------------------------------------------------+
|  Create City / Edit City                         |
+------------------------------------------------+
|  Card                                            |
|  +--------------------------------------------+ |
|  | Name (Arabic) *     [input: text RTL]       | |
|  | Name (English) *    [input: text LTR]       | |
|  +--------------------------------------------+ |
|  | [Cancel]                    [Save City]      | |
|  +--------------------------------------------+ |
+------------------------------------------------+
```

- Simple 2-field form inside a card.

---

### PAGE 13: Sliders List (`/admin/sliders`)

```
+------------------------------------------------+
|  Ad Sliders                       [+ Add Slider] |
+------------------------------------------------+
|  Card with Data Table                            |
|  +--------+------+-------+--------+-----+       |
|  | Image  | Link | Order | Active | Act |       |
|  +--------+------+-------+--------+-----+       |
|  | [thumb]| http | 1     | Active | ... |       |
|  | [thumb]| http | 2     |Inactive| ... |       |
|  +--------+------+-------+--------+-----+       |
+------------------------------------------------+
```

- Image column: 80x40px thumbnail, rounded
- Active column: Green "Active" or Gray "Inactive" badge
- Actions: Edit, Delete, Toggle Active

---

### PAGE 14: Slider Create/Edit

```
+------------------------------------------------+
|  Create Slider / Edit Slider                     |
+------------------------------------------------+
|  Card                                            |
|  +--------------------------------------------+ |
|  | Image *          [file upload with preview]  | |
|  | Link (optional)  [input: url]               | |
|  | Sort Order       [input: number, default 0] | |
|  | Active           [toggle switch]            | |
|  +--------------------------------------------+ |
|  | [Cancel]                   [Save Slider]     | |
|  +--------------------------------------------+ |
+------------------------------------------------+
```

---

### PAGE 15: Reports List (`/admin/reports`)

```
+------------------------------------------------+
|  Reports                                         |
+------------------------------------------------+
|  [All] [Unresolved] [Resolved]    <- Filter tabs |
+------------------------------------------------+
|  Card with Data Table                            |
|  +----------+--------+--------+--------+-----+  |
|  | Service  | User   | Reason | Status | Act |  |
|  +----------+--------+--------+--------+-----+  |
|  | Crane..  | Ahmed  | Spam.. |Unresolved|...|  |
|  +----------+--------+--------+--------+-----+  |
+------------------------------------------------+
```

---

### PAGE 16: Report Detail (`/admin/reports/{id}`)

```
+--------------------------------------------------+
|  < Back    Report #45                             |
+--------------------------------------------------+
|  +---------------------+  +--------------------+  |
|  | Report Details       |  | Reported Service   |  |
|  | Reporter: Ahmed Ali  |  | [Service card]     |  |
|  | Date: Mar 30, 2026   |  | Title, image,      |  |
|  | Reason: "This is..." |  | price, link to     |  |
|  |                      |  | service detail     |  |
|  | Status: [Badge]      |  +--------------------+  |
|  | [Mark Resolved btn]  |                          |
|  +---------------------+                          |
+--------------------------------------------------+
```

---

### PAGE 17: Roles List (`/admin/roles`)

```
+------------------------------------------------+
|  Roles & Permissions                [+ Add Role] |
+------------------------------------------------+
|  Card with Data Table                            |
|  +------+-----------+--------+-----+            |
|  | #    | Role Name | Admins | Act |            |
|  +------+-----------+--------+-----+            |
|  | 1    | Super Admin| 1     | ... |            |
|  | 2    | Moderator  | 3     | ... |            |
|  | 3    | Reviewer   | 2     | ... |            |
|  +------+-----------+--------+-----+            |
+------------------------------------------------+
```

---

### PAGE 18: Role Create/Edit (`/admin/roles/create`, `/admin/roles/{id}/edit`)

```
+------------------------------------------------+
|  Create Role / Edit Role                         |
+------------------------------------------------+
|  Card                                            |
|  +--------------------------------------------+ |
|  | Role Name *        [input: text]            | |
|  +--------------------------------------------+ |
|  | Permissions:                                 | |
|  |                                              | |
|  | Business Accounts                            | |
|  |   [x] View   [x] Approve   [x] Reject      | |
|  |                                              | |
|  | Services                                     | |
|  |   [x] View   [x] Approve   [x] Reject      | |
|  |                                              | |
|  | Categories                                   | |
|  |   [x] View   [x] Create  [x] Edit  [x] Del | |
|  |                                              | |
|  | Cities                                       | |
|  |   [x] View   [x] Create  [x] Edit  [x] Del | |
|  |                                              | |
|  | Sliders                                      | |
|  |   [x] View   [x] Create  [x] Edit  [x] Del | |
|  |                                              | |
|  | Reports                                      | |
|  |   [x] View   [x] Resolve                    | |
|  |                                              | |
|  | Roles (Super Admin only)                     | |
|  |   [x] View   [x] Create  [x] Edit  [x] Del | |
|  |                                              | |
|  | Admin Users (Super Admin only)               | |
|  |   [x] View   [x] Create  [x] Edit  [x] Del | |
|  +--------------------------------------------+ |
|  | [Cancel]                     [Save Role]     | |
|  +--------------------------------------------+ |
+------------------------------------------------+
```

**Permissions layout:**
- Grouped by feature (card-like sections)
- Each group: Label (16px, semibold) + checkbox grid below
- Checkboxes: Custom styled with `#6B21A8` fill when checked
- "Select All" checkbox per group

---

### PAGE 19: Admin Users List (`/admin/admins`)

```
+------------------------------------------------+
|  Admin Users                     [+ Add Admin]   |
+------------------------------------------------+
|  Card with Data Table                            |
|  +--------+-------+------+--------+-----+       |
|  | Avatar | Name  | Email| Role   | Act |       |
|  +--------+-------+------+--------+-----+       |
|  | [AV]   | Super | s@.. |S. Admin| ... |       |
|  | [AV]   | Ahmad | a@.. |Moderator|... |       |
|  +--------+-------+------+--------+-----+       |
+------------------------------------------------+
```

---

### PAGE 20: Admin User Create/Edit (`/admin/admins/create`, `/admin/admins/{id}/edit`)

```
+------------------------------------------------+
|  Create Admin / Edit Admin                       |
+------------------------------------------------+
|  Card                                            |
|  +--------------------------------------------+ |
|  | Name *             [input: text]            | |
|  | Email *            [input: email]           | |
|  | Password *         [input: password]        | |
|  | Confirm Password * [input: password]        | |
|  | Role *             [select: dropdown]       | |
|  +--------------------------------------------+ |
|  | [Cancel]                    [Save Admin]     | |
|  +--------------------------------------------+ |
+------------------------------------------------+
```

- On edit: password fields are optional (only fill to change)
- Role dropdown lists all available roles

---

## COMPLETE PAGE LIST SUMMARY

| # | Page | Route | Key Feature |
|---|---|---|---|
| 1 | Login | `/admin/login` | Centered card, no sidebar |
| 2 | Dashboard | `/admin/dashboard` | 4 stat cards + 2 recent tables |
| 3 | Business Accounts List | `/admin/business-accounts` | Status tabs + table + pagination |
| 4 | Business Account Detail | `/admin/business-accounts/{id}` | Detail view + approve/reject actions |
| 5 | Services List | `/admin/services` | Status tabs + table + pagination |
| 6 | Service Detail | `/admin/services/{id}` | Gallery + detail + approve/reject |
| 7 | Categories List | `/admin/categories` | Expandable rows with subcategories |
| 8 | Category Create/Edit | `/admin/categories/create` | Bilingual name + icon upload |
| 9 | Dynamic Fields List | `/admin/categories/{id}/dynamic-fields` | Table of fields per category |
| 10 | Dynamic Field Create/Edit | `/admin/categories/{id}/dynamic-fields/create` | Field config form |
| 11 | Cities List | `/admin/cities` | Simple table |
| 12 | City Create/Edit | `/admin/cities/create` | Bilingual name form |
| 13 | Sliders List | `/admin/sliders` | Table with image thumbs |
| 14 | Slider Create/Edit | `/admin/sliders/create` | Image upload + config |
| 15 | Reports List | `/admin/reports` | Resolved/unresolved tabs |
| 16 | Report Detail | `/admin/reports/{id}` | Report info + linked service |
| 17 | Roles List | `/admin/roles` | Simple table |
| 18 | Role Create/Edit | `/admin/roles/create` | Permission checkbox grid |
| 19 | Admin Users List | `/admin/admins` | Table with role column |
| 20 | Admin User Create/Edit | `/admin/admins/create` | User form + role select |

---

## ICONS REFERENCE (Heroicons - Outline style)

| Usage | Icon Name |
|---|---|
| Dashboard | `home` |
| Business Accounts | `building-office` |
| Services | `wrench-screwdriver` |
| Categories | `squares-2x2` |
| Cities | `map-pin` |
| Sliders | `photo` |
| Reports | `flag` |
| Roles | `shield-check` |
| Admin Users | `users` |
| Search | `magnifying-glass` |
| Add/Create | `plus` |
| Edit | `pencil-square` |
| Delete | `trash` |
| View/Eye | `eye` |
| Approve/Check | `check-circle` |
| Reject/Close | `x-circle` |
| Back Arrow | `arrow-left` |
| Chevron Down | `chevron-down` |
| Logout | `arrow-right-on-rectangle` |
| Notification | `bell` |
| Calendar | `calendar` |
| Upload | `cloud-arrow-up` |
| Filter | `funnel` |
| Sort | `arrows-up-down` |

---

## RESPONSIVE BEHAVIOR

| Breakpoint | Sidebar | Content | Table |
|---|---|---|---|
| >= 1280px (xl) | 260px fixed visible | Remaining width | Full columns |
| 1024-1279px (lg) | 260px fixed visible | Remaining width | Hide optional columns |
| 768-1023px (md) | Collapsible (hamburger toggle) | Full width | Horizontal scroll |
| < 768px (sm) | Hidden (overlay on toggle) | Full width | Horizontal scroll, card layout option |

---

## INTERACTION STATES

### Delete Confirmation Modal
When clicking any delete button:
- Modal title: "Confirm Deletion"
- Body: "Are you sure you want to delete this [item]? This action cannot be undone."
- Buttons: "Cancel" (secondary) + "Delete" (danger)

### Approve/Reject Actions
- Approve: Instant action with success alert
- Reject: Shows textarea modal for rejection reason, then confirms

### Form Validation
- All required fields marked with red asterisk (*)
- Error messages appear below the field in red (12px, `#DC2626`)
- Field border turns `#DC2626` on error
- Success flash message appears as Alert (success) at top of content area after form submission

### Loading States
- Buttons show spinner icon when submitting
- Tables show skeleton loading rows (3 gray animated bars per cell)

### Toast Notifications
- Position: top-right, 20px from edges
- Auto-dismiss: 5 seconds
- Slide-in animation from right
- Same styling as Alert component but floating with shadow

---

## FILE UPLOAD PATTERNS

### Image Upload Zone
```
+------------------------------------------+
|  - - - - - - - - - - - - - - - - - - - - |
|  |                                      | |
|  |    [Cloud Upload Icon - #6B21A8]     | |
|  |                                      | |
|  |    Click to upload or drag & drop    | |
|  |    PNG, JPG up to 5MB               | |
|  |                                      | |
|  - - - - - - - - - - - - - - - - - - - - |
+------------------------------------------+
```
- Dashed border: 2px dashed `#DDD6FE`
- Border-radius: 12px
- Background: `#F5F3FF` (hover)
- After upload: Shows image preview with remove (X) button overlay

---

## BILINGUAL FIELD PATTERN

For all translatable fields (name, title, description), the form shows two inputs:

```
  Name (Arabic) *
  +------------------------------------------+
  |  [RTL input field]          dir="rtl"     |
  +------------------------------------------+

  Name (English) *
  +------------------------------------------+
  |  [LTR input field]          dir="ltr"     |
  +------------------------------------------+
```

- Arabic field always comes first
- Arabic field has `dir="rtl"` and `text-align: right`
- English field has `dir="ltr"` and `text-align: left`
- Both are required (marked with *)
- In tables, show both languages: "معدات" / "Equipment" (AR on top, EN below in gray smaller text)

---

## TAILWIND CSS CONFIGURATION (for reference)

```js
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#6B21A8',
          hover: '#7C3AED',
          light: '#F5F3FF',
          border: '#DDD6FE',
        },
        page: {
          bg: '#F8F7FF',
        },
      },
      borderRadius: {
        DEFAULT: '12px',
      },
      boxShadow: {
        card: '0 2px 8px rgba(107, 33, 168, 0.08)',
        dropdown: '0 4px 16px rgba(107, 33, 168, 0.12)',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
}
```

---

## FULL PERMISSION LIST

These permissions should be checkboxes in the Role Create/Edit form:

| Group | Permissions |
|---|---|
| **Business Accounts** | `business-accounts.view`, `business-accounts.approve`, `business-accounts.reject` |
| **Services** | `services.view`, `services.approve`, `services.reject` |
| **Categories** | `categories.view`, `categories.create`, `categories.edit`, `categories.delete` |
| **Dynamic Fields** | `dynamic-fields.view`, `dynamic-fields.create`, `dynamic-fields.edit`, `dynamic-fields.delete` |
| **Cities** | `cities.view`, `cities.create`, `cities.edit`, `cities.delete` |
| **Sliders** | `sliders.view`, `sliders.create`, `sliders.edit`, `sliders.delete` |
| **Reports** | `reports.view`, `reports.resolve` |
| **Roles** | `roles.view`, `roles.create`, `roles.edit`, `roles.delete` |
| **Admins** | `admins.view`, `admins.create`, `admins.edit`, `admins.delete` |
