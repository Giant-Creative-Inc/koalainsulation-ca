// Enqueued only for the exact Efficiency campaign route and relationship.
(() => {
  const button = document.getElementById('get-estimate-btn');
  if (!button) return;
  button.setAttribute('href', '#estimate-form');
  button.setAttribute('aria-haspopup', 'dialog');
  button.addEventListener('click', (event) => {
    if (typeof window.bricksOpenPopup !== 'function') return;
    event.preventDefault();
    event.stopImmediatePropagation();
    window.bricksOpenPopup(4865);
  }, true);
})();

// Measure the actual header for the exact Efficiency campaign hero.
(() => {
  const hero = document.querySelector('.ca-landing-hero[data-hero]');
  if (!hero) return;
  const update = () => {
    const headerHeight = Math.max(0, hero.getBoundingClientRect().top + window.scrollY);
    hero.style.setProperty('--ca-hero-header-height', `${headerHeight}px`);
  };
  update();
  window.addEventListener('resize', update, { passive: true });
  window.addEventListener('load', update, { once: true });
  const header = document.querySelector('header');
  if (header && typeof ResizeObserver !== 'undefined') new ResizeObserver(update).observe(header);
})();
