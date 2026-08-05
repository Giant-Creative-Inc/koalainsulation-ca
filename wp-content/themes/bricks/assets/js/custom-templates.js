$(document).ready(function () {
  // Initially hide all accordion content except the first
  $(".acc-container4 .acc-content4").hide();
  $(".acc-container4 .acc4:first-child .acc-content4").show();
  $(".acc-container4 .acc4:first-child .acc-head4").addClass("active");
  $(
    ".acc-container4 .acc4:first-child .accordian-icon-wrapper .acc-icon-min"
  ).css("display", "block");
  $(
    ".acc-container4 .acc4:first-child .accordian-icon-wrapper .acc-icon-plus"
  ).css("display", "none");

  // Add click event for accordion headers
  $(".acc-head4").on("click", function () {
    const $accordionHead = $(this);
    const $accordionContent = $accordionHead.next(".acc-content4");
    const $iconPlus = $accordionHead.find(".acc-icon-plus");
    const $iconMin = $accordionHead.find(".acc-icon-min");

    if ($accordionHead.hasClass("active")) {
      // Collapse the currently active accordion
      $accordionContent.slideUp();
      $iconPlus.css("display", "block");
      $iconMin.css("display", "none");
      $accordionHead.removeClass("active");
    } else {
      // Collapse all accordions
      $(".acc-content4").slideUp();
      $(".acc-head4").removeClass("active");
      $(".acc-head4 .acc-icon-plus").css("display", "block");
      $(".acc-head4 .acc-icon-min").css("display", "none");

      // Expand the clicked accordion
      $accordionContent.slideDown();
      $iconPlus.css("display", "none");
      $iconMin.css("display", "block");
      $accordionHead.addClass("active");
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const servicesList = document.getElementById("usa-our-services");
  const servicesListCa = document.getElementById("ca-our-services");

  if (servicesList) {
    const servicesItems = servicesList.querySelectorAll(".service-item");

    servicesItems.forEach((item, index) => {
      if (index % 2 !== 0) {
        item.style.flexDirection = "row-reverse";
      }
    });
  }

  if (servicesListCa) {
    const servicesItems = servicesListCa.querySelectorAll(".service-item");

    servicesItems.forEach((item, index) => {
      if (index % 2 !== 0) {
        item.style.flexDirection = "row-reverse";
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  var locationHeroText = document.getElementById("location-hero-text");
  var readMoreBtn = document.getElementById("read-more-btn");
  var fullText = locationHeroText.innerHTML.trim(); // Grab the full text
  var charLimit = 200; // Set character limit for short text display

  if (fullText.length > charLimit) {
    var shortText = fullText.substring(0, charLimit) + "...";

    // Set initial shortened text
    locationHeroText.innerHTML = shortText;

    // Store the full text as a data attribute
    locationHeroText.setAttribute("data-full-text", fullText);
    locationHeroText.setAttribute("data-short-text", shortText);

    // Show the "Read More" button if the text is truncated
    readMoreBtn.style.display = "inline";
  }
});

function toggleText() {
  var locationHeroText = document.getElementById("location-hero-text");
  var fullText = locationHeroText.getAttribute("data-full-text");
  var shortText = locationHeroText.getAttribute("data-short-text");
  var btn = document.getElementById("read-more-btn");

  if (locationHeroText.innerHTML === shortText) {
    locationHeroText.innerHTML = fullText;
    btn.innerHTML = "Read Less";
  } else {
    locationHeroText.innerHTML = shortText;
    btn.innerHTML = "Read More";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const readMoreText = document.getElementById("read-more-text");
  const readMoreBtn = document.getElementById("read-more-btn1");

  if (!readMoreText || readMoreText.children.length === 0) {
    readMoreBtn.style.display = "none";
  } else {
    readMoreBtn.addEventListener("click", () => {
      const moreText = document.getElementById("more-text");
      readMoreText.style.display = "block";
      readMoreBtn.style.display = "none";
    });
  }
});

$(document).ready(function () {
  // Initially hide all accordion content (none expanded by default)
  $(".acc-container5 .acc-content5").hide();
  $(".acc-container5 .acc-head5").removeClass("active");
  $(".acc-container5 .acc-icon-min").css("display", "none");
  $(".acc-container5 .acc-icon-plus").css("display", "block");

  // Add click event for accordion headers
  $(".acc-head5").on("click", function () {
    const $accordionHead = $(this);
    const $accordionContent = $accordionHead.next(".acc-content5");
    const $iconPlus = $accordionHead.find(".acc-icon-plus");
    const $iconMin = $accordionHead.find(".acc-icon-min");

    if ($accordionHead.hasClass("active")) {
      // Collapse the currently active accordion
      $accordionContent.slideUp();
      $iconPlus.css("display", "block");
      $iconMin.css("display", "none");
      $accordionHead.removeClass("active");
    } else {
      // Collapse all accordions
      $(".acc-content5").slideUp();
      $(".acc-head5").removeClass("active");
      $(".acc-head5 .acc-icon-plus").css("display", "block");
      $(".acc-head5 .acc-icon-min").css("display", "none");

      // Expand the clicked accordion
      $accordionContent.slideDown();
      $iconPlus.css("display", "none");
      $iconMin.css("display", "block");
      $accordionHead.addClass("active");
    }
  });
});

