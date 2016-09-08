=== Shortcode Star Rating ===
Contributors: modshrink
Donate link:
Tags: rating, shortcode, dashicons
Requires at least: 3.8
Tested up to: 4.6
Stable tag: 0.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Rating in the article with Dashicons.

== Description ==
= Overview =
* You can star rating in the article by using the shortcode.
* It uses the built-in icon fonts(Dashicons), WordPress 3.8 or higher is required.

= Example of how to use =
* [star rating="3"] ... Display 3 filled star and 2 blank star. e.g. &#9733;&#9733;&#9733;&#9734;&#9734;
* [star rating="3.5"] ... Display 3 filled star, 1 half star and 1 blank star.
* [star rating="85" type="percent"] ... Express the value of the percentage. The range of values ​​is 0-100.
* [star rating="7" max="10"] ... You can display the stars more than 5. e.g. &#9733;&#9733;&#9733;&#9733;&#9733;&#9733;&#9733;&#9734;&#9734;&#9734;
* [star rating="5" numeric="yes"] ... Display the number. e.g. &#9733;&#9733;&#9733;&#9733;&#9733;(5/5)

= Options =
* rating ... Number of rating stars. Default value is '0'.
* max ...  Limit of star to be displayed. Default value is '5'.
* type ... Choose the 'percent' or 'rating'. Default value is 'rating'
* numeric ... Display the numbers after the rating star. Default value is 'no'.

= Settings =
In admin menu, 'Settings' -> 'Shortcode Star Rating'

* Star Color ... Enter the HEX color code the color of the star. Default '#FCAE00'.
* Adjust Star Size ... If you check in this box, stars fit in a perent box size. It is fixed at 20px If you do not check.


== Installation ==
= Install from dashboard =
1. Visit 'Plugins > Add New'
1. Search for 'Bottom Admin Bar'
1. Click on the 'Install Now'
1. Activate Bottom Admin Bar from your Plugins page.
= Manual upload =
1. Upload `/bottom-admin-bar/` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

== Screenshots ==
1. Shorcode options example

== Changelog ==

= 0.2 =
* Fixed errors when you set the 'rating' attr greater than the 'max' attr.

= 0.1 =
* Released
* Added setting page (Star color and size)
* Displays an alert when you are using less than WordPress 3.8
* Support the percentage value
* Added 'Star Rating' quick tag

= 0.0.2 =
* Changed codes based on wp_star_rating function

= 0.0.1 =
* Development

== Upgrade notice ==
