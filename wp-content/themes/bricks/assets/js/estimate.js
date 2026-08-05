/**
 * Estimate Form With Leads CRM integration
 */
// const locations = estimateData.zip_locations || [];
// console.log('Estimate Form Location:', locations);

function getCookie(name) {
    return document.cookie.split('; ').find(row => row.startsWith(name + '='))?.split('=')[1] || '';
}

let utm_source   = getCookie('utm_source');
let utm_medium   = getCookie('utm_medium');
let utm_campaign = getCookie('utm_campaign');


document.addEventListener("DOMContentLoaded", function () {
  const estimateForm = document.getElementById("estimateForm-custom");
  if (!estimateForm) {
    return;
  }

  const submitButton = estimateForm
    ? estimateForm.querySelector('button[type="submit"], input[type="submit"]')
    : "";

  // Ensure the form is bound only once
  if (!estimateForm.dataset.bound) {
    estimateForm.dataset.bound = "true";

    // Attach submit event listener
    estimateForm.addEventListener("submit", function (event) {
      event.preventDefault();

      // Disable button
      submitButton.disabled = true;

      // Create and insert spinner
      spinner = document.createElement("span");
      spinner.classList.add("loading-spinner");
      submitButton.parentNode.insertBefore(spinner, submitButton.nextSibling);

      const zip = document.getElementById("zip-custom").value.trim();
      // const locations = document.querySelectorAll(".single-location");
      const locations = estimateData.zip_locations || [];
      //const webhook_url = locations['webhook_url'] || '';
      // console.log('Estimate Form Locations' , locations);
      let matchFound = false;
      let locationUrl = "";

      // function the uses the location webhook to send information to zapier
      function sendToZapier(zapPayload) {
        console.log("in sendToZapier", zapPayload);
        try {
          // keep payload small; URLSearchParams suits WP AJAX nicely
          const body = new URLSearchParams(zapPayload);

          fetch(estimateData.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body: body.toString(),
            credentials: "same-origin",
            keepalive: true,
          }).catch(() => {}); // don’t block UX if it fails
        } catch (e) {
          console.warn("sendToZapier failed:", e);
        }
      }

      // Reusable function to submit form via AJAX
      function submitForm(
        key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        let checkbox = document.getElementById("consent");
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_form",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              if (webhook_url !== "") {
                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  webhook: webhook_url, // only if you must

                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  DoNotText: consent,
                  DoNotEmail: consentEmail,
                  key,
                  utm_source,
                  utm_medium,
                  utm_campaign,
                  utm_term,
                  utm_content,
                  page_url: locationUrl,
                };
                sendToZapier(zapPayload); // fire the server-side webhook
              }
              const pathParts = window.location.pathname
                .split("/")
                .filter(Boolean);

              let locationBase;

              if (pathParts.length >= 2) {
                locationBase = `/${pathParts[0]}/${pathParts[1]}`;
              } else if (pathParts.length === 1) {
                locationBase = `/${pathParts[0]}`; // just "/ca"
              } else {
                locationBase = ""; // root or fallback
              }

              // Then build the final redirect URL carefully:
              const redirectUrl = locationBase
                ? locationBase + "/thank-you"
                : "/thank-you";
              console.log("Redirecting to:", redirectUrl + query);

              window.location.href = redirectUrl + query;

              document.getElementById("estimateForm-custom").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSm(
        key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;
        let checkbox = document.getElementById("consent");
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_sm_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_sm_form",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            try {
              const message = result?.data?.message;
              const statusCode = result?.data?.status_code;
              const resultCode = result?.data?.response?.ResultCode;
              console.log(result?.data);
              // Ensure the response structure is correct
              if (
                resultCode === 0 ||
                (message == "Form submitted successfully." &&
                  statusCode === 200)
              ) {
                if (webhook_url !== "") {
                  const zapPayload = {
                    action: "handle_zapier_webhook",
                    nonce: estimateData.estimate_form_nonce,
                    webhook: webhook_url, // only if you must

                    first_name: firstName,
                    last_name: lastName,
                    email,
                    mobile_number: phone,
                    address1,
                    address2,
                    city,
                    state,
                    zip: zipValue,
                    DoNotText: consent,
                    DoNotEmail: consentEmail,
                    key,
                    utm_source,
                    utm_medium,
                    utm_campaign,
                    utm_term,
                    utm_content,
                    page_url: locationUrl,
                  };
                  sendToZapier(zapPayload); // fire the server-side webhook
                }

                document.getElementById("estimateForm").reset();
                const pathParts = window.location.pathname
                  .split("/")
                  .filter(Boolean);

                let locationBase;

                if (pathParts.length >= 2) {
                  locationBase = `/${pathParts[0]}/${pathParts[1]}`;
                } else if (pathParts.length === 1) {
                  locationBase = `/${pathParts[0]}`; // just "/ca"
                } else {
                  locationBase = ""; // root or fallback
                }

                // Then build the final redirect URL carefully:
                const redirectUrl = locationBase
                  ? locationBase + "/thank-you"
                  : "/thank-you";
                console.log("Redirecting to:", redirectUrl + query);

                window.location.href = redirectUrl + query;
              } else {
                // Response code is not 200
                console.error(
                  "Invalid ResultCode:",
                  result?.data?.response?.ResultCode,
                );
                showError("Submission failed. Please try again.");
              }
            } catch (error) {
              // Catch and log parsing or runtime errors
              console.error("An error occurred:", error);
              // Re-enable button
              submitButton.disabled = false;

              // Remove spinner
              if (spinner) {
                spinner.remove();
                spinner = null;
              }
              showError("An error occurred while processing your request.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSmWithHcp(
        key,
        sm_key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        let checkbox = document.getElementById("consent");
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          sm_key: sm_key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          recaptcha_token: recaptchaToken,
          action: "handle_both_submissions",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              if (webhook_url !== "") {
                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  webhook: webhook_url, // only if you must
                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  DoNotText: consent,
                  DoNotEmail: consentEmail,
                  key,
                  utm_source,
                  utm_medium,
                  utm_campaign,
                  utm_term,
                  utm_content,
                  page_url: locationUrl,
                };
                sendToZapier(zapPayload); // fire the server-side webhook
              }
              const pathParts = window.location.pathname
                .split("/")
                .filter(Boolean);

              let locationBase;

              if (pathParts.length >= 2) {
                locationBase = `/${pathParts[0]}/${pathParts[1]}`;
              } else if (pathParts.length === 1) {
                locationBase = `/${pathParts[0]}`; // just "/ca"
              } else {
                locationBase = ""; // root or fallback
              }

              // Then build the final redirect URL carefully:
              const redirectUrl = locationBase
                ? locationBase + "/thank-you"
                : "/thank-you";
              console.log("Redirecting to:", redirectUrl + query);

              window.location.href = redirectUrl + query;

              document.getElementById("estimateForm-custom").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Function to display error message
      function showError(message) {
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.textContent = message;
        errorMessage.style.display = "block";

        setTimeout(() => {
          errorMessage.style.display = "none";
        }, 3000);
      }

      // Check ZIP code match in locations
      // locations.forEach(function (location) {
      //   const zipcode = location.querySelector(".zipcode").textContent.trim();
      //   const additionalZipcodes = location
      //     .querySelector(".additional-zipcodes")
      //     .textContent.trim()
      //     .split(/\s*,\s*/);

      //   if (zipcode === zip || additionalZipcodes.includes(zip)) {
      //     const locationHcpKey = location
      //       .querySelector(".location-key")
      //       .textContent.trim();
      //     const locationSmKey = location
      //       .querySelector(".location-service-minder-key")
      //       .textContent.trim();
      //     locationUrl = location
      //       .querySelector(".place-link")
      //       .textContent.trim();

      //     if (locationHcpKey && locationSmKey) {
      //       submitFormSmWithHcp(
      //         locationHcpKey,
      //         locationSmKey,
      //         zip,
      //         locationUrl
      //       );
      //       console.log("Both keys found.");
      //     } else if (locationHcpKey && !locationSmKey) {
      //       submitForm(locationHcpKey, zip, locationUrl);
      //     } else if (locationSmKey && !locationHcpKey) {
      //       submitFormSm(locationSmKey, zip, locationUrl);
      //     } else {
      //       console.warn("Keys are empty for this ZIP code.");
      //     }

      //     matchFound = true;
      //   }
      // });

      grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise
          .execute("6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt", {
            action: "submit",
          })
          .then(function (token) {
            // Now you have the reCAPTCHA token
            // Pass it into your form submission logic

            locations.forEach(function (location) {
              const {
                zip: primaryZip,
                additional_zips,
                hcp_key,
                sm_key,
                url,
                webhook_url,
              } = location;

              if (primaryZip === zip || additional_zips.includes(zip)) {
                if (hcp_key && sm_key) {
                  submitFormSmWithHcp(
                    hcp_key,
                    sm_key,
                    zip,
                    url,
                    token,
                    webhook_url,
                  );
                } else if (hcp_key && !sm_key) {
                  submitForm(hcp_key, zip, url, token, webhook_url);
                } else if (sm_key && !hcp_key) {
                  submitFormSm(sm_key, zip, url, token, webhook_url);
                } else {
                  console.warn("Both keys are empty for this ZIP code.");
                }
                matchFound = true;
              }
            });

            // If no match found, submit with input field value (id="key") and ZIP code
            if (!matchFound) {
              const firstName = document.getElementById("fName-custom").value;
              const lastName = document.getElementById("lName-custom").value;
              const email = document.getElementById("email-custom").value;
              const phone = document.getElementById("phone-custom").value;
              const address1 = document.getElementById("address1-custom").value;
              const address2 = document.getElementById("address2-custom").value;
              const city = document.getElementById("city-custom").value;
              const state = document.getElementById("state-custom").value;
              const zipValue = document.getElementById("zip-custom").value;
              let checkbox = document.getElementById("consent");
              const consent_email = estimateForm.querySelector('input[name="consent-email"]');
              const consent = !(checkbox && checkbox.checked);
              const consentEmail = !(consent_email && consent_email.checked);
              const key = document.getElementById("key-custom").value;
              const keySm = document.getElementById("key-custom-sm").value;
              locationUrl = document.getElementById("url-custom").value;
              const utm_source = document.getElementById("utm_source").value;
              const utm_medium = document.getElementById("utm_medium").value;
              const utm_campaign =
                document.getElementById("utm_campaign").value;
              const utm_term = document.getElementById("utm_term").value;
              const utm_content = document.getElementById("utm_content").value;

              const zapPayload = {
                action: "handle_zapier_webhook",
                nonce: "",
                webhook: estimateData.acf_webhook_url || "",
                first_name: firstName,
                last_name: lastName,
                email,
                mobile_number: phone,
                address1,
                address2,
                city,
                state,
                zip: zipValue,
                utm_source,
                utm_medium,
                utm_campaign,
                utm_term,
                utm_content,
                DoNotText: consent,
                DoNotEmail: consentEmail,
                key,
                page_url: locationUrl,
              };
              sendToZapier(zapPayload); // fire the server-side webhook

              if (key !== "" && keySm !== "") {
                submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
                console.log("both found");
              } else if (key !== "" && keySm === "") {
                submitForm(key, zip, locationUrl, token);
              } else if (keySm !== "" && key === "") {
                submitFormSm(keySm, zip, locationUrl, token);
              } else {
                console.warn("Keys are empty.");
              }
            }
          })
          .catch(function () {
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError(
              "reCAPTCHA verification failed. Please refresh and try again.",
            );
          });
      });
    });

    // Phone input formatting
    document
      .getElementById("phone-custom")
      .addEventListener("input", function (e) {
        const x = e.target.value
          .replace(/\D/g, "")
          .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2]
          ? x[1]
          : `(${x[1]}) ${x[2]}${x[3] ? `-${x[3]}` : ""}`;
      });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const estimateForm = document.getElementById("estimateForm");
  if (!estimateForm) {
    return;
  }
  const submitButton = estimateForm.querySelector(
    'button[type="submit"], input[type="submit"]',
  );

  // Ensure the form is bound only once
  if (!estimateForm.dataset.bound) {
    estimateForm.dataset.bound = "true";

    // Attach submit event listener
    estimateForm.addEventListener("submit", function (event) {
      event.preventDefault();

      // Disable button
      submitButton.disabled = true;

      // Create and insert spinner
      spinner = document.createElement("span");
      spinner.classList.add("loading-spinner");
      submitButton.parentNode.insertBefore(spinner, submitButton.nextSibling);
      console.log("Submit click");
      const zip = document.getElementById("zip").value.trim();
      // const locations = document.querySelectorAll(".single-location");
      const locations = estimateData.zip_locations || [];
      let matchFound = false;
      let locationUrl = "";

      // function the uses the location webhook to send information to zapier
      function sendToZapier(zapPayload) {
        try {
          // keep payload small; URLSearchParams suits WP AJAX nicely
          const body = new URLSearchParams(zapPayload);

          fetch(estimateData.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body: body.toString(),
            credentials: "same-origin",
            keepalive: true,
          }).catch(() => {}); // don’t block UX if it fails
        } catch (e) {
          console.warn("sendToZapier failed:", e);
        }
      }
      // Reusable function to submit form via AJAX
      function submitForm(
        key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName").value;
        const lastName = document.getElementById("lName").value;
        const email = document.getElementById("email").value;
        const phone = document.getElementById("phone").value;
        const address1 = document.getElementById("address1").value;
        const address2 = document.getElementById("address2").value;
        const city = document.getElementById("city").value;
        const state = document.getElementById("state").value;
        const consent = document.getElementById("consent").checked;
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_form",
        };

        document.getElementById("errorMessage").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              if (webhook_url !== "") {
                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  webhook: webhook_url, // only if you must

                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  utm_source,
                  utm_medium,
                  utm_campaign,
                  utm_term,
                  utm_content,
                  DoNotText: consent,
                  DoNotEmail: consentEmail,
                  key,
                  page_url: locationUrl,
                };
                sendToZapier(zapPayload); // fire the server-side webhook
              }
              const pathParts = window.location.pathname
                .split("/")
                .filter(Boolean);

              let locationBase;

              if (pathParts.length >= 2) {
                locationBase = `/${pathParts[0]}/${pathParts[1]}`;
              } else if (pathParts.length === 1) {
                locationBase = `/${pathParts[0]}`; // just "/ca"
              } else {
                locationBase = ""; // root or fallback
              }

              // Then build the final redirect URL carefully:
              const redirectUrl = locationBase
                ? locationBase + "/thank-you"
                : "/thank-you";
              console.log("Redirecting to:", redirectUrl + query);

              window.location.href = redirectUrl + query;

              document.getElementById("estimateForm").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Reusable function to submit form via AJAX
      function submitFormSm(
        key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName").value;
        const lastName = document.getElementById("lName").value;
        const email = document.getElementById("email").value;
        const phone = document.getElementById("phone").value;
        const address1 = document.getElementById("address1").value;
        const address2 = document.getElementById("address2").value;
        const city = document.getElementById("city").value;
        const state = document.getElementById("state").value;
        const consent = document.getElementById("consent").checked;
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_sm_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_sm_form",
        };

        document.getElementById("errorMessage").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            console.log("Full result:", result);
            try {
              const message = result?.data?.message;
              const statusCode = result?.data?.status_code;
              const resultCode = result?.data?.response?.ResultCode;
              console.log(result?.data);
              // Ensure the response structure is correct
              if (
                resultCode === 0 ||
                (message == "Form submitted successfully." &&
                  statusCode === 200)
              ) {
                if (webhook_url !== "") {
                  const zapPayload = {
                    action: "handle_zapier_webhook",
                    nonce: estimateData.estimate_form_nonce,
                    webhook: webhook_url, // only if you must

                    first_name: firstName,
                    last_name: lastName,
                    email,
                    mobile_number: phone,
                    address1,
                    address2,
                    city,
                    state,
                    zip: zipValue,
                    utm_source,
                    utm_medium,
                    utm_campaign,
                    utm_term,
                    utm_content,
                    DoNotText: consent,
                    DoNotEmail: consentEmail,
                    key,
                    page_url: locationUrl,
                  };
                  sendToZapier(zapPayload); // fire the server-side webhook
                }
                document.getElementById("estimateForm").reset();
                const pathParts = window.location.pathname
                  .split("/")
                  .filter(Boolean);

                let locationBase;

                if (pathParts.length >= 2) {
                  locationBase = `/${pathParts[0]}/${pathParts[1]}`;
                } else if (pathParts.length === 1) {
                  locationBase = `/${pathParts[0]}`; // just "/ca"
                } else {
                  locationBase = ""; // root or fallback
                }

                // Then build the final redirect URL carefully:
                const redirectUrl = locationBase
                  ? locationBase + "/thank-you"
                  : "/thank-you";
                console.log("Redirecting to:", redirectUrl + query);

                window.location.href = redirectUrl + query;
              } else {
                // Response code is not 200
                console.error(
                  "Invalid ResultCode:",
                  result?.data?.response?.ResultCode,
                );
                showError("Submission failed. Please try again.");
              }
            } catch (error) {
              // Catch and log parsing or runtime errors
              console.error("An error occurred:", error);

              // Re-enable button
              submitButton.disabled = false;

              // Remove spinner
              if (spinner) {
                spinner.remove();
                spinner = null;
              }
              showError("An error occurred while processing your request.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSmWithHcp(
        key,
        sm_key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName").value;
        const lastName = document.getElementById("lName").value;
        const email = document.getElementById("email").value;
        const phone = document.getElementById("phone").value;
        const address1 = document.getElementById("address1").value;
        const address2 = document.getElementById("address2").value;
        const city = document.getElementById("city").value;
        const state = document.getElementById("state").value;
        const consent = document.getElementById("consent").checked;
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          sm_key: sm_key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          recaptcha_token: recaptchaToken,
          action: "handle_both_submissions",
        };

        document.getElementById("errorMessage").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              if (webhook_url !== "") {
                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  webhook: webhook_url, // only if you must

                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  utm_source,
                  utm_medium,
                  utm_campaign,
                  utm_term,
                  utm_content,
                  DoNotText: consent,
                  DoNotEmail: consentEmail,
                  key,
                  page_url: locationUrl,
                };
                sendToZapier(zapPayload); // fire the server-side webhook
              }
              const pathParts = window.location.pathname
                .split("/")
                .filter(Boolean);

              let locationBase;

              if (pathParts.length >= 2) {
                locationBase = `/${pathParts[0]}/${pathParts[1]}`;
              } else if (pathParts.length === 1) {
                locationBase = `/${pathParts[0]}`; // just "/ca"
              } else {
                locationBase = ""; // root or fallback
              }

              // Then build the final redirect URL carefully:
              const redirectUrl = locationBase
                ? locationBase + "/thank-you"
                : "/thank-you";
              console.log("Redirecting to:", redirectUrl + query);

              window.location.href = redirectUrl + query;

              document.getElementById("estimateForm").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Function to display error message
      function showError(message) {
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.textContent = message;
        errorMessage.style.display = "block";

        setTimeout(() => {
          errorMessage.style.display = "none";
        }, 3000);
      }

      // Check ZIP code match in locations
      // locations.forEach(function (location) {
      //     const zipcode = location.querySelector(".zipcode").textContent.trim();
      //     const additionalZipcodes = location
      //         .querySelector(".additional-zipcodes")
      //         .textContent.trim()
      //         .split(/\s*,\s*/);

      //     if (zipcode === zip || additionalZipcodes.includes(zip)) {
      //         const locationHcpKey = location
      //             .querySelector(".location-key")
      //             .textContent.trim();
      //         const locationSmKey = location
      //             .querySelector(".location-service-minder-key")
      //             .textContent.trim();
      //         locationUrl = location
      //             .querySelector(".place-link")
      //             .textContent.trim();

      //         if (locationHcpKey && locationSmKey) {
      //             submitFormSmWithHcp(
      //                 locationHcpKey,
      //                 locationSmKey,
      //                 zip,
      //                 locationUrl
      //             );
      //             console.log("Both keys found.");
      //         } else if (locationHcpKey && !locationSmKey) {
      //             submitForm(locationHcpKey, zip, locationUrl);
      //         } else if (locationSmKey && !locationHcpKey) {
      //             submitFormSm(locationSmKey, zip, locationUrl);
      //         } else {
      //             console.warn("Keys are empty for this ZIP code.");
      //         }

      //         matchFound = true;
      //     }
      // });
      grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise
          .execute("6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt", {
            action: "submit",
          })
          .then(function (token) {
            console.log({ token });
            let matchFound = false;

            locations.forEach(function (location) {
              const {
                zip: primaryZip,
                additional_zips,
                hcp_key,
                sm_key,
                url,
                webhook_url,
              } = location;

              if (primaryZip === zip || additional_zips.includes(zip)) {
                if (hcp_key && sm_key) {
                  submitFormSmWithHcp(
                    hcp_key,
                    sm_key,
                    zip,
                    url,
                    token,
                    webhook_url,
                  );
                } else if (hcp_key && !sm_key) {
                  submitForm(hcp_key, zip, url, token, webhook_url);
                } else if (sm_key && !hcp_key) {
                  submitFormSm(sm_key, zip, url, token, webhook_url);
                } else {
                  console.warn("Both keys are empty for this ZIP code.");
                }
                matchFound = true;
              }
            });

            if (!matchFound) {
              // const firstName = document.getElementById("fName-custom").value;
              // const lastName = document.getElementById("lName-custom").value;
              // const email = document.getElementById("email-custom").value;
              // const phone = document.getElementById("phone-custom").value;
              // const address1 = document.getElementById("address1-custom").value;
              // const address2 = document.getElementById("address2-custom").value;
              // const city = document.getElementById("city-custom").value;
              // const state = document.getElementById("state-custom").value;
              // const zipValue = document.getElementById("zip-custom").value;
              const firstName = document.getElementById("fName").value;
              const lastName = document.getElementById("lName").value;
              const email = document.getElementById("email").value;
              const phone = document.getElementById("phone").value;
              const address1 = document.getElementById("address1").value;
              const address2 = document.getElementById("address2").value;
              const city = document.getElementById("city").value;
              const state = document.getElementById("state").value;
              const zipValue = document.getElementById("zip").value;
              let checkbox = document.getElementById("consent");
              const consent_email = estimateForm.querySelector('input[name="consent-email"]');
              const consent = !(checkbox && checkbox.checked);
              const consentEmail = !(consent_email && consent_email.checked);
              const key = document.getElementById("key").value;
              const keySm = document.getElementById("keySm").value;
              const locationUrl = document.getElementById("url").value;
              const utm_source = document.getElementById("utm_source").value;
              const utm_medium = document.getElementById("utm_medium").value;
              const utm_campaign =
                document.getElementById("utm_campaign").value;
              const utm_term = document.getElementById("utm_term").value;
              const utm_content = document.getElementById("utm_content").value;

              const zapPayload = {
                action: "handle_zapier_webhook",
                nonce: "",
                webhook: estimateData.acf_webhook_url || "",
                first_name: firstName,
                last_name: lastName,
                email,
                mobile_number: phone,
                address1,
                address2,
                city,
                state,
                zip: zipValue,
                utm_source,
                utm_medium,
                utm_campaign,
                utm_term,
                utm_content,
                DoNotText: consent,
                DoNotEmail: consentEmail,
                key,
                page_url: locationUrl,
              };
              sendToZapier(zapPayload); // fire the server-side webhook

              if (key !== "" && keySm !== "") {
                submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
                console.log("both found");
              } else if (key !== "" && keySm === "") {
                submitForm(key, zip, locationUrl, token);
              } else if (keySm !== "" && key === "") {
                submitFormSm(keySm, zip, locationUrl, token);
              } else {
                console.warn("Keys are empty.");
              }
            }
          })
          .catch(function () {
            showError(
              "reCAPTCHA verification failed. Please refresh and try again.",
            );
          });
      });
    });

    // Phone input formatting handled by global formatter below
  }
});

jQuery(function ($) {
  if ($("body").hasClass("single-location")) {
    // $("header #estimate-popup-custom").remove();
    $("#url-custom").attr("href", window.location.href);
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const estimateForm = document.getElementById("sidebar-form");
  if (!estimateForm) {
    return;
  }
  const submitButton = estimateForm.querySelector(
    'button[type="submit"], input[type="submit"]',
  );

  // Ensure the form is bound only once
  if (!estimateForm.dataset.bound) {
    estimateForm.dataset.bound = "true";

    // Attach submit event listener
    estimateForm.addEventListener("submit", function (event) {
      event.preventDefault();

      // Disable button
      submitButton.disabled = true;

      // Create and insert spinner
      spinner = document.createElement("span");
      spinner.classList.add("loading-spinner");
      submitButton.parentNode.insertBefore(spinner, submitButton.nextSibling);
      const zip = document.getElementById("zip-custom").value.trim();
      // const locations = document.querySelectorAll(".single-location");
      const locations = estimateData.zip_locations || [];
      // console.log('Estimate Form Locations' , locations);
      let matchFound = false;
      let locationUrl = "";

      // function the uses the location webhook to send information to zapier
      function sendToZapier(zapPayload) {
        try {
          // keep payload small; URLSearchParams suits WP AJAX nicely
          const body = new URLSearchParams(zapPayload);

          fetch(estimateData.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body: body.toString(),
            credentials: "same-origin",
            keepalive: true,
          }).catch(() => {}); // don’t block UX if it fails
        } catch (e) {
          console.warn("sendToZapier failed:", e);
        }
      }

      // Reusable function to submit form via AJAX
      function submitForm(
        key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        let checkbox = document.getElementById("consent");
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_form",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              if (webhook_url !== "") {
                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  webhook: webhook_url, // only if you must

                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  utm_source,
                  utm_medium,
                  utm_campaign,
                  utm_term,
                  utm_content,
                  DoNotText: consent,
                  DoNotEmail: consentEmail,
                  key,
                  page_url: locationUrl,
                };
                sendToZapier(zapPayload); // fire the server-side webhook
              }
              const pathParts = window.location.pathname
                .split("/")
                .filter(Boolean);

              let locationBase;

              if (pathParts.length >= 2) {
                locationBase = `/${pathParts[0]}/${pathParts[1]}`;
              } else if (pathParts.length === 1) {
                locationBase = `/${pathParts[0]}`; // just "/ca"
              } else {
                locationBase = ""; // root or fallback
              }

              // Then build the final redirect URL carefully:
              const redirectUrl = locationBase
                ? locationBase + "/thank-you"
                : "/thank-you";
              console.log("Redirecting to:", redirectUrl + query);

              window.location.href = redirectUrl + query;

              document.getElementById("sidebar-form").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSm(
        key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        let checkbox = document.getElementById("consent");
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          nonce: estimateData.estimate_sm_form_nonce,
          recaptcha_token: recaptchaToken,
          action: "submit_estimate_sm_form",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            try {
              const message = result?.data?.message;
              const statusCode = result?.data?.status_code;
              const resultCode = result?.data?.response?.ResultCode;
              console.log(result);
              console.log(result?.data);
              // Ensure the response structure is correct
              if (
                resultCode === 0 ||
                (message == "Form submitted successfully." &&
                  statusCode === 200)
              ) {
                if (webhook_url !== "") {
                  const zapPayload = {
                    action: "handle_zapier_webhook",
                    nonce: estimateData.estimate_form_nonce,
                    webhook: webhook_url, // only if you must

                    first_name: firstName,
                    last_name: lastName,
                    email,
                    mobile_number: phone,
                    address1,
                    address2,
                    city,
                    state,
                    zip: zipValue,
                    utm_source,
                    utm_medium,
                    utm_campaign,
                    utm_term,
                    utm_content,
                    DoNotText: consent,
                    DoNotEmail: consentEmail,
                    key,
                    page_url: locationUrl,
                  };
                  sendToZapier(zapPayload); // fire the server-side webhook
                }
                document.getElementById("sidebar-form").reset();
                // document.getElementById("estimateForm-custom").reset();
                const pathParts = window.location.pathname
                  .split("/")
                  .filter(Boolean);

                let locationBase;

                if (pathParts.length >= 2) {
                  locationBase = `/${pathParts[0]}/${pathParts[1]}`;
                } else if (pathParts.length === 1) {
                  locationBase = `/${pathParts[0]}`; // just "/ca"
                } else {
                  locationBase = ""; // root or fallback
                }

                // Then build the final redirect URL carefully:
                const redirectUrl = locationBase
                  ? locationBase + "/thank-you"
                  : "/thank-you";
                console.log("Redirecting to:", redirectUrl + query);

                window.location.href = redirectUrl + query;
              } else {
                // Response code is not 200
                console.error(
                  "Invalid ResultCode:",
                  result?.data?.response?.ResultCode,
                );
                showError("Submission failed. Please try again.");
              }
            } catch (error) {
              // Catch and log parsing or runtime errors
              console.error("An error occurred:", error);
              // Re-enable button
              submitButton.disabled = false;

              // Remove spinner
              if (spinner) {
                spinner.remove();
                spinner = null;
              }
              showError("An error occurred while processing your request.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      function submitFormSmWithHcp(
        key,
        sm_key,
        zipValue,
        locationUrl,
        recaptchaToken,
        webhook_url = "",
      ) {
        let baseUrl = window.location.origin + window.location.pathname;
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }
        let query = window.location.search; // Includes the "?" if it exists
        const firstName = document.getElementById("fName-custom").value;
        const lastName = document.getElementById("lName-custom").value;
        const email = document.getElementById("email-custom").value;
        const phone = document.getElementById("phone-custom").value;
        const address1 = document.getElementById("address1-custom").value;
        const address2 = document.getElementById("address2-custom").value;
        const city = document.getElementById("city-custom").value;
        const state = document.getElementById("state-custom").value;
        let checkbox = document.getElementById("consent");
        const consent_email = estimateForm.querySelector('input[name="consent-email"]');
        const consent = !(checkbox && checkbox.checked);
        const consentEmail = !(consent_email && consent_email.checked);
        const utm_source = document.getElementById("utm_source").value;
        const utm_medium = document.getElementById("utm_medium").value;
        const utm_campaign = document.getElementById("utm_campaign").value;
        const utm_term = document.getElementById("utm_term").value;
        const utm_content = document.getElementById("utm_content").value;

        const data = {
          first_name: firstName,
          last_name: lastName,
          email: email,
          mobile_number: phone,
          address1: address1,
          address2: address2,
          city: city,
          state: state,
          DoNotText: consent,
          DoNotEmail: consentEmail,
          zip: zipValue,
          key: key,
          sm_key: sm_key,
          UtmSource: utm_source,
          UtmMedium: utm_medium,
          UtmCampaign: utm_campaign,
          recaptcha_token: recaptchaToken,
          action: "handle_both_submissions",
        };

        document.getElementById("errorMessage-custom").style.display = "none";

        // Send AJAX request to WordPress backend
        fetch(estimateData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data).toString(),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result?.data?.status_code === 201) {
              if (webhook_url !== "") {
                const zapPayload = {
                  action: "handle_zapier_webhook",
                  nonce: estimateData.estimate_form_nonce,
                  webhook: webhook_url, // only if you must

                  first_name: firstName,
                  last_name: lastName,
                  email,
                  mobile_number: phone,
                  address1,
                  address2,
                  city,
                  state,
                  zip: zipValue,
                  utm_source,
                  utm_medium,
                  utm_campaign,
                  utm_term,
                  utm_content,
                  DoNotText: consent,
                  DoNotEmail: consentEmail,
                  key,
                  page_url: locationUrl,
                };
                sendToZapier(zapPayload); // fire the server-side webhook
              }
              const pathParts = window.location.pathname
                .split("/")
                .filter(Boolean);

              let locationBase;

              if (pathParts.length >= 2) {
                locationBase = `/${pathParts[0]}/${pathParts[1]}`;
              } else if (pathParts.length === 1) {
                locationBase = `/${pathParts[0]}`; // just "/ca"
              } else {
                locationBase = ""; // root or fallback
              }

              // Then build the final redirect URL carefully:
              const redirectUrl = locationBase
                ? locationBase + "/thank-you"
                : "/thank-you";
              console.log("Redirecting to:", redirectUrl + query);

              window.location.href = redirectUrl + query;

              document.getElementById("sidebar-form").reset();
            } else {
              showError("Submission failed. Please try again.");
            }
          })
          .catch((error) => {
            console.error("AJAX Error:", error);
            // Re-enable button
            submitButton.disabled = false;

            // Remove spinner
            if (spinner) {
              spinner.remove();
              spinner = null;
            }
            showError("An error occurred. Please try again.");
          });
      }

      // Function to display error message
      function showError(message) {
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.textContent = message;
        errorMessage.style.display = "block";

        setTimeout(() => {
          errorMessage.style.display = "none";
        }, 3000);
      }

      grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise
          .execute("6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt", {
            action: "submit",
          })
          .then(function (token) {
            // Now you have the reCAPTCHA token
            // Pass it into your form submission logic

            locations.forEach(function (location) {
              const {
                zip: primaryZip,
                additional_zips,
                hcp_key,
                sm_key,
                url,
                webhook_url,
              } = location;

              if (primaryZip === zip || additional_zips.includes(zip)) {
                if (hcp_key && sm_key) {
                  submitFormSmWithHcp(
                    hcp_key,
                    sm_key,
                    zip,
                    url,
                    token,
                    webhook_url,
                  );
                } else if (hcp_key && !sm_key) {
                  submitForm(hcp_key, zip, url, token, webhook_url);
                } else if (sm_key && !hcp_key) {
                  submitFormSm(sm_key, zip, url, token, webhook_url);
                } else {
                  console.warn("Both keys are empty for this ZIP code.");
                }
                matchFound = true;
              }
            });

            // If no match found, submit with input field value (id="key") and ZIP code
            if (!matchFound) {
              const firstName = document.getElementById("fName-custom").value;
              const lastName = document.getElementById("lName-custom").value;
              const email = document.getElementById("email-custom").value;
              const phone = document.getElementById("phone-custom").value;
              const address1 = document.getElementById("address1-custom").value;
              const address2 = document.getElementById("address2-custom").value;
              const city = document.getElementById("city-custom").value;
              const state = document.getElementById("state-custom").value;
              const zipValue = document.getElementById("zip-custom").value;
              let checkbox = document.getElementById("consent");
              const consent_email = estimateForm.querySelector('input[name="consent-email"]');
              const consent = !(checkbox && checkbox.checked);
              const consentEmail = !(consent_email && consent_email.checked);
              const key = document.getElementById("key-custom").value;
              const keySm = document.getElementById("key-custom-sm").value;
              locationUrl = document.getElementById("url-custom").value;
              locationUrl = window.location.href;
              const utm_source = document.getElementById("utm_source").value;
              const utm_medium = document.getElementById("utm_medium").value;
              const utm_campaign =
                document.getElementById("utm_campaign").value;
              const utm_term = document.getElementById("utm_term").value;
              const utm_content = document.getElementById("utm_content").value;

              const zapPayload = {
                action: "handle_zapier_webhook",
                nonce: "",
                webhook: estimateData.acf_webhook_url || "",
                first_name: firstName,
                last_name: lastName,
                email,
                mobile_number: phone,
                address1,
                address2,
                city,
                state,
                zip: zipValue,
                utm_source,
                utm_medium,
                utm_campaign,
                utm_term,
                utm_content,
                DoNotText: consent,
                DoNotEmail: consentEmail,
                key,
                page_url: locationUrl,
              };
              sendToZapier(zapPayload); // fire the server-side webhook

              if (key !== "" && keySm !== "") {
                submitFormSmWithHcp(key, keySm, zip, locationUrl, token);
              } else if (key !== "" && keySm === "") {
                submitForm(key, zip, locationUrl, token);
              } else if (keySm !== "" && key === "") {
                submitFormSm(keySm, zip, locationUrl, token);
              } else {
                console.warn("Keys are empty.");
              }
            }
          })
          .catch(function () {
            showError(
              "reCAPTCHA verification failed. Please refresh and try again.",
            );
          });
      });
    });

    // Phone input formatting
    document
      .getElementById("phone-custom")
      .addEventListener("input", function (e) {
        const x = e.target.value
          .replace(/\D/g, "")
          .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2]
          ? x[1]
          : `(${x[1]}) ${x[2]}${x[3] ? `-${x[3]}` : ""}`;
      });
  }
});


// Global Phone Formatter (Handles Sidebar & Popups)
document.addEventListener("input", function (e) {
  // Target only our specific phone fields
  if (e.target && (e.target.id === "phone" || e.target.id === "phone-custom")) {
    let input = e.target.value.replace(/\D/g, "");
    if (input.startsWith("1")) input = input.substring(1);
    input = input.substring(0, 10);

    const areaCode = input.substring(0, 3);
    const middle = input.substring(3, 6);
    const last = input.substring(6, 10);

    let formatted = "";
    if (input.length === 0) {
      formatted = "";
    } else if (input.length <= 3) {
      formatted = `(${areaCode}`;
    } else if (input.length <= 6) {
      formatted = `(${areaCode}) ${middle}`;
    } else {
      formatted = `(${areaCode}) ${middle}-${last}`;
    }

    e.target.value = formatted;
  }
});