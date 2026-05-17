#!/bin/bash
# Build a "Codecanyon-style" upload-and-go release zip for cPanel.
# Output: releases/kukitales-vX.Y.Z.zip — contains vendor/ pre-built,
# ready to upload + extract on shared hosting. No composer needed.
#
# Usage:  ./build-release.sh [version]
# Example: ./build-release.sh 1.0.0

set -e

VERSION="${1:-$(date +%Y%m%d-%H%M%S)}"
NAME="kukitales-v${VERSION}"
OUT_DIR="releases"
WORK_DIR="${OUT_DIR}/${NAME}"
ZIP_PATH="${OUT_DIR}/${NAME}.zip"

echo "════════════════════════════════════════════"
echo " Building release: ${NAME}"
echo "════════════════════════════════════════════"

# 1. Fresh production composer install (no dev deps)
echo "==> [1/5] Installing production composer dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Stage files
echo "==> [2/5] Staging release files"
rm -rf "${WORK_DIR}" "${ZIP_PATH}"
mkdir -p "${WORK_DIR}"

# Copy everything except dev-only stuff
rsync -a \
  --exclude='.git/' \
  --exclude='.github/' \
  --exclude='.claude/' \
  --exclude='node_modules/' \
  --exclude='releases/' \
  --exclude='tests/' \
  --exclude='.env' \
  --exclude='.env.backup' \
  --exclude='.phpunit.cache/' \
  --exclude='.phpunit.result.cache' \
  --exclude='storage/installed.lock' \
  --exclude='storage/logs/*.log' \
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='storage/app/public/*' \
  --exclude='database/database.sqlite' \
  --exclude='_reference/' \
  --exclude='build-release.sh' \
  --exclude='Procfile' \
  --exclude='start.sh' \
  --exclude='nixpacks.toml' \
  --exclude='railway.json' \
  --exclude='*.zip' \
  --exclude='.DS_Store' \
  ./ "${WORK_DIR}/"

# 3. Ensure storage subdirectories exist with .gitkeep so permissions work
echo "==> [3/5] Preparing storage skeleton"
mkdir -p "${WORK_DIR}/storage/framework/cache/data"
mkdir -p "${WORK_DIR}/storage/framework/sessions"
mkdir -p "${WORK_DIR}/storage/framework/views"
mkdir -p "${WORK_DIR}/storage/logs"
mkdir -p "${WORK_DIR}/storage/app/public"
touch "${WORK_DIR}/storage/framework/cache/data/.gitkeep"
touch "${WORK_DIR}/storage/framework/sessions/.gitkeep"
touch "${WORK_DIR}/storage/framework/views/.gitkeep"
touch "${WORK_DIR}/storage/logs/.gitkeep"
touch "${WORK_DIR}/storage/app/public/.gitkeep"

# 4. Write a fresh INSTALL.txt at the root
echo "==> [4/5] Writing INSTALL.txt"
cat > "${WORK_DIR}/INSTALL.txt" <<'EOF'
╔══════════════════════════════════════════════════════════════════╗
║                  KukiTales — cPanel Installation                 ║
║                     Zero-config, 3 steps                         ║
╚══════════════════════════════════════════════════════════════════╝

STEP 1 — Upload & extract
─────────────────────────
  • cPanel → File Manager
  • Upload this zip into your home folder (NOT public_html)
  • Right-click → Extract → into a folder like  ~/kukitales

  Then point your domain's document root to the public/ folder:
    Option A (recommended): cPanel → Subdomains → create
      "kukitales.yourdomain.com" with document root
      /home/youruser/kukitales/public
    Option B: cPanel → Domains → set your main domain's
      document root to /home/youruser/kukitales/public

STEP 2 — Create the database
────────────────────────────
  • cPanel → MySQL® Databases
  • Create a new database (e.g. "kukitales")
  • Create a new MySQL user (with a strong password)
  • Add the user to the database with ALL PRIVILEGES
  • Note down the 3 values: database name, user, password

STEP 3 — Set credentials
────────────────────────
  • In cPanel → File Manager, open the kukitales folder
  • Rename ".env.example" to ".env"
  • Edit ".env" — set these 4 lines to YOUR values:

      APP_URL=https://yourdomain.com
      DB_DATABASE=yourcpanel_kukitales
      DB_USERNAME=yourcpanel_dbuser
      DB_PASSWORD=YourStrongPasswordHere

  • Save.

STEP 4 — Visit your site
────────────────────────
  Open https://yourdomain.com in your browser.

  The very first request will automatically:
    ✓ Create all 14 database tables
    ✓ Seed news/folktale categories
    ✓ Create the default admin account
    ✓ Lock the installer so it can't run twice

  Default admin login: https://yourdomain.com/admin
    Email:    admin@kukitales.com
    Password: password
  ⚠ Change this password immediately under People → Staff & Authors.

──────────────────────────────────────────────────────────────────────
SUPPORT
  Documentation: see README.md in this folder
  GitHub:        https://github.com/moldokipgen23/kukitales
──────────────────────────────────────────────────────────────────────
EOF

# 5. Zip it
echo "==> [5/5] Creating zip"
cd "${OUT_DIR}"
zip -rq "${NAME}.zip" "${NAME}/"
cd ..
SIZE=$(du -h "${ZIP_PATH}" | cut -f1)

echo ""
echo "════════════════════════════════════════════"
echo " ✓ Release ready"
echo "════════════════════════════════════════════"
echo " File: ${ZIP_PATH}"
echo " Size: ${SIZE}"
echo ""
echo " Upload this single zip to cPanel's File Manager,"
echo " extract, then follow INSTALL.txt (3 steps)."
echo "════════════════════════════════════════════"
