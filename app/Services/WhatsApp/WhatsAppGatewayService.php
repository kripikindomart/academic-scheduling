<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppSession;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class WhatsAppGatewayService
{
    protected $sessions = [];
    protected $qrCallbacks = [];

    public function __construct()
    {
        $this->ensureStorageDirectories();
    }

    /**
     * Initialize WhatsApp session
     */
    public function initializeSession(string $sessionId = null): WhatsAppSession
    {
        $sessionId = $sessionId ?? config('whatsapp.default_session_id');

        $session = WhatsAppSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'status' => WhatsAppSession::STATUS_DISCONNECTED,
                'is_active' => false,
            ]
        );

        return $session;
    }

    /**
     * Connect to WhatsApp with QR code
     */
    public function connectWithQR(string $sessionId = null): array
    {
        $startTime = microtime(true);
        Log::channel('whatsapp')->info('Starting WhatsApp QR connection', [
            'session_id' => $sessionId,
            'start_time' => $startTime
        ]);

        // Environment validation
        $this->validateEnvironment();

        $session = $this->initializeSession($sessionId);

        try {
            Log::channel('whatsapp')->info('Updating session status to connecting', ['session_id' => $session->session_id]);

            // Update session status
            $session->update([
                'status' => WhatsAppSession::STATUS_CONNECTING,
                'qr_code' => null,
                'last_activity' => now(),
            ]);

            Log::channel('whatsapp')->info('Generating QR code', [
            'session_id' => $session->session_id,
            'elapsed_time_since_start' => (microtime(true) - $startTime) * 1000 . 'ms'
        ]);

            // Generate QR code using Node.js Baileys
            $qrStartTime = microtime(true);
            $result = $this->generateQRCode($session->session_id);
            $qrEndTime = microtime(true);

            if ($result['success']) {
                Log::channel('whatsapp')->info('QR code generated successfully', [
                    'session_id' => $session->session_id,
                    'qr_generation_time' => ($qrEndTime - $qrStartTime) * 1000 . 'ms',
                    'total_elapsed_time' => ($qrEndTime - $startTime) * 1000 . 'ms'
                ]);

                $session->update([
                    'status' => WhatsAppSession::STATUS_QR_GENERATED,
                    'qr_code' => $result['qr_code'],
                    'last_activity' => now(),
                ]);

                return [
                    'success' => true,
                    'session_id' => $session->session_id,
                    'qr_code' => $result['qr_code'],
                    'expires_in' => config('whatsapp.qr_timeout', 60),
                    'message' => 'QR code generated successfully. Please scan with WhatsApp.',
                ];
            } else {
                Log::channel('whatsapp')->error('Failed to generate QR code', [
                    'session_id' => $session->session_id,
                    'error' => $result['error']
                ]);

                throw new Exception('Failed to generate QR code: ' . $result['error']);
            }

        } catch (Exception $e) {
            Log::channel('whatsapp')->error('WhatsApp QR connection failed', [
                'session_id' => $session->session_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $session->update([
                'status' => WhatsAppSession::STATUS_ERROR,
                'last_activity' => now(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate QR code using Node.js Baileys
     */
    protected function generateQRCode(string $sessionId): array
    {
        $nodeScript = resource_path('js/whatsapp/qr-generator.js');

        Log::channel('whatsapp')->info('QR generation started', [
            'session_id' => $sessionId,
            'script_path' => $nodeScript,
            'script_exists' => file_exists($nodeScript)
        ]);

        if (!file_exists($nodeScript)) {
            Log::channel('whatsapp')->error('QR generator script not found', ['script_path' => $nodeScript]);
            return [
                'success' => false,
                'error' => 'QR generator script not found at: ' . $nodeScript,
            ];
        }

        try {
            $output = [];
            $returnCode = 0;

            $command = "node {$nodeScript} {$sessionId} 2>&1";
            Log::channel('whatsapp')->info('Executing Node.js command', [
                'session_id' => $sessionId,
                'command' => $command,
                'script_execution_start' => microtime(true)
            ]);

            $execStartTime = microtime(true);
            exec($command, $output, $returnCode);
            $execEndTime = microtime(true);

            $rawOutput = implode('', $output);
            $executionTime = ($execEndTime - $execStartTime) * 1000;

            Log::channel('whatsapp')->info('Node.js execution completed', [
                'session_id' => $sessionId,
                'return_code' => $returnCode,
                'execution_time_ms' => round($executionTime, 2),
                'output_length' => strlen($rawOutput),
                'output_preview' => substr($rawOutput, 0, 500)
            ]);

            // Parse detailed log entries from Node.js output
            $this->parseNodeJsLogs($rawOutput, $sessionId);

            if ($returnCode === 0) {
                // Parse the JSON output - library outputs multiple JSON objects
                $result = $this->parseNodeJSOutput($rawOutput);

                if ($result !== null && isset($result['qr'])) {
                    Log::channel('whatsapp')->info('QR code generation successful', [
                        'session_id' => $sessionId,
                        'qr_code_length' => strlen($result['qr'])
                    ]);

                    return [
                        'success' => true,
                        'qr_code' => $result['qr'],
                    ];
                } else {
                    Log::channel('whatsapp')->error('Invalid QR response from Node.js script', [
                        'session_id' => $sessionId,
                        'raw_output' => $rawOutput,
                        'json_decode_error' => json_last_error_msg(),
                        'result_structure' => json_encode($result)
                    ]);

                    return [
                        'success' => false,
                        'error' => 'Invalid QR response from Node.js script. Output: ' . $rawOutput,
                    ];
                }
            } else {
                Log::channel('whatsapp')->error('Node.js script execution failed', [
                    'session_id' => $sessionId,
                    'return_code' => $returnCode,
                    'full_output' => $rawOutput
                ]);

                return [
                    'success' => false,
                    'error' => 'Node.js script execution failed (code ' . $returnCode . '): ' . $rawOutput,
                ];
            }
        } catch (Exception $e) {
            Log::channel('whatsapp')->error('Failed to execute QR generation', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to execute QR generation: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $to, string $message, array $options = []): array
    {
        try {
            $sessionId = $options['session_id'] ?? config('whatsapp.default_session_id');
            $session = $this->getActiveSession($sessionId);

            if (!$session) {
                return [
                    'success' => false,
                    'error' => 'No active WhatsApp session found',
                ];
            }

            // Create message record
            $whatsappMessage = WhatsAppMessage::create([
                'session_id' => $sessionId,
                'message_id' => uniqid('msg_', true),
                'from_number' => $session->phone_number,
                'to_number' => $this->formatPhoneNumber($to),
                'message_type' => $options['type'] ?? WhatsAppMessage::TYPE_TEXT,
                'content' => ['text' => $message],
                'status' => WhatsAppMessage::STATUS_PENDING,
            ]);

            // Send message using Node.js Baileys
            $result = $this->sendViaBaileys($sessionId, $whatsappMessage->to_number, $message, $options);

            if ($result['success']) {
                $whatsappMessage->update([
                    'status' => WhatsAppMessage::STATUS_SENT,
                    'sent_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message_id' => $whatsappMessage->message_id,
                    'to' => $whatsappMessage->to_number,
                    'status' => 'sent',
                ];
            } else {
                $whatsappMessage->update([
                    'status' => WhatsAppMessage::STATUS_FAILED,
                    'error_message' => $result['error'],
                ]);

                return [
                    'success' => false,
                    'error' => $result['error'],
                    'message_id' => $whatsappMessage->message_id,
                ];
            }

        } catch (Exception $e) {
            Log::channel('whatsapp')->error('WhatsApp send message failed', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send message via Baileys Node.js
     */
    protected function sendViaBaileys(string $sessionId, string $to, string $message, array $options = []): array
    {
        $nodeScript = resource_path('js/whatsapp/message-sender.cjs');

        if (!file_exists($nodeScript)) {
            return [
                'success' => false,
                'error' => 'Message sender script not found',
            ];
        }

        $payload = [
            'session_id' => $sessionId,
            'to' => $to,
            'message' => $message,
            'options' => $options,
        ];

        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'whatsapp_payload_');
            file_put_contents($tempFile, json_encode($payload));

            $output = [];
            $returnCode = 0;

            exec("node {$nodeScript} {$tempFile} 2>&1", $output, $returnCode);

            unlink($tempFile);

            if ($returnCode === 0) {
                $result = json_decode(implode('', $output), true);
                return $result ?? ['success' => false, 'error' => 'Invalid response'];
            } else {
                return [
                    'success' => false,
                    'error' => 'Script execution failed: ' . implode('', $output),
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to send message: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get active session
     */
    protected function getActiveSession(string $sessionId): ?WhatsAppSession
    {
        return WhatsAppSession::where('session_id', $sessionId)
            ->where('is_active', true)
            ->where('status', WhatsAppSession::STATUS_CONNECTED)
            ->first();
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

    /**
     * Check session status
     */
    public function checkSessionStatus(string $sessionId = null): array
    {
        $sessionId = $sessionId ?? config('whatsapp.default_session_id');
        $session = WhatsAppSession::where('session_id', $sessionId)->first();

        if (!$session) {
            return [
                'success' => false,
                'error' => 'Session not found',
            ];
        }

        return [
            'success' => true,
            'session_id' => $session->session_id,
            'status' => $session->status,
            'is_active' => $session->is_active,
            'phone_number' => $session->phone_number,
            'last_activity' => $session->last_activity,
        ];
    }

    /**
     * Disconnect session
     */
    public function disconnectSession(string $sessionId = null): array
    {
        $sessionId = $sessionId ?? config('whatsapp.default_session_id');
        $session = WhatsAppSession::where('session_id', $sessionId)->first();

        if (!$session) {
            return [
                'success' => false,
                'error' => 'Session not found',
            ];
        }

        try {
            // Disconnect using Node.js
            $this->disconnectBaileysSession($sessionId);

            $session->update([
                'status' => WhatsAppSession::STATUS_DISCONNECTED,
                'is_active' => false,
                'qr_code' => null,
                'connection_data' => null,
                'last_activity' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Session disconnected successfully',
            ];
        } catch (Exception $e) {
            Log::channel('whatsapp')->error('WhatsApp disconnect session failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Disconnect Baileys session
     */
    protected function disconnectBaileysSession(string $sessionId): void
    {
        $nodeScript = resource_path('js/whatsapp/session-disconnect.cjs');

        if (file_exists($nodeScript)) {
            exec("node {$nodeScript} {$sessionId} 2>&1", $output, $returnCode);
        }
    }

    /**
     * Validate environment requirements
     */
    protected function validateEnvironment(): void
    {
        Log::channel('whatsapp')->info('Validating WhatsApp environment requirements');

        // Check Node.js availability
        $nodeVersion = shell_exec('node --version 2>&1');
        if (!$nodeVersion) {
            Log::channel('whatsapp')->error('Node.js is not installed or not in PATH');
            throw new Exception('Node.js is required for WhatsApp gateway but not found');
        }

        Log::channel('whatsapp')->info('Node.js version found', ['version' => trim($nodeVersion)]);

        // Check npm packages
        $packageJsonPath = base_path('package.json');
        if (!file_exists($packageJsonPath)) {
            Log::channel('whatsapp')->error('package.json not found', ['path' => $packageJsonPath]);
            throw new Exception('package.json not found at: ' . $packageJsonPath);
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true);
        $requiredPackages = ['@kripikindomart/baileys-gateway', 'qrcode'];

        foreach ($requiredPackages as $package) {
            if (!isset($packageJson['dependencies'][$package])) {
                Log::channel('whatsapp')->error('Required npm package missing', ['package' => $package]);
                throw new Exception("Required npm package '{$package}' not found in package.json");
            }
        }

        // Check node_modules directory
        $nodeModulesPath = base_path('node_modules');
        if (!is_dir($nodeModulesPath)) {
            Log::channel('whatsapp')->warning('node_modules directory not found', ['path' => $nodeModulesPath]);
        }

        // Check session storage permissions
        $sessionPath = config('whatsapp.session_storage.path', storage_path('app/whatsapp/sessions'));
        if (!is_dir($sessionPath)) {
            if (!mkdir($sessionPath, 0755, true)) {
                Log::channel('whatsapp')->error('Failed to create session directory', ['path' => $sessionPath]);
                throw new Exception("Cannot create session directory: {$sessionPath}");
            }
        }

        if (!is_writable($sessionPath)) {
            Log::channel('whatsapp')->error('Session directory not writable', ['path' => $sessionPath]);
            throw new Exception("Session directory not writable: {$sessionPath}");
        }

        Log::channel('whatsapp')->info('Environment validation completed successfully');
    }

    /**
     * Parse detailed Node.js logs for timing information
     */
    protected function parseNodeJsLogs(string $rawOutput, string $sessionId): void
    {
        $lines = explode("\n", trim($rawOutput));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Try to parse JSON log entries
            $decoded = json_decode($line, true);
            if ($decoded && isset($decoded['log'])) {
                Log::channel('whatsapp')->info('Node.js log entry', [
                    'session_id' => $sessionId,
                    'node_log' => $decoded
                ]);

                // Log timing milestones
                if (isset($decoded['elapsed_ms'])) {
                    Log::channel('whatsapp')->info('QR generation milestone', [
                        'session_id' => $sessionId,
                        'milestone' => $decoded['log'],
                        'elapsed_ms' => $decoded['elapsed_ms']
                    ]);
                }
            }
        }
    }

    /**
     * Parse Node.js output to extract final result
     * The new library outputs multiple JSON objects, we need the final one
     */
    protected function parseNodeJSOutput(string $rawOutput): ?array
    {
        Log::channel('whatsapp')->info('Parsing Node.js output', [
            'raw_output_length' => strlen($rawOutput),
            'raw_output_preview' => substr($rawOutput, 0, 200)
        ]);

        // Try to parse as single JSON first (for backward compatibility)
        $decoded = json_decode($rawOutput, true);
        if ($decoded !== null && isset($decoded['success'])) {
            Log::channel('whatsapp')->info('Parsed as single JSON object', [
                'success' => $decoded['success'],
                'has_qr' => isset($decoded['qr'])
            ]);
            return $decoded;
        }

        // If that fails, try to extract JSON objects from the concatenated output
        // Look for patterns like "}{" which indicate concatenated JSON objects
        if (strpos($rawOutput, '}{') !== false) {
            Log::channel('whatsapp')->info('Detected concatenated JSON objects, attempting to parse');

            // Split concatenated JSON objects
            $parts = explode('}{', $rawOutput);
            $jsonObjects = [];

            foreach ($parts as $index => $part) {
                if ($index === 0) {
                    // First part - just add closing brace if missing
                    $jsonStr = $part . (substr($part, -1) !== '}' ? '}' : '');
                } elseif ($index === count($parts) - 1) {
                    // Last part - add opening brace if missing
                    $jsonStr = (substr($part, 0, 1) !== '{' ? '{' : '') . $part;
                } else {
                    // Middle parts - add both braces
                    $jsonStr = (substr($part, 0, 1) !== '{' ? '{' : '') . $part . (substr($part, -1) !== '}' ? '}' : '');
                }

                if ($this->isValidJson($jsonStr)) {
                    $jsonObjects[] = json_decode($jsonStr, true);
                }
            }

            // Find the success object
            foreach ($jsonObjects as $obj) {
                if (isset($obj['success']) && $obj['success'] === true) {
                    Log::channel('whatsapp')->info('Found success object in concatenated JSON', [
                        'has_qr' => isset($obj['qr']),
                        'qr_length' => isset($obj['qr']) ? strlen($obj['qr']) : 0
                    ]);
                    return $obj;
                }
            }

            // If no success object, return the last valid one
            if (!empty($jsonObjects)) {
                $lastObj = end($jsonObjects);
                Log::channel('whatsapp')->info('Returning last valid JSON object', [
                    'success' => $lastObj['success'] ?? false,
                    'has_qr' => isset($lastObj['qr'])
                ]);
                return $lastObj;
            }
        }

        // Try splitting by newlines as another fallback
        $lines = explode("\n", trim($rawOutput));
        $lastValidJson = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if ($this->isValidJson($line)) {
                $decoded = json_decode($line, true);
                if ($decoded !== null) {
                    $lastValidJson = $decoded;
                    if (isset($decoded['success']) && $decoded['success'] === true) {
                        Log::channel('whatsapp')->info('Found success object in line-by-line parsing', [
                            'has_qr' => isset($decoded['qr'])
                        ]);
                        return $decoded;
                    }
                }
            }
        }

        Log::channel('whatsapp')->warning('Could not parse valid JSON from Node.js output', [
            'output_length' => strlen($rawOutput)
        ]);

        return $lastValidJson;
    }

    /**
     * Check if a string is valid JSON
     */
    protected function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Ensure storage directories exist
     */
    protected function ensureStorageDirectories(): void
    {
        $paths = [
            config('whatsapp.session_storage.path'),
            config('whatsapp.media_storage.path'),
        ];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Get all sessions
     */
    public function getSessions(): array
    {
        return WhatsAppSession::withCount(['messages', 'notifications'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'phone_number' => $session->phone_number,
                    'status' => $session->status,
                    'is_active' => $session->is_active,
                    'last_activity' => $session->last_activity,
                    'messages_count' => $session->messages_count,
                    'notifications_count' => $session->notifications_count,
                    'created_at' => $session->created_at,
                ];
            })
            ->toArray();
    }
}