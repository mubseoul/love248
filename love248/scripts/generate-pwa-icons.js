#!/usr/bin/env node

/**
 * PWA Icon Generator for Love248
 * 
 * This script generates all required PWA icons from the existing favicon.
 * Run with: node scripts/generate-pwa-icons.js
 */

const fs = require('fs');
const path = require('path');

// Required icon sizes for PWA
const ICON_SIZES = [
    { size: 72, name: 'icon-72x72.png' },
    { size: 96, name: 'icon-96x96.png' },
    { size: 128, name: 'icon-128x128.png' },
    { size: 144, name: 'icon-144x144.png' },
    { size: 152, name: 'icon-152x152.png' },
    { size: 192, name: 'icon-192x192.png' },
    { size: 384, name: 'icon-384x384.png' },
    { size: 512, name: 'icon-512x512.png' }
];

// Shortcut icons
const SHORTCUT_ICONS = [
    { name: 'channels-shortcut.png', size: 96 },
    { name: 'dashboard-shortcut.png', size: 96 },
    { name: 'tokens-shortcut.png', size: 96 }
];

const PUBLIC_DIR = path.join(__dirname, '../public');
const ICONS_DIR = path.join(PUBLIC_DIR, 'images/icons');
const FAVICON_PATH = path.join(PUBLIC_DIR, 'favicon.png');

// Create icons directory if it doesn't exist
if (!fs.existsSync(ICONS_DIR)) {
    fs.mkdirSync(ICONS_DIR, { recursive: true });
    console.log('✅ Created icons directory');
}

// Check if we have sharp installed (for image processing)
let sharp;
try {
    sharp = require('sharp');
} catch (error) {
    console.log('⚠️  Sharp not found. Installing...');
    console.log('Run: npm install sharp --save-dev');
    console.log('Then rerun this script.');
    process.exit(1);
}

async function generateIcons() {
    try {
        // Check if favicon exists
        if (!fs.existsSync(FAVICON_PATH)) {
            console.log('❌ Favicon not found at:', FAVICON_PATH);
            console.log('Please ensure favicon.png exists in the public directory');
            return;
        }

        console.log('🚀 Generating PWA icons from favicon...');

        // Generate main app icons
        for (const icon of ICON_SIZES) {
            const outputPath = path.join(ICONS_DIR, icon.name);
            
            await sharp(FAVICON_PATH)
                .resize(icon.size, icon.size, {
                    fit: 'contain',
                    background: { r: 124, g: 58, b: 237, alpha: 1 } // Purple background
                })
                .png()
                .toFile(outputPath);
                
            console.log(`✅ Generated ${icon.name} (${icon.size}x${icon.size})`);
        }

        // Generate shortcut icons (these can be customized later)
        for (const shortcut of SHORTCUT_ICONS) {
            const outputPath = path.join(ICONS_DIR, shortcut.name);
            
            await sharp(FAVICON_PATH)
                .resize(shortcut.size, shortcut.size, {
                    fit: 'contain',
                    background: { r: 124, g: 58, b: 237, alpha: 1 }
                })
                .png()
                .toFile(outputPath);
                
            console.log(`✅ Generated ${shortcut.name} (shortcut icon)`);
        }

        console.log('🎉 All PWA icons generated successfully!');
        console.log('📍 Icons location:', ICONS_DIR);
        
        // Generate a simple HTML test page
        generateTestPage();
        
    } catch (error) {
        console.error('❌ Error generating icons:', error);
    }
}

function generateTestPage() {
    const testPageContent = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWA Icons Test - Love248</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #000; color: white; }
        .icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; }
        .icon-item { text-align: center; padding: 10px; background: #1a1a2e; border-radius: 8px; }
        .icon-item img { max-width: 100%; height: auto; border-radius: 4px; }
        .icon-item h3 { margin: 10px 0 5px 0; font-size: 14px; }
        .icon-item p { margin: 0; font-size: 12px; opacity: 0.8; }
    </style>
</head>
<body>
    <h1>Love248 PWA Icons Test</h1>
    <p>All generated PWA icons for the Love248 streaming platform:</p>
    
    <div class="icon-grid">
        ${ICON_SIZES.map(icon => `
        <div class="icon-item">
            <img src="/images/icons/${icon.name}" alt="${icon.name}">
            <h3>${icon.name}</h3>
            <p>${icon.size}x${icon.size}px</p>
        </div>
        `).join('')}
        
        <h2 style="grid-column: 1 / -1; margin: 20px 0 10px 0;">Shortcut Icons</h2>
        
        ${SHORTCUT_ICONS.map(icon => `
        <div class="icon-item">
            <img src="/images/icons/${icon.name}" alt="${icon.name}">
            <h3>${icon.name}</h3>
            <p>${icon.size}x${icon.size}px</p>
        </div>
        `).join('')}
    </div>
    
    <div style="margin-top: 40px; padding: 20px; background: #1a1a2e; border-radius: 8px;">
        <h2>Next Steps:</h2>
        <ol>
            <li>Customize shortcut icons with appropriate graphics</li>
            <li>Test PWA installation on mobile devices</li>
            <li>Verify all icons display correctly</li>
            <li>Update manifest.json if needed</li>
        </ol>
    </div>
</body>
</html>`;

    const testPagePath = path.join(PUBLIC_DIR, 'pwa-icons-test.html');
    fs.writeFileSync(testPagePath, testPageContent);
    console.log('📄 Test page generated: /pwa-icons-test.html');
}

// Run the generator
generateIcons().catch(console.error); 