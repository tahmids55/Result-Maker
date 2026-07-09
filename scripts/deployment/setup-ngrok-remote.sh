#!/bin/bash
sudo bash -c 'cat > /etc/systemd/system/ngrok-laravel.service <<INNER_EOF
[Unit]
Description=Ngrok Laravel Tunnel
After=network-online.target

[Service]
Type=simple
User=twetser
ExecStart=/usr/local/bin/ngrok http --domain=outline-timing-posting.ngrok-free.dev --authtoken=3GHkBWQI25jplXclMNMLBwaE2Ky_6ccTB1nGBRfLqm197zyMs 80
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
INNER_EOF'

sudo bash -c 'cat > /etc/systemd/system/ngrok-office.service <<INNER_EOF
[Unit]
Description=Ngrok ONLYOFFICE Tunnel
After=network-online.target

[Service]
Type=simple
User=twetser
ExecStart=/usr/local/bin/ngrok http --domain=chest-hurried-hatless.ngrok-free.dev --authtoken=3GHjMkjZhtXCRvVPKcCDdj92nW5_5Dd9ezYZW2JdL5uNrHvMe 8088
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
INNER_EOF'

sudo systemctl daemon-reload
sudo systemctl restart ngrok-laravel
sudo systemctl restart ngrok-office

echo "----------------------------------------------------"
echo "Success! The Ngrok background services are installed and running!"
echo "Laravel URL: https://outline-timing-posting.ngrok-free.dev"
echo "ONLYOFFICE URL: https://chest-hurried-hatless.ngrok-free.dev"
echo "----------------------------------------------------"
