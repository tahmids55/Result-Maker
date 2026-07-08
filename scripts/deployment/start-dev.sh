#!/bin/bash

echo "🚀 Starting ResultMaker Development Environment..."
echo "=================================================="

# 1. Start ONLYOFFICE Docker Container
echo "📦 [1/3] Starting ONLYOFFICE Docker container..."
docker start onlyoffice-ds > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ ONLYOFFICE is running on port 8088"
else
    echo "   ❌ Failed to start ONLYOFFICE container. Is Docker running?"
fi

# 2. Start the Bridge Server (Port 8001) in the background
echo "🌉 [2/3] Starting Docker Bridge Server (Port 8001)..."
php artisan serve --host=0.0.0.0 --port=8001 > storage/logs/bridge-server.log 2>&1 &
BRIDGE_PID=$!
echo "   ✅ Bridge server running in background (PID: $BRIDGE_PID)"

# 3. Handle graceful shutdown
cleanup() {
    echo ""
    echo "🛑 Shutting down development environment..."
    echo "   Killing Bridge Server (PID: $BRIDGE_PID)..."
    kill $BRIDGE_PID
    echo "✅ Shutdown complete. Goodbye!"
    exit 0
}
# Trap Ctrl+C (SIGINT) to run the cleanup function
trap cleanup SIGINT

# 4. Start the Main Server (Port 8000) in the foreground
echo "🌐 [3/3] Starting Main Web Server (Port 8000)..."
echo "=================================================="
echo "🎯 App is ready! Press Ctrl+C to stop all servers."
php artisan serve
