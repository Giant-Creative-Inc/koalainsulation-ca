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
        class="zip-shape-map-shell"
        style="position:relative;width:100%;height:<?php echo esc_attr($height); ?>;border-radius:12px;overflow:hidden;background:#9ec7ba;"
    >
        <div
            id="<?php echo esc_attr($map_id); ?>"
            class="zip-shape-map"
            style="width:100%;height:100%;"
            aria-label="Service area map"
        ></div>
        <button
            type="button"
            class="zip-shape-map-placeholder"
            data-zip-shape-map-load
            aria-controls="<?php echo esc_attr($map_id); ?>"
            style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:24px;border:0;background:#9ec7ba;color:#fff;font:inherit;font-weight:600;text-align:center;cursor:pointer;"
        >
            Load service area map
        </button>
    </div>

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

    $loader_path = get_template_directory() . '/assets/js/custom/zip-shape-map.js';
    $loader_version = file_exists($loader_path) ? filemtime($loader_path) : null;

    wp_enqueue_script(
        'zip-shape-map-loader',
        get_template_directory_uri() . '/assets/js/custom/zip-shape-map.js',
        array(),
        $loader_version,
        true
    );

    wp_add_inline_script(
        'zip-shape-map-loader',
        'window.zipShapeMapRestUrl = ' . wp_json_encode(rest_url('zip-shape-map/v1/boundaries')) . ';' .
        'window.zipShapeMapApiKey = ' . wp_json_encode(ZIP_SHAPE_MAPS_API_KEY) . ';',
        'before'
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

function zip_shape_map_prepare_boundary_geojson($features) {
    $prepared = array();

    foreach ((array) $features as $feature) {
        if (empty($feature['geometry']['type']) || empty($feature['geometry']['coordinates'])) {
            continue;
        }

        $prepared[] = array(
            'type'       => 'Feature',
            'properties' => new stdClass(),
            'geometry'   => array(
                'type'        => $feature['geometry']['type'],
                'coordinates' => $feature['geometry']['coordinates'],
            ),
        );
    }

    return array(
        'type'     => 'FeatureCollection',
        'features' => $prepared,
    );
}

function zip_shape_map_dissolve_boundary_geojson($features) {
    $geometries = array();

    foreach ((array) $features as $feature) {
        $geometry = $feature['geometry'] ?? array();
        $type = $geometry['type'] ?? '';
        $coordinates = $geometry['coordinates'] ?? array();

        if ($type === 'Polygon' && !empty($coordinates)) {
            $geometries[] = array('rings' => $coordinates);
        } elseif ($type === 'MultiPolygon') {
            foreach ($coordinates as $polygon) {
                if (!empty($polygon)) {
                    $geometries[] = array('rings' => $polygon);
                }
            }
        }
    }

    if (count($geometries) < 2) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    $response = wp_remote_post(
        'https://sampleserver6.arcgisonline.com/arcgis/rest/services/Utilities/Geometry/GeometryServer/union',
        array(
            'timeout' => 30,
            'body'    => array(
                'f'          => 'json',
                'sr'         => '4326',
                'geometries' => wp_json_encode(array(
                    'geometryType' => 'esriGeometryPolygon',
                    'geometries'   => $geometries,
                )),
            ),
        )
    );

    if (is_wp_error($response)) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    $union = json_decode(wp_remote_retrieve_body($response), true);
    $rings = $union['geometry']['rings'] ?? array();

    if (empty($rings)) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    $polygons = array();

    foreach ($rings as $ring) {
        if (zip_shape_map_ring_area($ring) < 0) {
            $polygons[] = array($ring);
        }
    }

    foreach ($rings as $ring) {
        if (zip_shape_map_ring_area($ring) < 0) {
            continue;
        }

        $assigned = false;

        foreach ($polygons as &$polygon) {
            if (zip_shape_map_point_in_ring($ring[0], $polygon[0])) {
                $polygon[] = $ring;
                $assigned = true;
                break;
            }
        }
        unset($polygon);

        if (!$assigned) {
            $polygons[] = array(array_reverse($ring));
        }
    }

    if (empty($polygons)) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    return array(
        'type'     => 'FeatureCollection',
        'features' => array(array(
            'type'       => 'Feature',
            'properties' => new stdClass(),
            'geometry'   => array(
                'type'        => 'MultiPolygon',
                'coordinates' => $polygons,
            ),
        )),
    );
}

function zip_shape_map_ring_area($ring) {
    $area = 0.0;
    $count = count($ring);

    for ($index = 0; $index < $count; $index++) {
        $next = ($index + 1) % $count;
        $area += ($ring[$index][0] * $ring[$next][1]) - ($ring[$next][0] * $ring[$index][1]);
    }

    return $area / 2;
}

function zip_shape_map_point_in_ring($point, $ring) {
    $inside = false;
    $count = count($ring);

    for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
        $xi = $ring[$i][0];
        $yi = $ring[$i][1];
        $xj = $ring[$j][0];
        $yj = $ring[$j][1];

        $intersects = (($yi > $point[1]) !== ($yj > $point[1]))
            && ($point[0] < (($xj - $xi) * ($point[1] - $yi) / (($yj - $yi) ?: PHP_FLOAT_EPSILON)) + $xi);

        if ($intersects) {
            $inside = !$inside;
        }
    }

    return $inside;
}

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

    $cache_key = 'zip_shape_v4_' . md5(implode(',', $codes));
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

    $combined_geojson = zip_shape_map_dissolve_boundary_geojson($features);

    set_transient($cache_key, $combined_geojson, 30 * DAY_IN_SECONDS);

    return rest_ensure_response($combined_geojson);
}
