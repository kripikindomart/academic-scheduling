# WhatsApp Gateway Documentation

## Overview

WhatsApp Gateway untuk Academic Scheduling System menggunakan Baileys library untuk mengirim pesan WhatsApp langsung dari aplikasi. Gateway ini mendukung:

- Multi-session WhatsApp connections
- QR code authentication
- Text, image, and document messages
- Template-based notifications
- Schedule reminders and notifications
- Real-time connection status monitoring

## Installation & Setup

### 1. Install Dependencies

Dependencies sudah terinstall:
- `@whiskeysockets/baileys` - WhatsApp Web library
- `qrcode` - QR code generation
- `qr-image` - QR code image processing

### 2. Environment Configuration

Tambahkan ke `.env` file:
```env
# WhatsApp Configuration
WHATSAPP_ENABLED=true
WHATSAPP_DEFAULT_SESSION_ID=default
WHATSAPP_QR_TIMEOUT=60
WHATSAPP_MESSAGE_RETRY_LIMIT=3
WHATSAPP_AUTO_RECONNECT=true
WHATSAPP_CONNECTION_TIMEOUT=30
WHATSAPP_SESSION_STORAGE_PATH=storage/whatsapp/sessions
WHATSAPP_MEDIA_STORAGE_PATH=storage/whatsapp/media
```

### 3. Database Migration

Run migration untuk membuat tabel WhatsApp:
```bash
php artisan migrate
```

Tabel yang dibuat:
- `whatsapp_sessions` - Session management
- `whatsapp_messages` - Message logs
- `whatsapp_notifications` - Notification logs

### 4. Storage Directories

Create WhatsApp storage directories:
```bash
php artisan migrate:create_storage_whatsapp_directories
```

## Usage

### 1. Access WhatsApp Gateway

Login ke aplikasi dan navigasi ke **System → WhatsApp Gateway** melalui sidebar.

### 2. Connect WhatsApp

1. Klik "Add Session" untuk membuat session baru
2. Klik "Connect" pada session yang ingin dihubungkan
3. Scan QR code yang muncul dengan WhatsApp
4. Tunggu hingga status berubah menjadi "Connected"

### 3. Send Messages

#### Manual Message:
1. Isi nomor telepon recipient (format: 08123456789)
2. Tulis pesan
3. Pilih message type (text, image, document)
4. Klik "Send Message"

#### Template Notifications:
- **Schedule Reminder**: Kirim reminder jadwal ke mahasiswa
- **Class Cancelled**: Beritahu pembatalan kelas
- **Class Rescheduled**: Beritahu perubahan jadwal
- **Custom**: Kirim notifikasi custom

### 4. API Endpoints

Semua endpoints memerlukan authentication token:

#### Session Management
```http
GET /api/whatsapp/sessions                    # List all sessions
POST /api/whatsapp/sessions/initialize         # Initialize new session
POST /api/whatsapp/sessions/connect-qr         # Connect with QR code
GET /api/whatsapp/sessions/status             # Check session status
POST /api/whatsapp/sessions/disconnect        # Disconnect session
POST /api/whatsapp/test-connection            # Test connection
```

#### Message Sending
```http
POST /api/whatsapp/send-message               # Send custom message
```

#### Notifications
```http
POST /api/whatsapp/notifications/schedule-reminder     # Send schedule reminder
POST /api/whatsapp/notifications/class-cancelled       # Send class cancelled notification
POST /api/whatsapp/notifications/class-rescheduled     # Send class rescheduled notification
POST /api/whatsapp/notifications/custom               # Send custom notification
```

#### Analytics
```http
GET /api/whatsapp/notifications/statistics    # Get notification statistics
GET /api/whatsapp/notifications/recent        # Get recent notifications
```

## API Examples

### Send Message
```javascript
const response = await fetch('/api/whatsapp/send-message', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    to: '08123456789',
    message: 'Hello from Academic Scheduling System!',
    type: 'text'
  })
});
```

### Send Schedule Reminder
```javascript
const response = await fetch('/api/whatsapp/notifications/schedule-reminder', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    schedule_id: 123,
    student_ids: [1, 2, 3] // Optional, send to specific students
  })
});
```

## Configuration Options

### WhatsApp Gateway Configuration (`config/whatsapp.php`)

```php
return [
    'enabled' => env('WHATSAPP_ENABLED', true),
    'default_session_id' => env('WHATSAPP_DEFAULT_SESSION_ID', 'default'),
    'qr_timeout' => env('WHATSAPP_QR_TIMEOUT', 60),
    'message_retry_limit' => env('WHATSAPP_MESSAGE_RETRY_LIMIT', 3),
    'auto_reconnect' => env('WHATSAPP_AUTO_RECONNECT', true),
    'connection_timeout' => env('WHATSAPP_CONNECTION_TIMEOUT', 30),

    'rate_limit' => [
        'messages_per_minute' => 30,
        'messages_per_hour' => 1000,
        'messages_per_day' => 10000,
    ],

    'notifications' => [
        'schedule_reminder' => [
            'enabled' => true,
            'template' => 'Hai {{student_name}}, jadwal kuliah {{course_name}} besok pukul {{time}} di ruangan {{room}}.',
        ],
        // ... other notification templates
    ],
];
```

## Security Considerations

1. **Phone Number Format**: Semua nomor telepon akan diformat otomatis ke format WhatsApp international
2. **Rate Limiting**: Gateway memiliki built-in rate limiting untuk mencegah spam
3. **Session Security**: Session data disimpan secara terenkripsi
4. **Authentication**: Semua API endpoints memerlukan valid authentication token

## Troubleshooting

### Common Issues

1. **QR Code Not Appearing**
   - Pastikan Node.js terinstall
   - Check WhatsApp storage directories permissions
   - Verify Baileys library installation

2. **Connection Timeout**
   - Increase `WHATSAPP_QR_TIMEOUT` di .env
   - Check network connectivity
   - Restart WhatsApp session

3. **Message Not Sending**
   - Verify session status is "Connected"
   - Check recipient phone number format
   - Review rate limit settings

4. **Session Disconnected**
   - WhatsApp Web session expired
   - Network connectivity issues
   - WhatsApp account banned/suspended

### Logs

Check logs untuk debugging:
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# WhatsApp specific logs
grep "whatsapp" storage/logs/laravel.log
```

### Reset WhatsApp Gateway

Untuk reset semua WhatsApp data:
```bash
# Reset database tables
php artisan migrate:rollback --step=4
php artisan migrate

# Clear storage directories
rm -rf storage/whatsapp/*

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Features Status

- [x] QR Code Authentication
- [x] Multi-Session Support
- [x] Text Messages
- [x] Message History
- [x] Notification Templates
- [x] Rate Limiting
- [x] Session Management
- [ ] Image Messages (in progress)
- [ ] Document Messages (in progress)
- [ ] Webhook Support (planned)
- [ ] Bulk Messaging (planned)
- [ ] Message Scheduling (planned)

## Support

For issues and feature requests:
1. Check this documentation first
2. Review application logs
3. Test API endpoints manually
4. Contact development team

## License

WhatsApp Gateway is part of Academic Scheduling System and follows the same license terms.