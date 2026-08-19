/**
 * reviews.js
 *
 * Review-count integration extracted from all-pages.js (CA theme).
 * Polls the Google/NiceJob review widget for its `.nj-trust__total` value and
 * mirrors it into the `#review-count` target. Self-terminates immediately when
 * `#review-count` is absent, so it is enqueued only where that target renders
 * (single locations and landing pages — matching the US theme; verify on CA
 * staging and widen the condition if a page shows a missing count).
 *
 * Pure vanilla JS (no jQuery dependency). Extracted 2026-08-19; logic is
 * byte-identical to the original block in all-pages.js.
 */

document.addEventListener("DOMContentLoaded", function () {
  const interval = setInterval(function () {
    const njReviewCountEl = document.querySelector('.nj-trust__total');
    const targetDiv = document.getElementById('review-count');

    if (njReviewCountEl && targetDiv) {
      targetDiv.textContent = njReviewCountEl.textContent;
      clearInterval(interval);
    }
  }, 200);
});
