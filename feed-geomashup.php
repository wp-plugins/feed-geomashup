<?php
/*
Plugin Name: FeedGeoMashup
Plugin URI: http://wordpress.blogs.wesleyan.edu/plugins/feedgeomashup/
Description: Let two great plugins play great together. Use FeedWordPress to aggregate geodata generated by GeoMashup. 
Version: 2.1
Author: Kevin Wiliarty
Author URI: http://open.pages.kevinwiliarty.com/
*/

/* 
Copyright 2010  Wesleyan University 

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 *
 * Create the admin interface to integrate with FeedWordPress
 *
 */

//hook into the metabox creation function for the 'Posts' page in the
//FeedWordPress Admin UI
add_action(
	/*hook=*/ 'feedwordpress_admin_page_posts_meta_boxes' ,
	/*function=*/ 'add_meta_box_feedgeomashup_options' ,
	/*priority=*/ 100 ,
	/*arguments=*/ 1
);

//hook into the settings-saving function for the 'Posts' page
add_action(
	/*hook=*/ 'feedwordpress_admin_page_posts_save' ,
	/*function=*/ 'feedgeomashup_options_save' ,
	/*priority=*/ 100 ,
	/*arguments=*/ 2
);

/**
 * Create an options area in the FeedWordPress Admin UI
 */

function add_meta_box_feedgeomashup_options( $page ) {
	add_meta_box(
		/*id=*/ 'feedgeomashup_options_box' ,
		/*title=*/ __('Feed GeoMashup Options') ,
		/*callback=*/ 'add_feedgeomashup_options_box' ,
		/*page=*/ $page->meta_box_context() ,
		/*context=*/ $page->meta_box_context()
	);
} /* add_meta_box_feedgeomashup_options() */

/**
 * Function to create the contents of the meta box
 */

function add_feedgeomashup_options_box( $page, $box = NULL ) {

	//the settings are stored in an array
	//these settings populate a radio-button class in feedwordpress
	$setting = array(
		//include unmapped posts?
		'feedgeomashup_posts' => array(
			'all'    => __('All Posts') ,
			'mapped' => __('Mapped Posts Only') ,
		),
		//whether to filter by range?
		'feedgeomashup_filter_mapped_posts' => array(
			'nofilter' => __('Do not filter mapped posts by range') ,
			'filter'   => __('Filter mapped posts by range') ,
		),
		//filter mapped posts by range?
		//these settings are used by this plugin directly
		'feedgeomashup_range' => array(
			'latmin'  => __('Minimum Latitude') ,
			'latmax'  => __('Maximum Latitude') ,
			'longmin' => __('Minimum Longitude') ,
			'longmax' => __('Maximum Longitude') ,
		),
	);

	//if this is the global settings page
	//use the global values
	if (!$page->for_feed_settings()) :
		$feedgeomashup_posts = get_option( 'feedwordpress_feedgeomashup_posts' );
		$feedgeomashup_filter_mapped_posts = get_option( 'feedwordpress_feedgeomashup_filter_mapped_posts' );
		$feedgeomashup_range = get_option( 'feedwordpress_feedgeomashup_range' );
		//set defaults if no range has been specified
		if ( !$feedgeomashup_range ) :
			$feedgeomashup_range = array(
				'latmin' => '-90' ,
				'latmax' => '90' ,
				'longmin' => '-360' ,
				'longmax' => '360'
			);
		endif;
	endif;
?>
<table class="edit-form narrow">

<!-- posts-to-syndicate row -->
<tr><th scope="row">Posts to syndicate:</th>
<td><?php
$params = array(
	'setting-default' => 'default',
	'global-setting-default' => 'all',
	'default-input-value' => 'default',
);
$page->setting_radio_control(
	'feedgeomashup posts', 'feedgeomashup_posts',
	$setting['feedgeomashup_posts'], $params
);
?>
</td></tr>

<!-- filter-mapped-posts row -->
<tr><th scope="row">Filter mapped posts?</th>
<td><?php
$params = array(
	'setting-default' => 'default' , 
	'global-setting-default' => 'nofilter' ,
	'default-input-value' => 'default',
);
$page->setting_radio_control(
	'feedgeomashup_filter_mapped_posts', 'feedgeomashup_filter_mapped_posts',
	$setting['feedgeomashup_filter_mapped_posts'], $params
);
?>
</td>
</tr>

<!-- filter-by-range row -->
<tr><th scope="row">Ranges</th>
<td>
<?php 
if ( !$page->for_feed_settings() ) : 
foreach( $setting['feedgeomashup_range'] as $limit => $label ) {
	$format = '<input type="textbox" maxlength="20" name="feedgeomashup_%s" value="%f" /> %s <br />';
	printf( $format , $limit , $feedgeomashup_range[$limit] , $label );
}
else :
	echo "This is a feed-specific settings page. For the present, at least, range values can be edited only on the default settings for Posts & Links.";
endif;
?>
</td>
</tr>

</table>
<?php
} /* add_feedgeomashup_options_box() */

/**
 * Function to save the settings
 */

function feedgeomashup_options_save( $params , $page) {

	//array of limits
	$limits = array( 'latmin' , 'latmax' , 'longmin' , 'longmax' );

	//fetch and sanitize the mapped post filter checkbox
	$feedgeomashup_filter_mapped_posts = $_REQUEST['feedgeomashup_filter_mapped_posts'];
	if ( $feedgeomashup_filter_mapped_posts != "filter" ) {
		$feedgeomashup_filter_mapped_posts = "nofilter";
	}

	//fetch and sanitize the limits; assemble into array
	foreach ($limits as $limit) {
		$name = "feedgeomashup_$limit";
		$value = $_REQUEST[$name];
		$value = preg_replace( '/[^0-9+\.\-]/', '' , $value );
		$feedgeomashup_range[$limit] = $value;
	}

	//clean up the values and handle empty strings
	$feedgeomashup_range['latmin'] = min( "90" , max( $feedgeomashup_range['latmin'] , "-90" ));
	$feedgeomashup_range['latmax'] = $feedgeomashup_range['latmax'] == "" ? '90' : max( "-90" , min( $feedgeomashup_range['latmax'] , "90" ));
	$feedgeomashup_range['longmin'] = min( "360" , max( $feedgeomashup_range['longmin'] , "-360" ));
	$feedgeomashup_range['longmax'] = $feedgeomashup_range['longmax'] == "" ? '360' : max( "-360" , min( $feedgeomashup_range['longmax'] , "360" ));

	if (!$page->for_feed_settings()) :
		update_option( 'feedwordpress_feedgeomashup_filter_mapped_posts', $feedgeomashup_filter_mapped_posts );
		update_option( 'feedwordpress_feedgeomashup_posts' , $_REQUEST['feedgeomashup_posts']);
		update_option( 'feedwordpress_feedgeomashup_range' , $feedgeomashup_range );
	else :
		$page->link->settings['feedgeomashup posts'] = $_REQUEST['feedgeomashup_posts'];
		$page->link->settings['feedgeomashup_filter_mapped_posts'] = $_REQUEST['feedgeomashup_filter_mapped_posts'];
	endif;
} /* feedgeomashup_options_save() */

/**
 * Function to handle unmapped posts
 */

function feedgeomashup_unmapped_posts( $posts , $link ) {

	//get the site-wide preference for keeping all or only mapped posts
	$sitewide_setting = get_option( 'feedwordpress_feedgeomashup_posts' );
	if ( $sitewide_setting  == 'mapped' ) :
		$keep_posts = 'mapped';
	//keep all posts is the default
	else :
		$keep_posts = 'all';
	endif;

	//override with the individual feed setting if appropriate
	$feed_setting = $link->settings['feedgeomashup posts'];
	if ( $feed_setting == 'mapped' ) :
		$keep_posts = 'mapped';
	elseif ($feed_setting == 'all' ) :
		$keep_posts = 'all';
	endif;

	//if the setting works out to 'all', just return the array
	if ( $keep_posts == 'all' ) {
		return $posts;
	}

	//otherwise, go through the array of items
	$link->magpie->originals = $posts;

	if ( is_array( $posts )) :
		foreach ( $posts as $key => $item ) :
			$post = new SyndicatedPost( $item , $link );	
			$post_point = $post->item['http://www.georss.org/georss']['point']; 
			if ( !$post_point ) :
				unset( $posts[$key] );
			endif;
		endforeach;
	endif;

	return $posts;
} /* feedgeomashup_unmapped_posts() */ 

//hook into syndicated_feed_items
add_filter( 'syndicated_feed_items' , 'feedgeomashup_unmapped_posts' , 100 , 2 );

/**
 * Function to filter posts based on latlong ranges
 */

function feedgeomashup_filter_mapped_posts( $posts , $link ) {

	//Is the option to filter mapped posts selected globally?
	$sitewide_setting = get_option('feedwordpress_feedgeomashup_filter_mapped_posts');
	if ( $sitewide_setting == 'filter' ) :
		$whether_filter = 'filter' ; 
	else : //use the default
		$whether_filter = 'nofilter';
	endif;

	//override with feed setting if appropriate
	$feed_setting = $link->settings['feedgeomashup_filter_mapped_posts'];
	if ( $feed_setting == 'filter' ) :
		$whether_filter = 'filter';
	elseif ( $feed_setting == 'nofilter' ) :
		$whether_filter = 'nofilter';
	endif;

	if ( $whether_filter == 'nofilter' ) :
		return $posts;
	endif;

	//Get the range
	$range = get_option( 'feedwordpress_feedgeomashup_range' );
	$range_latmin = $range['latmin'];
	$range_latmax = $range['latmax'];
	$range_longmin = $range['longmin'];
	$range_longmax = $range['longmax'];
	
	//Go through the array of items
	$link->magpie->originals = $posts;

	if ( is_array( $posts )) :
		foreach ( $posts as $key => $item ) :
			$post = new SyndicatedPost( $item , $link );
			$post_point = $post->item['http://www.georss.org/georss']['point'];
			$post_point = explode( ' ' , $post_point );
			$post_lat = $post_point[0];
			$post_long = $post_point[1];
			if (( $post_lat < $range_latmin ) || ( $post_lat > $range_latmax )) :
				unset( $posts[$key] );
				continue;
			endif;
			if ( $range_longmin < $range_longmax ) :
				if (( $post_long < $range_longmin ) || ( $post_long > $range_longmax )) :
					unset( $posts[$key] );
					continue;
				endif;
			elseif (( $post_long > $range_longmin ) || ( $post_long < $range_longmax )) :
				unset( $posts[$key] );
				continue;
			endif;
		endforeach;
	endif;

	return $posts;

} /* feedgeomashup_filter_mapped_posts */

//hook into syndicated_feed_items
add_filter( 'syndicated_feed_items' , 'feedgeomashup_filter_mapped_posts' , 99 , 2 );

/**
 *
 * Pass geo-data from FeedWordPress to GeoMashup
 *
 */

function feed_geomashup( $post_ID , $syndicated_item ) {

	//pull the location out of the RSS
	$my_point = $syndicated_item->item['http://www.georss.org/georss']['point'];

	//if the post has no geodata
	if( !$my_point ) { 
		return; 
	}

	//put the coordinates in an array for geo_mashup
	$my_points = explode( ' ' , $my_point );
	$mylat = $my_points[0];
	$mylng = $my_points[1];
	$location['lat'] = $mylat;
	$location['lng'] = $mylng;

	//set the object name for geo_mashup
	$object_name = 'post';

	//if the GeoMashup plugin is active
	if( method_exists( 'GeoMashupDB' , 'set_object_location' )) {
		//process the location information for the current post
		GeoMashupDB::set_object_location( $object_name , $post_ID , $location );
	}
} /* feed_geomashup() */

//run the feed_geo_mashup function for new and updated posts
add_action( 'post_syndicated_item' , 'feed_geomashup' , 10 , 2 );
add_action( 'update_syndicated_item' , 'feed_geomashup' , 10 , 2 );

?>
