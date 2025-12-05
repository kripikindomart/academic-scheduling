const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const fs = require('fs');
const path = require('path');

const sessionId = process.argv[2];
if (!sessionId) {
    console.error('Session ID is required');
    process.exit(1);
}

async function disconnectSession() {
    try {
        console.log('Disconnecting session:', sessionId);

        const sessionDir = path.join(__dirname, '../../../storage/whatsapp/sessions', sessionId);

        if (!fs.existsSync(sessionDir)) {
            console.log('Session directory does not exist');
            process.exit(0);
        }

        const { state, saveCreds } = await useMultiFileAuthState(sessionDir);

        const sock = makeWASocket({
            auth: state,
            printQRInTerminal: false,
            version: [2, 3000, 1015901307]
        });

        // Wait for connection
        await new Promise((resolve, reject) => {
            const timeout = setTimeout(() => {
                reject(new Error('Connection timeout'));
            }, 10000);

            sock.ev.on('connection.update', (update) => {
                const { connection, lastDisconnect } = update;

                if (connection === 'close') {
                    clearTimeout(timeout);
                    resolve();
                }

                if (connection === 'open') {
                    clearTimeout(timeout);
                    resolve();
                }
            });

            // If connection is already established
            if (sock.user) {
                clearTimeout(timeout);
                resolve();
            }
        });

        // Logout and close connection
        await sock.logout();
        sock.ev.close();

        // Clean up session files
        try {
            if (fs.existsSync(sessionDir)) {
                const files = fs.readdirSync(sessionDir);
                for (const file of files) {
                    fs.unlinkSync(path.join(sessionDir, file));
                }
                fs.rmdirSync(sessionDir);
            }
        } catch (error) {
            console.error('Error cleaning up session files:', error);
        }

        console.log('Session disconnected successfully');
        process.exit(0);

    } catch (error) {
        console.error('Error disconnecting session:', error);
        process.exit(1);
    }
}

disconnectSession();