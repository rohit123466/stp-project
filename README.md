# GroviX Digital - Client Website Builder / CMS

A PHP + MySQL website + CMS built for **GroviX Digital**, a digital marketing
agency. Public-facing marketing site with dynamic content, plus an admin panel
to manage services, portfolio, testimonials, and incoming leads.

Built as a college summer training project. PHP (procedural), MySQL (mysqli +
prepared statements), Bootstrap 5, vanilla JavaScript. No framework, no
Composer dependencies — runs directly on XAMPP.

---

## Folder Structure

```
stp project/
├── public/          Public-facing website
│   ├── index.php        Homepage (services + testimonials from DB)
│   ├── about.php         About page (static)
│   ├── services.php      Full services grid (from DB)
│   ├── portfolio.php     Full portfolio grid (from DB)
│   └── contact.php       Contact form -> inserts into `leads` table
│
├── admin/            Admin panel (session-protected)
│   ├── login.php             Admin login
│   ├── logout.php            Destroys session
│   ├── dashboard.php         Stats overview
│   ├── manage_services.php   CRUD for services
│   ├── manage_portfolio.php  CRUD for portfolio
│   ├── manage_testimonials.php CRUD for testimonials
│   ├── view_leads.php        View/search/filter leads, update status
│   └── change_password.php   Change the logged-in admin's password
│
├── includes/         Shared PHP includes
│   ├── db.php            Opens the mysqli database connection
│   ├── auth.php          Session guard, included at top of every admin page
│   ├── functions.php     Helper functions (input cleaning, image upload, etc.)
│   ├── header.php         Public site header/nav
│   ├── footer.php         Public site footer
│   ├── admin_header.php   Admin sidebar layout (top half)
│   └── admin_footer.php   Admin sidebar layout (bottom half)
│
├── config/
│   └── config.php     DB credentials + site settings (edit this first)
│
├── uploads/           Uploaded images land here
│   └── .htaccess          Blocks PHP execution inside this folder
│
├── assets/
│   ├── css/style.css   Small custom styles on top of Bootstrap
│   ├── js/main.js       Contact form validation + image preview
│   └── img/placeholder.svg  Fallback image shown when no image is uploaded
│
└── sql/
    ├── schema.sql       CREATE TABLE statements + sample seed data
    └── seed_admin.php   One-time script to set the admin password
```

---

## Setup (XAMPP)

1. **Copy the project into `htdocs`**
   Copy this whole folder into your XAMPP `htdocs` directory, e.g.
   `C:\xampp\htdocs\grovix` (avoid spaces in the folder name).

2. **Start Apache and MySQL** from the XAMPP Control Panel.

3. **Import the database**
   - Open `http://localhost/phpmyadmin`
   - Click **Import**, choose `sql/schema.sql`, and run it.
   - This creates the `opportunex_cms` database with all 5 tables and sample
     data (services, portfolio, testimonials, and leads across different
     pipeline statuses).

4. **Configure `config/config.php`**
   - Update `DB_USER` / `DB_PASS` if your MySQL isn't the default XAMPP
     `root` with no password.
   - Set `BASE_URL` to match the folder name you used in `htdocs`, e.g.
     `http://localhost/grovix`.

5. **Create the admin login**
   Visit `http://localhost/grovix/sql/seed_admin.php` once in your
   browser. It hashes and sets the admin password using your server's own
   PHP install (safer than importing a hard-coded hash from this repo).
   **Delete `sql/seed_admin.php` after running it once.**

6. **Visit the site**
   - Public site: `http://localhost/grovix/public/index.php`
   - Admin panel: `http://localhost/grovix/admin/login.php`

### Default admin login
```
Username: admin
Password: admin123
```
Change this immediately via **Admin > Change Password** after first login.

---

## Security Notes (for viva reference)

- **SQL injection**: every query that includes user input uses `mysqli`
  prepared statements with bound parameters — no string concatenation into
  SQL anywhere in the codebase.
- **Password storage**: admin passwords are hashed with `password_hash()`
  and checked with `password_verify()`. Plain text passwords are never
  stored or logged.
- **Session-based auth**: every admin page (except `login.php`) starts with
  `require_once '../includes/auth.php'`, which redirects to the login page
  if `$_SESSION['admin_id']` isn't set. `session_regenerate_id()` is called
  on login to prevent session fixation.
- **File upload validation**: uploaded images are checked server-side for
  real MIME type (via `finfo`, not just the file extension), file extension,
  and file size (max 2MB) before being moved into `/uploads` with a unique,
  sanitized filename.
- **Upload folder hardening**: `/uploads/.htaccess` blocks execution of
  `.php` files inside the uploads folder, so even if a malicious file slipped
  past validation, the server would refuse to run it as a script.
- **Input sanitization**: all output to HTML is passed through
  `htmlspecialchars()` / `clean_input()` to prevent XSS. All form input is
  validated both client-side (JavaScript, for UX) and server-side (PHP, the
  authoritative check).
- **Credentials separation**: DB credentials live only in
  `config/config.php`, not mixed into page logic.

---

## Notes

- Seed data ships with no real images (`image` columns are `NULL`); pages
  fall back to `assets/img/placeholder.svg` automatically. Upload real
  images through the admin panel to replace them.
- This project intentionally avoids frameworks/ORMs/Composer to keep the
  code simple and fully explainable for a college viva.
