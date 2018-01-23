#!/bin/bash
cd /var/www/html/portico
composer install
./vendor/bin/codecept run