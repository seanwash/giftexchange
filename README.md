# Gift Exchange

A Laravel-based Secret Santa organizer that makes gift exchanges simple and fun. Create events, invite participants, and automatically draw names with exclusions support.

## Features

- **No accounts required** - Token-based access for organizers and participants
- **Automatic name drawing** - Creates circular assignments ensuring everyone gives and receives
- **Exclusions support** - Prevent specific participants from being matched
- **Wish lists** - Participants can share interests to help their Secret Santa
- **Interactive reveal** - Fun spinning wheel animation to reveal assignments
- **Event management** - Set event dates, themes, and gift amount limits

## How It Works

1. **Create an event** - Set up your gift exchange with details and preferences
2. **Add participants** - Each participant receives a unique access link
3. **Enter interests** - Participants share their wish lists
4. **Automatic drawing** - Once everyone is ready, names are drawn automatically
5. **View assignments** - Each participant sees who they're giving to via their unique link

## Tech Stack

- Laravel 12
- PHP 8.4
- SQLite (default)
- Tailwind CSS 4
- Alpine.js 3
- Pest (testing)

## Railway Deployment

This application is configured for deployment on [Railway](https://railway.com) using [Railpack](https://railpack.com), which automatically detects and configures Laravel applications with FrankenPHP.

### Quick Deploy

1. Create a new project on Railway
2. Deploy from GitHub repo or use Railway CLI
3. Add a PostgreSQL database service
4. Configure environment variables (see below)
5. Deploy - Railpack will automatically:
   - Detect PHP 8.4 from `composer.json`
   - Install Composer and NPM dependencies
   - Run database migrations
   - Optimize Laravel caches (config, routes, views, events)
   - Configure the application server

### Environment Variables

Set these environment variables in Railway:

**Required:**
- `APP_KEY` - Generate with `php artisan key:generate`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` - Railway will provide this automatically
- `DB_CONNECTION=pgsql`
- `DB_URL=${{Postgres.DATABASE_URL}}` - References your PostgreSQL service
- `LOG_CHANNEL=stderr` - Required for Railway's ephemeral filesystem

**Optional (for structured logs):**
- `LOG_STDERR_FORMATTER=\Monolog\Formatter\JsonFormatter`

**If using queues:**
- `QUEUE_CONNECTION=database`

**Railpack Configuration:**
- `RAILPACK_SKIP_MIGRATIONS=true` - Disable automatic migrations (default: false)
- `RAILPACK_PHP_EXTENSIONS` - Additional PHP extensions (e.g., `gd,imagick,redis`)

### Additional Services

Railpack handles the main application automatically. If you need queue workers or scheduled tasks, create separate services:

**Worker Service (optional):**
- Start Command: `php artisan queue:work`
- Or use the script: `./railway/run-worker.sh`

**Cron Service (optional):**
- Start Command: `./railway/run-cron.sh`
- Runs the Laravel scheduler every minute

For detailed Railpack documentation, see the [Railpack PHP Guide](https://railpack.com/languages/php/).
