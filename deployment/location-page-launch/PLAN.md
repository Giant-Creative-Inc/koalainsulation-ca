# Beanstalk location launch: database and release plan

Status: Canadian runtime and migration implemented; six staging locations migrated and enabled. Production unchanged. No auto-merge or automatic migration hook. November 2 QA is complete with open navigation, reviews and content-review findings; see QA.md. CRM is excluded from the current QA scope at the user's request.

## Database strategy

Migrate the target site's current records in place. Never restore staging over production, bulk-sync the database, run a global search/replace, or copy US IDs into Canada. Take a new inventory immediately before execution; the counts below are observations from November 2, 2026, not hardcoded migration scope.

## Planned write set

| Data | Planned operation | Required safeguards |
| --- | --- | --- |
| Approved `location` posts: `post_content` | Generate native block layouts separately from each production location's own ACF values. Preserve existing native layouts unless a reviewed transformation is required. | Explicit ID/slug allowlist; exclude closed, redirect-only, test and campaign locations pending a decision. Capture original content, status, modified time and hash. Dry run must show proposed content and diff. No slug, permalink, title, parent or status changes. |
| Design attachments in posts/postmeta and uploads | Import the reviewed compressed design assets using normal WordPress media APIs. Reuse exact identity/hash matches. | No staging attachment IDs. Resolve existing production attachments by verified file identity. Preserve existing media metadata. Record new IDs, asset hashes, titles and alt text; verify Cloudflare delivery. |
| Attachment identity metadata | Set `_koala_beanstalk_asset` and `_koala_beanstalk_sha256` for newly managed design assets. | Abort on duplicate identity or mismatching hash. Preserve editorial alt/title changes on reruns. |
| `koala_beanstalk_location_media` option | Save the production attachment mapping as each import completes. | Snapshot whether the option existed and its original value; reruns must reuse completed imports. |
| Location review feed mappings / review plugin data | Conditional repair only if a fresh parity audit proves a location would lose its existing reviews. | Separate reviewed per-location diff and rollback export. Do not copy staging feed IDs, cached reviews or a whole plugin table. Prefer production's current feed; preserve intentional corporate fallback. |
| SEO/schema postmeta | Preserve current production values by default. A separately reviewed correction may be needed for confirmed regressions. | Compare titles, descriptions, canonical, robots, social tags, local-business/service/FAQ/breadcrumb schema and visible copy. No wholesale staging SEO import. Record exact before/after keys for any approved exception. |
| `koala_beanstalk_locations_enabled` option | Enable only as the final release write after all content/media checks pass. | Preserve absent/false state for rollback. Renderer support is implemented; enable this option with the migrated ID allowlist during rollout. |

Other ACF fields, Bricks metadata, global content parts, menus, location ownership/territories, ZIP/postal coverage, form definitions and feeds, notification destinations, CRM credentials, SMTP, consent settings, lead entries and attribution settings are outside the default write set. Any necessary exception requires its own diff and validation. WordPress may create revisions/update modification timestamps and plugin hooks may update derived caches; inventory and verify those effects in the rehearsal.

## Required migration implementation before execution

The existing staging migration is a reference, not a production command. Build and test a production-safe runner with `dry-run`, `apply`, `verify` and `rollback` modes. It must validate the exact `home_url` including Canada's `/ca` path, the intended environment, code hashes, forms/fields, attachment identity and approved location allowlist. Never spoof the environment to bypass the staging guard.

A dry run must make zero persistent writes and export per-record before/after hashes and the exact proposed option/meta/media changes. Apply must require that reviewed plan, reject drift, create an exclusive private rollback file before writing, read back every save, journal progress, and stop on failure with activation disabled. Test reruns, interruption, cross-site backup rejection and rollback drift detection. Use WordPress APIs rather than raw SQL so field synchronization and caches remain coherent. Do not deploy development test tooling.

## Execution sequence after approval

1. Rehearse on a current production clone in staging. Export baseline SEO/HTML, content, ACF/integration hashes, navigation targets, redirects, form configuration fingerprints and attachment identities. Never copy production credentials into a public QA artifact.
2. Back up the production database and uploads/media configuration through hosting. Store a verified SQL export and migration rollback JSON outside the public web root with restricted permissions. Confirm a usable restore path. Avoid a site-wide content freeze: use record-level drift checks and coordinate edits to affected locations.
3. Deploy the separately approved runtime while activation remains disabled. Verify file hashes, PHP compatibility and unrelated city/service/national pages. Do not replace the production database.
4. Run the runner's dry run against production; review the exact allowlist, content diffs, image mapping and any explicit SEO/review corrections. Abort on missing fields/forms/media or drift.
5. Apply the approved content/media changes, verify saved hashes, check offloaded assets, and verify the old renderer is still recoverable. Activate last, purge relevant WordPress/hosting/Cloudflare caches, and read back normal public URLs.
6. Run every-location SEO and navigation comparisons, then desktop/mobile interaction tests. Keep existing noindex/canonical policy for intentional campaign/test records; ensure production does not inherit staging-wide indexing restrictions.
7. Separately authorize clearly identified synthetic form submissions before sending them. Cover sidebar and popup, required validation/consent, matched/unmatched ZIP or postal code, location identity, source URL/UTMs, confirmation, notification and CRM receipt. Document both the entry and downstream receipt; a 200 response or successful lookup is insufficient. Never opt a test contact into marketing or trigger a real booking.
8. Record release SHA, backup paths, migration journal, public cache verification and all remaining exceptions. Release is complete only after these checks pass.

## Rollback

Disable activation first and purge all HTML caches so the old Bricks renderer serves again. Restore only migration-owned content/options and explicitly changed meta from the private journal, using before/after hashes to refuse overwriting subsequent edits. Preserve all new leads and unrelated edits. Keep imported attachments unless separately proven unused; deleting them can break shared/CDN references. Do not restore an old whole-database backup over new leads as a routine rollback. Full restore is a coordinated disaster-recovery option with a plan to preserve post-backup writes. Verify old navigation, forms, SEO and public cache output after rollback.

## Release gates

- Meaningful failing tests before any new behavior change, followed by passing Beanstalk `composer test` and `composer lint`; browser checks for header, content, footer, dynamic location data and asset isolation.
- No lost SEO content, links, NAP, schema or established indexing policy compared with the old page. Check heading hierarchy, canonical destination, image alt text and sitemap inclusion. Validate structured data meaning, not just JSON syntax.
- Header/footer destination parity and working desktop/mobile menus, location switching, phone links and service/resource links. Check both location and unrelated page types.
- Routing lookup and end-to-end lead delivery are separate gates. No claim of delivery until authorized tests prove the intended downstream destination.
- Human review and manual merge/deployment decision. No auto-merge and no automatic database migration.

## Canada scope and outstanding work

Target home URL: `https://koalainsulation.com/ca` (not merely the Canadian origin hostname). Canadian WordPress is accessed on its own host, but public links include `/ca`; validate both the instance identity and public path. Current read-only inventory: nine published production locations and seven on staging; East Toronto and Testing are absent from staging. Neither environment has native location layouts or activation enabled. This PR now includes the Canadian runtime, dependency manifest, migration and regression tests.

Forms are 1 (Location Contact Form) and 3 (Location Contact Form - Popup). Canadian runtime must resolve/configure those forms and validate their actual field maps and routing integration. Do not import US forms 12/13 or use US ZIP validation, US-specific campaigns/financing assumptions, or numeric US media/feed/location IDs. Confirm postal-code normalization, territories, country identity, tracking and Canadian consent copy. Rehearse provider-specific booking embeds without creating real bookings.

Build and QA the Canadian runtime on staging before production migration. Use current production records to rehearse East Toronto explicitly; do not create or overwrite production locations from stale staging. Decide whether Testing and Efficiency are intentionally excluded; retain their observed noindex/canonical policy. The seven paired old Canadian pages have matching titles/descriptions in the current HTML sample, but there is no native Canadian output yet to compare. Efficiency has duplicate staging meta rows; do not copy them into production or treat them as approved cleanup.

Prepare a Canada-specific content/media/form manifest and private rollback journal. Resolve all design assets locally on the Canadian instance. Audit Canadian reviews, local NAP/schema, service/resource links and `/ca` navigation. Confirm global/cross-border navigation and unrelated Efficiency/city/service features remain intact. Existing city-page PR #3 is a separate feature and must not be silently bundled into the location launch.
