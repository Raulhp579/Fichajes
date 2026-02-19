const fs = require('fs');
const path = require('path');

// Source and Destination paths
// Source: ../public/browser (Angular output)
// Destination: ../public (Laravel public root)
const sourceDir = path.join(__dirname, '..', 'public', 'browser');
const destDir = path.join(__dirname, '..', 'public');

console.log(`Copying files from ${sourceDir} to ${destDir}...`);

// Function to copy directory content recursively
function copyDir(src, dest) {
  if (!fs.existsSync(dest)) {
    fs.mkdirSync(dest, { recursive: true });
  }

  // Clean old JS and CSS files in destination to prevent caching issues
  if (fs.existsSync(dest)) {
    const destFiles = fs.readdirSync(dest);
    for (const file of destFiles) {
      if (file.match(/main-.*\.js/) || file.match(/styles-.*\.css/) || file === 'index.html') {
        const filePath = path.join(dest, file);
        fs.unlinkSync(filePath);
        console.log(`Deleted old file: ${file}`);
      }
    }
  }

  const entries = fs.readdirSync(src, { withFileTypes: true });

  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);

    if (entry.isDirectory()) {
      copyDir(srcPath, destPath);
    } else {
      fs.copyFileSync(srcPath, destPath);
      // console.log(`Copied: ${entry.name}`);
    }
  }
}

try {
  if (fs.existsSync(sourceDir)) {
    copyDir(sourceDir, destDir);
    console.log('Files copied successfully.');
  } else {
    console.error(`Source directory not found: ${sourceDir}`);
    process.exit(1);
  }
} catch (err) {
  console.error('Error copying files:', err);
  process.exit(1);
}
