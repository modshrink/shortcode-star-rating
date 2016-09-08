<?php
/*
Plugin Name: Shortcode Star Rating
Plugin URI: https://github.com/modshrink/shortcode-star-rating
Description: You can star rating in the article by using the shortcode. It uses the built-in icon fonts, WordPress 3.8 or higher is required.
Version: 0.1
Author: modshrink
Author URI: http://www.modshrink.com/
Text Domain: shortcode-star-rating
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

$ShortcodeStarRating = new ShortcodeStarRating();

class ShortcodeStarRating {
	public $size;
	public $icon_font_size;
	public $int_font_size;
	public $color;
	public $checked;

	/**
	* Construnt
	*/
	public function __construct() {
		// Text Domain
		load_plugin_textdomain( 'shortcode-star-rating', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// Plugin Activation
		if ( function_exists( 'register_activation_hook' ) ) {
			register_activation_hook( __FILE__, array( &$this, 'activationHook' ) );
		}

		// Plugin Uninstall
		if ( function_exists( 'register_uninstall_hook' ) ) {
			register_uninstall_hook( __FILE__, 'ShortcodeStarRating::uninstallHook' );
		}

		// Version Check
		global $wp_version;
		if( version_compare( $wp_version, '3.8', '<' ) ) {
			add_action( 'admin_notices', array( &$this, 'ssr_notice' ) );
		}

		add_action( 'admin_init', array( &$this, 'pluginDeactivate' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'dashicons_css' ) );
		add_action( 'wp_head', array( &$this, 'shortcode_star_rating_css' ) );
		add_action( 'admin_print_footer_scripts', array( &$this, 'appthemes_add_quicktags' ) );
		add_action( 'admin_menu', array( &$this, 'ssr_add_pages' ) );
		add_shortcode( 'star', array( &$this, 'shortcode_star_rating_func' ) );
	}

	/**
	* Display when it is activated in WordPress 3.8 or lower.
	*/
	public function ssr_notice() { ?>
	<div class="updated">
	<p><?php printf( __( '%s is not working.', 'shortcode-star-rating' ), '<strong>Shortcode Star Raging</strong>', 'shortcode-star-rating' ); ?></p>
	<p><?php
	_e( 'This plugin require the <strong>WordPress 3.8 or higher</strong>.', 'shortcode-star-rating' );
	printf( __( ' Your WordPress version is %s.', 'shortcode-star-rating' ), get_bloginfo('version') );
	echo ' <a href="?deactivatePluginKey=1">' . __( ' Deactivate Now', 'shortcode-star-rating' ) . '</a>';
	?></p>
	</div>
	<?php
	}

	/**
	* Plugin Deactivated from Update Message Box.
	*/
	public function pluginDeactivate() {
		if( is_plugin_active( 'shortcode-star-rating/shortcode-star-rating.php' ) && isset( $_GET['deactivatePluginKey'] ) ) {
			deactivate_plugins( 'shortcode-star-rating/shortcode-star-rating.php' );
			remove_action( 'admin_notices', array( &$this, 'ssr_notice' ) );
			add_action( 'admin_notices', array( &$this, 'pluginDeactivateMessage' ) );
		}
	}

	/**
	* Message after plugin deactivated.
	*/
	public function pluginDeactivateMessage() { ?>
	<div id="message" class="updated"><p><?php _e( 'Plugin <strong>deactivated</strong>.' ) ?></p></div>
	<?php }

	/**
	* Plugin Activation
	*/
	public function activationHook() {
	// Installed Flag
	if ( !get_option( 'shortcode_star_rating_installed' ) ) {
	update_option( 'shortcode_star_rating_installed', 1 );
	}
	// Default Star Color
	if ( !get_option( 'ssr_star_color' ) ) {
	update_option( 'ssr_star_color', '#FCAE00' );
	}
	}

	/**
	* Plugin Uninstall
	*/
	static function uninstallHook() {
		delete_option( 'shortcode_star_rating_installed' );
		delete_option( 'ssr_star_color' );
		delete_option( 'ssr_star_size' );
	}

	/**
	* Load plugin textdomain.
	*/
	public function dashicons_css() {
		wp_enqueue_style( 'dashicons', site_url( '/' )."/wp-includes/css/dashicons.min.css" );
	}

	/**
	* Inline CSS for styling of star.
	*/
	public function shortcode_star_rating_css() {
		if( get_option( 'ssr_star_size' ) == 'yes' ) {
			$this->icon_font_size = '100%';
			$this->int_font_size = '80%';
		} else {
			$this->icon_font_size = '20px';
			$this->int_font_size = '13px';
		}
	?>
	<style type="text/css" media="all">.shortcode-star-rating{padding:0 0.5em;}.dashicons{font-size:<?php echo $this->icon_font_size; ?>;width:auto;height:auto;line-height:normal;text-decoration:inherit;vertical-align:middle;}.shortcode-star-rating [class^="dashicons dashicons-star-"]:before{color:<?php echo get_option( 'ssr_star_color' ) ?>;}.ssr-int{margin-left:0.2em;font-size:<?php echo $this->int_font_size; ?>;vertical-align:middle;color:#333;}/*.shortcode-star-rating:before,.shortcode-star-rating:after{display: block;height:0;visibility:hidden;content:"\0020";}.shortcode-star-rating:after{clear:both;}*/</style>
	<?php }

	/**
	* Replace shortcode to rating star.
	*/
	public function shortcode_star_rating_func( $atts ) {
		extract( shortcode_atts( array(
			'rating' => '0',
			'type' => 'rating',
			'number' => '0',
			'max' => '5',
			'numeric' => 'no',
		), $atts ) );

		if( $max == NULL ) {
			$max = 5;
		}

		/* Display tyle: rating */
		if( $type == "rating" ) {
			// 小数点以下の最後が0の場合は削除
			if( is_float( $rating ) ) {
				$rating = preg_replace( '/\.?0+$/', '', (int)$rating );
			}
			$empty_rating = $max - $rating;

			if( is_float( $empty_rating ) ) {
				$filled = floor( $rating );
				$half = 1;
				$empty = floor($empty_rating);
			} else {
				$filled = $rating;
				$half = 0;
				$empty = $empty_rating;
			}

			if( $max < $filled ) {
				$filled = $max;
			}
		}

		/* Display tyle: percent */
		if( $type == "percent" ) {
			$fill_percentage = $max * ( $rating * 0.01 );
			$empty_percentage = $max - $fill_percentage;

			if( preg_match( '/^\d+\.\d+$/', $fill_percentage ) ) {
				$filled = floor( $fill_percentage );
				$half = 1;
				$empty = floor( $empty_percentage );
			} else {
				$filled = $fill_percentage;
				$half = 0;
				$empty = $empty_percentage;
			}
		}
		// ratingがmaxより高くmaxがマイナス値を取る場合はsrt_repeatでエラーが出るため、強制的に0とする。
		if( !ctype_digit( strval( $empty ) ) ) {
			$empty = 0;
		}

		$ssr_html = "<span class=\"shortcode-star-rating\">";
		$ssr_html .= str_repeat( '<span class="dashicons dashicons-star-filled"></span>', (int)$filled );
		$ssr_html .= str_repeat( '<span class="dashicons dashicons-star-half"></span>', $half );
		$ssr_html .= str_repeat( '<span class="dashicons dashicons-star-empty"></span>', $empty );

		if( $numeric == "yes" ) {
			if( $type == "percent" ) {
				$ssr_html .= "<span class=\"ssr-int\">(" . $rating . "%)</span>";
			} else {
				$ssr_html .= "<span class=\"ssr-int\">(" . $rating . "/" . $max . ")</span>";
			}
		}

		$ssr_html .= "</span>";

		return $ssr_html;
	}

	/**
	* Add quicktag for shortcode.
	*/
	public function appthemes_add_quicktags() {
		if ( wp_script_is( 'quicktags' ) ){
	?>
	<script type="text/javascript">
	QTags.addButton( 'shortcode_star_rating', 'Star Rating', '[star rating=\"\"]', '', 'r', 'Sortcode Rating' );
	</script>
	<?php
		}
	}

	/**
	* Add admin menu.
	*/
	public function ssr_add_pages() {
		add_options_page( 'Shortcode Star Rating', 'Shortcode Star Rating', 'level_8', __FILE__, array( &$this, 'ssr_plugin_options' ) );
	}

	/**
	* Add admin menu.
	*/
	public function ssr_plugin_options() {
		if( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
			$this->color = $_POST[ 'ssr_star_color' ];
			if( isset( $_POST['ssr_star_size'] ) && $_POST['ssr_star_size'] == 'yes' ) {
				$this->size = $_POST[ 'ssr_star_size' ];
			} else {
				$this->size = 'no';
			}
			if( preg_match( '/\A[0-9A-Fa-f]{6}\z/', $this->color ) || preg_match( '/\A#{1}[0-9A-Fa-f]{6}\z/', $this->color ) ) {
				if( preg_match( '/\A[0-9A-Fa-f]{6}\z/', $this->color ) ) {
					$this->color = '#' . $this->color;
				}
			} else {
				$this->color = get_option( 'ssr_star_color' );
				echo '<div class="updated"><p>' . __( 'Invalid HEX color code.', 'shortcode-star-rating' ) . '</p></div>';
			}

			update_option( 'ssr_star_color', $this->color );
			update_option( 'ssr_star_size', $this->size );
			add_action( 'admin_notices', array(&$this, 'colorErrorMes' ) );
		}

		if( get_option( 'ssr_star_size' ) == 'yes' ) {
			$this->checked = ' checked="checked"';
		}

	?>
	<div class="wrap">

	<h2>Shortcode Star Rating</h2>

	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ); ?>">
	<?php wp_nonce_field('update-options'); ?>

	<table class="form-table">

	<tr valign="top">
	<th scope="row"><?php _e( 'Star Color', 'shortcode-star-rating' ); ?></th>
	<td>
	<input type="text" name="ssr_star_color" value="<?php echo get_option( 'ssr_star_color' ); ?>" />
	<p class="description"><?php printf( __( 'Please enter in the Hex color. (Default: %s)', 'shortcode-star-rating' ), '#FCAE00' ); ?></p>
	</td>
	</tr>

	<tr valign="top">
	<th scope="row"><?php _e( 'Adjust Star Size', 'shortcode-star-rating' ); ?></th>
	<td>
	<input type="checkbox" id ="ssr-star-size" name="ssr_star_size" value="yes"<?php echo $this->checked; ?> /> <label for="ssr-star-size"><?php _e( 'Fit in a perent box', 'shortcode-star-rating' ); ?></label>
	<p class="description"><?php _e( 'If there is no check, 20px font size is set.', 'shortcode-star-rating' ); ?></p>
	</td>
	</tr>

	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
	</p>

	</form>
	</div>
	<?php
	}
}
