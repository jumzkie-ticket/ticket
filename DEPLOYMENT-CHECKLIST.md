# Vercel Deployment Checklist

## Before Deploying

- [ ] Commit all changes to your repository
- [ ] Push to GitHub
- [ ] Have a database ready (PlanetScale, Neon, or Supabase)
- [ ] Generate APP_KEY locally: `php artisan key:generate --show`

## Vercel Setup

### 1. Import Project
- [ ] Go to https://vercel.com/new
- [ ] Import your GitHub repository
- [ ] Select the repository: `jumzkie-ticket/ticket`

### 2. Configure Build Settings
Vercel should auto-detect these, but verify:
- Framework Preset: **Other**
- Build Command: `npm run vercel-build` (auto-detected)
- Output Directory: Leave empty
- Install Command: `npm install` (auto-detected)

### 3. Add Environment Variables

Copy these from your local `.env` or generate new ones:

#### Essential (App will not work without these!)
```
APP_NAME=TicketSupport
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-app.vercel.app
```

#### Database (REQUIRED - choose one)

**PlanetScale MySQL:**
```
DB_CONNECTION=mysql
DB_HOST=your-host.psdb.cloud
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

**Or PostgreSQL (Neon/Supabase):**
```
DB_CONNECTION=pgsql
DB_HOST=your-host
DB_PORT=5432
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

#### Serverless Optimizations
```
LOG_CHANNEL=stderr
LOG_LEVEL=error
SESSION_DRIVER=cookie
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
```

### 4. Deploy
- [ ] Click "Deploy"
- [ ] Wait for build to complete (3-5 minutes first time)
- [ ] Check build logs for any errors

### 5. Post-Deployment

#### Run Database Migrations

**Option A: Via Vercel CLI (Recommended)**
```bash
npm i -g vercel
vercel login
vercel link
vercel env pull .env.production
php artisan migrate --force --env=production
```

**Option B: Add temporary migration route**
Add to `routes/web.php`:
```php
Route::get('/setup-database', function () {
    if (app()->environment('production')) {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]); // If you have seeders
        return 'Database setup complete!';
    }
    return 'Not available';
});
```
Visit once: `https://your-app.vercel.app/setup-database`
Then remove the route!

### 6. Verify Deployment
- [ ] Visit your Vercel URL
- [ ] Test authentication (login/register)
- [ ] Test creating a ticket
- [ ] Check that CSS/JS assets load
- [ ] Test database operations

## Troubleshooting

### Build Fails with "composer: command not found"
- The vercel-install.js script should handle this
- Check build logs to see where it fails
- Ensure PHP is available in Vercel environment

### 500 Error After Deployment
Check these in Vercel:
1. **Functions Tab** > Click on `/api/index.php` > View logs
2. Verify all environment variables are set
3. Ensure `APP_KEY` is correctly formatted: `base64:...`
4. Check database connection settings

### Assets Not Loading
1. Verify `APP_URL` matches your Vercel URL exactly
2. Check Deployments > Functions tab > Ensure vite build succeeded
3. Look for `/build/*` files in deployment

### Database Connection Failed
1. Verify DB credentials in Vercel environment variables
2. Check if database accepts connections from Vercel IPs
3. For PlanetScale: Ensure you're using the production branch connection string

## Performance Tips

1. **Use a database connection pooler** for better performance
2. **Enable caching** with Upstash Redis:
   ```
   CACHE_DRIVER=redis
   REDIS_URL=your-upstash-redis-url
   ```
3. **Use S3 for file uploads** instead of local storage
4. **Enable Vercel Analytics** in project settings

## Limitations

⚠️ **Important Vercel Limitations:**
- No persistent file storage (uploads won't persist)
- No background queue workers (use external service)
- 10-second execution timeout (free tier)
- Cold starts (first request may be slow)
- No WebSocket support

## Need Help?

1. Check Vercel Function logs
2. Review Laravel logs in Vercel dashboard
3. Consult `README-VERCEL.md` for detailed documentation
4. Consider alternative platforms if limitations are blockers

## Alternative Platforms

If Vercel doesn't fit your needs:
- **Railway** - Best Laravel support, includes database
- **Render** - Great free tier, persistent storage
- **Fly.io** - Full control, better for complex apps
- **DigitalOcean App Platform** - Simple, affordable
