const test = require("node:test");
const assert = require("node:assert/strict");

const locationSwitch = require("../wp-content/themes/bricks/assets/js/cross-border-location-switch.js");

test("Canadian site recognizes five-digit US ZIP codes", () => {
  assert.equal(locationSwitch.isUsZip("90210"), true);
  assert.equal(locationSwitch.isUsZip("12345-6789"), true);
  assert.equal(locationSwitch.isUsZip("M5V 3A8"), false);
});

test("Canadian site redirects only when the visitor accepts", () => {
  assert.equal(
    locationSwitch.getSwitchUrl("90210", "CA", true),
    "https://koalainsulation.com/locations/"
  );
  assert.equal(locationSwitch.getSwitchUrl("90210", "CA", false), null);
  assert.equal(locationSwitch.getSwitchUrl("M5V 3A8", "CA", true), null);
});
