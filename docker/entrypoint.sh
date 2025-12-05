set -e

# This is the script for loading environment variables from AWS SSM Parameter Store

# Path
SSM_PATH="/facefinder/"

# Fetch parameters
PARAMS=$(aws ssm get-parameters-by-path \
    --path "$SSM_PATH" \
    --with-decryption \
    --query "Parameters[*].[Name,Value]" \
    --output text)

# Creating .env file inside container

ENV_FILE="/var/www/html/.env"
rm -f $ENV_FILE
touch $ENV_FILE

# Convert SSM results into KEY=VALUE format
while IFS=$'\t' read -r name value; do
    key=$(basename "$name")
    echo "$key=$value" >> $ENV_FILE
done <<< "$PARAMS"

# End of script

exec "$@"