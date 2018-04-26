=== Wp-Thumbie - Related Posts with thumbnails for WordPress ===
Contributors: blogsdna
Tags: related posts, similar posts, thumbnails, thumbnail related posts, related posts with thumbails
Requires at least: 2.5
Tested up to: 3.3.1
Stable tag: 0.1.9

Show user defined number of related / similar posts with thumbnail image

== Description ==

Display a list of related posts along with thumbnail images of those posts for the current post. 

You can select the number of posts to be display on articles or on feed.

You can choose to exclude posts from certain categories as well as exclude pages.


= Features =

* Display Related Posts with thumbnail image automatically in content / feed, no need to edit template files 
* Doesn't use any custom field to generate thumbnail images 
* You can manually add code to your template where you want the Wp-thumbie to be displayed 
* Exclude posts from categories 
* Exclude display of thumbnail related posts on Pages 
* Exclude pages in thumbnail related Posts list 
* Related posts based on content and post title 
* Set thumbnail image size (width & height)
* Turn on/off post excerpt (automattically extracted from posts)
* set length of post excerpt 
* Different styling for displaying thumbnail related posts 
* Display Thumbnail related posts in RSS Feeds (Styling issue)
* To use images from external sites you must set ALLOW_ALL_EXTERNAL_SITES to True in timthumb.php (use it as your own risk)

== Changelog ==
= 0.1.9 =
* Plugin uses latest version of timthumb.php script (version 2.8.10)
* Compatible with WordPress 3.3.1

= 0.1.8 =
* Compatible with WordPress 3.1.2
* Uses latest version of timthumb.php image resizing script (v1.35 Rev149 http://code.google.com/p/timthumb/source/detail?r=149)
* Added Belorussian language support thanks to Marcis Gasuns of http://pc.de
* Added Albanian language Support thanks to Romeo of romeolab.com

= 0.1.7 =
* Compatible with WordPress 2.9.2

= 0.1.6 =
*  Fixed thumbnails not displayed issue for webhost with mod_security apache module installed.

= 0.1.5 =
* Compatible with WordPress 2.8.5 
* Fixed typo for (missing underscore "_top"), opening links in New window

= 0.1.4 =
* Release


== Installation ==

1. Download Wp-Thumbie plugin

2. Extract the contents of wp-thumbie.zip to wp-content/plugins/ folder. You should get a folder called wp-thumbie.

3. Activate the Plugin in WP-Admin. 

4. Goto Settings > Wp-Thumbie to configure

== Screenshots ==

1. Wp-Thumbie Option Page

2. Thumbnail Related Posts 

== How to Upgrade =
1. If you have modified stylesheet (wp_thumbie_verticle.css) then make sure to backup that file first.

2. Once you have backup files use WrodPress plugin upgrade option to upgrade to latest version.


== Frequently Asked Questions ==
= is Wp-thumbie users vulnerable to latest timthumb.php 0-day vulnerability? =
No, thankfully wp-thumbie has been using older version of timthumb.php script which doesn't have support for external images thats where the vulnerability exists.

= What are the requirements for this plugin? =

WordPress 2.5 or above
Your Webhost must support Php GD Library

= Can I customize what is displayed? =

All options can be customized within the Options page in WP-Admin itself

The plugin uses the css id `wp_thumbie` in the `div` that surrounds the list items. So, if you are interested, you can add code to your *style.css* file of your theme to style the related posts list.

= Thumbnails are not displayed ? =

Check out permission the of Wp-thumbie and wp-thumbie/cache folder. it should be set to 777.

For more information, please visit http://www.blogsdna.com/

= Known Issues =

If you turn on Wp-Thumbie Thumbnail Related posts for feeds style may not apply properly in feed readers.

= Thesis Theme Issue =

Wp-thmbie is not compatible with Thesis theme, issue is images are added to post using custom field.
We are working on getting custom field and attachment support in Wp-thumbie that will fix the issue of Thesis theme blogs.