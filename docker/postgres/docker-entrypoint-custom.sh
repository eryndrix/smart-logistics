#!/bin/bash
set -e  # Exit immediately if a command exits with a non-zero status

# Create certs directory if it doesn't exist
mkdir -p /var/lib/postgresql/certs

# Copy TLS certificates from temporary location to PostgreSQL certs directory
cp /tmp/certs/postgres.crt /var/lib/postgresql/certs/postgres.crt
cp /tmp/certs/postgres.key /var/lib/postgresql/certs/postgres.key
cp /tmp/certs/ca.crt /var/lib/postgresql/certs/ca.crt

# Set ownership of the certificates directory to the 'postgres' user and group
chown -R postgres:postgres /var/lib/postgresql/certs

# Restrict permissions on the private key to owner only for security
chmod 600 /var/lib/postgresql/certs/postgres.key

# Execute the original PostgreSQL entrypoint script with custom config file
exec docker-entrypoint.sh postgres -c config_file=/etc/postgresql/postgresql.conf
