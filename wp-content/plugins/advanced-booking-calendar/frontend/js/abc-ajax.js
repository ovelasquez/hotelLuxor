function getLastDayDate(someDate){ // Returns the last day of the month for someDate
	var y = someDate.getFullYear();
	var m = someDate.getMonth();
	var d = new Date(y, m + 1, 0 );
	return d;
}

function getDateYYYYMMDD(someDate){
	var dd = someDate.getDate();
	if (dd < 10){
		dd = '0' + dd;
	}
	var mm = someDate.getMonth() + 1;
	if (mm < 10){
		mm = '0' + mm;
	}
	var yyyy = someDate.getFullYear();
	return yyyy + '-'+ mm + '-'+ dd;
}

// Single Calendar

jQuery('.abc-single-button-right').live('click', function(){
	var uniqid = jQuery(this).data('id');
	var calendar = jQuery(this).data('calendar');
	var abcSingleCheckin = jQuery('#abc_singlecalendar_' + uniqid).data('checkin-' + uniqid);
	var abcSingleCheckout = jQuery('#abc_singlecalendar_' + uniqid).data('checkout-' + uniqid);
	var month = jQuery('#abc_singlecalendar_' + uniqid).data('month-' + uniqid);
	month = month + 1;
	jQuery('#singlecalendar-month-' + uniqid).hide();
	jQuery('#abc-calendar-days-' + uniqid).hide();
	jQuery('.abc-single-button-right').attr('disabled',true);
	jQuery('#abc_single_loading-' + uniqid).show();
	dateData = {
			action: 'abc_booking_getMonth',
			abc_nonce: ajax_abc_booking_SingleCalendar.abc_nonce,
			month: month
	}
	jQuery.post(ajax_abc_booking_SingleCalendar.ajaxurl, dateData, function (response){
		jQuery('#singlecalendar-month-' + uniqid).html(response);
	});
	data = {
		action: 'abc_booking_getSingleCalendar',
		abc_nonce: ajax_abc_booking_SingleCalendar.abc_nonce,
		month: month, 
		uniqid: uniqid, 
		calendar: calendar,
		start: abcSingleCheckin,
		end: abcSingleCheckout
	};
	jQuery.post(ajax_abc_booking_SingleCalendar.ajaxurl, data, function (response){
		jQuery('#abc-calendar-days-' + uniqid).html(response);
		jQuery('#abc-calendar-days-' + uniqid).show();
		jQuery('#singlecalendar-month-' + uniqid).show();
		jQuery('.abc-single-button-right').attr('disabled',false);
		jQuery('#abc_single_loading-' + uniqid).hide();
	});
	jQuery('#abc_singlecalendar_' + uniqid).data('month-' + uniqid, month);
	return false;	
});

jQuery('.abc-single-button-left').live('click', function(){
	var uniqid = jQuery(this).data('id');
	var calendar = jQuery(this).data('calendar');
	var abcSingleCheckin = jQuery('#abc_singlecalendar_' + uniqid).data('checkin-' + uniqid);
	var abcSingleCheckout = jQuery('#abc_singlecalendar_' + uniqid).data('checkout-' + uniqid);
	var month = jQuery('#abc_singlecalendar_' + uniqid).data('month-' + uniqid);
	month = month - 1;
	jQuery('#singlecalendar-month-' + uniqid).hide();
	jQuery('#abc-calendar-days-' + uniqid).hide();
	jQuery('.abc-single-button-left').attr('disabled',true);
	jQuery('#abc_single_loading-' + uniqid).show();
	dateData = {
			action: 'abc_booking_getMonth',
			abc_nonce: ajax_abc_booking_SingleCalendar.abc_nonce,
			month: month
	}
	jQuery.post(ajax_abc_booking_SingleCalendar.ajaxurl, dateData, function (response){
		jQuery('#singlecalendar-month-' + uniqid).html(response);
	});
	data = {
		action: 'abc_booking_getSingleCalendar',
		abc_nonce: ajax_abc_booking_SingleCalendar.abc_nonce,
		month: month, 
		uniqid: uniqid, 
		calendar: calendar,
		start: abcSingleCheckin,
		end: abcSingleCheckout
	};
	jQuery.post(ajax_abc_booking_SingleCalendar.ajaxurl, data, function (response){
		jQuery('#abc-calendar-days-' + uniqid).html(response);
		jQuery('#abc-calendar-days-' + uniqid).show();
		jQuery('#singlecalendar-month-' + uniqid).show();
		jQuery('.abc-single-button-left').attr('disabled',false);
		jQuery('#abc_single_loading-' + uniqid).hide();	
	});
	jQuery('#abc_singlecalendar_' + uniqid).data('month-' + uniqid, month);
	return false;	
});	

jQuery('.abc-date-selector').live('click', function(){
	var uniqid = jQuery(this).data('id');
	var calendar = jQuery(this).data('calendar');
	var date = jQuery(this).data('date');
	var tempDate = new Date(date);
	var abcSingleCheckin = jQuery('#abc_singlecalendar_' + uniqid).data('checkin-' + uniqid);
	var abcSingleCheckout = jQuery('#abc_singlecalendar_' + uniqid).data('checkout-' + uniqid);
	var lastDay = getDateYYYYMMDD(getLastDayDate(tempDate));
	if(abcSingleCheckin == 0){
		abcSingleCheckin = date;
		abcSingleCheckout = 0;
		jQuery(this).addClass('abc-date-selected');
	} else if (abcSingleCheckin != 0 && abcSingleCheckout == 0 && date > abcSingleCheckin){
		var tempDate = new Date(abcSingleCheckin);
		while(getDateYYYYMMDD(tempDate) <= date){
			if(jQuery('#abc-day-' + uniqid + getDateYYYYMMDD(tempDate)).hasClass('abc-booked')){
				break;
			}
			jQuery('#abc-day-' + uniqid + getDateYYYYMMDD(tempDate)).addClass('abc-date-selected');
			tempDate.setDate(tempDate.getDate() + 1);
		}
		tempDate.setDate(tempDate.getDate() - 1);
		abcSingleCheckout = getDateYYYYMMDD(tempDate);
	} else if (abcSingleCheckin > date 
			|| (abcSingleCheckin != 0 && abcSingleCheckout != 0 && date >= abcSingleCheckout)
			|| (abcSingleCheckin != 0 && abcSingleCheckout != 0 && date >= abcSingleCheckin)
			){
		var tempDate = new Date(abcSingleCheckin);
		jQuery('.abc-date-selector').removeClass('abc-date-selected');
		jQuery(this).addClass('abc-date-selected');
		abcSingleCheckin = date;
		abcSingleCheckout = 0;
	}	
	data = {
		action: 'abc_booking_setDataRange',
		abc_nonce: ajax_abc_booking_SingleCalendar.abc_nonce,
		start: abcSingleCheckin, 
		end: abcSingleCheckout, 
		uniqid: uniqid, 
		calendar: calendar
	};
	jQuery.post(ajax_abc_booking_SingleCalendar.ajaxurl, data, function (response){
		jQuery('#abc-booking-' + uniqid).html(response);
	});
	jQuery('#abc_singlecalendar_' + uniqid).data('checkin-' + uniqid, abcSingleCheckin);
	jQuery('#abc_singlecalendar_' + uniqid).data('checkout-' + uniqid, abcSingleCheckout);
	return false;	
});

jQuery('.abc-date-selector').live('mouseenter', function(){
	var uniqid = jQuery(this).data('id');
	var date = jQuery(this).data('date');
	var abcSingleCheckin = jQuery('#abc_singlecalendar_' + uniqid).data('checkin-' + uniqid);
	var abcSingleCheckout = jQuery('#abc_singlecalendar_' + uniqid).data('checkout-' + uniqid);
	if(date > abcSingleCheckin && abcSingleCheckin != 0 && abcSingleCheckout == 0){
		var tempDate = new Date(abcSingleCheckin);
		while(getDateYYYYMMDD(tempDate) <= date){
			if(jQuery('#abc-day-'+ uniqid + getDateYYYYMMDD(tempDate)).hasClass('abc-booked')){
				break;
			}
			jQuery('#abc-day-'+ uniqid + getDateYYYYMMDD(tempDate)).addClass('abc-date-selected');
			tempDate.setDate(tempDate.getDate() + 1);
		}
	}
});

jQuery('.abc-date-selector').live('mouseleave', function(){
	var uniqid = jQuery(this).data('id');
	var date = jQuery(this).data('date');
	var abcSingleCheckin = jQuery('#abc_singlecalendar_' + uniqid).data('checkin-' + uniqid);
	var abcSingleCheckout = jQuery('#abc_singlecalendar_' + uniqid).data('checkout-' + uniqid);
	if(date > abcSingleCheckin && abcSingleCheckin != 0 && abcSingleCheckout == 0){
		var tempDate = new Date(date);
		while(getDateYYYYMMDD(tempDate) > abcSingleCheckin){
			jQuery('#abc-day-'+ uniqid + getDateYYYYMMDD(tempDate)).removeClass('abc-date-selected');
			tempDate.setDate(tempDate.getDate() - 1);
		}
	}
});	
// Calendar overview

jQuery('.abc-overview-button').live('click', function(){
	var uniqid = jQuery(this).data('id');
	var overviewMonth = jQuery(this).data('month');
	var overviewYear = jQuery(this).data('year');
	jQuery('.abc-overview-button').attr('disabled',true);
	jQuery('.abcMonth').attr('disabled',true);
	jQuery('.abcYear').attr('disabled',true);
	data = {
		action: 'abc_booking_getCalOverview',
		abc_nonce: ajax_abc_booking_calOverview.abc_nonce,
		month: overviewMonth,
		year: overviewYear,
		uniqid: uniqid
	};
	
	jQuery.post(ajax_abc_booking_calOverview.ajaxurl, data, function (response){
		jQuery('#abc-calendaroverview-' + uniqid).html(response);
		jQuery('.abc-overview-button').attr('disabled',false);
		jQuery('.abcMonth').attr('disabled',false);
		jQuery('.abcYear').attr('disabled',false);
	});
	
	return false;	
});	

jQuery( "select[name='abcMonth']").live('change', function () {
	var uniqid = jQuery(this).data('id');
	var overviewMonth = jQuery( "select[name='abcMonth']").val();
	var overviewYear = jQuery( "select[name='abcYear']").val();
	jQuery('.abcMonth').attr('disabled',true);
	jQuery('.abcYear').attr('disabled',true);
	jQuery('.abc-button-rl').attr('disabled',true);
	data = {
		action: 'abc_booking_getCalOverview',
		abc_nonce: ajax_abc_booking_calOverview.abc_nonce,
		month: overviewMonth,
		year: overviewYear,
		uniqid: uniqid
	};
	jQuery.post(ajax_abc_booking_calOverview.ajaxurl, data, function (response){
		jQuery('#abc-calendaroverview-' + uniqid).html(response);
		jQuery('.abc-button-rl').attr('disabled',false);
		jQuery('.abcMonth').attr('disabled',false);
		jQuery('.abcYear').attr('disabled',false);
	});
	return false;
});
jQuery( "select[name='abcYear']").live('change', function () {
	var uniqid = jQuery(this).data('id');
	var overviewMonth = jQuery( "select[name='abcMonth']").val();
	var overviewYear = jQuery( "select[name='abcYear']").val();
	jQuery('.abcMonth').attr('disabled',true);
	jQuery('.abcYear').attr('disabled',true);
	jQuery('.abc-button-rl').attr('disabled',true);
	data = {
		action: 'abc_booking_getCalOverview',
		abc_nonce: ajax_abc_booking_calOverview.abc_nonce,
		month: overviewMonth,
		year: overviewYear,
		uniqid: uniqid
	};
	jQuery.post(ajax_abc_booking_calOverview.ajaxurl, data, function (response){
		jQuery('#abc-calendaroverview-' + uniqid).html(response);
		jQuery('.abc-button-rl').attr('disabled',false);
		jQuery('.abcMonth').attr('disabled',false);
		jQuery('.abcYear').attr('disabled',false);
	});
	return false;
});

// Booking form

function getAbcAvailabilities(){
	data = {
		action: 'abc_booking_getBookingResult',			
		from: jQuery("#abc-from").val(),
		to: jQuery("#abc-to").val(),
		persons: jQuery("#abc-persons").val()
	};
	jQuery('#abc-submit-button').hide();
	jQuery('#abc-bookingresults').hide();
	jQuery('.abc-submit-loading').show();
	jQuery.post(ajax_abc_booking_showBookingForm.ajaxurl, data, function (response){
		jQuery('#abc-submit-button').show();
		jQuery('.abc-submit-loading').hide();
		jQuery('#abc-bookingresults').html(response);
		jQuery("#abc-bookingresults").slideDown("slow");
		jQuery('.abc-submit').attr('disabled',false);
	});	
	return false;	
}

jQuery('#abc-check-availabilities').live('click', function() {
	getAbcAvailabilities();
});

jQuery(document).ready(function() {
    if (jQuery("#abcPostTrigger").length && jQuery("#abcPostTrigger").val() == 1 ) {
		getAbcAvailabilities();
		jQuery('html, body').animate({
                    scrollTop: jQuery("#abc-form-content").offset().top
                }, 2000);
	}
});

jQuery('#abc-back-to-availabilities').click(function(){
	jQuery('#abc-from').attr('disabled',false);
	jQuery('#abc-from').removeClass('abc-deactivated');
	jQuery('#abc-to').attr('disabled',false);
	jQuery('#abc-to').removeClass('abc-deactivated');
	jQuery('#abc-persons').attr('disabled',false);
	jQuery('#abc-persons').removeClass('abc-deactivated');
	jQuery('#abc-back-to-availabilities').hide();
	jQuery('#abc-check-availabilities').show();
	
	return false;	
});
jQuery(document).on('click', '.abc-bookingform-book', function(){
	jQuery('#abc-bookingresults').fadeOut('medium');
	jQuery('#abc-check-availabilities').hide();
	jQuery('#abc-from').attr('disabled',true);
	jQuery('#abc-from').addClass('abc-deactivated');
	jQuery('#abc-to').attr('disabled',true);
	jQuery('#abc-to').addClass('abc-deactivated');
	jQuery('#abc-persons').attr('disabled',true);
	jQuery('#abc-persons').addClass('abc-deactivated');
	data = {
		action: 'abc_booking_getBookingFormStep2',
		from: jQuery(this).data('from'),
		to: jQuery(this).data('to'),
		persons: jQuery(this).data('persons'),
		calendar: jQuery(this).data('calendar')
	};
	jQuery.post(ajax_abc_booking_showBookingForm.ajaxurl, data, function (response){
		jQuery('#abc-bookingresults').html(response);
		jQuery('#abc-bookingresults').fadeIn('medium');
		jQuery('#abc-back-to-availabilities').show();
	});	
	return false;	
});

jQuery(document).on('click', '#abc-bookingform-extras-submit', function(){
	jQuery('#abc-bookingresults').fadeOut('medium');
	data = {
		action: 'abc_booking_getBookingFormStep2',
		extrasList: jQuery("input[name=abc-extras-checkbox]:checked").map(function () {return this.value;}).get().join(","),
		from: jQuery(this).data('from'),
		to: jQuery(this).data('to'),
		persons: jQuery(this).data('persons'),
		calendar: jQuery(this).data('calendar')
	};
	jQuery.post(ajax_abc_booking_showBookingForm.ajaxurl, data, function (response){
		jQuery('#abc-bookingresults').html(response);
		jQuery('#abc-bookingresults').fadeIn('medium');
		jQuery('#abc-back-to-availabilities').show();
	});	
	return false;
});

jQuery(document).on('click', '#abc-bookingform-back', function(){
	jQuery('#abc-form-content').fadeOut('medium');
	data = {
		action: 'abc_booking_getBackToBookingResult',
		from: jQuery(this).data('from'),
		to: jQuery(this).data('to'),
		persons: jQuery(this).data('persons')
	};
	alert(jQuery(this).data('from')+jQuery(this).data('to'));
	jQuery.post(ajax_abc_booking_showBookingForm.ajaxurl, data, function (response){
		jQuery('#abc-form-content').html(response);
		jQuery('#abc-form-content').fadeIn('medium');
	});	
	return false;	
});

jQuery(document).on('click', '#abc-bookingform-book-submit', function(){
		data = {
			action: 'abc_booking_getBookingFormBook',
			from: jQuery(this).data('from'),
			to: jQuery(this).data('to'),
			persons: jQuery(this).data('persons'),
			calendar: jQuery(this).data('calendar'),
			extraslist: jQuery(this).data('extraslist'),
			firstname: jQuery('#first_name').val(),
			lastname: jQuery('#last_name').val(),
			email: jQuery('#email').val(),
			phone: jQuery('#phone').val(),
			address: jQuery('#address').val(),
			zip: jQuery('#zip').val(),
			city: jQuery('#city').val(),
			county: jQuery('#county').val(),
			country: jQuery('#country').val(),
			message: jQuery('#message').val()
		};
	jQuery('.abc-booking-form').validate({ // initialize the plugin
        errorClass:'abc-form-error',
		rules: ajax_abc_booking_showBookingForm.rules,
		submitHandler: function (form) { 
		jQuery('#abc-form-content').fadeOut('medium');
		jQuery('#abc_bookinform_loading').show();
		jQuery('html, body').animate({ scrollTop: (jQuery('#abc-form-wrapper').offset().top - 150)}, 'slow');
		jQuery.post(ajax_abc_booking_showBookingForm.ajaxurl, data, function (response){
			jQuery('#abc_bookinform_loading').hide();
			jQuery('#abc-form-content').html(response);
			jQuery('#abc-form-content').fadeIn('medium');
		});	
		return false;
        }
    });	
});
	