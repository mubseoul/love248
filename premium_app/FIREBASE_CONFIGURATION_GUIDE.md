# Firebase Configuration Guide

This guide will walk you through getting the actual Firebase configuration keys to replace the placeholder values in `lib/firebase_options.dart`.

## Step 1: Access Firebase Console

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Sign in with your Google account (you should already be logged in as mubupwork@gmail.com)
3. Select your existing **"premiumwork"** project

## Step 2: Add Apps to Your Firebase Project

You need to register each platform (Android, iOS, Web) as separate apps in your Firebase project.

### 2.1 Add Android App

1. In Firebase Console, click **"Add app"** button
2. Select **Android** icon
3. Fill in the registration form:
   - **Android package name**: `com.premiumwork.app.premium_app`
   - **App nickname** (optional): `Premium Work Android`
   - **Debug signing certificate SHA-1** (optional): Leave blank for now
4. Click **"Register app"**
5. **Download `google-services.json`**
6. Place the downloaded file in: `android/app/google-services.json`
7. Follow the SDK setup steps (these are already done in your project)

### 2.2 Add iOS App

1. Click **"Add app"** again
2. Select **iOS** icon
3. Fill in the registration form:
   - **iOS bundle ID**: `com.premiumwork.app.premium_app`
   - **App nickname** (optional): `Premium Work iOS`
   - **App Store ID** (optional): Leave blank
4. Click **"Register app"**
5. **Download `GoogleService-Info.plist`**
6. Place the downloaded file in: `ios/Runner/GoogleService-Info.plist`
7. Follow the SDK setup steps (these are already done in your project)

### 2.3 Add Web App

1. Click **"Add app"** again
2. Select **Web** icon (</> symbol)
3. Fill in the registration form:
   - **App nickname**: `Premium Work Web`
   - **Also set up Firebase Hosting**: Leave unchecked for now
4. Click **"Register app"**
5. **Copy the Firebase configuration object** (this is what you need for firebase_options.dart)

## Step 3: Get Configuration Keys

After adding the Web app, you'll see a configuration object like this:

```javascript
const firebaseConfig = {
  apiKey: "AIzaSyC1234567890abcdefghijklmnopqrstuvwxyz",
  authDomain: "premiumwork.firebaseapp.com",
  projectId: "premiumwork",
  storageBucket: "premiumwork.appspot.com",
  messagingSenderId: "123456789012",
  appId: "1:123456789012:web:abcdef123456789012345",
  measurementId: "G-ABCDEFGHIJ",
};
```

### Alternative: Get Keys from Project Settings

If you missed the configuration during app setup:

1. Go to **Project Settings** (gear icon next to "Project Overview")
2. Scroll down to **"Your apps"** section
3. Click on each app to see its configuration:
   - **Web app**: Shows the JavaScript config object
   - **Android app**: Shows google-services.json download
   - **iOS app**: Shows GoogleService-Info.plist download

## Step 4: Update firebase_options.dart

Replace the placeholder values in `lib/firebase_options.dart` with your actual values:

### Web Configuration

```dart
static const FirebaseOptions web = FirebaseOptions(
  apiKey: 'YOUR_WEB_API_KEY',                    // From firebaseConfig.apiKey
  appId: 'YOUR_WEB_APP_ID',                      // From firebaseConfig.appId
  messagingSenderId: 'YOUR_MESSAGING_SENDER_ID', // From firebaseConfig.messagingSenderId
  projectId: 'premiumwork',                      // Should already be correct
  authDomain: 'premiumwork.firebaseapp.com',    // Should already be correct
  storageBucket: 'premiumwork.appspot.com',     // Should already be correct
  measurementId: 'YOUR_MEASUREMENT_ID',          // From firebaseConfig.measurementId
);
```

### Android Configuration

You can get these from the `google-services.json` file:

```dart
static const FirebaseOptions android = FirebaseOptions(
  apiKey: 'YOUR_ANDROID_API_KEY',                // From google-services.json -> client[0].api_key[0].current_key
  appId: 'YOUR_ANDROID_APP_ID',                  // From google-services.json -> client[0].client_info.mobilesdk_app_id
  messagingSenderId: 'YOUR_MESSAGING_SENDER_ID', // From google-services.json -> project_info.project_number
  projectId: 'premiumwork',                      // Should already be correct
  storageBucket: 'premiumwork.appspot.com',     // Should already be correct
);
```

### iOS Configuration

You can get these from the `GoogleService-Info.plist` file:

```dart
static const FirebaseOptions ios = FirebaseOptions(
  apiKey: 'YOUR_IOS_API_KEY',                    // From GoogleService-Info.plist -> API_KEY
  appId: 'YOUR_IOS_APP_ID',                      // From GoogleService-Info.plist -> GOOGLE_APP_ID
  messagingSenderId: 'YOUR_MESSAGING_SENDER_ID', // From GoogleService-Info.plist -> GCM_SENDER_ID
  projectId: 'premiumwork',                      // Should already be correct
  storageBucket: 'premiumwork.appspot.com',     // From GoogleService-Info.plist -> STORAGE_BUCKET
  iosBundleId: 'com.premiumwork.app.premium_app', // Should already be correct
);
```

## Step 5: Enable Cloud Messaging

1. In Firebase Console, go to **"Cloud Messaging"** in the left sidebar
2. Cloud Messaging should already be enabled for your project
3. If not enabled, click **"Get started"** to enable it

## Step 6: Configure Android for FCM (Additional Setup)

1. Open `android/build.gradle` (project level)
2. Make sure it includes the Google services plugin:

```gradle
buildscript {
    dependencies {
        classpath 'com.google.gms:google-services:4.4.0'
    }
}
```

3. Open `android/app/build.gradle`
4. Make sure it applies the Google services plugin at the bottom:

```gradle
apply plugin: 'com.google.gms.google-services'
```

## Step 7: Test Your Configuration

1. Run your app: `flutter run`
2. Tap the notification icon (floating action button)
3. You should see a real FCM token instead of "Loading..."
4. Copy this token for testing

## Step 8: Send Test Notification

1. Go to Firebase Console → **Cloud Messaging**
2. Click **"Send your first message"**
3. Fill in:
   - **Notification title**: "Test Notification"
   - **Notification text**: "Hello from Firebase!"
4. Click **"Send test message"**
5. Paste your FCM token
6. Click **"Test"**

## Common Configuration Values Explanation

- **apiKey**: Used to authenticate your app with Firebase services
- **appId**: Unique identifier for your app within the Firebase project
- **messagingSenderId**: Used for Firebase Cloud Messaging (same as project number)
- **projectId**: Your Firebase project ID (should be "premiumwork")
- **authDomain**: Used for Firebase Authentication
- **storageBucket**: Used for Firebase Storage
- **measurementId**: Used for Google Analytics (Web only)
- **iosBundleId**: iOS app bundle identifier

## Troubleshooting

### If you see "Loading..." for FCM Token:

- Check that you've placed the configuration files in the correct locations
- Verify the configuration keys are correct
- Make sure Cloud Messaging is enabled in Firebase Console
- Check the console logs for any Firebase initialization errors

### If notifications don't work:

- Verify notification permissions are granted on the device
- Check that the FCM token is being generated
- Test with Firebase Console first before implementing server-side sending
- Make sure the app is properly registered in Firebase Console

### File Locations Summary:

- `android/app/google-services.json` (Android config)
- `ios/Runner/GoogleService-Info.plist` (iOS config)
- `lib/firebase_options.dart` (Flutter config - update with your keys)

## Security Note

⚠️ **Important**: The `apiKey` in Firebase configuration is not a secret key. It's safe to include in your client-side code. However, make sure to configure Firebase Security Rules properly to protect your data.

## Next Steps After Configuration

1. Test notifications on all target platforms
2. Implement server-side notification sending
3. Set up notification analytics and tracking
4. Configure advanced notification features (actions, images, etc.)
5. Implement user notification preferences
