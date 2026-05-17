# Deploying KukiTales to Bunny Magic Containers

Full-stack deploy: PHP app + MariaDB + global edge distribution, all on Bunny.

## What gets deployed

```
┌─────────────────────────────────────────────────────┐
│  Bunny Magic Container — your KukiTales app         │
│  • PHP 8.4-FPM + Nginx + Supervisor in one image    │
│  • Auto-runs migrations on boot                     │
│  • Auto-generates APP_KEY                           │
│  • Globally distributed across Bunny's PoPs         │
├─────────────────────────────────────────────────────┤
│  Bunny Managed MariaDB                              │
│  • Persistent storage, no setup                     │
│  • Connects internally via 127.0.0.1                │
└─────────────────────────────────────────────────────┘
```

## One-time setup

### 1. Push the image to GitHub Container Registry

After the latest commit, GitHub Actions automatically builds and pushes the
Docker image to `ghcr.io/moldokipgen23/kukitales:latest`.

Confirm: GitHub repo → **Actions** tab → **Build & push Bunny container image**
workflow → green checkmark.

**Make the package public** (one-time):
- GitHub → your profile → **Packages** → click `kukitales`
- **Package settings** → **Change package visibility** → **Public**

(Otherwise Bunny can't pull it without auth — public is fine for the container image; the .env secrets stay private in Bunny.)

### 2. Deploy in Bunny dashboard

1. Bunny dashboard → **Magic Containers** → **+ New App**
2. **App name**: `kukitales`
3. **Deployment type**: **Magic deployment** (auto-distributes globally)

### 3. Add the App Container (your PHP app)

- Click **+ Add Container** → **Custom container**
- **Container name**: `app`
- **Image**: `ghcr.io/moldokipgen23/kukitales:latest`
- **Port**: `8080`
- **Endpoint**: enable HTTP endpoint → assign hostname

**Environment variables** (paste in Variables tab):
```env
APP_NAME=KukiTales
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Kolkata
APP_URL=https://your-bunny-hostname.b-cdn.net
APP_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kukitales
DB_USERNAME=kukitales
DB_PASSWORD=<choose-a-strong-password>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

BCRYPT_ROUNDS=12
```

Leave `APP_KEY` empty — the entrypoint auto-generates one on first boot.

### 4. Add the Database Container (MariaDB sidecar)

- Click **+ Add Container** → **Database** → **MariaDB 11**
- **Container name**: `db`
- **Database name**: `kukitales`
- **Username**: `kukitales`
- **Password**: same as `DB_PASSWORD` you set above
- **Persistent volume**: ✅ enabled (so data survives restarts)

The two containers share `localhost` — that's why we use `DB_HOST=127.0.0.1`.

### 5. Deploy

- Click **Deploy** at the top
- First boot takes ~60 seconds (image pull, then migrations + seeds run via entrypoint.sh)
- Visit the assigned hostname → site loads, admin auto-seeded

Login at `/admin`:
- Email: `admin@kukitales.com`
- Password: `password`
- **Change immediately.**

## Updating

Push to GitHub `main` → Actions rebuilds the image → in Bunny dashboard click
**Redeploy** on your app. Migrations run automatically on restart.

## Cost (rough)

- App container (smallest): ~$8–10/month
- MariaDB sidecar: ~$5/month
- Storage (10GB): ~$0.20/month
- Bandwidth: ~$0.005/GB

Typical low-traffic news site: **~$13–18/month**, scaling with traffic.

## Local testing of the image

```bash
docker build -f bunny/Dockerfile -t kukitales-bunny .
docker run --rm -p 8080:8080 \
  -e APP_URL=http://localhost:8080 \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=host.docker.internal \
  -e DB_PORT=3306 \
  -e DB_DATABASE=kukitales \
  -e DB_USERNAME=root \
  -e DB_PASSWORD=secret \
  kukitales-bunny
```

Visit http://localhost:8080.

## Files in this directory

- `Dockerfile` — multi-stage build (composer → runtime image)
- `docker/nginx.conf` — Nginx serves on port 8080
- `docker/php-fpm.conf` — PHP-FPM tuning
- `docker/supervisord.conf` — runs Nginx + PHP-FPM together
- `docker/entrypoint.sh` — boot-time setup (DB wait, migrate, seed, cache, link)
