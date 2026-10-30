"""Build conservative bounds from Statistics Canada's 2021 FSA GeoJSON export.

Usage: python3 tools/build-ca-fsa-bounds.py source.geojson
See data/README.md for the source query and licence.
"""
import json
import pathlib
import sys


def coordinates(value):
    if isinstance(value[0], (int, float)):
        yield value
    else:
        for child in value:
            yield from coordinates(child)


source = json.loads(pathlib.Path(sys.argv[1]).read_text())
assert not source.get('exceededTransferLimit')
result = {}
for feature in source['features']:
    points = list(coordinates(feature['geometry']['coordinates']))
    xs, ys = zip(*[(p[0], p[1]) for p in points])
    # Pad beyond the 0.01-degree export simplification tolerance.
    result[feature['properties']['CFSAUID']] = [
        round(min(xs) - .02, 5), round(min(ys) - .02, 5),
        round(max(xs) + .02, 5), round(max(ys) + .02, 5),
    ]
assert len(result) == 1643, 'Incomplete or changed source export; review manually.'
target = pathlib.Path(__file__).resolve().parent.parent / 'data/ca-fsa-bounds-2021.json'
target.write_text(json.dumps(dict(sorted(result.items())), separators=(',', ':')) + '\n')
