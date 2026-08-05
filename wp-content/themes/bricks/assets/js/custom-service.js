const CA_GRAVITY_FORM_ID = 3;

function setPopupPhoneLink(elementId, phoneNumber) {
  document.querySelectorAll("#" + elementId).forEach(function (element) {
    const phoneHref = "tel:" + phoneNumber;
    const existingLink = element.closest("a");

    if (existingLink) {
      existingLink.href = phoneHref;
      element.textContent = phoneNumber;
      return;
    }

    const phoneLink = document.createElement("a");
    Array.from(element.attributes).forEach(function (attribute) {
      phoneLink.setAttribute(attribute.name, attribute.value);
    });
    phoneLink.href = phoneHref;
    phoneLink.textContent = phoneNumber;
    element.replaceWith(phoneLink);
  });
}

function populateCanadaGravityLocationFields(postalCode, location) {
  const values = {
    [`input_${CA_GRAVITY_FORM_ID}_6`]: postalCode,
    [`input_${CA_GRAVITY_FORM_ID}_17`]: location.slug,
    [`input_${CA_GRAVITY_FORM_ID}_18`]: location.id,
    [`input_${CA_GRAVITY_FORM_ID}_19`]: window.location.href,
  };

  Object.entries(values).forEach(function ([inputId, value]) {
    const input = document.getElementById(inputId);

    if (input) {
      input.value = value || "";
      input.dispatchEvent(new Event("input", { bubbles: true }));
      input.dispatchEvent(new Event("change", { bubbles: true }));
    }
  });
}

function showCanadaGravityForm() {
  const form = document.getElementById(`gform_${CA_GRAVITY_FORM_ID}`);
  const wrapper = document.getElementById(
    `gform_wrapper_${CA_GRAVITY_FORM_ID}`
  );

  if (form) form.style.display = "block";
  if (wrapper) wrapper.style.display = "block";
}

function resetCanadaGravityForm() {
  document.getElementById(`gform_${CA_GRAVITY_FORM_ID}`)?.reset();
}

document.addEventListener("DOMContentLoaded", function () {
  const customService = document.getElementById("custom-service");
  const customServiceUl = document.getElementById("custom-service-ul");
  const toggleButton = customService?.querySelector("button");

  if (customService && customServiceUl && toggleButton) {
    customService.addEventListener("mouseenter", function () {
      customService.classList.add("open");
      toggleButton.setAttribute("aria-expanded", "true");
      customServiceUl.style.opacity = "1";
      customServiceUl.style.visibility = "visible";
      customServiceUl.style.minWidth = "290px";
    });

    customService.addEventListener("mouseleave", function () {
      customService.classList.remove("open");
      toggleButton.setAttribute("aria-expanded", "false");
      customServiceUl.style.opacity = "0";
      customServiceUl.style.visibility = "hidden";
    });
  }


  const body = document.body;
  const sidebar = document.querySelector('.side-bar-form-wrapper');
  const getQuoteBtns = document.querySelectorAll('.show-sidebar-form');
  const closeBtn = document.querySelector('.close-side-bar-form');

  const activeClass = 'form-active';

  // Loop through all buttons and add event listeners
  getQuoteBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      body.classList.add(activeClass);
      if (sidebar) {
        sidebar.classList.add(activeClass);

        // Focus the first input or textarea inside the sidebar form
        const firstInput = sidebar.querySelector('input, textarea, select, button');
        if (firstInput) {
          firstInput.focus();
        }
      }
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', function () {
      body.classList.remove(activeClass);
      if (sidebar) {
        sidebar.classList.remove(activeClass);
      }
    });
  }



});



function showNoLocationsPopup() {
  document.getElementById("popup-location_title").textContent = "No locations found.";
  document.getElementById("popup-location_address").textContent = "Would you like to view all our locations?";

  const phone_el = document.getElementById("popup-location_mobile");
  phone_el.innerHTML = "";

  const link_el = document.getElementById("popup-location_link");
  link_el.href = "/ca/locations";
  link_el.textContent = "View All Locations";

  const close2 = document.getElementById("popup-location_close2");
  if (close2) close2.style.display = "none";

  document.getElementById("location-popup-container").innerHTML = "";
  document.getElementById("location-popup-inner-static").style.display = "flex";
  document.getElementById("location-popup").style.display = "flex";
}

/**
 * Find My Location Button Click and Enter handled
 * Popup after Location Found and Nearest Location search as per zip code
 * Show/Hide popup
*/
document.querySelectorAll(".top-zipcode-input").forEach(function (input) {
  input.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      var locationContainer = this.closest(".location-container");
      var inputZip = this.value.trim();

      if (inputZip === "") {
        document.getElementById("location-popup").style.display = "none";
        alert("Enter Postal Code");
        return;
      }

      const zipCode = inputZip;
      const radius = 60;
      const apiKey =
        "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII";

      // var locations = document.querySelectorAll(".single-location");

      var nearbyLocationFinalArr = [];
      // Clear previous popup content (if any)
      const popupContainer = document.getElementById(
        "location-popup-container"
      );
      popupContainer.innerHTML = ""; // Clear previous popup data
      const popupInnerStatic = document.getElementById(
        "location-popup-inner-static"
      );

      popupInnerStatic.style.display = "flex";

      showCanadaGravityForm();

      let matchFound = false;

      fetch(ajaxData.ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "match_location_by_zip",
          nonce: ajaxData.match_location_nonce,
          zip_code: inputZip,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          // console.log('data', data.data.location);
          if (data.data.matched && data.data.location) {
            const locations = data.data.location;
            console.log(locations.title);

            document.getElementById("popup-location_title").textContent = locations.title;
            document.getElementById("popup-location_address").textContent = locations.address;
            // document.getElementById("popup-location_mobile").textContent = locations.phone;
            document.getElementById("popup-location_link").href = locations.website;
            document.getElementById("popup-location_close2").style.display = "flex";
            document.getElementById("location-popup").style.display = "flex";
            showCanadaGravityForm();
            populateCanadaGravityLocationFields(zipCode, locations);
            // document.getElementById("zip").value = inputZip;
            // document.getElementById("key").value = locations.key;
            // document.getElementById("keySm").value = locations.sm_key;
            // document.getElementById("url").value = locations.website;
            setPopupPhoneLink("est-phone-number", locations.phone);
            document.getElementById("tel-href").href = "tel:" + locations.phone;

            const phone_el = document.getElementById("popup-location_mobile");

            if (locations.phone) {
              phone_el.innerHTML = `<a href="tel:${locations.phone}">${locations.phone}</a>`;
            }

            const close2 = document.getElementById("popup-location_close2");
             if (close2) close2.style.display = "flex";

          } else {
            console.log("No direct zip match found.");
            const allLocations = data.data.locations;
            runFallbackSearch(zipCode, radius, apiKey, allLocations);
          }
        })
        .catch((err) => {
          console.error("AJAX error:", err);
        });

      function runFallbackSearch(zipCode, radius, apiKey, allLocations) {
        if (!matchFound) {
          //initialize loader
          document.getElementById("loader-wrapper").style.display = "flex";

          var matchedZipcodesArr = [];
          document.getElementById("location-popup").style.display = "none";
          console.log("Fetching nearby ZIP codes...");

          // Make the API request
          fetch(ajaxData.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
              action: "get_zip_codes_in_radius",
              nonce: ajaxData.zip_code_in_radius_nonce,
              zip_code: zipCode,
              radius: radius,
              api_key: apiKey,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                console.log("ZIP codes within the radius:", data.data.response);

                if (
                  data.data.response.zip_codes &&
                  data.data.response.zip_codes.length > 0
                ) {
                  // Extract nearby ZIP codes
                  const nearbyZips = data.data.response.zip_codes.map(
                    (item) => item.zip_code
                  );
                  console.log("Nearby ZIP Codes:", nearbyZips);
                  console.log('Location:', allLocations);

                  allLocations.forEach(function (location) {
                    const zipcode = location.zipcode;
                    const additionalZipcodes = location.additional_zipcodes || [];

                    const allZips = [zipcode, ...additionalZipcodes.map(zip => zip.trim())];

                    const matchingZips = allZips.filter(zip => nearbyZips.includes(zip));

                    if (matchingZips.length > 0) {
                      console.log("Matching ZIP Codes:", matchingZips);

                      matchedZipcodesArr.push(...matchingZips);

                      nearbyLocationFinalArr.push({
                        placeTitle: location.title,
                        placeAddress: location.address,
                        mobileNumber: location.phone,
                        websiteLink: location.website,
                        locationKey: location.key,
                        locationServiceminderKey: location.sm_key,
                        locationzipcode: zipcode,
                        locationId: location.id,
                        locationSlug: location.slug,
                        matchedZipcode: matchingZips,
                      });
                    }
                  });

                  console.log(
                    "Final nearby locations array:",
                    nearbyLocationFinalArr
                  );

                  if (nearbyLocationFinalArr.length > 0) {
                    fetch(
                      ajaxData.ajax_url,
                      {
                        method: "POST",
                        headers: {
                          "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: new URLSearchParams({
                          action: "get_zip_codes_distance_in_miles",
                          input_zip: inputZip,
                          nearby_zips: JSON.stringify(matchedZipcodesArr), // Send as JSON string
                        }),
                      }
                    )
                      .then((response) => response.json())
                      .then((data) => {
                        //hide loader
                        document.getElementById(
                          "loader-wrapper"
                        ).style.display = "none";

                        if (!data.success) {
                          throw new Error(
                            data.data.message || "Failed to fetch distances"
                          );
                        }

                        console.log("Distance Data:", data.data);

                        const sortedZipCodes = data.data.map(
                          (item) => item.zip
                        );

                        console.log("Sorted ZIP Codes:", sortedZipCodes);

                        // Reorder nearbyLocationFinalArr based on sorted zip codes
                        nearbyLocationFinalArr.sort((a, b) => {
                          // Find the first matching ZIP from sortedZipCodes in each location's matchedZipcode array
                          const indexA = sortedZipCodes.findIndex((zip) =>
                            a.matchedZipcode.includes(zip)
                          );
                          const indexB = sortedZipCodes.findIndex((zip) =>
                            b.matchedZipcode.includes(zip)
                          );

                          // If no match is found, set index to Infinity so unmatched locations go last
                          return (
                            (indexA === -1 ? Infinity : indexA) -
                            (indexB === -1 ? Infinity : indexB)
                          );
                        });

                        console.log(
                          "Sorted nearby locations:",
                          nearbyLocationFinalArr
                        );

                        // Now proceed with displaying the sorted locations
                        document.getElementById(
                          "location-popup"
                        ).style.display = "flex";
                        popupInnerStatic.style.display = "none"; // Hide static content

                        const close2 = document.getElementById("popup-location_close2");
                         if (close2) close2.style.display = "flex";

                        // Clear existing content before adding new locations
                        popupContainer.innerHTML = "";

                        // Add new content for each sorted location
                        nearbyLocationFinalArr.forEach((item) => {
                          const locationDiv = document.createElement("div");
                          locationDiv.classList.add("location-item");
                          locationDiv.dataset.locationId = item.locationId || "";
                          locationDiv.dataset.locationSlug = item.locationSlug || "";

                          locationDiv.innerHTML = `
        <h3 class="brxe-heading heading-style-h2 locationitem_title">${item.placeTitle}</h3>
        <h3 class="brxe-heading text-size-regular locationitem_address">${item.placeAddress}</h3>
        <h3 class="brxe-heading text-size-regular locationitem_phone"><a href="tel:${item.mobileNumber}">${item.mobileNumber}</a></h3>
        <div id="brxe-tfhrjk" class="brxe-block">
          <a href="${item.websiteLink}" class="brxe-div locationitem_link">
            <div id="brxe-xonhvx" class="brxe-text-basic">Visit Website</div>
          </a>
          <a class="brxe-div quote-btn-custom">
            <div class="brxe-text-basic"><span>Get a Free Estimate</span></div>
          </a>
          <p style="display:none;" class="seletced_location_key">${item.locationKey}</p>
          <p style="display:none;" class="seletced_location_sm_key">${item.locationServiceminderKey}</p>
          <p style="display:none;" class="location_zipcode">${item.locationzipcode}</p>
        </div>
      `;
                          popupContainer.appendChild(locationDiv);
                          document.getElementById("get-estimate-popup").style.display = "none";
                        });

                        const estimateCustomPopup = document.getElementById(
                          "estimate-popup-custom"
                        );
                        const locationPopup =
                          document.getElementById("location-popup");

                        const locationPopupCloseBtn = document.getElementById(
                          "estimate-custom-popup-close"
                        );

                        locationPopupCloseBtn.addEventListener(
                          "click",
                          function () {
                            estimateCustomPopup.style.display = "none";
                          }
                        );

                        // Attach event listeners after populating locations
                        document
                          .querySelectorAll(".quote-btn-custom")
                          .forEach((button) => {
                            button.addEventListener("click", function (event) {
                              const locationItem =
                                event.target.closest(".location-item");

                              if (locationItem) {
                                const clickedItemObj = {
                                  placeTitle:
                                    locationItem
                                      .querySelector(".locationitem_title")
                                      ?.textContent.trim() || null,
                                  placeAddress:
                                    locationItem
                                      .querySelector(".locationitem_address")
                                      ?.textContent.trim() || null,
                                  mobileNumber:
                                    locationItem
                                      .querySelector(".locationitem_phone")
                                      ?.textContent.trim() || null,
                                  websiteLink:
                                    locationItem
                                      .querySelector(".locationitem_link")
                                      ?.getAttribute("href") || null,
                                  locationKey:
                                    locationItem
                                      .querySelector(".seletced_location_key")
                                      ?.textContent.trim() || null,
                                  locationServiceminderKey:
                                    locationItem
                                      .querySelector(
                                        ".seletced_location_sm_key"
                                      )
                                      ?.textContent.trim() || null,
                                  locationZipcode: inputZip || null,
                                  locationId: locationItem.dataset.locationId || null,
                                  locationSlug: locationItem.dataset.locationSlug || null,
                                };

                                console.log(
                                  "clickedItemObj---",
                                  clickedItemObj
                                );

                                locationPopup.style.display = "none";
                                estimateCustomPopup.style.display = "flex";

                                populateCanadaGravityLocationFields(
                                  clickedItemObj.locationZipcode,
                                  {
                                    id: clickedItemObj.locationId,
                                    slug: clickedItemObj.locationSlug,
                                  }
                                );
                                setPopupPhoneLink(
                                  "est-phone-number-custom",
                                  clickedItemObj.mobileNumber
                                );
                                document.getElementById(
                                  "tel-href-custom"
                                ).href = `tel:${clickedItemObj.mobileNumber}`;

                                showCanadaGravityForm();
                              }
                            });
                          });
                      })
                      .catch((error) => {
                        //hide loader
                        document.getElementById(
                          "loader-wrapper"
                        ).style.display = "none";

                        console.error(
                          "Error fetching zip code distances:",
                          error
                        );
                      });
                  } else {
                    //hide loader
                    document.getElementById("loader-wrapper").style.display =
                      "none";

                    showNoLocationsPopup();
                  }
                } else {
                  //hide loader
                  document.getElementById("loader-wrapper").style.display =
                    "none";

                  showNoLocationsPopup();
                }
              } else {
                //hide loader
                document.getElementById("loader-wrapper").style.display =
                  "none";

                console.error("Error fetching ZIP codes:", data.data.message);
                showNoLocationsPopup();
              }
            })
            .catch((error) => {
              //hide loader
              document.getElementById("loader-wrapper").style.display = "none";

              console.error("Network error:", error);
              showNoLocationsPopup();
            });
        }
      }
    }
  });
});

document.querySelectorAll(".find-location-btn").forEach(function (button) {
  button.addEventListener("click", function () {
    var inputZip = this.closest(".location-container")
      .querySelector(".top-zipcode-input")
      .value.trim();

    if (inputZip === "") {
      document.getElementById("location-popup").style.display = "none";
      alert("Enter Zip or Postal Code");
      return;
    }

    const zipCode = inputZip;
    const radius = 60;
    const apiKey =
      "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII";

    // var locations = document.querySelectorAll(".single-location");
    var nearbyLocationFinalArr = [];
    // Clear previous popup content (if any)
    const popupContainer = document.getElementById(
      "location-popup-container"
    );
    popupContainer.innerHTML = ""; // Clear previous popup data
    const popupInnerStatic = document.getElementById(
      "location-popup-inner-static"
    );

    popupInnerStatic.style.display = "flex";

    showCanadaGravityForm();


    let matchFound = false;
    console.log(ajaxData.ajax_url);
    fetch(ajaxData.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "match_location_by_zip",
        nonce: ajaxData.match_location_nonce,
        zip_code: inputZip,
      }),
    })
    
      .then((res) => res.json())
      .then((data) => {
        console.log('data', data.data.location);
        if (data.data.matched && data.data.location) {
          const locations = data.data.location;

          document.getElementById("popup-location_title").textContent = locations.title;
          document.getElementById("popup-location_address").textContent = locations.address;
          // document.getElementById("popup-location_mobile").textContent = locations.phone;
          document.getElementById("popup-location_link").href = locations.website;
          document.getElementById("popup-location_close2").style.display = "flex";
          document.getElementById("location-popup").style.display = "flex";

            showCanadaGravityForm();
            populateCanadaGravityLocationFields(zipCode, locations);

          // document.getElementById("zip").value = inputZip;
          // document.getElementById("key").value = locations.key;
          // document.getElementById("keySm").value = locations.sm_key;
          const legacyUrlInput = document.getElementById("url");
          if (legacyUrlInput) legacyUrlInput.value = locations.website;
          setPopupPhoneLink("est-phone-number", locations.phone);
          document.getElementById("tel-href").href = "tel:" + locations.phone;
          document.getElementById("get-estimate-popup").style.display = "none";

          const phone_el = document.getElementById("popup-location_mobile");

            if (locations.phone) {
              phone_el.innerHTML = `<a href="tel:${locations.phone}">${locations.phone}</a>`;
            }

          const close2 = document.getElementById("popup-location_close2");
          if (close2) close2.style.display = "flex";
        } else {
          console.log("No direct zip match found.");
          const allLocations = data.data.locations;
          runFallbackSearch(zipCode, radius, apiKey, allLocations);
        }
      })
      .catch((err) => {
        console.error("AJAX error:", err);
      });
      function runFallbackSearch(zipCode, radius, apiKey, allLocations) {
  if (!matchFound) {
    // 1. Show Loader and Log Start
    document.getElementById("loader-wrapper").style.display = "flex";
    console.log("No direct match found. Running Canada Distance Logic for ZIP: " + zipCode);

    // 2. Prepare store list for the Distance API
    const storeZipsToCheck = allLocations.map(loc => ({
      title: loc.title,
      zip: loc.zipcode
    }));

    // 3. Call the PHP Distance Calculator
    fetch(ajaxData.ajax_url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        action: "get_ca_distances", // This matches the new function in functions.php
        user_zip: zipCode,
        store_data: JSON.stringify(storeZipsToCheck),
        radius: radius,
        api_key: apiKey
      }),
    })
    .then((res) => res.json())
    .then((data) => {
      document.getElementById("loader-wrapper").style.display = "none";

      if (data.success && data.data.length > 0) {
        console.log("Distance API Results:", data.data);
        
        // 4. Prepare the Popup Containers
        const popupContainer = document.getElementById("location-popup-container");
        const popupInnerStatic = document.getElementById("location-popup-inner-static");
        const locationPopup = document.getElementById("location-popup");
        
        popupContainer.innerHTML = ""; // Clear old results
        nearbyLocationFinalArr = []; // Reset the global array

        // 5. Loop through results and build the UI
        data.data.forEach((match) => {
          const loc = allLocations.find(l => l.title === match.title);
          
          if (loc) {
            // Add to our global tracking array
            nearbyLocationFinalArr.push({
              ...loc,
              distance: match.distance
            });

            // Create the HTML element
            const locationDiv = document.createElement("div");
            locationDiv.classList.add("location-item");
            locationDiv.dataset.locationId = loc.id || "";
            locationDiv.dataset.locationSlug = loc.slug || "";

            // Build the HTML structure (Matching your Bricks CSS IDs)
            locationDiv.innerHTML = `
              <h3 class="brxe-heading heading-style-h2 locationitem_title">${loc.title}</h3>
              <h3 class="brxe-heading text-size-regular locationitem_address">${loc.address}</h3>
              <h3 class="brxe-heading text-size-regular locationitem_phone"><a href="tel:${loc.phone}">${loc.phone}</a></h3>
              <div id="brxe-tfhrjk" class="brxe-block">
                <a href="${loc.website}" class="brxe-div locationitem_link">
                  <div id="brxe-xonhvx" class="brxe-text-basic">Visit Website</div>
                </a>
                <a href="#" class="brxe-div quote-btn-custom">
                  <div class="brxe-text-basic"><span>Get a Free Estimate</span></div>
                </a>
                <p style="display:none;" class="seletced_location_key">${loc.key}</p>
                <p style="display:none;" class="seletced_location_sm_key">${loc.sm_key}</p>
                <p style="display:none;" class="location_zipcode">${loc.zipcode}</p>
              </div>
            `;
            popupContainer.appendChild(locationDiv);
          }
        });

        // 6. Show the results popup
        locationPopup.style.display = "flex";
        popupInnerStatic.style.display = "none";
        document.getElementById("get-estimate-popup").style.display = "none";

        const close2 = document.getElementById("popup-location_close2");
        if (close2) close2.style.display = "flex";

        // 7. Attach Click Events to the newly created "Get Estimate" buttons
        const estimateCustomPopup = document.getElementById("estimate-popup-custom");
        
        popupContainer.querySelectorAll(".quote-btn-custom").forEach((btn) => {
          btn.addEventListener("click", function (e) {
            e.preventDefault();
            const container = this.closest(".location-item");
            
            // Extract data from THIS specific container to avoid "Multiple Result" errors
            // const storeKey = container.querySelector(".seletced_location_key").textContent.trim();
            // const storeSmKey = container.querySelector(".seletced_location_sm_key").textContent.trim();
            const storePhone = container.querySelector(".locationitem_phone").textContent.trim();
            const storeUrl = container.querySelector(".locationitem_link").getAttribute("href");

            // Populate the Estimate Form
            populateCanadaGravityLocationFields(zipCode, {
              id: container.dataset.locationId,
              slug: container.dataset.locationSlug,
            });
            // document.getElementById("zip-custom").value = zipCode;
            // document.getElementById("key-custom").value = storeKey;
            // document.getElementById("key-custom-sm").value = storeSmKey;
            const legacyCustomUrlInput = document.getElementById("url-custom");
            if (legacyCustomUrlInput) legacyCustomUrlInput.value = storeUrl;
            setPopupPhoneLink("est-phone-number-custom", storePhone);
            document.getElementById("tel-href-custom").href = "tel:" + storePhone;

            // Switch Popups
            locationPopup.style.display = "none";
            estimateCustomPopup.style.display = "flex";
            showCanadaGravityForm();
            
            console.log("Opening estimate form for: " + container.querySelector(".locationitem_title").textContent);
          });
        });

        console.log("Final nearby locations sorted by distance:", nearbyLocationFinalArr);

      } else {
        // No stores within the radius
        document.getElementById("loader-wrapper").style.display = "none";
        showNoLocationsPopup();
      }
    })
    .catch((error) => {
      document.getElementById("loader-wrapper").style.display = "none";
      console.error("Critical Error in runFallbackSearch:", error);
    });
  }
}
  });
});

document
  .getElementById("popup-location_close")
  .addEventListener("click", function () {
    document.getElementById("location-popup").style.display = "none";
    document.getElementById("get-estimate-popup").style.display = "none";
    document.querySelectorAll(".top-zipcode-input").forEach(function (input) {
      input.value = "";
    });
    resetCanadaGravityForm();
  });

const popupLocationClose2 = document.getElementById("popup-location_close2");

if (popupLocationClose2) {
  popupLocationClose2.addEventListener("click", function () {
    document.getElementById("location-popup").style.display = "none";
    document.getElementById("get-estimate-popup").style.display = "none";
    document.querySelectorAll(".top-zipcode-input").forEach(function (input) {
      input.value = "";
    });
  });
}

// Get estimate button function
const estimateBtn = document.getElementById("get-estimate-btn");

if (estimateBtn) {
  estimateBtn.addEventListener("click", function () {
    const popup = document.getElementById("get-estimate-popup");
    if(document.getElementById("brxe-oelkzx").classList.contains('brx-open')) {
      document.getElementById("brxe-nagnqq").click();
    }
    if (popup) {
      popup.style.display = "flex";
    }
  });
}

const getEstimateBtn1 = document.getElementById("get-estimate-btn1");
const getEstimateBtn2 = document.getElementById("get-estimate-btn2");
const getEstimateBtnServiceSingle = document.getElementById(
  "service-detail-est-btn"
);

if (getEstimateBtn1) {
  getEstimateBtn1.addEventListener("click", function () {
    document.getElementById("get-estimate-popup").style.display = "flex";
  });
}

if (getEstimateBtn2) {
  getEstimateBtn2.addEventListener("click", function () {
    document.getElementById("get-estimate-popup").style.display = "flex";
  });
}

if (getEstimateBtnServiceSingle) {
  getEstimateBtnServiceSingle.addEventListener("click", function () {
    document.getElementById("get-estimate-popup").style.display = "flex";
  });
}

document
  .getElementById("get-estimate-close-btn")
  .addEventListener("click", function () {
    //console.log("close");
    document.getElementById("get-estimate-popup").style.display = "none";
  });
