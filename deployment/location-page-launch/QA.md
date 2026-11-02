# Canadian location QA — November 2, 2026

## Completed

- Canadian runtime uploaded to staging; six existing real locations migrated and enabled at normal URLs. East Toronto does not exist on staging. Production unchanged.
- Local `composer test`: 84 tests / 336 assertions. `composer lint`: pass. CSS bundle freshness checked by composer test.
- Migration: 14 safeguard checks, 38 lifecycle checks, 16 activation/REST checks, and optional add-on feed table state tests pass. Dry run makes no database writes; apply/rollback failures preserve activation safety and editorial changes.
- Canadian forms 1 and 3 retained. Live field identities verified; popup service is field 24 and CTA is 23. Postal code field is 6. Existing consent and notification settings unchanged.
- Six normal staging URLs return 200/native layout. Titles, descriptions, canonical, robots and schema type sets match the saved staging baseline. This does not establish semantic parity for every structured-data value.
- Local London: header/content/footer/location services, Canadian review feed, popup open/load/close and image loading checked. Staging London: Canadian logo, local navigation/phone/services, FAQ/Canadian Franchise destinations, mobile menu and quote sidebar checked.
- Canadian social links and all four existing footer citation destinations preserved. Canadian facts 85%/64% retained. US financing footer omitted; all seven real Canadian locations currently have financing disabled.

## Outstanding release checks

- Staging forms are older than production and lack the newer attribution fields. The staging rehearsal uses its own captured manifest; do not deploy that manifest or staging forms to production.
- Staging review-feed data is incomplete: native London displays reviews unavailable. Local London has the correct Canadian feed. Verify each existing live Canadian feed in native output before completing production QA; do not copy staging review mappings.
- East Toronto requires a production-context preview/rehearsal because it is absent from staging.
- Full normalized old/new navigation, visible copy, schema values and redirect checks remain review items. Raw removed-link lists include legacy fragment IDs, national versus location-scoped routes, stale US review links and trailing-slash variants; these are not automatically failures.
- No synthetic entry was submitted. Postal lookup, entry creation, intended notification receipt and CRM receipt remain separate checks. Rendering a form is not proof of delivery.
- No PR auto-merge, live database changes, or live deployment performed.

Evidence: output/ca-launch-20261102 in the working workspace. Staging private backup and journals: /var/www/staging.ca.koalainsulation.com/beanstalk-launch-20261102. Temporary upload/extraction files are removed after final staging verification; retained backups/journals stay private.
