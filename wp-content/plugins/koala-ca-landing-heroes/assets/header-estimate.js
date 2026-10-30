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

// Reuse the shared location header, with the campaign contact details supplied for Efficiency.
(() => {
  const location = document.getElementById('loc-name');
  const phone = document.getElementById('loc-phone-number');
  const phoneLink = document.getElementById('phone1-wrapper');
  if (location) location.textContent = 'Canada';
  if (phone) phone.textContent = '(833)-903-5197';
  if (phoneLink) phoneLink.setAttribute('href', 'tel:8339035197');
  const menu = document.getElementById('brxe-cmsqdu');
  const home = document.getElementById('nav-home');
  if (!menu || !home) return;
  // The logo returns to the current campaign URL; services use the location root.
  const locationHome = new URL(home.href);
  locationHome.pathname = locationHome.pathname.split('/landing-pages/')[0].replace(/\/$/, '');
  locationHome.search = '';
  locationHome.hash = '';
  const services = [
    ['spray-foam-insulation', 'Spray Foam Insulation'],
    ['blown-in-attic-insulation', 'Blown-In Attic Insulation'],
    ['insulation-removal-services', 'Insulation Removal Services'],
    ['batt-insulation-installation', 'Batt Insulation Installation'],
    ['solar-attic-fans', 'Solar Attic Fans'],
    ['air-sealing-service', 'Air Sealing Service'],
    ['crawl-space-encapsulation', 'Crawl Space Encapsulation'],
    ['commercial-insulation-services', 'Commercial Insulation Services'],
  ];
  const items = services.map(([slug, title]) => {
    const li = document.createElement('li');
    li.className = 'menu-item';
    const link = document.createElement('a');
    link.className = 'brxe-text-link nav-nested-link';
    link.href = `${locationHome.href.replace(/\/$/, '')}/services/${slug}`;
    link.textContent = title;
    li.append(link);
    return li;
  });
  menu.replaceChildren(...items);
})();
