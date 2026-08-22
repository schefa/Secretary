#!/bin/bash
# Idempotently brings the "dev" site (joomla-dev.localhost) up to a working
# state after `docker compose up`: creates its database if missing, installs
# the Joomla core schema if missing, then discovers and installs com_secretary
# and mod_secretary_dashboard via Joomla's own extension installer (which runs
# their install.mysql.sql for us). Safe to re-run; every step is skipped once
# already done.
set -euo pipefail

cd "$(dirname "$0")/.."

MYSQL_CONTAINER="secretary_mysql"
DEV_CONTAINER="secretary_dev"
CONFIG_FILE="joomla-dev/configuration.php"
DEV_DB_PREFIX="j6_"
JOOMLA_BASE_IMAGE="joomla-base:6.1.3"
NETWORK="secretary"

# All credentials are sourced from the environment (the Makefile exports its
# defaults / `make up VAR=...` overrides); the literals here are only the
# fallback if this script is run directly without going through `make up`.
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-password}"
DEV_DB_NAME="${DEV_DB_NAME:-dev}"
DEV_DB_USER="${DEV_DB_USER:-dev}"
DEV_DB_PASS="${DEV_DB_PASSWORD:-root}"
ADMIN_USERNAME="${SECRETARY_DEV_ADMIN_USERNAME:-admin}"
ADMIN_PASSWORD="${SECRETARY_DEV_ADMIN_PASSWORD:-Secretary123!}"
ADMIN_EMAIL="${SECRETARY_DEV_ADMIN_EMAIL:-admin@example.com}"

mysql_root() {
	docker exec -i "$MYSQL_CONTAINER" mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$@"
}

echo "==> Waiting for MySQL to become healthy..."
until [ "$(docker inspect -f '{{.State.Health.Status}}' "$MYSQL_CONTAINER" 2>/dev/null)" = "healthy" ]; do
	sleep 2
done

echo "==> Ensuring '${DEV_DB_NAME}' database and user exist with the current password..."
mysql_root -e "
CREATE DATABASE IF NOT EXISTS \`${DEV_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DEV_DB_USER}'@'%' IDENTIFIED BY '${DEV_DB_PASS}';
ALTER USER '${DEV_DB_USER}'@'%' IDENTIFIED BY '${DEV_DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DEV_DB_NAME}\`.* TO '${DEV_DB_USER}'@'%';
FLUSH PRIVILEGES;
"

NEEDS_RESTART=0
CONFIG_BEFORE="$(shasum "$CONFIG_FILE" | awk '{print $1}')"
perl -pi -e "s/(public \\\$user = ')[^']*(')/\${1}${DEV_DB_USER}\${2}/" "$CONFIG_FILE"
perl -pi -e "s/(public \\\$password = ')[^']*(')/\${1}${DEV_DB_PASS}\${2}/" "$CONFIG_FILE"
perl -pi -e "s/(public \\\$db = ')[^']*(')/\${1}${DEV_DB_NAME}\${2}/" "$CONFIG_FILE"
CONFIG_AFTER="$(shasum "$CONFIG_FILE" | awk '{print $1}')"
if [ "$CONFIG_BEFORE" != "$CONFIG_AFTER" ]; then
	echo "==> ${CONFIG_FILE} credentials updated to match."
	NEEDS_RESTART=1
fi

CORE_INSTALLED=$(mysql_root -N -e \
	"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DEV_DB_NAME}' AND table_name='${DEV_DB_PREFIX}users';")

if [ "$CORE_INSTALLED" = "0" ]; then
	echo "==> Installing Joomla core schema into '${DEV_DB_NAME}'..."
	docker run --rm --network "$NETWORK" "$JOOMLA_BASE_IMAGE" \
		php installation/joomla.php install \
		--site-name="Secretary Dev" \
		--admin-user="Administrator" \
		--admin-username="$ADMIN_USERNAME" \
		--admin-password="$ADMIN_PASSWORD" \
		--admin-email="$ADMIN_EMAIL" \
		--db-type=mysqli --db-host=mysql --db-user="$DEV_DB_USER" \
		--db-pass="$DEV_DB_PASS" --db-name="$DEV_DB_NAME" --db-prefix="$DEV_DB_PREFIX" \
		-n
	NEEDS_RESTART=1
else
	echo "==> Joomla core already installed in '${DEV_DB_NAME}', skipping."
fi

echo "==> Syncing '${ADMIN_USERNAME}' password..."
ADMIN_HASH="$(docker exec -e ADMIN_PASSWORD="$ADMIN_PASSWORD" "$DEV_CONTAINER" \
	php -r 'echo password_hash(getenv("ADMIN_PASSWORD"), PASSWORD_DEFAULT);')"
mysql_root -N -e \
	"UPDATE \`${DEV_DB_NAME}\`.\`${DEV_DB_PREFIX}users\` SET password = '${ADMIN_HASH}' WHERE username = '${ADMIN_USERNAME}';"

if [ "$NEEDS_RESTART" = "1" ]; then
	echo "==> Restarting dev container to pick up database/config changes..."
	docker compose -f compose.yml restart dev
	sleep 5
fi

echo "==> Checking com_secretary installation..."
INSTALLED_EXTENSIONS="$(docker exec "$DEV_CONTAINER" php cli/joomla.php extension:list 2>/dev/null)"
if grep -qi com_secretary <<<"$INSTALLED_EXTENSIONS"; then
	echo "==> com_secretary already installed, skipping."
else
	echo "==> Discovering and installing com_secretary + dashboard module..."
	docker exec "$DEV_CONTAINER" php cli/joomla.php extension:discover -n
	docker exec "$DEV_CONTAINER" php cli/joomla.php extension:discover:install -n
fi

echo "==> Dev site ready: http://joomla-dev.localhost/ (username: ${ADMIN_USERNAME}, password: ${ADMIN_PASSWORD})"
