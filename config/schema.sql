CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS workspaces (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  white_label_name VARCHAR(255) NULL,
  white_label_logo_path VARCHAR(255) NULL,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS workspace_members (
  workspace_id INT NOT NULL,
  user_id INT NOT NULL,
  role VARCHAR(20) NOT NULL,
  UNIQUE KEY workspace_user_unique (workspace_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS brand_kits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  workspace_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  tagline VARCHAR(255) NULL,
  description TEXT NULL,
  voice_keywords TEXT NULL,
  usage_do TEXT NULL,
  usage_dont TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX brand_kits_workspace_idx (workspace_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS brand_assets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_kit_id INT NOT NULL,
  type ENUM('primary_logo','icon','mono','other') NOT NULL,
  path VARCHAR(255) NOT NULL,
  mime VARCHAR(100) NOT NULL,
  size INT NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL,
  INDEX brand_assets_kit_idx (brand_kit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS colors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_kit_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  hex VARCHAR(10) NOT NULL,
  sort_order INT NOT NULL,
  locked TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  INDEX colors_kit_idx (brand_kit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS font_selections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_kit_id INT NOT NULL UNIQUE,
  heading_font VARCHAR(255) NOT NULL,
  body_font VARCHAR(255) NOT NULL,
  heading_weights TEXT NULL,
  body_weights TEXT NULL,
  source VARCHAR(50) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS template_assets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_kit_id INT NOT NULL,
  type ENUM('social_profile','social_banner','favicon','email_signature') NOT NULL,
  path VARCHAR(255) NOT NULL,
  meta TEXT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS share_links (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_kit_id INT NOT NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  revoked_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  INDEX share_links_token_idx (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS invites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  workspace_id INT NOT NULL,
  email VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  status VARCHAR(20) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL,
  INDEX invites_token_idx (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  workspace_id INT NOT NULL UNIQUE,
  stripe_customer_id VARCHAR(255) NULL,
  stripe_subscription_id VARCHAR(255) NULL,
  status VARCHAR(50) NOT NULL,
  plan VARCHAR(50) NOT NULL,
  current_period_end DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
