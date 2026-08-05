<?php
/* Template Name: Testimonials */
get_header();

// Get the current URL path
$current_url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Split the URL into segments
$segments = explode('/', $current_url);

// Determine if the URL is location-specific or generic
if (count($segments) === 3 && $segments[0] === 'ca' && $segments[2] === 'testimonials') {

  $location_slug = sanitize_title($segments[1]);
  $location_post = get_page_by_path($location_slug, OBJECT, 'location');

  if (!$location_post) {
    // True 404 if location doesn't exist
    status_header(404);
    nocache_headers();
    get_template_part('404');
    get_footer();
    exit;
  }
  
  global $wp_query, $post;
  if ($wp_query) {
    $wp_query->is_404             = false;
    $wp_query->is_page            = true;
    $wp_query->queried_object     = $location_post;
    $wp_query->queried_object_id  = $location_post->ID;
  }

  $post = $location_post;
  setup_postdata($post);

  status_header(200);
  nocache_headers();

  // Handle Google Ads / Android HEAD checks
  if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    wp_reset_postdata();
    get_footer();
    exit;
  }

  show_location_page($location_slug);

  wp_reset_postdata();
  get_footer();
  exit;
}

/**
 * /ca/testimonials
 */
elseif (count($segments) === 2 && $segments[0] === 'ca' && $segments[1] === 'testimonials') {

  global $wp_query;
  if ($wp_query) {
    $wp_query->is_404  = false;
    $wp_query->is_page = true;
  }

  status_header(200);
  nocache_headers();

  if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    get_footer();
    exit;
  }

  render_fallback_content();

  get_footer();
  exit;
}

/**
 * Anything else -> real 404
 */
else {
  status_header(404);
  nocache_headers();
  get_template_part('404');
  get_footer();
  exit;
}

function show_location_page($location_slug)
{
    // Get the location post by slug
    $location_post = get_page_by_path($location_slug, OBJECT, 'location');

    if (!$location_post) {
        echo "Location not found.";
        return;
    }

    // Get the location ID
    $location_id = $location_post->ID;

    $location_name = get_field('location_name', $location_id);

    $location_title = get_the_title($location_id);

    $niceJobId = get_field('location_nicejob_id', $location_id);

    $gr_shortCode = get_field('google_review_shortcode', $location_id);

    ?>
    <main id="brx-content">
        <section id="brxe-xijebw" class="brxe-section section">
            <div id="brxe-jxukmf" class="brxe-container padding-global">
                <div id="brxe-ptxeyb" class="brxe-block padding-section-small">
                    <div id="brxe-eabunk" class="brxe-block brx-grid section-component">
                        <div id="brxe-qskzpo" class="brxe-block">
                            <div id="brxe-yktvvu" class="brxe-block">
                                <h1 id="brxe-vmlnte" class="brxe-heading heading-style-h1" data-animi="up">
                                  <?php if ($location_name) {
                                    echo 'Koala Insulation of ' . $location_name . ' Reviews';
                                  } else {
                                    echo 'Testimonials';
                                  } ?>
                                </h1>
                                <div id="brxe-nnregf" class="brxe-text-basic text-size-regular" data-animi="up"
                                    data-duration="0.6" data-delay="0.2">
                                    See what our happy customers have to say about their experience
                                    with Koala Insulation. Our commitment to quality and customer
                                    satisfaction shines through in every project, and we’re proud to
                                    share the stories of homeowners and businesses who’ve trusted us
                                    to improve their spaces.
                                </div>
                            </div>
                        </div>
                        <div id="brxe-imvfjq" class="brxe-block" data-animi="up" data-duration="0.6">
                            <img width="1005" height="1024"
                                src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-1005x1024.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="" id="brxe-dlkqlj"
                                decoding="async" data-type="string" sizes="(max-width: 1005px) 100vw, 1005px"
                                srcset="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-1005x1024.jpg'); ?> 1005w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-294x300.jpg'); ?> 294w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-768x783.jpg'); ?> 768w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1.jpg'); ?> 1032w" />
                        </div>
                        <img width="572" height="214"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                            class="brxe-image css-filter size-full" alt="" id="brxe-shfdty" decoding="async"
                            data-type="string" sizes="(max-width: 572px) 100vw, 572px"
                            srcset="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?> 572w, <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w" />
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-sqpuoa" class="brxe-section section">
            <div id="brxe-ybaspn" class="brxe-container padding-global">
                <div id="brxe-ipavqn" class="brxe-block padding-section-medium">
                    <div id="brxe-trcmnv" class="brxe-block section-component">
                        <div id="brxe-znbzhq" class="brxe-block">
                            <div id="brxe-hmywxl" class="brxe-div">
                              <h2 id="brxe-ipnkoa" class="brxe-heading heading-style-h2 text-color-green">
                              <?php if ($location_name) {
                                echo 'Real Reviews from ' . $location_name . ' Customers';
                              } else {
                                echo 'Real Reviews from Customers';
                              } ?>
                            </h2>
                                <?php
                                if ($niceJobId && $niceJobId !== "") {
                                  echo '<div class="nj-stories" data-branding="bottom"></div>';
                                } else if ($gr_shortCode && $gr_shortCode !== "") {
                                  echo do_shortcode($gr_shortCode);
                                } else {
                                  echo '<div class="nj-stories" data-branding="bottom" data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392"></div>';
                                }
                                ?>
                            </div>
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
                            Ready to get started with improving your home’s comfort? Get in touch with us for a quote and
                            let’s discuss how we can help you achieve a more energy-efficient and cozy home.
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
}

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{
    ?>
    <main id="brx-content">
        <section id="brxe-xijebw" class="brxe-section section">
            <div id="brxe-jxukmf" class="brxe-container padding-global">
                <div id="brxe-ptxeyb" class="brxe-block padding-section-small">
                    <div id="brxe-eabunk" class="brxe-block brx-grid section-component">
                        <div id="brxe-qskzpo" class="brxe-block">
                            <div id="brxe-yktvvu" class="brxe-block">
                                <h1 id="brxe-vmlnte" class="brxe-heading heading-style-h1" data-animi="up">
                                    Koala Insulation Reviews and Testimonials
                                </h1>
                                <div id="brxe-nnregf" class="brxe-text-basic text-size-regular" data-animi="up"
                                    data-duration="0.6" data-delay="0.2">
                                    See what our happy customers have to say about their experience
                                    with Koala Insulation. Our commitment to quality and customer
                                    satisfaction shines through in every project, and we’re proud to
                                    share the stories of homeowners and businesses who’ve trusted us
                                    to improve their spaces.
                                </div>
                            </div>
                        </div>
                        <div id="brxe-imvfjq" class="brxe-block" data-animi="up" data-duration="0.6">
                            <img width="1005" height="1024"
                                src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-1005x1024.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="" id="brxe-dlkqlj"
                                decoding="async" data-type="string" sizes="(max-width: 1005px) 100vw, 1005px"
                                srcset="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-1005x1024.jpg'); ?> 1005w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-294x300.jpg'); ?> 294w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-768x783.jpg'); ?> 768w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1.jpg'); ?> 1032w" />
                        </div>
                        <img width="572" height="214"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                            class="brxe-image css-filter size-full" alt="" id="brxe-shfdty" decoding="async"
                            data-type="string" sizes="(max-width: 572px) 100vw, 572px"
                            srcset="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?> 572w, <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w" />
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-sqpuoa" class="brxe-section section">
            <div id="brxe-ybaspn" class="brxe-container padding-global">
                <div id="brxe-ipavqn" class="brxe-block padding-section-medium">
                    <div id="brxe-trcmnv" class="brxe-block section-component">
                        <div id="brxe-znbzhq" class="brxe-block">
                            <div id="brxe-soskhz" class="brxe-div">
                                <div class="nj-badge"
                                    data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392">
                                </div>
                            </div>
                            <div id="brxe-hmywxl" class="brxe-div">
                                <div class="nj-stories" data-branding="bottom"
                                    data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div id="cta" class="brxe-template">
            <section id="brxe-agowsi" class="brxe-section section">
                <div id="brxe-hwktzs" class="brxe-block section-component">
                    <div id="brxe-evndrw" class="brxe-block">
                        <div id="brxe-tkgjbp" class="brxe-block">
                            <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                data-animi="up" data-delay="0.2" data-duration="0.6">
                                Find Your Location
                            </h2>
                            <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up"
                                data-duration="0.6" data-delay="0.3">
                                Ready to Improve Your Insulation?
                            </div>
                            <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold"
                                data-animi="up" data-duration="0.6" data-delay="0.3">
                                Whether it's spray foam insulation, blown-in insulation, or anything
                                in between, we're here to help.
                            </div>
                            <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                                data-duration="0.6">
                                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                                    <input type="text" id="my-zipcode-input" class="top-zipcode-input"
                                        placeholder="Zip or Postal Code" />
                                </div>
                                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                                    <div id="brxe-smmtik" class="brxe-div">
                                        <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg"
                                            width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M8.65481 16.7633C8.67746 16.7764 8.69527 16.7865 8.70788 16.7936L8.72882 16.8053C8.89597 16.8971 9.10332 16.8964 9.27063 16.8056L9.29212 16.7936C9.30473 16.7865 9.32254 16.7764 9.34519 16.7633C9.39049 16.737 9.45523 16.6988 9.53663 16.6486C9.69935 16.5484 9.92906 16.4007 10.2035 16.2068C10.7513 15.8198 11.4823 15.2456 12.2149 14.4955C13.673 13.0026 15.1875 10.7596 15.1875 7.875C15.1875 4.45774 12.4173 1.6875 9 1.6875C5.58274 1.6875 2.8125 4.45774 2.8125 7.875C2.8125 10.7596 4.32699 13.0026 5.78509 14.4955C6.51769 15.2456 7.24868 15.8198 7.79654 16.2068C8.07094 16.4007 8.30065 16.5484 8.46337 16.6486C8.54477 16.6988 8.60951 16.737 8.65481 16.7633ZM9 10.125C10.2426 10.125 11.25 9.11764 11.25 7.875C11.25 6.63236 10.2426 5.625 9 5.625C7.75736 5.625 6.75 6.63236 6.75 7.875C6.75 9.11764 7.75736 10.125 9 10.125Z"
                                                fill="white"></path>
                                        </svg>
                                    </div>
                                    <div id="brxe-aymaue" class="brxe-text-basic">
                                        Find My Location
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="brxe-bshupm" class="brxe-div">
                            <img width="296" height="143"
                                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug"
                                decoding="async" data-type="string" />
                        </div>
                        <div id="brxe-wowmun" class="brxe-div">
                            <img width="560" height="352"
                                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf"
                                decoding="async" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
          <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
          <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
        " />
                        </div>
                        <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
                            data-delay="1">
                            <img width="440" height="410"
                                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy"
                                decoding="async" data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
          <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
          <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
        " />
                        </div>
                    </div>
                </div>
                <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6"
                    data-delay="0.4">
                    <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
                        viewBox="0 0 62 62" fill="none">
                        <g clip-path="url(#clip0_6431_678)">
                            <path
                                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                                fill="#95C93D"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_6431_678">
                                <rect width="44" height="44" fill="white"
                                    transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </section>
        </div>
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
}
?>