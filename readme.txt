=== Feed GeoMashup ===
Contributors: kwiliarty
Donate link: none
Tags: feedwordpress, geomashup, syndication, geodata, georss, aggregation
Requires at least: 2.8
Tested up to: 4.2
Stable tag: 2.2.1

Let two great plugins play great together. Use FeedWordPress to aggregate 
geodata generated by GeoMashup. 

== Description ==

FeedGeoMashup lets [FeedWordPress](http://wordpress.org/extend/plugins/feedwordpress/) pass GeoRSS data to [GeoMashup](http://wordpress.org/extend/plugins/geo-mashup/). Starting with v2.0 you will find plugin options integrated into the FeedWordPress Admin UI. In particlar, you can set site-wide and feed-specific preferences to filter out posts that do not include GeoRSS data. You must have installed, activated and configured both FeedWordPress and GeoMashup. 

VERY IMPORTANT: You must additionally configure FeedWordPress to "Expose syndicated posts to formatting filters." Do this on the admin side at: Syndication > Posts & Links > Formatting > Formatting filters. 

== Installation ==

You can search for this plugin from your WordPress plugins administration interface and install it automatically.

Or to install it manually:

1. Download the zipped plugin using the link to the right
1. Unzip it and put the folder in your wp-contents/plugins folder

VERY IMPORTANT: You must additionally configure FeedWordPress to "Expose syndicated posts to formatting filters." Do this on the admin side at: Syndication > Posts & Links > Formatting > Formatting filters. 

== Frequently Asked Questions ==

= How come I see a shortcode ([geo_mashup_map]) but no map in my syndicated posts? =

You must configure FeedWordPress to "Expose syndicated posts to formatting filters." Do this on the admin side at: Syndication > Posts & Links > Formatting > Formatting filters.

= How does it work? =

Feed GeoMashup scans posts syndicated by FeedWordPress for geodata in the RSS. When your site creates a new syndicated post, GeoMashup can use that data as location information for the post. In addition, Feed GeoMashup scans the incoming posts for maps that GeoMashup created in the original. When it finds such a map, it will replace it with a new  [geo_mashup_map] shortcode. The shortcode on your site will probably not have the same settings as on the original. Your map will probably look different, but it will adhere to whatever preferences you have set up in your own GeoMashup options.  

= Will this plugin pull in geodata from other sources? =

It might. It was created to operate on the GeoRSS generated by the GeoMashup
plugin, but it should work on other similarly formatted feeds. 

= Where are the options settings? =

Look under "Syndication > Posts & Links" to find a Feed GeoMashup options box. Select an individual feed from the drop-down at the top of the same page to set feed-specific preferences.

= If I set Feed GeoMashup to filter out posts with no GeoRSS data, what will happen to posts that I have already syndicated? =

Nothing. They will not be deleted, but neither will they be updated. If you set Feed GeoMashup to filter them, FeedWordPress will be affectively unaware of them.

= Can I filter an incoming feed to include mapped posts only from a limited
area?

Yes. On the "Syndication > Posts & Links" settings page you can define limits
to a range of longitudes and latitudes. Only posts mapped within the limits
will be syndicated.

= Are you offering support for this plugin? =

No.

== Screenshots ==

1. Feed GeoMashup site-wide options
2. Feed GeoMashup feed-specific overrides

== Upgrade Notice ==

= 2.2.1 =
Updates to documentation and compatibility information.

= 2.2 =
Includes an important fix. GeoMashup single maps will now render correctly.

= 2.1 =
Introduces the option to filter mapped posts by longitude and latitude. Only
posts within a given range will be syndicated.

= 2.0 =
This is a significant advance over the initial release. You can now find FeedGeoMashup options among the FeedWordPress settings. You can opt to filter out posts that do not include GeoRSS data. You can set a site-wide preference and feed-specific overrides. 

= 1.0 =
This is the initial release.

== Usage ==

You must have FeedWordPress and GeoMashup installed, activated, and configured
in order for Feed GeoMashup to do anything. Please see the independent
documentation for those plugins. 

VERY IMPORTANT: You must additionally configure FeedWordPress to "Expose syndicated posts to formatting filters." Do this on the admin side at: Syndication > Posts & Links > Formatting > Formatting filters. 

Once you have those pieces in place, you can activate the plugin and start
pulling in GeoMashup maps.

Go to "Syndication > Posts & Links" in the FeedWordPress admin interface to set filtering preferences.

== Changelog ==

= 2.2.1 =
* Update the documentation with critical settings information
* Update the WordPress compatibility information

= 2.2 =
* GeoMashup single maps had been rendering imperfectly after syndication. The
 problem was that the RSS feed included rendered HTML rather than the
shortcode, but the GeoMashup JavaScripts could not be run from the originating
site. FeedGeoMashup now strips the rendered HTML and reasserts the bare shortcode. Any custom attributes set on the
original copy of the post will be lost. On the other hand, default settings on the
syndicating site will apply, a fact that will insure consistency even when
posts come from different sournces.  

= 2.1 =
* Users can now opt to filter an incoming feed's mapped posts by longitude and latitude. 

= 2.0 =
* Adding site-wide and feed-specific options to pull in all posts or only posts with GeoRSS data.

= 1.0 =
* This is the initial release
