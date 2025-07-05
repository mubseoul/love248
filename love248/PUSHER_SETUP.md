# Quick Pusher Setup for Real-Time Features

## The Error You're Seeing

```
WebSocket connection to 'wss://ws-.pusher.com/app/...' failed
```

This happens because the Pusher cluster is missing from the configuration.

## Quick Fix

### 1. Get Pusher Credentials

1. Go to [Pusher.com](https://pusher.com) and create a free account
2. Create a new app
3. Get your credentials from the "App Keys" tab:
   - App ID
   - Key
   - Secret
   - Cluster (e.g., `mt1`, `us2`, `eu`, etc.)

### 2. Update Your .env File

Add these lines to your `.env` file:

```env
# Broadcasting
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=your_app_id_here
PUSHER_APP_KEY=your_key_here
PUSHER_APP_SECRET=your_secret_here
PUSHER_APP_CLUSTER=mt1

# Frontend variables
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 3. Clear Config and Rebuild

```bash
php artisan config:clear
npm run build
# or for development
npm run dev
```

### 4. Start Required Services

```bash
# Start queue worker (in one terminal)
php artisan queue:work

# Start scheduler (in another terminal)
php artisan schedule:work
```

## Test Real-Time Features

1. Open the private stream page
2. Check browser console - you should see:
   ```
   ✅ Successfully subscribed to private stream channel
   ```
3. Take actions (start countdown, join stream) and verify real-time updates work

## Without Pusher (Development Only)

If you don't want to set up Pusher right now, the system will work without real-time features. Users will need to refresh the page to see updates, but all core functionality remains intact.

## Production Notes

- Use a proper queue driver like Redis in production
- Set up supervisor for queue workers
- Add cron job for scheduler: `* * * * * cd /path && php artisan schedule:run`
