<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data['subject'] ?? $data['title'] ?? 'Message from ' . $appName }}</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #ffffff;
            background-color: #000000;
            margin: 0;
            padding: 0;
        }
        
        /* Main container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #111111;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(255, 0, 0, 0.2);
            border: 1px solid #333333;
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, transparent 70%);
        }
        
        .email-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        
        .email-header .subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }
        
        /* Content */
        .email-content {
            padding: 40px 30px;
            background-color: #111111;
        }
        
        .email-subject {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 24px;
            line-height: 1.3;
            border-left: 4px solid #dc143c;
            padding-left: 16px;
        }
        
        .email-message {
            font-size: 16px;
            line-height: 1.7;
            color: #e0e0e0;
            margin-bottom: 32px;
        }
        
        .email-message p {
            margin-bottom: 16px;
        }
        
        .email-message p:last-child {
            margin-bottom: 0;
        }
        
        /* CTA Button */
        .cta-container {
            text-align: center;
            margin: 32px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 2px solid #dc143c;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
            background: linear-gradient(135deg, #ff1744 0%, #dc143c 100%);
        }
        
        /* Accent line */
        .accent-line {
            height: 2px;
            background: linear-gradient(90deg, #dc143c 0%, #8b0000 50%, #dc143c 100%);
            margin: 20px 0;
        }
        
        /* Footer */
        .email-footer {
            background-color: #0a0a0a;
            padding: 30px;
            text-align: center;
            border-top: 2px solid #dc143c;
        }
        
        .email-footer p {
            margin-bottom: 12px;
            color: #a0a0a0;
            font-size: 14px;
        }
        
        .email-footer a {
            color: #dc143c;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .email-footer a:hover {
            color: #ff1744;
            text-decoration: underline;
        }
        
        /* Social links */
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            padding: 10px;
            background-color: #1a1a1a;
            border: 2px solid #333333;
            border-radius: 50%;
            color: #e0e0e0;
            text-decoration: none;
            width: 40px;
            height: 40px;
            line-height: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: #dc143c;
            border-color: #dc143c;
            color: white;
            transform: scale(1.1);
        }
        
        /* Brand highlight */
        .brand-text {
            color: #dc143c;
            font-weight: 600;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .email-header,
            .email-content,
            .email-footer {
                padding: 24px 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
            
            .email-subject {
                font-size: 16px;
            }
            
            .email-message {
                font-size: 15px;
            }
            
            .cta-button {
                padding: 14px 24px;
                font-size: 15px;
            }
        }
        
        /* Dark mode specific overrides */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #000000;
            }
        }
    </style>
</head>
<body>
    <!-- Wrapper for email clients -->
    <div style="background-color: #000000; padding: 20px 0; min-height: 100vh;">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>{{ $appName }}</h1>
                <div class="subtitle">Premium Live Streaming Platform</div>
            </div>
            
            <!-- Main Content -->
            <div class="email-content">
                @if(isset($data['subject']) && $data['subject'])
                <div class="email-subject">
                    {{ $data['subject'] }}
                </div>
                @endif
                
                <div class="email-message">
                    {!! nl2br(e($data['body'] ?? $data['message'] ?? '')) !!}
                </div>
                            
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <div class="social-links">
                    <a href="{{ $appUrl }}" title="Website">🌐</a>
                    <a href="#" title="Twitter">🐦</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="Facebook">📘</a>
                </div>
                
                <p>
                    This email was sent from <strong class="brand-text">{{ $appName }}</strong>
                </p>
                <p>
                    <a href="{{ $appUrl }}">Visit our website</a> •
                    <a href="{{ $appUrl }}/privacy">Privacy Policy</a> •
                    <a href="{{ $appUrl }}/terms">Terms of Service</a>
                </p>
                <p style="margin-top: 20px; font-size: 12px; color: #666;">
                    © {{ date('Y') }} <span class="brand-text">{{ $appName }}</span>. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html> 