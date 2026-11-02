const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await context.newPage();

  // Force light theme
  await page.evaluate(() => {
    document.documentElement.classList.remove('dark');
    try { localStorage.setItem('theme', 'light'); } catch (e) {}
  });

  const baseUrl = 'http://localhost:8000';

  // 1. Login page - clean capture
  console.log('Capturing login page...');
  await page.goto(baseUrl + '/login', { waitUntil: 'networkidle' });
  await page.waitForTimeout(500);
  await page.screenshot({ path: 'screenshots/login2.png', viewport: { width: 1440, height: 900 }, fullPage: false });
  console.log('  → login2.png captured');

  // 2. Login as admin
  console.log('Attempting login...');
  const emailInput = page.getByPlaceholder('nama@kantor.com');
  const passwordInput = page.getByPlaceholder('Masukkan kata sandi');
  const submitBtn = page.getByRole('button', { name: /Masuk|Login|Sign in/i }) || page.getByRole('submit');

  await emailInput.fill('admin@gudang.in');
  await passwordInput.fill('password');
  await submitBtn.click();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(1500); // wait for dashboard to load

  // 3. Dashboard - wait extensively for charts to render
  console.log('Capturing dashboard...');
  await page.goto(baseUrl + '/dashboard', { waitUntil: 'networkidle' });
  
  // Wait for Chart.js to render and counters to animate
  await page.waitForTimeout(2000);
  
  // Trigger any lazy rendering / animation
  await page.evaluate(() => {
    // Ensure all counter elements are visible
    document.querySelectorAll('[data-count]').forEach(el => {
      el.style.opacity = '1';
      el.style.visibility = 'visible';
    });
    // Force chart re-render if needed
    const canvas = document.getElementById('flowChart');
    if (canvas && window.chart) {
      window.chart.update();
    }
  });
  
  // Additional wait for any animations to complete
  await page.waitForTimeout(1000);
  
  await page.screenshot({ path: 'screenshots/dashboard2.png', viewport: { width: 1440, height: 900 }, fullPage: false });
  console.log('  → dashboard2.png captured (with stock flow chart)');

  // 4. Barang page
  console.log('Capturing barang page...');
  await page.goto(baseUrl + '/items', { waitUntil: 'networkidle' });
  await page.waitForTimeout(500);
  await page.screenshot({ path: 'screenshots/barang2.png', viewport: { width: 1440, height: 900 }, fullPage: false });
  console.log('  → barang2.png captured');

  await browser.close();
  console.log('\nScreenshots captured in "screenshots/" folder:\n- login2.png\n- dashboard2.png (with chart data)\n- barang2.png');
})();