# Blade Structure & Design System
## Servixa Admin Dashboard

---

## Design Tokens (from Figma)

```css
/* Colors */
--color-primary:        #6B21A8;   /* main purple — buttons, prices, active nav */
--color-primary-hover:  #7C3AED;   /* hover/active state */
--color-primary-light:  #F5F3FF;   /* backgrounds, category icon bg */
--color-primary-border: #DDD6FE;   /* borders, dividers */

--color-text-primary:   #1F2937;   /* headings, labels */
--color-text-secondary: #6B7280;   /* subtitles, placeholders */
--color-text-muted:     #9CA3AF;   /* hints, timestamps */

--color-white:          #FFFFFF;   /* card bg, modal bg */
--color-bg:             #F8F7FF;   /* page background */

--color-success:        #16A34A;   /* approved, accepted, verified */
--color-success-light:  #DCFCE7;
--color-danger:         #DC2626;   /* rejected, delete */
--color-danger-light:   #FEE2E2;
--color-warning:        #D97706;   /* pending */
--color-warning-light:  #FEF3C7;

/* Spacing (from Grid.png) */
--page-margin:    16px;
--grid-gutter:    8px;
--column-width:   94px;

/* Radius */
--radius-sm:   8px;
--radius-md:   12px;   /* cards, inputs, buttons */
--radius-lg:   16px;   /* modals, bottom sheets */
--radius-full: 9999px; /* badges, pills, avatars */

/* Shadows */
--shadow-card: 0 2px 8px rgba(107, 33, 168, 0.08);
--shadow-modal: 0 8px 32px rgba(107, 33, 168, 0.16);
```

---

## 1. PHP Component Classes

```
app/View/Components/
├── Badge.php          ← status badges (pending/approved/rejected/etc.)
├── Button.php         ← primary, secondary, danger, outline variants
├── Card.php           ← general card wrapper with optional header/footer
├── Input.php          ← text input with icon support
├── Select.php         ← dropdown select with icon support
├── Textarea.php       ← multiline input
├── Modal.php          ← modal dialog wrapper
├── Alert.php          ← success/error/warning/info alerts
├── Avatar.php         ← user/business avatar with fallback initials
├── StatCard.php       ← dashboard stat card (number + label + icon)
├── DataTable.php      ← table wrapper with search + pagination
└── EmptyState.php     ← empty state illustration + message
```

---

## 2. Blade Component Views

```
resources/views/components/
├── badge.blade.php          ← <x-badge type="approved">Approved</x-badge>
├── button.blade.php         ← <x-button variant="primary">Save</x-button>
├── card.blade.php           ← <x-card title="Title">...</x-card>
├── input.blade.php          ← <x-input name="email" icon="envelope"/>
├── select.blade.php         ← <x-select name="city_id" :options="$cities"/>
├── textarea.blade.php       ← <x-textarea name="description"/>
├── modal.blade.php          ← <x-modal id="confirmModal" title="Confirm"/>
├── alert.blade.php          ← <x-alert type="success">Saved!</x-alert>
├── avatar.blade.php         ← <x-avatar src="..." name="Ahmad Ali"/>
├── stat-card.blade.php      ← dashboard number cards
├── data-table.blade.php     ← reusable table shell
└── empty-state.blade.php   ← "Nothing here yet" with purple folder illustration
```

---

## 3. Layouts

```
resources/views/layouts/
└── app.blade.php            ← master layout: sidebar + topbar + content slot
```

**`app.blade.php` structure:**
```html
<html>
  <head> Tailwind CSS, Alpine.js, fonts </head>
  <body class="bg-[#F8F7FF]">
    <div class="flex h-screen">

      <!-- Sidebar (fixed left) -->
      @include('partials.sidebar')

      <!-- Main content area -->
      <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Topbar -->
        @include('partials.header')

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto p-6">
          @yield('content')
        </main>

      </div>
    </div>

    <!-- Global modals slot -->
    @stack('modals')
    @stack('scripts')
  </body>
</html>
```

---

## 4. Partials

```
resources/views/partials/
├── sidebar.blade.php        ← left navigation panel
├── header.blade.php         ← topbar (breadcrumb + admin name + logout)
└── footer.blade.php         ← optional footer (version, copyright)
```

### Sidebar Design (from Figma style)

```
┌─────────────────────┐
│  SERVIXA  logo      │  ← purple bg, white text
│  ALL SERVICES       │
├─────────────────────┤
│  📊 Dashboard       │  ← active = purple bg pill
│  🏢 Business Accts  │
│  📋 Services        │
│  🗂️  Categories      │
│  🏙️  Cities          │
│  🖼️  Sliders         │
│  🚨 Reports         │
│  ──────────────── │
│  👥 Roles & Perms   │  ← super admin only
│  👤 Admin Users     │  ← super admin only
├─────────────────────┤
│  [avatar] Admin     │
│  Logout             │
└─────────────────────┘
```

---

## 5. Feature Pages (Admin Dashboard)

```
resources/views/
├── auth/
│   └── login.blade.php              ← admin login page

├── dashboard/
│   └── index.blade.php              ← overview stats + recent activity

├── business-accounts/
│   ├── index.blade.php              ← table with status filter tabs
│   ├── show.blade.php               ← detail + documents + approve/reject
│   └── _card.blade.php              ← reusable account row/card partial

├── services/
│   ├── index.blade.php              ← table with status filter tabs
│   ├── show.blade.php               ← detail + images + approve/reject
│   └── _card.blade.php

├── categories/
│   ├── index.blade.php              ← tree view: main → sub
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── dynamic-fields/
│       ├── index.blade.php          ← list fields for a category
│       ├── create.blade.php
│       └── edit.blade.php

├── cities/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php

├── sliders/
│   ├── index.blade.php              ← sortable slider list
│   ├── create.blade.php
│   └── edit.blade.php

├── reports/
│   ├── index.blade.php              ← reported services list
│   └── show.blade.php               ← report detail + resolve action

├── roles/
│   ├── index.blade.php              ← roles list (super admin only)
│   ├── create.blade.php             ← role + permissions checklist
│   └── edit.blade.php

└── admins/
    ├── index.blade.php              ← admin users list (super admin only)
    ├── create.blade.php
    └── edit.blade.php
```

---

## 6. Component Usage Examples

### Badge
```blade
{{-- Status badges --}}
<x-badge type="pending">Pending</x-badge>
<x-badge type="approved">Approved</x-badge>
<x-badge type="rejected">Rejected</x-badge>

{{-- Renders as colored pill --}}
{{-- pending  → amber bg   + amber text  --}}
{{-- approved → green bg   + green text  --}}
{{-- rejected → red bg     + red text    --}}
```

### Button
```blade
<x-button variant="primary" href="{{ route('services.index') }}">
    View Services
</x-button>

<x-button variant="success" wire:click="approve">
    Approve
</x-button>

<x-button variant="danger" onclick="confirm()">
    Reject
</x-button>

<x-button variant="outline">
    Cancel
</x-button>
```

### Card
```blade
<x-card title="Business Accounts" subtitle="Pending review">
    {{-- card body content --}}
    <x-slot name="actions">
        <x-button variant="primary">Add New</x-button>
    </x-slot>
</x-card>
```

### Input
```blade
<x-input
    name="license_number"
    label="License Number"
    placeholder="Enter license..."
    icon="identification"
    :value="old('license_number')"
/>
```

### Stat Card (Dashboard)
```blade
<x-stat-card
    label="Pending Business Accounts"
    :value="$pendingAccounts"
    icon="building-office"
    color="warning"
    :trend="+12"
/>
```

### Empty State
```blade
<x-empty-state
    title="No Services Found"
    message="No services are pending review right now."
    icon="document-magnifying-glass"
/>
```

---

## 7. Page Layout Pattern

Every admin page follows this exact pattern:

```blade
@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#1F2937]">Page Title</h1>
            <p class="text-sm text-[#6B7280]">Subtitle or breadcrumb</p>
        </div>
        <x-button variant="primary" href="...">+ Add New</x-button>
    </div>

    {{-- Status Filter Tabs (for approval pages) --}}
    <div class="flex gap-2 mb-4">
        <a href="?status=all"      class="tab {{ $status == 'all'      ? 'tab-active' : '' }}">All</a>
        <a href="?status=pending"  class="tab {{ $status == 'pending'  ? 'tab-active' : '' }}">Pending</a>
        <a href="?status=approved" class="tab {{ $status == 'approved' ? 'tab-active' : '' }}">Approved</a>
        <a href="?status=rejected" class="tab {{ $status == 'rejected' ? 'tab-active' : '' }}">Rejected</a>
    </div>

    {{-- Content Card --}}
    <x-card>
        {{-- Table or list --}}
    </x-card>

@endsection
```

---

## 8. Tailwind Color Config (tailwind.config.js)

```js
theme: {
    extend: {
        colors: {
            primary: {
                DEFAULT: '#6B21A8',
                hover:   '#7C3AED',
                light:   '#F5F3FF',
                border:  '#DDD6FE',
            }
        },
        borderRadius: {
            card:  '12px',
            modal: '16px',
        },
        boxShadow: {
            card:  '0 2px 8px rgba(107, 33, 168, 0.08)',
            modal: '0 8px 32px rgba(107, 33, 168, 0.16)',
        }
    }
}
```

---

## 9. Key Style Rules (from Figma)

| Element | Style |
|---|---|
| Page background | `#F8F7FF` (very light purple tint) |
| Cards | White `#FFFFFF`, radius `12px`, shadow `card` |
| Primary buttons | `#6B21A8` bg, white text, radius `12px`, hover `#7C3AED` |
| Outline buttons | white bg, `#6B21A8` border + text |
| Danger buttons | `#DC2626` bg, white text |
| Inputs | White bg, `#DDD6FE` border, `12px` radius, focus ring `#6B21A8` |
| Sidebar bg | `#6B21A8` (dark purple) |
| Sidebar text | White |
| Sidebar active item | White bg pill, `#6B21A8` text |
| Table header | `#F5F3FF` bg, `#1F2937` text |
| Table rows | White, hover `#F5F3FF` |
| Status: pending | `#FEF3C7` bg + `#D97706` text |
| Status: approved | `#DCFCE7` bg + `#16A34A` text |
| Status: rejected | `#FEE2E2` bg + `#DC2626` text |
| Prices | Bold, `#6B21A8` color |
| Section titles | `text-2xl font-bold`, accent word in `#6B21A8` |
