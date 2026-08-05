<?php
/* Template Name: Areas Served */
get_header();

// --- Drop-in replacement for robust routing + correct HTTP status ---

// Parse current path (no query string) and split
$path  = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$parts = $path === '' ? [] : explode('/', $path);

// Expect one of:
//   /ca/{location}/areas-served
//   /{location}/areas-served
//   /areas-served  (generic)
$idxAreas = array_search('areas-served', $parts, true);
if ($idxAreas === false) {
    // Not the route this template should handle → true 404
    status_header(404);
    get_template_part('404');
    get_footer();
    exit;
}

// Work out the location segment (if any), supporting optional /ca prefix
$location_slug = null;
if ($idxAreas >= 1 && $parts[$idxAreas - 1] !== 'ca') {
    // /{location}/areas-served
    $location_slug = sanitize_title($parts[$idxAreas - 1]);
} elseif ($idxAreas >= 2 && $parts[$idxAreas - 1] === 'ca') {
    // /ca/{location}/areas-served
    $location_slug = sanitize_title($parts[$idxAreas - 2]);
}

// Resolve the location (if provided)
$location = null;
if ($location_slug) {
    $location = get_page_by_path($location_slug, OBJECT, 'location'); // your CPT slug
    if (!$location instanceof WP_Post) {
        // Location segment present but invalid → true 404
        status_header(404);
        get_template_part('404');
        get_footer();
        exit;
    }

    // Mark as a real page and bind queried object so Yoast/canonicals/titles work
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
    // Generic /areas-served without a location segment → still a 200
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

    //$areas_served = get_field('location_area_serviced', $location_id);
    $areas_served = get_posts(array(
        'post_type' => 'resources-landing-pa',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rl_related_location',
                'value' => $location_id,
                'compare' => 'LIKE',
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'resources-page-type',
                'field' => 'slug',
                'terms' => 'areas-served',
            ),
        ),
    ));
    ?>
    <main id="brx-content">
        <section id="brxe-cigjsz" class="brxe-section section">
            <div id="brxe-nrbpzr" class="brxe-container padding-global">
                <div id="brxe-aukyrh" class="brxe-block padding-section-medium">
                    <div id="brxe-ahalnk" class="brxe-block">
                        <h1 id="brxe-hwooji" class="brxe-heading heading-style-display" data-animi="up" data-duration="0.6">
                            Areas Served
                        </h1>
                        <div id="brxe-mjmuai" class="brxe-block brx-grid" data-animi="up" data-duration="0.6"
                            data-delay="0.2">
                            <?php if ($areas_served): ?>
                                <?php foreach ($areas_served as $area): ?>
                                    <?php
                                    $area_id = $area->ID;
                                    $area_title = get_the_title($area_id);
                                    $area_link = get_permalink($area_id);
                                    ?>
                                    <a id="brxe-hvsime" class="brxe-block" href="<?php echo $area_link ?>">
                                        <div id="brxe-qwtyko" class="brxe-text-basic">
                                            <?php echo $area_title ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No serviced areas found for this location.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="cta-quote" class="brxe-section section">
            <div id="brxe-cqllpo" class="brxe-block section-component">
                <div id="brxe-etcewf" class="brxe-block">
                    <div id="brxe-onytvi" class="brxe-block">
                        <h2 id="brxe-olzjbf" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                            data-animi="up" data-delay="0.2" data-duration="0.6">
                            Get a quote
                        </h2>
                        <div id="brxe-cvthkj" class="brxe-text-basic text-size-regular text-weight-semibold" text-split=""
                            lines-slide-up="">
                            Ready to start your insulation project? Get a free quote from your
                            local Koala Insulation team today.
                        </div>
                        <div id="brxe-hmrpll" class="brxe-div btn is-no-icon" data-animi="up" data-delay="0.4"
                            data-duration="0.6"
                            data-interactions='[{"id":"arkdpe","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                            data-interaction-id="b144f1">
                            <div id="brxe-ddqhas" class="brxe-text-basic">
                                Get a Free Estimate
                            </div>
                        </div>
                    </div>
                    <div id="brxe-aegzfd" class="brxe-div">
                        <img width="296" height="143"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-plfski" decoding="async"
                            loading="lazy" data-type="string" />
                    </div>
                    <div id="brxe-stwpyx" class="brxe-div">
                        <img width="560" height="352"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                            class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-ozalhe"
                            decoding="async" loading="lazy" data-type="string" sizes="(max-width: 560px) 100vw, 560px"
                            srcset="
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
            " />
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php


} else {
    render_fallback_content();
}

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{
    ?>
    <h1>No serviced areas found for this location.</h1>
    <?php
}
get_footer();
?>