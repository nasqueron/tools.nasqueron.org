;(function ($, window, undefined) {
  'use strict';

  var $doc = $(document),
      Modernizr = window.Modernizr;

  $(document).ready(function() {
    $.fn.foundationAlerts           ? $doc.foundationAlerts() : null;
    $.fn.foundationButtons          ? $doc.foundationButtons() : null;
    $.fn.foundationAccordion        ? $doc.foundationAccordion() : null;
    $.fn.foundationNavigation       ? $doc.foundationNavigation() : null;
    $.fn.foundationTopBar           ? $doc.foundationTopBar() : null;
    $.fn.foundationCustomForms      ? $doc.foundationCustomForms() : null;
    $.fn.foundationMediaQueryViewer ? $doc.foundationMediaQueryViewer() : null;
    $.fn.foundationTabs             ? $doc.foundationTabs({callback : $.foundation.customForms.appendCustomMarkup}) : null;
    $.fn.foundationTooltips         ? $doc.foundationTooltips() : null;
    $.fn.foundationMagellan         ? $doc.foundationMagellan() : null;
    $.fn.foundationClearing         ? $doc.foundationClearing() : null;

    $.fn.placeholder                ? $('input, textarea').placeholder() : null;
  });

  // UNCOMMENT THE LINE YOU WANT BELOW IF YOU WANT IE8 SUPPORT AND ARE USING .block-grids
  // $('.block-grid.two-up>li:nth-child(2n+1)').css({clear: 'both'});
  // $('.block-grid.three-up>li:nth-child(3n+1)').css({clear: 'both'});
  // $('.block-grid.four-up>li:nth-child(4n+1)').css({clear: 'both'});
  // $('.block-grid.five-up>li:nth-child(5n+1)').css({clear: 'both'});

  // Hide address bar on mobile devices (except if #hash present, so we don't mess up deep linking).
  if (Modernizr.touch && !window.location.hash) {
    $(window).load(function () {
      setTimeout(function () {
        window.scrollTo(0, 1);
      }, 0);
    });
  }

})(jQuery, this);

/**
 * Switches to the specified UI tonality
 *
 * @param tonality The tonality (dark or light)
 */
function SwitchUITonality(tonality) {
	if (tonality == "light") {
		$('#content').removeClass("dark");
	} else {
		$('#content').addClass("dark");
	}
}

/**
 * Sets UI tonality option
 *
 * @param tonality The tonality (dark or light)
 */
function SetUITonality(tonality) {
	SwitchUITonality(tonality);
	$.cookie('UITonality', tonality, { expires: 360, path: '/' });
}

// Init code
var tonality = $.cookie('UITonality');
if (tonality != null) { SwitchUITonality(tonality); }

/**
 * Plural: determines if a count requires the singular or the plural
 *
 * @param int the objects count
 * @return string "s" if the count > 2; otherise, false.
 */
function s (count) {
	return Math.abs(count) >= 2 ? "s" : "";
}
