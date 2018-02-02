#!/bin/bash
HEADER_INC=/var/www/html/portico/header.inc.php
HEADER_INC_BACKUP=/var/www/html/portico/header.inc.php.backup
TEST_HEADER_INC=/var/www/html/portico/phpgwapi/doc/docker/testrunner/test-header.inc.php

#Wait for postgres container
sleep 5

cd /var/www/html/portico
composer install

#Backup existing header.inc.php
if [[ -f $HEADER_INC ]]; then
  HEADER_INC_EXISTS=true
  mv $HEADER_INC $HEADER_INC_BACKUP
fi

#Setup web application with header.inc.php for test env
ln -s $TEST_HEADER_INC $HEADER_INC

## Run tests
./vendor/bin/codecept run

#Restore existing header.inc.php
rm -rf $HEADER_INC
if [[ "$HEADER_INC_EXISTS" = true ]]; then
  mv $HEADER_INC_BACKUP $HEADER_INC
fi