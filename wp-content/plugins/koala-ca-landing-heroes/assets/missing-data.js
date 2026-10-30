// Only enqueued for the Canadian Efficiency content routes on Local/staging.
(() => {
  const hideMissingData = () => {
    document.querySelectorAll('a[href^="tel:"]').forEach((link) => {
      const empty = !/\d/.test(link.getAttribute('href').slice(4));
      if (empty) {
        link.style.setProperty('display', 'none', 'important');
        link.setAttribute('data-efficiency-empty-phone', '');
      } else if (link.hasAttribute('data-efficiency-empty-phone')) {
        link.style.removeProperty('display');
        link.removeAttribute('data-efficiency-empty-phone');
      }
    });
    // This homepage contact section has no map, address or phone to display.
    const contact = document.querySelector('#brxe-shnlwl');
    if (contact && contact.textContent.includes('Map data is missing.')) {
      contact.style.setProperty('display', 'none', 'important');
      contact.setAttribute('hidden', '');
    }
  };
  hideMissingData();
  window.addEventListener('load', hideMissingData, { once: true });
  new MutationObserver(hideMissingData).observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['href'] });
})();
