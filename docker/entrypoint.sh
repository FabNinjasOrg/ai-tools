#!/bin/bash
set -e

APP="facefinder"
REGION="ap-south-1"
SSM_PATH="/$APP/"
ENV_FILE="/var/www/html/.env"

echo "Starting SSM env loader for PRODUCTION..."
echo "SSM PATH: $SSM_PATH"
echo "ENV FILE: $ENV_FILE"

if ! command -v aws >/dev/null 2>&1; then
  echo "ERROR: aws CLI not found"
  exit 1
fi

rm -f "$ENV_FILE"
touch "$ENV_FILE"
chmod 600 "$ENV_FILE"

SSM_PARAMETER_NAMES=$(aws ssm get-parameters-by-path \
  --region "$REGION" \
  --path "$SSM_PATH" \
  --recursive \
  --with-decryption \
  --query 'Parameters[].Name' \
  --output text)

for name in $SSM_PARAMETER_NAMES; do
  value=$(aws ssm get-parameter \
    --region "$REGION" \
    --name "$name" \
    --with-decryption \
    --query 'Parameter.Value' \
    --output text)

  if [ -n "$name" ] && [ -n "$value" ]; then
    key=$(echo "$name" | awk -F/ '{print $NF}')
    echo "${key}=${value}" >> "$ENV_FILE"
  fi
done

exec "$@"
