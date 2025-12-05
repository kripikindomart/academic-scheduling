<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'whats_app_sessions';

    protected $fillable = [
        'session_id',
        'phone_number',
        'status',
        'qr_code',
        'connection_data',
        'last_activity',
        'is_active',
    ];

    protected $casts = [
        'connection_data' => 'array',
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'session_id', 'session_id');
    }

    public function notifications()
    {
        return $this->hasMany(WhatsAppNotification::class, 'session_id', 'session_id');
    }

    // Status constants
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_CONNECTING = 'connecting';
    const STATUS_CONNECTED = 'connected';
    const STATUS_QR_GENERATED = 'qr_generated';
    const STATUS_ERROR = 'error';

    public static function getStatuses()
    {
        return [
            self::STATUS_DISCONNECTED => 'Disconnected',
            self::STATUS_CONNECTING => 'Connecting',
            self::STATUS_CONNECTED => 'Connected',
            self::STATUS_QR_GENERATED => 'QR Generated',
            self::STATUS_ERROR => 'Error',
        ];
    }
}