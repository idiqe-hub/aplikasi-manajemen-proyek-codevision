/**
 * playwright-pdf.js
 * ============================================================
 * Script Playwright untuk otomatis capture screenshot semua
 * 9 laporan PDF dalam Wireframe Mode
 * ============================================================
 */

import { chromium } from 'playwright';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname  = path.dirname(__filename);

// ── KONFIGURASI ──────────────────────────────────────────────
const BASE_URL    = 'http://localhost/aplikasi-manajemen-proyek-codevision/public';
const ADMIN_EMAIL = 'admin@codevision.test';
const ADMIN_PASS  = 'password123';
const OUTPUT_DIR  = path.join(__dirname, 'Screenshots', 'normal');

// Daftar 9 halaman laporan PDF
const PAGES = [
  { name: '15_pdf_proyek_aktif',            url: '/reports/projects-active/pdf?html_preview=1' },
  { name: '16_pdf_tugas_per_developer',     url: '/reports/tasks-by-developer/pdf?html_preview=1' },
  { name: '17_pdf_tugas_terlambat',         url: '/reports/tasks-overdue/pdf?html_preview=1' },
  { name: '18_pdf_progres_proyek',          url: '/reports/project-progress/pdf?html_preview=1' },
  { name: '19_pdf_estimasi_vs_realisasi_jam', url: '/reports/hours-summary/pdf?html_preview=1' },
  { name: '20_pdf_beban_kerja',             url: '/reports/workload/pdf?html_preview=1' },
  { name: '21_pdf_efisiensi_waktu',         url: '/reports/time-efficiency/pdf?html_preview=1' },
  { name: '22_pdf_distribusi_tugas',        url: '/reports/task-distribution/pdf?html_preview=1' },
  { name: '23_pdf_produktivitas_mingguan',  url: '/reports/weekly-productivity/pdf?html_preview=1' },
];

function ensureDir(dir) {
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
}

function log(msg) {
  const ts = new Date().toLocaleTimeString('id-ID');
  console.log(`[${ts}] ${msg}`);
}

(async () => {
  ensureDir(OUTPUT_DIR);
  log('📐 Wireframe PDF Screenshot Tool — Codevision');
  log(`📁 Output: ${OUTPUT_DIR}`);
  log('─'.repeat(55));

  const browser = await chromium.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  const context = await browser.newContext({
    viewport: { width: 1000, height: 1414 }, // A4 aspect ratio
    locale: 'id-ID',
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
  });

  const page = await context.newPage();

  // 1. Login via form
  log('🔐 Navigasi ke halaman login...');
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(2000);
  
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', ADMIN_PASS);
  await page.click('button[type="submit"]');
  await page.waitForTimeout(4000);

  if (page.url().includes('/login')) {
    await page.fill('input[name="password"]', ADMIN_PASS);
    await page.keyboard.press('Enter');
    await page.waitForTimeout(4000);
  }

  log(`✅ Login selesai, memulai capture PDF wireframes...`);

  // 2. Screenshot setiap halaman PDF
  const results = [];

  for (const pageInfo of PAGES) {
    const fullUrl = `${BASE_URL}${pageInfo.url}`;
    log(`📸 Capturing: ${pageInfo.name} → ${fullUrl}`);

    try {
      await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 20000 });
      await page.waitForTimeout(1000);

      const screenshotPath = path.join(OUTPUT_DIR, `${pageInfo.name}.png`);
      await page.screenshot({ path: screenshotPath, fullPage: false });

      const stats = fs.statSync(screenshotPath);
      const sizeKB = (stats.size / 1024).toFixed(1);
      log(`   ✅ ${pageInfo.name}.png (${sizeKB} KB)`);
      results.push({ name: pageInfo.name, status: 'OK', sizeKB });

    } catch (err) {
      log(`   ❌ Gagal: ${err.message}`);
      results.push({ name: pageInfo.name, status: 'ERROR', error: err.message });
    }
  }

  await browser.close();

  log('─'.repeat(55));
  log('📊 LAPORAN HASIL CAPTURE PDF:');
  for (const r of results) {
    if (r.status === 'OK') log(`  ✅ ${r.name}.png (${r.sizeKB || '?'} KB)`);
    else log(`  ❌ ${r.name} — ${r.status}: ${r.error}`);
  }
  log('─'.repeat(55));
})();
