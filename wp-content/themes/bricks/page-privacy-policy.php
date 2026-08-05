<?php
/* Template Name: Privacy Policy */
get_header();

// Get the current URL path
$current_url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Split the URL into segments
$segments = explode('/', $current_url);

// Determine if the URL is location-specific or generic
if (count($segments) === 3 && $segments[2] === 'privacy-policy') {

  $location_slug = sanitize_title($segments[1]);
  $location_post = get_page_by_path($location_slug, OBJECT, 'location');

  if (!$location_post) {
    // True 404 if the location doesn't exist
    status_header(404);
    get_template_part('404');
    get_footer();
    exit;
  }

  // ✅ Tell WP this is a real page and use the location as the queried object
  global $wp_query, $post;
  if ($wp_query) {
    $wp_query->is_404 = false;
    $wp_query->is_page = true;
    $wp_query->queried_object    = $location_post;
    $wp_query->queried_object_id = $location_post->ID;
  }
  $post = $location_post;
  setup_postdata($post);
  status_header(200);
  nocache_headers();

  show_location_page($location_slug);

  wp_reset_postdata();
  get_footer();
  exit;

} elseif (count($segments) === 2 && $segments[1] === 'privacy-policy') {

  // Generic privacy policy page (e.g., /privacy-policy)
  global $wp_query;
  if ($wp_query) {
    $wp_query->is_404 = false;
    $wp_query->is_page = true;
  }
  status_header(200);
  nocache_headers();

  render_fallback_content();

  get_footer();
  exit;

} else {
  // Invalid or unexpected URL structure → real 404
  status_header(404);
  get_template_part('404');
  get_footer();
  exit;
}

get_footer();

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

  $location_title = get_the_title($location_id);
  ?>
  <main id="brx-content">
    <section id="brxe-kikkzc" class="brxe-section section">
      <div id="brxe-wtsixe" class="brxe-container padding-global">
        <div id="brxe-isdpcy" class="brxe-block">
          <div id="brxe-hlomay" class="brxe-block section-component">
            <div id="brxe-osvhli" class="brxe-block">
              <div id="brxe-rvzcqy" class="brxe-block">
                <h2 id="brxe-qbsfxd" class="brxe-heading heading-style-h2 is-blue" data-animi="up" data-duration="0.6"
                  data-delay="0.2">
                  Privacy Policy of <?php echo $location_title ?>
                </h2>
              </div>
              <div id="brxe-dllemy" class="brxe-text rich-text" data-animi="up" data-delay="0.3" data-duration="0.6">
                <div class="terms_grid">
                  <h3>1. Categories of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Carbon Ceiling, LLC is the owner and operator of this
                    website as well as the owner of Koala Insulation trademarks
                    and associated identifiers. Carbon Ceiling, LLC does not
                    operate any insulation businesses. All insulation services are
                    provided by independently owned and operated licensees or
                    franchisees of the Koala Insulation brand under agreements
                    governing such licenses and/or franchises. All information
                    collected will be retained by Carbon Ceiling, LLC and passed
                    on to the appropriate franchisee or licensee or related
                    company (collectively, “Affiliate”). Information provided to
                    Affiliates may also be provided to Carbon Ceiling, LLC and
                    will be retained in accordance with this policy and applicable
                    law.
                  </p>
                  <p>
                    (b) Information You Provide to Us. When you place an order,
                    open an account, request a quote, participate in a
                    questionnaire, communicate with customer service or send us an
                    email, you provide us with information that we collect. Such
                    information may include your name, address, phone number,
                    credit card information, home or building information and
                    purchase history.
                  </p>
                  <p>
                    (c) Other Information We Receive and Store. We also receive
                    information from you as a result of your visiting our site,
                    interacting with our webpage or its links, sending us emails
                    including your IP address, your home or building data, your
                    email address, your browser type and version, your operating
                    system and platform, your purchase history, products you view,
                    products you search for, length of visits to pages and other
                    browser interaction information.
                  </p>
                  <p>
                    (d) All information provided by you to Carbon Ceiling or any
                    Affiliate is the property of the receiving party and/or Carbon
                    Ceiling as governed by agreements between the Affiliate and
                    Carbon Ceiling. Once you voluntarily provide information to
                    us, we may retain it indefinitely and use it in the course of
                    our business.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>2. Use and Disclosure of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Use and Disclosure of Information. We will use and
                    disclose your information as follows: (1) to permit you to
                    review information online; (2) to analyze your needs and
                    eligibility for certain products or services that we or our
                    affiliates may offer; (3) to solicit you or subsequent owners
                    of your building or home for these services; (4) to ensure a
                    high quality customer experience; (5) to assist you with your
                    requested services; (6) to confirm and ship your orders or
                    schedule your estimate or install; (7) to follow up to make
                    sure such orders are fulfilled; (8) if, in the course of
                    placing an order and/or requesting information, you opt to
                    receive occasional emails from us notifying you of special
                    offers, we will use such information to notify you of such
                    offers; (9) if, in the course of placing an order and/or
                    entering a contest, you opt to receive occasional emails from
                    third parties to whom we provide your information, we will
                    disclose such information to such third parties; (10) to
                    protect the rights and safety of us, our shareholders,
                    members, officers, employees and customers; (11) at our
                    option, to notify you of any changes to this Privacy Policy or
                    our other terms and conditions of this website (you are bound
                    by the terms of any changes we post to our website whether or
                    not we notify you by email) and (12) as otherwise required by
                    law. We disclose your information to third parties only as
                    reasonably required to fulfill your orders and collect sums
                    due us (such as to shippers and credit card processors), to
                    protect the rights and safety of us, our shareholders,
                    members, employees and customers (such as to legal
                    representatives and law enforcement), as you expressly permit,
                    and as required by law (such as the result of a court
                    subpoena). We may transfer your information in the event of
                    the sale of substantially all of the assets of our business to
                    a third-party or in the event of a merger, consolidation or
                    acquisition. However, in such event, any acquirer will be
                    subject to the provisions of our commitments to you.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>3. Do Not Track.</h3>
                  <p></p>
                  <p>
                    We do not monitor, recognize, or respond to any opt-out or do
                    not track mechanisms, including general web browser “Do Not
                    Track” settings and/or signals. Further, we do not authorize
                    third parties to collect any personally identifiable
                    information about individuals who visit our site without
                    separate consent.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>4. Notification of Changes.</h3>
                  <p></p>
                  <p>
                    (a) Notification of Changes. Any changes in our Privacy Policy
                    will be posted to this website and will become effective as of
                    the date of posting with respect to information we then
                    collect in the future, but will not be changed with respect to
                    information that we have then already collected. It is and
                    will be your responsibility to review our Privacy Policy from
                    time to time to make sure you are aware of any changes.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>5. Effective Date.</h3>
                  <p></p>
                  <p>
                    (a) Effective Date. This Privacy Policy is effective with
                    respect to all data that we have collected since the date we
                    started collecting data, which was January 1, 2019.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>6. Questions.</h3>
                  <p></p>
                  <p>
                    (a) Questions. If you have any questions about our Privacy
                    Policy, feel free to contact us by sending a letter to: Carbon
                    Ceiling, LLC ATTN: Privacy Manager, 478 N. Babcock Street,
                    Melbourne, FL 32935.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>7. Limitation of Liability and Choice of Venue.</h3>
                  <p></p>
                  <p>
                    Under no circumstances shall the liability of any user
                    associated with this website exceed the amount the user has
                    paid to the website owner. Any dispute arising out of or
                    relating to this policy or any use or viewing of this website
                    shall be brought through arbitration in Brevard County,
                    Florida. In the event that this agreement to arbitrate is not
                    upheld, any court proceedings shall be in Brevard County
                    Florida and each user irrevocably consents and waives any
                    objections including without limitation objections to this
                    venue for forum non conveniens, in the courts having
                    jurisdiction over Brevard County Florida.
                  </p>
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
              <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
                data-delay="0.2" data-duration="0.6">
                Find Your Location
              </h2>
              <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up" data-duration="0.6"
                data-delay="0.3">
                Ready to Improve Your Insulation?
              </div>
              <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                data-duration="0.6" data-delay="0.3">
                Whether it's spray foam insulation, blown-in insulation, or
                anything in between, we're here to help.
              </div>
              <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                data-duration="0.6">
                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                  <input type="text" id="my-zipcode-input" class="top-zipcode-input" placeholder="Zip or Postal Code" />
                </div>
                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                  <div id="brxe-smmtik" class="brxe-div">
                    <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg" width="18"
                      height="18" viewBox="0 0 18 18" fill="none">
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
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug" decoding="async"
                data-type="string" />
            </div>
            <div id="brxe-wowmun" class="brxe-div">
              <img width="560" height="352"
                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf" decoding="async"
                data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
              " />
            </div>
            <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
              data-delay="1">
              <img width="440" height="410"
                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy" decoding="async"
                data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
              " />
            </div>
          </div>
        </div>
        <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6" data-delay="0.4">
          <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
            viewBox="0 0 62 62" fill="none">
            <g clip-path="url(#clip0_6431_678)">
              <path
                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                fill="#95C93D"></path>
            </g>
            <defs>
              <clipPath id="clip0_6431_678">
                <rect width="44" height="44" fill="white" transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
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
            <h2 id="brxe-olzjbf" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
              data-delay="0.2" data-duration="0.6">
              Get a quote
            </h2>
            <div id="brxe-cvthkj" class="brxe-text-basic text-size-regular text-weight-semibold" text-split=""
              lines-slide-up="">
              Ready to get started with improving your home’s comfort? Get in
              touch with us for a quote and let’s discuss how we can help you
              achieve a more energy-efficient and cozy home.
            </div>
            <div id="brxe-hmrpll" class="brxe-div btn is-no-icon" data-animi="up" data-delay="0.4" data-duration="0.6"
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
              class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-ozalhe" decoding="async"
              loading="lazy" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
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
    <section id="brxe-kikkzc" class="brxe-section section">
      <div id="brxe-wtsixe" class="brxe-container padding-global">
        <div id="brxe-isdpcy" class="brxe-block">
          <div id="brxe-hlomay" class="brxe-block section-component">
            <div id="brxe-osvhli" class="brxe-block">
              <div id="brxe-rvzcqy" class="brxe-block">
                <h2 id="brxe-qbsfxd" class="brxe-heading heading-style-h2 is-blue" data-animi="up" data-duration="0.6"
                  data-delay="0.2">
                  Privacy Policy CA
                </h2>
              </div>
              <div id="brxe-dllemy" class="brxe-text rich-text" data-animi="up" data-delay="0.3" data-duration="0.6">
                <div class="terms_grid">
                  <h3>1. Categories of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Carbon Ceiling, LLC is the owner and operator of this
                    website as well as the owner of Koala Insulation trademarks
                    and associated identifiers. Carbon Ceiling, LLC does not
                    operate any insulation businesses. All insulation services are
                    provided by independently owned and operated licensees or
                    franchisees of the Koala Insulation brand under agreements
                    governing such licenses and/or franchises. All information
                    collected will be retained by Carbon Ceiling, LLC and passed
                    on to the appropriate franchisee or licensee or related
                    company (collectively, “Affiliate”). Information provided to
                    Affiliates may also be provided to Carbon Ceiling, LLC and
                    will be retained in accordance with this policy and applicable
                    law.
                  </p>
                  <p>
                    (b) Information You Provide to Us. When you place an order,
                    open an account, request a quote, participate in a
                    questionnaire, communicate with customer service or send us an
                    email, you provide us with information that we collect. Such
                    information may include your name, address, phone number,
                    credit card information, home or building information and
                    purchase history.
                  </p>
                  <p>
                    (c) Other Information We Receive and Store. We also receive
                    information from you as a result of your visiting our site,
                    interacting with our webpage or its links, sending us emails
                    including your IP address, your home or building data, your
                    email address, your browser type and version, your operating
                    system and platform, your purchase history, products you view,
                    products you search for, length of visits to pages and other
                    browser interaction information.
                  </p>
                  <p>
                    (d) All information provided by you to Carbon Ceiling or any
                    Affiliate is the property of the receiving party and/or Carbon
                    Ceiling as governed by agreements between the Affiliate and
                    Carbon Ceiling. Once you voluntarily provide information to
                    us, we may retain it indefinitely and use it in the course of
                    our business.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>2. Use and Disclosure of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Use and Disclosure of Information. We will use and
                    disclose your information as follows: (1) to permit you to
                    review information online; (2) to analyze your needs and
                    eligibility for certain products or services that we or our
                    affiliates may offer; (3) to solicit you or subsequent owners
                    of your building or home for these services; (4) to ensure a
                    high quality customer experience; (5) to assist you with your
                    requested services; (6) to confirm and ship your orders or
                    schedule your estimate or install; (7) to follow up to make
                    sure such orders are fulfilled; (8) if, in the course of
                    placing an order and/or requesting information, you opt to
                    receive occasional emails from us notifying you of special
                    offers, we will use such information to notify you of such
                    offers; (9) if, in the course of placing an order and/or
                    entering a contest, you opt to receive occasional emails from
                    third parties to whom we provide your information, we will
                    disclose such information to such third parties; (10) to
                    protect the rights and safety of us, our shareholders,
                    members, officers, employees and customers; (11) at our
                    option, to notify you of any changes to this Privacy Policy or
                    our other terms and conditions of this website (you are bound
                    by the terms of any changes we post to our website whether or
                    not we notify you by email) and (12) as otherwise required by
                    law. We disclose your information to third parties only as
                    reasonably required to fulfill your orders and collect sums
                    due us (such as to shippers and credit card processors), to
                    protect the rights and safety of us, our shareholders,
                    members, employees and customers (such as to legal
                    representatives and law enforcement), as you expressly permit,
                    and as required by law (such as the result of a court
                    subpoena). We may transfer your information in the event of
                    the sale of substantially all of the assets of our business to
                    a third-party or in the event of a merger, consolidation or
                    acquisition. However, in such event, any acquirer will be
                    subject to the provisions of our commitments to you.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>3. Do Not Track.</h3>
                  <p></p>
                  <p>
                    We do not monitor, recognize, or respond to any opt-out or do
                    not track mechanisms, including general web browser “Do Not
                    Track” settings and/or signals. Further, we do not authorize
                    third parties to collect any personally identifiable
                    information about individuals who visit our site without
                    separate consent.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>4. Notification of Changes.</h3>
                  <p></p>
                  <p>
                    (a) Notification of Changes. Any changes in our Privacy Policy
                    will be posted to this website and will become effective as of
                    the date of posting with respect to information we then
                    collect in the future, but will not be changed with respect to
                    information that we have then already collected. It is and
                    will be your responsibility to review our Privacy Policy from
                    time to time to make sure you are aware of any changes.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>5. Effective Date.</h3>
                  <p></p>
                  <p>
                    (a) Effective Date. This Privacy Policy is effective with
                    respect to all data that we have collected since the date we
                    started collecting data, which was January 1, 2019.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>6. Questions.</h3>
                  <p></p>
                  <p>
                    (a) Questions. If you have any questions about our Privacy
                    Policy, feel free to contact us by sending a letter to: Carbon
                    Ceiling, LLC ATTN: Privacy Manager, 478 N. Babcock Street,
                    Melbourne, FL 32935.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>7. Limitation of Liability and Choice of Venue.</h3>
                  <p></p>
                  <p>
                    Under no circumstances shall the liability of any user
                    associated with this website exceed the amount the user has
                    paid to the website owner. Any dispute arising out of or
                    relating to this policy or any use or viewing of this website
                    shall be brought through arbitration in Brevard County,
                    Florida. In the event that this agreement to arbitrate is not
                    upheld, any court proceedings shall be in Brevard County
                    Florida and each user irrevocably consents and waives any
                    objections including without limitation objections to this
                    venue for forum non conveniens, in the courts having
                    jurisdiction over Brevard County Florida.
                  </p>
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
              <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
                data-delay="0.2" data-duration="0.6">
                Find Your Location
              </h2>
              <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up" data-duration="0.6"
                data-delay="0.3">
                Ready to Improve Your Insulation?
              </div>
              <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                data-duration="0.6" data-delay="0.3">
                Whether it's spray foam insulation, blown-in insulation, or
                anything in between, we're here to help.
              </div>
              <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                data-duration="0.6">
                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                  <input type="text" id="my-zipcode-input" class="top-zipcode-input" placeholder="Zip or Postal Code" />
                </div>
                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                  <div id="brxe-smmtik" class="brxe-div">
                    <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg" width="18"
                      height="18" viewBox="0 0 18 18" fill="none">
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
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug" decoding="async"
                data-type="string" />
            </div>
            <div id="brxe-wowmun" class="brxe-div">
              <img width="560" height="352"
                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf" decoding="async"
                data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
              " />
            </div>
            <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
              data-delay="1">
              <img width="440" height="410"
                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy" decoding="async"
                data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
              " />
            </div>
          </div>
        </div>
        <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6" data-delay="0.4">
          <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
            viewBox="0 0 62 62" fill="none">
            <g clip-path="url(#clip0_6431_678)">
              <path
                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                fill="#95C93D"></path>
            </g>
            <defs>
              <clipPath id="clip0_6431_678">
                <rect width="44" height="44" fill="white" transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
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
            <h2 id="brxe-olzjbf" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
              data-delay="0.2" data-duration="0.6">
              Get a quote
            </h2>
            <div id="brxe-cvthkj" class="brxe-text-basic text-size-regular text-weight-semibold" text-split=""
              lines-slide-up="">
              Ready to get started with improving your home’s comfort? Get in
              touch with us for a quote and let’s discuss how we can help you
              achieve a more energy-efficient and cozy home.
            </div>
            <div id="brxe-hmrpll" class="brxe-div btn is-no-icon" data-animi="up" data-delay="0.4" data-duration="0.6"
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
              class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-ozalhe" decoding="async"
              loading="lazy" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
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