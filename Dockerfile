FROM php:7.4-cli-alpine

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync
RUN install-php-extensions pdo_mysql opcache apcu intl xdebug @composer
RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

RUN printf "\
alias ll='ls -alF'\n\
alias composer='XDEBUG_MODE=off COMPOSER_MEMORY_LIMIT=-1 composer'\n\
" > /etc/profile.d/aliases.sh

ARG USER_ID
ARG GROUP_ID
RUN apk --no-cache add openssh git shadow
RUN groupmod -g ${GROUP_ID} www-data && usermod -u ${USER_ID} www-data
WORKDIR /home/www-data/app
