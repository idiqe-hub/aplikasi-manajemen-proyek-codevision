/**
 * playwright-wireframe.js
 * ============================================================
 * Script Playwright untuk otomatis capture screenshot semua
 * halaman utama aplikasi Codevision dalam Wireframe Mode
 * (Balsamiq-style) dan menyimpan ke folder Screenshots/wireframe/
 *
 * Cara menjalankan:
 *   node playwright-wireframe.js
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
const ADMIN_PASS  = 'password';
const OUTPUT_DIR  = path.join(__dirname, 'Screenshots', 'normal');

// Daftar halaman yang akan di-capture
const PAGES = [
  { name: '01_dashboard',           url: '/dashboard',             waitFor: '.card' },
  { name: '02_projects',            url: '/projects',              waitFor: 'table, .card' },
  { name: '03_projects_create',     url: '/projects/create',       waitFor: 'form' },
  { name: '04_tasks',               url: '/tasks',                 waitFor: 'table, .card' },
  { name: '05_tasks_kanban',        url: '/tasks/kanban',          waitFor: '.card' },
  { name: '06_tasks_create',        url: '/tasks/create',          waitFor: 'form' },
  { name: '07_clients',             url: '/clients',               waitFor: 'table, .card' },
  { name: '08_clients_create',      url: '/clients/create',        waitFor: 'form' },
  { name: '09_developers',          url: '/developers',            waitFor: 'table, .card' },
  { name: '09b_developers_create',  url: '/developers/create',     waitFor: 'form' },
  { name: '10_developers_capacity', url: '/developers/capacity',   waitFor: '.card' },
  { name: '10b_kpi_index',          url: '/kpi',                   waitFor: '.card' },
  { name: '10c_kpi_show',           url: '/kpi/17',                waitFor: '.card' },
  { name: '11_reports',             url: '/reports',               waitFor: '.card' },
  { name: '12_notifications',       url: '/notifications',         waitFor: '.card, li, .alert' },
  { name: '13_account_profile',     url: '/account/profile',       waitFor: 'form, .card' },
  { name: '14_account_password',    url: '/account/password',      waitFor: 'form' },
];

// ── HELPER ───────────────────────────────────────────────────
function ensureDir(dir) {
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
}

function log(msg) {
  const ts = new Date().toLocaleTimeString('id-ID');
  console.log(`[${ts}] ${msg}`);
}

// ── MAIN ─────────────────────────────────────────────────────
(async () => {
  ensureDir(OUTPUT_DIR);
  log('📐 Wireframe Screenshot Tool — Codevision');
  log(`📁 Output: ${OUTPUT_DIR}`);
  log('─'.repeat(55));

  const browser = await chromium.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
  });

  const page = await context.newPage();

  // ── 1. Login via form ─────────────────────────────────────
  log('🔐 Navigasi ke halaman login...');
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(2000);
  log(`   URL: ${page.url()}`);

  // Fill form
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', ADMIN_PASS);

  log('   Submit form login...');
  await page.click('button[type="submit"]');

  // Tunggu sampai navigasi selesai
  await page.waitForTimeout(4000);
  log(`   URL setelah login: ${page.url()}`);

  // Cek apakah masih di login
  if (page.url().includes('/login')) {
    // Coba dengan keyboard Enter
    log('   Coba dengan Enter key...');
    await page.fill('input[name="password"]', ADMIN_PASS);
    await page.keyboard.press('Enter');
    await page.waitForTimeout(4000);
    log(`   URL setelah Enter: ${page.url()}`);
  }

  const currentUrl = page.url();
  const isLoggedIn = !currentUrl.includes('/login');

  if (!isLoggedIn) {
    log('⚠️  Login mungkin gagal, tapi melanjutkan...');
  } else {
    log(`✅ Login berhasil! URL: ${currentUrl}`);
  }

  // Navigasi ke dashboard jika diperlukan
  if (currentUrl.endsWith('/') || currentUrl === BASE_URL || currentUrl === BASE_URL + '/') {
    await page.goto(`${BASE_URL}/dashboard`, { waitUntil: 'networkidle' });
    log(`   Redirect ke dashboard: ${page.url()}`);
  }

  // ── 2. Aktifkan Wireframe Mode ─────────────────────────────
  log('🎨 Mengaktifkan Wireframe Mode...');
  // normal mode
  await page.waitForTimeout(600);

  // Verifikasi wireframe aktif
  const wireframeActive = await page.evaluate(() => document.body.classList.contains('wireframe-mode'));
  log(`   Wireframe aktif: ${wireframeActive}`);

  // ── 3. Screenshot setiap halaman ──────────────────────────
  const results = [];

  for (const pageInfo of PAGES) {
    const fullUrl = `${BASE_URL}${pageInfo.url}`;
    log(`📸 Capturing: ${pageInfo.name} → ${fullUrl}`);

    try {
      await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 20000 });
      const landedUrl = page.url();

      if (landedUrl.includes('/login')) {
        log(`   ⚠️  Redirect ke login!`);
        results.push({ name: pageInfo.name, status: 'REDIRECT', error: 'Redirected to login' });
        continue;
      }

      // Tunggu elemen utama
      try {
        await page.waitForSelector(pageInfo.waitFor, { timeout: 6000 });
      } catch (_) {}

      // Pastikan wireframe mode aktif
      const hasWireframe = await page.evaluate(() => document.body.classList.contains('wireframe-mode'));
      if (!hasWireframe) {
        // normal mode
        await page.waitForTimeout(400);
      }

      // Scroll ke atas & render
      await page.evaluate(() => window.scrollTo(0, 0));
      await page.waitForTimeout(700);

      const screenshotPath = path.join(OUTPUT_DIR, `${pageInfo.name}.png`);
      await page.screenshot({ path: screenshotPath, fullPage: true });

      const stats = fs.statSync(screenshotPath);
      const sizeKB = (stats.size / 1024).toFixed(1);
      log(`   ✅ ${pageInfo.name}.png (${sizeKB} KB)`);
      results.push({ name: pageInfo.name, status: 'OK', sizeKB });

    } catch (err) {
      log(`   ❌ Gagal: ${err.message}`);
      results.push({ name: pageInfo.name, status: 'ERROR', error: err.message });
    }
  }

  // ── 4. Capture Developer Dashboard ──────────────────────────
  log('👨‍💻 Navigasi ke halaman developer...');
  try {
    const devContext = await browser.newContext({
      viewport: { width: 1440, height: 900 },
      locale: 'id-ID',
    });
    const devPage = await devContext.newPage();
    await devPage.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle' });
    await devPage.fill('input[name="email"]', 'rizhan@codevision.test');
    await devPage.fill('input[name="password"]', 'password');
    await devPage.click('button[type="submit"]');
    await devPage.waitForTimeout(4000);

    if (devPage.url().includes('/login')) {
      await devPage.fill('input[name="password"]', 'password');
      await devPage.keyboard.press('Enter');
      await devPage.waitForTimeout(4000);
    }

    await devPage.goto(`${BASE_URL}/developer/dashboard`, { waitUntil: 'networkidle', timeout: 20000 });
    
    // normal mode
    await devPage.waitForTimeout(600);
    const hasWireframe = await devPage.evaluate(() => document.body.classList.contains('wireframe-mode'));
    if (!hasWireframe) {
      await devPage.waitForTimeout(400);
    }

    await devPage.evaluate(() => window.scrollTo(0, 0));
    await devPage.waitForTimeout(700);

    const devPath = path.join(OUTPUT_DIR, '01b_dashboard_developer.png');
    await devPage.screenshot({ path: devPath, fullPage: true });
    const statsDev = fs.statSync(devPath);
    log(`   ✅ 01b_dashboard_developer.png (${(statsDev.size / 1024).toFixed(1)} KB)`);
    results.push({ name: '01b_dashboard_developer', status: 'OK', sizeKB: (statsDev.size / 1024).toFixed(1) });
    await devContext.close();
  } catch (err) {
    log(`   ❌ Gagal capture developer dashboard: ${err.message}`);
    results.push({ name: '01b_dashboard_developer', status: 'ERROR', error: err.message });
  }

  await browser.close();

  // ── 5. Laporan ────────────────────────────────────────────
  log('─'.repeat(55));
  log('📊 LAPORAN HASIL CAPTURE:');
  log('─'.repeat(55));

  let ok = 0, err = 0;
  results.sort((a, b) => a.name.localeCompare(b.name));
  for (const r of results) {
    if (r.status === 'OK') { log(`  ✅ ${r.name}.png (${r.sizeKB || '?'} KB)`); ok++; }
    else { log(`  ❌ ${r.name} — ${r.status}: ${r.error}`); err++; }
  }

  log('─'.repeat(55));
  log(`🎉 Selesai! ${ok} berhasil, ${err} gagal`);
  log(`📁 Hasil disimpan di: ${OUTPUT_DIR}`);
})();
