#!/bin/sh
set -e

echo "Starting SSM env loader..."

SSM_PATH="/facefinder/"
ENV_FILE="/var/www/html/.env"

# Ensure AWS CLI is available
if ! command -v aws >/dev/null 2>&1; then
  echo "ERROR: aws CLI not found inside container"
  exit 1
fi

# Fetch parameters safely
PARAMS=$(aws ssm get-parameters-by-path \
  --region ap-south-1 \
  --path "$SSM_PATH" \
  --with-decryption \
  --recursive \
  --query "Parameters[*].[Name,Value]" \
  --output text)

rm -f "$ENV_FILE"
touch "$ENV_FILE"
chmod 600 "$ENV_FILE"

# Convert SSM results into KEY=VALUE format
echo "$PARAMS" | while IFS=$'\t' read -r name value; do
  key=$(basename "$name" | tr -d '[:space:]')
  clean_value=$(printf '%s' "$value" | tr -d '\r')
  echo "${key}=${clean_value}" >> "$ENV_FILE"
done

echo ".env created successfully from SSM"

exec "$@"
