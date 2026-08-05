<?php
/* Template Name: Thank You */
get_header();

// Make /ca/{location}/thank-you or /{location}/thank-you resolve as 200
$path  = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$parts = $path === '' ? [] : explode('/', $path);
$idx   = array_search('thank-you', $parts, true);

if ($idx !== false) {
    $location_slug = null;
    if ($idx >= 1 && $parts[$idx - 1] !== 'ca') {
        $location_slug = sanitize_title($parts[$idx - 1]);       // /{location}/thank-you
    } elseif ($idx >= 2 && $parts[$idx - 1] === 'ca') {
        $location_slug = sanitize_title($parts[$idx - 2]);       // /ca/{location}/thank-you
    }

    global $wp_query, $post;
    if ($wp_query) { $wp_query->is_404 = false; $wp_query->is_page = true; }
    status_header(200); nocache_headers();

    // Optional: bind a location post so Yoast/canonicals/titles can key off it
    if ($location_slug) {
        $loc = get_page_by_path($location_slug, OBJECT, 'location');
        if ($loc instanceof WP_Post) {
            if ($wp_query) { $wp_query->queried_object = $loc; $wp_query->queried_object_id = $loc->ID; }
            $post = $loc; setup_postdata($post);
            // (use $location_slug or $loc->ID below to customize the message if you want)
        }
    }
}
?>

<main id="brx-content">
  <section id="brxe-oackrj" class="brxe-section section">
    <div id="brxe-yfjlyr" class="brxe-container padding-global">
      <div id="brxe-ndrwka" class="brxe-block padding-section-medium">
        <div id="brxe-camgob" class="brxe-block">
          <div id="brxe-gkiinf" class="brxe-block">
            <h3 id="brxe-bjikkh" class="brxe-heading heading-style-h1">
              Thank You!
            </h3>
            <div id="brxe-xmnttr" class="brxe-text-basic text-size-medium">
              We will get back to you as soon as possible.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
get_footer();
?>
