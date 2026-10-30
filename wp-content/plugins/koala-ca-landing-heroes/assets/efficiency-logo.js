// Session storage keeps separate ad journeys independent in separate browser tabs.
(() => {
  const config = window.koalaEfficiencyLogo;
  if (!config) return;
  const key = `koala-efficiency-entry:${config.route}`;
  let destination = config.entry;
  try {
    if (destination) sessionStorage.setItem(key, destination);
    else destination = sessionStorage.getItem(key);
  } catch (_) { /* The current landing-page logo still works without storage. */ }
  if (!destination) return;
  let url;
  try { url = new URL(destination); } catch (_) { return; }
  if (url.origin !== window.location.origin
      || url.pathname.replace(/\/$/, '') !== config.route.replace(/\/$/, '')) return;
  const update = () => {
    document.querySelectorAll('a#nav-home').forEach((logo) => {
      if (logo.href !== url.href) logo.href = url.href;
    });
  };
  update();
  // Shared location scripts can replace the logo destination after page load.
  new MutationObserver(update).observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['href'] });
})();
