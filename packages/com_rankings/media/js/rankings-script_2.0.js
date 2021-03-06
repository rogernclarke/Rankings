/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

function set_tab($context) {
	var $key = $context + "_tab"
	if (sessionStorage.getItem($key) !== null) {
		var $href = sessionStorage.getItem($key);
		var $clss = $href.substring(1);
		var $curr = $(".tt-nav-tabs a[href='" + $href + "']").parent();

		$('.tt-nav-tabs li').removeClass("active");
		$('.tab-pane').removeClass("active");

		$('.tt-rider-category').removeClass("active");
    	$('.tt-rider-category.' + $clss).addClass("active");

    	$('.tt-rider-rank').removeClass("active");
    	$('.tt-rider-rank.' + $clss).addClass("active");

	    $curr.addClass("active");
	    $($href).addClass("active");
	}
}

function ttToggleRides(rankingID) {
    var element = document.getElementById("tt-rankings-" + rankingID + "-rides");
    if (element.style.display === "none") {
        element.style.display = "table-row";
    } else {
        element.style.display = "none";
    }
    var element = document.getElementById("tt-rankings-row-" + rankingID + "-icon");
    element.classList.toggle("fa-angle-right");
    element.classList.toggle("fa-angle-down");
}

// script for tab steps
$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var $key = $(e.target).attr('data-context') + "_tab";
    var $href = $(e.target).attr('href');
    var $clss = $href.substring(1);
    var $curr = $(".tt-nav-tabs a[href='" + $href + "']").parent();

    $('.tt-nav-tabs li').removeClass("active");

    //$('.tt-rider-category').removeClass("active");
    //$('.tt-rider-category.' + $clss).addClass("active");

    //$('.tt-rider-rank').removeClass("active");
    //$('.tt-rider-rank.' + $clss).addClass("active");

    $curr.addClass("active");
    $curr.prevAll().addClass("visited");

	sessionStorage.setItem($key, $href);
});
// end  script for tab steps

// scripts for sorting startsheet table
function sort_bib()
{
	var table = $('#tt-event-startsheet');
	var tbody = $('#tt-event-startsheet-body');

	tbody.find('tr').sort(function(a, b) 
	{
		var stringA = $('td.tt-col-rider-bib', a).text();
		if (stringA == "Res") {
			stringA = "999";
		}
		var stringB = $('td.tt-col-rider-bib', b).text();
		if (stringB == "Res") {
			stringB = "999";
		}
		return stringA - stringB;
	}).appendTo(tbody);

	$('.tt-tabs-startsheet li').removeClass();
	$('#tt-start-order').addClass("tab-current");
	$('.tt-col-rider-start-time').show();
	$('.tt-col-predicted-time-at-finish').hide();
}
function sort_predicted_finish()
{
	var table = $('#tt-event-startsheet');
	var tbody = $('#tt-event-startsheet-body');

	tbody.find('tr').sort(function(a, b) 
	{
		var stringA = $('td.tt-col-predicted-time-at-finish', a).text();
		if (stringA == "-" | !stringA) {
			stringA = "23:59:59";
		}
		var stringB = $('td.tt-col-predicted-time-at-finish', b).text();
		if (stringB == "-" | !stringB) {
			stringB = "23:59:59";
		}
		var dateA = new Date('2019-01-01T' + stringA + 'Z');
		var dateB = new Date('2019-01-01T' + stringB + 'Z');
		return dateA - dateB;
	}).appendTo(tbody);

	$('.tt-tabs-startsheet li').removeClass();
	$('#tt-finish-order').addClass("tab-current");
	$('.tt-col-predicted-time-at-finish').show();
	$('.tt-col-rider-start-time').hide();
}
function sort_predicted_position()
{
	var table = $('#tt-event-startsheet');
	var tbody = $('#tt-event-startsheet-body');

	tbody.find('tr').sort(function(a, b) 
	{
		return strip_ordinal($('td.tt-col-ride-predicted-position', a).text())-strip_ordinal($('td.tt-col-ride-predicted-position', b).text());
	}).appendTo(tbody);
	
	$('.tt-tabs-startsheet li').removeClass();
	$('#tt-result-order').addClass("tab-current");
	$('.tt-col-predicted-time-at-finish').hide();
	$('.tt-col-rider-start-time').hide();
}
function strip_ordinal(numberWithOrdinal) {
	if (numberWithOrdinal == "-") {
		return 999;
	} else {
		return numberWithOrdinal.slice(-5,-2);
	}
}
// scripts for filtering results table
function filter_results_overall()
{
	var table = $('#tt-event-results');
	var tbody = $('#tt-event-results-body');

	$('#tt-event-results-body tr').css("display", "table-row");

	tbody.find('tr').sort(function(a, b) 
	{
		return strip_ordinal($('td.tt-col-event-position .tt-event-position', a).text())-strip_ordinal($('td.tt-col-event-position .tt-event-position', b).text());
	}).appendTo(tbody);

	$('.tabs-style-topline.tt-tabs-results li').removeClass();
	$('#tt-overall-filter').addClass("tab-current");
	$('.tt-col-event-gender-position').hide();
	$('.tt-col-event-vets-position').hide();
	$('.tt-col-ride-vets-standard-time').hide();
	$('.tt-col-ride-vets-standard-result').hide();
	$('.tt-tab-vets-footer').hide();
	$('.tt-col-event-position').show();
	$('.tt-col-ride-predicted-result').show();
	$('.tt-col-event-ride-points').show();
}
function filter_results_male()
{
	var table = $('#tt-event-results');
	var tbody = $('#tt-event-results-body');

	tbody.find('tr').filter(function() 
	{
		$(this).toggle($(this).text().indexOf("Male") > -1)
	});

	tbody.find('tr').sort(function(a, b) 
	{
		return strip_ordinal($('td.tt-col-event-position .tt-event-position', a).text())-strip_ordinal($('td.tt-col-event-position .tt-event-position', b).text());
	}).appendTo(tbody);

	$('.tabs-style-topline.tt-tabs-results li').removeClass();
	$('#tt-male-filter').addClass("tab-current");
	$('.tt-col-event-position').hide();
	$('.tt-col-event-vets-position').hide();
	$('.tt-col-ride-vets-standard-time').hide();
	$('.tt-col-ride-vets-standard-result').hide();
	$('.tt-tab-vets-footer').hide();
	$('.tt-col-event-gender-position').show();
	$('.tt-col-ride-predicted-result').show();
	$('.tt-col-event-ride-points').show();
}
function filter_results_female()
{
	var table = $('#tt-event-results');
	var tbody = $('#tt-event-results-body');

	tbody.find('tr').filter(function() 
	{
		$(this).toggle($(this).text().indexOf("Female") > -1)
	});

	tbody.find('tr').sort(function(a, b) 
	{
		return strip_ordinal($('td.tt-col-event-position .tt-event-position', a).text())-strip_ordinal($('td.tt-col-event-position .tt-event-position', b).text());
	}).appendTo(tbody);

	$('.tabs-style-topline.tt-tabs-results li').removeClass();
	$('#tt-female-filter').addClass("tab-current");
	$('.tt-col-event-position').hide();
	$('.tt-col-event-vets-position').hide();
	$('.tt-col-ride-vets-standard-time').hide();
	$('.tt-col-ride-vets-standard-result').hide();
	$('.tt-tab-vets-footer').hide();
	$('.tt-col-event-gender-position').show();
	$('.tt-col-ride-predicted-result').show();
	$('.tt-col-event-ride-points').show();
}
function filter_results_veterans()
{
	var table = $('#tt-event-results');
	var tbody = $('#tt-event-results-body');

	tbody.find('tr').filter(function() 
	{
		$(this).toggle($(this).text().indexOf("Veteran") > -1)
	});
	tbody.find('tr').sort(function(a, b) 
	{
		var stringA = strip_ordinal($('td.tt-col-event-vets-position .tt-event-position', a).text());
		if (!stringA) {
			stringA = "999";
		}
		var stringB = strip_ordinal($('td.tt-col-event-vets-position .tt-event-position', b).text());
		if (!stringB) {
			stringB = "999";
		}
		return stringA - stringB;
	}).appendTo(tbody);
	

	$('.tabs-style-topline.tt-tabs-results li').removeClass();
	$('#tt-veterans-filter').addClass("tab-current");
	$('.tt-col-event-position').hide();
	$('.tt-col-event-gender-position').hide();
	$('.tt-col-ride-predicted-result').hide();
	$('.tt-col-event-ride-points').hide();
	$('.tt-col-event-vets-position').show();
	$('.tt-col-ride-vets-standard-time').show();
	$('.tt-col-ride-vets-standard-result').show();
	$('.tt-tab-vets-footer').show();
}

jQuery(document).ready(function () {
    jQuery('.tt-accordion-container').each(function(index) {
        var mainContainer = jQuery(this);
        function openFirstPanel() {
            jQuery('> ul > li:first-child > .accordion-item-content', mainContainer).addClass('active').css('opacity', 0).slideDown("slow").animate({ opacity: 1 }, {
                queue: false,
                duration: 'slow'
            });
            jQuery('> ul > li:first-child', mainContainer).addClass('active').slideDown("slow").animate({ opacity: 1 }, {
                queue: false,
                duration: 'slow'
            });
            jQuery('> ul > li:first-child > .accordion-item-title > .indicator span', mainContainer).text('-');
        }

        var allPanels = jQuery('> ul > li .accordion-item-content', jQuery(this)).hide();
        openFirstPanel();

        jQuery('> ul > li', jQuery(this)).click(function() {
            var selectedItem = jQuery(this);
            var target = jQuery('> .accordion-item-content', jQuery(this));

            if (target.hasClass('active')) {
                target.removeClass('active').slideUp("slow").animate({ opacity: 0 }, {
                    queue: false,
                    duration: 'slow'
                });
            } else {
                allPanels.removeClass('active').slideUp("slow").animate({ opacity: 0 }, {
                    queue: false,
                    duration: 'slow'
                });
                target.addClass('active').slideDown("slow").animate({ opacity: 1 }, {
                    queue: false,
                    duration: 'slow'
                });
            }
            if (selectedItem.hasClass('active')) {
                selectedItem.removeClass('active');
                jQuery('> .accordion-item-title > .indicator span', jQuery(this)).text('+');
            } else {
                jQuery('> .tt-accordion > li', mainContainer).removeClass('active');
                jQuery('> ul > li .indicator span', mainContainer).text('+');
                selectedItem.addClass('active');
                jQuery('> .accordion-item-title > .indicator span', jQuery(this)).text('-');
            }
            return false;
        });
    });

    jQuery('.tt-tabs a[data-toggle="tab"]').click(function(e) {
    	e.preventDefault();
  		jQuery(this).tab('show');
  		e.stopPropagation();
    });

    jQuery( ".accordion-item-title form" ).click(function( event ) {
	    event.stopPropagation();
	});

	jQuery( ".accordion-item-content" ).click(function( event ) {
	    event.stopPropagation();
	});
});
