/**
	@ PREPEND FRAMEWORKS E BIBLIOTECAS
 */

// require "vendor/jquery.min.js"
// require "vendor/jquery.owl.carousel.min.js"
// require "vendor/jquery.mask.min.js"
// require "vendor/jquery.sticky.min.js";
// require "ismobile.js"
// require "dfp.js"

/**
	@ Funcões diversas do tema
 */

(function($, window, document) {
	$(function() {
		// ios ::active hack
		document.addEventListener('touchstart', function() {}, true);

		// dfpLoadBanners();
	});
}(window.jQuery, window, document));
