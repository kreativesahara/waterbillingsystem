# Classic Heights Apartments - Water Management & Billing System

A modernized, responsive PHP MVC application for residential water meter management, dynamic tariff calculation, automated SMS billing notifications, and 1-click Lipa na M-Pesa online payments.

---

## 🚀 Quick Start (Instant Zero-Setup Demo)

You can launch the application locally in seconds using PHP's built-in web server:

```bash
# Navigate to the project root and serve the public directory
php -S localhost:8000 -t public
```

Then open your browser at: **`http://localhost:8000`**

### 🔑 Demo Login Credentials

The application comes pre-seeded with sample data and convenient 1-click login buttons on the login screen:

| Role | Phone Number | Password | Assigned Unit | Capabilities |
| :--- | :--- | :--- | :--- | :--- |
| **System Admin** | `0700111222` | `admin123` | Management | Executive dashboard, meter reading console, billing registers, CSV/print export, bulk SMS dispatch |
| **Tenant** | `0712345678` | `tenant123` | House #1 | Mobile-first dashboard, active bill view, 6-month usage chart, M-Pesa 1-click checkout, PDF statement |
| **Tenant** | `0722334455` | `tenant123` | House #2 | Alternative tenant with paid balance |

---

## 🌟 Key Architecture & UI/UX Modernizations

### 1. Modern PHP MVC Architecture
- **Directory Isolation**: Sensitive application files (`app/`, `database/`) are shielded outside the public web root (`public/`).
- **Clean Routing**: Front controller (`public/index.php`) with expressive route handling and role-based middleware (`['admin']`, `['tenant']`).
- **Normalized Relational Schema**: Clean separation of `properties`, `houses`, `consumers`, `meter_readings`, `invoices`, and `payments`.
- **Database Flexibility**: Default self-contained SQLite for zero-setup execution, with instant toggle to MySQL/MariaDB in `app/config.php` and production schema in `database/schema.sql`.

### 2. Hardened Security
- **Secure Password Hashing**: Passwords stored using `password_hash()` (Bcrypt with cost factor 10) instead of legacy plaintext.
- **PDO Prepared Statements**: Eliminates SQL injection vulnerabilities across all queries.
- **Session Protection**: Automatic session regeneration on login to prevent session fixation attacks.

### 3. Tailwind CSS Component Design System
- **Modern Color Palette**: Sleek ocean slate (`#0f172a`, `#0284c7`, `#10b981`) replacing outdated clashing colors.
- **Smart Meter Reading Console**: Real-time keystroke calculations ($Units = Current - Previous$). Dynamically retrieves previous dial and uncollected arrears from the database (no hardcoded readings or balances).
- **Anomaly Detection**: Highlights negative consumption errors and flags unusually high water usage alerts.
- **Responsive Mobile-First Tenant Portal**: Card-based interface with due date countdown, 6-month historical consumption bar chart, and printable statement.

### 4. Financial & SMS Integrations
- **Lipa na M-Pesa Online (STK Push)**: Interactive mobile checkout workflow that prompts the handset, verifies payment, auto-reconciles the invoice in the database, and generates an official receipt code (`QRT...`).
- **SMS Dispatch Service**: Automated notification engine with templates for bill dispatch and payment receipts, logged in the `notifications` table.

---

## 📁 Project Structure

```plaintext
waterbillingsystem/
├── app/
│   ├── config.php            # Global system, database, tariff, and gateway configuration
│   ├── Controllers/
│   │   ├── AuthController.php    # Login, registration, session management
│   │   ├── AdminController.php   # Executive KPI dashboard, meter entry, records table
│   │   ├── TenantController.php  # Mobile-first bill summary, usage charts, statements
│   │   └── ApiController.php     # REST API for live calculations, M-Pesa, and SMS
│   ├── Core/
│   │   ├── Database.php          # PDO singleton with auto-migration and seed engine
│   │   ├── Router.php            # Clean route dispatcher and middleware guard
│   │   ├── Auth.php              # Session and RBAC authentication helper
│   │   └── Response.php          # View layout renderer and JSON helper
│   ├── Services/
│   │   ├── BillingService.php    # Meter calculation logic and invoice generation
│   │   ├── MPesaService.php      # Daraja STK push simulation and transaction ledger
│   │   └── SmsService.php        # Bill SMS notification dispatcher
│   └── Views/
│       ├── layouts/              # Main responsive layout and Auth split-card layout
│       ├── auth/                 # Tabbed login and tenant registration forms
│       ├── admin/                # Dashboard, meter entry console, billing records, tenants
│       └── tenant/               # Mobile bill view, consumption chart, printable invoice
├── database/
│   ├── schema.sql                # Production MySQL schema
│   └── waterbillingsystem.sqlite # Zero-setup local SQLite database
├── public/
│   ├── index.php                 # Front controller entry point
│   ├── .htaccess                 # Apache rewrite rules
│   ├── css/                      # Custom styles (if needed beyond Tailwind CDN)
│   └── js/                       # Client assets
├── index.php                     # Root wrapper
└── README.md
```

---

&copy; Lensmen Technologies. Classic Heights Apartments Water Management System.
