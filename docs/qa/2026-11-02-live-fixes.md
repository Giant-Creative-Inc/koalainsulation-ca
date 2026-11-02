# Canadian live QA fixes — 2 November 2026

The seven native Canadian location pages return HTTP 200, and the audit found no 404 among 100 unique current internal page links. Two shared asset requests and the national postal finder need these runtime fixes.

- Stop the legacy `add_preload_lcp_image` callback on native location pages. It requests the missing `uploads/2025/02/2-600x400.webp`; native pages provide their own imagery.
- Exclude the already-built deferred CSS bundle from WP Rocket minification and its shared quote/financing background from background lazy loading, on native location requests only. The live optimizer currently requests `/ca/ca/wp-content/themes/bricks/beanstalk/assets/location-quote-banner/background.webp` (404); the correct `/ca/wp-content/...` asset returns 200. Existing optimizer exclusions are retained; legacy pages are unaffected.
- Use the null-safe phone helper for both exact postal-match triggers. The finder currently lacks optional `#tel-href`, causing a caught TypeError before the visible phone updates. Form 3 postal, slug, and location ID assignments are preserved.

## Validation

Regression tests failed before runtime changes for both missing-element triggers, the missing preload removal, and missing optimizer exclusions. After fixes:

- `composer test`: 87 tests, 345 assertions.
- `composer lint`: passes.
- `node --test tests/*.test.js` using Node 22: 6 passes.
- Canadian Local Durham desktop/mobile: header, content, footer and location heading present; background resolves to the original theme asset; no legacy image preloads or legacy page JS; no mobile overflow. Runtime files synchronized to both Canadian Local source and active theme mirror.
- Browser-only override of the national live finder with the proposed JS: Durham phone becomes a working tel link, with Form 3 postal/slug/ID intact. No server code was changed by this check.
- Read-only exact postal lookup samples for all seven locations returned their expected IDs. No lead submissions, emails or CRM delivery tests performed.

## FTP deployment after review

Upload only these three files from this branch to `/var/www/ca.koalainsulation.com/htdocs/`, retaining their relative paths:

1. `wp-content/themes/bricks/beanstalk/location-page.php`
2. `wp-content/themes/bricks/beanstalk/location-performance.php`
3. `wp-content/themes/bricks/assets/js/custom-service.js`

No database updates or migration files. Do not upload tests, vendor, Composer files, tooling or this document.

After uploading, purge WP Rocket generated CSS/background CSS and page caches, then the hosting/CDN caches for Canadian location pages, `/ca/locations`, and the changed JS/CSS assets. Verify canonical URLs in a new browser context: missing legacy preload absent, no duplicated `/ca/ca/` background request, quote and financing backgrounds visible, Durham search shows a clickable phone and correct hidden routing fields. Check desktop and mobile.

## Separate remaining review

East Toronto has a third-party CallRail `icap.js` 404; provider configuration needs investigation. Blog, Franchise and footer Locations navigation differ from saved old pages; navigation decisions are outside this patch and existing navigation PRs remain separate. This PR does not merge, deploy, change database settings, or certify actual lead delivery.
