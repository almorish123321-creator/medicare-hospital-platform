#!/bin/bash
set -e

# ─── Coloured output helpers ───────────────────────────────────────────
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Medicare Pro - Docker Entrypoint${NC}"
echo -e "${GREEN}========================================${NC}"

# ─── 1. Wait for MySQL to be ready ─────────────────────────────────────
echo -e "${YELLOW}[Entrypoint] Waiting for MySQL...${NC}"
until php artisan tinker --execute="echo DB::connection()->getPdo();" > /dev/null 2>&1; do
    echo -e "${YELLOW}[Entrypoint] MySQL is not ready yet. Retrying in 3 seconds...${NC}"
    sleep 3
done
echo -e "${GREEN}[Entrypoint] MySQL is ready!${NC}"

# ─── 2. Run Migrations ────────────────────────────────────────────────
echo -e "${YELLOW}[Entrypoint] Running database migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}[Entrypoint] Migrations completed.${NC}"

# ─── 3. Run Seeders (local environment only) ──────────────────────────
if [ "$APP_ENV" = "local" ]; then
    echo -e "${YELLOW}[Entrypoint] Running database seeders (env=local)...${NC}"
    php artisan db:seed --force --env=local
echo -e "${GREEN}[Entrypoint] Seeders completed.${NC}"
fi

# ─── 4. Generate Swagger Documentation ─────────────────────────────────
echo -e "${YELLOW}[Entrypoint] Generating Swagger documentation...${NC}"
php artisan l5-swagger:generate
echo -e "${GREEN}[Entrypoint] Swagger docs generated.${NC}"

# ─── 5. Clear & Cache Configs ──────────────────────────────────────────
echo -e "${YELLOW}[Entrypoint] Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}[Entrypoint] Application optimized.${NC}"

# ─── 6. Ensure storage permissions ─────────────────────────────────────
echo -e "${YELLOW}[Entrypoint] Setting storage permissions...${NC}"
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─── 7. Start PHP-FPM ──────────────────────────────────────────────────
echo -e "${GREEN}[Entrypoint] Starting PHP-FPM...${NC}"
echo -e "${GREEN}========================================${NC}"

# Execute the CMD (php-fpm)
exec "$@"
