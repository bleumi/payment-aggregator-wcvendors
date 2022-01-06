<?php

/*
 * Plugin Name:  Bleumi Payments for WC Vendors Marketplace
 * Description:  Support split payments in Bleumi Payments for WC Vendors Marketplace
 * Version:      1.0.0
 * Author:       Bleumi Inc
 * Author URI:   https://bleumi.com/
 * License:      Copyright 2020 Bleumi, MIT License
*/

add_filter('woocommerce_available_payment_gateways', 'wc_wcv_bleumi_disable_unknown_vendor');
function wc_wcv_bleumi_disable_unknown_vendor($gws) {
	if (is_admin()) {
		return $gws;
	}

	foreach(WC()->cart->get_cart() as $item) {
		$product_id = $item['product_id'];
		$vendor_id = WCV_Vendors::get_vendor_from_product($product_id);
		$bleumi_id = get_user_meta($vendor_id, 'wc_wcv_bleumi_vendor_id');
		if(empty($bleumi_id)) {
			unset($gws['bleumi']);
		}
	}

	return $gws;
}

add_filter('edit_user_profile_update', 'wc_wcv_bleumi_save_vendor_id');
function wc_wcv_bleumi_save_vendor_id($user_id) {
	if ( isset( $_POST['wc_wcv_bleumi_vendor_id'] ) ) {
		update_user_meta( $user_id, 'wc_wcv_bleumi_vendor_id', $_POST['wc_wcv_bleumi_vendor_id'] );
	}
}

add_filter('wcvendors_admin_before_bank_details', 'wc_wcv_bleumi_before_bank_details');
function wc_wcv_bleumi_before_bank_details($user) { ?>
	<tr>
		<th>
			<label for="wc_wcv_bleumi_vendor_id">Bleumi Vendor ID
				<span class="description"></span>
			</label>
		</th>
		<td>
			<input type="text" name="wc_wcv_bleumi_vendor_id" id="wc_wcv_bleumi_vendor_id" value="<?php echo get_user_meta( $user->ID, 'wc_wcv_bleumi_vendor_id', true ); ?>" class="regular-text">
		</td>
	</tr>
<?php }
