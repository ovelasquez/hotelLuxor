<?php

include('bookingform.php');
add_shortcode( 'abc-bookingform', 'abc_booking_showBookingForm'); // Shortcode for the booking form

include('calendaroverview.php');
add_shortcode( 'abc-overview', 'abc_booking_showCalOverview'); // Shortcode for the calendar overview

include('singlecalendar.php');
add_shortcode( 'abc-single', 'abc_booking_showSingleCalendar'); // Shortcode for a single calendar
?>