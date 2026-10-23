# syntax=docker/dockerfile:1
FROM dunglas/frankenphp:1.11-php8.4

LABEL org.opencontainers.image.source="https://github.com/rizwanhamka/uts-pl-24-539823-sv-24684"

# Ekstensi PHP untuk Laravel + MySQL
RUN install-php-extensions pdo_mysql opcache zip

# Composer dengan versi dikunci
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1) Salin HANYA composer.json dan composer.lock, lalu pasang dependensi.
#    Lapisan ini tetap di-cache selama kedua berkas tidak berubah.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

# 2) Baru salin kode aplikasi
COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

COPY Caddyfile /etc/frankenphp/Caddyfile
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

HEALTHCHECK --interval=10s --timeout=5s --start-period=40s --retries=10 \
  CMD curl -fsS http://localhost:8000/up || exit 1

ENTRYPOINT ["docker-entrypoint.sh"]
