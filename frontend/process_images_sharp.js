const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const sourceDir = `C:\\Users\\abc\\.gemini\\antigravity\\brain\\2f38f262-c16d-49a2-80d4-642b113e563e`;
const targetDir = `d:\\indian-panaroma\\indian-panorama\\public\\images`;

const files = fs.readdirSync(sourceDir).filter(f => f.endsWith('.png'));

(async () => {
  for (const file of files) {
    const match = file.match(/^([a-z_]+)_\d+\.png$/);
    if (match) {
      const baseName = match[1].replace(/_/g, '-');
      const sourcePath = path.join(sourceDir, file);
      const targetWebp = path.join(targetDir, `${baseName}.webp`);
      
      console.log(`Processing ${baseName}...`);
      try {
        await sharp(sourcePath)
          .resize(800)
          .webp({ quality: 75 })
          .toFile(targetWebp);
        console.log(`Saved ${targetWebp}`);
      } catch (e) {
        console.error(`Error processing ${baseName}:`, e.message);
      }
    }
  }
})();
