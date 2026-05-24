#!/usr/bin/env bash
set -euo pipefail

WP_PATH="${WP_PATH:-/app/wordpress}"
SITE_URL="${SITE_URL:-https://generalwordpressinstructions.lndo.site}"
SITE_TITLE="${SITE_TITLE:-WordPress-ohjeet}"
SITE_TAGLINE="${SITE_TAGLINE:-Selkeä ohjekirjasto}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-admin}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"

if [ ! -f "${WP_PATH}/wp-load.php" ]; then
    wp core download --path="${WP_PATH}" --force
fi

if [ ! -f "${WP_PATH}/wp-config.php" ]; then
    wp config create \
        --path="${WP_PATH}" \
        --dbname=wordpress \
        --dbuser=wordpress \
        --dbpass=wordpress \
        --dbhost=database \
        --skip-check
fi

if ! wp core is-installed --path="${WP_PATH}" >/dev/null 2>&1; then
    wp core install \
        --path="${WP_PATH}" \
        --url="${SITE_URL}" \
        --title="${SITE_TITLE}" \
        --admin_user="${ADMIN_USER}" \
        --admin_password="${ADMIN_PASSWORD}" \
        --admin_email="${ADMIN_EMAIL}"
fi

wp option update blogname "${SITE_TITLE}" --path="${WP_PATH}"
wp option update blogdescription "${SITE_TAGLINE}" --path="${WP_PATH}"
wp plugin activate general-wp-instructions --path="${WP_PATH}"
wp theme activate instruction-manual --path="${WP_PATH}"
wp rewrite structure '/%postname%/' --path="${WP_PATH}"
wp rewrite flush --path="${WP_PATH}"
wp gwi ensure-screenshot-users --password="${ADMIN_PASSWORD}" --path="${WP_PATH}"

echo "WordPress instruction site is ready at ${SITE_URL}"
echo "Screenshot capture login: WP_USER=maria.korhonen WP_PASSWORD=${ADMIN_PASSWORD}"
