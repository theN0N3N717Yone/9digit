"use strict";
let menu, animate;

!function () {
  document.querySelectorAll("#layout-menu").forEach(function (e) {
    menu = new Menu(e, { orientation: "vertical", closeChildren: !1 });
    window.Helpers.scrollToActive(animate = !1);
    window.Helpers.mainMenu = menu;
  });

  document.querySelectorAll(".layout-menu-toggle").forEach(e => {
    e.addEventListener("click", e => {
      e.preventDefault();
      window.Helpers.toggleCollapsed();
    });
  });

  if (document.getElementById("layout-menu")) {
    var t = document.getElementById("layout-menu");
    var l = function () { Helpers.isSmallScreen() || document.querySelector(".layout-menu-toggle").classList.add("d-block") };
    let e = null;

    t.onmouseenter = function () {
      e = Helpers.isSmallScreen() ? setTimeout(l, 0) : setTimeout(l, 300);
    };

    t.onmouseleave = function () {
      document.querySelector(".layout-menu-toggle").classList.remove("d-block");
      clearTimeout(e);
    };
  }

  let e = document.getElementsByClassName("menu-inner"),
      o = document.getElementsByClassName("menu-inner-shadow")[0];

  0 < e.length && o && e[0].addEventListener("ps-scroll-y", function () {
    this.querySelector(".ps__thumb-y").offsetTop ? o.style.display = "block" : o.style.display = "none";
  });

  [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (e) {
    return new bootstrap.Tooltip(e);
  });

  function n(e) {
    "show.bs.collapse" == e.type || "show.bs.collapse" == e.type ? e.target.closest(".accordion-item").classList.add("active") : e.target.closest(".accordion-item").classList.remove("active");
  }

  [].slice.call(document.querySelectorAll(".accordion")).map(function (e) {
    e.addEventListener("show.bs.collapse", n);
    e.addEventListener("show.bs.collapse", n);
  });

  window.Helpers.setAutoUpdate(!0);
  window.Helpers.initPasswordToggle();
  window.Helpers.initSpeechToText();

  // Check if the zoom level is 100%
  if (window.devicePixelRatio === 1) {
    window.Helpers.setCollapsed(false, false); // Set menu to be open
  } else {
    window.Helpers.setCollapsed(true, false); // Set menu to be closed
  }
}();