FROM php:fpm-alpine

COPY --from=mlocati/php-extension-installer:latest --link /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
	;

RUN set -eux; \
    install-php-extensions \
		apcu \
		intl \
		opcache \
		zip \
        mbstring \
        pdo \
        pdo_mysql \
    ;

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer/composer:2-bin --link /composer /usr/bin/composer
ENV APP_ENV=dev XDEBUG_MODE=off

COPY docker/php/php.ini "$PHP_INI_DIR/php.ini"

COPY --link composer.* symfony.* ./
COPY --link . ./
RUN rm -Rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative; \
	composer dump-env dev; \
	composer run-script post-install-cmd; \
	chmod +x bin/console; sync;

RUN set -eux; \
    apk add --no-cache bash; \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash; \
    apk add symfony-cli;

WORKDIR /var/www/devoted
CMD composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress;
CMD ["php-fpm"]
