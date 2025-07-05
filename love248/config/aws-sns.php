<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AWS SNS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AWS SNS notifications service
    |
    */

    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],

    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),

    'version' => 'latest',

    /*
    |--------------------------------------------------------------------------
    | Topics
    |--------------------------------------------------------------------------
    |
    | SNS Topic ARNs for different types of notifications
    |
    */
    'topics' => [
        'general' => env('AWS_SNS_GENERAL_TOPIC_ARN'),
        'maintenance' => env('AWS_SNS_MAINTENANCE_TOPIC_ARN'),
        'security' => env('AWS_SNS_SECURITY_TOPIC_ARN'),
        'features' => env('AWS_SNS_FEATURES_TOPIC_ARN'),
        'email' => env('AWS_SNS_EMAIL_TOPIC_ARN'),
        'sms' => env('AWS_SNS_SMS_TOPIC_ARN'),
    ],
];
