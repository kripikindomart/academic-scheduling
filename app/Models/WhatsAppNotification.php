<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppNotification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'whats_app_notifications';

    protected $fillable = [
        'session_id',
        'notification_type',
        'recipient_number',
        'recipient_name',
        'message_content',
        'template_name',
        'template_data',
        'status',
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'read_at',
        'error_message',
        'retry_count',
        'last_retry_at',
    ];

    protected $casts = [
        'template_data' => 'json',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'last_retry_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(WhatsAppSession::class, 'session_id', 'session_id');
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'notification_id');
    }

    // Notification type constants
    const TYPE_SCHEDULE_REMINDER = 'schedule_reminder';
    const TYPE_CLASS_CANCELLED = 'class_cancelled';
    const TYPE_CLASS_RESCHEDULED = 'class_rescheduled';
    const TYPE_EXAM_REMINDER = 'exam_reminder';
    const TYPE_ASSIGNMENT_REMINDER = 'assignment_reminder';
    const TYPE_GENERAL_NOTIFICATION = 'general_notification';
    const TYPE_CUSTOM = 'custom';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';
    const STATUS_FAILED = 'failed';

    public static function getNotificationTypes()
    {
        return [
            self::TYPE_SCHEDULE_REMINDER => 'Schedule Reminder',
            self::TYPE_CLASS_CANCELLED => 'Class Cancelled',
            self::TYPE_CLASS_RESCHEDULED => 'Class Rescheduled',
            self::TYPE_EXAM_REMINDER => 'Exam Reminder',
            self::TYPE_ASSIGNMENT_REMINDER => 'Assignment Reminder',
            self::TYPE_GENERAL_NOTIFICATION => 'General Notification',
            self::TYPE_CUSTOM => 'Custom',
        ];
    }

    public function getTemplateNameAttribute()
    {
        return $this->template_data['template_name'] ?? null;
    }

    public function getFormattedMessageAttribute()
    {
        if ($this->template_name && $this->template_data) {
            return $this->processTemplate();
        }

        return $this->message_content;
    }

    private function processTemplate()
    {
        $template = $this->template_data;
        $message = $this->message_content ?? '';

        if (isset($template['variables']) && is_array($template['variables'])) {
            foreach ($template['variables'] as $key => $value) {
                $message = str_replace('{{' . $key . '}}', $value, $message);
            }
        }

        return $message;
    }
}