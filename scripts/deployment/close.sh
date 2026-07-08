#!/bin/bash

echo "🔴 Shutting down MarksCraft services..."

# Stop the Laravel Queue Worker
echo "Stopping Queue Worker..."
pkill -f "php artisan queue:work"
if [ $? -eq 0 ]; then
    echo "✅ Queue Worker stopped."
else
    echo "⚠️ No Queue Worker was running."
fi

# Stop Cloudflare Tunnels
echo "Stopping Cloudflare Tunnels..."
pkill -f cloudflared
if [ $? -eq 0 ]; then
    echo "✅ Cloudflare Tunnels stopped."
    # Clean up logs so old URLs don't show up in status checks
    rm -f /home/twetser/markscraft/storage/logs/cloudflared-app.log
    rm -f /home/twetser/markscraft/storage/logs/cloudflared-onlyoffice.log
else
    echo "⚠️ No Cloudflare Tunnels were running."
fi

# Optional: Stop ONLYOFFICE Docker Container (Uncomment if you want this to stop automatically too)
# echo "Stopping ONLYOFFICE Document Server..."
# docker stop onlyoffice-ds
# echo "✅ ONLYOFFICE Server stopped."

echo "🎉 All requested services have been shut down!"
