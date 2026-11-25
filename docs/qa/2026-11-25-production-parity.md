# Canadian production code parity — 25 November 2026

Compared GitHub main `18ff932a73033604c6e24aed4d352df3b90e5f20` to read-only SHA-256 inventories from `/var/www/ca.koalainsulation.com/htdocs`.

Five deployed runtime files differed and were copied verbatim into this commit:

- `wp-content/themes/bricks/beanstalk/assets/footer.css`
- `wp-content/themes/bricks/beanstalk/assets/header.css`
- `wp-content/themes/bricks/beanstalk/assets/location-critical.css`
- `wp-content/themes/bricks/beanstalk/assets/location-deferred.css`
- `wp-content/themes/bricks/beanstalk/location-footer.php`

These preserve deployed footer destinations/social styling and desktop dropdown positioning. No production files, database settings, or caches were changed.

All 931 common tracked runtime/theme/plugin/index files now match the captured production hashes. Production's plugin README and Composer scripts are older; repository documentation and development tooling are intentionally retained. Missing production files are development-only tests/configuration/build tooling. Live-only files in the audited theme/plugin directories are dependencies, backup files, and OS metadata; they were not imported. This verifies tracked code parity, not database/content/media, third-party plugins, server configuration, generated caches, or a Git deployment revision (production has no `.git` directory).

Validation:

- PHP syntax: location footer passes.
- Generated location CSS consistency check: passes.
- `composer lint`: passes, 41 files.
- `composer test`: 90 tests, 346 assertions, two failures. Re-running against unchanged GitHub main gives the same failures in `CanadianAssetsTest`: an obsolete hardcoded franchise-link expectation and the old insulation-removal category ID. No test expectations were changed in this synchronization.

The new cache-safe attribution work is separate on `feature/ca-cache-safe-attribution`; it is not part of the production-matched main baseline.
