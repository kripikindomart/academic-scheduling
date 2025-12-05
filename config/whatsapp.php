<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk WhatsApp Gateway menggunakan Baileys
    |
    */

    'enabled' => env('WHATSAPP_ENABLED', true),

    'default_session_id' => env('WHATSAPP_DEFAULT_SESSION_ID', 'default'),

    'qr_timeout' => env('WHATSAPP_QR_TIMEOUT', 60), // seconds

    'message_retry_limit' => env('WHATSAPP_MESSAGE_RETRY_LIMIT', 3),

    'auto_reconnect' => env('WHATSAPP_AUTO_RECONNECT', true),

    'connection_timeout' => env('WHATSAPP_CONNECTION_TIMEOUT', 30), // seconds

    'session_storage' => [
        'path' => env('WHATSAPP_SESSION_STORAGE_PATH', storage_path('whatsapp/sessions')),
    ],

    'media_storage' => [
        'path' => env('WHATSAPP_MEDIA_STORAGE_PATH', storage_path('whatsapp/media')),
    ],

    'auth' => [
        'strategy' => 'qr', // qr, pair_code, atau phone_number
        'timeout' => 60, // seconds
    ],

    'webhook' => [
        'secret' => env('WHATSAPP_WEBHOOK_SECRET'),
        'verify_ssl' => env('WHATSAPP_WEBHOOK_VERIFY_SSL', true),
    ],

    'rate_limit' => [
        'messages_per_minute' => 30,
        'messages_per_hour' => 1000,
        'messages_per_day' => 10000,
    ],

    'message_types' => [
        'text' => true,
        'image' => true,
        'video' => true,
        'audio' => true,
        'document' => true,
        'sticker' => true,
        'location' => true,
        'contact' => true,
    ],

    'features' => [
        'read_receipts' => true,
        'presence_updates' => true,
        'typing_notifications' => true,
        'business_profile' => false,
        'multi_device' => true,
    ],

    'logging' => [
        'enabled' => env('WHATSAPP_LOGGING_ENABLED', true),
        'level' => env('WHATSAPP_LOG_LEVEL', 'info'),
        'channel' => 'whatsapp',
    ],

    'notifications' => [
        'schedule_reminder' => [
            'enabled' => true,
            'template' => 'Hai {{student_name}}, jadwal kuliah {{course_name}} besok pukul {{time}} di ruangan {{room}}. Jangan sampai lupa ya!',
            'timing' => '1 day before',
        ],
        'class_cancelled' => [
            'enabled' => true,
            'template' => 'Pemberitahuan: Kelas {{course_name}} pada {{date}} telah DIBATALKAN. Info selengkapnya akan diberikan segera.',
        ],
        'class_rescheduled' => [
            'enabled' => true,
            'template' => 'Pengumuman: Jadwal kelas {{course_name}} diubah. Jadwal baru: {{new_date}} pukul {{new_time}} di ruangan {{new_room}}.',
        ],
        'exam_reminder' => [
            'enabled' => true,
            'template' => 'Reminder: Ujian {{exam_name}} akan dilaksanakan besok pukul {{time}} di ruangan {{room}}. Persiapkan diri Anda dengan baik!',
        ],
    ],

    'security' => [
        'allowed_numbers' => [], // Empty berarti semua nomor diizinkan
        'blocked_numbers' => [],
        'require_authentication' => false,
    ],

    'development' => [
        'debug_mode' => env('APP_DEBUG', false),
        'mock_messages' => false,
        'save_media_files' => true,
    ],
];