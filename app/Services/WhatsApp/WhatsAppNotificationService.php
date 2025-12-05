<?php

namespace App\Services\WhatsApp;

use App\Models\Student;
use App\Models\Lecturer;
use App\Models\WhatsAppNotification;
use App\Models\WhatsAppMessage;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppNotificationService
{
    protected $gateway;

    public function __construct(WhatsAppGatewayService $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Send schedule reminder to students
     */
    public function sendScheduleReminder(Schedule $schedule, array $studentIds = null): array
    {
        try {
            $recipients = $this->getScheduleRecipients($schedule, $studentIds);
            $results = [];

            foreach ($recipients as $recipient) {
                if (empty($recipient['phone'])) {
                    continue;
                }

                $message = $this->formatScheduleReminderMessage($schedule, $recipient);

                $notification = WhatsAppNotification::create([
                    'session_id' => config('whatsapp.default_session_id'),
                    'notification_type' => WhatsAppNotification::TYPE_SCHEDULE_REMINDER,
                    'recipient_number' => $this->formatPhoneNumber($recipient['phone']),
                    'recipient_name' => $recipient['name'],
                    'message_content' => $message,
                    'template_name' => 'schedule_reminder',
                    'template_data' => [
                        'template_name' => 'schedule_reminder',
                        'variables' => [
                            'student_name' => $recipient['name'],
                            'course_name' => $schedule->course->name ?? 'Unknown Course',
                            'time' => $schedule->start_time->format('H:i'),
                            'room' => $schedule->room->name ?? 'Unknown Room',
                            'date' => $schedule->date->format('d/m/Y'),
                        ]
                    ],
                    'status' => WhatsAppNotification::STATUS_PENDING,
                ]);

                $result = $this->gateway->sendMessage(
                    $recipient['phone'],
                    $message,
                    [
                        'type' => WhatsAppMessage::TYPE_TEXT,
                        'session_id' => config('whatsapp.default_session_id'),
                        'notification_id' => $notification->id,
                    ]
                );

                if ($result['success']) {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_SENT,
                        'sent_at' => now(),
                    ]);
                } else {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_FAILED,
                        'error_message' => $result['error'],
                    ]);
                }

                $results[] = [
                    'recipient' => $recipient['name'],
                    'phone' => $recipient['phone'],
                    'success' => $result['success'],
                    'message' => $result['error'] ?? 'Message sent successfully',
                ];
            }

            return [
                'success' => true,
                'total_recipients' => count($recipients),
                'sent_count' => count(array_filter($results, fn($r) => $r['success'])),
                'failed_count' => count(array_filter($results, fn($r) => !$r['success'])),
                'results' => $results,
            ];

        } catch (Exception $e) {
            Log::channel('whatsapp')->error('Schedule reminder notification failed', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send class cancelled notification
     */
    public function sendClassCancelledNotification(Schedule $schedule, string $reason = null): array
    {
        try {
            $recipients = $this->getScheduleRecipients($schedule);
            $results = [];

            foreach ($recipients as $recipient) {
                if (empty($recipient['phone'])) {
                    continue;
                }

                $message = $this->formatClassCancelledMessage($schedule, $recipient, $reason);

                $notification = WhatsAppNotification::create([
                    'session_id' => config('whatsapp.default_session_id'),
                    'notification_type' => WhatsAppNotification::TYPE_CLASS_CANCELLED,
                    'recipient_number' => $this->formatPhoneNumber($recipient['phone']),
                    'recipient_name' => $recipient['name'],
                    'message_content' => $message,
                    'template_name' => 'class_cancelled',
                    'template_data' => [
                        'template_name' => 'class_cancelled',
                        'variables' => [
                            'course_name' => $schedule->course->name ?? 'Unknown Course',
                            'date' => $schedule->date->format('d/m/Y'),
                            'time' => $schedule->start_time->format('H:i'),
                            'reason' => $reason ?? 'Alasan tidak disebutkan',
                        ]
                    ],
                    'status' => WhatsAppNotification::STATUS_PENDING,
                ]);

                $result = $this->gateway->sendMessage($recipient['phone'], $message);

                if ($result['success']) {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_SENT,
                        'sent_at' => now(),
                    ]);
                } else {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_FAILED,
                        'error_message' => $result['error'],
                    ]);
                }

                $results[] = [
                    'recipient' => $recipient['name'],
                    'success' => $result['success'],
                    'message' => $result['error'] ?? 'Notification sent successfully',
                ];
            }

            return [
                'success' => true,
                'total_recipients' => count($recipients),
                'sent_count' => count(array_filter($results, fn($r) => $r['success'])),
                'results' => $results,
            ];

        } catch (Exception $e) {
            Log::channel('whatsapp')->error('Class cancelled notification failed', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send class rescheduled notification
     */
    public function sendClassRescheduledNotification(Schedule $oldSchedule, Schedule $newSchedule): array
    {
        try {
            $recipients = $this->getScheduleRecipients($oldSchedule);
            $results = [];

            foreach ($recipients as $recipient) {
                if (empty($recipient['phone'])) {
                    continue;
                }

                $message = $this->formatClassRescheduledMessage($oldSchedule, $newSchedule, $recipient);

                $notification = WhatsAppNotification::create([
                    'session_id' => config('whatsapp.default_session_id'),
                    'notification_type' => WhatsAppNotification::TYPE_CLASS_RESCHEDULED,
                    'recipient_number' => $this->formatPhoneNumber($recipient['phone']),
                    'recipient_name' => $recipient['name'],
                    'message_content' => $message,
                    'template_name' => 'class_rescheduled',
                    'template_data' => [
                        'template_name' => 'class_rescheduled',
                        'variables' => [
                            'course_name' => $newSchedule->course->name ?? 'Unknown Course',
                            'new_date' => $newSchedule->date->format('d/m/Y'),
                            'new_time' => $newSchedule->start_time->format('H:i'),
                            'new_room' => $newSchedule->room->name ?? 'Unknown Room',
                            'old_date' => $oldSchedule->date->format('d/m/Y'),
                            'old_time' => $oldSchedule->start_time->format('H:i'),
                        ]
                    ],
                    'status' => WhatsAppNotification::STATUS_PENDING,
                ]);

                $result = $this->gateway->sendMessage($recipient['phone'], $message);

                if ($result['success']) {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_SENT,
                        'sent_at' => now(),
                    ]);
                } else {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_FAILED,
                        'error_message' => $result['error'],
                    ]);
                }

                $results[] = [
                    'recipient' => $recipient['name'],
                    'success' => $result['success'],
                    'message' => $result['error'] ?? 'Reschedule notification sent successfully',
                ];
            }

            return [
                'success' => true,
                'total_recipients' => count($recipients),
                'sent_count' => count(array_filter($results, fn($r) => $r['success'])),
                'results' => $results,
            ];

        } catch (Exception $e) {
            Log::channel('whatsapp')->error('Class rescheduled notification failed', [
                'old_schedule_id' => $oldSchedule->id,
                'new_schedule_id' => $newSchedule->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send custom notification
     */
    public function sendCustomNotification(string $title, string $message, array $recipients): array
    {
        try {
            $results = [];

            foreach ($recipients as $recipient) {
                $phoneNumber = is_array($recipient) ? $recipient['phone'] : $recipient;
                $recipientName = is_array($recipient) ? $recipient['name'] : $recipient;

                if (empty($phoneNumber)) {
                    continue;
                }

                $notification = WhatsAppNotification::create([
                    'session_id' => config('whatsapp.default_session_id'),
                    'notification_type' => WhatsAppNotification::TYPE_CUSTOM,
                    'recipient_number' => $this->formatPhoneNumber($phoneNumber),
                    'recipient_name' => $recipientName,
                    'message_content' => $message,
                    'status' => WhatsAppNotification::STATUS_PENDING,
                ]);

                $result = $this->gateway->sendMessage($phoneNumber, $message);

                if ($result['success']) {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_SENT,
                        'sent_at' => now(),
                    ]);
                } else {
                    $notification->update([
                        'status' => WhatsAppNotification::STATUS_FAILED,
                        'error_message' => $result['error'],
                    ]);
                }

                $results[] = [
                    'recipient' => $recipientName,
                    'phone' => $phoneNumber,
                    'success' => $result['success'],
                    'message' => $result['error'] ?? 'Message sent successfully',
                ];
            }

            return [
                'success' => true,
                'total_recipients' => count($recipients),
                'sent_count' => count(array_filter($results, fn($r) => $r['success'])),
                'results' => $results,
            ];

        } catch (Exception $e) {
            Log::channel('whatsapp')->error('Custom notification failed', [
                'title' => $title,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get recipients for schedule
     */
    protected function getScheduleRecipients(Schedule $schedule, array $studentIds = null): array
    {
        $recipients = [];

        // Get students
        if ($schedule->school_class) {
            $studentsQuery = $schedule->school_class->students();

            if ($studentIds) {
                $studentsQuery->whereIn('students.id', $studentIds);
            }

            $students = $studentsQuery->get();

            foreach ($students as $student) {
                $recipients[] = [
                    'name' => $student->name,
                    'phone' => $student->phone,
                    'type' => 'student',
                    'id' => $student->id,
                ];
            }
        }

        // Get lecturer
        if ($schedule->lecturer) {
            $recipients[] = [
                'name' => $schedule->lecturer->name,
                'phone' => $schedule->lecturer->phone,
                'type' => 'lecturer',
                'id' => $schedule->lecturer->id,
            ];
        }

        return $recipients;
    }

    /**
     * Format schedule reminder message
     */
    protected function formatScheduleReminderMessage(Schedule $schedule, array $recipient): string
    {
        $template = config('whatsapp.notifications.schedule_reminder.template');

        $variables = [
            '{{student_name}}' => $recipient['name'],
            '{{course_name}}' => $schedule->course->name ?? 'Unknown Course',
            '{{time}}' => $schedule->start_time->format('H:i'),
            '{{room}}' => $schedule->room->name ?? 'Unknown Room',
            '{{date}}' => $schedule->date->format('d/m/Y'),
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }

    /**
     * Format class cancelled message
     */
    protected function formatClassCancelledMessage(Schedule $schedule, array $recipient, ?string $reason): string
    {
        $template = config('whatsapp.notifications.class_cancelled.template');

        $variables = [
            '{{course_name}}' => $schedule->course->name ?? 'Unknown Course',
            '{{date}}' => $schedule->date->format('d/m/Y'),
            '{{time}}' => $schedule->start_time->format('H:i'),
            '{{reason}}' => $reason ?? 'Alasan tidak disebutkan',
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }

    /**
     * Format class rescheduled message
     */
    protected function formatClassRescheduledMessage(Schedule $oldSchedule, Schedule $newSchedule, array $recipient): string
    {
        $template = config('whatsapp.notifications.class_rescheduled.template');

        $variables = [
            '{{course_name}}' => $newSchedule->course->name ?? 'Unknown Course',
            '{{new_date}}' => $newSchedule->date->format('d/m/Y'),
            '{{new_time}}' => $newSchedule->start_time->format('H:i'),
            '{{new_room}}' => $newSchedule->room->name ?? 'Unknown Room',
            '{{old_date}}' => $oldSchedule->date->format('d/m/Y'),
            '{{old_time}}' => $oldSchedule->start_time->format('H:i'),
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStatistics(): array
    {
        $total = WhatsAppNotification::count();
        $sent = WhatsAppNotification::where('status', WhatsAppNotification::STATUS_SENT)->count();
        $failed = WhatsAppNotification::where('status', WhatsAppNotification::STATUS_FAILED)->count();
        $pending = WhatsAppNotification::where('status', WhatsAppNotification::STATUS_PENDING)->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $total > 0 ? round(($sent / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(int $limit = 10): array
    {
        return WhatsAppNotification::with('session')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->notification_type,
                    'recipient' => $notification->recipient_name,
                    'recipient_number' => $notification->recipient_number,
                    'status' => $notification->status,
                    'created_at' => $notification->created_at,
                    'sent_at' => $notification->sent_at,
                    'message_preview' => substr($notification->message_content, 0, 50) . '...',
                ];
            })
            ->toArray();
    }

    /**
     * Format phone number for WhatsApp
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading zeros and add country code if needed
        if (strlen($phone) <= 10) {
            // Assume Indonesian number
            $phone = '62' . ltrim($phone, '0');
        } elseif (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone . '@s.whatsapp.net';
    }
}