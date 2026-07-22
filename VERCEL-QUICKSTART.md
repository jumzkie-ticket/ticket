# 🚀 Quick Start: Deploy to Vercel

## 1. Prepare Your Database (5 minutes)

### Option A: PlanetScale (Recommended)
1. Go to [planetscale.com](https://planetscale.com) → Sign up
2. Create new database
3. Copy connection details (host, username, password)

### Option B: Neon (PostgreSQL)
1. Go to [neon.tech](https://neon.tech) → Sign up
2. Create new project
3. Copy connection string

## 2. Get Your APP_KEY

Run this command locally:
```bash
php artisan key:generate --show
```

Copy the output (should look like: `base64:xxxxxxxx...`)

## 3. Deploy to Vercel

1. **Go to**: [vercel.com/new](https://vercel.com/new)

2. **Import** your repository: `jumzkie-ticket/ticket`

3. **Add Environment Variables** (click "Add" for each):

   ```
   APP_NAME = TicketSupport
   APP_ENV = production
   APP_KEY = [paste your generated key from step 2]
   APP_DEBUG = false
   APP_URL = https://your-project-name.vercel.app
   
   DB_CONNECTION = mysql
   DB_HOST = [your database host]
   DB_PORT = 3306
   DB_DATABASE = [your database name]
   DB_USERNAME = [your database username]
   DB_PASSWORD = [your database password]
   
   LOG_CHANNEL = stderr
   SESSION_DRIVER = cookie
   CACHE_DRIVER = array
   QUEUE_CONNECTION = sync
   ```

4. **Click "Deploy"** and wait (3-5 minutes)

## 4. Run Migrations

### Quick Method (One-Time Setup Route):

1. Add this to `routes/web.php`:
   ```php
   Route::get('/run-migrations', function () {
       if (app()->environment('production')) {
           Artisan::call('migrate', ['--force' => true]);
           return 'Migrations completed successfully!';
       }
       return 'Not available in this environment';
   });
   ```

2. Visit: `https://your-app.vercel.app/run-migrations`

3. See "Migrations completed successfully!" message

4. **IMPORTANT**: Remove that route from `routes/web.php` and redeploy

### Alternative: Use Vercel CLI

```bash
npm i -g vercel
vercel login
vercel link
vercel env pull .env.production
php artisan migrate --force --env=production
```

## 5. Test Your App

Visit your Vercel URL and test:
- ✅ Homepage loads
- ✅ Login works
- ✅ Create ticket works
- ✅ Styles are applied

## Done! 🎉

Your Laravel app is now live on Vercel!

## ⚠️ Important Notes

- **File uploads won't persist** - Use S3 if you need uploads
- **First request may be slow** (cold start) - This is normal
- **Sessions use cookies** - Users stay logged in
- **No background jobs** - All processing happens on request

## Need Help?

- **Build fails?** Check Vercel deployment logs
- **500 errors?** Check Functions tab in Vercel dashboard
- **Database errors?** Verify connection settings
- **Full docs**: See `README-VERCEL.md` and `DEPLOYMENT-CHECKLIST.md`

## Better Alternatives

If you need persistent file storage or background jobs, consider:
- **Railway** - Dead simple Laravel deployment
- **Render** - Great free tier
- **Fly.io** - Full server control
