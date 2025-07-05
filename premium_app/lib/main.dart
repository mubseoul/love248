import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:flutter_native_splash/flutter_native_splash.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'firebase_options.dart';
import 'services/notification_service.dart';

void main() async {
  WidgetsBinding widgetsBinding = WidgetsFlutterBinding.ensureInitialized();
  FlutterNativeSplash.preserve(widgetsBinding: widgetsBinding);
  
  // Initialize Firebase
  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );
  
  // Set up background message handler
  FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  
  // Initialize notification service
  await NotificationService().initialize();
  
  runApp(const PremiumWorkApp());
}

class PremiumWorkApp extends StatelessWidget {
  const PremiumWorkApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Premium Work',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blue),
        useMaterial3: true,
      ),
      home: const WebViewScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}

class WebViewScreen extends StatefulWidget {
  const WebViewScreen({super.key});

  @override
  State<WebViewScreen> createState() => _WebViewScreenState();
}

class _WebViewScreenState extends State<WebViewScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;
  late final NotificationService _notificationService;

  @override
  void initState() {
    super.initState();
    
    _notificationService = NotificationService();
    
    // Listen for notification clicks
    _notificationService.notificationClickStream.listen((payload) {
      _handleNotificationClick(payload);
    });
    
    // Initialize the WebView controller
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            // Update loading progress
          },
          onPageStarted: (String url) {
            setState(() {
              _isLoading = true;
            });
          },
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
            // Remove the splash screen when the web page is fully loaded
            FlutterNativeSplash.remove();
            
            // Subscribe to a general topic for notifications
            _notificationService.subscribeToTopic('general');
          },
          onWebResourceError: (WebResourceError error) {
            // Handle web resource errors
            debugPrint('WebView error: ${error.description}');
          },
          onNavigationRequest: (NavigationRequest request) {
            // Allow all navigation requests
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse('https://dev.premiumwork.com.br/'));
  }
  
  void _handleNotificationClick(String payload) {
    debugPrint('Handling notification click: $payload');
    
    // Try to parse as URL and navigate
    try {
      if (payload.startsWith('http')) {
        _controller.loadRequest(Uri.parse(payload));
      } else {
        // Handle other payload types or navigate to specific sections
        debugPrint('Non-URL payload received: $payload');
      }
    } catch (e) {
      debugPrint('Error handling notification click: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading)
            const Center(
              child: CircularProgressIndicator(),
            ),
        ],
      ),

    );
  }

}
