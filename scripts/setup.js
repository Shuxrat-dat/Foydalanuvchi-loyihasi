#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');
const crypto = require('crypto');

const rootDir = path.resolve(__dirname, '..');
const varDir = path.join(rootDir, 'var');
const jwtDir = path.join(rootDir, 'config', 'jwt');
const privateKeyFile = path.join(jwtDir, 'private.pem');
const publicKeyFile = path.join(jwtDir, 'public.pem');
const dbFile = path.join(varDir, 'data.db');

console.log('\x1b[36m%s\x1b[0m', '==> [Setup] Initializing Foydalanuvchi loyihasi environment...');

// 1. Ensure var/ and config/jwt/ directories exist
if (!fs.existsSync(varDir)) {
    fs.mkdirSync(varDir, { recursive: true });
}
if (!fs.existsSync(jwtDir)) {
    fs.mkdirSync(jwtDir, { recursive: true });
}

// 2. Ensure JWT Keys exist
if (!fs.existsSync(privateKeyFile) || !fs.existsSync(publicKeyFile)) {
    console.log('\x1b[33m%s\x1b[0m', '==> [Setup] Generating Lexik JWT keypair...');
    try {
        const passphrase = 'a5b4871c63490ecabbab689b7a96c6dea8a556a7ec1ba159fdaa7389d18bfa72';
        const { publicKey, privateKey } = crypto.generateKeyPairSync('rsa', {
            modulusLength: 2048,
            publicKeyEncoding: {
                type: 'spki',
                format: 'pem'
            },
            privateKeyEncoding: {
                type: 'pkcs8',
                format: 'pem',
                cipher: 'aes-256-cbc',
                passphrase: passphrase
            }
        });

        fs.writeFileSync(privateKeyFile, privateKey, { mode: 0o600 });
        fs.writeFileSync(publicKeyFile, publicKey, { mode: 0o644 });
        console.log('\x1b[32m%s\x1b[0m', '==> [Setup] JWT keypair successfully created.');
    } catch (err) {
        console.warn('==> [Setup] Node keygen warning:', err.message);
    }
}

// 3. Ensure SQLite schema and demo data exist
const needsDbInit = !fs.existsSync(dbFile) || fs.statSync(dbFile).size === 0;

if (needsDbInit) {
    console.log('\x1b[33m%s\x1b[0m', '==> [Setup] Initializing SQLite database schema...');
    try {
        execSync('php bin/console doctrine:schema:update --force --no-interaction', {
            cwd: rootDir,
            stdio: 'inherit'
        });
    } catch (err) {
        console.error('\x1b[31m%s\x1b[0m', '==> [Setup] Schema update error:', err.message);
    }

    console.log('\x1b[33m%s\x1b[0m', '==> [Setup] Seeding realistic demo data...');
    try {
        execSync('php bin/console app:seed-demo --no-interaction', {
            cwd: rootDir,
            stdio: 'inherit'
        });
    } catch (err) {
        console.error('\x1b[31m%s\x1b[0m', '==> [Setup] Seeding error:', err.message);
    }
} else {
    console.log('\x1b[32m%s\x1b[0m', '==> [Setup] Database var/data.db already present.');
}

console.log('\x1b[32m%s\x1b[0m', '==> [Setup] Environment successfully prepared!\n');
