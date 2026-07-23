const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const sourceDir = `C:\\Users\\abc\\.gemini\\antigravity\\brain\\b3263778-b985-4a24-8dd1-059190bb85f0`;
const targetDir = `d:\\indian-panaroma\\indian-panorama\\public\\images\\festivals`;
const fallbackImagesDir = `d:\\indian-panaroma\\indian-panorama\\public\\images`;

if (!fs.existsSync(targetDir)) {
  fs.mkdirSync(targetDir, { recursive: true });
}

// All required festival images
const allFestivals = [
  'diwali', 'holi', 'dussehra', 'eid', 'christmas', 'navratri', 
  'pushkar', 'surajkund', 'rann-utsav', 'hornbill', 'kumbh', 
  'pongal', 'baisakhi', 'onam', 'lohri', 'bihu', 
  'khajuraho', 'konark', 'jlf', 'ziro'
];

const fallbackMap = {
  'khajuraho': 'img-1.jpg',
  'konark': 'img-2.jpg',
  'jlf': 'img-3.jpg',
  'ziro': 'img-4.jpg'
};

const generatedFiles = fs.readdirSync(sourceDir).filter(f => f.endsWith('.png'));

(async () => {
  let processed = 0;
  for (const fest of allFestivals) {
    // Find if we generated it
    // Note: rann-utsav was generated as rann_utsav
    const searchName = fest.replace('-', '_');
    const match = generatedFiles.find(f => f.startsWith(searchName + '_'));
    
    let sourcePath = null;
    if (match) {
      sourcePath = path.join(sourceDir, match);
    } else if (fallbackMap[fest]) {
      sourcePath = path.join(fallbackImagesDir, fallbackMap[fest]);
    }
    
    if (sourcePath && fs.existsSync(sourcePath)) {
      const targetWebp = path.join(targetDir, `${fest}.webp`);
      console.log(`Processing ${fest} from ${sourcePath}...`);
      try {
        await sharp(sourcePath)
          .resize(1024, 683, { fit: 'cover' }) // ensure good dimension
          .webp({ quality: 65, effort: 6 }) // quality 65 usually yields 50-80kb for 1024px
          .toFile(targetWebp);
        
        const stat = fs.statSync(targetWebp);
        console.log(`Saved ${targetWebp} - ${(stat.size / 1024).toFixed(2)} KB`);
        processed++;
      } catch (e) {
        console.error(`Error processing ${fest}:`, e.message);
      }
    } else {
      console.error(`Missing source for ${fest}`);
    }
  }
  console.log(`Finished processing ${processed}/${allFestivals.length} images.`);
})();
