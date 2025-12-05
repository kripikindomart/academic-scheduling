<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsApp\WhatsAppGatewayService;
use App\Services\WhatsApp\WhatsAppNotificationService;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WhatsAppController extends Controller
{
    protected $gateway;
    protected $notificationService;

    public function __construct(WhatsAppGatewayService $gateway, WhatsAppNotificationService $notificationService)
    {
        $this->gateway = $gateway;
        $this->notificationService = $notificationService;
    }

    /**
     * Initialize WhatsApp session
     */
    public function initializeSession(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->get('session_id');
            $session = $this->gateway->initializeSession($sessionId);

            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'status' => $session->status,
                    'is_active' => $session->is_active,
                    'phone_number' => $session->phone_number,
                    'last_activity' => $session->last_activity,
                ],
            ]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Initialize WhatsApp session failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Connect with QR code
     */
    public function connectWithQR(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->get('session_id');

            Log::info('WhatsApp QR connection request received', [
                'session_id' => $sessionId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $result = $this->gateway->connectWithQR($sessionId);

            Log::info('WhatsApp QR connection completed', [
                'session_id' => $sessionId,
                'success' => $result['success'],
                'has_qr_code' => isset($result['qr_code']),
                'error' => $result['error'] ?? null
            ]);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('WhatsApp QR connection failed', [
                'session_id' => $request->get('session_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check session status
     */
    public function checkSessionStatus(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->get('session_id');
            $result = $this->gateway->checkSessionStatus($sessionId);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Check WhatsApp session status failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect session
     */
    public function disconnectSession(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->get('session_id');
            $result = $this->gateway->disconnectSession($sessionId);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('WhatsApp disconnect session failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete session
     */
    public function deleteSession(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->get('session_id');

            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session ID is required',
                ], 422);
            }

            // Disconnect session first
            $this->gateway->disconnectSession($sessionId);

            // Find and delete the session
            $session = \App\Models\WhatsAppSession::where('session_id', $sessionId)->first();

            if ($session) {
                // Delete related messages and notifications first
                $session->messages()->delete();
                $session->notifications()->delete();
                $session->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Session deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Delete WhatsApp session failed', [
                'session_id' => $request->get('session_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'to' => 'required|string',
                'message' => 'required|string',
                'type' => 'nullable|in:text,image,document',
                'session_id' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->gateway->sendMessage(
                $request->get('to'),
                $request->get('message'),
                [
                    'type' => $request->get('type', 'text'),
                    'session_id' => $request->get('session_id'),
                ]
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Send WhatsApp message failed', [
                'to' => $request->get('to'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send schedule reminder
     */
    public function sendScheduleReminder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'schedule_id' => 'required|exists:schedules,id',
                'student_ids' => 'nullable|array',
                'student_ids.*' => 'exists:students,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $schedule = Schedule::with(['course', 'room', 'school_class.students', 'lecturer'])
                ->findOrFail($request->get('schedule_id'));

            $result = $this->notificationService->sendScheduleReminder(
                $schedule,
                $request->get('student_ids')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Send schedule reminder failed', [
                'schedule_id' => $request->get('schedule_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send class cancelled notification
     */
    public function sendClassCancelledNotification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'schedule_id' => 'required|exists:schedules,id',
                'reason' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $schedule = Schedule::with(['course', 'room', 'school_class.students', 'lecturer'])
                ->findOrFail($request->get('schedule_id'));

            $result = $this->notificationService->sendClassCancelledNotification(
                $schedule,
                $request->get('reason')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Send class cancelled notification failed', [
                'schedule_id' => $request->get('schedule_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send class rescheduled notification
     */
    public function sendClassRescheduledNotification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_schedule_id' => 'required|exists:schedules,id',
                'new_schedule_id' => 'required|exists:schedules,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $oldSchedule = Schedule::with(['course', 'room'])
                ->findOrFail($request->get('old_schedule_id'));

            $newSchedule = Schedule::with(['course', 'room'])
                ->findOrFail($request->get('new_schedule_id'));

            $result = $this->notificationService->sendClassRescheduledNotification(
                $oldSchedule,
                $newSchedule
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Send class rescheduled notification failed', [
                'old_schedule_id' => $request->get('old_schedule_id'),
                'new_schedule_id' => $request->get('new_schedule_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send custom notification
     */
    public function sendCustomNotification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'recipients' => 'required|array|min:1',
                'recipients.*.name' => 'required|string',
                'recipients.*.phone' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->notificationService->sendCustomNotification(
                $request->get('title'),
                $request->get('message'),
                $request->get('recipients')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Send custom notification failed', [
                'title' => $request->get('title'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all sessions
     */
    public function getSessions(): JsonResponse
    {
        try {
            $sessions = $this->gateway->getSessions();

            return response()->json([
                'success' => true,
                'sessions' => $sessions,
            ]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Get WhatsApp sessions failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStatistics(): JsonResponse
    {
        try {
            $stats = $this->notificationService->getNotificationStatistics();

            return response()->json([
                'success' => true,
                'statistics' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Get notification statistics failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $notifications = $this->notificationService->getRecentNotifications($limit);

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
            ]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Get recent notifications failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test WhatsApp connection
     */
    public function testConnection(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->get('session_id');
            $result = $this->gateway->checkSessionStatus($sessionId);

            if ($result['success'] && $result['status'] === 'connected') {
                // Send test message
                $testMessage = 'Test message from Academic Scheduling System';
                $testResult = $this->gateway->sendMessage(
                    $result['phone_number'] ?? 'test@c.us',
                    $testMessage,
                    ['session_id' => $sessionId]
                );

                return response()->json([
                    'success' => true,
                    'connection_status' => $result,
                    'test_message' => $testResult,
                ]);
            }

            return response()->json([
                'success' => false,
                'connection_status' => $result,
                'message' => 'WhatsApp is not connected',
            ]);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Test WhatsApp connection failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}