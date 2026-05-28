#!/bin/sh

# Exit immediately if a command exits with a non-zero status,
# if an unset variable is used, or if any command in a pipeline fails
set -euo pipefail

# Set connection variables from environment or use defaults
host="${RABBITMQ_HOST:-rabbitmq}"
port="${RABBITMQ_PORT:-5671}"
queue="${RABBITMQ_QUEUE:-messages.low}"
sleep_time="${RABBITMQ_SLEEP:-3}"

# Wait for RabbitMQ server to become available on the specified TLS port
echo "🐰 Waiting for RabbitMQ TLS (${host}:${port})..."
while ! nc -z "$host" "$port"; do
  echo "   ⏳ RabbitMQ unavailable - retrying in ${sleep_time}s..."
  sleep "$sleep_time"
done

# Output the connection details once RabbitMQ is ready
echo "✅ RabbitMQ TLS ready!"
echo "   Host:     ${host}:${port}"
echo "   Queue:    ${queue}"
echo "   Sleep:    ${sleep_time}s"
echo "   Tries:    3"
echo "   Timeout:  60s"
echo "─────────────────────────────────────────"

# Start the Laravel queue worker for RabbitMQ with the specified parameters
exec php /var/www/html/artisan queue:work rabbitmq \
  --queue="${queue}" \
  --sleep="${sleep_time}" \
  --tries=3 \
  --timeout=60 \
  --verbose
