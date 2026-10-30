# Canadian postal-distance sanity bounds

`ca-fsa-bounds-2021.json` contains 1,643 padded envelopes in WGS84, keyed by
three-character census Forward Sortation Area (FSA). Each value is
`[west, south, east, north]`. It is an independent **rejection check**, not a
postal-code coordinate database, a source of replacement distances, or a
franchise-territory definition. Exact ownership and provider distances remain
unchanged. US lookups do not use this data.

Source: Statistics Canada, 2021 Census Forward Sortation Area Boundary File,
Digital boundary files, layer 14:
https://geo.statcan.gc.ca/geo_wa/rest/services/2021/Digital_boundary_files/MapServer/14

Reference guide:
https://www150.statcan.gc.ca/n1/pub/92-179-g/92-179-g2021001-eng.htm

Adapted from Statistics Canada, 2021 Census Forward Sortation Area Boundary
File. This does not constitute an endorsement by Statistics Canada of this
product. FSA is an official mark of Canada Post Corporation. Use is subject to
the Statistics Canada Open Licence:
https://www.statcan.gc.ca/en/reference/licence

Exported 2026-10-19 with this layer's `/query` endpoint:
`where=1=1&outFields=CFSAUID&outSR=4326&maxAllowableOffset=0.01&geometryPrecision=5&f=geojson`.
Rebuild with `python3 tools/build-ca-fsa-bounds.py source.geojson`.
The generator requires all 1,643 features and pads envelopes by 0.02 degrees.
The runtime also allows two miles of tolerance. Census FSAs may differ from
current Canada Post boundaries; unknown FSAs do not establish a safe fallback.

## Staging investigation: Toronto M5V 3A8

The provider's fresh `info.json` response labels N2V 0G6 as Waterloo, but gives
43.642600, -79.387097 (downtown Toronto). Its radius response consequently reports
0.553 miles from M5V 3A8. L9L 0B2 (Port Perry) and L7J 0B6 (Acton) also have
implausible short distances. The simple-response arrays are correctly paired;
the errors are upstream data, not a parsing or ranking mistake.

The 50,000-result cap around M5V 3A8 reaches only 11.621 miles. Those three
invalid records are the only owned codes returned. Rejecting them therefore
produces `lookup_failed / invalid_geography`, not a guessed franchise or a
false claim of no coverage. The search UI uses its existing lookup-error
message; form resolution uses the existing unresolved/manual-review path.
A complete, reliable postal-coordinate source is still needed to establish the
correct automatic nearest location for this case. This safeguard cannot repair
provider coordinates or detect every error inside a census-area envelope.

## Deployment / QA boundary

Deploy the resolver, `canadian-distance-validation.php`, this JSON dataset, and
`zip-lookup-log.php` together. No ownership, credentials, forms or cache-rule
changes are needed. Existing radius transients are validated at read time;
there is no broad cache purge. To roll back, restore the previous resolver and
lookup-log files; the now-unused helper and data can remain safely in place.

Testing must not submit forms or run background delivery jobs. Call the
resolver with `entry_id=0`. Suppress `wp_mail` using an ephemeral `pre_wp_mail`
filter in the QA process, because lookup errors may trigger configured alerts.
The standalone regression suite mocks all external delivery.
