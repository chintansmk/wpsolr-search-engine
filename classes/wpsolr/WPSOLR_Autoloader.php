<?php
/**
 * Custom namespace autoloader
 */

namespace wpsolr;

// autoloader declaration for phpunit bootstrap script in phpunit.xml
spl_autoload_register( array( WPSOLR_Autoloader::CLASS, 'Load' ) );

class WPSOLR_Autoloader {

	// autoload classes based on namespaces beginning with 'wpsolr'
	static function Load( $className ) {

		if ( substr( $className, 0, strlen( 'wpsolr' ) ) === 'wpsolr' ) {
			$filename = __DIR__ . '/../' . str_replace( '\\', '/', $className ) . '.php';

			if ( file_exists( $filename ) ) {

				require_once( $filename );

				if ( class_exists( $className ) ) {
					return true;
				}
			}
		}

		return false;
	}

}