const test = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const source = fs.readFileSync(__dirname + '/../wp-content/themes/bricks/assets/js/custom-service.js', 'utf8');
for (const trigger of ['enter', 'button']) {
  test(`exact postal match renders phone and routing without optional tel-href: ${trigger}`, async () => {
    const handlers = {}, errors = [], elements = {};
    const element = id => elements[id] ||= {style:{}, value:'', innerHTML:'text', addEventListener(){}, dispatchEvent(){}};
    const input = {value:'L1J 7A5', closest(){return {querySelector(){return input}}}, addEventListener(event, handler){handlers.enter=handler}};
    const button = {closest(){return {querySelector(){return input}}}, addEventListener(event, handler){handlers.button=handler}};
    const document = {
      addEventListener(){},
      getElementById(id){return id === 'tel-href' ? null : element(id)},
      querySelectorAll(selector){return selector === '.top-zipcode-input' ? [input] : selector === '.find-location-btn' ? [button] : []},
    };
    const location = {id:20711, slug:'durham', title:'Durham', address:'Test address', phone:'905-555-0100', website:'/ca/durham'};
    const context = {document, window:{location:{href:'https://example.test/ca/locations'}}, Event:class {}, URLSearchParams,
      ajaxData:{ajax_url:'/ajax',match_location_nonce:'test'}, console:{log(){},error(...args){errors.push(args)}},
      fetch:async()=>({json:async()=>({data:{matched:true,location}})})};
    vm.runInNewContext(source, context);
    handlers[trigger].call(trigger === 'enter' ? input : button, {key:'Enter'});
    await new Promise(resolve=>setImmediate(resolve));
    assert.deepEqual(errors, []);
    assert.equal(element('input_3_17').value, 'durham');
    assert.equal(element('input_3_18').value, 20711);
    assert.match(element('popup-location_mobile').innerHTML, /href="tel:905-555-0100"/);
  });
}
