<?php
/*
Plugin Name: Shortcode Rating
Plugin URI: https://github.com/modshrink/shortcode-rating
Description: While you are logged in to WordPress, this plugin will move to the bottom the admin bar that is displayed on the web site.
Version: 0.0.1
Author: modshrink
Author URI: http://www.modshrink.com/
Text Domain: shortcode-rating
Domain Path: /languages
License: GPL2

Copyright 2014  modshrink  (email : hello@modshrink.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'wp_head', 'shortcode_rating_css');
add_action('plugins_loaded', 'shortcode_rating_init');

/**
 * Load plugin textdomain.
 */


function shortcode_rating_init() {
  load_plugin_textdomain( 'shortcode-rating', false, dirname( plugin_basename( __FILE__ ) ) ); 
}


/**
 * Inline CSS for styling of star.
 */

function shortcode_rating_css() { ?>
<style type="text/css" media="all">.shortcode-rating [class^="dashicons dashicons-star-"]:before{color:#FCAE00;}.shortcode-rating .int{color:#333;font-size:75%;}</style>
<?php }


/**
 * Replace shortcode to rating star.
 */

function shortcode_rating_func($atts) {
  extract(shortcode_atts(array(
    'max' => '5',
    'value' => '0'
  ), $atts));

  if( $max == NULL) {
    $max = 5;
  }
  
  $no_rate = $max - $value;
  if( is_float($no_rate) ) {
    $filled = floor($value);
    $half = 1;
    $empty = floor($no_rate);
  } else {
    $filled = $value;
    $half = 0;
    $empty = $no_rate;
  }

  if( $max < $filled ) {
    $filled = $max;
  }

  echo "<div class=\"shortcode-rating\">";
  echo str_repeat( '<div class="dashicons dashicons-star-filled"></div>', $filled );
  echo str_repeat( '<div class="dashicons dashicons-star-half"></div>', $half );
  echo str_repeat( '<div class="dashicons dashicons-star-empty"></div>', $empty );
  echo "<span class=\"int\">(" . $value . "/" . $max . ")</span>";
  echo "</div>";
}

add_shortcode('rating', 'shortcode_rating_func');


/**
 * Add quicktag for shortcode.
 */

function appthemes_add_quicktags() {
    if (wp_script_is('quicktags')){
    ?>
      <script type="text/javascript">
      QTags.addButton( 'shortcode_rating', 'Rating', '[rating value=\"\" max=\"\"]', '', 'r', 'Sortcode Rating' );
      </script>
      <?php
    }
}

add_action( 'admin_print_footer_scripts', 'appthemes_add_quicktags' );