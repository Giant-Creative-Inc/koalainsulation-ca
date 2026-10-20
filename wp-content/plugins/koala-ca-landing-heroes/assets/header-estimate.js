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
