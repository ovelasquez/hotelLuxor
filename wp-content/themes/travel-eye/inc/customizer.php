<?php
/**
 * Theme Customizer.
 *
 * @package Travel_Eye
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function travel_eye_customize_register( $wp_customize ) {

	// Load custom controls.
	require get_template_directory() . '/inc/customizer/control.php';

	// Load customize helpers.
	require get_template_directory() . '/inc/helper/options.php';

	// Load customize sanitize.
	require get_template_directory() . '/inc/customizer/sanitize.php';

	// Load customize callback.
	require get_template_directory() . '/inc/customizer/callback.php';

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	// Load customize option.
	require get_template_directory() . '/inc/customizer/option.php';

	// Load reset option.
	require get_template_directory() . '/inc/customizer/reset.php';

	// Modify default customizer options.
	$wp_customize->get_control( 'background_color' )->section = 'section_color_main_background';

}
add_action( 'customize_register', 'travel_eye_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since 1.0.0
 */
function travel_eye_customize_preview_js() {

	wp_enqueue_script( 'travel_eye_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );

}
add_action( 'customize_preview_init', 'travel_eye_customize_preview_js' );

/**
 * Load styles for Customizer.
 *
 * @since 1.0.0
 */
function travel_eye_load_customizer_styles() {

	global $pagenow;

	if ( 'customize.php' === $pagenow ) {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'travel-eye-customizer-style', get_template_directory_uri() . '/css/customizer' . $min . '.css', false, '1.4.0' );
	}

}

add_action( 'admin_enqueue_scripts', 'travel_eye_load_customizer_styles' );

/**
 * Add Upgrade To Pro button.
 *
 * @since 1.4.0
 */
function travel_eye_custom_customize_enqueue_scripts() {

	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_register_script( 'travel-eye-customizer-button', get_template_directory_uri() . '/js/customizer-button' . $min . '.js', array( 'customize-controls' ), '1.4.0', true );
	$data = array(
		'updrade_button_text' => __( 'Buy Travel Eye Pro', 'travel-eye' ),
		'updrade_button_link' => esc_url( 'http://themepalace.com/downloads/travel-eye-pro/' ),
	);
	wp_localize_script( 'travel-eye-customizer-button', 'Travel_Eye_Customizer_Object', $data );
	wp_enqueue_script( 'travel-eye-customizer-button' );

}

add_action( 'customize_controls_enqueue_scripts', 'travel_eye_custom_customize_enqueue_scripts' );
