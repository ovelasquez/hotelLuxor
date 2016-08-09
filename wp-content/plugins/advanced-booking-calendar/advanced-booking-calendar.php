<?php
/*
Plugin Name: Advanced Booking Calendar
Plugin URI: https://booking-calendar-plugin.com/
Description: The Booking System that makes managing your online reservations easy. A great Booking Calendar plugin for Accommodations.
Author: Advanced Booking Calendar
Author URI: https://booking-calendar-plugin.com
Version: 1.2.4
Text Domain: advanced-booking-calendar
Domain Path: /languages/
*/

include('functions.php');
include('widget.php');
include('backend/bookings.php');
include('backend/seasons-calendars.php');
include('frontend/shortcodes.php');
include('backend/analytics.php');
include('backend/settings.php');
include('backend/extras.php');


global $abcUrl;
$abcUrl = plugin_dir_url(__FILE__);

// Loading translations
add_action( 'plugins_loaded', 'abc_booking_load_textdomain' );
function abc_booking_load_textdomain() {
	load_plugin_textdomain('advanced-booking-calendar', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
}

//Install
function advanced_booking_calendar_install() {
	global $wpdb;
	$bookingTable = $wpdb->prefix . "advanced_booking_calendar_bookings";

	if($wpdb->get_var("show tables like '$bookingTable'") != $bookingTable) 
	{
	$bookings = "CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_bookings` (
                 `id` int(255) NOT NULL AUTO_INCREMENT,
                 `start` date NOT NULL,
                 `end` date NOT NULL,
                 `calendar_id` int(255) NOT NULL,
                 `persons` int(32) NOT NULL,
                 `first_name` varchar(255) NOT NULL,
                 `last_name` varchar(255) NOT NULL,
                 `email` varchar(255) NOT NULL,
                 `phone` varchar(255) NOT NULL,
                 `address` varchar(255) NOT NULL,
                 `zip` varchar(255) NOT NULL,
                 `city` varchar(255) NOT NULL,
                 `county` varchar(255) NOT NULL,
                 `country` varchar(255) NOT NULL,
                 `message` text NOT NULL,
                 `price` float NOT NULL,
                 `state` varchar(32) NOT NULL,
                 `room_id` int(11) NOT NULL,
                 PRIMARY KEY (`id`)
                ) CHARSET=utf8";

	$requests = "CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_requests` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `current_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         `date_from` date NOT NULL,
         `date_to` date NOT NULL,
         `persons` int(10) NOT NULL,
         `successful` tinyint(1) NOT NULL,
         PRIMARY KEY (`id`)
        ) CHARSET=utf8";

	$rooms = "CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_rooms` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `calendar_id` int(11) NOT NULL,
         `name` varchar(255) NOT NULL,
         UNIQUE KEY `id_2` (`id`),
         KEY `id` (`id`)
        ) CHARSET=utf8";

	$calendars = "CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_calendars` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `name` varchar(255) NOT NULL,
         `maxUnits` int(11) NOT NULL,
         `maxAvailabilities` int(11) NOT NULL,
         `pricePreset` double NOT NULL,
         `minimumStayPreset` int(16) NOT NULL,
         `partlyBooked` int(16) NOT NULL,
         `infoPage` int(11) NOT NULL,
         `infoText` text NOT NULL,
         PRIMARY KEY (`id`)
        ) CHARSET=utf8";

	$seasons = "CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_seasons` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `title` varchar(255) NOT NULL,
         `price` double NOT NULL,
 		 `lastminute` int(11) NOT NULL,
         `minimumStay` int(16) NOT NULL,
         PRIMARY KEY (`id`)
        ) CHARSET=utf8";

	$seasonsAssignment = "CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_seasons_assignment` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `calendar_id` int(11) NOT NULL,
         `season_id` int(11) NOT NULL,
         `start` date NOT NULL,
         `end` date NOT NULL,
         PRIMARY KEY (`id`)
        ) CHARSET=utf8";
	
	$extras ="CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_extras` ( 
		`id` INT(16) NOT NULL AUTO_INCREMENT , 
		`name` TEXT NOT NULL , 
		`explanation` TEXT NOT NULL , 
		`calculation` TEXT NOT NULL ,
		`mandatory` TEXT NOT NULL , 
		`price` FLOAT(32) NOT NULL ,
		 PRIMARY KEY (`id`) ) charset=utf8";
	$bookingExtras ="CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_booking_extras` ( 
		`id` INT(32) NOT NULL AUTO_INCREMENT , 
		`booking_id` INT(32) NOT NULL , 
		`extra_id` INT(32) NOT NULL , 
		 PRIMARY KEY (`id`) ) charset=utf8";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($extras);
	dbDelta($bookingExtras);
	dbDelta($bookings);
	dbDelta($requests);
	dbDelta($rooms);
	dbDelta($calendars);
	dbDelta($seasons);
	dbDelta($seasonsAssignment);
	}
	add_option ('abc_email', get_option( 'admin_email' ));
	add_option ('abc_bookingpage', 0);
	add_option ('abc_dateformat', "Y-m-d");
	add_option ('abc_priceformat', ".");
	add_option ('abc_cookies', 0);
	add_option ('abc_googleanalytics', 0);
	$locale = localeconv();
	add_option ('abc_currency', $locale['currency_symbol']);
	add_option ('abc_newsletter_10th_asked', 0);
	add_option ('abc_newsletter_100th_asked', 0);
	add_option ('abc_newsletter_20000revenue_asked', 0);
	$abc_text_details = __("Your details", "abc-booking").":\n";
	$abc_greeting = __("Hi [abc_first_name]!", "abc-booking")."\n";
	$abc_goodbye = sprintf(__('Your %s-Team', 'advanced-booking-calendar'), get_option( 'blogname' ));
	$abc_text_details .= __("Room type", "abc-booking").": [abc_calendar_name]\n";
	$abc_text_details .= __("Room price", "abc-booking").": [abc_room_price]\n";
	$abc_text_details .= __("Selected extras", "abc-booking").": [abc_optional_extras]\n";
	$abc_text_details .= __("Additional costs", "abc-booking").": [abc_mandatory_extras]\n";
	$abc_text_details .= __("Total price", "abc-booking").": [abc_total_price]\n";
	$abc_text_details .= __("Checkin - Checkout", "abc-booking").": [abc_checkin_date] - [abc_checkout_date]\n";
	$abc_text_details .= __("Number of guests", "abc-booking").": [abc_person_count]\n";
	$abc_text_details .= __("Full Name", "abc-booking").": [abc_first_name] - [abc_last_name]\n";
	$abc_text_details .= __("Email", "abc-booking").": [abc_email]\n";
	$abc_text_details .= __("Phone", "abc-booking").": [abc_phone]\n";
	$abc_text_details .= __("Address", "abc-booking").": [abc_address], [abc_zip] [abc_city], [abc_county], [abc_country] \n";
	$abc_text_details .= __("Your message to us", "abc-booking").": [abc_message]\n\n";
	$abc_subject_unconfirmed = sprintf(__('Your booking at %s', 'advanced-booking-calendar'),get_option( 'blogname' )).' - [abc_checkin_date] - [abc_checkout_date]';
	add_option ('abc_subject_unconfirmed', $abc_subject_unconfirmed);
	$abc_text_unconfirmed =  $abc_greeting; 
	$abc_text_unconfirmed .=  sprintf(__("Thank you for booking at %s. Your booking has not yet been confirmed. Please wait for an additional confirmation email.", "abc-booking"), get_option( 'blogname' ))."\n\n";
	$abc_text_unconfirmed .= $abc_text_details;
	$abc_text_unconfirmed .= $abc_goodbye;
	add_option ('abc_text_unconfirmed', $abc_text_unconfirmed);
	$abc_subject_confirmed = sprintf(__('Confirming your booking at %s', 'advanced-booking-calendar'),get_option( 'blogname' )).' - [abc_checkin_date] - [abc_checkout_date]';
	add_option ('abc_subject_confirmed', $abc_subject_confirmed);
	$abc_text_confirmed = $abc_greeting;
	$abc_text_confirmed .= __("We are happy to confirm your booking!", "abc-booking")."\n\n";
	$abc_text_confirmed .= $abc_text_details;
	$abc_text_confirmed .= __("If you have any questions regard your stay, feel free to contact us.", "abc-booking")."\n\n";
	$abc_text_confirmed .= $abc_goodbye;
	add_option ('abc_text_confirmed', $abc_text_confirmed);
	$abc_subject_canceled = sprintf(__('Canceling your booking at %s', 'advanced-booking-calendar'),get_option( 'blogname' ));
	add_option ('abc_subject_canceled', $abc_subject_canceled);
	$abc_text_canceled = $abc_greeting;
	$abc_text_canceled .= __("We are very sorry to cancel your booking! We already had another reservation for your requested travel period.", "abc-booking")."\n";
	$abc_text_canceled .= sprintf(__("Please check our website at %s for an alternative. We would be very happy to welcome you any time soon.", "abc-booking"), get_site_url())."\n\n";
	$abc_text_canceled .= $abc_goodbye;
	add_option ('abc_text_canceled', $abc_text_canceled);
	$abc_subject_rejected = sprintf(__('Rejecting your booking at %s', 'advanced-booking-calendar'),get_option( 'blogname' ));
	add_option ('abc_subject_rejected', $abc_subject_rejected);
	$abc_text_rejected = $abc_greeting;
	$abc_text_rejected .= __("We are very sorry to reject your booking! We already had another reservation for your requested travel period.", "abc-booking")."\n";
	$abc_text_rejected .= sprintf(__("Please check our website at %s for an alternative. We would be very happy to welcome you any time soon.", "abc-booking"), get_site_url())."\n\n";
	$abc_text_rejected .= $abc_goodbye;
	add_option ('abc_text_rejected', $abc_text_rejected);
	add_option('abc_installdate', date('Y-m-d'));
	add_option('abc_poweredby', 0);
	add_option('abc_feedbackModal01', 0);
	add_option('abc_currencyPosition', 1);
	add_option('abc_pluginversion', '124');
	add_option('abc_personcount', 2);
	add_option('abc_bookingform', array(
		 		'firstname' => '2',
		 		'lastname' => '2',
		 		'phone' => '2',
		 		'street' => '2',
		 		'zip' => '2',
		 		'city' => '2',
		 		'county' => '0',
		 		'country' => '2',
		 		'message' => '1',
		 		'inputs' => '8'
				));
	
} //==>advanced_booking_calendar_install()
register_activation_hook( __FILE__, 'advanced_booking_calendar_install');


//Uninstall
function advanced_booking_calendar_uninstall() {
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS 
		`".$wpdb->prefix."advanced_booking_calendar_bookings`,
		`".$wpdb->prefix."advanced_booking_calendar_booking_extras`,
		`".$wpdb->prefix."advanced_booking_calendar_calendars`,
		`".$wpdb->prefix."advanced_booking_calendar_extras`,
		`".$wpdb->prefix."advanced_booking_calendar_requests`,
		`".$wpdb->prefix."advanced_booking_calendar_rooms`,
		`".$wpdb->prefix."advanced_booking_calendar_seasons`,
		`".$wpdb->prefix."advanced_booking_calendar_seasons_assignment`
		");
	delete_option ('abc_email');
	delete_option ('abc_bookingpage');
	delete_option ('abc_dateformat');
	delete_option ('abc_priceformat');
	delete_option ('abc_cookies');
	delete_option ('abc_googleanalytics');
	delete_option ('abc_currency');
	delete_option ('abc_newsletter_10th_asked');
	delete_option ('abc_newsletter_100th_asked');
	delete_option ('abc_newsletter_20000revenue_asked');
	delete_option ('abc_subject_unconfirmed');
	delete_option ('abc_text_unconfirmed');
	delete_option ('abc_subject_confirmed');
	delete_option ('abc_text_confirmed');
	delete_option ('abc_subject_canceled');
	delete_option ('abc_text_canceled');
	delete_option ('abc_subject_rejected');
	delete_option ('abc_text_rejected');
	delete_option('abc_pluginversion');
	delete_option('abc_installdate');
	delete_option('abc_poweredby');
	delete_option('abc_feedbackModal01');
	delete_option('abc_currencyPosition');
	delete_option('abc_bookingform');

} //==>advanced_booking_calendar_uninstall()
register_uninstall_hook( __FILE__, 'advanced_booking_calendar_uninstall');

//Backend Actions:
function advanced_booking_calendar_admin_actions() {
	//Backend Menu
	add_menu_page('Advanced Booking Calendar', 
			'Advanced Booking Calendar', 
			'manage_options', 
			'advanced_booking_calendar', 
			'advanced_booking_calendar_show_bookings', 
			'dashicons-calendar-alt'
			//plugins_url( 'advanced-booking-calendar/images/advanced_booking_calendar.png')
			);
			
	//Submenu "Bookings"
	add_submenu_page('advanced_booking_calendar',
			'Advanced Booking Calendar - '.__('Bookings', 'advanced-booking-calendar'),
			__('Bookings', 'advanced-booking-calendar'),
			'manage_options',
			'advanced_booking_calendar',
			'advanced_booking_calendar_show_bookings'
	);
	//Submenu "Seasons & Calendars"
	add_submenu_page('advanced_booking_calendar',
			'Advanced Booking Calendar - '.__('Seasons & Calendars', 'advanced-booking-calendar'),
			__('Seasons & Calendars', 'advanced-booking-calendar'),
			'manage_options',
			'advanced-booking-calendar-show-seasons-calendars',
			'advanced_booking_calendar_show_seasons_calendars'
	);
	//Submenu "Extras"
	add_submenu_page('advanced_booking_calendar',
			'Advanced Booking Calendar - '.__('Extras', 'advanced-booking-calendar'),
			__('Extras', 'advanced-booking-calendar'),
			'manage_options',
			'advanced-booking-calendar-show-extras',
			'advanced_booking_calendar_show_extras'
	);
	//Submenu "Analytics"
	add_submenu_page('advanced_booking_calendar',
			'Advanced Booking Calendar - '.__('Analytics', 'advanced-booking-calendar'),
			__('Analytics', 'advanced-booking-calendar'),
			'manage_options',
			'advanced-booking-calendar-show-analytics',
			'advanced_booking_calendar_show_analytics'
	);
	//Submenu "Settings"
	add_submenu_page('advanced_booking_calendar',
			'Advanced Booking Calendar - '.__('Settings', 'advanced-booking-calendar'),
			__('Settings', 'advanced-booking-calendar'),
			'manage_options',
			'advanced-booking-calendar-show-settings',
			'advanced_booking_calendar_show_settings'
	);

} //==>advanced_booking_calendar_admin_actions()
add_action('admin_menu', 'advanced_booking_calendar_admin_actions');

// Links on Plugin-Page
add_filter( 'plugin_row_meta', 'abc_plugin_row_meta', 10, 2 );

function abc_plugin_row_meta( $links, $file ) {

	if ( strpos( $file, 'advanced-booking-calendar.php' ) !== false ) {
		$new_links = array(
					'<a href="https://twitter.com/BookingCal" target="_blank">Twitter</a>',
					'<a href="https://booking-calendar-plugin.com/setup-guide" target="_blank">Setup Guide</a>'
				);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}

// Update Check
function advanced_booking_update_check(){
	if ( intval(get_option( 'abc_pluginversion' )) < '110' || intval(get_option( 'abc_pluginversion' )) == 0) {
		update_option('abc_pluginversion', '110');
		add_option('abc_installdate', date('Y-m-d'));
		add_option('abc_poweredby', 0);	
		add_option('abc_feedbackModal01', 0);
		add_option('abc_currencyPosition', 1);
    }
    if(intval(get_option( 'abc_pluginversion' )) < '117'){
		abc_booking_setPersonCount();
	}
    if(intval(get_option( 'abc_pluginversion' )) < '118'){
		update_option('abc_pluginversion', '118');
		global $wpdb;
		$wpdb->query("ALTER TABLE `".$wpdb->prefix."advanced_booking_calendar_calendars` ADD `minimumStayPreset` INT(16) NOT NULL AFTER `pricePreset`;");
		$wpdb->query("ALTER TABLE `".$wpdb->prefix."advanced_booking_calendar_seasons` ADD `minimumStay` INT(16) NOT NULL AFTER `lastminute`;");
		$wpdb->query("UPDATE `".$wpdb->prefix."advanced_booking_calendar_calendars` SET `minimumStayPreset` = 1;");
		$wpdb->query("UPDATE `".$wpdb->prefix."advanced_booking_calendar_seasons` SET `minimumStay` = 1;");
	}
	if(intval(get_option( 'abc_pluginversion' )) < '119'){
		update_option('abc_pluginversion', '119');
		global $wpdb;
		$wpdb->query("ALTER TABLE `".$wpdb->prefix."advanced_booking_calendar_calendars` ADD `partlyBooked` INT(16) NOT NULL AFTER `minimumStayPreset`;");
		$wpdb->query("UPDATE `".$wpdb->prefix."advanced_booking_calendar_calendars` SET `partlyBooked` = 1;");
	}
	
	if(intval(get_option( 'abc_pluginversion' )) < '120'){
		update_option('abc_pluginversion', '120');
		global $wpdb;
		$extras ="CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_extras` ( 
			`id` INT(16) NOT NULL AUTO_INCREMENT , 
			`name` TEXT NOT NULL , 
			`explanation` TEXT NOT NULL , 
			`calculation` TEXT NOT NULL ,
			`mandatory` TEXT NOT NULL , 
			`price` FLOAT(32) NOT NULL ,
			 PRIMARY KEY (`id`) ) charset=utf8";
		$bookingExtras ="CREATE TABLE `".$wpdb->prefix."advanced_booking_calendar_booking_extras` ( 
			`id` INT(32) NOT NULL AUTO_INCREMENT , 
			`booking_id` INT(32) NOT NULL , 
			`extra_id` INT(32) NOT NULL , 
			 PRIMARY KEY (`id`) ) charset=utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($extras);
		dbDelta($bookingExtras);
		$wpdb->query("ALTER TABLE `".$wpdb->prefix."advanced_booking_calendar_bookings` ADD `county` TEXT NOT NULL AFTER `city`;");
		update_option('abc_bookingform', array(
		 		'firstname' => '2',
		 		'lastname' => '2',
		 		'phone' => '2',
		 		'street' => '2',
		 		'zip' => '2',
		 		'city' => '2',
		 		'county' => '0',
		 		'country' => '2',
		 		'message' => '1',
		 		'inputs' => '8'
				));
	}
		
}
add_action( 'plugins_loaded', 'advanced_booking_update_check' );

?>