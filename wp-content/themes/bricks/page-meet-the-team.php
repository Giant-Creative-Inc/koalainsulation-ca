<?php
/* Template Name: Meet the Team */
get_header();

// --- Drop-in replacement for /meet-the-team routing + correct HTTP status ---

// Parse current path (no query string) and split
$path  = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$parts = $path === '' ? [] : explode('/', $path);

// Supported patterns:
//   /ca/{location}/meet-the-team
//   /{location}/meet-the-team
//   /meet-the-team (generic)
$idx = array_search('meet-the-team', $parts, true);
if ($idx === false) {
    // Not a route this template should handle → true 404
    status_header(404);
    get_template_part('404');
    get_footer();
    exit;
}

// Determine location segment (if any), handling optional /ca prefix
$location_slug = null;
if ($idx >= 1 && $parts[$idx - 1] !== 'ca') {
    // /{location}/meet-the-team
    $location_slug = sanitize_title($parts[$idx - 1]);
} elseif ($idx >= 2 && $parts[$idx - 1] === 'ca') {
    // /ca/{location}/meet-the-team
    $location_slug = sanitize_title($parts[$idx - 2]);
}

// Resolve the location and set proper HTTP status + queried object
$location = null;
if ($location_slug) {
    $location = get_page_by_path($location_slug, OBJECT, 'location'); // your CPT slug
    if (!$location instanceof WP_Post) {
        // Location provided but not found → true 404
        status_header(404);
        get_template_part('404');
        get_footer();
        exit;
    }

    // ✅ Mark as a real page and bind the location so titles/canonicals work
    global $wp_query, $post;
    if ($wp_query) {
        $wp_query->is_404 = false;
        $wp_query->is_page = true;
        $wp_query->queried_object    = $location;
        $wp_query->queried_object_id = $location->ID;
    }
    $post = $location;
    setup_postdata($post);
    status_header(200);
    nocache_headers();

} else {
    // Generic /meet-the-team (no location segment) → still a 200
    global $wp_query;
    if ($wp_query) {
        $wp_query->is_404 = false;
        $wp_query->is_page = true;
    }
    status_header(200);
    nocache_headers();
}
// --- End drop-in ---

if ($location) {
  // Get the location ID
  $location_id = $location->ID;

  // Query for "Meet the Team" posts related to the current location
  $args = array(
    'post_type' => 'resources-landing-pa', // The custom post type slug for "Meet the Team"
    'posts_per_page' => -1, // Show all posts
    'meta_query' => array(
      array(
        'key' => 'rl_related_location', // The ACF relationship field key in "Meet the Team"
        'value' => $location_id, // The location ID from the URL
        'compare' => 'LIKE', // Ensure it checks for the location ID in the relationship field
      ),
    ),
    'tax_query' => array(
      array(
        'taxonomy' => 'resources-page-type',
        'field' => 'slug',
        'terms' => 'meet-the-team', // Replace with the term you want to filter by
      ),
    ),
  );

  $query = new WP_Query($args);

  // Display related team members
  if ($query->have_posts()):
    while ($query->have_posts()):
      $query->the_post();
      ?>
      <main id="brx-content">
        <section id="brxe-toxqeb" class="brxe-section section">
          <div id="brxe-ykpymv" class="brxe-container padding-global">
            <div id="brxe-ezcjwt" class="brxe-block section-component">
              <div id="brxe-mtktyr" class="brxe-block brx-grid">
                <div id="brxe-qwenkp" class="brxe-block hero_content-wrapper">
                  <div id="brxe-ixvlsr" class="brxe-div image-wrapper absolute">
                    <img width="572" height="214"
                      src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                      class="brxe-image image-contain css-filter size-full" alt="" id="brxe-trylcc" decoding="async"
                      data-type="string" sizes="(max-width: 572px) 100vw, 572px"
                      srcset="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?> 572w, <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w" />
                  </div>
                  <h2 id="brxe-mqypve" class="brxe-heading heading-style-h2 font-weight-bold is-all-caps" data-animi="up"
                    data-duration="0.6">
                    <?php the_title(); ?>
                  </h2>
                  <div id="brxe-phxlqj" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                    data-duration="0.6" data-delay="0.2">
                    <?php
                    $heroContent = get_field('rl_hero_description');
                    if ($heroContent) {
                      echo '<div>' . esc_html($heroContent) . '</div>';
                    }
                    ?>
                  </div>
                </div>
                <div id="brxe-vdwwag" class="brxe-block hero_image-wrapper">
                  <?php
                  $rl_image = get_field('rl_image');
                  if ($rl_image): ?>
                    <img width="1024" height="952" src="<?php echo esc_url($rl_image); ?>"
                      class="brxe-image image-cover css-filter size-large" alt="Meet the Team" id="brxe-xpkvbi" loading="eager"
                      decoding="async" srcset="<?php echo esc_url($rl_image); ?>" sizes="(max-width: 1024px) 100vw, 1024px" />
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section id="brxe-wpwydx" class="brxe-section section">
          <div id="brxe-uebymw" class="brxe-container padding-global">
            <div id="brxe-ifwoob" class="brxe-block section-component">
              <div id="brxe-sqbaqq" class="brxe-block" data-animi="up" data-duration="0.6">
                <div id="brxe-vqlorg" class="brxe-text">
                  <?php
                  $content = get_field('rl_description');
                  if ($content) {
                    echo $content;
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
      <?php
    endwhile;
    echo '</div>';
  else:
    echo '<p>No related post found for this location.</p>';
  endif;

  wp_reset_postdata(); // Reset the query
} else {
  echo '<p>Location not found.</p>';
}

get_footer();
?>