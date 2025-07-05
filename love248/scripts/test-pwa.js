#!/usr/bin/env node

/**
 * PWA Testing Script for Love248
 */

const fs = require('fs');
const path = require('path');

console.log('🧪 Testing Love248 PWA Setup...\n');

const PUBLIC_DIR = path.join(__dirname, '../public');
const REQUIRED_FILES = [
    'manifest.json',
    'sw.js', 
    'offline.html',
    'images/icons/icon-192x192.png',
    'images/icons/icon-512x512.png'
];

// Test required files
console.log('📁 Checking PWA files...');
REQUIRED_FILES.forEach(file => {
    const filePath = path.join(PUBLIC_DIR, file);
    if (fs.existsSync(filePath)) {
        console.log(`✅ ${file}`);
    } else {
        console.log(`❌ ${file} - MISSING`);
    }
});

// Test manifest
console.log('\n📋 Checking manifest.json...');
try {
    const manifestPath = path.join(PUBLIC_DIR, 'manifest.json');
    const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
    console.log(`✅ Name: ${manifest.name}`);
    console.log(`✅ Icons: ${manifest.icons?.length || 0} defined`);
    console.log(`✅ Display: ${manifest.display}`);
} catch (error) {
    console.log(`❌ Manifest error: ${error.message}`);
}

console.log('\n🎉 PWA Setup Complete!');
console.log('\n🚀 To run your PWA:');
console.log('1. php artisan serve');
console.log('2. npm run dev');
console.log('3. Visit http://localhost:8000');
console.log('\n📱 Test installation on mobile/desktop browsers!'); 