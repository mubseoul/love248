import 'dart:async';
import 'dart:io';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class NotificationService {
  static final NotificationService _instance = NotificationService._internal();
  factory NotificationService() => _instance;
  NotificationService._internal();

  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _flutterLocalNotificationsPlugin =
      FlutterLocalNotificationsPlugin();

  String? _fcmToken;
  String? get fcmToken => _fcmToken;

  // Stream controller for notification clicks
  final StreamController<String> _notificationClickController =
      StreamController<String>.broadcast();
  Stream<String> get notificationClickStream =>
      _notificationClickController.stream;

  Future<void> initialize() async {
    try {
      // Request permission for notifications
      await _requestPermission();

      // Initialize local notifications
      await _initializeLocalNotifications();

      // Get FCM token
      await _getFCMToken();

      // Set up message handlers
      _setupMessageHandlers();

      debugPrint('NotificationService initialized successfully');
    } catch (e) {
      debugPrint('Error initializing NotificationService: $e');
    }
  }

  Future<void> _requestPermission() async {
    if (kIsWeb) {
      // Web permission request
      NotificationSettings settings = await _firebaseMessaging.requestPermission(
        alert: true,
        announcement: false,
        badge: true,
        carPlay: false,
        criticalAlert: false,
        provisional: false,
        sound: true,
      );
      debugPrint('Web notification permission: ${settings.authorizationStatus}');
    } else if (Platform.isIOS) {
      // iOS permission request
      NotificationSettings settings = await _firebaseMessaging.requestPermission(
        alert: true,
        announcement: false,
        badge: true,
        carPlay: false,
        criticalAlert: false,
        provisional: false,
        sound: true,
      );
      debugPrint('iOS notification permission: ${settings.authorizationStatus}');

      // Request permission for local notifications on iOS
      await _flutterLocalNotificationsPlugin
          .resolvePlatformSpecificImplementation<
              IOSFlutterLocalNotificationsPlugin>()
          ?.requestPermissions(
            alert: true,
            badge: true,
            sound: true,
          );
    } else if (Platform.isAndroid) {
      // Android permission request (handled automatically for most cases)
      await _flutterLocalNotificationsPlugin
          .resolvePlatformSpecificImplementation<
              AndroidFlutterLocalNotificationsPlugin>()
          ?.requestNotificationsPermission();
    }
  }

  Future<void> _initializeLocalNotifications() async {
    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    const DarwinInitializationSettings initializationSettingsIOS =
        DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );

    const DarwinInitializationSettings initializationSettingsMacOS =
        DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );

    const InitializationSettings initializationSettings =
        InitializationSettings(
      android: initializationSettingsAndroid,
      iOS: initializationSettingsIOS,
      macOS: initializationSettingsMacOS,
    );

    await _flutterLocalNotificationsPlugin.initialize(
      initializationSettings,
      onDidReceiveNotificationResponse: _onNotificationTap,
    );

    // Create notification channel for Android
    if (Platform.isAndroid) {
      const AndroidNotificationChannel channel = AndroidNotificationChannel(
        'high_importance_channel',
        'High Importance Notifications',
        description: 'This channel is used for important notifications.',
        importance: Importance.high,
        playSound: true,
      );

      await _flutterLocalNotificationsPlugin
          .resolvePlatformSpecificImplementation<
              AndroidFlutterLocalNotificationsPlugin>()
          ?.createNotificationChannel(channel);
    }
  }

  Future<void> _getFCMToken() async {
    try {
      _fcmToken = await _firebaseMessaging.getToken();
      debugPrint('FCM Token: $_fcmToken');
      
      // Listen for token refresh
      _firebaseMessaging.onTokenRefresh.listen((token) {
        _fcmToken = token;
        debugPrint('FCM Token refreshed: $token');
        // Here you can send the token to your server
        _sendTokenToServer(token);
      });

      // Send initial token to server
      if (_fcmToken != null) {
        _sendTokenToServer(_fcmToken!);
      }
    } catch (e) {
      debugPrint('Error getting FCM token: $e');
    }
  }

  void _setupMessageHandlers() {
    // Handle foreground messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

    // Handle background message taps
    FirebaseMessaging.onMessageOpenedApp.listen(_handleMessageOpenedApp);

    // Handle notification tap when app is terminated
    _firebaseMessaging.getInitialMessage().then((message) {
      if (message != null) {
        _handleMessageOpenedApp(message);
      }
    });
  }

  Future<void> _handleForegroundMessage(RemoteMessage message) async {
    debugPrint('Received foreground message: ${message.messageId}');
    debugPrint('Message data: ${message.data}');

    // Show local notification when app is in foreground
    await _showLocalNotification(message);
  }

  void _handleMessageOpenedApp(RemoteMessage message) {
    debugPrint('Message opened app: ${message.messageId}');
    debugPrint('Message data: ${message.data}');

    // Handle navigation or other actions when notification is tapped
    _handleNotificationAction(message.data);
  }

  Future<void> _showLocalNotification(RemoteMessage message) async {
    RemoteNotification? notification = message.notification;
    // AndroidNotification? android = message.notification?.android;

    // Show notification only if we have the required data
    if (notification != null && !kIsWeb) {
      await _flutterLocalNotificationsPlugin.show(
        notification.hashCode,
        notification.title,
        notification.body,
        NotificationDetails(
          android: AndroidNotificationDetails(
            'high_importance_channel',
            'High Importance Notifications',
            channelDescription: 'This channel is used for important notifications.',
            importance: Importance.high,
            priority: Priority.high,
            icon: '@mipmap/ic_launcher',
            // Note: NetworkImageAndroidBitmap requires additional setup
            // largeIcon: android?.imageUrl != null
            //     ? NetworkImageAndroidBitmap(android!.imageUrl!)
            //     : null,
          ),
          iOS: const DarwinNotificationDetails(
            presentAlert: true,
            presentBadge: true,
            presentSound: true,
          ),
          macOS: const DarwinNotificationDetails(
            presentAlert: true,
            presentBadge: true,
            presentSound: true,
          ),
        ),
        payload: message.data.toString(),
      );
    }
  }

  void _onNotificationTap(NotificationResponse notificationResponse) {
    debugPrint('Notification tapped: ${notificationResponse.payload}');
    
    if (notificationResponse.payload != null) {
      _notificationClickController.add(notificationResponse.payload!);
    }
  }

  void _handleNotificationAction(Map<String, dynamic> data) {
    // Handle different notification actions based on data
    if (data.containsKey('action')) {
      String action = data['action'];
      switch (action) {
        case 'open_url':
          if (data.containsKey('url')) {
            _notificationClickController.add(data['url']);
          }
          break;
        case 'navigate':
          if (data.containsKey('route')) {
            _notificationClickController.add(data['route']);
          }
          break;
        default:
          debugPrint('Unknown notification action: $action');
      }
    }
  }

  Future<void> _sendTokenToServer(String token) async {
    try {
      // TODO: Implement your server API call to save the FCM token
      // Example:
      // await http.post(
      //   Uri.parse('https://your-server.com/api/save-token'),
      //   headers: {'Content-Type': 'application/json'},
      //   body: json.encode({'token': token, 'platform': Platform.operatingSystem}),
      // );
      
      debugPrint('Token should be sent to server: $token');
    } catch (e) {
      debugPrint('Error sending token to server: $e');
    }
  }

  // Method to subscribe to topics
  Future<void> subscribeToTopic(String topic) async {
    try {
      await _firebaseMessaging.subscribeToTopic(topic);
      debugPrint('Subscribed to topic: $topic');
    } catch (e) {
      debugPrint('Error subscribing to topic $topic: $e');
    }
  }

  // Method to unsubscribe from topics
  Future<void> unsubscribeFromTopic(String topic) async {
    try {
      await _firebaseMessaging.unsubscribeFromTopic(topic);
      debugPrint('Unsubscribed from topic: $topic');
    } catch (e) {
      debugPrint('Error unsubscribing from topic $topic: $e');
    }
  }

  // Method to show custom local notification
  Future<void> showCustomNotification({
    required String title,
    required String body,
    String? payload,
  }) async {
    await _flutterLocalNotificationsPlugin.show(
      DateTime.now().millisecondsSinceEpoch.remainder(100000),
      title,
      body,
      const NotificationDetails(
        android: AndroidNotificationDetails(
          'high_importance_channel',
          'High Importance Notifications',
          channelDescription: 'This channel is used for important notifications.',
          importance: Importance.high,
          priority: Priority.high,
          icon: '@mipmap/ic_launcher',
        ),
        iOS: DarwinNotificationDetails(
          presentAlert: true,
          presentBadge: true,
          presentSound: true,
        ),
        macOS: DarwinNotificationDetails(
          presentAlert: true,
          presentBadge: true,
          presentSound: true,
        ),
      ),
      payload: payload,
    );
  }

  void dispose() {
    _notificationClickController.close();
  }
}

// Top-level function to handle background messages
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  debugPrint('Handling a background message: ${message.messageId}');
  debugPrint('Message data: ${message.data}');
  debugPrint('Message notification: ${message.notification?.title}');
} 