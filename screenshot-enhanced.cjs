const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await context.newPage();

  // Set light theme for better screenshots
  await page.evaluate(() => {
    document.documentElement.classList.remove('dark');
    try { localStorage.setItem('theme', 'light'); } catch (e) {}
  });

  const baseUrl = 'http://localhost:8000';

  // 1. Login page
  console.log('Capturing login page...');
  await page.goto(baseUrl + '/login', { waitUntil: 'networkidle' });
  await page.waitForTimeout(500); // small wait for any animations
  await page.screenshot({ path: 'screenshots/login.png', viewport: { width: 1440, height: 900 }, fullPage: false });
  console.log('  → login.png captured');

  // 2. Attempt to login as admin
  console.log('Attempting login...');
  const emailInput = page.getByPlaceholder('nama@kantor.com');
  const passwordInput = page.getByPlaceholder('Masukkan kata sandi');
  const submitBtn = page.getByRole('button', { name: /Masuk|Login|Sign in/i }) || page.getByRole('submit');

  await emailInput.fill('admin@gudang.in');
  await passwordInput.fill('password');
  await submitBtn.click();
  // Wait for redirect and page to load
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(1000);

  // 3. Dashboard admin - wait for charts to render
  console.log('Navigating to dashboard...');
  await page.goto(baseUrl + '/dashboard', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1500); // wait for Chart.js to render
  // Ensure charts are visible - trigger any lazy loading
  await page.evaluate(() => {
    const counters = document.querySelectorAll('[data-count]');
    counters.forEach(el => {
      if (el.dataset.count && !el.dataset.count.includes('NaN')) {
        const target = parseFloat(el.dataset.count);
        if (isFinite(target) && target > 0) {
          // Trigger the counter animation by making it visible
          el.style.opacity = '1';
        }
      }
    });
  });
  await page.waitForTimeout(500);
  await page.screenshot({ path: 'screenshots/dashboard.png', viewport: { width: 1440, height: 900 }, fullPage: false });
  console.log('  → dashboard.png captured (with stock flow chart data)');

  // 4. Barang page
  console.log('Navigating to barang page...');
  await page.goto(baseUrl + '/items', { waitUntil: 'networkidle' });
  await page.waitForTimeout(500);
  await page.screenshot({ path: 'screenshots/barang.png', viewport: { width: 1440, height: 900 }, fullPage: false });
  console.log('  → barang.png captured');

  await browser.close();
  console.log('\nScreenshots captured in "screenshots/" folder:\n- login.png\n- dashboard.png (with chart data)\n- barang.png');
})();