#!/bin/bash
psql -h localhost -U postgres -c 'CREATE DATABASE porticotest;'
psql -h localhost -U postgres -c 'CREATE ROLE portico LOGIN SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;'
psql -h localhost -U postgres portico-empty-start < portico-empty-start.2.sql