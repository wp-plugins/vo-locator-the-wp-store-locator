=== VO Locator - WP Store Locator Plugin ===
Contributors: Jurski
Tags: business locations, store locator, mapping,google maps, locator, shop locator, class locator, events locators, jobs locators, shop finder, shortcode, location finder, places, zipcode locator, stores, plugin, maps, coordinates, latitude, longitude, posts, best google maps, geocoding, shops, page, zipcode, zip code, zip code search, custom google maps, store finder, address map, google map, address location map, map maker, proximity search, map creator, gmaps, mapping software, google map plugin, map tools, google maps, zip code locator, mapping tools, locator maps, map of addresses, map multiple locations, wordpress locator, zipcode search, store locator map
Requires at least: 3.3
Tested up to: 4.2.2
Stable tag: 1.0

Simple wordpress store locator plugin to manage multiple business locations and other any places using Google Maps

== Description ==
VO Locator is simple location management plugin to power your wordpress website with unlimited locations which enable users to find stores, classes locations, events places, jobs availability places. Setup your locations within few minutes with this powerful VO Locator plugin, built in responsive layout, no need to customize map views for various devices. VO Loctator plugin supports shortcode, now display your store listings with one single line of code.

= Visit our website for more information =
[WP Store Locator](http://www.vitalorganizer.com/vo-locator-wordpress-store-locator-plugin/) | [Demo](http://www.vitalorganizer.com/vo-locator-demo/) | [Documentation](http://www.vitalorganizer.com/vo-locator-documentation/) | [Win VO Locator Pro](http://www.vitalorganizer.com/vo-locator-documentation/)

= Funtionality & Features =
* Add Unlimited locations to showcase store listings, class locations, job locations, events listing and other locations with use of Google maps.
* Responsive layout.
* Customize store listing/map listing appearance according to the website theme.
* Auto location look-up based on where user is currently located.
* Auto locate co-ordinates, only need to enter address of the desired place/store.
* Ability to modify the Listing Field Labels.
* Embed listings/maps on page and posts easily using shortcode [VO-LOCATOR].
* Ability to add turn-by-turn driving directions to the location.
* Add thumbnail to the specific location/store.
* Map zoom and scroll with satellite view.
* Easily turn Map display On/Off.
* Ability to hide address from pubic/users for your special cases where you need to only show closest contact phone and other details.

= Using shortcode in theme template files =

In any case if you need to add listing within theme template files, add this line of code to your theme template:

`if(function_exists("volocator_func"))
{
    echo volocator_func();
} )`

Or One can even use the line of code mentioned below instead of the above function

`<?php echo do_shortcode( '[VO-LOCATOR]' ); ?>`

== Installation ==
= Plugin =
1. Upload the `vo-locator` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add your locations through the 'Listings' page in the VO Locator admin area
4. Place the shortcode `[VO-LOCATOR]` in the body of a Page or a Post to display your store locator
5. customize the listing display page and colors through "settings" page in the VO Locator admin area

== Screenshots ==
1. Demo1: Showcase of VO Store Locator listings in Desktop.
2. Demo1: Showcase of VO Store Locator listings in Tablet.
3. Demo1: Showcase of VO Store Locator listings in Mobile.
4. Manage Locations: Easily Manage a Few or Many Locations, Sortable by Name, Street, City, zip, etc. 
5. Add Locations: Once You Add a Location, it is Automatically Given Coordinates
6. Customizer: Choose the Important Options For the Look & Feel of Your store locator


== Frequently Asked Questions ==
[Check documentation](http://www.vitalorganizer.com/vo-locator-documentation/) for the most updated information

= Is VO Locator displayed across various devices with perfect layout? =
* Yes, VO Locator is fully responsive and works across various devices

= Can I disable the map view for my store locator in any case? =
* Yes, you can enable or disable the map view display through 'settings' page in VO Locator admin area

= Can I display the VO Locator in a page template instead of using shortcode in a Page or a Post? =
* Yes, in your page template, add the code:
`if(function_exists("volocator_func"))
{
    echo volocator_func();
} )`
*Or One can even use the line of code mentioned below instead of the above function `<?php echo do_shortcode( '[VO-LOCATOR]' ); ?>`



