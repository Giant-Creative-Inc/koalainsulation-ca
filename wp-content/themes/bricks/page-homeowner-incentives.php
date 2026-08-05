<?php
/* Template Name: Homeowner Incentives */
get_header();

// --- Drop-in replacement for /homeowner-incentives routing + correct HTTP status ---

// Parse current path (no query string) and split into parts
$path  = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$parts = $path === '' ? [] : explode('/', $path);

// We support all of these:
//   /ca/{location}/homeowner-incentives
//   /{location}/homeowner-incentives
//   /homeowner-incentives   (generic)
$idx = array_search('homeowner-incentives', $parts, true);
if ($idx === false) {
    // Not a route this template should handle → true 404
    status_header(404);
    get_template_part('404');
    get_footer();
    exit;
}

// Work out the location segment (if any), handling optional /ca prefix
$location_slug = null;
if ($idx >= 1 && $parts[$idx - 1] !== 'ca') {
    // /{location}/homeowner-incentives
    $location_slug = sanitize_title($parts[$idx - 1]);
} elseif ($idx >= 2 && $parts[$idx - 1] === 'ca') {
    // /ca/{location}/homeowner-incentives
    $location_slug = sanitize_title($parts[$idx - 2]);
}

// Resolve the location and set proper HTTP status + queried object
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
    // Generic /homeowner-incentives (no location segment) → still a 200
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

    // Query for "Homeowner Incentives" posts related to the current location
    $args = array(
        'post_type' => 'location-homeowner-i', // The custom post type slug for "Homeowner Incentives"
        'posts_per_page' => -1, // Show all posts
        'meta_query' => array(
            array(
                'key' => 'ho_related_location', // The ACF relationship field key in "Homeowner Incentives"
                'value' => $location_id, // The location ID from the URL
                'compare' => 'LIKE', // Ensure it checks for the location ID in the relationship field
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();

            // Get all Homeowner FAQs
            // $all_faqs = get_posts(array(
            //     'post_type' => 'homeowner-incentives',
            //     'posts_per_page' => -1,
            // ));
            ?>
            <main id="brx-content">
                <section id="brxe-oarbyv" class="brxe-section section">
                    <div id="brxe-djsxjl" class="brxe-container padding-global">
                        <div id="brxe-usipuv" class="brxe-block section-component">
                            <div id="brxe-qnrahx" class="brxe-block brx-grid hero-block-grid">
                                <div id="brxe-pimvbm" class="brxe-block hero_content-wrapper">
                                    <h1 id="brxe-bcxope" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        <?php the_title(); ?>
                                    </h1>
                                    <h2 id="brxe-hcoxor" class="brxe-heading text-size-large is-blue" data-animi="up"
                                        data-duration="0.6">
                                        Save more with available rebates and Koala Insulation
                                    </h2>
                                    <div id="brxe-pqwnwo" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-delay="0.2" data-duration="0.6">
                                        <?php
                                        $heroContent = get_field('ho_hero_description');
                                        if ($heroContent) {
                                            echo $heroContent;
                                        }
                                        ?>
                                    </div>
                                    <div id="brxe-uchxjt" class="brxe-div image-wrapper absolute">
                                        <img width="572" height="214"
                                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-zgjhsi"
                                            decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
              " />
                                    </div>
                                </div>
                                <div id="brxe-txferx" class="brxe-block hero_image-wrapper">
                                    <?php
                                    $heroImage = get_field('ho_hero_image');
                                    if ($heroImage):
                                        ?>
                                        <img width="1005" height="1024" src="<?php echo esc_url($heroImage); ?>"
                                            class="brxe-image image-cover css-filter size-large" alt=" " id="brxe-zkpceh"
                                            loading="eager" decoding="async" srcset="<?php echo esc_url($heroImage); ?>"
                                            sizes="(max-width: 1005px) 100vw, 1005px" />
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
				 <p>
										</p>
				<h3 id="brxe-ywrmrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                              Ontario Incentives
                                </h3> 
                <section id="brxe-nxlsnd" class="brxe-section section">
                    <div id="brxe-lkgcfl" class="brxe-container padding-global">
                        <div id="brxe-lwhlxh" class="brxe-block section-component">
                            <div >
                               	<div>
                                  <h2 id="brxe-gqriuj" class="brxe-heading font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                       <span style="color:#03325b;"> Home Renovation Savings Program</span>
                                    </h2>
                                    <div id="brxe-znxkrm" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                       <p><strong>The Home Renovation Savings Program from Enbridge Gas and Save On Energy now offers Ontario homeowners two paths to savings:</p>
										<ul>
  <li>The stand-alone Attic Insulation Rebate with savings up $1250 to improve your attic insulation, with no energy assessment required.</li>
  <li>The multi-service path with rebates of up $7950 for improving your home insulation, air sealing, and more.</li>
</ul> </strong>
										<h3 id="brxe-ywrmrz" class="brxe-heading font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                             Attic Insulation Rebate
                                </h3> 
										<p>Koala Insulation is one of the approved insulation contractors participating in the Attic Insulation Rebate from Enbridge Gas and Save on Energy.</p>
										<p>Program Highlights:</p>
											 <ul>
                                            <li>No Energy Assessment Required</li>
                                            <li>$650 - $1250 Back In Rebates</li>
                                            <li>We Handle The Paperwork</li>
											<li>Koala Insulation Is A Participating Contractor</li>	 
										</ul>
										<p><strong>How the program works</strong></p>
										<ol>
                                            <li>Koala Insulation will confirm your eligibility and provide a free quote. </li>
                                            <li>Koala Insulation will verify your existing insulation level and rebate amount.</li>
                                            <li>Koala insulation will complete your insulation upgrade and submit the paperwork for you.</li>
											<li>You will recieve your rebate cheque in apporximately 60 days after the paperwork has been accepted and processed by Enbridge Gas and Save On Energy.</li>	 
										</ol>
									<p>
										<strong>Your Rebate Amount Is Determined By Your Current Insulation Level.</strong> </p>
										<p>Your Rebate is calculated at 50% of the total attic insulation cost (after tax), up to the maximum Rebate amount       
										</p>
										<ul>
                                            <li>Attic Less than or equal to R-12 upgraded to R-50: Maximum Rebate <strong>$1250</strong></li>
                                            <li>Attic Greater than R-12 to R-25 upgraded to R-50: Maximum Rebate <strong>$1000 </strong></li>
                                            <li>Attic Greater than R-25 to R-35 upgraded to R-50: Maximum Rebate<strong> $800</strong></li>
											<li>Cathedral/Flat Roof R25 or lower upgraded to R-28: Maximum Rebate<strong> $750</strong> </li>
											<li>Cathedral/Flat Roof 12 or lower upgraded to R-20: Maximum Rebate<strong> $650</strong> </li> 
										</ul>
										<h3 id="brxe-ywrmrz" class="brxe-heading font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                              Multi-service Insulation & Air Sealing (attic, wall, foundation, exposed floor) Rebate
                                </h3> 
                                        <ul>
                                            <li>Home energy assessment: Get $600 back</li>
                                            <li>Insulation: Rebates up to $7,700</li>
                                            <li>Air sealing: Rebates up to $250</li>
                                        </ul>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </section>
                    <section id="brxe-dzrbgh" class="brxe-section section bricks-lazy-hidden">
                    <div id="brxe-felhhm" class="brxe-container padding-global bricks-lazy-hidden">
                        <div id="brxe-qisrmc" class="brxe-block section-component bricks-lazy-hidden">
                            <div id="brxe-uagtxr" class="brxe-block bricks-lazy-hidden">
                                <h2 id="brxe-zaddvc" class="brxe-heading heading-style-h2 text-weight-semibold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    qualifying Insulation upgrade and air sealing&nbsp; Rebate
                                    amounts<br />
                                </h2>
                                <div id="brxe-rnekkn" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    To qualify for the Home Renovation Savings rebates, a minimum of two
                                    energy-saving upgrades are required.<br />
                                </div>
                                <div id="brxe-ajkvzh" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    These upgrade projects are included in the Home Renovation Savings program:
                                </div>
                            </div>
                            <div id="brxe-tbtith" class="brxe-block brx-grid bricks-lazy-hidden">
                                <div id="brxe-idscxt" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                    <div id="brxe-ivherk" class="brxe-block bricks-lazy-hidden">
                                        <h2 id="brxe-nxyzbu" class="brxe-heading heading-style-h2 text-weight-semibold">
                                            Attic Insulation
                                        </h2>
                                    </div>
                                    <div id="brxe-cevldw" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-udopdr" class="brxe-text-basic text-size-regular text-weight-semibold">
                                            Up to $1,500
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-asewvw" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                    <div id="brxe-hcazqd" class="brxe-block bricks-lazy-hidden">
                                        <h2 id="brxe-oyhyrh" class="brxe-heading heading-style-h2 text-weight-semibold">
                                            Exterior Wall Insulation
                                        </h2>
                                    </div>
                                    <div id="brxe-gqmlfk" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-vfywmp" class="brxe-text-basic text-size-regular text-weight-semibold">
                                            Up to $3,600
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-bicxvk" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                    <div id="brxe-svhtei" class="brxe-block bricks-lazy-hidden">
                                        <h2 id="brxe-wxiglr" class="brxe-heading heading-style-h2 text-weight-semibold">
                                            Exposed Floor Insulation
                                        </h2>
                                    </div>
                                    <div id="brxe-exungx" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-pajwvf" class="brxe-text-basic text-size-regular text-weight-semibold">
                                            Up to $300
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-mhsoqz" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                    <div id="brxe-dhaadn" class="brxe-block bricks-lazy-hidden">
                                        <h2 id="brxe-fopucr" class="brxe-heading heading-style-h2 text-weight-semibold">
                                            Exterior Wall Insulation
                                        </h2>
                                    </div>
                                    <div id="brxe-etlgtm" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-mktoeh" class="brxe-text-basic text-size-regular text-weight-semibold">
                                            Up to $3,600
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-eexsxn" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                    <div id="brxe-fgrzsm" class="brxe-block bricks-lazy-hidden">
                                        <h2 id="brxe-jwlmku" class="brxe-heading heading-style-h2 text-weight-semibold">
                                            Air Sealing
                                        </h2>
                                    </div>
                                    <div id="brxe-iwslnv" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-kputmj" class="brxe-text-basic text-size-regular text-weight-semibold">
                                            Up to $250
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-idyggx" class="brxe-section section bricks-lazy-hidden">
                    <div id="brxe-rkzaxj" class="brxe-container padding-global bricks-lazy-hidden">
                        <div id="brxe-uzdgnn" class="brxe-block section-component bricks-lazy-hidden">
                            <div id="brxe-sdamlz" class="brxe-block bricks-lazy-hidden">
                                <h3 id="brxe-ywrmrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    How the program works
                                </h3>
                                <div id="brxe-xulpbf" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    <ol>
                                        <li>
                                             Schedule an assessment:
                                        </li>
                                        Before beginning any work, complete an initial home energy assessment with a registered energy advisor from an approved service organization.<br /><br />
                                        <li>Get a custom report:</li>
                                        See which upgrades are recommended for your home and choose at least two to complete.<br /><br />
                                        <li>Complete 2+ upgrades:</li>
                                        Koala Insulation will do the upgrades for your home. <br /><br />
                                        <li>Schedule a follow-up assessment:</li>
                                        After upgrades are completed, schedule a final assessment with the same registered energy advisor.<br /><br />
										<li>Receive your rebates:</li>
                                        Your rebates will be sent after your energy advisor submits all required documents.<br /><br />
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
				<section id="brxe-nxlsnd" class="brxe-section section">
                    <div id="brxe-lkgcfl" class="brxe-container padding-global">
                        <div id="brxe-lwhlxh" class="brxe-block section-component">
                            <div id="brxe-vokjnu" class="brxe-block grid-block">
                                <div id="brxe-zstpoa" class="brxe-block">
                                    <h2 id="brxe-gqriuj" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        Home Efficiency Rebate (HER)
                                    </h2>
                                    <div id="brxe-znxkrm" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        Enbridge Gas launched the Home Efficiency Rebate program on
                                        July 15, 2024. <strong>This program is discontinued and became the Home Renovation Savings Program.</strong> 
                                    </div>
                                </div>
                                <div id="brxe-shuetv" class="brxe-block" data-animi="up" data-duration="0.6">
                                    <div id="brxe-exdnjl" class="brxe-block image-wrapper" data-animi="scale" data-duration="0.6"
                                        data-delay="0.2">
                                        <img width="709" height="824"
                                            src="<?php echo home_url('/wp-content/uploads/2024/10/embridge-home-efficiency-rebate.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full"
                                            alt="Embridge Home Efficiency Rebate program" id="brxe-tevdpk" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-xqxrcv" class="brxe-section section bricks-lazy-hidden">
                    <div id="brxe-lkklqw" class="brxe-container padding-global bricks-lazy-hidden">
                        <div id="brxe-tlzhcp" class="brxe-block section-component bricks-lazy-hidden">
                            <div id="brxe-sntpbh" class="brxe-block grid-block bricks-lazy-hidden">
                                <div id="brxe-rjknvb" class="brxe-block image-wrapper bricks-lazy-hidden" data-animi="up"
                                    data-duration="0.6">
                                    <img width="908" height="983"
                                        src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20908%20983'%3E%3C/svg%3E"
                                        class="brxe-image image-cover css-filter size-full bricks-lazy-hidden" alt=""
                                        id="brxe-cfjvrk" decoding="async"
                                        data-src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?>"
                                        data-type="string" data-sizes="(max-width: 908px) 100vw, 908px"
                                        data-srcset="<?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?> 908w, <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-277x300.jpg'); ?> 277w, <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-768x831.jpg'); ?> 768w" />
                                </div>
                                <div id="brxe-zlqhlx" class="brxe-block bricks-lazy-hidden">
                                    <h2 id="brxe-krotvj" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        Canada Greener Homes Loan<br>(Discontinued as of October 1, 2025)
                                    </h2>
                                    <div id="brxe-ayukqg" class="brxe-block hide bricks-lazy-hidden">
                                        <h4 id="brxe-flruyb"
                                            class="brxe-heading heading-style-h4 text-color-green font-weight-medium"
                                            data-animi="up" data-duration="0.6">
                                            Annual Limit
                                        </h4>
                                        <h3 id="brxe-vizhhm" class="brxe-heading heading-style-h3 font-weight-medium"
                                            data-animi="up" data-duration="0.6">
                                            $1200
                                        </h3>
                                    </div>
                                    <div id="brxe-igpbdj" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
										<p>
											<strong><em>The last day to apply for a loan is October 1, 2025</em></strong>
										</p>
                                        <strong>The Canada Greener Homes Loan offers interest-free loans
                                            between $5,000 and $40,000, with a repayment term of 10 years,
                                            to help Canadians make their homes more energy efficient and
                                            comfortable.</strong><br /><br />The following comes from the Natural Resources Canada
                                        Greener Homes Loan program page, on the official Government of
                                        Canada website.<br /><a
                                            href="https://natural-resources.canada.ca/energy-efficiency/homes/canada-greener-homes-initiative/canada-greener-homes-loan/24286"><u>For
                                                full details on this program, visit their website.</u></a>
                                    </div>
                                    <div id="brxe-adieci" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        <p><span class="is-blue">Loan Eligibility:</span></p>
                                        <p></p>
                                        <ul>
                                            <li>
                                                You should not start any retrofit work before your loan
                                                application has been submitted. Any retrofits started before
                                                submitting your loan application are ineligible.
                                            </li>
                                            <br />
                                            <li>
                                                You must be a Canadian citizen, permanent resident, or
                                                non-permanent resident who is legally authorized to work in
                                                Canada
                                            </li>
                                            <br />
                                            <li>
                                                You must own the home and it must be your primary residence
                                            </li>
                                            <br />
                                            <li>
                                                You have a pre-retrofit evaluation and have not yet had a
                                                post-retrofit evaluation
                                            </li>
                                            <br />
                                            <li>
                                                Your pre-retrofit evaluation was completed on or after April
                                                1, 2020
                                            </li>
                                            <br />
                                            <li>
                                                You have not started the retrofits for which you are seeking a
                                                loan
                                            </li>
                                            <br />
                                            <li>
                                                You have a good credit history and are not in:<br />
                                                <ul>
                                                    <li>a consumer proposal</li>
                                                    <li>an orderly payment of debt program</li>
                                                    <li>a bankruptcy or equivalent insolvency proceeding</li>
                                                </ul>
                                            </li>
                                        </ul>
                                        <p><span class="is-blue">Eligible retrofits:</span></p>
                                        <ul>
                                            <li>
                                                <strong>Home insulation</strong><br />Upgrade your eligible
                                                attic, cathedral ceiling, flat roof, exterior wall, exposed
                                                floor, basement and crawl space.
                                            </li>
                                            <br />
                                            <li>
                                                <strong>Air-sealing</strong> Perform air sealing to improve
                                                the airtightness of your home to achieve the air-change rate
                                                target.
                                            </li>
                                        </ul>
                                        It is good practice, when safe to do so, to take pictures during
                                        retrofits of your home, especially for those renovations that
                                        cannot be visually verified by the energy advisor such as:
                                        <ul>
                                            <li>
                                                insulation concealed behind siding or covered by drywall
                                            </li>
                                            <li>insulation in a cathedral ceiling</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <h2 id="brxe-vjwkht" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                    <div id="brxe-jafqln" class="brxe-text-basic text-size-regular text-weight-semibold text-weight-semibold"
                        data-animi="up" data-duration="0.6">
                        $4,000
                    </div>
                </h2>
                <section id="brxe-eaeoov" class="brxe-section section">
                    <div id="brxe-scffln" class="brxe-container padding-global">
                        <div id="brxe-ofqwue" class="brxe-block section-component">
                            <h4 id="brxe-tyurqy" class="brxe-heading heading-style-h4 font-weight-medium" data-animi="up"
                                data-duration="0.6">
                                Resources
                            </h4>
                            <a id="brxe-hwlyfw"
                                href="https://www.homerenovationsavings.ca/"
                                target="_blank" class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-awpsdj" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-kgvjxc" class="brxe-heading heading-style-h6 font-weight-medium">
                                        More on the Home Renovation Savings Program
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-daosaf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a>
                            <a id="brxe-hwlyfw" href="https://natural-resources.canada.ca/energy-efficiency/home-energy-efficiency/canada-greener-homes-initiative/canada-greener-homes-loan" target="_blank" class="brxe-block" data-animi="up"
                                data-duration="0.6" data-delay="0.4">
                                <div id="brxe-awpsdj" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-kgvjxc" class="brxe-heading heading-style-h6 font-weight-medium">
                                        More on the Canada Greener Homes Loan
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-daosaf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a>
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
                    <div id="brxe-bggdwc" class="brxe-block section-component">
                        <div id="brxe-akzgbv" class="brxe-block">
                            <div id="brxe-dckkyl" class="brxe-block">
                                <h2 id="brxe-cfdbrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    Get a quote
                                </h2>
                                <div id="brxe-sushnb" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-delay="0.2" data-duration="0.6">
                                    Ready to start your insulation project? Get a free quote from your
                                    local Koala Insulation team today.
                                </div>
                                <div id="brxe-dbvghn" class="brxe-div btn is-no-icon"
                                    data-interactions='[{"id":"pigxzw","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                                    data-interaction-id="9f6f9b">
                                    <div id="brxe-efxxnv" class="brxe-text-basic">
                                        Get a Free Estimate
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-ltloqb" class="brxe-div">
                                <img width="296" height="143"
                                    src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-bdqbqz" decoding="async"
                                    loading="lazy" data-type="string" />
                            </div>
                            <div id="brxe-vjmidr" class="brxe-div">
                                <img width="560" height="352"
                                    src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                                    class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-kuxsvn"
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
        endwhile;
        echo '</div>';
    else:
        render_fallback_content();
    endif;

    wp_reset_postdata(); // Reset the query
} else {
    render_fallback_content();
}
get_footer();

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{
    // Get all Homeowner FAQs
    // $all_faqs = get_posts(array(
    //     'post_type' => 'homeowner-incentives',
    //     'posts_per_page' => -1,
    // ));
    ?>
    <main id="brx-content">
        <section id="brxe-oarbyv" class="brxe-section section">
            <div id="brxe-djsxjl" class="brxe-container padding-global">
                <div id="brxe-usipuv" class="brxe-block section-component">
                    <div id="brxe-qnrahx" class="brxe-block brx-grid hero-block-grid">
                        <div id="brxe-pimvbm" class="brxe-block hero_content-wrapper">
                            <h1 id="brxe-bcxope" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                data-animi="up" data-duration="0.6">
                                Homeowner Incentives
                            </h1>
                            <h2 id="brxe-hcoxor" class="brxe-heading text-size-large is-blue" data-animi="up"
                                data-duration="0.6">
                                Save more with available rebates and Koala Insulation
                            </h2>
                            <div id="brxe-pqwnwo" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-delay="0.2" data-duration="0.6">
                                At Koala Insulation, we’re committed to helping you make your home more comfortable and energy-efficient while saving you money. That’s why we’ve gathered information on valuable homeowner incentives and rebates designed to offset the cost of your insulation upgrade. Our team is here to guide you through every step to make sure you get the most out of these programs.
                            </div>
                            <div id="brxe-uchxjt" class="brxe-div image-wrapper absolute">
                                <img width="572" height="214"
                                    src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-zgjhsi"
                                    decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
              " />
                            </div>
                        </div>
                        <div id="brxe-txferx" class="brxe-block hero_image-wrapper">
                            <img width="1005" height="1024"
                                src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-1005x1024.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="" id="brxe-xnaoan" loading="eager"
                                decoding="async" srcset="
              <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-1005x1024.jpg'); ?> 1005w,
              <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-294x300.jpg'); ?>    294w,
              <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-768x783.jpg'); ?>    768w,
              <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2.jpg'); ?>           1032w
            " sizes="(max-width: 1005px) 100vw, 1005px" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
		 <p>
								</p>
			<h3 id="brxe-ywrmrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                              Ontario Incentives
                                </h3>
        <section id="brxe-nxlsnd" class="brxe-section section">
            <div id="brxe-lkgcfl" class="brxe-container padding-global">
                <div id="brxe-lwhlxh" class="brxe-block section-component">
                    <div >
                       	<div>
                          <h2 id="brxe-gqriuj" class="brxe-heading font-weight-bold text-allcaps"
                                data-animi="up" data-duration="0.6">
                               <span style="color:#03325b;"> Home Renovation Savings Program</span>
                            </h2>
                            <div id="brxe-znxkrm" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-duration="0.6">
                               <p><strong>The Home Renovation Savings Program from Enbridge Gas and Save On Energy now offers Ontario homeowners two paths to savings:</p>
								<ul>
  <li>The stand-alone Attic Insulation Rebate with savings up $1250 to improve your attic insulation, with no energy assessment required.</li>
  <li>The multi-service path with rebates of up $7950 for improving your home insulation, air sealing, and more.</li>
</ul> </strong>
								<h3 id="brxe-ywrmrz" class="brxe-heading font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                             Attic Insulation Rebate
                                </h3>
								<p>Koala Insulation is one of the approved insulation contractors participating in the Attic Insulation Rebate from Enbridge Gas and Save on Energy.</p>
								<p>Program Highlights:</p>
								 <ul>
                                    <li>No Energy Assessment Required</li>
                                    <li>$650 - $1250 Back In Rebates</li>
                                    <li>We Handle The Paperwork</li>
									<li>Koala Insulation Is A Participating Contractor</li>
								</ul>
								<p><strong>How the program works</strong></p>
								<ol>
                                    <li>Koala Insulation will confirm your eligibility and provide a free quote. </li>
                                    <li>Koala Insulation will verify your existing insulation level and rebate amount.</li>
                                    <li>Koala insulation will complete your insulation upgrade and submit the paperwork for you.</li>
									<li>You will recieve your rebate cheque in apporximately 60 days after the paperwork has been accepted and processed by Enbridge Gas and Save On Energy.</li>
								</ol>
							<p>
								<strong>Your Rebate Amount Is Determined By Your Current Insulation Level.</strong> </p>
								<p>Your Rebate is calculated at 50% of the total attic insulation cost (after tax), up to the maximum Rebate amount
								</p>
								<ul>
                                    <li>Attic Less than or equal to R-12 upgraded to R-50: Maximum Rebate <strong>$1250</strong></li>
                                    <li>Attic Greater than R-12 to R-25 upgraded to R-50: Maximum Rebate <strong>$1000 </strong></li>
                                    <li>Attic Greater than R-25 to R-35 upgraded to R-50: Maximum Rebate<strong> $800</strong></li>
									<li>Cathedral/Flat Roof R25 or lower upgraded to R-28: Maximum Rebate<strong> $750</strong> </li>
									<li>Cathedral/Flat Roof 12 or lower upgraded to R-20: Maximum Rebate<strong> $650</strong> </li>
								</ul>
								<h3 id="brxe-ywrmrz" class="brxe-heading font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                              Multi-service Insulation & Air Sealing (attic, wall, foundation, exposed floor) Rebate
                                </h3>
                                        <ul>
                                            <li>Home energy assessment: Get $600 back</li>
                                            <li>Insulation: Rebates up to $7,700</li>
                                            <li>Air sealing: Rebates up to $250</li>
                                        </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-dzrbgh" class="brxe-section section bricks-lazy-hidden">
            <div id="brxe-felhhm" class="brxe-container padding-global bricks-lazy-hidden">
                <div id="brxe-qisrmc" class="brxe-block section-component bricks-lazy-hidden">
                    <div id="brxe-uagtxr" class="brxe-block bricks-lazy-hidden">
                        <h2 id="brxe-zaddvc" class="brxe-heading heading-style-h2 text-weight-semibold text-allcaps"
                            data-animi="up" data-duration="0.6">
                            qualifying Insulation upgrade and air sealing Rebate amounts<br />
                        </h2>
                        <div id="brxe-rnekkn" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                            data-duration="0.6">
                            To qualify for the Home Renovation Savings rebates, a minimum of two energy-saving upgrades are required.<br />
                        </div>
                        <div id="brxe-ajkvzh" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                            data-duration="0.6">
                            These upgrade projects are included in the Home Renovation Savings program:
                        </div>
                    </div>
                    <div id="brxe-tbtith" class="brxe-block brx-grid bricks-lazy-hidden">
                        <div id="brxe-idscxt" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                            <div id="brxe-ivherk" class="brxe-block bricks-lazy-hidden">
                                <h2 id="brxe-nxyzbu" class="brxe-heading heading-style-h2 text-weight-semibold">
                                    Attic Insulation
                                </h2>
                            </div>
                            <div id="brxe-cevldw" class="brxe-block bricks-lazy-hidden">
                                <div id="brxe-udopdr" class="brxe-text-basic text-size-regular text-weight-semibold">
                                    Up to $1,500
                                </div>
                            </div>
                        </div>
                        <div id="brxe-asewvw" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                            <div id="brxe-hcazqd" class="brxe-block bricks-lazy-hidden">
                                <h2 id="brxe-oyhyrh" class="brxe-heading heading-style-h2 text-weight-semibold">
                                    Exterior Wall Insulation
                                </h2>
                            </div>
                            <div id="brxe-gqmlfk" class="brxe-block bricks-lazy-hidden">
                                <div id="brxe-vfywmp" class="brxe-text-basic text-size-regular text-weight-semibold">
                                    Up to $3,600
                                </div>
                            </div>
                        </div>
                        <div id="brxe-bicxvk" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                            <div id="brxe-svhtei" class="brxe-block bricks-lazy-hidden">
                                <h2 id="brxe-wxiglr" class="brxe-heading heading-style-h2 text-weight-semibold">
                                    Exposed Floor Insulation
                                </h2>
                            </div>
                            <div id="brxe-exungx" class="brxe-block bricks-lazy-hidden">
                                <div id="brxe-pajwvf" class="brxe-text-basic text-size-regular text-weight-semibold">
                                    Up to $300
                                </div>
                            </div>
                        </div>
                        <div id="brxe-mhsoqz" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                            <div id="brxe-dhaadn" class="brxe-block bricks-lazy-hidden">
                                <h2 id="brxe-fopucr" class="brxe-heading heading-style-h2 text-weight-semibold">
                                    Exterior Wall Insulation
                                </h2>
                            </div>
                            <div id="brxe-etlgtm" class="brxe-block bricks-lazy-hidden">
                                <div id="brxe-mktoeh" class="brxe-text-basic text-size-regular text-weight-semibold">
                                    Up to $3,600
                                </div>
                            </div>
                        </div>
                        <div id="brxe-eexsxn" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                            <div id="brxe-fgrzsm" class="brxe-block bricks-lazy-hidden">
                                <h2 id="brxe-jwlmku" class="brxe-heading heading-style-h2 text-weight-semibold">
                                    Air Sealing
                                </h2>
                            </div>
                            <div id="brxe-iwslnv" class="brxe-block bricks-lazy-hidden">
                                <div id="brxe-kputmj" class="brxe-text-basic text-size-regular text-weight-semibold">
                                    Up to $250
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-idyggx" class="brxe-section section bricks-lazy-hidden">
            <div id="brxe-rkzaxj" class="brxe-container padding-global bricks-lazy-hidden">
                <div id="brxe-uzdgnn" class="brxe-block section-component bricks-lazy-hidden">
                    <div id="brxe-sdamlz" class="brxe-block bricks-lazy-hidden">
                        <h3 id="brxe-ywrmrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                            data-animi="up" data-duration="0.6">
                            How the program works
                        </h3>
                        <div id="brxe-xulpbf" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                            data-duration="0.6">
                            <ol>
                                        <li>
                                             Schedule an assessment:
                                        </li>
                                        Before beginning any work, complete an initial home energy assessment with a registered energy advisor from an approved service organization.<br><br>
                                        <li>Get a custom report:</li>
                                        See which upgrades are recommended for your home and choose at least two to complete.<br><br>
                                        <li>Complete 2+ upgrades:</li>
                                        Koala Insulation will do the upgrades for your home. <br><br>
                                        <li>Schedule a follow-up assessment:</li>
                                        After upgrades are completed, schedule a final assessment with the same registered energy advisor.<br><br>
										<li>Receive your rebates:</li>
                                        Your rebates will be sent after your energy advisor submits all required documents.<br><br>
                                    </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-nxlsnd" class="brxe-section section">
            <div id="brxe-lkgcfl" class="brxe-container padding-global">
                <div id="brxe-lwhlxh" class="brxe-block section-component">
                    <div id="brxe-vokjnu" class="brxe-block grid-block">
                        <div id="brxe-zstpoa" class="brxe-block">
                            <h2 id="brxe-gqriuj" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                data-animi="up" data-duration="0.6">
                                Home Efficiency Rebate (HER)
                            </h2>
                            <div id="brxe-znxkrm" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-duration="0.6">
                                Enbridge Gas launched the Home Efficiency Rebate program on
                                July 15, 2024. <strong>This program is discontinued and became the Home Renovation Savings Program.</strong>
                            </div>
                        </div>
                        <div id="brxe-shuetv" class="brxe-block" data-animi="up" data-duration="0.6">
                            <div id="brxe-exdnjl" class="brxe-block image-wrapper" data-animi="scale" data-duration="0.6"
                                data-delay="0.2">
                                <img width="709" height="824"
                                    src="<?php echo home_url('/wp-content/uploads/2024/10/embridge-home-efficiency-rebate.png'); ?>"
                                    class="brxe-image image-contain css-filter size-full"
                                    alt="Embridge Home Efficiency Rebate program" id="brxe-tevdpk" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-xqxrcv" class="brxe-section section bricks-lazy-hidden">
            <div id="brxe-lkklqw" class="brxe-container padding-global bricks-lazy-hidden">
                <div id="brxe-tlzhcp" class="brxe-block section-component bricks-lazy-hidden">
                    <div id="brxe-sntpbh" class="brxe-block grid-block bricks-lazy-hidden">
                        <div id="brxe-rjknvb" class="brxe-block image-wrapper bricks-lazy-hidden" data-animi="up"
                            data-duration="0.6">
                            <img width="908" height="983"
                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20908%20983'%3E%3C/svg%3E"
                                class="brxe-image image-cover css-filter size-full bricks-lazy-hidden" alt=""
                                id="brxe-cfjvrk" decoding="async"
                                data-src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?>"
                                data-type="string" data-sizes="(max-width: 908px) 100vw, 908px"
                                data-srcset="<?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?> 908w, <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-277x300.jpg'); ?> 277w, <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-768x831.jpg'); ?> 768w" />
                        </div>
                        <div id="brxe-zlqhlx" class="brxe-block bricks-lazy-hidden">
                            <h2 id="brxe-krotvj" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                data-animi="up" data-duration="0.6">
                                Canada Greener Homes Loan<br>(Discontinued as of October 1, 2025)
                            </h2>
                            <div id="brxe-ayukqg" class="brxe-block hide bricks-lazy-hidden">
                                <h4 id="brxe-flruyb"
                                    class="brxe-heading heading-style-h4 text-color-green font-weight-medium"
                                    data-animi="up" data-duration="0.6">
                                    Annual Limit
                                </h4>
                                <h3 id="brxe-vizhhm" class="brxe-heading heading-style-h3 font-weight-medium"
                                    data-animi="up" data-duration="0.6">
                                    $1200
                                </h3>
                            </div>
                            <div id="brxe-igpbdj" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-duration="0.6">
								<p>
									<strong><em>The last day to apply for a loan is October 1, 2025</em></strong>
								</p>
                                <strong>The Canada Greener Homes Loan offers interest-free loans
                                    between $5,000 and $40,000, with a repayment term of 10 years,
                                    to help Canadians make their homes more energy efficient and
                                    comfortable.</strong><br /><br />The following comes from the Natural Resources Canada
                                Greener Homes Loan program page, on the official Government of
                                Canada website.<br /><a
                                    href="https://natural-resources.canada.ca/energy-efficiency/homes/canada-greener-homes-initiative/canada-greener-homes-loan/24286"><u>For
                                        full details on this program, visit their website.</u></a>
                            </div>
                            <div id="brxe-adieci" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-duration="0.6">
                                <p><span class="is-blue">Loan Eligibility:</span></p>
                                <p></p>
                                <ul>
                                    <li>
                                        You should not start any retrofit work before your loan application has been submitted. Any retrofits started before submitting your loan application are ineligible.
                                    </li>
                                    <br />
                                    <li>
                                        You must be a Canadian citizen, permanent resident, or non-permanent resident who is legally authorized to work in Canada
                                    </li>
                                    <br />
                                    <li>
                                        You must own the home and it must be your primary residence
                                    </li>
                                    <br />
                                    <li>
                                        You have a pre-retrofit evaluation and have not yet had a post-retrofit evaluation
                                    </li>
                                    <br />
                                    <li>
                                        Your pre-retrofit evaluation was completed on or after April 1, 2020
                                    </li>
                                    <br />
                                    <li>
                                        You have not started the retrofits for which you are seeking a loan
                                    </li>
                                    <br />
                                    <li>
                                        You have a good credit history and are not in:<br />
                                        <ul>
                                            <li>a consumer proposal</li>
                                            <li>an orderly payment of debt program</li>
                                            <li>a bankruptcy or equivalent insolvency proceeding</li>
                                        </ul>
                                    </li>
                                </ul>
                                <p><span class="is-blue">Eligible retrofits:</span></p>
                                <ul>
                                    <li>
                                        <strong>Home insulation</strong><br />Upgrade your eligible attic, cathedral ceiling, flat roof, exterior wall, exposed floor, basement and crawl space.
                                    </li>
                                    <br />
                                    <li>
                                        <strong>Air-sealing</strong> Perform air sealing to improve the airtightness of your home to achieve the air-change rate target.
                                    </li>
                                </ul>
                                It is good practice, when safe to do so, to take pictures during retrofits of your home, especially for those renovations that cannot be visually verified by the energy advisor such as:
                                <ul>
                                    <li>
                                        insulation concealed behind siding or covered by drywall
                                    </li>
                                    <li>insulation in a cathedral ceiling</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <h2 id="brxe-vjwkht" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
            <div id="brxe-jafqln" class="brxe-text-basic text-size-regular text-weight-semibold text-weight-semibold"
                data-animi="up" data-duration="0.6">
                $4,000
            </div>
        </h2>
        <section id="brxe-eaeoov" class="brxe-section section">
            <div id="brxe-scffln" class="brxe-container padding-global">
                <div id="brxe-ofqwue" class="brxe-block section-component">
                    <h4 id="brxe-tyurqy" class="brxe-heading heading-style-h4 font-weight-medium" data-animi="up"
                        data-duration="0.6">
                        Resources
                    </h4>
                    <a id="brxe-hwlyfw"
                        href="https://www.homerenovationsavings.ca/"
                        target="_blank" class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                        <div id="brxe-awpsdj" class="brxe-block title-item-acc accordion-title-wrapper">
                            <h6 id="brxe-kgvjxc" class="brxe-heading heading-style-h6 font-weight-medium">
                                More on the Home Renovation Savings Program
                            </h6>
                            <svg class="brxe-svg" id="brxe-daosaf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                viewBox="0 0 25 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                    fill="#0F172A"></path>
                            </svg>
                        </div>
                    </a>
                    <a id="brxe-hwlyfw" href="https://natural-resources.canada.ca/energy-efficiency/home-energy-efficiency/canada-greener-homes-initiative/canada-greener-homes-loan" target="_blank" class="brxe-block" data-animi="up"
                        data-duration="0.6" data-delay="0.4">
                        <div id="brxe-awpsdj" class="brxe-block title-item-acc accordion-title-wrapper">
                            <h6 id="brxe-kgvjxc" class="brxe-heading heading-style-h6 font-weight-medium">
                                More on the Canada Greener Homes Loan
                            </h6>
                            <svg class="brxe-svg" id="brxe-daosaf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                viewBox="0 0 25 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                    fill="#0F172A"></path>
                            </svg>
                        </div>
                    </a>
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
            <div id="brxe-bggdwc" class="brxe-block section-component">
                <div id="brxe-akzgbv" class="brxe-block">
                    <div id="brxe-dckkyl" class="brxe-block">
                        <h2 id="brxe-cfdbrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                            data-animi="up" data-duration="0.6">
                            Get a quote
                        </h2>
                        <div id="brxe-sushnb" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                            data-delay="0.2" data-duration="0.6">
                            Ready to start your insulation project? Get a free quote from your
                            local Koala Insulation team today.
                        </div>
                        <div id="brxe-dbvghn" class="brxe-div btn is-no-icon"
                            data-interactions='[{"id":"pigxzw","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                            data-interaction-id="9f6f9b">
                            <div id="brxe-efxxnv" class="brxe-text-basic">
                                Get a Free Estimate
                            </div>
                        </div>
                    </div>
                    <div id="brxe-ltloqb" class="brxe-div">
                        <img width="296" height="143"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-bdqbqz" decoding="async"
                            loading="lazy" data-type="string" />
                    </div>
                    <div id="brxe-vjmidr" class="brxe-div">
                        <img width="560" height="352"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                            class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-kuxsvn"
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