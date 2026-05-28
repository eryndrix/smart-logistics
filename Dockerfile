# =====================================================================
# PHP 8.5-FPM Application Container
# =====================================================================
FROM php:8.5-fpm

# =====================================================================
# 1. SYSTEM DEPENDENCIES
# =====================================================================
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    curl \
    netcat-openbsd \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    pkg-config \
    autoconf \
    redis-tools \
    tzdata \
    && ln -snf /usr/share/zoneinfo/$APP_TZ /etc/localtime \
    && echo "$APP_TZ" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean \
    && rm -rf /tmp/* /var/tmp/*

# =====================================================================
# 2. PHP EXTENSIONS (CORE)
# =====================================================================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_pgsql \
        mbstring \
        intl \
        zip \
        exif \
        sockets \
        pcntl \
        bcmath \
    && docker-php-source delete

# =====================================================================
# 3. PHP EXTENSIONS (PECL)
# =====================================================================
RUN pecl install -f redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# =====================================================================
# 4. ENVIRONMENT VARIABLES
# =====================================================================
ENV APP_TZ=Asia/Yekaterinburg
ENV COMPOSER_HOME=/tmp/composer \
    COMPOSER_CACHE_DIR=/tmp/composer/cache

# =====================================================================
# 5. SET PHP TIMEZONE CONFIGURATION
# =====================================================================
RUN echo "date.timezone = Asia/Yekaterinburg" > /usr/local/etc/php/conf.d/timezone.ini

# =====================================================================
# 6. NON-ROOT USER SETUP
# =====================================================================
ARG UID=1000
ARG GID=1000

RUN groupadd -g ${GID} appgroup 2>/dev/null || true \
    && useradd -u ${UID} -g ${GID} -m -s /usr/sbin/nologin appuser 2>/dev/null || true

# =====================================================================
# 7. SSL CERTIFICATES DIRECTORIES
# =====================================================================
RUN mkdir -p /certs \
    && chown -R appuser:appgroup /certs

# =====================================================================
# 8. DIRECTORIES AND OWNERSHIP
# =====================================================================
RUN mkdir -p ${COMPOSER_HOME} ${COMPOSER_CACHE_DIR} \
    && chown -R appuser:appgroup /tmp/composer

# =====================================================================
# 9. WORKING DIRECTORY
# =====================================================================
WORKDIR /var/www/html

# =====================================================================
# 10. APPLICATION SOURCE CODE
# =====================================================================
COPY --chown=appuser:appgroup ./src /var/www/html

# =====================================================================
# 11. COMPOSER WRAPPER
# =====================================================================
COPY --chown=appuser:appgroup ./docker/php/composer.phar /usr/local/bin/composer.phar
RUN printf '#!/bin/sh\nexec php /usr/local/bin/composer.phar "$@"\n' > /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    && chown appuser:appgroup /usr/local/bin/composer

# =====================================================================
# 12. STORAGE DIRECTORY
# =====================================================================
RUN mkdir -p storage/logs \
    && chown -R appuser:appgroup storage \
    && chmod -R 750 storage

# =====================================================================
# 13. QUEUE HELPER SCRIPT
# =====================================================================
COPY ./docker/rabbitmq/wait-for-rabbitmq.sh /usr/local/bin/wait-for-rabbitmq.sh
RUN chmod +x /usr/local/bin/wait-for-rabbitmq.sh \
    && chown appuser:appgroup /usr/local/bin/wait-for-rabbitmq.sh

# =====================================================================
# 14. PHP CONFIGURATION FILES
# =====================================================================
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# =====================================================================
# 15. EXPOSE PORT
# =====================================================================
EXPOSE 8000

# =====================================================================
# 16. SWITCH TO NON-ROOT USER
# =====================================================================
USER appuser
