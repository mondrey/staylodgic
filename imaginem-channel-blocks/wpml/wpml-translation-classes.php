<?php
/**
 * Pricing Services Pricing
 */
if ( class_exists('SitePress') ) {
	class WPML_Themecore_Testimonials extends WPML_Elementor_Module_With_Items  {

		/**
		 * @return string
		 */
		public function get_items_field() {
			return 'testimony';
		}

		/**
		 * @return array
		 */
		public function get_fields() {
			return array( 
				'title', 
				'thename',
				'company',
				'quote'
			);
		}

		/**
		 * @param string $field
		 *
		 * @return string
		 */
		protected function get_title( $field ) {
			switch( $field ) {
				case 'title':
					return esc_html__( 'Title', 'theme-core' );

				case 'thename':
					return esc_html__( 'Name', 'theme-core' );

				case 'company':
					return esc_html__( 'Company', 'theme-core' );

				case 'quote':
					return esc_html__( 'Quote', 'theme-core' );

				default:
					return '';
			}
		}

		/**
		 * @param string $field
		 *
		 * @return string
		 */
		protected function get_editor_type( $field ) {
			switch( $field ) {                
				case 'title':
					return 'LINE';

				case 'thename':
					return 'LINE';
					
				case 'company':
					return 'LINE';

				case 'quote':
					return 'AREA';

				default:
					return '';
			}
		}

	}
}

/**
 * Pricing Services Pricing
 */
if ( class_exists('SitePress') ) {
	class WPML_Themecore_Pricing_Services extends WPML_Elementor_Module_With_Items  {

		/**
		 * @return string
		 */
		public function get_items_field() {
			return 'row';
		}

		/**
		 * @return array
		 */
		public function get_fields() {
			return array( 
				'rowtext'
			);
		}

		/**
		 * @param string $field
		 *
		 * @return string
		 */
		protected function get_title( $field ) {
			switch( $field ) {
				case 'rowtext':
					return esc_html__( 'Title', 'theme-core' );

				default:
					return '';
			}
		}

		/**
		 * @param string $field
		 *
		 * @return string
		 */
		protected function get_editor_type( $field ) {
			switch( $field ) {                
				case 'rowtext':
					return 'LINE';

				default:
					return '';
			}
		}

	}
}
