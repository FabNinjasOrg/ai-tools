#!/bin/sh
set -e

echo "Starting SSM env loader..."

# Path in SSM
SSM_PATH="/facefinder/"

# Check AWS CLI
if ! command -v aws >/dev/null 2>&1; then
  echo "ERROR: aws CLI not found inside container"
  exit 1
fi

# Fetch parameters
PARAMS=$(aws ssm get-parameters-by-path \
    --region ap-south-1 \
    --path "$SSM_PATH" \
    --with-decryption \
    --recursive \
    --query "Parameters[*].[Name,Value]" \
    --output text)

# .env file location
ENV_FILE="/var/www/html/.env"

# Create fresh .env file
rm -f "$ENV_FILE"
touch "$ENV_FILE"
chmod 600 "$ENV_FILE"

# Convert SSM results into KEY=VALUE format
echo "$PARAMS" | while IFS=$'\t' read -r name value; do
    key=$(basename "$name")
    echo "$key=$value" >> "$ENV_FILE"
done

echo ".env created successfully from SSM"

# Hand over control to the main container command
exec "$@"
