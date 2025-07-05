# Firebase Keys Quick Reference

## Current Placeholder Values in `lib/firebase_options.dart`

Replace these placeholder values with your actual Firebase configuration:

### 🔄 Values to Replace:

| Current Placeholder                            | Replace With                    | Where to Find                                   |
| ---------------------------------------------- | ------------------------------- | ----------------------------------------------- |
| `AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`       | Your actual API key             | Firebase Console → Project Settings → Your apps |
| `1:123456789:web:xxxxxxxxxxxxxxxxxxxxxxxx`     | Your actual Web App ID          | Web app config object                           |
| `1:123456789:android:xxxxxxxxxxxxxxxxxxxxxxxx` | Your actual Android App ID      | google-services.json                            |
| `1:123456789:ios:xxxxxxxxxxxxxxxxxxxxxxxx`     | Your actual iOS App ID          | GoogleService-Info.plist                        |
| `123456789`                                    | Your actual Messaging Sender ID | Same for all platforms                          |
| `G-XXXXXXXXXX`                                 | Your actual Measurement ID      | Web app config only                             |

### 📱 Platform-Specific Instructions:

#### Web (from Firebase Console → Web app config):

```dart
static const FirebaseOptions web = FirebaseOptions(
  apiKey: 'REPLACE_WITH_WEB_API_KEY',           // firebaseConfig.apiKey
  appId: 'REPLACE_WITH_WEB_APP_ID',             // firebaseConfig.appId
  messagingSenderId: 'REPLACE_WITH_SENDER_ID',  // firebaseConfig.messagingSenderId
  projectId: 'premiumwork',                     // ✅ Already correct
  authDomain: 'premiumwork.firebaseapp.com',   // ✅ Already correct
  storageBucket: 'premiumwork.appspot.com',    // ✅ Already correct
  measurementId: 'REPLACE_WITH_MEASUREMENT_ID', // firebaseConfig.measurementId
);
```

#### Android (from google-services.json):

```dart
static const FirebaseOptions android = FirebaseOptions(
  apiKey: 'REPLACE_WITH_ANDROID_API_KEY',       // client[0].api_key[0].current_key
  appId: 'REPLACE_WITH_ANDROID_APP_ID',         // client[0].client_info.mobilesdk_app_id
  messagingSenderId: 'REPLACE_WITH_SENDER_ID',  // project_info.project_number
  projectId: 'premiumwork',                     // ✅ Already correct
  storageBucket: 'premiumwork.appspot.com',    // ✅ Already correct
);
```

#### iOS (from GoogleService-Info.plist):

```dart
static const FirebaseOptions ios = FirebaseOptions(
  apiKey: 'REPLACE_WITH_IOS_API_KEY',           // API_KEY
  appId: 'REPLACE_WITH_IOS_APP_ID',             // GOOGLE_APP_ID
  messagingSenderId: 'REPLACE_WITH_SENDER_ID',  // GCM_SENDER_ID
  projectId: 'premiumwork',                     // ✅ Already correct
  storageBucket: 'premiumwork.appspot.com',    // STORAGE_BUCKET
  iosBundleId: 'com.premiumwork.app.premium_app', // ✅ Already correct
);
```

## 🚀 Quick Setup Checklist:

- [ ] 1. Go to [Firebase Console](https://console.firebase.google.com/)
- [ ] 2. Select "premiumwork" project
- [ ] 3. Add Android app (`com.premiumwork.app.premium_app`)
- [ ] 4. Download `google-services.json` → place in `android/app/`
- [ ] 5. Add iOS app (`com.premiumwork.app.premium_app`)
- [ ] 6. Download `GoogleService-Info.plist` → place in `ios/Runner/`
- [ ] 7. Add Web app
- [ ] 8. Copy config values to `lib/firebase_options.dart`
- [ ] 9. Test with `flutter run`

## 🔍 How to Find Your Values:

### Method 1: During App Registration

When you add each app, Firebase shows you the configuration. Copy these values immediately.

### Method 2: From Project Settings

1. Firebase Console → ⚙️ Project Settings
2. Scroll to "Your apps" section
3. Click on each app to see its configuration

### Method 3: From Downloaded Files

- **google-services.json**: Open in text editor, find the JSON values
- **GoogleService-Info.plist**: Open in text editor, find the key-value pairs

## ⚡ Testing Steps:

1. Replace the values in `firebase_options.dart`
2. Run: `flutter run`
3. Tap the notification button (floating action button)
4. You should see a real FCM token (not "Loading...")
5. Copy the token and test notifications from Firebase Console

## 🔧 File Locations:

```
android/app/google-services.json          ← Android config file
ios/Runner/GoogleService-Info.plist       ← iOS config file
lib/firebase_options.dart                 ← Update this with your keys
```

## 🚨 Important Notes:

- **messagingSenderId** is the same across all platforms (it's your project number)
- **projectId** should remain "premiumwork"
- **API keys** are different for each platform
- **App IDs** are different for each platform
- The configuration files must be in the exact locations specified
