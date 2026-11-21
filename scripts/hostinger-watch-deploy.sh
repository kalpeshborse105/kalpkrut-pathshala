#!/usr/bin/env bash
# Cron-friendly post-publish bootstrap for Hostinger Git auto-deploy.
# Example cron (every minute):
# * * * * * /home/u796881501/domains/pathshala.kalpkrutinfotech.com/public_html/scripts/hostinger-watch-deploy.sh >/dev/null 2>&1
set -euo pipefail

APP="${HOME}/domains/pathshala.kalpkrutinfotech.com/public_html"
STAMP_FILE="${HOME}/.pathshala_last_bootstrap"
LOG_FILE="${HOME}/.pathshala_bootstrap.log"

if [ ! -f "${APP}/artisan" ]; then
  exit 0
fi

# Detect newest published file change
CURRENT="$(stat -c %Y "${APP}/composer.lock" 2>/dev/null || echo 0)-$(stat -c %Y "${APP}/.htaccess" 2>/dev/null || echo 0)"
PREVIOUS="$(cat "${STAMP_FILE}" 2>/dev/null || true)"

if [ "${CURRENT}" = "${PREVIOUS}" ]; then
  exit 0
fi

{
  echo "[$(date -Is)] Detected publish change, bootstrapping..."
  if [ -f "${HOME}/pathshala.production.env" ] && [ ! -f "${APP}/.env" ]; then
    cp -a "${HOME}/pathshala.production.env" "${APP}/.env"
  fi
  mkdir -p "${APP}/storage/framework/cache/data" \
           "${APP}/storage/framework/sessions" \
           "${APP}/storage/framework/views" \
           "${APP}/storage/logs" \
           "${APP}/storage/app/public" \
           "${APP}/bootstrap/cache"
  chmod -R ug+rwx "${APP}/storage" "${APP}/bootstrap/cache" || true
  cd "${APP}"
  php scripts/hostinger-bootstrap.php
  # Keep durable env backup
  if [ -f "${APP}/.env" ]; then
    cp -a "${APP}/.env" "${HOME}/pathshala.production.env"
  fi
  echo "${CURRENT}" > "${STAMP_FILE}"
  echo "[$(date -Is)] Bootstrap done"
} >> "${LOG_FILE}" 2>&1
