<?php
if(isset($_POST['btnSubmit']))
{
	if(!isset($_POST['location_map']))
		$set_value = 0;
	else
		$set_value = sanitize_text_field($_POST['location_map']);
			
	vosl_data('sl_location_map', 'update', $set_value);
	
	if(!isset($_POST['find_current_location_lookup']))
		$set_value = 0;
	else
		$set_value = sanitize_text_field($_POST['find_current_location_lookup']);
		
	vosl_data('sl_current_location_lookup', 'update', $set_value);	
	
	if($_POST['fndLocationText']!='')
	{
		$fndLocationText = sanitize_text_field( $_POST['fndLocationText'] );
		vosl_data('sl_find_location_text', 'update', $fndLocationText);
	}	
	
	if($_POST['color-field']!='')
	{
		$colorfield = sanitize_text_field( $_POST['color-field'] );
		vosl_data('sl_highlight_color', 'update', $colorfield);
	}	
		
	if($_POST['color-field-text']!='')
	{
		$colorfieldtext = sanitize_text_field( $_POST['color-field-text'] );
		vosl_data('sl_highlight_text_color', 'update', $colorfieldtext);
	}	
		
	if($_POST['color-field-text-bg']!='')
	{
		$colorfieldtextbg = sanitize_text_field( $_POST['color-field-text-bg'] );
		vosl_data('sl_listing_bg_color', 'update', $colorfieldtextbg);	
	}
}

$location_value = vosl_data('sl_location_map');
$location_current = vosl_data('sl_current_location_lookup');

if($location_value=='')
	$location_value = 1;

?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div><h2>VO Location Settings</h2>
<div style="width:65%;float:left">
<form method="post">
<table class="form-table">
<tbody>
<tr valign="top">
<th scope="row">Show Map</th>
<td> <fieldset><legend class="screen-reader-text"><span>Show Location with Map</span></legend><label for="users_can_register">
<input type="checkbox" value="1" id="location_map" name="location_map" <?php if($location_value==1){ ?> checked="checked" <?php } ?>>
This will enable showing Map with listing</label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row">Current Location</th>
<td> <fieldset><legend class="screen-reader-text"><span>Enable Current Location Lookup on Load?</span></legend><label for="users_can_register">
<input type="checkbox" value="1" id="find_current_location_lookup" name="find_current_location_lookup" <?php if($location_current==1){ ?> checked="checked" <?php } ?>>
Enable Current Location Lookup on Load</label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row">Search Box Heading Text</th>
<td>
<?php
if(vosl_data('sl_find_location_text')=='')
	$text = 'Find a Location';
else
	$text = vosl_data('sl_find_location_text');	
?>
<input type="text" value="<?=esc_html($text);?>" id="fndLocationText" name="fndLocationText" />
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row">Listing Highlight Color</th>
<td>
<?php 
if(vosl_data('sl_highlight_color')=='')
	$bgcolor = '#3DA1D9';
else
	$bgcolor = vosl_data('sl_highlight_color');	
?>
<input type="text" value="<?=esc_html($bgcolor);?>" id="color-field" name="color-field" />
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row">Listing Text Color</th>
<td>
<?php 
if(vosl_data('sl_highlight_text_color')=='')
	$bgcolor = '#000000';
else
	$bgcolor = vosl_data('sl_highlight_text_color');	
?>
<input type="text" value="<?=esc_html($bgcolor);?>" id="color-field-text" name="color-field-text" />
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row">Listing background Color</th>
<td>
<?php 
if(vosl_data('sl_listing_bg_color')=='')
	$bgcolor = '#FFFFFF';
else
	$bgcolor = vosl_data('sl_listing_bg_color');	
?>
<input type="text" value="<?=esc_html($bgcolor);?>" id="color-field-text-bg" name="color-field-text-bg" />
</fieldset></td>
</tr>
</tbody></table>
<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="btnSubmit"></p></form>
</div>
<div style="width:25%;float:left;border-left:2px solid #999;padding-left:10px">
<h2>Documentation</h2>
<p>To use this plugin within pages and posts you simply will have to insert this shortcode [VO-LOCATOR] within your post/page content</p>
<p>If you need to use this plugin in php code you will need to call php function as belows:</p>
<p><strong>
if(function_exists("volocator_func"))<br />
{<br />
echo volocator_func();<br />
}</strong>
</p>
<p>Or else you can do as follows:</p><strong>
<p>echo do_shortcode( '[VO-LOCATOR]' ); </p></strong>
<p>For more information please visit our website: <a href="http://www.vitalorganizer.com/vo-locator-wordpress-store-locator-plugin/" target="_blank">Click Here</a>&nbsp;|&nbsp;<a href="http://www.vitalorganizer.com/vo-locator-documentation/" target="_blank">Documentation</a>&nbsp;|&nbsp;<a href="http://www.vitalorganizer.com/vo-locator-documentation/" target="_blank">Win VO Locator PRO</a></p>
</div>
</div>