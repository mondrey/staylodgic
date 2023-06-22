<?php
namespace AtollMatrix;
class Customers {

	public static function generateCustomerHtmlList($array) {
		$html = "<ul class='existing-customer'>";
		foreach ($array as $key => $value) {
			if ( 'Country' == $key ) {
				$value = \AtollMatrix\Common::countryCodeToEmoji($value) . ' ' . themecore_country_list('display',$value);
			}
			$html .= "<li><strong>{$key}:</strong> {$value}</li>";
		}
		$html .= "</ul>";
		return $html;
	}

}
