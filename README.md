# New WordPress (Swarm-ready)

This repo packages WordPress for your Docker Swarm platform with:
- Secret-based DB password and salts
- DB readiness wait on startup
- CI/CD workflow that builds, pushes, and deploys from a registry

## Files
- Dockerfile: Extends the official `wordpress:php-apache` image, adds `wp-cli`, DB wait helper, and uses a custom entrypoint.
- entrypoint.sh: Waits for the DB (configurable) and hands off to the official entrypoint.
- wait-for-db.sh: Simple TCP wait (uses `DB_WAIT_HOST`, `DB_WAIT_PORT`, `DB_WAIT_TIMEOUT`).
- wordpress/wp-config.php: Reads `DB_PASSWORD_FILE` and `WP_SALT_FILE` (Docker secrets) and configures WordPress.
- .github/workflows/wordpress-ci.yml: Build, push, and deploy via SSH using your central deploy scripts.

## Required platform-side secrets
On the server, the deployment scripts will ensure and mount these secrets automatically (generated if missing):
- new-wordpress-production-db-password (auto-generated)
- new-wordpress-salts (set this with content of PHP define salts) — strongly recommended
- new-wordpress-admin-pass (admin password used by post-deploy wp-cli)

## GitHub secrets required
Set these in your GitHub repository Settings → Secrets and variables → Actions:
- REGISTRY_HOST (e.g., registry.example.com)
- REGISTRY_USERNAME
- REGISTRY_PASSWORD
- REGISTRY_NAMESPACE (namespace/project in the registry)
- SSH_HOST (your server hostname/IP)
- SSH_USER
- SSH_PRIVATE_KEY (PEM private key for SSH)
- SITE_DOMAIN (e.g., new-wordpress.example.com)

## Environment contract
The platform injects:
- WORDPRESS_DB_HOST, WORDPRESS_DB_NAME, WORDPRESS_DB_USER
- DB_PASSWORD_FILE → file path to DB password secret
- Optional: WP_SALT_FILE → file path to salts
- Optional: WP_ADMIN_PASSWORD_FILE → used by platform post-deploy to provision site
- WAIT_FOR_DB=true, DB_WAIT_HOST/PORT/TIMEOUT

## Local dev (optional)
You can run with docker compose for local testing, but production is handled by the platform.