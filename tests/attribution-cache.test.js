const test = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const source = fs.readFileSync(__dirname + '/../wp-content/plugins/koala-gravity-integration-main/assets/js/attribution.js', 'utf8');

function visitor({search = '', stored, stale = '24235797805'} = {}) {
  const events = {}, jqueryEvents = {};
  const fields = {UtmCampaign: 23, gclid: 24, landing_page: 25, referrer: 26};
  const forms = [1, 3].map(() => Object.fromEntries(Object.values(fields).map(id => [id, {value: stale}])));
  forms.forEach(form => form[99] = {value: 'customer@example.test'});
  let cookie = stored ? 'kgi_attrib=' + encodeURIComponent(JSON.stringify(stored)) : '';
  const document = {
    readyState: 'complete', referrer: '', body: {getAttribute: () => ''},
    addEventListener(name, fn) {events[name] = fn;},
    querySelectorAll(selector) {
      const index = selector === '#gform_1' ? 0 : selector === '#gform_3' ? 1 : -1;
      return index < 0 ? [] : [{querySelector: selector => forms[index][selector.match(/input_(\d+)/)[1]]}];
    }
  };
  Object.defineProperty(document, 'cookie', {get: () => cookie, set: value => {cookie = value.split(';')[0];}});
  vm.runInNewContext(source, {
    document, URLSearchParams,
    window: {location: {href: 'https://example.test/ca/east-toronto/' + search, search},
      sessionStorage: {getItem: () => null}, jQuery: () => ({on: (name, fn) => {jqueryEvents[name] = fn;}})},
    kgiData: {trackingFieldIds: {1: fields, 3: fields}}
  });
  return {forms, events, jqueryEvents};
}

test('clean visitor clears cached campaign and click ID on inline and popup forms', () => {
  const {forms} = visitor();
  for (const form of forms) {
    assert.equal(form[23].value, '');
    assert.equal(form[24].value, '');
    assert.equal(form[25].value, 'https://example.test/ca/east-toronto/');
    assert.equal(form[26].value, '');
    assert.equal(form[99].value, 'customer@example.test');
  }
});
test('current campaign replaces cached values and older cookie values', () => {
  const {forms} = visitor({search: '?utm_campaign=NEW&gclid=NEW_CLICK', stored: {UtmCampaign: 'OLD', gclid: 'OLD_CLICK', landing_page: 'https://example.test/first'}});
  for (const form of forms) {
    assert.equal(form[23].value, 'NEW');
    assert.equal(form[24].value, 'NEW_CLICK');
    assert.equal(form[25].value, 'https://example.test/first');
  }
});
test('untagged navigation retains this visitor attribution cookie', () => {
  const {forms} = visitor({stored: {UtmCampaign: 'OWN', gclid: 'OWN_CLICK'}});
  for (const form of forms) {
    assert.equal(form[23].value, 'OWN');
    assert.equal(form[24].value, 'OWN_CLICK');
  }
});
for (const event of ['submit', 'gform/post_render', 'gform_post_render']) {
  test(`${event} replaces stale values restored after initial population`, () => {
    const {forms, events, jqueryEvents} = visitor({search: '?utm_campaign=OWN'});
    forms.forEach(form => {form[23].value = 'STALE'; form[24].value = 'STALE';});
    (events[event] || jqueryEvents[event])();
    forms.forEach(form => {assert.equal(form[23].value, 'OWN'); assert.equal(form[24].value, '');});
  });
}
test('separate visitors using identical cached markup remain isolated', () => {
  const first = visitor({search: '?utm_campaign=A&gclid=A_CLICK'});
  const second = visitor({search: '?utm_campaign=B&gclid=B_CLICK'});
  assert.equal(first.forms[0][23].value, 'A');
  assert.equal(second.forms[0][23].value, 'B');
  assert.equal(visitor().forms[0][23].value, '');
});
