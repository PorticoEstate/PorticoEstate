## Install
- In root of project run `composer install`
- Select your platform for the selenium chromedriver
- Startup a postgres database on port `5433`
- Create database `porticotest`
- Give user `portico` full access to database
- Import dbdump `tests/_data/portico-test.sql` into the new database `porticotest`

## Run
- Startup postgres database on port `5433` with preloaded dbdump for tests
- From `tests/` run `./run-test-in-dev.sh`