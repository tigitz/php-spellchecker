ARG PHP_VERSION

FROM php:${PHP_VERSION}-cli-buster

ENV XDEBUG_MODE=coverage
RUN echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
 && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini"

ARG XDEBUG_VERSION=3.1.1

RUN apt-get update \
    && apt install -y \
     curl \
     git \
     zip \
     unzip \
     openssl \
     libzip-dev \
     ispell \
     iamerican \
     irussian \
     hunspell \
     hunspell-en-us \
     hunspell-ru \
     aspell \
     aspell-en \
     aspell-ru \
     libpspell-dev \
    && pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-configure pspell \
	&& docker-php-ext-enable xdebug \
    && docker-php-ext-install pspell \
    && docker-php-ext-install zip \
    && rm -r /var/lib/apt/lists/*

RUN cp /usr/share/hunspell/en_US.aff  /usr/share/hunspell/en_US.aff.orig \
    && cp /usr/share/hunspell/en_US.dic  /usr/share/hunspell/en_US.dic.orig \
    && iconv --from ISO8859-1 -t ascii//TRANSLIT /usr/share/hunspell/en_US.aff.orig > /usr/share/hunspell/en_US.aff \
    && iconv --from ISO8859-1 -t ascii//TRANSLIT /usr/share/hunspell/en_US.dic.orig > /usr/share/hunspell/en_US.dic \
    && head /usr/share/hunspell/en_US.aff \
    && sed -i '/SET ISO8859-1/c\SET UTF-8' /usr/share/hunspell/en_US.aff

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /usr/src/myapp
