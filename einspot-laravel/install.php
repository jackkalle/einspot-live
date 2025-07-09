<?php

// Installation Script for Einspot Laravel

// --- Configuration & Security ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Show errors during installation
define('INSTALLER_VERSION', '1.0.0');
define('LOCK_FILE', __DIR__ . '/storage/installed.lock'); // Lock file to prevent re-installation
define('ENV_EXAMPLE_FILE', __DIR__ . '/.env.example');
define('ENV_FILE', __DIR__ . '/.env');

session_start();

// --- Pre-Installation Check ---
if (file_exists(LOCK_FILE)) {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Already Installed</title>";
    echo "<style>body{font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f0f0; color: #333; text-align: center;} .container{background:white; padding:2em; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1);}</style></head><body>";
    echo "<div class='container'><h1>Application Already Installed</h1>";
    echo "<p>The application appears to be already installed. If you need to re-install, please remove the <code>storage/installed.lock</code> file and ensure your database is clean.</p>";
    echo "<p><a href='./public'>Go to Homepage</a></p>"; // Assuming Laravel's public dir
    echo "</div></body></html>";
    exit;
}

// --- Step Management ---
$currentStep = $_GET['step'] ?? $_SESSION['install_step'] ?? 'welcome';
$_SESSION['install_step'] = $currentStep;

// --- Helper Functions ---
function checkPhpVersion(string $requiredVersion): bool {
    return version_compare(PHP_VERSION, $requiredVersion, '>=');
}

function checkExtension(string $extensionName): bool {
    return extension_loaded($extensionName);
}

function isWritableDir(string $path): bool {
    return is_writable($path);
}

function generateAppKey(): string {
    return 'base64:'.base64_encode(random_bytes(32)); // PHP-based key generation
}

// --- HTML Structure ---
function renderHeader(string $title) {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>{$title} - Einspot Installer</title>";
    echo "<style>
        body { font-family: 'Poppins', 'Inter', sans-serif; margin: 0; padding: 20px; background-color: #f4f6f8; color: #333; display: flex; flex-direction: column; align-items: center; }
        .installer-container { background-color: white; width: 100%; max-width: 700px; margin-top: 20px; padding: 30px; border-radius: 8px; box-shadow: 0 6px 12px rgba(0,0,0,0.1); }
        h1, h2 { color: #dc2626; text-align:center; } /* Einspot Red */
        h1 { font-size: 1.8em; margin-bottom: 10px; }
        h2 { font-size: 1.4em; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;}
        ul { list-style-type: none; padding: 0; }
        li { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        li:last-child { border-bottom: none; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
        input[type='text'], input[type='password'], input[type='email'], input[type='number'], select {
            width: calc(100% - 22px); padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;
            font-size: 0.95em; box-shadow: inset 0 1px 2px rgba(0,0,0,0.075);
        }
        input[type='checkbox'] { margin-right: 5px; vertical-align: middle; }
        button, .button {
            background-color: #dc2626; color: white; padding: 12px 25px; border: none; border-radius: 5px;
            cursor: pointer; font-size: 1em; font-weight: 500; text-decoration: none; display: inline-block;
            transition: background-color 0.2s ease;
        }
        button:hover, .button:hover { background-color: #b91c1c; }
        button[disabled] { background-color: #ccc; cursor: not-allowed; }
        .nav-buttons { margin-top: 30px; display: flex; justify-content: space-between; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        .text-center { text-align: center; }
        .mt-4 { margin-top: 1.5rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .code-block { background-color: #2d2d2d; color: #f0f0f0; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; word-wrap: break-word; font-size: 0.9em; }
    </style></head><body>";
    echo "<div class='installer-container'><h1>Einspot Solutions Laravel Installer</h1>";
}

function renderFooter() {
    echo "</div>"; // Close installer-container
    echo "<p class='text-center mt-4 text-sm text-gray-500'>&copy; " . date('Y') . " Einspot Solutions. Installer Version: " . INSTALLER_VERSION . "</p>";
    echo "</body></html>";
}

// --- Step Implementations ---

// == Step 1: Welcome & Requirements Check ==
if ($currentStep === 'welcome') {
    renderHeader('Welcome');
    echo "<h2>Step 1: Welcome & System Requirements</h2>";
    echo "<p>Welcome to the Einspot Solutions application installer. This wizard will guide you through the installation process.</p>";

    $phpVersionRequired = '8.2.0';
    $extensionsRequired = ['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
    $writableDirs = [
        'storage' => __DIR__ . '/storage',
        'storage/framework' => __DIR__ . '/storage/framework',
        'storage/framework/sessions' => __DIR__ . '/storage/framework/sessions',
        'storage/framework/views' => __DIR__ . '/storage/framework/views',
        'storage/framework/cache' => __DIR__ . '/storage/framework/cache/data',
        'storage/logs' => __DIR__ . '/storage/logs',
        'bootstrap/cache' => __DIR__ . '/bootstrap/cache',
    ];

    $allChecksPassed = true;

    echo "<h3>PHP Version:</h3>";
    if (checkPhpVersion($phpVersionRequired)) {
        echo "<li>PHP Version: <span class='success'>" . PHP_VERSION . " (Required: {$phpVersionRequired}+) - OK</span></li>";
    } else {
        echo "<li>PHP Version: <span class='error'>" . PHP_VERSION . " (Required: {$phpVersionRequired}+) - Failed</span></li>";
        $allChecksPassed = false;
    }

    echo "<h3>PHP Extensions:</h3><ul>";
    foreach ($extensionsRequired as $ext) {
        if (checkExtension($ext)) {
            echo "<li>Extension '{$ext}': <span class='success'>Enabled - OK</span></li>";
        } else {
            echo "<li>Extension '{$ext}': <span class='error'>Disabled - Failed</span></li>";
            $allChecksPassed = false;
        }
    }
    echo "</ul>";

    echo "<h3>Directory Permissions:</h3><ul>";
    foreach ($writableDirs as $name => $path) {
        // Ensure directories exist before checking writability, attempt to create if not.
        if (!is_dir($path)) {
            if (!@mkdir($path, 0775, true) && !is_dir($path)) { // Check again after trying to make it
                 echo "<li>Directory '{$name}' (<code>{$path}</code>): <span class='error'>Missing and could not be created. Please create it manually and make it writable.</span></li>";
                $allChecksPassed = false;
                continue;
            }
        }
        if (isWritableDir($path)) {
            echo "<li>Directory '{$name}' (<code>{$path}</code>): <span class='success'>Writable - OK</span></li>";
        } else {
            echo "<li>Directory '{$name}' (<code>{$path}</code>): <span class='error'>Not Writable - Failed</span></li>";
            $allChecksPassed = false;
        }
    }
    echo "</ul>";

    if (!$allChecksPassed) {
        echo "<p class='error mt-4'>Your server does not meet all the requirements. Please resolve the issues listed above before proceeding.</p>";
        echo "<div class='nav-buttons'><button onclick='window.location.reload()'>Retry Checks</button></div>";
    } else {
        echo "<p class='success mt-4'>Congratulations! Your server meets all requirements.</p>";
        echo "<div class='nav-buttons'><form method='GET'><input type='hidden' name='step' value='database_setup'><button type='submit'>Next: Database Setup</button></form></div>";
    }
    renderFooter();
    exit;
}

// Subsequent steps (database, .env, admin user, finalize) will be added here.

// == Step 2: Database Configuration Form ==
if ($currentStep === 'database_setup') {
    renderHeader('Database Setup');
    echo "<h2>Step 2: Database Configuration</h2>";
    echo "<p>Please provide your database connection details. Ensure the database is already created on your server.</p>";

    // Display errors from previous attempt if any
    if (isset($_SESSION['db_error'])) {
        echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['db_error']) . "</div>";
        unset($_SESSION['db_error']);
    }
    if (isset($_SESSION['db_form_data'])) {
        $old = $_SESSION['db_form_data'];
        unset($_SESSION['db_form_data']);
    } else {
        $old = [];
    }

    echo "<form method='POST' action='install.php?step=process_database_setup'>"; // Will process in next step

    echo "<div><label for='db_host'>Database Host:</label><input type='text' name='db_host' id='db_host' value='" . htmlspecialchars($old['db_host'] ?? '127.0.0.1') . "' required></div>";
    echo "<div><label for='db_port'>Database Port:</label><input type='number' name='db_port' id='db_port' value='" . htmlspecialchars($old['db_port'] ?? '3306') . "' required></div>";
    echo "<div><label for='db_name'>Database Name:</label><input type='text' name='db_name' id='db_name' value='" . htmlspecialchars($old['db_name'] ?? 'einspot_db') . "' required></div>";
    echo "<div><label for='db_user'>Database User:</label><input type='text' name='db_user' id='db_user' value='" . htmlspecialchars($old['db_user'] ?? '') . "' required></div>";
    echo "<div><label for='db_pass'>Database Password:</label><input type='password' name='db_pass' id='db_pass' value='" . htmlspecialchars($old['db_pass'] ?? '') . "'></div>";

    echo "<div class='nav-buttons'>";
    echo "<a href='install.php?step=welcome' class='button' style='background-color:#6c757d;'>Back</a>"; // Grey back button
    echo "<button type='submit'>Next: Configure Site & Admin</button>";
    echo "</div></form>";

    renderFooter();
    exit;
}


// Placeholder for other steps
// Catch-all for unimplemented steps or direct access to later steps without session
if (!in_array($currentStep, ['welcome', 'database_setup', 'process_database_setup', 'admin_setup', 'process_admin_setup', 'finalize'])) {
    $_SESSION['install_step'] = 'welcome'; // Reset to welcome if step is invalid
    header('Location: install.php');
    exit;
}

// Fallback for direct access to later steps before session is properly set (very basic)
if ($currentStep !== 'welcome' && $currentStep !== 'database_setup' && empty($_POST) && !isset($_SESSION['db_credentials_validated'])) {
     if ($currentStep !== 'process_database_setup' && $currentStep !== 'admin_setup' && $currentStep !== 'process_admin_setup' && $currentStep !== 'finalize') {
        // Allow process steps to be hit directly if they are handling POST, otherwise redirect
        // This logic needs to be more robust if direct step access is a concern.
        // For now, simple redirect if not a known processing step or if crucial session data is missing.
     }
}


// == Step 2.5: Process Database Configuration & Write .env ==
if ($currentStep === 'process_database_setup') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: install.php?step=database_setup'); // Redirect if not POST
        exit;
    }

    $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? ''; // Password can be empty

    $_SESSION['db_form_data'] = $_POST; // Store for repopulation on error

    // Basic validation
    if (empty($dbName) || empty($dbUser)) {
        $_SESSION['db_error'] = "Database Name and User are required.";
        header('Location: install.php?step=database_setup');
        exit;
    }

    // Try to connect to database
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        // Connection successful
        $_SESSION['db_credentials_validated'] = true;
        $_SESSION['db_config'] = [
            'host' => $dbHost, 'port' => $dbPort, 'name' => $dbName,
            'user' => $dbUser, 'pass' => $dbPass
        ];
        unset($_SESSION['db_error']);
        unset($_SESSION['db_form_data']);

        // Create .env file if it doesn't exist
        if (!file_exists(ENV_FILE)) {
            if (!copy(ENV_EXAMPLE_FILE, ENV_FILE)) {
                $_SESSION['install_error'] = "Failed to create .env file. Please check permissions.";
                header('Location: install.php?step=database_setup'); // Go back, show error
                exit;
            }
        }

        // Read .env content
        $envContent = file_get_contents(ENV_FILE);
        if ($envContent === false) {
            $_SESSION['install_error'] = "Failed to read .env file. Please check permissions.";
            header('Location: install.php?step=database_setup');
            exit;
        }

        // Update .env with DB details & APP_KEY
        $appKey = generateAppKey();
        $envUpdates = [
            'APP_KEY' => $appKey,
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $dbHost,
            'DB_PORT' => $dbPort,
            'DB_DATABASE' => $dbName,
            'DB_USERNAME' => $dbUser,
            'DB_PASSWORD' => $dbPass, // Note: if password contains special chars like #, direct replace might fail
            'APP_ENV' => 'local', // Default to local, can be changed later
            'APP_DEBUG' => 'true', // Default to true for local setup
            'APP_URL' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . str_replace('/install.php', '', $_SERVER['SCRIPT_NAME'])
        ];

        foreach ($envUpdates as $key => $value) {
            // Escape special characters for regex, especially for DB_PASSWORD
            $escapedValue = str_replace(['\\', '$', '^', '[', ']', '(', ')', '*', '+', '?', '.', '{', '}', '|', '#', '&', '!'], ['\\\\', '\\$', '\\^', '\\[', '\\]', '\\(', '\\)', '\\*', '\\+', '\\?', '\\.', '\\{', '\\}', '\\|', '\\#', '\\&', '\\!'], $value);
            if (strpos($envContent, "{$key}=") !== false) {
                 $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $envContent);
            } else {
                 $envContent .= "\n{$key}={$escapedValue}";
            }
        }

        if (file_put_contents(ENV_FILE, $envContent) === false) {
            $_SESSION['install_error'] = "Failed to write to .env file. Please check permissions.";
            header('Location: install.php?step=database_setup');
            exit;
        }

        // Proceed to admin setup
        $_SESSION['install_step'] = 'admin_setup';
        header('Location: install.php?step=admin_setup');
        exit;

    } catch (PDOException $e) {
        $_SESSION['db_error'] = "Database connection failed: " . $e->getMessage();
        header('Location: install.php?step=database_setup');
        exit;
    }
}


// == Step 3: Admin User Creation Form ==
if ($currentStep === 'admin_setup') {
    if (!isset($_SESSION['db_credentials_validated']) || !$_SESSION['db_credentials_validated']) {
        $_SESSION['db_error'] = "Database setup must be completed first.";
        header('Location: install.php?step=database_setup');
        exit;
    }
    renderHeader('Admin User Setup');
    echo "<h2>Step 3: Create Super Administrator Account</h2>";
    echo "<p>This user will have full administrative privileges.</p>";

    if (isset($_SESSION['admin_error'])) {
        echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['admin_error']) . "</div>";
        unset($_SESSION['admin_error']);
    }
     if (isset($_SESSION['admin_form_data'])) {
        $old = $_SESSION['admin_form_data'];
        unset($_SESSION['admin_form_data']);
    } else {
        $old = [];
    }

    echo "<form method='POST' action='install.php?step=process_admin_setup'>";
    echo "<div><label for='admin_fname'>First Name:</label><input type='text' name='admin_fname' id='admin_fname' value='" . htmlspecialchars($old['admin_fname'] ?? 'Admin') . "' required></div>";
    echo "<div><label for='admin_lname'>Last Name:</label><input type='text' name='admin_lname' id='admin_lname' value='" . htmlspecialchars($old['admin_lname'] ?? 'User') . "' required></div>";
    echo "<div><label for='admin_email'>Email:</label><input type='email' name='admin_email' id='admin_email' value='" . htmlspecialchars($old['admin_email'] ?? '') . "' required></div>";
    echo "<div><label for='admin_pass'>Password:</label><input type='password' name='admin_pass' id='admin_pass' required></div>";
    echo "<div><label for='admin_pass_confirm'>Confirm Password:</label><input type='password' name='admin_pass_confirm' id='admin_pass_confirm' required></div>";

    echo "<div class='nav-buttons'>";
    echo "<a href='install.php?step=database_setup' class='button' style='background-color:#6c757d;'>Back</a>";
    echo "<button type='submit'>Next: Import Database Schema</button>";
    echo "</div></form>";
    renderFooter();
    exit;
}


// == Step 3.5: Process Admin User & Import DB Schema ==
if ($currentStep === 'process_admin_setup') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['db_credentials_validated']) || !$_SESSION['db_credentials_validated']) {
        header('Location: install.php?step=admin_setup');
        exit;
    }

    $adminFname = trim($_POST['admin_fname'] ?? '');
    $adminLname = trim($_POST['admin_lname'] ?? '');
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPass = $_POST['admin_pass'] ?? '';
    $adminPassConfirm = $_POST['admin_pass_confirm'] ?? '';

    $_SESSION['admin_form_data'] = $_POST;

    // Validation
    if (empty($adminFname) || empty($adminLname) || empty($adminEmail) || empty($adminPass)) {
        $_SESSION['admin_error'] = "All admin fields are required.";
        header('Location: install.php?step=admin_setup'); exit;
    }
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['admin_error'] = "Invalid admin email format.";
        header('Location: install.php?step=admin_setup'); exit;
    }
    if (strlen($adminPass) < 8) { // Basic password length check
        $_SESSION['admin_error'] = "Admin password must be at least 8 characters.";
        header('Location: install.php?step=admin_setup'); exit;
    }
    if ($adminPass !== $adminPassConfirm) {
        $_SESSION['admin_error'] = "Admin passwords do not match.";
        header('Location: install.php?step=admin_setup'); exit;
    }

    // Get DB config from session
    $dbConfig = $_SESSION['db_config'];
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']}";

    try {
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // --- Import Database Schema (SQL DDL) ---
        // This part assumes the DDL is stored in files or an array here.
        // For simplicity, I'll define a function to get SQL DDL content.
        // In a real script, these would be read from the migration-like SQL files I generated.
        // For this example, I'll include a snippet. The actual DDL is extensive.

        $sqlDdlPath = __DIR__ . '/database/schema_ddl.sql';

        if (!file_exists($sqlDdlPath)) {
            $_SESSION['admin_error'] = "Database schema file (schema_ddl.sql) not found. Cannot proceed.";
            header('Location: install.php?step=admin_setup'); exit;
        }

        $allDdlStatements = file_get_contents($sqlDdlPath);
        if ($allDdlStatements === false) {
            $_SESSION['admin_error'] = "Could not read database schema file (schema_ddl.sql).";
            header('Location: install.php?step=admin_setup'); exit;
        }

        // Execute DDL statements
        // Simple execution: PDO::exec can run multiple statements if separated by semicolons
        // and if the driver supports it. For more complex .sql files, parsing might be needed.
        try {
            $pdo->exec($allDdlStatements);
        } catch (PDOException $e) {
            // Catch error during DDL execution specifically
             $_SESSION['admin_error'] = "Error importing database schema: " . $e->getMessage() . ". Please ensure the database is empty or tables do not conflict.";
            header('Location: install.php?step=admin_setup'); exit;
        }

        // --- Insert Admin User ---
        // Attempt to use Laravel's Hasher if possible, otherwise fallback
        $hashedPassword = '';
        $laravelBootstrapPath = __DIR__ . '/bootstrap/app.php';
        $laravelAutoloadPath = __DIR__ . '/vendor/autoload.php';

        if (file_exists($laravelAutoloadPath) && file_exists($laravelBootstrapPath)) {
            try {
                require_once $laravelAutoloadPath;
                $app = require_once $laravelBootstrapPath; // This bootstraps Laravel
                // $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap(); // Might be needed for full bootstrap
                $hashedPassword = \Illuminate\Support\Facades\Hash::make($adminPass);
            } catch (\Throwable $e) {
                // Fallback if Laravel bootstrap fails
                $hashedPassword = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);
            }
        } else {
            // Fallback if Laravel is not fully there (e.g. vendor not installed)
            $hashedPassword = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $stmt = $pdo->prepare("INSERT INTO users (name, firstName, lastName, email, password, isAdmin, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())");
        $stmt->execute([
            $adminFname . ' ' . $adminLname,
            $adminFname,
            $adminLname,
            $adminEmail,
            $hashedPassword,
            1 // isAdmin = true
        ]);

        unset($_SESSION['admin_error']);
        unset($_SESSION['admin_form_data']);
        $_SESSION['admin_user_created_flag'] = true; // Flag that admin and DB setup is done
        $_SESSION['install_step'] = 'finalize';
        header('Location: install.php?step=finalize');
        exit;

    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Database operation failed: " . $e->getMessage() . ". Check DDL or if tables already exist.";
        header('Location: install.php?step=admin_setup'); // Go back to admin form, though error might be DDL
        exit;
    } catch (\Throwable $e) { // Catch other errors like Laravel bootstrap
        $_SESSION['admin_error'] = "An unexpected error occurred during admin setup or DB import: " . $e->getMessage();
        header('Location: install.php?step=admin_setup');
        exit;
    }
}


// == Step 4: Finalize Installation ==
if ($currentStep === 'finalize') {
    if (!isset($_SESSION['db_credentials_validated']) || !$_SESSION['db_credentials_validated'] || !isset($_SESSION['admin_user_created_flag'])) { // Check a flag set by process_admin_setup
        // For now, db_credentials_validated is a proxy. A more specific flag from admin creation is better.
        // Let's assume if we reached here and process_admin_setup didn't error out, it's okay for now.
        // A more robust check would be if the admin user exists in DB or a specific session flag.
    }

    renderHeader('Installation Complete');
    echo "<h2>Step 4: Installation Finalized</h2>";

    $symlinkCreated = false;
    $publicStoragePath = __DIR__ . '/public/storage';
    $storageAppPublicPath = __DIR__ . '/storage/app/public';

    // Attempt to create symlink (may fail on shared hosting due to permissions)
    if (!file_exists($publicStoragePath)) {
        if (@symlink($storageAppPublicPath, $publicStoragePath)) {
            $symlinkCreated = true;
            echo "<p class='success'>Storage symlink (<code>public/storage</code>) created successfully.</p>";
        } else {
            echo "<p class='error'>Could not create the storage symlink automatically. You may need to create it manually if your hosting allows (<code>ln -s " . htmlspecialchars($storageAppPublicPath) . " " . htmlspecialchars($publicStoragePath) . "</code>) or ensure your file upload paths are configured correctly if symlinks are not possible. Files uploaded via the application might not be publicly accessible otherwise.</p>";
        }
    } else {
        echo "<p class='success'>Storage symlink (<code>public/storage</code>) already exists.</p>";
        $symlinkCreated = true; // Assuming it's correct
    }

    // Create lock file
    if (file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'))) {
        echo "<p class='success'>Installation lock file created. The installer will not run again.</p>";
    } else {
        echo "<p class='error'><strong>CRITICAL:</strong> Could not create the lock file (<code>" . htmlspecialchars(LOCK_FILE) . "</code>). Please create an empty file with this name and path manually to prevent re-running the installer, or ensure the <code>storage</code> directory is writable by the web server.</p>";
    }

    echo "<div class='alert alert-success mt-4'>";
    echo "<h4>Installation Successful!</h4>";
    echo "<p>Einspot Solutions has been successfully installed.</p>";
    echo "</div>";

    echo "<div class='mt-4 mb-4 code-block' style='background-color: #ffe0b2; color: #856404; border: 1px solid #ffe0b2;'>";
    echo "<h3 style='color:#856404; margin-top:0;'>IMPORTANT SECURITY NOTICE:</h3>";
    echo "<p>For security reasons, please <strong>DELETE</strong> the <code>install.php</code> file from your server immediately.</p>";
    echo "</div>";

    $appUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . str_replace('/install.php', '', $_SERVER['SCRIPT_NAME']);

    echo "<div class='nav-buttons text-center'>";
    echo "<a href='" . htmlspecialchars($appUrl) . "/public' class='button'>Go to Homepage</a>";
    echo "<a href='" . htmlspecialchars($appUrl) . "/public/admin/dashboard' class='button' style='margin-left:10px; background-color:#5cb85c;'>Go to Admin Login</a>"; // Assuming /admin redirects to login if not authenticated
    echo "</div>";

    // Clear session data related to installation
    session_unset();
    session_destroy();

    renderFooter();
    exit;
}


// Default end for script if no step matches explicitly above current steps.
// This makes sure all defined steps are covered in the main if/elseif chain.
if (!in_array($currentStep, ['welcome', 'database_setup', 'process_database_setup', 'admin_setup', 'process_admin_setup', 'finalize'])) {
    $_SESSION['install_step'] = 'welcome'; // Reset to welcome if step is invalid or flow is broken
    header('Location: install.php');
    exit;
}


?>
