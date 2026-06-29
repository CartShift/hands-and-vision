#!/bin/sh
set -e

SITE_URL="${WP_SITE_URL:-http://127.0.0.1:8888}"

chown -R www-data:www-data /var/www/html/wp-content

if ! wp core is-installed --allow-root 2>/dev/null; then
  wp core install \
    --url="$SITE_URL" \
    --title="Hands and Vision" \
    --admin_user="admin" \
    --admin_password="password" \
    --admin_email="dev@localhost.local" \
    --skip-email \
    --allow-root
fi

wp core update --allow-root

wp plugin install advanced-custom-fields --activate --force --allow-root
wp plugin install woocommerce --activate --force --allow-root
wp plugin install all-in-one-wp-migration --activate --force --allow-root
wp theme activate hands-and-vision --allow-root

mkdir -p /var/www/html/wp-content/ai1wm-backups
mkdir -p /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage
chown -R www-data:www-data /var/www/html/wp-content/ai1wm-backups
chown -R www-data:www-data /var/www/html/wp-content/plugins
chown -R www-data:www-data /var/www/html/wp-content/uploads
chmod -R 775 /var/www/html/wp-content/ai1wm-backups
chmod -R 775 /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage

echo ""
echo "WordPress is ready:"
echo "  Site:  $SITE_URL"
echo "  Admin: $SITE_URL/wp-admin"
echo "  User:  admin / password"
