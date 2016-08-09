<?php
//Function for shortcode "abc-bookingform". Form asks for customers input, tracks those inputs, shows availabilities and creates bookings.
function abc_booking_showBookingForm( $atts ) {
	global $abcUrl;
	wp_enqueue_style( 'styles-css', $abcUrl.'frontend/css/styles.css' );
	wp_enqueue_style( 'font-awesome', $abcUrl.'frontend/css/font-awesome.min.css' );
	wp_enqueue_script('abc-functions', $abcUrl.'frontend/js/abc-functions.js', array('jquery'));
	wp_enqueue_script('abc-ajax', $abcUrl.'frontend/js/abc-ajax.js', array('jquery'));
	wp_enqueue_script('jquery-validate', $abcUrl.'frontend/js/jquery.validate.min.js', array('jquery'));
	wp_enqueue_script('jquery-ui-datepicker');
	$dateformat = abc_booking_dateFormatToJS(getAbcSetting("dateformat"));
	wp_localize_script( 'abc-functions', 'abc_functions_vars', array( 'dateformat' => $dateformat, 'firstday' => getAbcSetting("firstdayofweek")));
	wp_localize_script( 'abc-ajax', 'ajax_abc_booking_showBookingForm', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_enqueue_style('abc-datepicker', $abcUrl.'/frontend/css/jquery-ui.min.css');
	$validateLang = array('ar','bg','bn_BD','ca','cs','da','de','el','es_AR','es_PE','es','et','eu','fa','fi',
		'fr','ge','gl','he','hr','hu','hy_AM','id','is','it','ja','ka','kk','ko','lt','lv','mk','my','nl','no',
		'pl','pt_BR','pt_PT','ro','ru','si','sk','sl','sr_lat','sr','sv','th','tj','tr','uk','vi','zh_TW','zh');
	if(substr(get_locale(), 0,2) != 'en' && in_array(get_locale(), $validateLang)){
		wp_enqueue_script('jquery-validate-lang', $abcUrl.'frontend/js/validate_lang/messages_'.get_locale().'.js', array('jquery'));
	}elseif(substr(get_locale(), 0,2) != 'en' && in_array(substr(get_locale(), 0,2), $validateLang)){
		wp_enqueue_script('jquery-validate-lang', $abcUrl.'frontend/js/validate_lang/messages_'.substr(get_locale(), 0,2).'.js', array('jquery'));
	}
	$datepickerLang = array('af','ar-DZ','ar','az','be','bg','bs','ca','cs','cy-GB','da','de','el','en-AU','en-GB','en-NZ',
		'eo','es','et','eu','fa','fi','fo','fr-CA','fr-CH','fr','gl','he','hi','hr','hu','hy','id','is',
		'it-CH','it','ja','ka','kk','km','ko','ky','lb','lt','lv','mk','ml','ms','nb','nl-BE','nl','nn',
		'no','pl','pt-BR','pt','rm','ro','ru','sk','sl','sq','sr-SR','sr','sv','ta','th','tj','tr','uk',
		'vi','zh-CN','zh-HK','zh-TW');
	if(substr(get_locale(), 0,2) != 'en' && in_array(get_locale(), $datepickerLang)){
		wp_enqueue_script('jquery-datepicker-lang', $abcUrl.'frontend/js/datepicker_lang/datepicker-'.get_locale().'.js', array('jquery'));
	}elseif(substr(get_locale(), 0,2) != 'en' && in_array(substr(get_locale(), 0,2), $datepickerLang)){
		wp_enqueue_script('jquery-datepicker-lang', $abcUrl.'frontend/js/datepicker_lang/datepicker-'.substr(get_locale(), 0,2).'.js', array('jquery'));
	}
	$bookingFormSetting = getAbcSetting("bookingform");	
	$validateRules = array('email' => array( 'required' => true, 'email' => true));
	if($bookingFormSetting["firstname"] == 2){$validateRules["first_name"]["required"] = true;}
	if($bookingFormSetting["lastname"] == 2){$validateRules["last_name"]["required"] = true;}
	if($bookingFormSetting["phone"] == 2){$validateRules["phone"]["required"] = true;}
	if($bookingFormSetting["street"] == 2){$validateRules["address"]["required"] = true;}
	if($bookingFormSetting["zip"] == 2){$validateRules["zip"]["required"] = true;}
	if($bookingFormSetting["city"] == 2){$validateRules["city"]["required"] = true;}
	if($bookingFormSetting["county"] == 2){$validateRules["county"]["required"] = true;}
	if($bookingFormSetting["country"] == 2){$validateRules["country"]["required"] = true;}
	if($bookingFormSetting["message"] == 2){$validateRules["message"]["required"] = true;}
	wp_localize_script( 'abc-ajax', 'ajax_abc_booking_showBookingForm', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'rules' => $validateRules));
	$abcFromValue = '';
	$abcToValue = '';
	$abcPostTrigger = 0;
	$bookingFormResult = '';	
	if(isset($_POST['abc-from']) && isset($_POST['abc-to']) 
		&& abc_booking_validateDate($_POST['abc-from'], getAbcSetting("dateformat"))
		&& abc_booking_formatDateToDB($_POST['abc-from']) >= date('Y-m-d') )
		{ // Checking for POST variables (via single calendar)
			$abcFromValue = sanitize_text_field($_POST['abc-from']);
			$abcToValue = sanitize_text_field($_POST['abc-to']);
	}elseif(isset($_COOKIE['abc-from']) && isset($_COOKIE['abc-to'])  
		&& abc_booking_validateDate($_COOKIE['abc-from'], getAbcSetting("dateformat"))
		&& abc_booking_formatDateToDB($_COOKIE['abc-from']) >= date('Y-m-d'))
		{ // Checking for cookies and checking if "from date" is in the past
			$abcFromValue = sanitize_text_field($_COOKIE['abc-from']);
			$abcToValue = sanitize_text_field($_COOKIE['abc-to']);
	}
	if(isset($_POST['abc-trigger']) && $_POST['abc-trigger'] == 1){
		$abcPostTrigger = 1;		
	}	
	$abcPersonValue = 1;
	if(isset($_POST['abc-persons'])){ // Checking for cookies
		$abcPersonValue = intval($_POST['abc-persons']);
	}elseif(isset($_COOKIE['abc-persons'])){ // Checking for cookies
		$abcPersonValue = intval($_COOKIE['abc-persons']);
	}
	$optionPersons = '';
	for( $i = 1; $i <= getAbcSetting('personcount'); $i++) { 
		$optionPersons .= '<option value="'.$i.'"';
		if ( $i == $abcPersonValue) {
			$optionPersons .= ' selected';
		}
		$optionPersons .= '>'.$i.'</option>';
	}
	$bookingFormResult .= '
	<div id="abc-form-wrapper">
		<img alt="'.__('Loading...', 'advanced-booking-calendar').'" src="'.admin_url('/images/wpspin_light.gif').'" align="middle" class="waiting" id="abc_bookinform_loading" style="display:none" />				
		<div id="abc-form-content">
			<div class="abc-column">
				<form class="abc-form"  method="post">
					<label for="abc-from">'.__('Checkin', 'advanced-booking-calendar').'</label>
					<div class="abc-input-fa">
						<span class="fa fa-calendar"></span>
						<input id="abc-from" name="abc-from" class="abc-from" value="'.$abcFromValue.'">
					</div>
					<label for="abc-to">'.__('Checkout', 'advanced-booking-calendar').'</label>
					<div class="abc-input-fa">
						<span class="fa fa-calendar"></span>
						<input id="abc-to" name="abc-to" class="abc-to" value="'.$abcToValue.'">
					</div>
			</div>			
			<div class="abc-column abc-form">	
					<label for="abc-persons">'.__('Persons', 'advanced-booking-calendar').'</label>
					<div class="abc-input-fa">
						<span class="fa fa-female abc-guest1"></span>
						<span class="fa fa-male abc-guest2"></span>
						<select id="abc-persons" name="abc-persons">
							'.$optionPersons.'
						</select>
					</div>
					<input id="abcPostTrigger" type="hidden" name="abcPostTrigger" value="'.$abcPostTrigger.'">	
				</form>	
			</div>
			<div class="abc-form-row">
				<button class="abc-submit" id="abc-check-availabilities"><span id="abc-submit-button" class="abc-submit-text">'.__('Check availabilities', 'advanced-booking-calendar').'</span><span class="abc-submit-loading" />'.__('Loading...', 'advanced-booking-calendar').'</span></button>
				<button class="abc-submit" id="abc-back-to-availabilities" style="display: none;"><span class="abc-submit-text">'.__('Edit', 'advanced-booking-calendar').'</span></button>
			</div>	
			
			<div id="abc-bookingresults"></div>
		</div>
	</div>
		';
	return $bookingFormResult;
}

//AJAX-Request for saving the customer inputs to the cookie, checking availabilities and calculating prices.
function ajax_abc_booking_getBookingResult () {
	$dateformat = getAbcSetting("dateformat");
	if (isset($_POST["from"])  && isset($_POST["to"]) 
		&& abc_booking_validateDate($_POST["from"], $dateformat) 
		&& abc_booking_validateDate($_POST["to"], $dateformat)
		&& $_POST["from"] != $_POST["to"]){
		global $wpdb;
		$abcFromValue = sanitize_text_field($_POST["from"]); 
		$abcToValue = sanitize_text_field($_POST["to"]);
		$abcPersons = intval($_POST["persons"]);
		// Normalizing entered dates
		$normFromValue = abc_booking_formatDateToDB($abcFromValue);
		$normToValue = abc_booking_formatDateToDB($abcToValue);
		$isSuccessful = false;
		
		//Setting Cookies
		if(getAbcSetting("cookies") == 1) {
			$domain = str_replace('www', '', str_replace('https://','',str_replace('http://','',get_site_url()))); // Getting domain-name for creating cookies
			setcookie('abc-from', $abcFromValue, time()+3600*24*30*6, '/',  $domain);
			setcookie('abc-to', $abcToValue, time()+3600*24*30*6, '/', $domain );
			setcookie('abc-persons', $abcPersons, time()+3600*24*30*6, '/', $domain );
		}
					
		//Getting actual results
		$requestQuery = "SELECT * FROM ".$wpdb->prefix."advanced_booking_calendar_calendars WHERE maxUnits >= ".$abcPersons;
		$er = $wpdb->get_results($requestQuery, ARRAY_A);
		$getBookingResultReturn = '';
		$numberOfDays = floor((strtotime($normToValue) - strtotime($normFromValue))/(60*60*24));
		$currency = getAbcSetting('currency');
		$priceformat = getAbcSetting('priceformat');
		$availableRooms = '';
		foreach($er as $row) {
			if (getAbcAvailability($row["id"], sanitize_text_field($_POST["from"]), sanitize_text_field($_POST["to"])) > 0){
				$isSuccessful = true;
				$totalSum = abc_booking_getTotalPrice($row["id"], $normFromValue, $numberOfDays);
				$availableRooms .= '<div class="abc-result-calendar">';
				if ($row["infoPage"] == 0){
					$availableRooms .= '<span class="abc-result-roomname" title="'.esc_html($row["infoText"]).'">'.esc_html($row["name"]).', </span>';
				} else {
					$availableRooms.= '<span class="abc-result-roomname"><a href="'.get_permalink($row["infoPage"]).'" title="'.esc_html($row["infoText"]).'">'.esc_html($row["name"]).'</a>, </span>';
				}
				for( $i = 1; $i <= $row["maxUnits"]; $i++) {
					$availableRooms .= '<span class="fa fa-male"></span>';
				}
				$availableRooms .= ' 
						<br/><span>'.esc_html($row["infoText"]).'<br/>';
				$minimumStay = abc_booking_checkMinimumStay($row["id"], $normFromValue, $normToValue);		
				if($minimumStay > 0){
					$availableRooms .= '<b>'.sprintf( __('Your stay is too short. Minimum stay for those dates is %d nights.', 'advanced-booking-calendar'), $minimumStay ).'</b>';					
				}else {
					$availableRooms .= __('Total price', 'advanced-booking-calendar').': '.abc_booking_formatPrice($totalSum).', '.__('average', 'advanced-booking-calendar').': '.abc_booking_formatPrice(number_format(($totalSum/$numberOfDays), 2, $priceformat, '')).'
							<form action="'.get_permalink().'" method="post">
								<div data-persons="'.$abcPersons.'" data-from="'.$abcFromValue.'" data-to="'.$abcToValue.'" data-calendar="'.$row["id"].'" class="abc-bookingform-book abc-submit">
									<span class="fa fa-chevron-right"></span>
									<span>'.__('Select room', 'advanced-booking-calendar').'</span>
								</div>
							</form>';
				}
				$availableRooms .= '</span></div>';
			}
		}
		if ($isSuccessful){
			$getBookingResultReturn .= '<span class="abc-result-header">'.__('Available rooms for your stay', 'advanced-booking-calendar').':</span>'.$availableRooms;	 
			$getBookingResultReturn .= abc_booking_setPageview('bookingform/rooms-available'); // Google Analytics Tracking 
		}else {
			$getBookingResultReturn .='<span class="abc-result-header">'.__('No rooms available for your search request.', 'advanced-booking-calendar').'</span>';
			$getBookingResultReturn .= abc_booking_setPageview('bookingform/rooms-unavailable'); // Google Analytics Tracking
		}	
		//Saving inputs for tracking
		$wpdb->insert($wpdb->prefix.'advanced_booking_calendar_requests', 
			array('date_from' => $normFromValue,
			'date_to' => $normToValue,
			'persons' => $abcPersons,
			'successful' => $isSuccessful));
		
		//Returning output
		echo $getBookingResultReturn;
	} else {
		echo __('Something went wrong.', 'advanced-booking-calendar');
	}	
	die();
}
add_action('wp_ajax_abc_booking_getBookingResult', 'ajax_abc_booking_getBookingResult');
add_action( 'wp_ajax_nopriv_abc_booking_getBookingResult', 'ajax_abc_booking_getBookingResult');

function ajax_abc_booking_getBookingFormStep2 () {
	if (isset($_POST["from"])  && isset($_POST["to"]) && isset($_POST["persons"]) && isset($_POST["calendar"]) ) {//&& date("Y-m-d", strtotime($_POST["from"])) >= date("Y-m-d")
		global $wpdb;
		$dateformat = getAbcSetting("dateformat");
		$abcFromValue = sanitize_text_field($_POST["from"]); 
		$abcToValue = sanitize_text_field($_POST["to"]);
		// Normalizing entered dates
		$normFromValue = abc_booking_formatDateToDB($abcFromValue);
		$normToValue = abc_booking_formatDateToDB($abcToValue);
		$abcPersons = intval($_POST["persons"]);
		$calendarId = intval($_POST["calendar"]);
		$requestQuery = "SELECT name FROM ".$wpdb->prefix."advanced_booking_calendar_calendars WHERE id = ".$calendarId;
		$er = $wpdb->get_row($requestQuery);
		$calendarName = $er->name;
		$numberOfDays = floor((strtotime($normToValue) - strtotime($normFromValue))/(60*60*24));
		$totalPrice = abc_booking_getTotalPrice($calendarId, $normFromValue, $numberOfDays);
		$bookingFormOutput = '';
		$extrasOptional = getAbcExtrasList($numberOfDays, $abcPersons, 1);
		if(!isset($_POST["extrasList"]) && count($extrasOptional) > 0){
			$amountOfExtras = count($extrasOptional);
			$bookingFormOutput .= '
				<hr class="abc-form-hr" />
				<form class="abc-booking-form" action="'.get_permalink().'" method="post">
					<div class="abc-form-row">
								<div class="abc-column">
									<span class="abc-result-header">'._n('Optional extra', 'Optional extras', $amountOfExtras, 'advanced-booking-calendar').'</span>
								</div>
							</div>
							<div class="abc-form-row">';
			foreach($extrasOptional as $extra){
				$tempText = '<span  class="abc-extra-name abc-pointer">'.$extra["name"].', '.abc_booking_formatPrice($extra["priceValue"]).'</span>';
				if(strlen($extra["explanation"]) > 1){
					$tempText .= '<span class="abc-extra-cost abc-pointer"></br>('.$extra["priceText"].')</br>'.$extra["explanation"].'</span>';
				}	
				$bookingFormOutput .= '<div class="abc-column">
												<div class="abc-option">
													<div class="abc-optional-column-checkbox">
														<input type="checkbox" id="checkbox'.$extra["id"].'" name="abc-extras-checkbox" class="abc-extra-checkbox" value="'.$extra["id"].'">
													</div>
													<div class="abc-optional-column-text"><label for="checkbox'.$extra["id"].'">'.$tempText.'</label></div>
												</div>
											</div>';
			}					
			$bookingFormOutput .= '
					</div>
					<div class="abc-form-row">
						<div  id="abc-bookingform-extras-submit" data-persons="'.$abcPersons.'" data-from="'.$abcFromValue.'" data-to="'.$abcToValue.'" data-calendar="'.$calendarId.'" class="abc-submit">
							<span class="fa fa-chevron-right"></span>
							<span>'.__('Continue', 'advanced-booking-calendar').'</span>
						</div>	
					</div>
				</form>	';
		}else {
			$extrasComplete = getAbcExtrasList($numberOfDays, $abcPersons);
			$extrasSelected = explode(',', sanitize_text_field($_POST["extrasList"]));
			$extrasOptional = '';
			$extrasMandatory = '';
			$optionalCosts = 0;
			$mandatoryCosts = 0;
			$optionalCounter = 0;
			$extrasOutput = '';
			foreach($extrasComplete as $extra) {
				switch($extra["mandatory"]){
					case '1':
						$mandatoryCosts += $extra["priceValue"];
						$tempText = '<span class="abc-extra-name">'.$extra["name"].', '.abc_booking_formatPrice($extra["priceValue"]).'</span>';
						if(strlen($extra["explanation"]) > 1){
							$tempText .= '<span class="abc-extra-cost"></br>('.$extra["priceText"].')</br>'.$extra["explanation"].'</span>';
						}
						$extrasMandatory .= '<div class="abc-column">'.$tempText.'</div>';
						break;
					case '0':
						if(in_array($extra["id"], $extrasSelected)){
							$optionalCounter++;
							$optionalCosts += $extra["priceValue"];
							if(strlen($extrasOptional) > 1){
								$extrasOptional .= ', ';
							} 
							$extrasOptional .= $extra["name"].': '.abc_booking_formatPrice($extra["priceValue"]);
						}
						break;	
				}
			}
			if(strlen($extrasMandatory) > 1){
				$extrasOutput .= '<div class="abc-form-row">
								<div class="abc-column">
									<span class="abc-result-header">'.__('Additional costs', 'advanced-booking-calendar').'</span>
								</div>
							</div>
							<div class="abc-form-row">
					'.$extrasMandatory.'
					</div>
					<div class="abc-clearfix">
						<hr class="abc-form-hr" />
					</div>';
			}
			if($optionalCounter > 0){
				$extrasOptional = _n('Selected extra', 'Selected extras', $optionalCounter, 'advanced-booking-calendar').': '.$extrasOptional.'<br/>';
			}
			$priceOutput = '';
			if($optionalCosts >0){
				$priceOutput .= __('Costs for the extras', 'advanced-booking-calendar').': '.abc_booking_formatPrice($optionalCosts).'<br/>';
			}
			if($mandatoryCosts >0){
				$priceOutput .= __('Additional costs', 'advanced-booking-calendar').': '.abc_booking_formatPrice($mandatoryCosts).'<br/>';
			}
			if($mandatoryCosts >0 || $optionalCosts >0){
				$priceOutput .= __('Price for the room', 'advanced-booking-calendar').': '.abc_booking_formatPrice($totalPrice).'<br/>';
				$totalPrice = $totalPrice + $optionalCosts + $mandatoryCosts;
			}
			$bookingFormOutput = '
				<hr class="abc-form-hr" />
				<form class="abc-booking-form" action="'.get_permalink().'" method="post">
				<div class="abc-column">';
			$bookingFormSetting = getAbcSetting("bookingform");	
			$bookingFormColumn = ceil(($bookingFormSetting["inputs"]+1)/2);
			$rowCount = 0;
			if($bookingFormSetting["firstname"] > 0){
				$bookingFormOutput .= '<label for="first_name">'.__('First Name', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="first_name" name="first_name"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["lastname"] > 0){
				$bookingFormOutput .= '<label for="last_name">'.__('Last Name', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="last_name" name="last_name"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			$bookingFormOutput .= '<label for="email">'.__('Email Address', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="email" name="email"><br />';
			$rowCount++;
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["phone"] > 0){
				$bookingFormOutput .= '<label for="phone">'.__('Phone Number', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="phone" name="phone"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["street"] > 0){
				$bookingFormOutput .= '<label for="address">'.__('Street Address, House no.', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="address" name="address"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["zip"] > 0){
				$bookingFormOutput .= '<label for="zip">'.__('ZIP Code', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="zip" name="zip"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["city"] > 0){
				$bookingFormOutput .= '<label for="city">'.__('City', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="city" name="city"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["county"] > 0){
				$bookingFormOutput .= '<label for="county">'.__('State / County', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="county" name="county"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["country"] > 0){
				$bookingFormOutput .= '<label for="country">'.__('Country', 'advanced-booking-calendar').'</label><br />
						<input type="text" id="country" name="country"><br />';
				$rowCount++;
			}	
			if($rowCount == $bookingFormColumn){$bookingFormOutput .= '	</div><div class="abc-column">';}
			if($bookingFormSetting["country"] > 0){
				$bookingFormOutput .= '<label for="message">'.__('Message', 'advanced-booking-calendar').'</label><br />
						<textarea id="message" name="message"></textarea>';
				$rowCount++;
			}
			$bookingFormOutput .= '</div>
					<div class="abc-clearfix">
						<hr class="abc-form-hr" />
					</div>
					'.$extrasOutput.'
					<div class="abc-fullcolumn">
						<span>
							<b>'.__('Your stay', 'advanced-booking-calendar').':</b><br/>
							'.__('Checkin', 'advanced-booking-calendar').': '.$abcFromValue.'<br/>
							'.__('Checkout', 'advanced-booking-calendar').': '.$abcToValue.'<br/>
							'.__('Room type', 'advanced-booking-calendar').': '.$calendarName.'<br/>
							'.$extrasOptional.$priceOutput.'
							<b>'.__('Total Price', 'advanced-booking-calendar').': '.abc_booking_formatPrice($totalPrice).'</b><br/>
						</span>
					</div>
					<div class="abc-form-row">
						<button class="abc-submit" id="abc-bookingform-book-submit" data-persons="'.$abcPersons.'" data-from="'.$abcFromValue.'" 
							data-to="'.$abcToValue.'" data-calendar="'.$calendarId.'" data-extraslist="'.sanitize_text_field($_POST["extrasList"]).'">
							'.__('Book now', 'advanced-booking-calendar').'
						</button>	
					</div>
				</form>	
					';
			$bookingFormOutput .= abc_booking_setPageview('bookingform/bookingpage'); // Google Analytics Tracking
		}
		echo $bookingFormOutput;
	}
	die();
}
add_action('wp_ajax_abc_booking_getBookingFormStep2', 'ajax_abc_booking_getBookingFormStep2');
add_action( 'wp_ajax_nopriv_abc_booking_getBookingFormStep2', 'ajax_abc_booking_getBookingFormStep2');	

// Creating a booking in the DB and sending a mail to the customer and the owner
function ajax_abc_booking_getBookingFormBook () {
	$bookingFormOutput = '';
	$bookingForm = getAbcSetting("bookingform");
	if (isset($_POST["from"])  && isset($_POST["to"]) && abc_booking_formatDateToDB($_POST["from"]) >= date('Y-m-d') 
		&& isset($_POST["persons"]) && isset($_POST["calendar"]) && filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)
		&& getAbcAvailability(sanitize_text_field($_POST["calendar"]),sanitize_text_field($_POST["from"]), sanitize_text_field($_POST["to"])) 
		&& abc_booking_checkMinimumStay(intval($_POST["calendar"]), abc_booking_formatDateToDB($_POST["from"]), abc_booking_formatDateToDB($_POST["to"])) == 0
		&& (isset($_POST["firstname"]) || ($bookingForm["firstname"] < 2))  && (isset($_POST["lastname"]) || ($bookingForm["lastname"] < 2))
		&& (isset($_POST["phone"]) || ($bookingForm["phone"] < 2)) && (isset($_POST["address"]) || ($bookingForm["street"] < 2))
		&& (isset($_POST["zip"]) || ($bookingForm["zip"] < 2)) && (isset($_POST["county"]) || ($bookingForm["county"] < 2))
		&& (isset($_POST["city"]) || ($bookingForm["city"] < 2)) && (isset($_POST["country"]) || ($bookingForm["country"] < 2))
		&& (isset($_POST["message"]) || ($bookingForm["message"] < 2))
	){
		/*)*/
		// Sanitizing inputs
		$bookingData = array();
		$bookingData["start"] = abc_booking_formatDateToDB($_POST["from"]);
		$bookingData["end"] = abc_booking_formatDateToDB($_POST["to"]);
		$bookingData["persons"] = intval($_POST["persons"]);
		$bookingData["calendar_id"] = intval($_POST["calendar"]);
		if(isset($_POST["extraslist"])){
			$bookingData["extras"] = sanitize_text_field($_POST["extraslist"]);
		}else{
			$bookingData["extras"] = '';
		}	
		if(isset($_POST["firstname"])){
			$bookingData["first_name"]= sanitize_text_field($_POST["firstname"]);
		}else{
			$bookingData["first_name"] = '';
		}
		if(isset($_POST["lastname"])){
			$bookingData["last_name"] = sanitize_text_field($_POST["lastname"]);
		}else{
			$bookingData["last_name"] = '';
		}
		if(isset($_POST["email"])){
			$bookingData["email"] = sanitize_email($_POST["email"]);
		}else{
			$bookingData["email"] = '';
		}
		if(isset($_POST["phone"])){			
			$bookingData["phone"] = sanitize_text_field($_POST["phone"]);
		}else{
			$bookingData["phone"] = '';
		}
		if(isset($_POST["address"])){			
			$bookingData["address"] = sanitize_text_field($_POST["address"]);
		}else{
			$bookingData["address"] = '';
		}
		if(isset($_POST["zip"])){			
			$bookingData["zip"] = sanitize_text_field($_POST["zip"]);
		}else{
			$bookingData["zip"] = '';
		}
		if(isset($_POST["city"])){			
			$bookingData["city"] = sanitize_text_field($_POST["city"]);
		}else{
			$bookingData["city"] = '';
		}
		if(isset($_POST["country"])){			
			$bookingData["country"] = sanitize_text_field($_POST["country"]);
		}else{
			$bookingData["country"] = '';
		}
		if(isset($_POST["message"])){			
			$bookingData["message"] = sanitize_text_field($_POST["message"]);
		}else{
			$bookingData["message"] = '';
		}
		if(isset($_POST["county"])){			
			$bookingData["county"] = sanitize_text_field($_POST["county"]);
		}else{
			$bookingData["county"] = '';
		}		
		$bookingData["state"] = "open";
		
		// Saving booking request in DB and getting booking ID
		$bookingData["booking_id"] = setAbcBooking($bookingData);
		
		// Sending emails 
		sendAbcGuestMail($bookingData);
		sendAbcAdminMail($bookingData);
		
		// Returning Thank-You-Page
		$bookingFormOutput .= '
				<div class="abc-form-row">
					<span class="abc-result-header">'.__('Thank you for your booking request!', 'advanced-booking-calendar').'</span></br>
					<span>'.__('We have sent you an email including a summary of your booking!', 'advanced-booking-calendar').'</span>
				</div>';
		$bookingFormOutput .= abc_booking_setPageview('bookingform/booking-successful'); // Google Analytics Tracking	
		
	} else {
		$bookingFormOutput .= '<div class="abc-form-row">'.__('Something went wrong, your booking could not be completed. Please try again.', 'advanced-booking-calendar').'</div>';
		$bookingFormOutput .= abc_booking_setPageview('bookingform/booking-error'); // Google Analytics Tracking	
	}
	echo $bookingFormOutput;
	die();
}
add_action('wp_ajax_abc_booking_getBookingFormBook', 'ajax_abc_booking_getBookingFormBook');
add_action( 'wp_ajax_nopriv_abc_booking_getBookingFormBook', 'ajax_abc_booking_getBookingFormBook');
?>