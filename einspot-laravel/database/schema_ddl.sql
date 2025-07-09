-- SQL DDL for Einspot Laravel Application

-- From 0001_01_01_000000_create_users_table.php
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  firstName VARCHAR(255) NULL,
  lastName VARCHAR(255) NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  isAdmin TINYINT(1) NOT NULL DEFAULT 0,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
  email VARCHAR(255) PRIMARY KEY,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
  id VARCHAR(255) PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  payload LONGTEXT NOT NULL,
  last_activity INT NOT NULL,
  INDEX sessions_user_id_index (user_id),
  INDEX sessions_last_activity_index (last_activity)
  -- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Handled by Laravel if session driver is DB
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- From 2024_07_09_100001_create_categories_table.php
CREATE TABLE IF NOT EXISTS categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  description TEXT NULL,
  type VARCHAR(255) NOT NULL COMMENT 'e.g., product, blog',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100002_create_products_table.php
CREATE TABLE IF NOT EXISTS products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  description TEXT NULL,
  price DECIMAL(10, 2) NOT NULL,
  stock_quantity INT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NULL,
  images JSON NULL,
  pdf_manual_path VARCHAR(255) NULL,
  meta_title VARCHAR(255) NULL,
  meta_description TEXT NULL,
  meta_keywords VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100003_create_tags_table.php
CREATE TABLE IF NOT EXISTS tags (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  slug VARCHAR(255) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100004_create_product_tags_table.php
CREATE TABLE IF NOT EXISTS product_tag (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  tag_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY product_tag_product_id_tag_id_unique (product_id, tag_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100005_create_services_table.php
CREATE TABLE IF NOT EXISTS services (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  description TEXT NULL,
  icon_path VARCHAR(255) NULL,
  image_url VARCHAR(255) NULL,
  features JSON NULL,
  whatsapp_text TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100006_create_projects_table.php
CREATE TABLE IF NOT EXISTS projects (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  client VARCHAR(255) NULL,
  location VARCHAR(255) NULL,
  duration VARCHAR(255) NULL,
  status VARCHAR(255) NOT NULL DEFAULT 'Completed',
  type VARCHAR(255) NULL COMMENT 'e.g., HVAC, Fire Safety',
  description TEXT NOT NULL,
  image_url VARCHAR(255) NULL,
  images JSON NULL,
  brands_used JSON NULL,
  technologies JSON NULL,
  meta_title VARCHAR(255) NULL,
  meta_description TEXT NULL,
  meta_keywords VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100007_create_blogs_table.php
CREATE TABLE IF NOT EXISTS blogs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content LONGTEXT NOT NULL,
  excerpt TEXT NULL,
  user_id BIGINT UNSIGNED NULL COMMENT 'Author',
  category_id BIGINT UNSIGNED NULL COMMENT 'Blog category',
  image_url VARCHAR(255) NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  published_at TIMESTAMP NULL,
  meta_title VARCHAR(255) NULL,
  meta_description TEXT NULL,
  meta_keywords VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100008_create_blog_post_tag_table.php
CREATE TABLE IF NOT EXISTS blog_post_tag (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  blog_id BIGINT UNSIGNED NOT NULL,
  tag_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY blog_post_tag_blog_id_tag_id_unique (blog_id, tag_id),
  FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100009_create_quote_requests_table.php
CREATE TABLE IF NOT EXISTS quote_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NULL,
  company VARCHAR(255) NULL,
  service_of_interest VARCHAR(255) NULL,
  project_description TEXT NOT NULL,
  estimated_budget VARCHAR(255) NULL,
  timeline VARCHAR(255) NULL,
  product_service_name VARCHAR(255) NULL COMMENT 'Product/Service from WhatsApp prefill',
  status VARCHAR(255) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100010_create_settings_table.php
CREATE TABLE IF NOT EXISTS settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(255) NOT NULL UNIQUE,
  value TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100011_create_contact_submissions_table.php
CREATE TABLE IF NOT EXISTS contact_submissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NULL,
  company VARCHAR(255) NULL,
  service VARCHAR(255) NULL COMMENT 'Service of interest',
  message TEXT NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_100012_create_newsletter_subscriptions_table.php
CREATE TABLE IF NOT EXISTS newsletter_subscriptions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_120000_create_orders_table.php
CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  shipping_address TEXT NOT NULL,
  billing_address TEXT NULL,
  sub_total DECIMAL(10, 2) NOT NULL,
  vat_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  shipping_cost DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  total_amount DECIMAL(10, 2) NOT NULL,
  status VARCHAR(255) NOT NULL DEFAULT 'pending' COMMENT 'e.g., pending, processing, shipped, delivered, cancelled, returned',
  payment_method VARCHAR(255) NULL,
  payment_status VARCHAR(255) NOT NULL DEFAULT 'pending' COMMENT 'e.g., pending, paid, failed',
  payment_reference VARCHAR(255) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_120001_create_order_items_table.php
CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NULL,
  product_name VARCHAR(255) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  price_at_purchase DECIMAL(10, 2) NOT NULL,
  total_price DECIMAL(10, 2) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- From 2024_07_09_130000_create_activity_logs_table.php
CREATE TABLE IF NOT EXISTS activity_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(255) NOT NULL COMMENT 'e.g., created, updated, deleted',
  loggable_id BIGINT UNSIGNED NOT NULL,
  loggable_type VARCHAR(255) NOT NULL,
  description TEXT NULL,
  properties JSON NULL COMMENT 'Store old/new attributes or other data',
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX activity_logs_loggable_type_loggable_id_index (loggable_type, loggable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
