/**
 * sliders.js
 *
 * Swiper slider initialisers extracted from all-pages.js (CA theme).
 * Loaded conditionally (with the swiper-bundle CDN library) only where custom
 * slider markup renders. Contains: photo slider, before/after slider, services
 * slider, and the partner logo slider.
 *
 * Depends on: jQuery, Swiper (swiper-bundle).
 *
 * The three number-slider blocks keep their original jQuery(document).ready
 * wrapper, which binds $ = jQuery locally — so this module does not rely on the
 * global `$` that all-pages.js sets, and load order does not matter.
 *
 * Extracted 2026-08-19; logic is byte-identical to the original blocks in
 * all-pages.js (see all-pages.js.bak-20260819-ca-port).
 */

jQuery(document).ready(function ($) {

  // swiper slider photos
function numberWithZero(num) {
  if (num < 10) {
    return "0" + num;
  } else {
    return num;
  }
}

  $(".slider-photo-wrapper").each(function (index) {
    let totalSlides = numberWithZero(
      $(this).find(".swiper-slide.is-slider-main-slide").length
    );
    $(".swiper-number-total").text(totalSlides);
    let loopMode = false;
    if ($(this).attr("loop-mode") === "true") {
      loopMode = true;
    }
    let sliderDuration = 300;
    if ($(this).attr("slider-duration") !== undefined) {
      sliderDuration = +$(this).attr("slider-duration");
    }
    const swiper = new Swiper($(this).find(".swiper")[0], {
      speed: 1000,
      loop: true,
      autoHeight: false,
      centeredSlides: loopMode,
      followFinger: true,
      freeMode: false,
      slideToClickedSlide: false,
      parallax: true,
      slidesPerView: 1,
      spaceBetween: "8%",
      rewind: false,
      mousewheel: {
        forceToAxis: true,
      },
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },
      breakpoints: {
        // mobile landscape
        480: {
          slidesPerView: 1,
          spaceBetween: 24,
        },
        // tablet
        768: {
          slidesPerView: 2,
          spaceBetween: 24,
        },
        // desktop
        992: {
          slidesPerView: 2,
          spaceBetween: 24,
        },
      },
      pagination: {
        el: $(this).find(".swiper-bullet-wrapper")[0],
        bulletActiveClass: "is-active",
        bulletClass: "swiper-bullet",
        bulletElement: "button",
        clickable: true,
      },
      navigation: {
        nextEl: $(this).find(".swiper-next")[0],
        prevEl: $(this).find(".swiper-prev")[0],
        disabledClass: "is-disabled",
      },
      scrollbar: {
        el: $(this).find(".swiper-drag-wrapper")[0],
        draggable: true,
        dragClass: "swiper-drag",
        snapOnRelease: true,
      },
      slideActiveClass: "is-active",
      slideDuplicateActiveClass: "is-active",
    });

    swiper.on("slideChange", function (e) {
      //console.log(swiper.realIndex);
      let slidenumber = numberWithZero(e.realIndex + 1);
      $(".swiper-number-current").text(slidenumber);
    });
  });

  // swiper slider why reinsulate before after photos

  function numberWithZero(num) {
    if (num < 10) {
      return "0" + num;
    } else {
      return num;
    }
  }

  $(".slider-before-after-wrapper").each(function (index) {
    let totalSlides = numberWithZero(
      $(this).find(".swiper-slide.is-slider-main-slide").length
    );
    $(".swiper-number-total").text(totalSlides);
    let loopMode = false;
    if ($(this).attr("loop-mode") === "true") {
      loopMode = true;
    }
    let sliderDuration = 300;
    if ($(this).attr("slider-duration") !== undefined) {
      sliderDuration = +$(this).attr("slider-duration");
    }
    const swiper = new Swiper($(this).find(".swiper")[0], {
      speed: 1000,
      loop: true,
      autoHeight: false,
      centeredSlides: loopMode,
      followFinger: true,
      freeMode: false,
      slideToClickedSlide: false,
      parallax: true,
      slidesPerView: 1,
      spaceBetween: "8%",
      rewind: false,
      mousewheel: {
        forceToAxis: true,
      },
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },
      breakpoints: {
        // mobile landscape
        480: {
          slidesPerView: 1,
          spaceBetween: 80,
        },
        // tablet
        768: {
          slidesPerView: 1,
          spaceBetween: 80,
        },
        // desktop
        992: {
          slidesPerView: 1,
          spaceBetween: 80,
        },
      },
      pagination: {
        el: $(this).find(".swiper-bullet-wrapper")[0],
        bulletActiveClass: "is-active",
        bulletClass: "swiper-bullet",
        bulletElement: "button",
        clickable: true,
      },
      navigation: {
        nextEl: $(this).find(".swiper-next")[0],
        prevEl: $(this).find(".swiper-prev")[0],
        disabledClass: "is-disabled",
      },
      scrollbar: {
        el: $(this).find(".swiper-drag-wrapper")[0],
        draggable: true,
        dragClass: "swiper-drag",
        snapOnRelease: true,
      },
      slideActiveClass: "is-active",
      slideDuplicateActiveClass: "is-active",
    });

    swiper.on("slideChange", function (e) {
      //console.log(swiper.realIndex);
      let slidenumber = numberWithZero(e.realIndex + 1);
      $(".swiper-number-current").text(slidenumber);
    });
  });

  // swiper slider services
  function numberWithZero(num) {
    if (num < 10) {
      return "0" + num;
    } else {
      return num;
    }
  }

  $(".services-slider-main-component").each(function (index) {
    let totalSlides = numberWithZero(
      $(this).find(".service-card-home.swiper-slide").length
    );
    $(".swiper-number-total").text(totalSlides);
    let loopMode = false;
    if ($(this).attr("loop-mode") === "true") {
      loopMode = true;
    }
    let sliderDuration = 300;
    if ($(this).attr("slider-duration") !== undefined) {
      sliderDuration = +$(this).attr("slider-duration");
    }
    const swiper = new Swiper($(this).find(".swiper")[0], {
      speed: 1000,
      loop: true,
      autoHeight: false,
      centeredSlides: false,
      followFinger: true,
      freeMode: false,
      slideToClickedSlide: false,
      parallax: true,
      slidesPerView: 1,
      spaceBetween: "8%",
      rewind: false,
      mousewheel: {
        forceToAxis: true,
      },
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },
      breakpoints: {
        // mobile landscape
        480: {
          slidesPerView: 1,
          spaceBetween: 24,
          slidesPerGroup: 1,
        },
        // tablet
        768: {
          slidesPerView: 2,
          spaceBetween: 24,
          slidesPerGroup: 2,
        },
        // desktop
        992: {
          slidesPerView: 3,
          slidesPerGroup: 3,
          spaceBetween: 24,
        },
      },
      pagination: {
        el: $(this).find(".swiper-bullet-wrapper")[0],
        bulletActiveClass: "is-active",
        bulletClass: "swiper-bullet",
        bulletElement: "button",
        clickable: true,
      },
      navigation: {
        nextEl: $(this).find(".swiper-next")[0],
        prevEl: $(this).find(".swiper-prev")[0],
        disabledClass: "is-disabled",
      },
      scrollbar: {
        el: $(this).find(".swiper-drag-wrapper")[0],
        draggable: true,
        dragClass: "swiper-drag",
        snapOnRelease: true,
      },
      slideActiveClass: "is-active",
      slideDuplicateActiveClass: "is-active",
    });

    swiper.on("slideChange", function (e) {
      //console.log(swiper.realIndex);
      let slidenumber = numberWithZero(e.realIndex + 1);
      $(".swiper-number-current").text(slidenumber);
    });
  });
});

// swiper slider partners (responsive create/destroy)
document.addEventListener("DOMContentLoaded", function () {
  let swiper = null;

  function initSwiper() {
    const container = document.querySelector('.swiper-partner-wrapper');
    if (!container) return;

    const screenWidth = window.innerWidth;
    const slideCount = container.querySelectorAll('.swiper-slide').length;
    
    let slots = 5; 
    if (screenWidth < 768) slots = 2;
    else if (screenWidth < 1024) slots = 3;

    if (slideCount > slots) {
      if (!swiper) {
        swiper = new Swiper('.swiper-partner-wrapper', {
          loop: true,
          slidesPerView: 'auto', // Allows logos to stay their natural size
          spaceBetween: 60,      // Healthy spacing between logos
          centeredSlides: false,
          autoplay: { delay: 3000, disableOnInteraction: false },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          }
        });
      }
    } else if (swiper) {
      swiper.destroy(true, true);
      swiper = null;
    }
  }

  initSwiper();
  window.addEventListener('resize', initSwiper);
});
