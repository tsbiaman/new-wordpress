FROM wordpress:6.4-php8.1-apache

# Install tools for healthchecks and DB wait
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    curl \
    netcat \
    default-mysql-client \
    ca-certificates \
  && rm -rf /var/lib/apt/lists/*

# Install wp-cli
RUN curl -sSLo /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x /usr/local/bin/wp

# Add entrypoint helpers
COPY wait-for-db.sh /usr/local/bin/wait-for-db
COPY entrypoint.sh /usr/local/bin/wp-entrypoint
RUN chmod +x /usr/local/bin/wait-for-db /usr/local/bin/wp-entrypoint

# Copy application WordPress sources
COPY wordpress/ /var/www/html/

# Ensure ownership for uploads/themes/plugins
RUN mkdir -p /var/www/html/wp-content/uploads \
  && chown -R www-data:www-data /var/www/html/wp-content

ENTRYPOINT ["/usr/local/bin/wp-entrypoint"]
CMD ["apache2-foreground"]
