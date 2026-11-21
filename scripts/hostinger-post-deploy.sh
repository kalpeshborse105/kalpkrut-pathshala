#!/usr/bin/env bash
# Manual/SSH fallback after Hostinger Git deploy.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if [ -f "$HOME/pathshala.production.env" ] && [ ! -f .env ]; then
  cp "$HOME/pathshala.production.env" .env
fi

mkdir -p storage/framework/{cache/data,sessions,views} storage/logs storage/app/public bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache || true

if [ -d public ]; then
  rm -f public_html
  ln -sfn "$ROOT/public" "$ROOT/public_html"
fi

composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction
php scripts/hostinger-bootstrap.php
echo "Post-deploy finished."
