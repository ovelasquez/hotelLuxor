<?php
/**
 * Template file.
 *
 * @package Travel_Eye
 */

?>
	<?php if ( ! empty( $instance['title'] ) ) : ?>
		<?php echo $args['before_title']; ?> <?php echo esc_html( $instance['title'] ); ?> <?php echo $args['after_title']; ?>
	<?php endif; ?>

	<?php if ( ! empty( $instance['subtitle'] ) ) : ?>
		<h4 class="widget-sub-title"><?php echo esc_html( $instance['subtitle'] ); ?></h4>
	<?php endif; ?>
	<?php
		$nav_menu_locations = get_nav_menu_locations();
		$menu_id = 0;
		if ( isset( $nav_menu_locations['social'] ) && absint( $nav_menu_locations['social'] ) > 0 ) {
			$menu_id = absint( $nav_menu_locations['social'] );
		}
		if ( $menu_id > 0 ) {

			$menu_items = wp_get_nav_menu_items( $menu_id );

			if ( ! empty( $menu_items ) ) {
				echo '<ul class="size-medium">';
				foreach ( $menu_items as $m_key => $m ) {
					echo '<li>';
					echo '<a href="' . esc_url( $m->url ) . '" target="_blank">';
					echo '<span class="title screen-reader-text">' . esc_attr( $m->title ) . '</span>';
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		}
	?>
