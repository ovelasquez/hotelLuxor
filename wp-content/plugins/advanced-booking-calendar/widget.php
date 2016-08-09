<?php

class abcAvailabilityFormWidget extends WP_Widget {

    // constructor
    function abcAvailabilityFormWidget() {
        $widget_ops = array(
            'classname' => 'abcAvailabilityFormWidget',
            'description' => __('Advanced Booking Calendar Widget.', 'advanced-booking-calendar'),
        );
        parent::__construct(false, __('Availability Form Widget', 'advanced-booking-calendar'), $widget_ops);
    }

    // widget form creation
    function form($instance) {

        // Check title
        if ($instance) {
            $title = esc_attr($instance['title']);
        } else {
            // Initial title
            $title = __('Reservaciones', 'advanced-booking-calendar');
        }
        echo "<p> <label for=\"" . $this->get_field_id('title') . "\">" . __('Title:', 'advanced-booking-calendar') . "</label>
				<input class=\"widefat\" id=\"" . $this->get_field_id('title') . "\" name=\"" . $this->get_field_name('title') . "\" type=\"text\" value=\"" . $title . "\" />
			</p>";
        // Frontend output
        if (getAbcSetting("bookingpage") > 0) {
            echo "<p>" . __('This widgets loads a small booking form. After a user selected the dates and clicked on "Check availabilites", the booking form is loaded.', 'advanced-booking-calendar') . "</p>";
        } else {
            echo "<p style=\"color:red\">" . __('There is no booking page configured. Check the settings and select a page with the booking form.', 'advanced-booking-calendar') . "</p>";
        }
    }

    // Update function for changes
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    // Display widget
    function widget($args, $instance) {
        global $abcUrl;
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        wp_enqueue_style('styles-css', $abcUrl . 'frontend/css/styles.css');
        wp_enqueue_style('font-awesome', $abcUrl . 'frontend/css/font-awesome.min.css');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('abc-widget', $abcUrl . 'frontend/js/abc-widget.js', array('jquery'));
        $dateformat = abc_booking_dateFormatToJS(getAbcSetting("dateformat"));
        wp_localize_script('abc-widget', 'abc_functions_vars', array('dateformat' => $dateformat, 'firstday' => getAbcSetting("firstdayofweek")));
        wp_enqueue_style('abc-datepicker', $abcUrl . '/frontend/css/jquery-ui.min.css');
        $datepickerLang = array('af', 'ar-DZ', 'ar', 'az', 'be', 'bg', 'bs', 'ca', 'cs', 'cy-GB', 'da', 'de', 'el', 'en-AU', 'en-GB', 'en-NZ',
            'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fo', 'fr-CA', 'fr-CH', 'fr', 'gl', 'he', 'hi', 'hr', 'hu', 'hy', 'id', 'is',
            'it-CH', 'it', 'ja', 'ka', 'kk', 'km', 'ko', 'ky', 'lb', 'lt', 'lv', 'mk', 'ml', 'ms', 'nb', 'nl-BE', 'nl', 'nn',
            'no', 'pl', 'pt-BR', 'pt', 'rm', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr-SR', 'sr', 'sv', 'ta', 'th', 'tj', 'tr', 'uk',
            'vi', 'zh-CN', 'zh-HK', 'zh-TW');
        if (substr(get_locale(), 0, 2) != 'en' && in_array(get_locale(), $datepickerLang)) {
            wp_enqueue_script('jquery-datepicker-lang', $abcUrl . 'frontend/js/datepicker_lang/datepicker-' . get_locale() . '.js', array('jquery'));
        } elseif (substr(get_locale(), 0, 2) != 'en' && in_array(substr(get_locale(), 0, 2), $datepickerLang)) {
            wp_enqueue_script('jquery-datepicker-lang', $abcUrl . 'frontend/js/datepicker_lang/datepicker-' . substr(get_locale(), 0, 2) . '.js', array('jquery'));
        }
        $abcPersonValue = 1;
        if (isset($_POST['abc-persons'])) { // Checking for cookies
            $abcPersonValue = intval($_POST['abc-persons']);
        } elseif (isset($_COOKIE['abc-persons'])) { // Checking for cookies
            $abcPersonValue = intval($_COOKIE['abc-persons']);
        }
        $optionPersons = '';
        for ($i = 1; $i <= getAbcSetting('personcount'); $i++) {
            $optionPersons .= '<option value="' . $i . '"';
            if ($i == $abcPersonValue) {
                $optionPersons .= ' selected';
            }
            $optionPersons .= '>' . $i . '</option>';
        }
        $abcFromValue = '';
        $abcToValue = '';
        if (isset($_POST['abc-from']) && isset($_POST['abc-to']) && abc_booking_validateDate($_POST['abc-from'], getAbcSetting("dateformat")) && abc_booking_formatDateToDB($_POST['abc-from']) >= date('Y-m-d')
        ) { // Checking for POST variables (via single calendar)
            $abcFromValue = sanitize_text_field($_POST['abc-from']);
            $abcToValue = sanitize_text_field($_POST['abc-to']);
        } elseif (isset($_COOKIE['abc-from']) && isset($_COOKIE['abc-to']) && abc_booking_validateDate($_COOKIE['abc-from'], getAbcSetting("dateformat")) && abc_booking_formatDateToDB($_COOKIE['abc-from']) >= date('Y-m-d')) { // Checking for cookies and checking if "from date" is in the past
            $abcFromValue = sanitize_text_field($_COOKIE['abc-from']);
            $abcToValue = sanitize_text_field($_COOKIE['abc-to']);
        }
        echo $before_widget;
        echo '<div class="widget-text">';
        echo '<div class="widget-title">';
        // Check if title is set
        if ($title) {
            echo  $title;
        }
        echo '</div>
			<div class="widget-textarea">';
        if (getAbcSetting("bookingpage") > 0) {
            echo '<div id="abc-form-wrapper">
				<div id="abc-form-content">
					<form class="abc-form"  method="post" action="' . get_permalink(getAbcSetting("bookingpage")) . '">
					<div class="abc-column">
						<label for="abc-widget-from">' . __('Checkin', 'advanced-booking-calendar') . '</label>
						<div class="abc-input-fa">
							<span class="fa fa-calendar"></span>
							<input id="abc-widget-from" name="abc-from" value="' . $abcFromValue . '">
						</div>
						<label for="abc-widget-to">' . __('Checkout', 'advanced-booking-calendar') . '</label>
						<div class="abc-input-fa">
							<span class="fa fa-calendar"></span>
							<input id="abc-widget-to" name="abc-to" value="' . $abcToValue . '">
						</div>	
						<label for="abc-persons">' . __('Persons', 'advanced-booking-calendar') . '</label>
						<div class="abc-input-fa">
							<span class="fa fa-female abc-guest1"></span>
							<span class="fa fa-male abc-guest2"></span>
							<select id="abc-persons" name="abc-persons">
								' . $optionPersons . '
							</select>
						</div>
						<input id="abc-trigger" type="hidden" name="abc-trigger" value="1">	
						</div>
						<div class="abc-form-row">
							<button type="submit" class="abc-submit" id="abc-widget-check-availabilities">
								<span class="abc-submit-text">' . __('Disponibilidades', 'advanced-booking-calendar') . '</button>
						</div>	
					</form>	
					
					<div id="abc-bookingresults"></div>
				</div>
			</div>';
            echo $before_title . " ". $after_title;
        } else {
            echo '<p>' . __('There is no booking page configured. Check the settings of the Advanced Booking Calendar.', 'advanced-booking-calendar') . '</p>';
        }
        echo '</div></div>';
        echo $after_widget;
    }

}

// Register widget
add_action('widgets_init', create_function('', 'return register_widget("abcAvailabilityFormWidget");'));
?>
