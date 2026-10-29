import { spawn, exec } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Membuat pengganti __dirname yang tidak ada di ES Module
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Port bawaan dari "php artisan serve" adalah 8000
const PORT = 8000; 
const envPath = path.join(__dirname, '.env');

console.log(`⏳ Memulai Cloudflare Tunnel untuk Laravel di port ${PORT}...`);

const cloudflared = spawn('cloudflared', ['tunnel', '--config', 'cloudflared-empty.yml', '--url', `http://localhost:${PORT}`]);

let urlFound = false;

cloudflared.stderr.on('data', (data) => {
  const output = data.toString();
  process.stdout.write(output); // Menampilkan log di terminal

  if (!urlFound) {
    // Mencari URL trycloudflare yang baru
    const match = output.match(/https:\/\/[a-zA-Z0-9-]+\.trycloudflare\.com/);
    if (match) {
      const tunnelUrl = match[0];
      console.log(`\n🎉 URL Cloudflare Baru Ditemukan: ${tunnelUrl}`);
      
      // 1. Update file .env
      updateEnv(tunnelUrl);
      
      // 2. Buka otomatis di Browser (Perintah khusus Windows)
      console.log(`🌍 Membuka website di browser...`);
      exec(`start ${tunnelUrl}`);
      
      urlFound = true;
    }
  }
});

function updateEnv(newUrl) {
  if (!fs.existsSync(envPath)) {
    return console.error('\n❌ File .env tidak ditemukan!');
  }

  let envFile = fs.readFileSync(envPath, 'utf8');

  // Mengubah baris APP_URL dan OPERATOR_URL secara otomatis
  envFile = envFile.replace(/^APP_URL=.*$/m, `APP_URL=${newUrl}`);
  envFile = envFile.replace(/^OPERATOR_URL=.*$/m, `OPERATOR_URL=${newUrl}`);

  fs.writeFileSync(envPath, envFile);
  console.log('✅ File .env berhasil diubah otomatis!\n');
}

cloudflared.on('close', (code) => {
  console.log(`🛑 Tunnel ditutup.`);
});