# Canadian Beanstalk testing

Add a meaningful failing regression before changing runtime behaviour. Run `composer test` and `composer lint` after each change. After CSS changes run `composer build:location-css` first. Test header, content, footer, location data and asset isolation in a browser.

Canadian dependencies are isolated in location-country.php: forms 1 and 3, verified Canadian attachments, and Canadian footer facts. The popup has different service and CTA hidden fields from the sidebar. Preserve the actual Canadian form definitions and integration settings.

Install with `composer install` in this folder. Development tooling is never deployed: exclude tests/, vendor/, tools/, Composer files, PHPUnit and PHPCS configuration, hidden files, and this document. Sync production files with the active Canadian Local mirror only.

The migration and activation harnesses are in tests/location-launch at repository root; run each PHP test directly. No database, leads, notifications or HTTP requests are created by these harnesses.
