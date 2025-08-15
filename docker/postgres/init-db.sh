#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    -- Create pgvector extension
    CREATE EXTENSION IF NOT EXISTS vector;

    -- Create testing database
    CREATE DATABASE "testing";
    GRANT ALL PRIVILEGES ON DATABASE "testing" TO "$POSTGRES_USER";
EOSQL

echo "PostgreSQL initialization completed with pgvector extension"
