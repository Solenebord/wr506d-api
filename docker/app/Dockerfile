FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid


# 1000 is default, in case --build-arg is not passed with the build command.
ARG USER_ID=9999
ARG GROUP_ID=9999
ARG HOME_DIR=/home/www-data
# Change user ID & group ID & home directory & shell of www-data user.
# We change home dir because some custom commands may use home dir
# for caching (like composer, npm or yarn) or for another reason.
RUN mkdir ${HOME_DIR} \
    && chown -R ${USER_ID}:${GROUP_ID} ${HOME_DIR} \
    && usermod --uid ${USER_ID} --home ${HOME_DIR} --shell /bin/bash www-data \
    && groupmod --gid ${GROUP_ID} www-data

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

ENV TZ=America/New_York
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Get Symfony CLI
#RUN curl -sS https://get.symfony.com/cli/installer | bash
#RUN export PATH="$HOME/.symfony5/bin:$PATH"

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www/symfony
USER $user
