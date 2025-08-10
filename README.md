# Scholarships Monitor (Laravel)

This repository now serves a Laravel app backed by SQLite. The Python pipeline writes `data/detections.csv`, and Laravel imports it into the `detections` table.

## Included
- `laravel-app/` full Laravel project
- Console command `detections:import` to load CSV into SQLite
- Blade UI for server-rendered pages (no API required)

## Data and pipeline (excluded)
- `data/` (sources, detections, cron files, logs)
- Python scripts and virtualenvs

## Local development
- Requirements: PHP 8.2+, Composer
- Create the SQLite file at `laravel-app/database/database.sqlite` and configure `.env` for SQLite
- Migrate and import data:
  - `php artisan migrate --ansi`
  - `php artisan detections:import ../data/detections.csv --truncate`
- Serve the app:
  - `php artisan serve`

## Notes
- The pipeline should produce/clean `data/detections.csv` (no longer using `web/`).
- You can schedule periodic imports via cron or Laravel scheduler.

