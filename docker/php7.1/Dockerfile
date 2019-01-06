FROM php:7.1-cli-stretch

RUN echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
 && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini"

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
     hunspell \
     hunspell-en-us \
     aspell \
     aspell-en \
     libpspell-dev \
    && docker-php-ext-configure pspell \
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
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php  --no-ansi --install-dir=/usr/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer --ansi --version --no-interaction

WORKDIR /usr/src/myapp
