// Import baileys-gateway using ES modules
import { BaileysGateway, EVENTS } from '@kripikindomart/baileys-gateway';
import path from 'path';
import qrcode from 'qrcode';

const sessionId = process.argv[2];
if (!sessionId) {
    console.error('Session ID is required');
    process.exit(1);
}

const startTime = Date.now();
console.log(JSON.stringify({
    log: 'QR generation process started',
    session_id: sessionId,
    timestamp: new Date().toISOString(),
    start_time: startTime
}));

async function generateQR() {
    try {
        console.log(JSON.stringify({
            log: 'Creating Baileys Gateway',
            session_id: sessionId,
            elapsed_ms: Date.now() - startTime
        }));

        // Create gateway instance with custom session folder
        const sessionFolder = path.resolve(process.cwd(), 'storage/whatsapp/sessions');
        const gateway = new BaileysGateway({
            sessionFolder: sessionFolder,
            autoLoad: false,
            maxSessions: 10
        });

        console.log(JSON.stringify({
            log: 'Gateway created, starting session',
            session_id: sessionId,
            elapsed_ms: Date.now() - startTime
        }));

        // Setup event listeners
        gateway.on(EVENTS.QR_UPDATED, (data) => {
            if (data.sessionId === sessionId) {
                const elapsed = Date.now() - startTime;
                console.log(JSON.stringify({
                    log: 'QR Code received, generating Data URL',
                    session_id: sessionId,
                    elapsed_ms: elapsed,
                    qr_data: data.qr
                }));

                // Convert QR to Data URL using qrcode library
                const qrStartTime = Date.now();

                qrcode.toDataURL(data.qr, (err, url) => {
                    const qrProcessingTime = Date.now() - qrStartTime;
                    const totalElapsed = Date.now() - startTime;

                    if (err) {
                        console.log(JSON.stringify({
                            log: 'QR code generation failed',
                            session_id: sessionId,
                            elapsed_ms: totalElapsed,
                            qr_processing_time_ms: qrProcessingTime,
                            error: err.message
                        }));
                        process.exit(1);
                    }

                    console.log(JSON.stringify({
                        log: 'QR code generated successfully',
                        session_id: sessionId,
                        elapsed_ms: totalElapsed,
                        qr_processing_time_ms: qrProcessingTime,
                        success: true,
                        qr: url
                    }));
                    process.exit(0);
                });
            }
        });

        gateway.on(EVENTS.CONNECTED, (data) => {
            if (data.sessionId === sessionId) {
                const elapsed = Date.now() - startTime;
                console.log(JSON.stringify({
                    log: 'Connection opened successfully',
                    session_id: sessionId,
                    elapsed_ms: elapsed,
                    phone: data.phoneNumber,
                    success: true,
                    connected: true
                }));
                process.exit(0);
            }
        });

        gateway.on(EVENTS.CONNECTING, (data) => {
            if (data.sessionId === sessionId) {
                console.log(JSON.stringify({
                    log: 'Session connecting...',
                    session_id: sessionId,
                    elapsed_ms: Date.now() - startTime
                }));
            }
        });

        gateway.on(EVENTS.DISCONNECTED, (data) => {
            if (data.sessionId === sessionId) {
                const elapsed = Date.now() - startTime;
                console.log(JSON.stringify({
                    log: 'Connection closed',
                    session_id: sessionId,
                    elapsed_ms: elapsed,
                    error: data.error?.message
                }));
            }
        });

        const ERROR_EVENT = 'error';
        gateway.on(ERROR_EVENT, (data) => {
            if (data.sessionId === sessionId) {
                const elapsed = Date.now() - startTime;
                console.log(JSON.stringify({
                    log: 'Connection error',
                    session_id: sessionId,
                    elapsed_ms: elapsed,
                    error: data.error?.message
                }));
            }
        });

        console.log(JSON.stringify({
            log: 'Creating WhatsApp session',
            session_id: sessionId,
            elapsed_ms: Date.now() - startTime
        }));

        // Create session (this will trigger QR generation)
        const session = await gateway.startSession(sessionId, {
            printQR: false,
            timeout: 60000
        });

        console.log(JSON.stringify({
            log: 'Session creation initiated',
            session_id: sessionId,
            elapsed_ms: Date.now() - startTime
        }));

        // Timeout after 60 seconds
        const timeoutMs = 60000;
        setTimeout(() => {
            console.log(JSON.stringify({
                log: 'QR generation timeout reached',
                session_id: sessionId,
                elapsed_ms: Date.now() - startTime,
                timeout_ms: timeoutMs,
                success: false,
                error: 'QR generation timeout after ' + timeoutMs + 'ms'
            }));
            process.exit(1);
        }, timeoutMs);

    } catch (error) {
        console.log(JSON.stringify({
            log: 'Fatal error in QR generation',
            session_id: sessionId,
            elapsed_ms: Date.now() - startTime,
            success: false,
            error: error.message,
            stack: error.stack
        }));
        process.exit(1);
    }
}

generateQR();