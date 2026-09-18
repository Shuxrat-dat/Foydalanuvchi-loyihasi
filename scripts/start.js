#!/usr/bin/env node

const { spawn } = require('child_process');
const path = require('path');

const rootDir = path.resolve(__dirname, '..');

require('./setup.js');

const PORT = process.env.PORT || '8000';
const HOST = process.env.HOST || '127.0.0.1';

console.log('\x1b[36m%s\x1b[0m', '=======================================================');
console.log('\x1b[1m\x1b[32m%s\x1b[0m', '   🚀 FOYDALANUVCHI LOYIHASI — SERVER ISHLATILDI');
console.log('\x1b[36m%s\x1b[0m', '=======================================================');
console.log('\x1b[1m%s\x1b[0m', `   Veb paneli:          http://${HOST}:${PORT}/`);
console.log('\x1b[1m%s\x1b[0m', `   Swagger API:         http://${HOST}:${PORT}/api`);
console.log('\x1b[36m%s\x1b[0m', '-------------------------------------------------------');
console.log('\x1b[33m%s\x1b[0m', '   Video va testlash uchun demo kirish maʼlumotlari:');
console.log('   • Admin:    admin@example.com    / admin123');
console.log('   • Menejjer: manager@example.com / manager123');
console.log('   • Foydalanuvchi: user@example.com / user123');
console.log('\x1b[36m%s\x1b[0m', '-------------------------------------------------------');
console.log('   Serverni toʻxtatish uchun Ctrl+C bosing.\n');

const phpServer = spawn('php', ['-S', `${HOST}:${PORT}`, '-t', 'public'], {
    cwd: rootDir,
    stdio: 'inherit'
});

phpServer.on('error', (err) => {
    console.error('\x1b[31m%s\x1b[0m', `PHP serverini ishga tushirib boʻlmadi: ${err.message}`);
    process.exit(1);
});

phpServer.on('close', (code) => {
    if (code !== 0) {
        console.log(`PHP server ${code} kodi bilan toʻxtadi`);
    }
    process.exit(code || 0);
});

process.on('SIGINT', () => {
    console.log('\nServer toʻxtatilmoqda...');
    phpServer.kill('SIGINT');
    process.exit(0);
});

process.on('SIGTERM', () => {
    phpServer.kill('SIGTERM');
    process.exit(0);
});
