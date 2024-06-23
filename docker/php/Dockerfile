FROM php:8.3-fpm

RUN echo "\e[1;33mInstall COMPOSER\e[0m"
RUN cd /tmp \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN apt-get update

# Install useful tools
RUN apt-get -y install apt-utils nano wget dialog vim

# Install important libraries
RUN echo "\e[1;33mInstall important libraries\e[0m"
RUN apt-get -y install --fix-missing \
    apt-utils \
    build-essential \
    git \
    curl \
    libcurl4 \
    libcurl4-openssl-dev \
    zlib1g-dev \
    libzip-dev \
    zip \
    libbz2-dev \
    locales \
    libmcrypt-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev

RUN docker-php-ext-install pgsql pdo_pgsql pdo

# RUN echo "\e[1;33mInstall important docker dependencies\e[0m"
# RUN docker-php-ext-install \
#     exif \
#     pcntl \
#     bcmath \
#     ctype \
#     curl \
#     iconv \   
#     xml \
#     soap \
#     pcntl \
#     mbstring \
#     tokenizer \
#     bz2 \
#     zip \
#     intl

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN useradd ifdancoder \
  && usermod -u 1000 ifdancoder \
  && groupmod -g 1000 ifdancoder

USER ifdancoder
