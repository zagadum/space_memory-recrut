/*
 * Cookie Consent Alert
 * EU GDPR compliant cookie consent banner
 */
(function () {
    "use strict";

    var cookieAlert = document.querySelector(".cookiealert");
    var acceptBtn = document.querySelector(".acceptcookies");

    if (!cookieAlert) {
        return;
    }

    cookieAlert.offsetHeight; // Force reflow

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : '';
    }

    function setCookie(name, value, days) {
        var d = new Date();
        d.setTime(d.getTime() + (days * 86400000));
        document.cookie = name + "=" + value + ";expires=" + d.toUTCString() + ";path=/";
    }

    cookieAlert.classList.add("show");

    acceptBtn.addEventListener("click", function () {
        cookieAlert.classList.remove("show");
    });
})();
