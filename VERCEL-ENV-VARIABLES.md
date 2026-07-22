# 🔑 Vercel Environment Variables

Copy and paste these **exact values** into your Vercel project settings.

## 📋 How to Add in Vercel:

1. Go to your Vercel project dashboard
2. Click **Settings** → **Environment Variables**
3. For each variable below, click **"Add"**:
   - Enter the **Key** (left column)
   - Enter the **Value** (right column)
   - Select **All** environments (Production, Preview, Development)
   - Click **"Save"**

---

## 🚀 Required Environment Variables

### Application Configuration

| Key | Value |
|-----|-------|
| `APP_NAME` | `TicketSystem` |
| `APP_ENV` | `production` |
| `APP_KEY` | `base64:0BQHNLdAkdynFXu35ySgzR18ow9O3zFYh8tAcPpvNGc=` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-project-name.vercel.app` |

⚠️ **Important**: Replace `your-project-name.vercel.app` with your actual Vercel domain!

### Database Configuration (Supabase with Connection Pooling)

| Key | Value |
|-----|-------|
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `aws-1-ap-south-1.pooler.supabase.com` |
| `DB_PORT` | `6543` |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres.wvtqsbhgxutjrzosvdbo` |
| `DB_PASSWORD` | `jumzkieticket` |
| `DB_SSLMODE` | `require` |

### Logging (Vercel Optimized)

| Key | Value |
|-----|-------|
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `error` |

### Session, Cache & Queue (Serverless Optimized)

| Key | Value |
|-----|-------|
| `SESSION_DRIVER` | `cookie` |
| `SESSION_LIFETIME` | `120` |
| `SESSION_ENCRYPT` | `false` |
| `CACHE_DRIVER` | `array` |
| `QUEUE_CONNECTION` | `sync` |

### Filesystem

| Key | Value |
|-----|-------|
| `FILESYSTEM_DISK` | `public` |

### Locale Settings (Optional but Recommended)

| Key | Value |
|-----|-------|
| `APP_LOCALE` | `en` |
| `APP_FALLBACK_LOCALE` | `en` |

### Mail Settings (Optional - if you need email)

| Key | Value |
|-----|-------|
| `MAIL_MAILER` | `log` |
| `MAIL_FROM_ADDRESS` | `hello@example.com` |
| `MAIL_FROM_NAME` | `TicketSystem` |

---

## ⚡ Quick Copy-Paste Format

If Vercel supports bulk import, use this format:

```env
APP_NAME=TicketSystem
APP_ENV=production
APP_KEY=base64:0BQHNLdAkdynFXu35ySgzR18ow9O3zFYh8tAcPpvNGc=
APP_DEBUG=false
APP_URL=https://your-project-name.vercel.app

DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-south-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.wvtqsbhgxutjrzosvdbo
DB_PASSWORD=jumzkieticket
DB_SSLMODE=require

LOG_CHANNEL=stderr
LOG_LEVEL=error

SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_DRIVER=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=TicketSystem
```

⚠️ **Remember**: Update `APP_URL` with your actual Vercel URL after deployment!

---

## 📝 Notes

### Why Different from Local .env?

Your local `.env` uses:
- `SESSION_DRIVER=database` → Won't work in serverless
- `CACHE_STORE=database` → Slow in serverless
- `QUEUE_CONNECTION=database` → No background workers

Vercel uses:
- `SESSION_DRIVER=cookie` → Works with serverless
- `CACHE_DRIVER=array` → Fast for serverless
- `QUEUE_CONNECTION=sync` → Processes immediately

### Security Note

Your database password is visible in this file. After deployment:
1. ✅ This file is in `.gitignore` (safe)
2. ✅ Vercel environment variables are encrypted
3. ⚠️ Don't share this file publicly

### After Deployment

Once deployed, you can update `APP_URL` to match your Vercel domain:
1. Vercel Dashboard → Settings → Environment Variables
2. Edit `APP_URL` 
3. Set to: `https://your-actual-domain.vercel.app`
4. Redeploy

---

## ✅ Checklist

- [ ] All environment variables added to Vercel
- [ ] `APP_URL` updated to Vercel domain
- [ ] Code committed and pushed to GitHub
- [ ] Deployed to Vercel
- [ ] Run migrations (see `VERCEL-QUICKSTART.md`)
- [ ] Test the application

---

## 🆘 Need Help?

If deployment fails:
1. Check Vercel build logs
2. Verify all environment variables are set
3. Check Functions logs in Vercel dashboard
4. See `DEPLOYMENT-CHECKLIST.md` for troubleshooting
