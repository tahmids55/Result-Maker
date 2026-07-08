#!/bin/bash

# Clear the screen for a clean menu
clear

echo "==============================================="
echo "   MarksCraft Server Tunnel Manager"
echo "==============================================="
echo ""
echo "What would you like to do?"
echo ""
echo "  1) 🌐 Expose to Internet (Start Tunnels)"
echo "  2) 🛑 Close Internet Access (Stop Tunnels)"
echo "  3) 🔍 Check Active Tunnel URLs"
echo "  4) ❌ Exit"
echo ""
read -p "Select an option (1-4): " choice

case $choice in
    1)
        echo ""
        echo "🚀 Connecting to your Ubuntu Server to START Cloudflare Tunnels..."
        ssh -t twetser@192.168.0.146 "cd /home/twetser/markscraft && sudo scripts/deployment/expose-website.sh"
        ;;
    2)
        echo ""
        echo "🛑 Connecting to your Ubuntu Server to STOP Cloudflare Tunnels..."
        ssh -t twetser@192.168.0.146 "cd /home/twetser/markscraft && sudo scripts/deployment/close.sh"
        ;;
    3)
        echo ""
        echo "🔍 Checking Active URLs on Ubuntu Server..."
        ssh -t twetser@192.168.0.146 "cd /home/twetser/markscraft && echo '🟢 MarksCraft App:     ' \$(grep -a -o 'https://[a-zA-Z0-9-]*\.trycloudflare\.com' storage/logs/cloudflared-app.log | head -n 1) && echo '🟠 ONLYOFFICE Server:  ' \$(grep -a -o 'https://[a-zA-Z0-9-]*\.trycloudflare\.com' storage/logs/cloudflared-onlyoffice.log | head -n 1)"
        ;;
    4)
        echo "Goodbye!"
        exit 0
        ;;
    *)
        echo "❌ Invalid option selected. Please run the script again."
        exit 1
        ;;
esac
