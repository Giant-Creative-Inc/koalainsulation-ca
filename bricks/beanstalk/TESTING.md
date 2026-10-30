# Canadian City Page testing

Run from this directory:

```sh
composer install
composer test
composer lint
```

`composer test` checks generated city CSS, runs the city-theme PHPUnit suite, and runs each isolated Content Engine contract/import/editor test in its own PHP process. `composer lint` checks all deployed Beanstalk PHP against WordPress coding standards.

Image-parser tests use WordPress core's real HTML API. Set `WP_CORE_DIR` to a WordPress installation root when it is not available at the adjacent Canadian Local installation (`../../../ca/app/public` from this directory). No database is used by the automated suite. Composer dependencies and test tooling remain here and must not be copied into the deployed theme.

Edit city section CSS under `wp-content/themes/bricks/beanstalk/assets`, then run `composer build:css`. Check generated bundles into Git. For a Local mirror, back up and synchronize only changed production files; do not copy Composer dependencies, tests or tools.

For frontend changes, also verify a draft in Canadian WordPress: city/province, franchise header and phone, Postal Code contact form, services, reviews, FAQs, footer and schema. Confirm a non-city page retains its existing layout and does not load Beanstalk city assets. Do not submit forms or publish fixtures as part of a smoke test.
