<?php
function atollmatrix_payment_metadata() {

	$atollmatrix_imagepath =  get_template_directory_uri() . '/framework/options/images/metaboxes/';
	$atollmatrix_imagepath_alt =  get_template_directory_uri() . '/framework/options/images/';

	$atollmatrix_imagepath =  get_template_directory_uri() . '/framework/options/images/';

	$atollmatrix_payment_box = array(
		'id' => 'paymentmeta-box',
		'title' => 'Payment Metabox',
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
				'name' => esc_html__('Room','atollmatrix'),
				'id' => 'atollmatrix_payment_booking_id',
				'class' => 'room_choice',
				'type' => 'payments',
				'target' => 'booking_numbers',
				'desc' => '',
				'options' => ''
			),
		)
	);
	return $atollmatrix_payment_box;
}
/*
* Meta options for Room post type
*/
function atollmatrix_paymentitem_metaoptions(){
	$atollmatrix_payment_box = atollmatrix_payment_metadata();
	atollmatrix_generate_metaboxes($atollmatrix_payment_box,get_the_id());
}
?>