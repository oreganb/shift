#!/usr/bin/env bash
# Upload /web to Blacknight FTP. Credentials in deploy.credentials (gitignored).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
CREDS="$ROOT/deploy.credentials"
WEB="$ROOT/web"

if [[ ! -f "$CREDS" ]]; then
  echo "Missing deploy.credentials — copy from deploy.credentials.example"
  exit 1
fi
# shellcheck source=/dev/null
source "$CREDS"

echo "==> Syncing content from docs/ into web/data/…"
mkdir -p "$WEB/data/ontology" "$WEB/data/vault" "$WEB/downloads"
rm -rf "$WEB/data/ontology/"* "$WEB/data/vault/"*
cp -R "$ROOT/docs/SHIFT owl/"* "$WEB/data/ontology/"
cp -R "$ROOT/docs/Reasoning Rules/"* "$WEB/data/ontology/"
unzip -qo "$ROOT/docs/SHIFT_Ontology_KG.zip" -d "$WEB/data/vault/"
cp "$ROOT/docs/SHIFT_Ontology_KG.zip" "$WEB/downloads/"

echo "==> Building JSON…"
php "$WEB/scripts/build-ontology-json.php"
php "$WEB/scripts/build-vault-json.php"
php "$WEB/scripts/build-search-index.php"

echo "==> Preparing downloads…"
mkdir -p "$WEB/downloads"

if ! command -v lftp &>/dev/null; then
  echo "lftp required: brew install lftp"
  exit 1
fi

echo "==> Uploading to $FTP_HOST ($FTP_DIR)…"
lftp -u "$FTP_USER","$FTP_PASS" "$FTP_HOST" <<EOF
set ssl:verify-certificate no
set ftp:passive-mode true
cd $FTP_DIR
mirror --reverse --verbose \
  --exclude-glob .DS_Store \
  --exclude-glob includes-template/ \
  "$WEB" .
bye
EOF

echo "==> Deploy complete: https://shift.ultrasoftware.ie/"
echo "    Run install once: install.php?key=shift-install-<see install.php>"
