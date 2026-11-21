#!/usr/bin/env bash
# Production deploy for NQ Baccarat. Run ON the server:
#
#   ./deploy.sh              # pull + migrate + rebuild caches
#   ./deploy.sh SomeSeeder   # …and re-run one specific seeder
#   ./deploy.sh --seed       # …and re-run EVERY seeder (confirms first)

set -euo pipefail
cd "$(dirname "$0")"

echo "→ git pull"
LOCK_BEFORE=$(git rev-parse HEAD:composer.lock 2>/dev/null || echo none)
git pull
LOCK_AFTER=$(git rev-parse HEAD:composer.lock 2>/dev/null || echo none)

if [ "$LOCK_BEFORE" != "$LOCK_AFTER" ]; then
    if command -v composer >/dev/null 2>&1; then
        echo "→ composer install (lockfile changed)"
        composer install --no-dev --optimize-autoloader --no-interaction
    else
        echo "⚠ composer.lock changed but composer is not on PATH."
        echo "  Install dependencies manually before trusting this deploy."
    fi
fi

echo "→ migrate"
php artisan migrate --force

if [ "${1:-}" = "--seed" ]; then
    echo "⚠ This re-runs EVERY seeder."
    read -r -p "Type 'yes' to continue: " confirm
    if [ "$confirm" = "yes" ]; then
        echo "→ db:seed (all)"
        php artisan db:seed --force
    else
        echo "→ skipped seeding"
    fi
elif [ "${1:-}" != "" ]; then
    echo "→ db:seed --class=$1"
    php artisan db:seed --class="$1" --force
fi

echo "→ cache config/routes/views"
php artisan optimize

echo "→ storage link"
php artisan storage:link 2>/dev/null || true

echo "✓ deployed $(git log --oneline -1)"
