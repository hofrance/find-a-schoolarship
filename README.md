# Scholarships Monitor (Web)

This repository publishes only the `web/` site (PHP frontend + assets). The discovery/detection pipeline and data are excluded from version control.

## Included
- `web/` PHP app
- Security headers in `index.php`
- `futuristic-style.css` theme
- `api.php` for server-side DataTables

## Excluded (via .gitignore)
- `data/` (sources, detections, cron files, logs)
- Python scripts and virtualenvs
- Local caches, logs, IDE files

## Deploy locally
- Requires PHP 8+
- Serve `web/` (e.g., `php -S 127.0.0.1:8000 -t web`)

## Deploy to Hostinger (public_html)
Upload only what’s inside `web/` into `public_html/`:
- `index.php`, `api.php`
- `futuristic-style.css`, `futuristic-ui.js`
- `detections.csv` (place it next to `index.php`)

Steps:
1. Generate the latest `web/detections.csv` locally (pipeline writes here).
2. Open Hostinger File Manager or FTP and upload all files from `web/` to `public_html/`.
3. Ensure `index.php` and `api.php` sit directly under `public_html/`.
4. Test your domain. If CSP blocks CDNs, relax or remove the CSP header in `index.php`.

