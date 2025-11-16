<?php
// Custom wp-config.php tailored for Docker Swarm + secrets
// Reads DB password from a Docker secret file and optionally includes salts from a secret file.

// 1) Secrets: DB password and salts
if (($f = getenv('DB_PASSWORD_FILE')) && file_exists($f)) {
    $pw = trim(file_get_contents($f));
    // Populate env so downstream code can read it
    putenv("WORDPRESS_DB_PASSWORD={$pw}");
    $_ENV['WORDPRESS_DB_PASSWORD'] = $pw;
    $_SERVER['WORDPRESS_DB_PASSWORD'] = $pw;
}

// If the salts secret is a PHP file with define('AUTH_KEY', '...'); etc., include it now
$saltFile = getenv('WP_SALT_FILE');
if ($saltFile && file_exists($saltFile)) {
    include $saltFile; // expected to contain define() calls
}

// 2) Database settings (pull from environment or defaults)
define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wordpress');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wordpress');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: '');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'db:3306');
define('DB_CHARSET', getenv('WORDPRESS_DB_CHARSET') ?: 'utf8mb4');
define('DB_COLLATE', getenv('WORDPRESS_DB_COLLATE') ?: '');

// 3) Table prefix
$table_prefix = getenv('WORDPRESS_TABLE_PREFIX') ?: 'wp_';

// 4) Authentication unique keys and salts
// If not defined by salts secret above, try environment variables, otherwise fall back to placeholders.
function define_if_missing($name, $value) {
    if (!defined($name) && $value !== null && $value !== '') {
        define($name, $value);
    }
}

define_if_missing('AUTH_KEY', getenv('AUTH_KEY'));
define_if_missing('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
define_if_missing('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY'));
define_if_missing('NONCE_KEY', getenv('NONCE_KEY'));
define_if_missing('AUTH_SALT', getenv('AUTH_SALT'));
define_if_missing('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define_if_missing('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT'));
define_if_missing('NONCE_SALT', getenv('NONCE_SALT'));

// If still not defined, leave placeholders; it's strongly recommended to set WP_SALT_FILE secret in production.
define_if_missing('AUTH_KEY', 'set-in-WP_SALT_FILE');
define_if_missing('SECURE_AUTH_KEY', 'set-in-WP_SALT_FILE');
define_if_missing('LOGGED_IN_KEY', 'set-in-WP_SALT_FILE');
define_if_missing('NONCE_KEY', 'set-in-WP_SALT_FILE');
define_if_missing('AUTH_SALT', 'set-in-WP_SALT_FILE');
define_if_missing('SECURE_AUTH_SALT', 'set-in-WP_SALT_FILE');
define_if_missing('LOGGED_IN_SALT', 'set-in-WP_SALT_FILE');
define_if_missing('NONCE_SALT', 'set-in-WP_SALT_FILE');

// 5) Site URLs (force HTTPS)
define('WP_HOME', 'https://new-wordpress.tsbi.fun');
define('WP_SITEURL', 'https://new-wordpress.tsbi.fun');
define('FORCE_SSL_ADMIN', true);

// 6) Debugging
// Supports either WORDPRESS_DEBUG or WP_DEBUG env flags
$wpDebug = getenv('WP_DEBUG');
if ($wpDebug === false || $wpDebug === '') {
    $wpDebug = getenv('WORDPRESS_DEBUG');
}
define('WP_DEBUG', ($wpDebug && strtolower($wpDebug) !== 'false' && $wpDebug !== '0'));

// 6) Absolute path to the WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
