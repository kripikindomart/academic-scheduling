const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const fs = require('fs');
const path = require('path');

const payloadFile = process.argv[2];
if (!payloadFile) {
    console.error('Payload file is required');
    process.exit(1);
}

// Read payload
let payload;
try {
    const payloadData = fs.readFileSync(payloadFile, 'utf8');
    payload = JSON.parse(payloadData);
} catch (error) {
    console.error('Error reading payload:', error);
    console.log(JSON.stringify({
        success: false,
        error: 'Invalid payload file'
    }));
    process.exit(1);
}

const { session_id, to, message, options } = payload;

async function sendMessage() {
    try {
        console.log('Sending message for session:', session_id);

        const sessionDir = path.join(__dirname, '../../../storage/whatsapp/sessions', session_id);

        if (!fs.existsSync(sessionDir)) {
            console.log(JSON.stringify({
                success: false,
                error: 'Session not found'
            }));
            process.exit(1);
        }

        const { state, saveCreds } = await useMultiFileAuthState(sessionDir);

        const sock = makeWASocket({
            auth: state,
            printQRInTerminal: false,
            version: [2, 3000, 1015901307]
        });

        let connectionOpen = false;

        // Wait for connection
        await new Promise((resolve, reject) => {
            const timeout = setTimeout(() => {
                reject(new Error('Connection timeout'));
            }, 30000);

            sock.ev.on('connection.update', (update) => {
                const { connection, lastDisconnect } = update;

                if (connection === 'close') {
                    const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                    clearTimeout(timeout);
                    reject(new Error('Connection closed: ' + (lastDisconnect?.error?.message || 'Unknown')));
                }

                if (connection === 'open') {
                    connectionOpen = true;
                    clearTimeout(timeout);
                    resolve();
                }
            });
        });

        if (!connectionOpen) {
            throw new Error('Failed to establish connection');
        }

        // Format recipient number
        const formattedTo = to.includes('@') ? to : to + '@s.whatsapp.net';

        // Send message based on type
        let result;
        const messageType = options.type || 'text';

        switch (messageType) {
            case 'text':
                result = await sock.sendMessage(formattedTo, {
                    text: message
                });
                break;

            case 'image':
                if (options.imageUrl) {
                    result = await sock.sendMessage(formattedTo, {
                        image: { url: options.imageUrl },
                        caption: message
                    });
                } else {
                    throw new Error('Image URL is required for image messages');
                }
                break;

            case 'document':
                if (options.documentUrl) {
                    result = await sock.sendMessage(formattedTo, {
                        document: { url: options.documentUrl },
                        fileName: options.fileName || 'document',
                        caption: message
                    });
                } else {
                    throw new Error('Document URL is required for document messages');
                }
                break;

            default:
                result = await sock.sendMessage(formattedTo, {
                    text: message
                });
        }

        // Close connection
        sock.ev.close();

        console.log(JSON.stringify({
            success: true,
            message_id: result.key.id,
            session_id: session_id,
            to: formattedTo,
            status: 'sent',
            timestamp: new Date().toISOString()
        }));

        process.exit(0);

    } catch (error) {
        console.error('Error sending message:', error);
        console.log(JSON.stringify({
            success: false,
            error: error.message,
            session_id: session_id,
            to: to
        }));
        process.exit(1);
    }
}

sendMessage();