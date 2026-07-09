#!/bin/bash

# Force working directory to the Laravel project root
cd /home/twetser/markscraft

echo "Cleaning up any old Cloudflare tunnels..."
pkill -f cloudflared

echo "Starting Persistent Ngrok Tunnels..."
sudo systemctl restart ngrok-laravel
sudo systemctl restart ngrok-office

echo ""
echo "------------------------------------------------------"
echo "🌐 PUBLIC URLS GENERATED (STATIC & PERMANENT)"
echo "------------------------------------------------------"
echo "🟢 MarksCraft App:     https://outline-timing-posting.ngrok-free.dev"
echo "🟠 ONLYOFFICE Server:  https://chest-hurried-hatless.ngrok-free.dev"

# Auto-update APP_URL in .env
sed -i "s|^APP_URL=.*|APP_URL=https://outline-timing-posting.ngrok-free.dev|" .env

# Auto-update ONLYOFFICE_URL in .env
if grep -q "^ONLYOFFICE_URL=" .env; then
    sed -i "s|^ONLYOFFICE_URL=.*|ONLYOFFICE_URL=https://chest-hurried-hatless.ngrok-free.dev|" .env
else
    echo "ONLYOFFICE_URL=https://chest-hurried-hatless.ngrok-free.dev" >> .env
fi

# Clear cache to apply new URLs
php artisan optimize:clear

echo "------------------------------------------------------"
echo "✅ URLs are locked in. Your server is live on the internet!"
echo "🛑 To stop the tunnels, simply run: sudo systemctl stop ngrok-laravel ngrok-office"
