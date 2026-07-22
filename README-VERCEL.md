# Laravel on Vercel Deployment Guide

This guide will help you deploy this Laravel application on Vercel with both frontend and backend functionality.

## Prerequisites

1. A Vercel account ([vercel.com](https://vercel.com))
2. Your repository pushed to GitHub
3. Database access (recommended: PlanetScale, Neon, or Supabase for PostgreSQL)

## Deployment Steps

### 1. Connect Your Repository to Vercel

1. Go to [vercel.com/new](https://vercel.com/new)
2. Import your GitHub repository
3. Vercel will auto-detect the project

### 2. Configure Environment Variables

In your Vercel project settings, add these environment variables:

#### Required Variables

```bash
APP_NAME=YourAppName
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.vercel.app

LOG_CHANNEL=stderr
LOG_LEVEL=error

# Session & Cache (use cookie/array for serverless)
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
CACHE_DRIVER=array
QUEUE_CONNECTION=sync

# Database (example with PlanetScale MySQL)
DB_CONNECTION=mysql
DB_HOST=your-db-host.psdb.cloud
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Or use PostgreSQL (Neon/Supabase)
# DB_CONNECTION=pgsql
# DB_HOST=your-db-host
# DB_PORT=5432
# DB_DATABASE=your-database
# DB_USERNAME=your-username
# DB_PASSWORD=your-password
```

#### Get Your APP_KEY

Run this locally to generate an app key:

```bash
php artisan key:generate --show
```

Copy the output and paste it into Vercel's `APP_KEY` environment variable.

### 3. Database Setup

#### Option A: PlanetScale (Recommended for MySQL)

1. Create a free account at [planetscale.com](https://planetscale.com)
2. Create a new database
3. Get connection credentials
4. Add to Vercel environment variables

#### Option B: Neon or Supabase (PostgreSQL)

1. Create account at [neon.tech](https://neon.tech) or [supabase.com](https://supabase.com)
2. Create a new database
3. Get connection credentials
4. Update `DB_CONNECTION=pgsql` in Vercel

#### Option C: SQLite (Not Recommended for Production)

For testing only, you can use SQLite:

```bash
DB_CONNECTION=sqlite
```

Note: SQLite won't persist between deployments on Vercel.

### 4. Deploy

1. Click "Deploy" in Vercel
2. Wait for the build to complete
3. Your app will be live at `https://your-app.vercel.app`

### 5. Run Migrations

After first deployment, you need to run migrations. You have two options:

#### Option A: Use Vercel CLI (Recommended)

```bash
# Install Vercel CLI
npm i -g vercel

# Login
vercel login

# Link to your project
vercel link

# Run migrations
vercel env pull .env.production
php artisan migrate --force --env=production
```

#### Option B: Create a setup route

Add a temporary route in `routes/web.php`:

```php
Route::get('/setup', function () {
    if (App::environment('production')) {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migrations completed!';
    }
    return 'Not allowed';
});
```

Visit `https://your-app.vercel.app/setup` once, then remove the route.

## Important Notes

### Limitations on Vercel

1. **No Persistent Storage**: Files uploaded won't persist. Use S3 or Cloudinary for file uploads.
2. **No Background Jobs**: Queue workers won't run. Use external services like Laravel Vapor Queues or Qstash.
3. **Cold Starts**: First request may be slow (3-5 seconds).
4. **Execution Time Limit**: 10 seconds on free tier, 60 seconds on pro.

### Recommended Configuration

For optimal performance on Vercel:

```env
SESSION_DRIVER=cookie          # Don't use database sessions
CACHE_DRIVER=array             # Or use Redis (Upstash)
QUEUE_CONNECTION=sync          # Or use external queue service
FILESYSTEM_DISK=s3             # Use S3 for file uploads
```

### File Uploads

If your app needs file uploads, use AWS S3:

1. Create an S3 bucket
2. Add these environment variables to Vercel:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

### Redis Cache (Optional)

For better performance, use Upstash Redis:

1. Create free account at [upstash.com](https://upstash.com)
2. Create a Redis database
3. Add to Vercel:

```env
CACHE_DRIVER=redis
REDIS_CLIENT=phpredis
REDIS_URL=your-upstash-redis-url
```

## Troubleshooting

### Build Fails

- Check that all environment variables are set
- Ensure `composer.json` has all required dependencies
- Check Vercel build logs for specific errors

### 500 Errors

- Check Vercel Function logs
- Ensure `APP_KEY` is set correctly
- Verify database connection settings
- Check that migrations have been run

### Assets Not Loading

- Ensure `APP_URL` matches your Vercel URL
- Check that `npm run build` completed successfully
- Verify routes in `vercel.json` are correct

## Support

If you encounter issues:

1. Check Vercel Function logs: Project > Deployments > Click deployment > Functions tab
2. Check runtime logs: Real-time logs show in the Vercel dashboard
3. Review Laravel logs: They output to stderr in Vercel

## Alternative Platforms

If Vercel doesn't meet your needs, consider:

- **Railway**: Better Laravel support, includes database
- **Render**: Great free tier with persistent storage
- **Fly.io**: Full VM control, better for Laravel
- **DigitalOcean App Platform**: Simple Laravel deployment
