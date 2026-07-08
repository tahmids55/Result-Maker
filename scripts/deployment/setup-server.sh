#!/bin/bash
echo "🚀 Starting MarksCraft Server Setup..."

# Ensure the script is run as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run as root (using sudo)"
  exit
fi

export DEBIAN_FRONTEND=noninteractive

echo "📦 Updating system packages..."
apt update && apt upgrade -y

echo "🛠️ Installing prerequisites..."
apt install -y software-properties-common ca-certificates lsb-release apt-transport-https curl unzip supervisor sqlite3 nginx

echo "🐘 Adding PHP 8.3 repository..."
add-apt-repository ppa:ondrej/php -y
apt update

echo "🐘 Installing PHP 8.3..."
apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common php8.3-sqlite3 php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath

echo "🎼 Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

echo "🐳 Installing Docker for ONLYOFFICE..."
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    usermod -aG docker twetser
fi

echo "🐳 Starting ONLYOFFICE Document Server..."
if ! docker ps | grep onlyoffice-ds > /dev/null; then
    docker run -i -t -d -p 8088:80 --restart always --name onlyoffice-ds -e JWT_SECRET=super_secret_jwt_key_123_must_be_at_least_32_chars onlyoffice/documentserver
fi

echo "🔑 Setting up Laravel environment & permissions..."
cd /home/twetser/markscraft

# Copy .env if not exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Run Composer Install (just in case)
sudo -u twetser composer install --optimize-autoloader --no-dev

# Clear and Cache Laravel (Running as twetser)
sudo -u twetser php artisan key:generate
sudo -u twetser php artisan storage:link
sudo -u twetser touch database/database.sqlite
sudo -u twetser php artisan migrate --force
sudo -u twetser php artisan optimize:clear
sudo -u twetser php artisan config:cache
sudo -u twetser php artisan view:cache

# Set permissions
chown -R www-data:www-data /home/twetser/markscraft
usermod -aG www-data twetser
chmod -R 775 /home/twetser/markscraft/storage
chmod -R 775 /home/twetser/markscraft/bootstrap/cache
chmod 664 /home/twetser/markscraft/database/database.sqlite

echo "🌐 Configuring Nginx Web Server..."
cat << 'EOF' > /etc/nginx/sites-available/markscraft
server {
    listen 80;
    server_name _;
    root /home/twetser/markscraft/public;

    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/markscraft /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
systemctl restart nginx

echo "⚙️ Configuring Supervisor for Background Worker..."
cat << 'EOF' > /etc/supervisor/conf.d/markscraft-worker.conf
[program:markscraft-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/twetser/markscraft/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/home/twetser/markscraft/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start markscraft-worker:*

echo "🎉 DEPLOYMENT COMPLETE! 🎉"
echo "You can now access your server at: http://192.168.0.146"
echo "ONLYOFFICE Document Server is running at: http://192.168.0.146:8088"
