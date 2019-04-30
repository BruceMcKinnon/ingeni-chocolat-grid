=== Ingeni Slick Carousel ===

Contributors: Bruce McKinnon
Tags: carousel, slick slider
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2019.02

A Chocolat lightbox based grid and lightbox for Foundation-based Wordpress themes.



== Description ==

* - Images are added by adding them to a folder (hosted on the web server).

* - Based on the Choclat lightbox




== Installation ==

1. Upload the 'ingeni-chocolat-grid' folder to the '/wp-content/plugins/' directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. Display the lightbox grid using the shortcode



== Frequently Asked Questions ==



= How do a display the lightbox? =

Use the shortcode [ingeni-chocolat]

The following parameters may be included:



source_path: Directory relative to the home page that contains the images to b displayed. Defaults to '/photos-bucket/',

wrapper_class: Wrapping class name. Defaults to 'ingeni-chocolat-wrap'.

max_thumbs: Max. number of thumbnails to display. Defaults to 0 (show all thumbnails).

arrows: Show navigation arrows. Defaults to 1 (show arrows).

shuffle: Randomly shuffle the order of the images. Defaults to 1 (shuffle images).

bg_images: Display images as background images. Default = 0 (foreground images)

category: Display the featured images from posts of a specific category. Provide the category name as the parameter value.

file_ids: Provide a command delimited list of WP media library IDs. (You can get this by creating a gallery within the post. The gallery short specifies a list of media ids to be used).





== Changelog ==

v2018.01 - Initial version

v2019.01 - Added support for progressive-image JS library to increase loading times with large galleries

v2019.02 - Added plugin updater code (via Github repo)

v2019.03 - Added the file_ids param. Allows you to pass a command delimited list of media IDs for the required photos. For example, create a WP Gallery within the post and use the 'ids' param as the file_ids param.

