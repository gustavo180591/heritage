#!/bin/bash

# Clear cache directories
echo "Clearing cache..."
rm -rf var/cache/*

# Set proper permissions
echo "Setting permissions..."
chmod -R 777 var/

# Clear PHP opcache if enabled
if [ -x "$(command -v php-7.4)" ]; then
    echo "Clearing PHP 7.4 opcache..."
    php-7.4 -r 'if (function_exists("opcache_reset")) { opcache_reset(); }'
fi

echo "Cache cleared successfully!"
