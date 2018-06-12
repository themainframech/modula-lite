<?php

/**
 *  Helper Class for Modula Gallery
 */
class Modula_Helper {
	
	/*
	*
	* Generate html attributes based on array
	*
	* @param array atributes
	*
	* @return string
	*
	*/
	public static function generate_attributes( $attributes ) {
		$return = '';
		foreach ( $attributes as $name => $value ) {
			$return .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}

		return $return;

	}
}