<?php
/*
Plugin Name: Ingeni Chocolat Grid Gallery
Version: 2020.02
Plugin URI: http://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: Chocolat lightbox based grid and lightbox for Wordpress
*/

/*
Copyright (c) 2019 Ingeni Web Solutions
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

Disclaimer: 
	Use at your own risk. No warranty expressed or implied is provided.
	This program is free software; you can redistribute it and/or modify 
	it under the terms of the GNU General Public License as published by 
	the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 	See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Requires : Wordpress 3.x or newer ,PHP 5 +

v2018.01 - Initial version
v2019.01 - Added support for progressive-image JS library to increase loading times with large galleries
v2019.02 - Added plugin updater code (via Github repo).
					- Add some support CSS for background images, where the image is portrait oriented.
					- Fixed use of $ instead of jQuery.
v2019.03 - Added the file_ids param. Allows you to pass a command delimited list of media IDs for the required photos. For example, create a WP Gallery within the post and use the 'ids' param as the file_ids param.
v2020.01 - Support .jpeg files.
				 - Fixed loading of plugin updater code. Moved to the init() hook.
v2020.02 - Added the cell_class parameter - Allows use with the Foundation Float grid. Defaults to 'cell' for use with the XY Grid.
*/

add_shortcode( 'ingeni-chocolat','do_ingeni_chocolat' );
function do_ingeni_chocolat( $atts ) {
	$retHtml = "";

	$def_path = ingeni_chocolat_get_home_path();
	$params = shortcode_atts( array(
			'source_path' => '/photos-bucket/',
			'wrapper_class' => 'ingeni-chocolat-wrap',
			'max_thumbs' => 0,
			'shuffle' => 1,
			'arrows' => 0,
			'file_list' => "",
			'file_path' => "",
			'file_ids' => "",
			'start_path' => $def_path,
			'bg_images' => 0,
			'category' => "",
			'lightbox' => 1,
			'progressive' => 1,
			'cell_class' => 'cell',
		), $atts ) ;

	$photos = array();
	$home_path = "";
	//fb_log('file ids='.$params['file_ids']);

	// If a list of media ID, get the source URLs and create a file_list
	if (strlen($params['file_ids']) > 0) {

		$media_ids = array();
		$media_ids = explode(",",$params['file_ids']);

		$source_urls = "";
		$idx = 0;


		foreach($media_ids as $media_id) {
			$source_urls .= wp_get_attachment_url( $media_id ) . ',';
		}
		$source_urls = substr($source_urls,0,strlen($source_urls)-1);

		$params['file_list'] = $source_urls;
		$params['file_path'] = "";
	}

	ingeni_chocolat_get_photos( $params['category'], $params['file_list'], $params['max_thumbs'], $params['start_path'], $params['source_path'], $photos, $home_path, $params['progressive'] );
	
	$show_arrows = "false";
	if ($params['arrows'] == 1) {
		$show_arrows = "true";
	}
	
	$sync1 = "";

	
	$idx = 0;
	if ($params['shuffle'] > 0) {
		shuffle($photos);
	}
	clearstatcache();

	foreach ($photos as $photo) {
		if ( (strpos(strtolower($photo),'.jpg') !== false) || (strpos(strtolower($photo),'.jpeg') !== false) || (strpos(strtolower($photo),'.png') !== false) ) {		
			if ($params['bg_images'] > 0) {

				if ( $params['lightbox'] > 0 ) {
					$sync1 .= '<div class="'.$params['cell_class'].' small-12 medium-6 large-4"><a class="chocolat-image" href="'. $home_path . $photo .'"><div class="bg-item" style="background-image:url(\''. $home_path . $photo .'\')" ></div></a></div>';				
				} else {
					$sync1 .= '<div class="'.$params['cell_class'].' small-12 medium-6 large-4"><div class="bg-item" style="background-image:url(\''. $home_path . $photo .'\')" ></div></div>';				
				}

			} else {
				if ( $params['lightbox'] > 0 ) {
					if ( file_exists( getcwd() . $params['source_path'] . 'tiny/' . $photo ) ) {
						$sync1 .= '<div class="'.$params['cell_class'].' small-12 medium-6 large-4"><a class="chocolat-image progressive replace" href="'. $home_path . $photo .'"><img src="'. $home_path . 'tiny/' . $photo .'" class="preview"></img></a></div>';
					} else {
						$sync1 .= '<div class="'.$params['cell_class'].' small-12 medium-6 large-4"><a class="chocolat-image" href="'. $home_path . $photo .'"><img src="'. $home_path . $photo .'"></img></a></div>';
					}
				} else {
					if ( file_exists( getcwd() . $params['source_path'] . 'tiny/' . $photo ) ) {
						$sync1 .= '<div class="'.$params['cell_class'].' small-12 medium-6 large-4"><a class="progressive replace" href="'. $home_path . $photo .'"><img src="'. $home_path . 'tiny/' . $photo .'" ></img></a></div>';
					} else {
						$sync1 .= '<div class="'.$params['cell_class'].' small-12 medium-6 large-4"><img src="'. $home_path . $photo .'" ></img></div>';
					}
				}
			}
			$idx += 1;
			if ( ($idx >= $params['max_thumbs']) && ($params['max_thumbs'] > 0) ) {
				break;
			}
		}
	}

	$js = "";
	if ( $params['lightbox'] > 0 ) {
		$js = '<script type="text/javascript">jQuery(document).ready(
				function() {
					jQuery(".chocolat-wrap").Chocolat({
						imageSize: \'contain\',
						arrows: '.$show_arrows.',
						loop: true,
						enableZoom: true
			}); });</script>';
	}
	$retHtml = $sync1.$js;

	return $retHtml;
}


function ingeni_chocolat_get_home_path() {
	$home    = set_url_scheme( get_option( 'home' ), 'http' );
	$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

	if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
			$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
			$home_path = trailingslashit( $home_path );
	} else {
			$home_path = ABSPATH;
	}

	return str_replace( '\\', '/', $home_path );
}


function ingeni_chocolat_get_photos($category, $file_list, $max_thumbs, $start_path, $source_path, &$photo_collection , &$home_path) {
	//$photo_collection = array();
//fb_log('tumbs='.$max_thumbs);
	if ( strlen($category) > 0 ) {
		$post_attribs = array (
			'posts_per_page' => $max_thumbs,
			'offset' => 0,
			'category_name' => $category
		);
		$myquery = new WP_Query( $post_attribs );
	
		if ( $myquery->have_posts() ) {
			while ( $myquery->have_posts() ) {
				$myquery->the_post();
				$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );

				array_push( $photo_collection, $thumb_url );
			}
		}

	} elseif ( strlen($file_list) > 0 ) {
		$photo_collection = explode(",",$file_list);

	} else {
		try {
			if ($start_path != '') {
				chdir($start_path);
			}
//fb_log('curr path:'.getcwd() .'|'.$source_path);;
			$photo_collection = scandir(getcwd() . $source_path);
		} catch (Exception $ex) {
			fb_log('Scanning folder '.$source_path.' : '.$ex->message);
		}
		$home_path = get_bloginfo('url') . $source_path;
	}

	//return $photo_collection;
}




function ingeni_load_chocolat() {
	// chocolat slider
	$dir = plugins_url( 'chocolat/', __FILE__ );

	wp_enqueue_style( 'chocolat-css', $dir . 'css/chocolat.css' );

	wp_register_script( 'chocolat_js', $dir .'js/jquery.chocolat.min.js', null, '0.4', true );
	wp_enqueue_script( 'chocolat_js' );


	// progressive-image image loading
	$dir = plugins_url( 'progressive-image/', __FILE__ );
	
	wp_enqueue_style( 'progressive-css', $dir . 'css/progressive-image.min.css' );

	wp_register_script( 'progressive_js', $dir .'js/progressive-image.min.js', null, '1.2', true );
	wp_enqueue_script( 'progressive_js' );

	// Init auto-update from GitHub repo
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/ingeni-chocolat-grid',
		__FILE__,
		'ingeni-chocolat-grid'
	);
}
add_action( 'init', 'ingeni_load_chocolat' );


// Plugin activation/deactivation hooks
function ingeni_chocolat_activation() {
	flush_rewrite_rules( false );
}
register_activation_hook(__FILE__, 'ingeni_chocolat_activation');

function ingeni_chocolat_deactivation() {
  flush_rewrite_rules( false );
}
register_deactivation_hook( __FILE__, 'ingeni_chocolat_deactivation' );

?>