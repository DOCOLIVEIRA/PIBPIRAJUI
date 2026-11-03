#!/bin/sh
# Configura o cron para rodar todos os dias às 06:00 (horário de Brasília) e faz uma atualização inicial
chmod +x /usr/share/nginx/html/scripts/update-youtube.sh

echo "0 6 * * * /usr/share/nginx/html/scripts/update-youtube.sh >> /var/log/youtube-sync.log 2>&1" > /etc/crontabs/root

crond -b -l 8

/usr/share/nginx/html/scripts/update-youtube.sh >> /var/log/youtube-sync.log 2>&1 &
