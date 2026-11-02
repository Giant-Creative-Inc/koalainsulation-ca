# Canadian location QA — November 2, 2026

Outcome: QA completed with issues. The six staging locations are not yet cleared for launch. CRM delivery is excluded at the user's request. Production was not changed.

## Location results

| Location | HTTP / core SEO | Desktop / mobile controls | Forms / postal lookup | Maps after Git sync | Reviews | Overview navigation | Overall |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Edmonton | Pass | Pass | Pass within scope | Pass | Fail | Fail | Needs fixes |
| Durham | Pass | Pass | Pass within scope | Pass | Fail | Fail | Needs fixes |
| Winnipeg | Pass | Pass | Pass within scope | Pass | Fail | Fail | Needs fixes |
| Halton | Pass | Pass | Pass within scope | Pass | Fail | Fail | Needs fixes |
| London | Pass | Pass | Pass within scope | Pass | Fail | Fail | Needs fixes |
| Kitchener-Waterloo | Pass | Pass | Pass within scope | Pass | Fail | Fail | Needs fixes |

URLs are `https://staging.koalainsulation.com/ca/<slug>`, using edmonton, durham, winnipeg, halton, london and kitchener-waterloo. East Toronto is absent from staging and is outside this six-page result. Testing and Efficiency remain excluded.

## Confirmed issues

### CA-QA-01 — Failed Blog and Services overview destinations

On all six pages, the header/footer Blog destination `/ca/<slug>/blog` and Services overview destination `/ca/<slug>/services/` return HTTP 404. Slash variants were checked, and full GET requests confirmed the errors. The individual service detail links passed the crawl.

The global alternatives `/ca/blog` and `/ca/services` return 200. The same location-scoped London overview routes also return 404 on the existing live site; their existence there does not make these links acceptable. Correct the destinations or implement the intended overview routes before launch.

### CA-QA-02 — Missing staging review feeds

All six pages show “Reviews are currently unavailable.” The staging location records have empty google_review_shortcode values. Hero ratings/review counts are consequently absent too. Existing live Canadian feeds were not overwritten. Restore or validate the correct Canadian feeds in the staging rehearsal; do not copy a shared US feed or stale staging mappings into production.

### CA-QA-03 — Review: old/new visible copy

The six location-specific overview disclosures are preserved, including their full “Read More” copy. The apparent initial text mismatch was the old toggle text being concatenated with the first heading; it was excluded after normalized comparison.

Seventeen shared old paragraphs/headings are changed or absent in each native page: eleven explanatory/benefit/service-step paragraphs plus the testimonial heading and five old quotes. Examples include the old Natural Resources Canada 65% sentence, the green-insulation paragraph, and several comfort/quality promises. Review these as Beanstalk design/content changes rather than automatically calling them regressions or silently marking them Pass. Restore required copy unless its removal is approved. Validate the old testimonials' Canadian relevance before restoring them.

Kitchener-Waterloo also replaces “Premium Insulation Service in Kitchener Waterloo” with “Trusted Insulation Services in Kitchener Waterloo.” This needs content approval if exact legacy-copy parity is required.

### CA-QA-04 — Review: Open Graph output changed

On all six pages, og:type changes from website to article, og:title changes from the short location name to the SEO title, og:url gains a trailing slash, and article:modified_time reflects migration. The SEO title, description, canonical and robots tags remain unchanged. Review the intended social sharing type/title/URL rather than treating these tags as unchanged.

### CA-QA-05 — Existing broken image preload

`/ca/wp-content/uploads/2025/02/2-600x400.webp` returns 404 and is still preloaded on every page. The same preload exists in the saved old HTML. This is not a missing visible hero image. Remove or correct the unnecessary preload when addressing asset cleanup.

### CA-QA-06 — Staging form attribution coverage gap

Staging forms 1 and 3 are older than production and lack the newer CTA/service and other attribution fields. Both render the existing Canadian Postal Code field and consent copy. Keep the separate production dependency manifest; do not overwrite live forms with staging forms. This limits attribution parity testing even though rendering and validation pass.

## Resolved during this QA pass

CA-QA-07: staging's existing zip-shape-map-shortcode.php differed from Git. With the user's explicit authorization, the Git version from commit a3efd1b was uploaded after a private backup. Eight map regression checks and PHP syntax validation passed. The final file SHA-256 is b5a7a0c08fbebdc7c621e53fe5b5bf215e2623d760bdd95dde9ee67fdd3133a0.

All six normal page URLs now emit empty boundary-code arrays, showBoundary=false and showPin=true. Browser retests confirmed loaded Google map tiles and centers matching each location's configured coordinates. The Canadian territory outline is disabled. The 165 previously uploaded runtime files plus this map helper were rechecked against Git (166 total). This does not claim that every unrelated WordPress/plugin file on the server was compared.

## Completed checks

- All six normal URLs return 200 and use the native renderer. Each has one H1 and no image elements missing an alt attribute; decorative empty alt values are allowed.
- SEO title, meta description, canonical and robots match the saved old staging pages. The complete parsed JSON-LD values are identical, not merely the schema type sets. Staging retains its noindex/nofollow response header.
- Desktop at 1440 × 1000: Services and Resources menus open/close on every location. All 48 service tabs select the matching visible panels. Every overview disclosure opens. No duplicate DOM IDs, horizontal overflow or initial Bricks engine asset leakage was found.
- Mobile at 390 × 844: all six menus open/close; Resources submenus and quote sidebars open; quote popups load and close; there is no horizontal overflow. Loaded hero images and footers were checked on all six locations.
- Existing Canadian location names, address details, base phone numbers, service ordering, Canadian logo, FAQ and Canadian Franchise destinations, social links and all four footer citation links are present. Browser phone replacement by existing tracking scripts was observed; this was not changed.
- Both sidebar form 1 and popup form 3 were tested with empty submissions on all six pages (12 validation flows). Each rejected the five required fields; no confirmation or successful entry was created. The popup remains open to show its validation errors.
- Form entry counts stayed unchanged: form 1 = 34; form 3 = 7. Sidebar hidden location slug/ID and source path match all six locations. Popup forms post to the respective Canadian location URL.
- Public postal lookup matched all six office postal codes to their expected locations. Lowercase/no-space Canadian input normalized correctly. A US ZIP input was rejected with Canadian postal-code guidance.
- Video lightboxes opened the existing Who We Are video on all six pages, fit the mobile viewport and removed the iframe on close. This tests the embed lifecycle, not completion of external video playback.
- Maps were retested on all six pages after syncing the Git helper; each loaded tiles around the correct coordinates without a Canadian territory outline.

## Limits and evidence

No successful lead was submitted, no confirmation/email delivery was claimed, and no CRM delivery testing was performed. Paid performance testing, external social-platform availability and a full WCAG audit are outside this pass. Recheck affected items after fixes.

Evidence is in the working workspace at output/ca-qa-20261102: saved HTML; full SEO/schema/copy comparisons; navigation/asset responses; desktop/mobile screenshots; 12 form-validation results; postal lookup results; map retests; unchanged entry counts; and Git/server hash verification. No lead details were exported.

Private staging backups/journals remain outside htdocs at /var/www/staging.ca.koalainsulation.com/beanstalk-launch-20261102. map-before.php retains the replaced helper. Temporary upload and purge helpers were removed. PRs 8 (staging) and 6 (main) remain subject to manual review/merge; do not auto-merge.
