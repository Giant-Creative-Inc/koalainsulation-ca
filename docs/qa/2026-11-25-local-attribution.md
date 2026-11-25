# Canadian Local attribution verification — 25 November 2026

Production-matched main: `5673e7c21cb738130b178fb2d051861d85a871ee` (pushed and read back from GitHub).
Attribution implementation: `6e4303d`, on `feature/ca-cache-safe-attribution`.

Applied the three runtime plugin files (attribution JS, version constant, plugin header) to the existing Canadian Local installation at `/Users/44northdigitalmarketing/repositories/koala/ca/app/public`. Updated only its existing generated attribution JS and gzip variant so WP Rocket would serve the fix. Original files are backed up in `/private/tmp/koala-ca-local-before-attribution-0.8.3.tar.gz`. No Local database changes.

Browser verification used the existing test Chromium, three isolated contexts, and `http://koala-insulation-ca.local/east-toronto`. Local paths omit the production `/ca` prefix. Test contexts blocked requests to other hosts. No leads were submitted.

- A: campaign and gclid `LOCAL_QA_A` matched.
- B: campaign and gclid `LOCAL_QA_B` matched, independently of A.
- Clean visitor: campaign and gclid blank.
- For all three contexts, deliberately replacing the campaign/click-ID fields with `STALE_CACHE`, then dispatching `gform/post_render`, restored the correct visitor-owned values (including blanks).
- Navigating to the same page without campaign parameters preserved only that context's attribution.
- Tested the actual Form 1 mappings: campaign field 13, gclid field 21.

All 13 targeted Node regression tests pass after rebasing onto the production-matched main. Browser coverage above is Form 1 on East Toronto; real AJAX validation, popup/mobile/other-location paths, saved entries, notifications, CRM, tracker and Google Ads delivery are not certified by these checks. Existing broad theme-suite failures are documented in `2026-11-25-production-parity.md`.

Chrome DevTools and the system Chrome automation browser failed to initialize; the in-app browser's redacted field comparisons were inconclusive. The successful assertions above came from direct Playwright checks in the installed test Chromium, not from those inconclusive reads.
