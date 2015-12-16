<article id="tp_donate-<?php the_ID(); ?>" <?php post_class('tp_single_event'); ?>>

	<?php
		/**
		 * donate_before_loop_room_summary hook
		 *
		 * @hooked donate_show_room_sale_flash - 10
		 * @hooked donate_show_room_images - 20
		 */
		do_action( 'donate_before_single_event' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * donate_single_event_title hook
			 */
			do_action( 'donate_single_event_title' );

			/**
			 * donate_single_event_thumbnail hook
			 */
			do_action( 'donate_single_event_thumbnail' );

			/**
			 * donate_loop_event_countdown
			 */
			do_action( 'donate_loop_event_countdown' );

			/**
			 * donate_single_event_content hook
			 */
			do_action( 'donate_single_event_content' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * hotel_booking_after_loop_room hook
		 *
		 * @hooked hotel_booking_output_room_data_tabs - 10
		 * @hooked hotel_booking_upsell_display - 15
		 * @hooked hotel_booking_output_related_products - 20
		 */
		do_action( 'donate_after_single_event' );
	?>

</article><!-- #product-<?php the_ID(); ?> -->