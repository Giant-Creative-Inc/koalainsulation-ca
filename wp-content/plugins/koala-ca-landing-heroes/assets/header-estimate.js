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

// Match the Canadian popup's keyboard and reading order to its visible layout.
(() => {
  const syncPopupOrder = () => {
    const form = document.querySelector('#brxe-fexrky #gform_3');
    if (!form) return;
    const email = form.querySelector('#field_3_4');
    const postal = form.querySelector('#field_3_6');
    if (email && postal && (email.compareDocumentPosition(postal) & Node.DOCUMENT_POSITION_FOLLOWING)) {
      postal.after(email);
    }
    const ids = ['input_3_1', 'input_3_3', 'input_3_5', 'input_3_6', 'input_3_4', 'input_3_9_1', 'input_3_10_1', 'gform_submit_button_3'];
    ids.forEach((id, index) => {
      const field = form.querySelector(`#${id}`);
      if (field) field.setAttribute('tabindex', String(200 + index));
    });
  };
  syncPopupOrder();
  new MutationObserver(syncPopupOrder).observe(document.body, { childList: true, subtree: true });
  document.addEventListener('gform/postRender', syncPopupOrder);
})();
