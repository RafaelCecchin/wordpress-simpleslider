<?php

/**
 * Plugin Name:       Wordpress Simple Slider
 * Description:       Slider simples para Wordpress utilizando a biblioteca Slick.
 * Version:           1.0
 * Requires at least: 5.4
 * Requires PHP:      7.2
 * Author:            Rafael Cecchin
 * Author URI:        www.rafaelcecchin.com.br
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 **/

define( "WORDPRESS_SIMPLESLIDER_FILE",  __FILE__ );
define( "WORDPRESS_SIMPLESLIDER_POST_TYPE",  'wp-simpleslider' );
define( "WORDPRESS_SIMPLESLIDER_PLUGIN_DIR",  plugin_dir_path( WORDPRESS_SIMPLESLIDER_FILE ) );
define( "WORDPRESS_SIMPLESLIDER_BASENAME",  plugin_basename( WORDPRESS_SIMPLESLIDER_FILE ) );
define( "WORDPRESS_SIMPLESLIDER_URL", plugin_dir_url( WORDPRESS_SIMPLESLIDER_FILE ) );

include 'wordpress-simpleslider.class.php';

$objSimpleSlider = new WordpressSimpleslider();