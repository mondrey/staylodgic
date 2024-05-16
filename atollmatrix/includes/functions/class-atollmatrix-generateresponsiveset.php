<?php
class atollmatrix_GenerateResponsiveSet {

	public function __construct( $pageid, $meta_field, $csstarget, $parameter, $unit ) {
		$this->id         = $pageid;
		$this->meta_field = $meta_field;
		$this->csstarget  = $csstarget;
		$this->parameter  = $parameter;
		$this->unit       = $unit;
		$this->input      = $this->get_input( $this->id, $this->meta_field );
		$this->metainputs = $this->set_inputs( $this->input, ',' );
		$this->fields     = $this->set_css( $this->metainputs, $this->csstarget, $this->parameter, $this->unit );
		$this->media      = $this->collective_responsive( $this->fields );
		$this->mediaset   = $this->media['mediaquery'];
	}

	/**
	 * Get the required inputs
	 * @param  [type] $pageid     [page ID]
	 * @param  [type] $meta_field [meta data name]
	 * @return [type]             [stored input]
	 */
	public function get_input( $pageid, $meta_field ) {
		$got_input = get_post_meta( $pageid, $meta_field, true );
		return $got_input;
	}

	/**
	 * Seperates inputs to array
	 * @param  [type] $input     [fields to seperate]
	 * @param  string $seperator [seperator]
	 * @return [type]            [returns an array]
	 */
	public function set_inputs( $input, $seperator = ',' ) {
		$array = explode( $seperator, $input );
		return $array;
	}

	/**
	 * Array to CSS
	 * @param  [type] $values [array of numerical values]
	 * @param  [type] $target  [CSS target]
	 * @param  [type] $parameter  [CSS parameter]
	 * @param  [type] $unit  [Unit for CSS parameter]
	 * @return [type]        [fields for responsive devices]
	 */
	public function set_css( $values, $target, $parameter, $unit ) {

		// nothing here
		if ( ! isset( $values ) || ! is_array( $values ) ) {
			return false;
		}
		if ( isset( $values[0] ) && '' === $values[0] ) {
			return false;
		}

		$fields = array();

		// Desktop
		if ( isset( $values[0] ) ) {
			$fields['desktop'] = $target . '{' . $parameter . ':' . $values[0] . $unit . '; }';
		}
		// Tablet
		if ( isset( $values[1] ) ) {
			$fields['tablet'] = $target . '{' . $parameter . ':' . $values[1] . $unit . '; }';
		} else {
			$fields['tablet'] = $fields['desktop'];
		}
		// Mobile
		if ( isset( $values[2] ) ) {
			$fields['mobile'] = $target . '{' . $parameter . ':' . $values[2] . $unit . '; }';
		} else {
			$fields['mobile'] = $fields['tablet'];
		}

		return $fields;

	}

	public function collective_responsive( $fields ) {

		$tablet_media_queries     = '@media only screen and (max-width: 1100px),only screen and (min-width: 768px) and (max-width: 959px)';
		$mobile_media_queries     = '@media only screen and (max-width: 767px),only screen and (min-width: 480px) and (max-width: 767px)';
		$responsive               = array();
		if ( isset( $fields['desktop'] ) ) {
			$responsive['desktop']    = $fields['desktop'];
		}
		if ( isset( $fields['tablet'] ) ) {
			$responsive['tablet']     = $tablet_media_queries . '{' . $fields['tablet'] . '}';
		}
		if ( isset( $fields['mobile'] ) ) {
			$responsive['mobile']     = $mobile_media_queries . '{' . $fields['mobile'] . '}';
		}
		if ( ! empty( $responsive['desktop'] ) ) {
			$responsive['mediaquery'] = $responsive['desktop'] . $responsive['tablet'] . $responsive['mobile'];
		} else {
			$responsive['mediaquery'] = '';
		}

		return $responsive;
	}

}
