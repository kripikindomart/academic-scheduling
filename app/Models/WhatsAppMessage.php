<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'whats_app_messages';

    protected $fillable = [
        'session_id',
        'message_id',
        'from_number',
        'to_number',
        'message_type',
        'content',
        'media_url',
        'media_type',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'error_message',
    ];

    protected $casts = [
        'content' => 'json',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(WhatsAppSession::class, 'session_id', 'session_id');
    }

    // Message type constants
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_DOCUMENT = 'document';
    const TYPE_TEMPLATE = 'template';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';
    const STATUS_FAILED = 'failed';

    public static function getMessageTypes()
    {
        return [
            self::TYPE_TEXT => 'Text',
            self::TYPE_IMAGE => 'Image',
            self::TYPE_VIDEO => 'Video',
            self::TYPE_AUDIO => 'Audio',
            self::TYPE_DOCUMENT => 'Document',
            self::TYPE_TEMPLATE => 'Template',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_READ => 'Read',
            self::STATUS_FAILED => 'Failed',
        ];
    }
}