# sidq-mart

> **SIDQ MART — Production-Grade Enterprise E-Commerce Platform**  
> **Engineered & Powered by SIDQ Technology (সিদিক টেকনোলজি)**  
> *Core Software Architecture & Commerce Engine © SIDQ Technology. All Rights Reserved.*

---

## 🌟 Overview

**SIDQ MART** is a modern, high-performance, and white-label ready e-commerce web application engineered with **Laravel 12** and **Bootstrap 5**. Designed with clean code architecture, portable migrations (supporting SQLite for development and MySQL/XAMPP for production), accessible design tokens (WCAG 2.2 AA), and an ultra-modern administrative panel.

---

## 🚀 Key Features

### 🛒 1. Customer Storefront
- **Responsive Product Showcase**: 5-column responsive product card grid with hover image zoom, lift animations, instant discount badges, and quick add-to-cart buttons.
- **Trust & Features Section**: Polished `.intro-part` trust strip with soft mint background (`#f8fffa`), circular double-ring icons, and animated hover effects.
- **Offcanvas Cart Drawer**: Real-time sliding cart drawer for seamless shopping without page redirects.
- **Fast 1-Page Checkout**: Dynamic delivery zone fee calculation (Inside Dhaka ৳70 / Outside Dhaka ৳130 / Free delivery above threshold), coupon validation, and payment instructions.
- **Dual-Mode Customer Authentication**:
  - **Instant Modal Popup (`#customerAuthModal`)**: Available from any page with AJAX login and registration tabs.
  - **Dedicated Login & Registration Page (`/login`)**: Full-screen responsive customer portal.
- **Customer Dashboard (`/customer/dashboard`)**: Order history tracking, delivery status timeline, and saved address management.

### 🛡️ 2. Role-Based Access Control (RBAC) & Staff Management
- **4 Distinct Roles**:
  - **সুপার অ্যাডমিন (Administrator)**: Full unrestricted platform management.
  - **শপ ম্যানেজার (Shop Manager)**: Orders, catalog, stock, banners, coupons, and customer viewing.
  - **এমপ্লয়ি / স্টাফ (Employee)**: Order processing, delivery status update, invoice printing, and inventory viewing.
  - **সাধারণ গ্রাহক (Customer)**: Storefront shopping and order tracking.
- **Granular Permissions Matrix**: Over 12 customizable permissions grouped into Orders, Catalog, Marketing, Staff, and Settings with automatic role presets.

### 🎨 3. Modernized Admin Console & Dynamic Theme Customizer
- **Pastel Mint Aesthetic (`#f8fffa`)**: Soft, eye-pleasing mint background palette, glassmorphic header, 14px rounded cards, and clean typography.
- **Dynamic Theme Color Engine**: Change primary brand color, button hover color, and admin background tint directly from Admin Settings with live preview and 8 one-click presets (SIDQ Red, Emerald Green, Royal Blue, Modern Violet, Vibrant Orange, Rose Crimson, Ocean Teal).

---

## 🔑 Default Login Credentials

| Role | Email | Password | Access URL |
|---|---|---|---|
| **Super Administrator** | `admin@sidqmart.com` | `admin123` | `http://127.0.0.1:8000/admin` |
| **Administrator (Tech Support)** | `admin@sidqtech.com` | `admin123` | `http://127.0.0.1:8000/admin` |
| **Shop Manager** | `manager@sidqmart.com` | `password123` | `http://127.0.0.1:8000/admin` |
| **Employee / Staff** | `employee@sidqmart.com` | `password123` | `http://127.0.0.1:8000/admin` |
| **Customer** | `customer@sidqmart.com` | `password123` | `http://127.0.0.1:8000/login` |

---

## 🛠️ Installation & Setup Guide

### Prerequisites
- PHP 8.2 or higher
- Composer
- SQLite (or MySQL via XAMPP)

### Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/sidq-technology/sidq-mart.git
   cd sidq-mart
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Configure environment:**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Run migrations and seed default data:**
   ```bash
   php artisan migrate --seed
   ```

5. **Link storage directory:**
   ```bash
   php artisan storage:link
   ```

6. **Start local development server:**
   ```bash
   php artisan serve
   ```
   Visit `http://127.0.0.1:8000` in your browser.

---

## 🏛️ Architecture & Attribution

- **Engine**: SIDQ Commerce Engine (Enterprise Edition)
- **Built for**: **SIDQ MART**
- **Developed by**: **SIDQ Technology (সিদিক টেকনোলজি)**
- **Intellectual Property**: Core architecture, UI design tokens, and components engineered by SIDQ Technology.
