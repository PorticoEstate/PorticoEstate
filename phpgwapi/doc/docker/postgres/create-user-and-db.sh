#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
    CREATE ROLE portico LOGIN SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION PASSWORD 'portico';
    CREATE DATABASE porticotest;
EOSQL