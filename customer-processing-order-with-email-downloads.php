<?php

/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($order->get_billing_first_name())); ?></p>
<?php /* translators: %s: Order number */ ?>
<p><?php printf(esc_html__('Just to let you know &mdash; we\'ve received your order #%s, and it is now being processed:', 'woocommerce'), esc_html($order->get_order_number())); ?></p>

<?php


$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted() && !$sent_to_admin && !is_a($email, 'WC_Email_Customer_Refunded_Order');
var_dump($show_downloads);
$downloads = $order->get_downloadable_items();

$columns   = apply_filters(
	'woocommerce_email_downloads_columns',
	array(
		'download-product' => __('Product', 'woocommerce'),
		'download-expires' => __('Expires', 'woocommerce'),
		'download-file'    => __('Download', 'woocommerce'),
	)
);

?>

<h2 class="woocommerce-order-downloads__title"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h2>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px;" border="1">
	<thead>
		<tr>
			<?php foreach ( $columns as $column_id => $column_name ) : ?>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo esc_html( $column_name ); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>

	<?php foreach ( $downloads as $download ) : ?>
		<tr>
			<?php foreach ( $columns as $column_id => $column_name ) : ?>
				<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
					<?php
					if ( has_action( 'woocommerce_email_downloads_column_' . $column_id ) ) {
						do_action( 'woocommerce_email_downloads_column_' . $column_id, $download, $plain_text );
					} else {
						switch ( $column_id ) {
							case 'download-product':
								?>
								<a href="<?php echo esc_url( get_permalink( $download['product_id'] ) ); ?>"><?php echo wp_kses_post( $download['product_name'] ); ?></a>
								<?php
								break;
							case 'download-file':
								?>
								<a href="<?php echo esc_url( $download['download_url'] ); ?>" class="woocommerce-MyAccount-downloads-file button alt"><?php echo esc_html( $download['download_name'] ); ?></a>
								<?php
								break;
							case 'download-expires':
								if ( ! empty( $download['access_expires'] ) ) {
									?>
									<time datetime="<?php echo esc_attr( date( 'Y-m-d', strtotime( $download['access_expires'] ) ) ); ?>" title="<?php echo esc_attr( strtotime( $download['access_expires'] ) ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) ); ?></time>
									<?php
								} else {
									esc_html_e( 'Never', 'woocommerce' );
								}
								break;
						}
					}
					?>
				</td>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
</table>



<?php


do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
	echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
