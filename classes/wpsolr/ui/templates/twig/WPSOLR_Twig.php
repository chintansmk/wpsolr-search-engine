<?php

namespace wpsolr\ui\templates\twig;

use wpsolr\utilities\WPSOLR_Global;

/**
 * Manage Twig templates
 */
class WPSOLR_Twig {

	protected $twig_environment;

	/**
	 * Singleton constructor called from WPSOLR_Global
	 * @return WPSOLR_Twig
	 */
	public static function global_object() {

		$result = new WPSOLR_Twig();

		/* Create a file loader */
		$loader_file = new \Twig_Loader_Filesystem( $result->get_default_twig_template_path() );

		/* Create a string loader for templates stored in database (Widget templates defined with WP Customizer) */
		$loader_string = new \Twig_Loader_String();

		/* Hybrid loader to load a file template, or a string template if file does not exist */
		$loader_chain = new \Twig_Loader_Chain( array( $loader_file, $loader_string ) );

		$is_debug = WPSOLR_Global::getOption()->get_is_debug_environment();;

		$twig = new \Twig_Environment( $loader_chain, array(
			'cache'       => $result->get_default_twig_template_cache_path(),
			// Reload only on dev env
			'auto_reload' => $is_debug,
			'debug'       => $is_debug,
		) );

		// Register WPSOLR Twig extension
		$twig->addExtension( new WPSOLR_Twig_Extension() );

		if ( $is_debug ) {
			// Add debugger, for dump function to be here: http://twig.sensiolabs.org/doc/functions/dump.html
			$twig->addExtension( new \Twig_Extension_Debug() );
		}


		return $result->setTwigEnvironment( $twig );
	}

	/**
	 * Default facets template file name
	 *
	 * @return string
	 */
	public function get_default_twig_template_facets_name() {
		return 'wpsolr/facets_html.twig';
	}

	/**
	 * Default sort template file name
	 *
	 * @return string
	 */
	public function get_default_twig_template_sort_name() {
		return 'wpsolr/dropdownlist.html.twig';
	}

	/**
	 * @param \Twig_Environment $twig_environment
	 *
	 * @return WPSOLR_Twig
	 */
	public function setTwigEnvironment( \Twig_Environment $twig_environment ) {
		$this->twig_environment = $twig_environment;

		return $this;
	}

	/**
	 * @return Twig_Environment
	 */
	public function getTwigEnvironment() {
		return $this->twig_environment;
	}

	/**
	 * Path containing all Twig templates
	 *
	 * @return string
	 */
	public function get_default_twig_template_path() {
		return plugin_dir_path( __FILE__ );
	}


	/**
	 * Path containing all Twig templates caches
	 *
	 * @return string
	 */
	public function get_default_twig_template_cache_path() {
		return $this->get_default_twig_template_path() . 'cache';
	}

	/**
	 * Default facets template file content
	 *
	 * @return string
	 */
	public function get_twig_template_file_content( $template_file_name ) {

		return file_get_contents( $this->get_default_twig_template_path() . $template_file_name );
	}

}