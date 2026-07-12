FROM alpine:3.18

# Install Nginx, PHP 8.2, Supervisor and necessary extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    php82 \
    php82-fpm \
    php82-sqlite3 \
    php82-pdo_sqlite \
    php82-json \
    php82-openssl \
    php82-curl \
    php82-mbstring \
    php82-session \
    php82-fileinfo \
    php82-dom \
    php82-xml \
    php82-ctype \
    php82-iconv

# Create symlink for php command
RUN ln -sf /usr/bin/php82 /usr/bin/php

# Configure Nginx, FPM and Supervisor
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/fpm.conf /etc/php82/php-fpm.d/www.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy all application files
COPY . /var/www/html

# Setup folder structure, create SQLite DB file and set permissions for Nginx user
RUN mkdir -p /var/www/html/app/Database/backups \
    && mkdir -p /var/www/html/public/uploads/logos \
    && mkdir -p /var/www/html/public/uploads/profiles \
    && touch /var/www/html/app/Database/siwes.db \
    && chown -R nginx:nginx /var/www/html \
    && chmod -R 775 /var/www/html/app/Database \
    && chmod -R 775 /var/www/html/public/uploads

# Expose HTTP Port
EXPOSE 80

# Launch services using Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
