$ = jQuery;
window.addEventListener("load", function () {
  var select = document.getElementById("stateSelect");
  var accordionSections = document.querySelectorAll(".acc1");

  // // Populate select options with state names
  accordionSections.forEach(function (section) {
    var stateName = section.querySelector(".state-name").innerText;
    var option = document.createElement("option");
    option.value = stateName.toLowerCase().replace(/s/g, "-");
    option.text = stateName;
    select.appendChild(option);
  });

  if (select) {
    // Event listener for filtering accordion sections
    select.addEventListener("change", function () {
      var selectedState = this.value.toLowerCase();
      accordionSections.forEach(function (section) {
        var stateName = section
          .querySelector(".state-name")
          .innerText.toLowerCase()
          .replace(/s/g, "-");
        if (selectedState === "" || stateName === selectedState) {
          section.style.display = "block";
        } else {
          section.style.display = "none";
        }
      });
    });
  }

  var locations = [];
  var dynPlaces = document.querySelectorAll(".info_content");
  dynPlaces.forEach(function (elem) {
    var place = [];
    var title = elem.querySelector(".place-title").innerText;
    var pAddress = elem.querySelector(".place-address").innerText;
    var pLat = Number(elem.querySelector(".lat").innerText);
    var pLong = Number(elem.querySelector(".long").innerText);
    var pPhone = elem.querySelector(".mobile-number").innerText;
    var pZip = elem.querySelector(".zipcode").innerText;
    var anchorElement = elem.querySelector(".place-link");
    var hrefLink = anchorElement ? anchorElement.getAttribute("href") : "";
    var additionalZipcodes = elem.querySelector(
      ".additional-zipcodes"
    ).innerText;
    var pServiceAreas = elem.querySelector(".service-area").innerText;

    place.push(
      title,
      pLat,
      pLong,
      pAddress,
      pPhone,
      pZip,
      hrefLink,
      additionalZipcodes,
      pServiceAreas
    );
    locations.push(place);
  });
  //console.log(locations);
  var gmarkers = [];
  var map = new google.maps.Map(document.getElementById("map1"), {
    zoom: 4,
    center: new google.maps.LatLng(37.09024, -95.712891),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
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

  function ZoomAndCenterMyLocation(lat, long) {
    map.setCenter(new google.maps.LatLng(lat, long));
    map.setZoom(10);
  }

  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else {
      // console.log("Geolocation is not supported by this browser.");
    }
  }

  function showPosition(position) {
    var lat = position.coords.latitude;
    var long = position.coords.longitude;
    ZoomAndCenterMyLocation(lat, long);
  }

  function searchByZipCodeAndCreateMarker(zips, closestZip) {
    // Ensure zips is an array and sanitize it
    if (!Array.isArray(zips)) {
      zips = [zips];
    }
    zips = zips.map(function (zip) {
      return zip.trim(); // Trim each zip code for consistency
    });

    // Clear existing markers
    gmarkers.forEach(function (marker) {
      marker.setMap(null);
    });
    gmarkers = [];

    var foundLocations = locations.filter(function (location) {
      var zipCodes = [location[5]]; // Add primary zip code

      // Check if any zip in zips matches any zip in zipCodes
      return zips.some(function (zip) {
        return zipCodes.includes(zip);
      });
    });

    if (foundLocations.length > 0) {
      foundLocations.forEach(function (location) {
        var marker = createMarker(
          new google.maps.LatLng(location[1], location[2]),
          location[0],
          location[3],
          location[4],
          location[6],
          location[8]
        );
        gmarkers.push(marker);
      });

      if (closestZip !== null) {
        var foundLocations = locations.filter(function (location) {
          var zipCodes = [location[5]]; // Add primary zip code
          const additionalZipCodes = location[7]
            ? location[7].split(",").map((zip) => zip.trim())
            : [];

          var allZips = [...zipCodes, ...additionalZipCodes];

          // Check if any zip in zips matches any zip in zipCodes
          return allZips.includes(closestZip);
        });

        // Automatically display the infowindow for the first matched location
        var firstLocation = foundLocations[0];
        var firstMarker = gmarkers[0];
        infowindow.setContent(
          "<div>" +
            '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 0px;">Koala Insulation of</p>' +
            '<p style="color: white; font-size: 18px; font-weight: 900;line-height: 27px; margin-bottom: 4px;">' +
            firstLocation[0] +
            "</p>" +
            '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
            firstLocation[3] +
            "</p>" +
            `<a href="tel:${firstLocation[4]}" style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">` +
            firstLocation[4] +
            "</a>" +
            '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
            '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
            firstLocation[8] +
            "</p>" +
            '<a href="' +
            firstLocation[6] +
            '" style="text-decoration: none;">' +
            '<button style="background-color: #97D700; color: #002E5D; font-size: 12px; line-height:18px;font-weight: 700; text-transform:uppercase; letter-spacing:-3%; padding: 8px 16px; border: none; border-radius: 999px; cursor: pointer;">Visit website</button>' +
            "</a>" +
            "</div>"
        );

        infowindow.open(map, firstMarker);

        // Zoom and center the map on the first matched location
        map.setCenter(
          new google.maps.LatLng(firstLocation[1], firstLocation[2])
        );
        map.setZoom(4);

        handleScrollAndHighlightPrimaryZips(zips, closestZip);
      } else {
        // Automatically display the infowindow for the first matched location
        var firstLocation = foundLocations[0];
        var firstMarker = gmarkers[0];
        infowindow.setContent(
          "<div>" +
            '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 0px;">Koala Insulation of</p>' +
            '<p style="color: white; font-size: 18px; font-weight: 900;line-height: 27px; margin-bottom: 4px;">' +
            firstLocation[0] +
            "</p>" +
            '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
            firstLocation[3] +
            "</p>" +
            `<a href="tel:${firstLocation[4]}" style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">` +
            firstLocation[4] +
            "</a>" +
            '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
            '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
            firstLocation[8] +
            "</p>" +
            '<a href="' +
            firstLocation[6] +
            '" style="text-decoration: none;">' +
            '<button style="background-color: #97D700; color: #002E5D; font-size: 12px; line-height:18px;font-weight: 700; text-transform:uppercase; letter-spacing:-3%; padding: 8px 16px; border: none; border-radius: 999px; cursor: pointer;">Visit website</button>' +
            "</a>" +
            "</div>"
        );

        infowindow.open(map, firstMarker);

        // Zoom and center the map on the first matched location
        map.setCenter(
          new google.maps.LatLng(firstLocation[1], firstLocation[2])
        );
        map.setZoom(4);

        handleScrollAndHighlightPrimaryZips(zips, closestZip);
      }
    } else {
      alert("Unfortunately we do not service your area at this time");
    }
  }

  function searchByAdditionalZipCodeAndCreateMarker(zips, closestZip) {
    // Ensure zips is an array and sanitize it
    if (!Array.isArray(zips)) {
      zips = [zips];
    }
    zips = zips.map((zip) => zip.trim());

    // Clear existing markers
    gmarkers.forEach((marker) => marker.setMap(null));
    gmarkers = [];

    // Filter locations by matching ZIP codes
    const foundLocations = locations.filter((location) => {
      const zipCodes = location[7]
        ? location[7].split(",").map((zip) => zip.trim())
        : [];
      return zips.some((zip) => zipCodes.includes(zip));
    });

    if (foundLocations.length > 0) {
      // Create markers for matched locations
      foundLocations.forEach((location) => {
        const marker = createMarker(
          new google.maps.LatLng(location[1], location[2]),
          location[0],
          location[3],
          location[4],
          location[6],
          location[8]
        );
        gmarkers.push(marker);
      });

      // Show info window for the first matched location
      const firstLocation = foundLocations[0];
      const firstMarker = gmarkers[0];
      infowindow.setContent(
        "<div>" +
          '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 0px;">Koala Insulation of</p>' +
          '<p style="color: white; font-size: 18px; font-weight: 900;line-height: 27px; margin-bottom: 4px;">' +
          firstLocation[0] +
          "</p>" +
          '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
          firstLocation[3] +
          "</p>" +
          `<a href="tel:${firstLocation[4]}" style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">` +
          firstLocation[4] +
          "</a>" +
          '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
          '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
          firstLocation[8] +
          "</p>" +
          '<a href="' +
          firstLocation[6] +
          '" style="text-decoration: none;">' +
          '<button style="background-color: #97D700; color: #002E5D; font-size: 12px; line-height:18px;font-weight: 700; text-transform:uppercase; letter-spacing:-3%; padding: 8px 16px; border: none; border-radius: 999px; cursor: pointer;">Visit website</button>' +
          "</a>" +
          "</div>"
      );
      infowindow.open(map, firstMarker);

      map.setCenter(new google.maps.LatLng(firstLocation[1], firstLocation[2]));
      map.setZoom(4);

      // Scroll to and highlight matching elements
      handleScrollAndHighlightAdditionalZips(zips);
    } else {
      alert("Unfortunately we do not service your area at this time.");
    }
  }

 function handleScrollAndHighlightAdditionalZips(zips) {
    const infoContentArray = Array.from(
      document.querySelectorAll(".info_content")
    ).filter((element) => {
      const additionalZips = element.querySelector(".additional-zipcodes")
        ? element
            .querySelector(".additional-zipcodes")
            .innerText.split(",")
            .map((zip) => zip.trim())
        : [];
      return zips.some((zip) => additionalZips.includes(zip));
    });

    if (infoContentArray.length > 0) {
      // Highlight all matched elements
      infoContentArray.forEach((element) => {
        element.style.backgroundColor = "white";
      });

      // Safely check if either scroll container exists before scrolling
      const scrollContainer = document.querySelector("#brxe-htrqiy") || document.querySelector("#brxe-gehexg");
      if (scrollContainer) {
        const firstElement = infoContentArray[0];
        scrollContainer.scrollTo({
          top: firstElement.offsetTop - scrollContainer.offsetTop,
          behavior: "smooth",
        });
      }
    }
  }

  function handleScrollAndHighlightPrimaryZips(zips, closestZip) {
    const infoContentArray = Array.from(
      document.querySelectorAll(".info_content")
    ).filter((element) => {
      var primaryZip = element.querySelector(".zipcode").innerText.trim();
      return zips.some((zip) => primaryZip === zip);
    });

    if (infoContentArray.length > 0) {
      // Highlight all matched elements
      infoContentArray.forEach((element) => {
        element.style.backgroundColor = "white";
      });
    }

    // Safely look for either scroll container
    const scrollContainer = document.querySelector("#brxe-htrqiy") || document.querySelector("#brxe-gehexg");

    if (scrollContainer) {
      if (closestZip !== null) {
        const infoContentArrayClosest = Array.from(
          document.querySelectorAll(".info_content")
        ).filter((element) => {
          var primaryZip = element.querySelector(".zipcode").innerText.trim();
          const additionalZips = element.querySelector(".additional-zipcodes")
            ? element
                .querySelector(".additional-zipcodes")
                .innerText.split(",")
                .map((zip) => zip.trim())
            : [];

          // FIXED: Use an array instead of spreading string characters
          var allZips = [primaryZip, ...additionalZips];
          return allZips.includes(closestZip);
        });

        if (infoContentArrayClosest.length > 0) {
          const firstElement = infoContentArrayClosest[0];
          scrollContainer.scrollTo({
            top: firstElement.offsetTop - scrollContainer.offsetTop,
            behavior: "smooth",
          });
        }
      } else if (infoContentArray.length > 0) {
        const firstElement = infoContentArray[0];
        scrollContainer.scrollTo({
          top: firstElement.offsetTop - scrollContainer.offsetTop,
          behavior: "smooth",
        });
      }
    }
  }

 $(document).ready(function () {
  // Helper to normalize zip codes (Upper case, no spaces)
  function normalizeZip(zip) {
    return zip ? zip.toString().trim().toUpperCase().replace(/\s+/g, "") : "";
  }

  $("#current-loc").click(function () {
    getLocation();
  });

  $("#search-zip").click(function () {
    performSearch();
  });

  $("#zipcode-input").keydown(function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      performSearch();
    }
  });

  function performSearch() {
    // 1. Normalize Input
    var rawInput = $("#zipcode-input").val();
    var zipCode = normalizeZip(rawInput);

    if (zipCode) {
      const radius = 60;
      const apiKey = "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII";

      var domLocations = document.querySelectorAll(".info_content"); // Renamed to avoid shadowing global locations array
      var matchedLocationZips = []; 
      var matchedZipcodesArr = [];

      // --- PHASE 1: Check Local HTML for exact match ---
      domLocations.forEach(function (location) {
        // Get DOM value (Main Zip)
        var rawDomZip = location.querySelector(".zipcode").textContent.trim();
        var domZipNormalized = normalizeZip(rawDomZip);

        // Get Additional Zips
        var rawAdditional = location.querySelector(".additional-zipcodes").textContent;
        var additionalZipcodes = rawAdditional.split(/\s*,\s*/).map(function (z) {
          return normalizeZip(z);
        });

        console.log("Checking (Normalized):", domZipNormalized, "vs Input:", zipCode);

        // Check 1: Does the Main Zip match?
        if (domZipNormalized === zipCode) {
           matchedLocationZips.push(rawDomZip);
        } 
        // Check 2: Is the input in the Additional Zips?
        else if (additionalZipcodes.includes(zipCode)) {
           console.log("Found match in additional zips for location:", rawDomZip);
           matchedLocationZips.push(rawDomZip); 
        }
      });

      // If we found ANY match (Main or Additional), open the marker
      if (matchedLocationZips.length > 0) {
        console.log("Opening marker for location(s):", matchedLocationZips);
        searchByZipCodeAndCreateMarker(matchedLocationZips, null);
      } 
      else {
        // --- PHASE 2: API Request (No local match found) ---
        document.getElementById("loader-wrapper").style.display = "flex";

        // Check if input is a Canadian alphanumeric postal code
        const isCanadian = /^[A-Z]\d[A-Z]\d[A-Z]\d$/i.test(zipCode);

        if (isCanadian) {
          console.log("No direct matches. Canadian ZIP detected. Running Canada Distance Logic...");

          // Format store data for get_ca_distances using our global locations array
          const storeZipsToCheck = locations.map(function (loc) {
            return {
              title: loc[0], // Location Title
              zip: loc[5]    // Primary Zipcode
            };
          });

          fetch(koalaData.ajax_url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
              action: "get_ca_distances",
              user_zip: zipCode,
              store_data: JSON.stringify(storeZipsToCheck),
              radius: radius,
              api_key: apiKey
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              document.getElementById("loader-wrapper").style.display = "none";

              if (data.success && data.data && data.data.length > 0) {
                console.log("Canadian Distance API Results:", data.data);

                var matchedLocationZips = [];
                var closestZip = null;

                data.data.forEach((match, index) => {
                  // Find the matching location in our local locations array
                  const localMatch = locations.find(loc => loc[0] === match.title);
                  if (localMatch) {
                    const primaryZip = localMatch[5];
                    matchedLocationZips.push(primaryZip);
                    if (index === 0) {
                      closestZip = primaryZip;
                    }
                  }
                });

                if (matchedLocationZips.length > 0) {
                  searchByZipCodeAndCreateMarker(matchedLocationZips, closestZip);
                } else {
                  alert("Unfortunately we do not service your area at this time");
                }
              } else {
                alert("Unfortunately we do not service your area at this time");
              }
            })
            .catch((error) => {
              document.getElementById("loader-wrapper").style.display = "none";
              console.error("Critical Error in Canadian search:", error);
              alert("An error occurred while searching. Please try again.");
            });

        } else {
          // Fallback US Radius logic
          console.log("No direct matches. US ZIP detected. Fetching nearby ZIP codes...");

          fetch(koalaData.ajax_url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
              action: "get_zip_codes_in_radius",
              zip_code: zipCode, 
              radius: radius,
              api_key: apiKey,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success && data.data.response.zip_codes && data.data.response.zip_codes.length > 0) {
                
                const nearbyZipsNormalized = data.data.response.zip_codes.map((item) =>
                  normalizeZip(item.zip_code)
                );
                
                console.log("Nearby Zips (Normalized):", nearbyZipsNormalized);

                domLocations.forEach(function (location) {
                  var rawDomZip = location.querySelector(".zipcode").textContent.trim();
                  var domZipNormalized = normalizeZip(rawDomZip);

                  var rawAdditional = location.querySelector(".additional-zipcodes").textContent.replace(/"/g, "");
                  var additionalZipcodesNormalized = rawAdditional.split(/\s*,\s*/).map((z) => normalizeZip(z));

                  const allLocationZipsNormalized = [domZipNormalized, ...additionalZipcodesNormalized];

                  const matchingZips = allLocationZipsNormalized.filter((z) =>
                    nearbyZipsNormalized.includes(z)
                  );

                  if (matchingZips.length > 0) {
                    matchedZipcodesArr.push(...matchingZips);
                    matchedLocationZips.push(rawDomZip);
                  }
                });

                if (matchedZipcodesArr.length > 0) {
                  // Get distances
                  fetch(koalaData.ajax_url, {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({
                      action: "get_zip_codes_distance_in_miles",
                      input_zip: zipCode,
                      nearby_zips: JSON.stringify(matchedZipcodesArr),
                    }),
                  })
                    .then((response) => response.json())
                    .then((distData) => {
                      document.getElementById("loader-wrapper").style.display = "none";
                      if (!distData.success) throw new Error(distData.data.message);

                      const sortedZipCodes = distData.data.map((item) => item.zip);
                      let closestZip = sortedZipCodes[0];
                      searchByZipCodeAndCreateMarker(matchedLocationZips, closestZip);
                    })
                    .catch((err) => {
                      document.getElementById("loader-wrapper").style.display = "none";
                      console.error("Error fetching distances:", err);
                    });
                } else {
                  document.getElementById("loader-wrapper").style.display = "none";
                  alert("Unfortunately we do not service your area at this time");
                }
              } else {
                document.getElementById("loader-wrapper").style.display = "none";
                alert("No nearby postal codes found.");
              }
            })
            .catch((error) => {
              document.getElementById("loader-wrapper").style.display = "none";
              console.error("Network error:", error);
              alert("Failed to fetch ZIP codes. Please check your network connection.");
            });
        }
      }
    } else {
      alert("Please enter a ZIP code.");
    }
  }
});

  var infowindow = new google.maps.InfoWindow({
    maxWidth: 285,
  });

  function createMarker(
    latlng,
    title,
    pAddress,
    pPhone,
    hrefLink,
    pServiceAreas
  ) {
    var marker = new google.maps.Marker({
      position: latlng,
      map: map,
      icon: koalaData.map_pin,
    });

    google.maps.event.addListener(marker, "click", function () {
      infowindow.setContent(
        "<div>" +
          '<p style="color: white; font-size: 18px; font-weight: 900; line-height: 27px; margin-bottom: 0px;">Koala Insulation of</p>' +
          '<p style="color: white; font-size: 18px; font-weight: 900;line-height: 27px; margin-bottom: 4px;">' +
          title +
          "</p>" +
          '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 4px;">' +
          pAddress +
          "</p>" +
          `<a href="tel:${pPhone}" style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 12px;">` +
          pPhone +
          "</a>" +
          '<p style="color: white; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 4px;">Common Areas Serviced</p>' +
          '<p style="color: rgba(255,255,255,0.6);font-size:14px; line-height:21px; font-weight: 400; margin-bottom: 8px; text-transform: uppercase;">' +
          pServiceAreas +
          "</p>" +
          '<a href="' +
          hrefLink +
          '" style="text-decoration: none;">' +
          '<button style="background-color: #97D700; color: #002E5D; font-size: 12px; line-height:18px;font-weight: 700; text-transform:uppercase; letter-spacing:-3%; padding: 8px 16px; border: none; border-radius: 999px; cursor: pointer;">Visit website</button>' +
          "</a>" +
          "</div>"
      );

      infowindow.setPosition(latlng);
      infowindow.open(map, marker);

      // Zoom and pan to the marker when it's clicked
      map.setZoom(4);
      map.panTo(latlng);
    });

    return marker;
  }

  locations.forEach(function (location) {
    var marker = createMarker(
      new google.maps.LatLng(location[1], location[2]),
      location[0],
      location[3],
      location[4],
      location[6],
      location[8]
    );
    gmarkers.push(marker);
  });

  $("#zipcode-input").click(function () {
    //normalize zip code
    //var zip = $(this).val().trim().toUpperCase().replace(/\s+/g, "");
    var zip = $(this).val().trim();
    if (zip === "") {
      resetMap();
      // Scroll the container back to the top
      document.querySelector("#brxe-gehexg").scrollTo({
        top: 0,
        behavior: "smooth",
      });

      // Reset the background color of all highlighted elements
      var highlightedElements = document.querySelectorAll(".info_content");
      highlightedElements.forEach(function (element) {
        element.style.backgroundColor = "rgba(255, 255, 255, 0.1)";
      });
    }
  });

  function resetMap() {
    // Clear existing markers
    gmarkers.forEach(function (marker) {
      marker.setMap(null);
    });
    gmarkers = [];

    // Reset map to initial state
    map.setZoom(4);
    map.setCenter(new google.maps.LatLng(37.09024, -95.712891));

    // Create markers for each location in the locations array
    locations.forEach(function (location) {
      var marker = createMarker(
        new google.maps.LatLng(location[1], location[2]),
        location[0],
        location[3],
        location[4],
        location[6],
        location[8]
      );
      gmarkers.push(marker);
    });
  }
});
