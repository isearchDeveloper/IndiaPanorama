const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const sourceDir = `C:\\Users\\abc\\.gemini\\antigravity\\brain\\2f38f262-c16d-49a2-80d4-642b113e563e`;
const targetDir = `d:\\indian-panaroma\\indian-panorama\\public\\images`;

const files = fs.readdirSync(sourceDir).filter(f => f.endsWith('.png'));

for (const file of files) {
  const match = file.match(/^([a-z_]+)_\d+\.png$/);
  if (match) {
    const baseName = match[1].replace(/_/g, '-');
    const sourcePath = path.join(sourceDir, file);
    const tempPng = path.join(targetDir, `${baseName}.png`);
    
    fs.copyFileSync(sourcePath, tempPng);
    console.log(`Processing ${baseName}...`);
    try {
      execSync(`npx -y @squoosh/cli --resize "{\\"width\\":400}" --webp auto -d "${targetDir}" "${tempPng}"`, { stdio: 'inherit' });
    } catch (e) {
      console.error(`Error processing ${baseName}:`, e.message);
    }
    if (fs.existsSync(tempPng)) {
      fs.unlinkSync(tempPng);
    }
  }
}
