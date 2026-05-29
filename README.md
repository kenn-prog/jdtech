# JDTech — Full Stack Website

> **A beginner-friendly, production-ready PHP + MySQL e-commerce website.**  
> Built with HTML, CSS, JavaScript, PHP, and MySQL.

---

## Quick Start

```bash
# 1. Clone or copy the project into your web server folder
#    For XAMPP: C:/xampp/htdocs/jdtech
#    For Laragon: C:/laragon/www/jdtech

# 2. Import the database
#    Open phpMyAdmin → Import → select database/project.sql
#    Then import database/seed.sql for sample data

# 3. Configure the app
#    Copy .env and fill in your DB credentials:
cp .env .env.local
# Edit DB_HOST, DB_USER, DB_PASS in includes/config.php if needed

# 4. Rename the htaccess file
mv htaccess .htaccess

# 5. Visit the site
http://localhost/jdtech
```

**Demo accounts (from seed.sql):**
| Role  | Email                | Password   |
|-------|----------------------|------------|
| Admin | admin@jdtech.com     | admin123   |
| User  | user@jdtech.com      | user123    |

---

## Project Structure

```
jdtech/
│
├── index.php           ← Homepage (hero, features, products, team)
├── about.php           ← About Us page
├── contact.php         ← Contact form & store info
├── products.php        ← Full product catalog with filters
├── cart.php            ← Shopping cart + checkout
├── login.php           ← Login form
├── register.php        ← Registration form
├── dashboard.php       ← User dashboard (orders, profile summary)
├── profile.php         ← Edit profile, delete account
├── settings.php        ← Change password, account info
├── 404.php             ← Custom error page
│
├── assets/
│   ├── css/
│   │   ├── style.css        ← Main stylesheet (design system)
│   │   ├── responsive.css   ← Media queries, auth/dashboard/admin styles
│   │   └── admin.css        ← Admin-panel-only styles
│   │
│   ├── js/
│   │   ├── script.js        ← Global UI: cart badge, toast, mobile menu
│   │   ├── validation.js    ← Client-side form validation helpers
│   │   └── ajax.js          ← Reusable fetch/AJAX utility functions
│   │
│   ├── images/              ← Product and hero images
│   ├── icons/               ← Custom SVG icons
│   └── fonts/               ← Self-hosted web fonts (if any)
│
├── includes/
│   ├── config.php      ← App settings: DB, upload limits, env vars
│   ├── db.php          ← MySQL connection + query helpers
│   ├── session.php     ← Session start + session helper functions
│   ├── auth.php        ← Login/admin guards: requireLogin(), requireAdmin()
│   ├── functions.php   ← Shared utilities: h(), redirect(), uploadFile()
│   ├── header.php      ← HTML <head> with CSS links
│   ├── navbar.php      ← Top navigation bar (desktop + mobile)
│   ├── sidebar.php     ← Admin panel sidebar
│   └── footer.php      ← HTML footer with JS script tags
│
├── backend/
│   ├── login-process.php    ← Verifies email/password, creates session
│   ├── register-process.php ← Validates & saves new user, creates session
│   ├── logout.php           ← Destroys session, returns JSON
│   ├── upload.php           ← Handles profile/product file uploads
│   ├── update-profile.php   ← Updates user name/phone in DB
│   ├── change-password.php  ← Verifies old password, saves new hash
│   ├── delete-account.php   ← Confirms password, deletes user row
│   ├── add-product.php      ← Admin: inserts new product (with image)
│   ├── edit-product.php     ← Admin: updates existing product
│   └── delete-product.php   ← Admin: removes product from DB
│
├── admin/
│   ├── index.php       ← Dashboard: stats, recent orders, quick actions
│   ├── products.php    ← CRUD for all products (add/edit/delete modal)
│   ├── orders.php      ← View all orders, update delivery status
│   ├── users.php       ← View registered users, delete accounts
│   └── settings.php    ← Edit homepage content (hero, team, store info)
│
├── api/
│   ├── products.php    ← GET/POST: products, homepage, orders
│   ├── users.php       ← Admin: get users, update order status
│   └── auth.php        ← Check session, logout via JSON
│
├── uploads/
│   ├── profile/        ← User avatar images
│   ├── products/       ← Product images
│   └── documents/      ← General document uploads
│
├── database/
│   ├── project.sql     ← Creates all tables (run first)
│   └── seed.sql        ← Inserts demo data (run second)
│
├── logs/
│   └── error.log       ← PHP errors (production mode only)
│
├── vendor/             ← Composer packages (e.g. PHPMailer)
│
├── .env                ← Database credentials (never commit this!)
├── .gitignore          ← Tells Git to ignore .env, uploads, logs
├── htaccess            ← Rename to .htaccess — Apache security rules
├── composer.json       ← PHP package manager config
├── README.md           ← This file
└── LICENSE             ← MIT open source license
```

---

## How Each Piece Works

### How Frontend Connects to Backend

```
User clicks "Add to Cart"
    ↓
JavaScript (onclick) runs addToCart()
    ↓
JS saves item to sessionStorage (no server needed)
    ↓
User clicks "Checkout"
    ↓
JS calls: fetch('api/products.php?action=add_order', { method: 'POST', body: formData })
    ↓
PHP receives data in $_POST, validates it
    ↓
PHP runs INSERT query via mysqli
    ↓
PHP returns: { "ok": true, "order_id": 42 }
    ↓
JS reads the JSON and updates the page (no reload!)
```

This pattern is called **AJAX** (Asynchronous JavaScript And XML).  
Modern code uses `fetch()` and JSON instead of XML, but the concept is the same.

---

### How PHP Connects to MySQL

```php
// Step 1: Open a connection
$conn = mysqli_connect('127.0.0.1', 'root', '', 'jdtech');

// Step 2: Run a query
$result = mysqli_query($conn, "SELECT * FROM items");

// Step 3: Read the rows
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'];
}

// Step 4: PHP auto-closes when the script ends
```

In this project, `includes/db.php` handles the connection once, and  
helper functions like `fetchAll()`, `fetchOne()`, and `runQuery()` make  
it easy to talk to the database from any page.

---

### Authentication Flow

```
1. User fills in login.php form (email + password)
         ↓
2. JS sends POST to backend/login-process.php via fetch()
         ↓
3. PHP looks up the email in the users (or admin) table
         ↓
4. password_verify($submitted, $storedHash) checks the password
         ↓
   ✅ Match → $_SESSION['user'] = [...user data...]
              session_regenerate_id(true)  ← prevents session fixation
              Return: { ok: true, user: {...} }
   ❌ No match → Return: { ok: false, msg: "Invalid email or password." }
         ↓
5. JS redirects to dashboard.php (user) or admin/index.php (admin)
         ↓
6. Every protected page starts with requireLogin() or requireAdmin()
   which checks $_SESSION['user'] — redirect if missing
```

**Why we use `password_hash()` and `password_verify()`:**  
- Never store plain-text passwords. If your database is ever leaked,  
  hashed passwords are useless to attackers.  
- PHP's `password_hash()` uses bcrypt automatically. The cost factor  
  (set in config.php as `BCRYPT_COST`) controls how slow hashing is —  
  slower = harder for attackers to crack.

---

### Session Handling

```
Browser                          PHP Server
  │                                   │
  │── GET /dashboard.php ────────────>│
  │                                   │  session_start()
  │                                   │  Reads COOKIE: jdtech_sess=abc123
  │                                   │  Loads $_SESSION from server storage
  │                                   │
  │                                   │  if empty($_SESSION['user'])
  │                                   │      redirect to login.php
  │                                   │  else
  │<── HTML: dashboard content ───────│      render the page
  │                                   │
```

Key session rules in this project:
- `session_start()` is called once in `includes/session.php`
- Session cookie is `HttpOnly` (JS can't steal it via XSS)
- `session_regenerate_id(true)` runs on login (prevents session fixation)
- `destroySession()` clears everything on logout

---

### File Upload Flow

```
1. HTML form must have:
   <form method="POST" enctype="multipart/form-data">
   <input type="file" name="image" />

2. User selects a file and submits

3. PHP receives it in $_FILES['image']:
   $_FILES['image']['name']     = "photo.jpg"
   $_FILES['image']['size']     = 204800  (bytes)
   $_FILES['image']['tmp_name'] = "/tmp/php7F3gHx"
   $_FILES['image']['error']    = 0  (0 = no error)

4. Validation (uploadFile() in functions.php):
   ✅ Error code === 0?
   ✅ Size < 5MB?
   ✅ Extension in [jpg, png, gif, webp]?
   ✅ getimagesize() confirms it's actually an image?

5. Generate safe filename:
   uniqid('img_', true) . '.jpg'
   → "img_6632a1b4d2f3a.jpg"  (prevents overwriting)

6. move_uploaded_file(tmp_path, uploads/products/img_xxx.jpg)

7. Store relative path in database:
   "uploads/products/img_xxx.jpg"

8. Display it:
   <img src="<?= APP_URL ?>/uploads/products/img_xxx.jpg">
```

---

### Admin Panel Structure

```
/admin/                     ← All files here require requireAdmin()
│
├── index.php               ← Stats dashboard (products, users, orders, revenue)
├── products.php            ← Full product CRUD with modal form
│                              Uses: backend/add-product.php
│                                    backend/edit-product.php
│                                    backend/delete-product.php
│
├── orders.php              ← Table of all orders with status dropdowns
│                              Uses: api/users.php?action=update_order_status
│
├── users.php               ← Table of all registered users
│                              Uses: api/users.php?action=get_users
│                                    api/users.php?action=delete_user
│
└── settings.php            ← Edit homepage: hero, about, team, contact info
                               Directly updates the `homepage` table via POST form
```

Every admin page:
1. Starts with `requireAdmin()` — non-admins are kicked out immediately
2. Includes `sidebar.php` for the left navigation
3. Loads data via the API using `fetch()` for dynamic content
4. Sends mutations (add/edit/delete) to `/backend/` via `fetch()` POST

---

### API Folder Purpose

The `/api/` folder contains **JSON-only endpoints** that your JavaScript  
calls using `fetch()`. Think of them as a mini REST API.

```
Frontend (JavaScript)          API (PHP)              Database (MySQL)
         │                         │                         │
         │─ fetch('api/products    │                         │
         │    .php?action=         │                         │
         │    get_products') ─────>│                         │
         │                         │─ SELECT * FROM items ──>│
         │                         │<─ [row, row, row] ──────│
         │<─ { ok: true,           │                         │
         │     products: [...] } ──│                         │
         │                         │                         │
         │  JS renders the HTML    │                         │
         │  without page reload    │                         │
```

Rules for API files:
- Always set `header('Content-Type: application/json')`
- Always return a JSON object with an `ok` key (`true`/`false`)
- Use `jsonResponse()` helper from `functions.php`
- Use `requireLoginAPI()` or `requireAdminAPI()` instead of redirect-based guards

---

### Security Best Practices

| Threat | How We Handle It |
|--------|-----------------|
| **SQL Injection** | `escape()` wraps all user inputs before SQL; use prepared statements for extra safety |
| **XSS (Cross-Site Scripting)** | `h()` (htmlspecialchars) wraps all user output in HTML |
| **Password Theft** | `password_hash()` with bcrypt — plain passwords never stored |
| **Session Fixation** | `session_regenerate_id(true)` runs after every login |
| **CSRF** | `getCsrfToken()` / `verifyCsrf()` helpers available in functions.php |
| **Unauthorized Access** | `requireLogin()` / `requireAdmin()` guards on every protected page |
| **File Upload Attacks** | Extension whitelist + `getimagesize()` verify real images only |
| **Directory Listing** | `Options -Indexes` in .htaccess prevents browsing folders |
| **Sensitive File Exposure** | .htaccess blocks access to `.env`, `logs/`, `database/`, `includes/` |
| **Clickjacking** | `X-Frame-Options: SAMEORIGIN` header via .htaccess |
| **MIME Sniffing** | `X-Content-Type-Options: nosniff` header |

---

### Beginner Best Practices

1. **Work locally first** — Use XAMPP or Laragon. Never develop on a live server.

2. **Keep credentials in `.env`** — Never hardcode passwords in PHP files.

3. **Always validate twice** — Client-side JS for UX, server-side PHP for security.

4. **Use `h()` on all output** — Any `echo $_POST['name']` without `h()` is an XSS hole.

5. **Never trust `$_FILES['type']`** — Use `getimagesize()` to verify real images.

6. **Check `$_SERVER['REQUEST_METHOD']`** — Backend files should reject GET requests.

7. **Use `requireLogin()` at the top** — Don't rely on hiding links to protect pages.

8. **Commit often, commit small** — One feature per commit makes bugs easier to find.

9. **Read error messages** — PHP errors tell you exactly what went wrong and where.

10. **Use phpMyAdmin** — Visually check your database to understand what's being saved.

---

### Recommended Development Workflow

```
Week 1 — Setup
  ✅ Install XAMPP / Laragon
  ✅ Import project.sql and seed.sql
  ✅ Visit localhost/jdtech — confirm homepage loads
  ✅ Login with admin@jdtech.com / admin123

Week 2 — Database & CRUD
  ✅ Add real products via Admin → Products
  ✅ Update homepage content via Admin → Settings
  ✅ Test registering a new user account

Week 3 — Frontend Polish
  ✅ Edit assets/css/style.css to match your brand colors
  ✅ Add real product images to uploads/products/
  ✅ Update about.php with real store information

Week 4 — Backend Features
  ✅ Test the full checkout flow (add to cart → order)
  ✅ Change order statuses in Admin → Orders
  ✅ Test profile update and password change

Week 5 — Production Prep
  ✅ Set APP_ENV=production in config.php
  ✅ Enable HTTPS (uncomment in .htaccess)
  ✅ Set real DB credentials in .env
  ✅ Upload to a hosting provider (Hostinger, Bluehost, etc.)
  ✅ Test everything on the live server
```

---

## Tech Stack

| Layer      | Technology        | Purpose                        |
|------------|-------------------|--------------------------------|
| Frontend   | HTML5             | Page structure and content     |
| Frontend   | CSS3              | Styling and responsive layout  |
| Frontend   | JavaScript (ES6+) | UI interactions and AJAX calls |
| Backend    | PHP 8+            | Server logic and API endpoints |
| Database   | MySQL 8+          | Data storage and retrieval     |
| Server     | Apache (XAMPP)    | Local development web server   |
| Packages   | Composer          | PHP dependency manager         |

---

## Folder Permissions (for Linux/production servers)

```bash
# Uploads folder must be writable by the web server
chmod 755 uploads/
chmod 755 uploads/profile/
chmod 755 uploads/products/
chmod 755 uploads/documents/

# Logs folder must be writable
chmod 755 logs/

# Everything else should be read-only for the web server
chmod 644 *.php
```

---

## License

MIT License — see `LICENSE` file for details.  
Free to use, modify, and distribute.

---

*Built with ❤️ for JDTech — Makati City, Philippines*
