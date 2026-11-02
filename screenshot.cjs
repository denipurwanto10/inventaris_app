const { chromium } = require('playwright');
const path = require('node:path');

const BASE = process.env.APP_URL || 'http://127.0.0.1:8000';
const OUT = path.join(__dirname, 'screenshots');
const VIEWPORT = { width: 1440, height: 900 };

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: VIEWPORT, deviceScaleFactor: 1, locale: 'id-ID' });
  const page = await context.newPage();

  const shoot = async (file) => {
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1600); // chart animation + fonts
    await page.screenshot({ path: path.join(OUT, file), fullPage: false });
    console.log(`${file} -> ${page.url()}`);
  };

  console.log('Login page...');
  await page.goto(`${BASE}/login`, { waitUntil: 'networkidle' });
  await shoot('login-1440x900.png');

  console.log('Logging in...');
  await page.getByPlaceholder('nama@kantor.com').fill('admin@gudang.in');
  await page.getByPlaceholder('Masukkan kata sandi').fill('password');
  await Promise.all([
    page.waitForURL('**/dashboard', { timeout: 15000 }).catch(() => {}),
    page.getByRole('button', { name: /^Masuk$/ }).click(),
  ]);

  console.log('Dashboard...');
  await page.goto(`${BASE}/dashboard`, { waitUntil: 'networkidle' });
  await shoot('dashboard-1440x900.png');

  console.log('Barang...');
  await page.goto(`${BASE}/items`, { waitUntil: 'networkidle' });
  await shoot('barang-1440x900.png');

  await browser.close();
  console.log('Done.');
})().catch((e) => { console.error(e); process.exit(1); });
