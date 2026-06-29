#!/bin/bash
set -e

mkdir -p /var/www/html/wp-content/ai1wm-backups
mkdir -p /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage
mkdir -p /var/www/html/wp-content/uploads
mkdir -p /var/www/html/wp-content/upgrade

for dir in \
  /var/www/html/wp-content/ai1wm-backups \
  /var/www/html/wp-content/uploads \
  /var/www/html/wp-content/upgrade \
  /var/www/html/wp-content/plugins \
  /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage
do
  chown -R www-data:www-data "$dir" 2>/dev/null || true
  chmod -R 775 "$dir" 2>/dev/null || true
done

if [ "$#" -eq 0 ]; then
  set -- apache2-foreground
fi

exec /usr/local/bin/docker-entrypoint.sh "$@"
