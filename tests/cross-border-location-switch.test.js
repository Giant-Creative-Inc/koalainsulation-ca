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

test("Canadian site supplies branded American switch-dialog copy", () => {
  assert.deepEqual(locationSwitch.getDialogCopy("CA"), {
    title: "Switch to the American site?",
    message: "It looks like you entered a U.S. ZIP code.",
    visitLabel: "Visit American Site",
    stayLabel: "Stay on Canadian Site",
  });
});

test("map search targets the map ZIP field instead of a header field", () => {
  assert.equal(locationSwitch.getInputSelectorForTrigger("search-zip"), "#zipcode-input");
  assert.equal(
    locationSwitch.getInputSelectorForTrigger("my-search-zip"),
    ".top-zipcode-input"
  );
});
