#!/bin/bash

# Kill existing cloudflared quick tunnels to prevent background conflicts
echo "Cleaning up any existing tunnels..."
pkill -f cloudflared

# Force working directory to the Laravel project root
cd /home/twetser/markscraft

# Start App Tunnel
echo "Starting MarksCraft application tunnel (Port 80)..."
nohup cloudflared tunnel --protocol http2 --url http://localhost:80 > storage/logs/cloudflared-app.log 2>&1 &

sleep 3 # Stagger to prevent Cloudflare rate-limiting

# Start ONLYOFFICE Tunnel
echo "Starting ONLYOFFICE Document Server tunnel (Port 8088)..."
nohup cloudflared tunnel --protocol http2 --url http://localhost:8088 > storage/logs/cloudflared-onlyoffice.log 2>&1 &

echo "Waiting for Cloudflare to assign URLs..."
APP_URL=""
OO_URL=""
MAX_RETRIES=30
RETRY_COUNT=0

while [[ -z "$APP_URL" || -z "$OO_URL" ]] && [[ $RETRY_COUNT -lt $MAX_RETRIES ]]; do
    sleep 2
    
    if [ -z "$APP_URL" ]; then
        APP_URL=$(grep -a -o 'https://[a-zA-Z0-9-]*\.trycloudflare\.com' storage/logs/cloudflared-app.log | head -n 1)
    fi
    
    if [ -z "$OO_URL" ]; then
        OO_URL=$(grep -a -o 'https://[a-zA-Z0-9-]*\.trycloudflare\.com' storage/logs/cloudflared-onlyoffice.log | head -n 1)
    fi
    
    ((RETRY_COUNT++))
done

echo ""
echo "------------------------------------------------------"
echo "🌐 PUBLIC URLS GENERATED"
echo "------------------------------------------------------"

if [ -z "$APP_URL" ]; then
    echo "🟢 MarksCraft App:     [Still generating, check storage/logs/cloudflared-app.log]"
else
    echo "🟢 MarksCraft App:     $APP_URL"
    
    # Auto-update APP_URL in .env
    sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" .env
fi

if [ -z "$OO_URL" ]; then
    echo "🟠 ONLYOFFICE Server:  [Still generating, check storage/logs/cloudflared-onlyoffice.log]"
else
    echo "🟠 ONLYOFFICE Server:  $OO_URL"
    
    # Auto-update ONLYOFFICE_URL in .env
    if grep -q "^ONLYOFFICE_URL=" .env; then
        sed -i "s|^ONLYOFFICE_URL=.*|ONLYOFFICE_URL=$OO_URL|" .env
    else
        echo "ONLYOFFICE_URL=$OO_URL" >> .env
    fi
fi

php artisan optimize:clear

echo "------------------------------------------------------"
echo "✅ URLs have been automatically injected into your .env file!"
echo "🛑 To stop the tunnels later, simply run: ./close.sh"
