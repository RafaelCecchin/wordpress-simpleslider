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

define("WORDPRESS_SIMPLESLIDER_BASENAME",  plugin_basename( __FILE__ ));
define("WORDPRESS_SIMPLESLIDER_URL", plugin_dir_url(__FILE__));

include 'wordpress-simpleslider.class.php';

$objSimpleSlider = new WordpressSimpleslider();