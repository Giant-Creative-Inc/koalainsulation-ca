/**
 * Playwright CLI regression check. Open a Canadian legacy service page from
 * this repository root, then run-code --filename tests/mobile-service-submenu.browser.js.
 * The local stylesheet is injected into this isolated browser only.
 */
async (page) => {
  await page.reload({ waitUntil: 'domcontentloaded' });
  await page.setViewportSize({ width: 390, height: 844 });
  await page.locator('h1').click();
  await page.waitForFunction(() => document.querySelector('#nav-home')?.href.endsWith('/ca/durham'));
  await page.addStyleTag({ path: 'wp-content/themes/bricks/assets/css/custom-service.css' });
  await page.locator('#brxe-rbrwsc').click();
  await page.waitForTimeout(500);
  const mobile = await page.evaluate(() => {
    const submenu = document.getElementById('custom-service-ul');
    const quote = document.getElementById('national-nav-quote');
    const s = submenu.getBoundingClientRect();
    const q = quote.getBoundingClientRect();
    const x = q.right - 12;
    const y = q.top + q.height / 2;
    return {
      submenuRight: s.right,
      quoteRight: q.right,
      submenuCoversQuote: !!document.elementFromPoint(x, y)?.closest('#custom-service-ul'),
      overflow: document.documentElement.scrollWidth > innerWidth,
    };
  });
  if (!mobile.submenuCoversQuote || mobile.submenuRight < mobile.quoteRight - 1 || mobile.overflow) {
    throw new Error('Mobile Services submenu exposes the estimate button: ' + JSON.stringify(mobile));
  }
  await page.locator('#brxe-fyjdox').click();
  await page.setViewportSize({ width: 1440, height: 1000 });
  const desktopQuoteVisible = await page.locator('#national-nav-quote').isVisible();
  if (!desktopQuoteVisible) throw new Error('Desktop estimate button must remain visible.');
  return { mobile, desktopQuoteVisible, passed: true };
}
