<?php
/**
 * Add your Google Maps JavaScript API key here.
 */
if (!defined('ZIP_SHAPE_MAPS_API_KEY')) {
    define('ZIP_SHAPE_MAPS_API_KEY', 'AIzaSyBOBCV9KYqqwo8CRYhBbHfjBp5Jea72XQk');
}

/**
 * Shortcode:
 *
 * [zip_shape_map]
 *
 * Optional:
 * [zip_shape_map post_id="123"]
 * [zip_shape_map height="500px" zoom="10"]
 * [zip_shape_map lat="43.190950" lng="-89.370207" zipcodes="53532,53571,53590"]
 **/
add_shortcode('zip_shape_map', 'zip_shape_map_render_shortcode');

function zip_shape_map_render_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id'  => '',
        'lat'      => '',
        'lng'      => '',
        'zipcodes' => '',
        'height'   => '650px',
        'zoom'     => '10',
    ), $atts, 'zip_shape_map');

    $post_id = !empty($atts['post_id']) ? absint($atts['post_id']) : get_the_ID();

    if (!$post_id) {
        $post_id = get_queried_object_id();
    }

    $lat = $atts['lat'] !== ''
        ? $atts['lat']
        : get_post_meta($post_id, 'location_latitude', true);

    /**
     * This reads your current field spelling:
     * location_logitude
     */
    $lng = $atts['lng'] !== ''
        ? $atts['lng']
        : get_post_meta($post_id, 'location_logitude', true);

    /**
     * Fallback in case you later rename the field to location_longitude.
     */
    if ($lng === '') {
        $lng = get_post_meta($post_id, 'location_longitude', true);
    }

    $zipcodes_raw = $atts['zipcodes'] !== ''
        ? $atts['zipcodes']
        : get_post_meta($post_id, 'additional_zipcodes', true);

    $location_address = get_post_meta($post_id, 'location_address', true);
    $show_pin = zip_shape_map_address_has_street($location_address);

    $lat = is_numeric($lat) ? (float) $lat : null;
    $lng = is_numeric($lng) ? (float) $lng : null;

    $zipcodes = zip_shape_map_parse_zipcodes($zipcodes_raw);

    if ($lat === null || $lng === null || empty($zipcodes)) {
        return '<p>Map data is missing.</p>';
    }

    zip_shape_map_enqueue_assets();

    static $instance = 0;
    $instance++;

    $map_id = 'zip-shape-map-' . $instance;

    $height = preg_match('/^\d+(px|rem|em|vh|vw|%)$/', $atts['height'])
        ? $atts['height']
        : '650px';

    $zoom = absint($atts['zoom']);

    if (!$zoom) {
        $zoom = 10;
    }

    $config = array(
        'id'       => $map_id,
        'lat'      => $lat,
        'lng'      => $lng,
        'zipcodes' => $zipcodes,
        'zoom'     => $zoom,
        'showPin'  => $show_pin,
    );

    ob_start();
    ?>

    <div
        id="<?php echo esc_attr($map_id); ?>"
        class="zip-shape-map"
        style="width:100%;height:<?php echo esc_attr($height); ?>;border-radius:12px;overflow:hidden;"
    ></div>

    <script>
        window.zipShapeMapConfigs = window.zipShapeMapConfigs || [];
        window.zipShapeMapConfigs.push(<?php echo wp_json_encode($config); ?>);
    </script>

    <?php
    return ob_get_clean();
}

function zip_shape_map_address_has_street($address) {
    $address = trim(wp_strip_all_tags((string) $address));

    if ($address === '') {
        return false;
    }

    return (bool) preg_match(
        '/\b\d{1,6}\s+[\p{L}0-9][\p{L}0-9\s.\'#-]*\s+(street|st\.?|road|rd\.?|avenue|ave\.?|court|ct\.?|drive|dr\.?|lane|ln\.?|boulevard|blvd\.?|way|place|pl\.?|circle|cir\.?|terrace|ter\.?|trail|trl\.?|parkway|pkwy\.?|highway|hwy\.?|route|rte\.?|square|sq\.?|crescent|cres\.?|close|gate|gardens|grove|heights|landing|loop|mews|path|row|run|view|walk|line|concession|sideroad|side\s+road|rue|chemin|ch\.?|rang|autoroute)\b/iu',
        $address
    );
}

function zip_shape_map_parse_zipcodes($zipcodes_raw) {
    $input = strtoupper((string) $zipcodes_raw);

    $codes = array();

    // U.S. ZIP codes, with optional ZIP+4 support.
    preg_match_all('/\b\d{5}(?:-\d{4})?\b/', $input, $us_matches);

    foreach ($us_matches[0] as $zip) {
        $zip = substr($zip, 0, 5);
        $codes[$zip] = $zip;
    }

    // Full Canadian postal codes, converted to FSA.
    // Example: N6A 1A1 becomes N6A.
    preg_match_all(
        '/\b([ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z])\s?\d[ABCEGHJ-NPRSTV-Z]\d\b/i',
        $input,
        $ca_full_matches
    );

    foreach ($ca_full_matches[1] as $fsa) {
        $fsa = strtoupper($fsa);
        $codes[$fsa] = $fsa;
    }

    // Direct FSA support.
    // Example: N6A
    preg_match_all(
        '/\b([ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z])\b/i',
        $input,
        $ca_fsa_matches
    );

    foreach ($ca_fsa_matches[1] as $fsa) {
        $fsa = strtoupper($fsa);
        $codes[$fsa] = $fsa;
    }

    return array_values($codes);
}

function zip_shape_map_split_boundary_codes($codes) {
    $split = array(
        'us' => array(),
        'ca' => array(),
    );

    foreach ($codes as $code) {
        $code = strtoupper(trim((string) $code));

        if (preg_match('/^\d{5}$/', $code)) {
            $split['us'][] = $code;
        } elseif (preg_match('/^[ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z]$/i', $code)) {
            $split['ca'][] = $code;
        }
    }

    $split['us'] = array_values(array_unique($split['us']));
    $split['ca'] = array_values(array_unique($split['ca']));

    return $split;
}

function zip_shape_map_sql_list($codes) {
    return implode(',', array_map(function ($code) {
        return "'" . str_replace("'", "''", $code) . "'";
    }, $codes));
}

function zip_shape_map_fetch_geojson($url) {
    $response = wp_remote_get($url, array(
        'timeout' => 20,
    ));

    if (is_wp_error($response)) {
        return new WP_Error(
            'boundary_request_failed',
            'Could not load boundary data.',
            array('status' => 500)
        );
    }

    $body = wp_remote_retrieve_body($response);
    $geojson = json_decode($body, true);

    if (!is_array($geojson) || empty($geojson['features'])) {
        return new WP_Error(
            'empty_boundaries',
            'No boundaries found.',
            array('status' => 404)
        );
    }

    return $geojson;
}

function zip_shape_map_enqueue_assets() {
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;

    wp_enqueue_script(
        'turf-js',
        'https://cdn.jsdelivr.net/npm/@turf/turf@7/turf.min.js',
        array(),
        '7',
        true
    );

    wp_add_inline_script(
        'turf-js',
        'window.zipShapeMapRestUrl = ' . wp_json_encode(rest_url('zip-shape-map/v1/boundaries')) . ';',
        'before'
    );

    $inline_js = <<<'JS'
window.initZipShapeMaps = async function () {
    const configs = window.zipShapeMapConfigs || [];

    for (const config of configs) {
        await window.renderOneZipShapeMap(config);
    }
};

window.renderOneZipShapeMap = async function (config) {
    const el = document.getElementById(config.id);

    if (!el) {
        return;
    }

    const pin = {
        lat: Number(config.lat),
        lng: Number(config.lng)
    };

    const map = new google.maps.Map(el, {
        center: pin,
        zoom: Number(config.zoom) || 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true,
        gestureHandling: 'none',
        zoomControl: false,
        keyboardShortcuts: false,
        clickableIcons: false,
        streetViewControl: false,
        fullscreenControl: false,
        mapTypeControl: false,
        scaleControl: false,
        rotateControl: false,
        styles: [
          { elementType: "geometry", stylers: [{ color: "#9ec7ba" }] },
          { elementType: "labels.icon", stylers: [{ visibility: "off" }] },
          { elementType: "labels.text.fill", stylers: [{ color: "#fcfcff" }] },
          { elementType: "labels.text.stroke", stylers: [{ visibility: "off" }] },
          {
            featureType: "administrative",
            elementType: "geometry",
            stylers: [{ color: "#9ec7ba" }],
          },
          {
            featureType: "administrative.country",
            elementType: "labels.text.fill",
            stylers: [{ visibility: "off" }],
          },
          {
            featureType: "administrative.land_parcel",
            stylers: [{ visibility: "off" }],
          },
          {
            featureType: "administrative.locality",
            elementType: "labels.text.fill",
            stylers: [{ color: "#fcfcff" }],
          },
          {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{ color: "#fcfcff" }],
          },
          {
            featureType: "poi.park",
            elementType: "geometry",
            stylers: [{ color: "#99a9bb" }],
          },
          {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{ color: "#fcfcff" }],
          },
          {
            featureType: "road",
            elementType: "geometry.fill",
            stylers: [{ color: "#7EB4A3" }],
          },
          {
            featureType: "road.arterial",
            elementType: "geometry",
            stylers: [{ color: "#7EB4A3" }],
          },
          {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{ color: "#7EB4A3" }],
          },
          {
            featureType: "road.highway.controlled_access",
            elementType: "geometry",
            stylers: [{ color: "#7EB4A3" }],
          },
          {
            featureType: "transit",
            elementType: "labels.text.fill",
            stylers: [{ color: "#fcfcff" }],
          },
          {
            featureType: "water",
            elementType: "geometry",
            stylers: [{ color: "#7EB4A3" }],
          },
        ],
    });

    if (config.showPin) {
        new google.maps.Marker({
            map: map,
            position: pin,
            icon: "/wp-content/uploads/2024/09/map-pin.svg",
        });
    }

    if (!Array.isArray(config.zipcodes) || !config.zipcodes.length) {
        return;
    }

    try {
        let shape = await window.getCachedZipShape(config.zipcodes);

        if (window.turf && typeof turf.polygonSmooth === 'function') {
            const smoothedShape = turf.polygonSmooth(shape, {
                iterations: 3
            });

            if (smoothedShape) {
                shape = smoothedShape;
            }
        }

        map.data.addGeoJson(shape);

        map.data.setStyle({ //#1D6662
            fillColor: '#1D6662',
            fillOpacity: 0,
            strokeColor: '#1D6662',
            strokeWeight: 3,
            clickable: false
        });

        const bounds = new google.maps.LatLngBounds();

        map.data.forEach(function (feature) {
            feature.getGeometry().forEachLatLng(function (latLng) {
                bounds.extend(latLng);
            });
        });

        if (config.showPin) {
            bounds.extend(pin);
        }

        map.fitBounds(bounds);

    } catch (error) {
        console.error('ZIP boundary map error:', error);
    }
};

window.getCachedZipShape = async function (zipcodes) {
    const sortedZips = [...zipcodes].sort();
    const browserCacheKey = 'zipShapeMapRoundedV2:' + sortedZips.join(',');

    const cached = localStorage.getItem(browserCacheKey);

    if (cached) {
        try {
            return JSON.parse(cached);
        } catch (error) {
            localStorage.removeItem(browserCacheKey);
        }
    }

    const restUrl = window.zipShapeMapRestUrl + '?zips=' + encodeURIComponent(sortedZips.join(','));

    const response = await fetch(restUrl);

    const geoJson = await response.json();

    let shape = geoJson;

    if (window.turf && geoJson.features && geoJson.features.length) {
        const merged = turf.union(turf.featureCollection(geoJson.features));

        if (merged) {
            shape = merged;
        }
    }

    try {
        localStorage.setItem(browserCacheKey, JSON.stringify(shape));
    } catch (error) {
        console.warn('Could not save ZIP shape to browser cache.', error);
    }

    return shape;
};
JS;

    wp_add_inline_script('turf-js', $inline_js, 'after');

    wp_enqueue_script(
        'google-maps-js',
        'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode(ZIP_SHAPE_MAPS_API_KEY) . '&callback=initZipShapeMaps&v=weekly',
        array('turf-js'),
        null,
        true
    );
}

/**
 * REST endpoint for cached ZIP/ZCTA boundary data.
 */
add_action('rest_api_init', function () {
    register_rest_route('zip-shape-map/v1', '/boundaries', array(
        'methods'             => 'GET',
        'callback'            => 'zip_shape_map_get_boundaries',
        'permission_callback' => '__return_true',
    ));
});

function zip_shape_map_get_boundaries(WP_REST_Request $request) {
    $zipcodes_raw = $request->get_param('zips');
    $codes = zip_shape_map_parse_zipcodes($zipcodes_raw);

    if (empty($codes)) {
        return new WP_Error(
            'missing_zipcodes',
            'No ZIP codes or postal codes provided.',
            array('status' => 400)
        );
    }

    sort($codes);

    $cache_key = 'zip_shape_' . md5(implode(',', $codes));
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return rest_ensure_response($cached);
    }

    $split = zip_shape_map_split_boundary_codes($codes);
    $features = array();

    // U.S. ZIP/ZCTA boundaries.
    if (!empty($split['us'])) {
        $where = 'GEOID IN (' . zip_shape_map_sql_list($split['us']) . ')';

        $url = add_query_arg(array(
            'f'              => 'geojson',
            'where'          => $where,
            'outFields'      => 'GEOID,BASENAME',
            'returnGeometry' => 'true',
            'outSR'          => '4326',
        ), 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb/tigerWMS_Census2020/MapServer/84/query');

        $geojson = zip_shape_map_fetch_geojson($url);

        if (is_wp_error($geojson)) {
            return $geojson;
        }

        $features = array_merge($features, $geojson['features']);
    }

    // Canadian FSA boundaries.
    if (!empty($split['ca'])) {
        $where = 'CFSAUID IN (' . zip_shape_map_sql_list($split['ca']) . ')';

        $url = add_query_arg(array(
            'f'              => 'geojson',
            'where'          => $where,
            'outFields'      => 'CFSAUID,PRNAME',
            'returnGeometry' => 'true',
            'outSR'          => '4326',
        ), 'https://geo.statcan.gc.ca/geo_wa/rest/services/2021/Digital_boundary_files/MapServer/14/query');

        $geojson = zip_shape_map_fetch_geojson($url);

        if (is_wp_error($geojson)) {
            return $geojson;
        }

        $features = array_merge($features, $geojson['features']);
    }

    if (empty($features)) {
        return new WP_Error(
            'empty_boundaries',
            'No boundaries found.',
            array('status' => 404)
        );
    }

    $combined_geojson = array(
        'type'     => 'FeatureCollection',
        'features' => $features,
    );

    set_transient($cache_key, $combined_geojson, 30 * DAY_IN_SECONDS);

    return rest_ensure_response($combined_geojson);
}