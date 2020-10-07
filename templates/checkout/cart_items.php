<?php
/**
 * Template for displaying cart items in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/checkout/cart_items.php
 *
 * @version     2.0
 * @package     Template
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<?php $cart_contents = FP()->cart->cart_contents; ?>

<tbody>
<?php foreach ( $cart_contents as $cart_item_key => $cart_content ) { ?>
    <tr>
        <td class="donate_cart_item_name">
            <!--thumbnail-->
            <div class="donate_cart_item_thumbnail">
				<?php echo get_the_post_thumbnail( $cart_content->campaign_id, 'thumbnail', array(
					'width'  => 100,
					'height' => 100
				) ); ?>
            </div>
            <!--title-->
            <div class="donate_cart_item_name_title">
                <a href="<?php echo esc_url( get_permalink( $cart_content->campaign_id ) ) ?>"><?php printf( '%s', $cart_content->product_data->post_title ) ?></a>
            </div>
        </td>
        <td class="donate_cart_item_amount"><?php printf( '%s', donate_price( $cart_content->amount, $cart_content->currency ) ) ?></td>
    </tr>
<?php } ?>
</tbody>