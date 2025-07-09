# Einspot Solutions - Laravel Rebuild

This project is a rebuild of the Einspot Solutions web application using the Laravel Framework (version 11.x) with MySQL, targeting compatibility with shared hosting environments (cPanel/Hostinger) and local development via Laragon. The goal is to retain all functionality and features from the original MERN stack application.

## Core Stack

*   **Framework:** Laravel 11.x (PHP 8.2+)
*   **Database:** MySQL 5.7+ / MariaDB
*   **Frontend Templating:** Blade (with Tailwind CSS utility classes)
*   **Authentication:** Manually implemented (mimicking Laravel Breeze structure) due to sandbox limitations with `artisan` commands.
*   **Storage:** Public storage with symbolic link for uploads (products, services, projects, blogs, settings).
*   **Quote Requests:** WhatsApp pre-filled messages + Email notifications to admin + Database logging.
*   **Hosting Target:** Shared Hosting (cPanel, Hostinger), Laragon (local).

## Key Features Implemented (or Structure In Place)

### User-Facing Website
*   **Home Page:** Hero section, Services highlights, Product categories preview, Latest projects grid, Blog section preview, WhatsApp CTA.
*   **About Page:** Company information, mission, vision, team (placeholders).
*   **Services Page:** Dynamic listing of services, individual service detail page.
*   **Product Catalog Page:** Listing of products with category/tag filtering, search.
*   **Individual Product Page:** Detailed product view, image gallery, PDF manual download link, related products.
*   **Project Portfolio Page:** Listing of projects, individual project detail page.
*   **Blog:** Articles by category/tag, individual blog post page.
*   **Contact/Support Page:** Contact form, contact information, map placeholder.
*   **Quote Request System:**
    *   WhatsApp pre-filled messages on product and service pages.
    *   Quote requests saved to database.
    *   Admin email notification on new quote request.
*   **Newsletter Subscription:** Form and backend logic.

### Admin Section (Backend Logic & Routes in Place, UI Pending)
*   **Authentication:** Admin login (utilizes main user login with `isAdmin` flag).
*   **Dashboard (Data Fetching Implemented):**
    *   Welcome message.
    *   Analytics placeholders for: Pending Orders, Order Returns (schema/logic for returns TBD), Confirmed Orders, Cancelled Orders, Orders Shipped, Orders Delivered, Total Orders, Total Customers, Total Quote Requests.
    *   Top Category By Sales (with time filters).
    *   Latest Products table.
    *   Recent Orders table.
*   **Content Management (CRUD backend logic in controllers):**
    *   Products (with images, PDF manuals, categories, tags, SEO fields).
    *   Categories (for Products, Blogs).
    *   Tags.
    *   Services (with icons, images).
    *   Projects (with images, client, location, status, timeline).
    *   Blog Posts (WYSIWYG content expected, SEO fields).
*   **Request Management:**
    *   View Quote Requests.
    *   View Contact Submissions.
*   **Settings Management:**
    *   Website Contact Info, Social Media Links, WhatsApp number, Logo upload, Hero slider content (backend logic for storage exists).
*   **Activity Logs:** Backend logging for most admin CRUD actions.

### SEO & Technical
*   Auto slug generation for relevant models.
*   Meta fields (title, description, keywords) for Products, Blogs, Projects.
*   Canonical URLs on detail pages.
*   `robots.txt` configured.
*   Dynamic `sitemap.xml` generation.

## Setup Instructions

### 1. Local Development (Laragon or similar)

Laragon is a recommended local development environment for Windows. Similar environments like XAMPP, MAMP (macOS), or Docker can also be used.

1.  **Clone the Repository:**
    ```bash
    git clone [repository-url] einspot-laravel
    cd einspot-laravel
    ```

2.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```

3.  **Environment Configuration:**
    *   Copy `.env.example` to `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Generate a new application key:
        ```bash
        php artisan key:generate
        ```
    *   Configure your database connection in `.env`. For Laragon, typical settings might be:
        ```ini
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=einspot_db  # Or your preferred DB name
        DB_USERNAME=root        # Default Laragon DB username
        DB_PASSWORD=           # Default Laragon DB password (empty)
        ```
    *   Create the database (e.g., `einspot_db`) in phpMyAdmin or HeidiSQL (tools included with Laragon).
    *   Update `APP_URL` to your local development URL (e.g., `http://einspot-laravel.test`).
    *   Configure `MAIL_MAILER` and other mail settings if you want to test email sending (e.g., use `log` driver for development, or Mailtrap).

4.  **Database Migration:**
    *   The migration files are located in `database/migrations/`.
    *   Run migrations:
        ```bash
        php artisan migrate
        ```
    *   **Alternative (if `artisan migrate` fails due to sandbox/environment issues):** The SQL DDL statements for all tables have been provided during the development process. You can execute these manually via phpMyAdmin or a similar tool to set up your database schema.

5.  **Storage Link:**
    *   Create the symbolic link for file uploads:
        ```bash
        php artisan storage:link
        ```
    *   This links `public/storage` to `storage/app/public`.

6.  **Frontend Assets (if applicable):**
    *   This project primarily uses Blade with Tailwind CSS. Ensure Node.js and npm are installed.
    *   Install frontend dependencies:
        ```bash
        npm install
        ```
    *   Compile frontend assets:
        ```bash
        npm run dev
        ```
    *   For production build (when deploying): `npm run build`.

7.  **Serve the Application:**
    *   Laragon automatically creates virtual hosts. You should be able to access the site via the URL set in `APP_URL` (e.g., `http://einspot-laravel.test`).
    *   Alternatively, use Laravel's built-in server (not recommended for full Laragon setup): `php artisan serve`.

### 2. Shared Hosting Deployment (cPanel / Hostinger)

1.  **Prerequisites:**
    *   PHP 8.2+ enabled on your hosting account.
    *   MySQL database created with a user and password. Note these credentials.
    *   SSH access is highly recommended for `composer` and `artisan` commands. If not available, deployment is more manual.

2.  **Upload Files:**
    *   Zip your entire Laravel project directory (excluding `node_modules` and ideally `vendor` if you can run composer on the server).
    *   Upload the ZIP file to your hosting account (e.g., into `public_html` or a subdirectory).
    *   Extract the files on the server.

3.  **Document Root:**
    *   Configure your domain or subdomain's document root to point to the `public` directory of your Laravel installation.
    *   Example: If you uploaded to `/home/youruser/einspot-app/`, the document root should be `/home/youruser/einspot-app/public/`.

4.  **Install PHP Dependencies (Composer):**
    *   **With SSH:** Navigate to your project root and run:
        ```bash
        composer install --optimize-autoloader --no-dev
        ```
    *   **Without SSH:** You may need to upload your local `vendor` directory. Ensure it was generated with the correct PHP version and without dev dependencies. This is less ideal.

5.  **Environment Configuration (`.env` file):**
    *   Copy `.env.example` to `.env` on the server.
    *   **Generate `APP_KEY`:**
        *   With SSH: `php artisan key:generate`
        *   Without SSH: Generate a key locally (e.g., using an online tool or your local Laravel `php artisan key:generate`) and paste the `base64:...` string into `APP_KEY` in your server's `.env` file.
    *   Update `APP_URL` to your live domain (e.g., `https://www.yourdomain.com`).
    *   Set `APP_ENV=production` and `APP_DEBUG=false`.
    *   Configure your database credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    *   Configure your `MAIL_MAILER` settings (e.g., `smtp`) and all `MAIL_*` variables for your email provider.
    *   Update `ADMIN_NOTIFICATION_EMAIL` to the email address that should receive admin notifications.

6.  **Database Migration:**
    *   **With SSH:** Run `php artisan migrate --force` (the `--force` flag is important for production).
    *   **Without SSH / Manual SQL:** Use phpMyAdmin or your hosting panel's database tool to import the SQL DDL statements provided during development to create all tables.

7.  **Storage Link:**
    *   **With SSH:** Run `php artisan storage:link`.
    *   **Without SSH:**
        *   Check if your cPanel/hosting panel has a tool to create symbolic links. Link `public/storage` to `../storage/app/public` (relative path from within the `public` directory).
        *   If symlinks are not possible, you might need to change the `public` disk root in `config/filesystems.php` to point directly to a directory within your public web root (e.g., `public_path('uploads')`) and update all file upload paths in controllers. This is less standard.

8.  **Permissions:**
    *   Ensure the `storage` directory (and its subdirectories like `framework/sessions`, `framework/views`, `framework/cache`, `logs`) and the `bootstrap/cache` directory are writable by the web server (often 775 or 755 permissions, check host recommendations).

9.  **Optimize (Highly Recommended - with SSH):**
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
    ```
    If you make changes to `.env` or config files after caching, you'll need to run `php artisan optimize:clear`.

10. **Cron Job (for scheduled tasks):**
    *   If your application uses Laravel's scheduler (this one doesn't explicitly yet, but it's good practice), set up a cron job in your hosting panel:
        ```
        * * * * * cd /path/to/your/laravel-project && php artisan schedule:run >> /dev/null 2>&1
        ```

## Key Areas for Further Development & Refinement

This project provides a solid foundation. However, several areas require further development to reach full functionality and polish:

1.  **User-Facing Frontend - Data Integration & Styling:**
    *   **Controller Logic:** Update all public-facing controller methods (e.g., in `PageController`, `ProductController`, `BlogController`, etc.) to fetch actual data from the database using Eloquent models and pass it to their respective Blade views. Currently, many have placeholder data or commented-out fetching logic.
    *   **Blade View Population:** Populate all Blade views (`home`, `about`, `products`, `services`, `projects`, `blog` index/show pages) with the dynamic data from controllers. Replace all mock data and placeholder text.
    *   **Styling:** Complete Tailwind CSS (or Bootstrap 5, if chosen) styling for all pages and components to match the specified UI guidelines (Red+White theme, Poppins/Inter font, specific layouts like Bento Grid for projects, card grids, etc.). Ensure full responsiveness.
    *   **Interactive Elements:** Implement JavaScript for mobile menu functionality, image galleries (e.g., product page), and any other dynamic UI interactions.
    *   **Forms:** Ensure frontend forms (Contact, Quote Request, Newsletter) are fully functional with proper validation feedback and success/error messages (e.g., using session flash messages).

2.  **Admin Dashboard UI & Functionality (Step 5):**
    *   **Admin Layout:** Create a dedicated admin layout (`layouts/admin.blade.php`) with navigation specific to admin tasks.
    *   **Dashboard View:** Build `admin/dashboard.blade.php` to display the analytics data fetched by `AdminDashboardController`. This includes:
        *   Welcome message ("Hi Admin! [Profile Icon]...").
        *   Displaying stats (Total Orders, Customers, etc.).
        *   Integrating a JavaScript charting library (e.g., Chart.js, ApexCharts) to visualize data for "Pending orders, Order Return, Confirmed Order," etc., and "Top Category By Sales." Backend endpoints might be needed if charts fetch data via AJAX, or data can be passed directly from controller.
        *   Tables for "Latest Products" and "Recent Orders."
    *   **CRUD Interfaces:** Implement Blade views (index, create, edit forms) for managing:
        *   Products (including image uploads, PDF manual uploads, tag/category management).
        *   Categories.
        *   Tags.
        *   Services (including icon/image uploads).
        *   Projects (including image uploads).
        *   Blog Posts (including image uploads, WYSIWYG editor for content).
        *   Orders (view details, update status).
        *   Users (view list, manage roles - requires `isAdmin` flag handling).
        *   Quote Requests (view details, update status).
        *   Contact Submissions (view details).
        *   Newsletter Subscriptions (view list, toggle active status).
    *   **Settings Management UI:** Create the interface for `admin/settings` to manage website settings (contact info, social links, logo, hero slider content).
    *   **Activity Log Viewer:** An admin interface to view the `activity_logs` table with filtering/pagination.
    *   **"Order Return" Feature:** This is a new requirement.
        *   **Database:** Decide if this needs a new `order_returns` table or if an 'returned' status on the `orders` table is sufficient. Update migrations/DDL if needed.
        *   **Backend:** Add logic in `OrderController` (or a new `OrderReturnController`) to handle return requests and processing.
        *   **Admin UI:** Interface for admins to manage and view returns.

3.  **Authentication & Authorization:**
    *   **Admin Middleware:** Create and apply an `isAdmin` middleware to all `/admin` routes to ensure only admin users can access them.
    *   **Password Reset:** While `AuthController` has methods, ensure the password reset flow (email sending, token handling, reset form) is fully implemented and tested. The default Laravel migrations include `password_reset_tokens` table, but mailables and views for this flow need to be created if not using a starter kit like Breeze.

4.  **Payment Gateway Integration (Frontend & Testing):**
    *   The backend `server.py` had Flutterwave and Paystack. The Laravel `OrderController` has placeholder methods for verification.
    *   The frontend checkout process needs to be built to integrate with these payment gateways (e.g., redirect to payment page, handle callbacks).
    *   Thoroughly test the payment flow in sandbox/test modes for both gateways.

5.  **Email Notifications:**
    *   Admin notifications for new orders (if different from quotes/contacts).
    *   User notifications (order confirmation, shipping updates, password reset, etc.).
    *   Ensure all Mailables are created, templates are styled, and mail sending is robust (queued jobs recommended for production).

6.  **Error Handling & User Experience:**
    *   Implement comprehensive error pages (404, 500, etc.).
    *   Refine user feedback mechanisms (e.g., toast notifications for success/error messages on AJAX actions).

7.  **Testing (Step 8 - To be integrated):**
    *   **Unit Tests:** For critical business logic in models and services/controllers.
    *   **Feature Tests:** For testing routes, controller actions, and request/response cycles.
    *   **Manual Testing:** Thoroughly test all user flows, admin functionalities, form submissions, and responsiveness.

## Note on Sandbox Environment

During the development in the current sandbox environment, persistent issues were encountered with `php artisan` CLI commands, particularly related to class autoloading after Composer operations and unexpected `git clone` attempts during `php artisan migrate`. These issues necessitated manual creation of migration SQL DDL and a workaround approach for some setup steps. These problems are specific to this sandbox and are not expected in a standard Laravel development or deployment environment like Laragon or a properly configured shared/dedicated server.

---

This README provides a starting point. It should be further refined as the missing development areas are addressed.
The next logical step, as per your direction and to support the admin dashboard, is to flesh out the `OrderController` and `AdminDashboardController` with more complete data fetching and then move to building the Admin Dashboard UI (Step 5).
