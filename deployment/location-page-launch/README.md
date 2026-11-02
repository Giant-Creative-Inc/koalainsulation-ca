# Canadian location rollout

Runtime upload: Canadian functions.php and production files in beanstalk/. Exclude tests/, vendor/, tools/, Composer files, PHPUnit/PHPCS config, hidden files and TESTING.md. Do not upload US files. City importer PR 3 is separate. No auto-merge.

Place the deployment package outside htdocs with directory mode 0700 and files 0600. Run from the Canadian WordPress root. Set release_dir to the absolute private package path.

```bash
wp db export "$release_dir/before.sql" --allow-root
sha256sum "$release_dir/before.sql" > "$release_dir/before.sql.sha256"
wp eval-file "$release_dir/migrate.php" dry-run "$release_dir/locations.proposed.json" "$release_dir/plan.json" --allow-root
# Review target slugs and proposed content before applying.
wp eval-file "$release_dir/migrate.php" apply "$release_dir/plan.json" "$release_dir/journal.json" --allow-root
wp eval-file "$release_dir/migrate.php" verify "$release_dir/journal.json" --allow-root
wp eval-file "$release_dir/migrate.php" activate "$release_dir/journal.json" --allow-root
```

Writes: seven existing post_content values; imported design attachments and identity metadata; media map; migrated ID allowlist; activation. WordPress may create revisions and modification timestamps. Existing SEO, ACF, review feeds, postal territories, form definitions, notifications and KGI configuration stay intact and are fingerprinted. Never copy a staging database or forms over production.

Production target: https://koalainsulation.com/ca. Testing and Efficiency remain legacy. East Toronto is included on production but absent from staging. Staging rehearses six locations with its own captured manifest because its forms lack the newer live attribution fields. Keep the production manifest unchanged. A rendered form does not prove lead delivery.

Purge only affected /ca/location paths and Canadian Beanstalk assets. Check normal public URLs afterwards. Keep private backups/journals; remove temporary upload/extraction files and helpers after QA.

Rollback: disable activation, run the runner in rollback mode with the private journal, restore matching runtime if required, and purge affected paths. Do not restore an old whole database over new leads. The runner refuses editorial drift and retains imported media.
