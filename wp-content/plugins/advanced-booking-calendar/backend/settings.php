<?php

//Edit  general settings
function abc_booking_editCalendarSettings() {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die("Go away");
	}
	if (isset($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && isset($_POST["currency"]) && isset($_POST["dateformat"]) && isset($_POST["priceformat"]) && isset($_POST["cookies"]) ) {
		update_option ('abc_email', sanitize_email($_POST["email"]));
		update_option ('abc_bookingpage', sanitize_text_field($_POST["bookingpage"]));
		update_option ('abc_dateformat', sanitize_text_field($_POST["dateformat"]));
		update_option ('abc_priceformat', sanitize_text_field($_POST["priceformat"]));
		update_option ('abc_currency', sanitize_text_field($_POST["currency"]));
		update_option ('abc_currencyPosition', intval($_POST["currencyPosition"]));
		update_option ('abc_cookies', sanitize_text_field($_POST["cookies"]));
		update_option ('abc_googleanalytics', intval($_POST["googleanalytics"]));
		update_option ('abc_poweredby', intval($_POST["poweredby"]));
		if(getAbcSetting("newsletter") == 0 && intval($_POST["newsletter"]) == 1){
			subscribeAbcNewsletter(sanitize_email($_POST["email"]), 0);
			update_option ('abc_newsletter', intval($_POST["newsletter"]));
		}elseif(getAbcSetting("newsletter") == 1 && intval($_POST["newsletter"]) == 0){
			subscribeAbcNewsletter(sanitize_email($_POST["email"]), 1);
			update_option ('abc_newsletter', sanitize_email($_POST["newsletter"]));
		}
	}

	wp_redirect(  admin_url( "admin.php?page=advanced-booking-calendar-show-settings&setting=general" ) );
	exit;
} //==>editCalendarSettings()
add_action( 'admin_post_abc_booking_editCalendarSettings', 'abc_booking_editCalendarSettings' );

//Edit booking form settings
function abc_booking_editBookingFormSettings() {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die("Go away");
	}
	if (isset($_POST["firstname"]) &&  isset($_POST["lastname"]) &&  isset($_POST["phone"]) &&  isset($_POST["street"])
		 &&  isset($_POST["zip"]) &&  isset($_POST["city"]) &&  isset($_POST["county"])  
		 &&  isset($_POST["country"])  &&  isset($_POST["message"])) {
		 	$fieldCounter = 0;
			if(intval($_POST["firstname"]) > 0) {$fieldCounter++;}
			if(intval($_POST["lastname"]) > 0) {$fieldCounter++;}
			if(intval($_POST["phone"]) > 0) {$fieldCounter++;}
			if(intval($_POST["street"]) > 0) {$fieldCounter++;}
			if(intval($_POST["zip"]) > 0) {$fieldCounter++;}
			if(intval($_POST["city"]) > 0) {$fieldCounter++;}
			if(intval($_POST["county"]) > 0) {$fieldCounter++;}
			if(intval($_POST["country"]) > 0) {$fieldCounter++;}
			if(intval($_POST["message"]) > 0) {$fieldCounter++;}
		 	$options = array(
		 		'firstname' => intval($_POST["firstname"]),
		 		'lastname' => intval($_POST["lastname"]),
		 		'phone' => intval($_POST["phone"]),
		 		'street' => intval($_POST["street"]),
		 		'zip' => intval($_POST["zip"]),
		 		'city' => intval($_POST["city"]),
		 		'county' => intval($_POST["county"]),
		 		'country' => intval($_POST["country"]),
		 		'message' => intval($_POST["message"]),
		 		'inputs' => $fieldCounter
				);
			update_option('abc_bookingform', $options);
	}

	wp_redirect(  admin_url( "admin.php?page=advanced-booking-calendar-show-settings&setting=bookingform" ) );
	exit;
} //==>editCalendarSettings()
add_action( 'admin_post_abc_booking_editBookingFormSettings', 'abc_booking_editBookingFormSettings' );

//Edit email settings
function abc_booking_editEmailSettings() {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die("Go away");
	}
	if (isset($_POST["subjectunconfirmed"]) && isset($_POST["textunconfirmed"]) 
			&& isset($_POST["subjectconfirmed"]) && isset($_POST["textconfirmed"])
			&& isset($_POST["subjectcanceled"]) && isset($_POST["textcanceled"])
			&& isset($_POST["subjectrejected"]) && isset($_POST["textrejected"])
		) {
			update_option ('abc_subject_unconfirmed', sanitize_text_field($_POST["subjectunconfirmed"]));
			update_option ('abc_text_unconfirmed', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST["textunconfirmed"] ))));
			update_option ('abc_subject_confirmed', sanitize_text_field($_POST["subjectconfirmed"]));
			update_option ('abc_text_confirmed', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST["textconfirmed"] ))));
			update_option ('abc_subject_canceled', sanitize_text_field($_POST["subjectcanceled"]));
			update_option ('abc_text_canceled', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST["textcanceled"] ))));
			update_option ('abc_subject_rejected', sanitize_text_field($_POST["subjectrejected"]));
			update_option ('abc_text_rejected', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST["textrejected"] ))));
	}

	wp_redirect(  admin_url( "admin.php?page=advanced-booking-calendar-show-settings&setting=email" ) );
	exit;
} //==>editEmailSettings()
add_action( 'admin_post_abc_booking_editEmailSettings', 'abc_booking_editEmailSettings' );

//Backend output:
function advanced_booking_calendar_show_settings() {
	global $abcUrl;
	wp_enqueue_script('uikit-js', $abcUrl.'backend/js/uikit.min.js', array('jquery'));
	wp_enqueue_style('uikit', $abcUrl.'/frontend/css/uikit.gradient.min.css');
	
	//Preparing Date vars (saved format is selected)
	$date1 = "";
	$date2 = "";
	$date3 = "";
	$date4 = "";
	if(getAbcSetting("dateformat") == "Y-m-d") {
		$date1 = 'selected';
	} elseif(getAbcSetting("dateformat") == "d.m.Y") {
		$date2 = 'selected';
	} elseif(getAbcSetting("dateformat") == "d/m/Y") {
		$date3 = 'selected';
	} elseif(getAbcSetting("dateformat") == "m/d/Y") {
		$date4 = 'selected';
	}
	
	//Price Format
	$priceComma = "";
	$priceDot = "";
	$currencyPositionBefore = "";
	$currencyPositionAfter = "";
	$newslettertrue = "";
	$newsletterfalse = "";
	$cookiestrue = "";
	$cookiesfalse = "";
	$gatrue = "";
	$gafalse = "";
	$poweredbytrue = "";
	$poweredbyfalse = "";
	$firstdayofweekSunday = "";
	$firstdayofweekMonday = "";
	if(getAbcSetting("priceformat") == ",") {
		$priceComma = 'selected';
	} elseif(getAbcSetting("priceformat") == ".") {
		$priceDot = 'selected';
	}
	if(getAbcSetting("currencyPosition") == 0) {
		$currencyPositionBefore = 'checked';
	} elseif(getAbcSetting("currencyPosition") == 1) {
		$currencyPositionAfter = 'checked';
	}
	if(getAbcSetting("newsletter") == "1") {
		$newslettertrue = 'checked';
	} elseif(getAbcSetting("newsletter") == "0") {
		$newsletterfalse = 'checked';
	}
	if(getAbcSetting("cookies") == "1") {
		$cookiestrue = 'checked';
	} elseif(getAbcSetting("cookies") == "0") {
		$cookiesfalse = 'checked';
	}
	if(getAbcSetting("googleanalytics") == "1") {
		$gatrue = 'checked';
	} elseif(getAbcSetting("googleanalytics") == "0") {
		$gafalse = 'checked';
	}
	if(getAbcSetting("poweredby") == "1") {
		$poweredbytrue = 'checked';
	} elseif(getAbcSetting("poweredby") == "0") {
		$poweredbyfalse = 'checked';
	}
	if(getAbcSetting("firstdayofweek") == "0") {
		$firstdayofweekSunday = 'checked';
	} elseif(getAbcSetting("firstdayofweek") == "1") {
		$firstdayofweekMonday = 'checked';
	}
	$bookingVarArray = abc_booking_getBookingVars();
	$placeholderList = '';
	$numItems = count($bookingVarArray);
	$i = 0;
	foreach ($bookingVarArray as $bookingVars){
		$placeholderList .= '['.$bookingVars.']';
		if(++$i === $numItems) {
			$placeholderList .= '.';
		} else {
			$placeholderList .= ', ';			
		}
	}
	$bookingForm = getAbcSetting("bookingform");	
	$firstname = array('', '', '');
	$lastname = array('', '', '');
	$phone = array('', '', '');
	$street = array('', '', '');
	$zip = array('', '', '');
	$county = array('', '', '');
	$city = array('', '', '');
	$country = array('', '', '');
	$message = array('', '', '');
	switch ($bookingForm["firstname"]) {
		case '0':$firstname[0] = ' checked';break;
		case '1':$firstname[1] = ' checked';break;
		case '2':$firstname[2] = ' checked';break;
	}
	switch ($bookingForm["lastname"]) {
		case '0':$lastname[0] = ' checked';break;
		case '1':$lastname[1] = ' checked';break;
		case '2':$lastname[2] = ' checked';break;
	}
	switch ($bookingForm["phone"]) {
		case '0':$phone[0] = ' checked';break;
		case '1':$phone[1] = ' checked';break;
		case '2':$phone[2] = ' checked';break;
	}
	switch ($bookingForm["street"]) {
		case '0':$street[0] = ' checked';break;
		case '1':$street[1] = ' checked';break;
		case '2':$street[2] = ' checked';break;
	}
	switch ($bookingForm["zip"]) {
		case '0':$zip[0] = ' checked';break;
		case '1':$zip[1] = ' checked';break;
		case '2':$zip[2] = ' checked';break;
	}
	switch ($bookingForm["county"]) {
		case '0':$county[0] = ' checked';break;
		case '1':$county[1] = ' checked';break;
		case '2':$county[2] = ' checked';break;
	}
	switch ($bookingForm["city"]) {
		case '0':$city[0] = ' checked';break;
		case '1':$city[1] = ' checked';break;
		case '2':$city[2] = ' checked';break;
	}
	switch ($bookingForm["country"]) {
		case '0':$country[0] = ' checked';break;
		case '1':$country[1] = ' checked';break;
		case '2':$country[2] = ' checked';break;
	}
	switch ($bookingForm["message"]) {
		case '0':$message[0] = ' checked';break;
		case '1':$message[1] = ' checked';break;
		case '2':$message[2] = ' checked';break;
	}
	$settingsMessage = '';
	if ( isset($_GET["setting"]) ) {
		switch ($_GET["setting"]) {
			case 'email':
					$settingsMessage .= '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
										<p><strong>'.__('Email settings have been saved.', 'advanced-booking-calendar').'</strong></p><button type="button" class="notice-dismiss">
										<span class="screen-reader-text">'.__('Dismiss this notice.', 'advanced-booking-calendar').'</span></button></div>';
					break;
			case 'general':
					$settingsMessage .= '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
										<p><strong>'.__('General settings have been saved.', 'advanced-booking-calendar').'</strong></p><button type="button" class="notice-dismiss">
										<span class="screen-reader-text">'.__('Dismiss this notice.', 'advanced-booking-calendar').'</span></button></div>';
					break;
			case 'bookingform':
					$settingsMessage .= '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
										<p><strong>'.__('Booking form settings have been saved. Please make sure to update the email templates as well.', 'advanced-booking-calendar').'</strong></p><button type="button" class="notice-dismiss">
										<span class="screen-reader-text">'.__('Dismiss this notice.', 'advanced-booking-calendar').'</span></button></div>';
					break;
		}
	}
	//Regex for email pattern
	$emailPattern = "[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$";
	echo '<div class="wrap">
		  <h1>'.__('Settings', 'advanced-booking-calendar').'</h1>
		  '.$settingsMessage.'
		  <div>
		  <ul class="uk-tab" data-uk-tab="{connect:\'#tab-content\'}">
					<li><a href="#">'.__('General Settings', 'advanced-booking-calendar').'</a></li>
					<li><a href="#">'.__('Booking Form Settings', 'advanced-booking-calendar').'</a></li>
					<li><a href="#">'.__('Email Settings', 'advanced-booking-calendar').'</a></li>
		  </ul>
		  <ul id="tab-content" class="uk-switcher uk-margin">
		   	<li>	
			  <form method="post" class="uk-form uk-form-horizontal" action="admin-post.php">
			  <input type="hidden" name="action" value="abc_booking_editCalendarSettings" />
			  <div class="uk-form-row">
			      <label class="uk-form-label" for="email">'.__('Email', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls"><input pattern="'.$emailPattern.'" name="email" id="email" type="email" value="'.getAbcSetting("email").'" class="regular-text code" placeholder="mail@mail.com" required />
				      <p class="description">'.__('This Email Address will receive all booking requests', 'advanced-booking-calendar').'</p></div>
			    </div>
			  <div class="uk-form-row">
			      <label class="uk-form-label" for="newsletter">Advanced Booking Calendar '.__('Newsletter', 'advanced-booking-calendar').':</label>
			       <div class="uk-form-controls">
			      <fieldset>
			      	<input '.$newslettertrue.' type="radio" name="newsletter" id="newsletter-true" value="1"><label for="newsletter-true"> '.__('Yes, I want to get vital informations on how to raise my occupation rate and stay tuned about Advanced Booking Calendar', 'advanced-booking-calendar').'</label><br/>
			      	<input '.$newsletterfalse.' type="radio" name="newsletter" id="newsletter-false" value="0"><label for="newsletter-false"> '.__('No, disable the newsletter', 'advanced-booking-calendar').'</label>
			      </fieldset>
			      	<p class="description">'.__('You will receive regular tips on how to create great Hotel websites and informations about this plugin. We promise to never spam you. You can unsubscribe anytime.', 'advanced-booking-calendar').'</p>
				     </div>
			    </div>
				<div class="uk-form-row">
			      <label class="uk-form-label" for="bookingpage">'.__('Page with booking form', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls">
			      	'.wp_dropdown_pages(
			      		array(
			      			'echo' => 0, 
			      			'show_option_none' => __('Not selected', 'advanced-booking-calendar'), 
			      			'option_none_value' => "0", 
			      			'selected' => getAbcSetting("bookingpage"),
			      			'name' => "bookingpage"
			      			)
			      		).' 
			   		<a target="_blank" href="edit.php?post_type=page">'.__('Manage Pages', 'advanced-booking-calendar').'</a><br />
			        <p class="description">'.__('Select the page which uses the booking form shortcode. You have to add the shortcode [abc-bookingform] manually to this page. The page will be linked, if user selects dates on the single calendar shortcode.', 'advanced-booking-calendar').'</p></div>
			    </div>
				<div class="uk-form-row">
			      <label class="uk-form-label" for="currency">'.__('Currency', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls"><input name="currency" id="currency" value="'.getAbcSetting("currency").'" class="regular-text" placeholder="&euro;" required />
				      <p class="description">'.__('Example: &euro; or $', 'advanced-booking-calendar').'</p></div>
			    </div>
				<div class="uk-form-row">
			      <label class="uk-form-label" for="priceformat">'.__('Price format', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls">
			      	 <select name="priceformat" id="priceformat">
				        <option value="," '.$priceComma.'>0,00</option>
						<option value="." '.$priceDot.'>0.00</option>
				  	  </select></div>
			    </div>
				<div class="uk-form-row">
			      <label class="uk-form-label" for="dateformat">'.__('Date format', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls"><select name="dateformat" id="dateformat">
				        <option value="Y-m-d" '.$date1.'>2016-12-15</option>
						<option value="d.m.Y" '.$date2.'>15.12.2016</option>
						<option value="d/m/Y" '.$date3.'>15/12/2016</option>
						<option value="m/d/Y" '.$date4.'>12/15/2016</option>
				  	  </select></div>
			    </div>
				<div class="uk-form-row">
			      <label class="uk-form-label" for="currencyPosition">'.__('Position of currency sign', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls">
				      <fieldset>
				      	<input '.$currencyPositionBefore.' type="radio" name="currencyPosition" id="currencyPositionBefore" value="0">
				      		<label for="currencyPositionBefore"> '.__('Before the amount', 'advanced-booking-calendar').' ($ 50)</label><br/>
				      	<input '.$currencyPositionAfter.' type="radio" name="currencyPosition" id="currencyPositionAfter" value="1">
				      		<label for="currencyPositionAfter"> '.__('After the amount', 'advanced-booking-calendar').' (50 $)</label>
				      </fieldset>
				  </div>
			    </div>
			    <div class="uk-form-row">
			      <label class="uk-form-label" for="cookies">'.__('Cookies', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls">
			      <fieldset>
			      	<input '.$cookiestrue.' type="radio" name="cookies" id="cookies-true" value="1"><label for="cookies-true"> '.__('Enabled', 'advanced-booking-calendar').'</label><br/>
			      	<input '.$cookiesfalse.' type="radio" name="cookies" id="cookies-false" value="0"><label for="cookies-false"> '.__('Disabled', 'advanced-booking-calendar').'</label>
			      </fieldset>
			      	<p class="description">'.__('If cookies are enabled, customer date inputs are saved in a cookie (no personal data is stored in the cookie).', 'advanced-booking-calendar').'</p>
				     </div>
			    </div>
			    <div class="uk-form-row">
			      <label class="uk-form-label" for="googleanalytics">Google Analytics:</label>
			      <div class="uk-form-controls">
			      <fieldset>
			      	<input '.$gatrue.' type="radio" name="googleanalytics" id="googleanalytics-true" value="1"><label for="googleanalytics-true"> '.__('Enabled', 'advanced-booking-calendar').'</label><br/>
			      	<input '.$gafalse.' type="radio" name="googleanalytics" id="googleanalytics-false" value="0"><label for="googleanalytics-false"> '.__('Disabled', 'advanced-booking-calendar').'</label>
			      </fieldset>
			      	<p class="description">'.__('If enabled, user interactions with calendars and forms will be tracked in your Google Universal Analytics. Please configure Universal Analytics seperately. We recommend using the following plugin:', 'advanced-booking-calendar').' 
			      			<a href="https://wordpress.org/plugins/google-universal-analytics/" target="_blank">Google Universal Analytics</a>.</p>
				     </div>
			    </div>
			    <div class="uk-form-row">
			      <label class="uk-form-label" for="googleanalytics">'.__('Powered-by-Link', 'advanced-booking-calendar').':</label>
			      <div class="uk-form-controls">
			      <fieldset>
			      	<input '.$poweredbytrue.' type="radio" name="poweredby" id="poweredby-true" value="1"><label for="poweredby-true"> '.__('Enabled', 'advanced-booking-calendar').'</label><br/>
			      	<input '.$poweredbyfalse.' type="radio" name="poweredby" id="poweredby-false" value="0"><label for="poweredby-false"> '.__('Disabled', 'advanced-booking-calendar').'</label>
			      </fieldset>
			      	<p class="description">'.__('If link is enabled, a tiny "powered by Advanced Booking Calendar"-link will show up below the calendar overview.', 'advanced-booking-calendar').'</p>
				     </div>
			    </div>
				<div class="uk-form-row">
					<input class="button button-primary" type="submit" value="'.__('Save', 'advanced-booking-calendar').'" />
				</div>	
			  </form>
			</li>
			<li>
				<h3>'.__('Booking Form Inputs', 'advanced-booking-calendar').'</h3>
				<form method="post" class="uk-form uk-form-horizontal" action="admin-post.php">
				<input type="hidden" name="action" value="abc_booking_editBookingFormSettings" />
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="firstname">'.__('First Name', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="firstname" id="firstname-required" value="2"'.$firstname[2].'> <label for="firstname-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="firstname" id="firstname-optional" value="1"'.$firstname[1].'> <label for="firstname-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="firstname" id="firstname-disabled" value="0"'.$firstname[0].'> <label for="firstname-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="last">'.__('Last Name', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="lastname" id="lastname-required" value="2"'.$lastname[2].'> <label for="lastname-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="lastname" id="lastname-optional" value="1"'.$lastname[1].'> <label for="lastname-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="lastname" id="lastname-disabled" value="0"'.$lastname[0].'> <label for="lastname-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="phone">'.__('Phone Number', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="phone" id="phone-required" value="2"'.$phone[2].'> <label for="phone-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="phone" id="phone-optional" value="1"'.$phone[1].'> <label for="phone-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="phone" id="phone-disabled" value="0"'.$phone[0].'> <label for="phone-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="street">'.__('Street Address, House no.', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="street" id="street-required" value="2"'.$street[2].'> <label for="street-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="street" id="street-optional" value="1"'.$street[1].'> <label for="street-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="street" id="street-disabled" value="0"'.$street[0].'> <label for="street-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="zip">'.__('ZIP Code', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="zip" id="zip-required" value="2"'.$zip[2].'> <label for="zip-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="zip" id="zip-optional" value="1"'.$zip[1].'> <label for="zip-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="zip" id="zip-disabled" value="0"'.$zip[0].'> <label for="zip-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="county">'.__('State / County', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="county" id="county-required" value="2"'.$county[2].'> <label for="county-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="county" id="county-optional" value="1"'.$county[1].'> <label for="county-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="county" id="county-disabled" value="0"'.$county[0].'> <label for="county-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="city">'.__('City', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="city" id="city-required" value="2"'.$city[2].'> <label for="city-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="city" id="city-optional" value="1"'.$city[1].'> <label for="city-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="city" id="city-disabled" value="0"'.$city[0].'> <label for="city-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="country">'.__('Country', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="country" id="country-required" value="2"'.$country[2].'> <label for="country-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="country" id="country-optional" value="1"'.$country[1].'> <label for="country-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="country" id="country-disabled" value="0"'.$country[0].'> <label for="country-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
			    <div class="uk-form-row">
			    	<label class="uk-form-label" for="message">'.__('message', 'advanced-booking-calendar').':</label>
			    	<div class="uk-form-controls">
				    	<fieldset>
				      		<input type="radio" name="message" id="message-required" value="2"'.$message[2].'> <label for="message-required"> '.__('Required', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="message" id="message-optional" value="1"'.$message[1].'> <label for="message-optional"> '.__('Optional', 'advanced-booking-calendar').'</label><br/>
				      		<input type="radio" name="message" id="message-disabled" value="0"'.$message[0].'> <label for="message-disabled"> '.__('Disabled', 'advanced-booking-calendar').'</label>
				     	</fieldset>
			      	</div>
			    </div>
				<div class="uk-form-row">
					<input class="button button-primary" type="submit" value="'.__('Save', 'advanced-booking-calendar').'" />
				</div>	
			    </form>
			</li>
		   	<li>
			  <h3>'.__('Placeholders', 'advanced-booking-calendar').'</h3>
			  <p>'.__('You can use the following placeholder in both subject and text. They will be replaced with the actual content when the email is send to the guest:', 'advanced-booking-calendar').'<br/>
			  <i>'.$placeholderList.'</i></p>
			  <hr>
			  <form method="post" class="uk-form uk-form-horizontal" action="admin-post.php">
				<input type="hidden" name="action" value="abc_booking_editEmailSettings" />
				<h3>'.__('Templates', 'advanced-booking-calendar').'</h3>
				<h4>'.__('Unconfirmed Booking', 'advanced-booking-calendar').'</h4>
				<div class="uk-form-row">
					<label class="uk-form-label" for="subjectunconfirmed">'.__('Subject for an unconfirmed booking mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<input class="uk-form-width-large" name="subjectunconfirmed" id="subjectunconfirmed" value="'.get_option('abc_subject_unconfirmed').'"/>
					</div>
				</div>	
				<div class="uk-form-row">
					<label class="uk-form-label" for="textunconfirmed">'.__('Text for an unconfirmed booking mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<textarea class="uk-form-width-large" rows="10" name="textunconfirmed" id="textunconfirmed">'.get_option('abc_text_unconfirmed').'</textarea>	
					</div>
				</div>
				<h4>'.__('Confirming an open Booking', 'advanced-booking-calendar').'</h4>
				<div class="uk-form-row">
					<label class="uk-form-label" for="subjectconfirmed">'.__('Subject for a booking confirmation mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<input class="uk-form-width-large" name="subjectconfirmed" id="subjectconfirmed" value="'.get_option('abc_subject_confirmed').'"/>
					</div>
				</div>	
				<div class="uk-form-row">
					<label class="uk-form-label" for="textconfirmed">'.__('Text for a booking confirmation mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<textarea class="uk-form-width-large" rows="10" name="textconfirmed" id="textconfirmed">'.get_option('abc_text_confirmed').'</textarea>	
					</div>
				<h4>'.__('Canceling a confirmed Booking', 'advanced-booking-calendar').'</h4>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label" for="subjectcanceled">'.__('Subject for a cancelation mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<input class="uk-form-width-large" name="subjectcanceled" id="subjectcanceled" value="'.get_option('abc_subject_canceled').'"/>
					</div>
				</div>	
				<div class="uk-form-row">
					<label class="uk-form-label" for="textcanceled">'.__('Text for a cancelation mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<textarea class="uk-form-width-large" rows="10" name="textcanceled" id="textcanceled">'.get_option('abc_text_canceled').'</textarea>	
					</div>
				</div>
				<h4>'.__('Rejecting an open Booking', 'advanced-booking-calendar').'</h4>
				<div class="uk-form-row">
					<label class="uk-form-label" for="subjectrejected">'.__('Subject for a rejection mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<input class="uk-form-width-large" name="subjectrejected" id="subjectrejected" value="'.get_option('abc_subject_rejected').'"/>
					</div>
				</div>	
				<div class="uk-form-row">
					<label class="uk-form-label" for="textrejected">'.__('Text for a rejection mail', 'advanced-booking-calendar').'</label>
					<div class="uk-form-controls">
						<textarea class="uk-form-width-large" rows="10" name="textrejected" id="textrejected">'.get_option('abc_text_rejected').'</textarea>	
					</div>
				</div>
				<div class="uk-form-row">
					<input class="button button-primary" type="submit" value="'.__('Save', 'advanced-booking-calendar').'" />
				</div>	
			  </form>
			</li>
		</ul>
		</div>
		</div>';

}//==>advanced_booking_calendar_show_settings()
	
?>