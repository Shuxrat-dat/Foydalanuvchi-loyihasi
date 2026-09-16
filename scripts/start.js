#!/usr/bin/env node

const { spawn } = require('child_process');
const path = require('path');

const rootDir = path.resolve(__dirname, '..');

// Run setup first
require('./setup.js');

const PORT = process.env.PORT || '8000';
const HOST = process.env.HOST || '127.0.0.1';

console.log('\x1b[36m%s\x1b[0m', '=======================================================');
console.log('\x1b[1m\x1b[32m%s\x1b[0m', '   🚀 FOYDALANUVCHI LOYIHASI — SERVER STARTED');
console.log('\x1b[36m%s\x1b[0m', '=======================================================');
console.log('\x1b[1m%s\x1b[0m', `   Web Dashboard:   http://${HOST}:${PORT}/`);
console.log('\x1b[1m%s\x1b[0m', `   Swagger API UI:  http://${HOST}:${PORT}/api`);
console.log('\x1b[36m%s\x1b[0m', '-------------------------------------------------------');
console.log('\x1b[33m%s\x1b[0m', '   Demo Credentials for Video & Testing:');
console.log('   • Admin:    admin@example.com    / admin123');
console.log('   • Manager:  manager@example.com  / manager123');
console.log('   • User:     user@example.com     / user123');
console.log('\x1b[36m%s\x1b[0m', '-------------------------------------------------------');
console.log('   Press Ctrl+C to stop the server.\n');

const phpServer = spawn('php', ['-S', `${HOST}:${PORT}`, '-t', 'public'], {
    cwd: rootDir,
    stdio: 'inherit'
});

phpServer.on('error', (err) => {
    console.error('\x1b[31m%s\x1b[0m', `Failed to start PHP server: ${err.message}`);
    process.exit(1);
});

phpServer.on('close', (code) => {
    if (code !== 0) {
        console.log(`PHP server exited with code ${code}`);
    }
    process.exit(code || 0);
});

process.on('SIGINT', () => {
    console.log('\nStopping server...');
    phpServer.kill('SIGINT');
    process.exit(0);
});

process.on('SIGTERM', () => {
    phpServer.kill('SIGTERM');
    process.exit(0);
});
