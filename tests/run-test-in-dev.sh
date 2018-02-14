#!/bin/bash

export WEBHOST=localhost
export WEBPORT=8080
export DBHOST=localhost
export DBPORT=5433
export DBNAME=porticotest
export DBUSER=portico
export DBPASSWORD=portico
export DBDUMP=tests/_data/portico-test.sql
export SELENIUMHOST=localhost

cd ..
./vendor/bin/chromedriver --port=4444 --url-base=/wd/hub &
./vendor/bin/codecept run --env development --debug &
wait -n
pkill -P $$