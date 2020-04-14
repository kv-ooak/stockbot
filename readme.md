## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

# Clear Cache

```bash
php artisan clear-compiled
composer dump-autoload
php artisan optimize
php artisan cache:clear
```


# Laravel folder permission

```bash
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```


# Build trader extension for php7.0
## Install php7.0-fpm

```bash
# remove php5 modules
sudo apt-get autoremove --purge php5-*
# add php-7.0 source list by [Ondřej Surý](https://github.com/oerdnj)
sudo add-apt-repository ppa:ondrej/php
# Update index
sudo apt-get update
# Install php7.0-fpm with needed extensions
sudo apt-get install php7.0-fpm php7.0-cli php7.0-common php7.0-json php7.0-opcache  php7.0-mysql php7.0-phpdbg php7.0-gd php7.0-imap php7.0-ldap php7.0-pgsql php7.0-pspell php7.0-recode php7.0-snmp php7.0-tidy php7.0-dev php7.0-intl php7.0-gd php7.0-curl
sudo apt-get install php7.0 libapache2-mod-php7.0 php7.0 php7.0-common php7.0-gd php7.0-mysql php7.0-mcrypt php7.0-curl php7.0-intl php7.0-xsl php7.0-mbstring php7.0-zip php7.0-bcmath php7.0-iconv
# Install addition lib for apache
sudo apt-get install libapache2-mod-php7.0
# Install snmp
sudo apt-get install snmp
sudo apt-get install php-xml
```

## Download extension
https://pecl.php.net/package/trader

## Build extension

```bash
wget https://pecl.php.net/get/trader-0.4.0.tgz
tar zxvf trader-0.4.0.tgz
phpize
./configure
make && sudo make install
```

## Activate trader extension in fpm and cli

```bash
sudo echo "extension=trader.so" > /etc/php/7.0/mods-available/trader.ini
sudo ln -sf /etc/php/7.0/mods-available/trader.ini /etc/php/7.0/fpm/conf.d/20-trader.ini
sudo ln -sf /etc/php/7.0/mods-available/trader.ini /etc/php/7.0/cli/conf.d/20-trader.ini
sudo ln -sf /etc/php/7.0/mods-available/trader.ini /etc/php/7.0/apache2/conf.d/20-trader.ini
sudo service php7.0-fpm restart
```


# Build and install Redis

## Prepare

```bash
# Start off by updating all of the apt-get packages:
sudo apt-get update
# Once the process finishes, download a compiler with build essential which will help us install Redis from source:
sudo apt-get install build-essential
# Finally, we need to download tcl:
sudo apt-get install tcl8.5
```

## Install Redis

```bash
# Download the latest stable release tarball from Redis.io.
wget http://download.redis.io/releases/redis-stable.tar.gz
# Untar it and switch into that directory:
tar xzf redis-stable.tar.gz
cd redis-stable
# Proceed to with the make command:
make
# Run the recommended make test (Optional):
make test
# Finish up by running make install, which installs the program system-wide.
sudo make install

# To access the script move into the utils directory:
cd utils
# From there, run the Ubuntu/Debian install script:
sudo ./install_server.sh
# As the script runs, you can choose the default options by pressing enter. Once the script completes, the redis-server will be running in the background.
```

## Setting Redis

```bash
# You can start and stop redis with these commands (the number depends on the port you set during the installation. 6379 is the default port setting):
sudo service redis_6379 start
sudo service redis_6379 stop
# You can then access the redis database by typing the following command:
redis-cli
# To set Redis to automatically start at boot, run:
sudo update-rc.d redis_6379 defaults
```


# Install composer

```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```


# Config Apache2

```config
<Directory "/var/www/StockBot/public/">
    Options FollowSymLinks
    AllowOverride all
    Order Deny,Allow
    Allow from All
</Directory>
```


# Crontab
```bash
30 15 * * 1-5 /usr/bin/python /var/www/StockBot/script/stockdata.py
25 8 * * 1-5 /usr/bin/python /var/www/StockBot/script/vietstock.py
```


# Fix timezone for sql
```bash
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql -p
```