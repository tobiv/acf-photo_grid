<?php

/*
Plugin Name: Advanced Custom Fields: Photo Grid
Plugin URI: PLUGIN_URL
Description: Provides a drag-and-drop interface to build a simple 3:2-ratio-image grid.
Version: 1.0.0
Author: Tobias Vogler
Author URI: https://github.com/tobiv
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_plugin_photo_grid') ) :

class acf_plugin_photo_grid {

	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		// vars
		$this->settings = array(
			'version'	=> '1.0.0',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);


		// set text domain
		// https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
		load_plugin_textdomain( 'acf-photo_grid', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );


		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field_types')); // v5
		add_action('acf/register_fields', 		array($this, 'include_field_types')); // v4

	}


	/*
	*  include_field_types
	*
	*  This function will include the field type class
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	$version (int) major ACF version. Defaults to false
	*  @return	n/a
	*/

	function include_field_types( $version = false ) {

		// support empty $version
		if( !$version ) $version = 4;


		// include
		include_once('fields/acf-photo_grid-v' . $version . '.php');

	}

}


// initialize
new acf_plugin_photo_grid();


// class_exists check
endif;

?>
