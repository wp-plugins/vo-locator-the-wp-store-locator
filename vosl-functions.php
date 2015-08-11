<?php



/*-----------------------------------------------------------*/
function vosl_data($setting_name, $i_u_d_s="select", $setting_value="") {
	global $wpdb;
	
	if(!is_array($setting_value))
	{
		$setting_value = sanitize_text_field($setting_value);
	}
	
	if ($i_u_d_s == "insert" || $i_u_d_s == "add" || $i_u_d_s == "update") {
		$setting_value = (is_array($setting_value))? serialize($setting_value) : $setting_value;
		$exists = $wpdb->get_var($wpdb->prepare("SELECT setting_id FROM ".VOSL_SETTING_TABLE." WHERE setting_name = %s", $setting_name));
		if (!$exists) {	
			$q = $wpdb->prepare("INSERT INTO ".VOSL_SETTING_TABLE." (setting_name, setting_value) VALUES (%s, %s)", $setting_name, $setting_value); 
		} else { 
			$q = $wpdb->prepare("UPDATE ".VOSL_SETTING_TABLE." SET setting_value = %s WHERE setting_name = %s", $setting_value, $setting_name);
		}
		$wpdb->query($q);
	} elseif ($i_u_d_s == "delete") {
		$q = $wpdb->prepare("DELETE FROM ".VOSL_SETTING_TABLE." WHERE setting_name = %s", $setting_name);
		$wpdb->query($q);
	} elseif ($i_u_d_s == "select" || $i_u_d_s == "get") {
		$q = $wpdb->prepare("SELECT setting_value FROM ".VOSL_SETTING_TABLE." WHERE setting_name = %s", $setting_name);
		$r = $wpdb->get_var($q);
		$r = (@unserialize($r) !== false || $r === 'b:0;')? unserialize($r) : $r;  //checking if stored in serialized form
		/*if (function_exists("apply_filters")) {
			return apply_filters( 'option_' . $setting_name, $r);  //Compability for WPML or any plugin that uses option_(option_name) hooks
		} else {*/
			return $r;
		//}
	}
}
/*----------------------------------------------------------------*/

/*-------------------------------*/
if (!function_exists("vosl_template")){
   function vosl_template($content) {

	global $vosl_dir, $vosl_base, $vosl_path, $votext_domain, $wpdb, $vosl_vars, $vosl_inc_dir, $form;
	if(! preg_match('|\[vo-locator|i', $content)) {
		return $content;
	}
	else {
		include($vosl_path."/vosl-inc/includes/locations.php");	
		return preg_replace("@\[vo-locator(.*)?\]@i", $form, $content);
		}
    }
}

add_shortcode( 'VO-LOCATOR', 'volocator_func' );

function volocator_func()
{
	global $vosl_dir, $vosl_base, $vosl_uploads_base, $vosl_path, $votext_domain, $wpdb, $vosl_vars, $vosl_inc_dir, $form;
	include($vosl_path."/vosl-inc/includes/locations.php");	
	return $form;
}

function vosl_menu_pages_filter($sl_menu_pages) {
	/*if (function_exists('do_sl_hook')){do_sl_hook('sl_menu_pages_filter', '', array(&$sl_menu_pages));}*/
	
	foreach ($sl_menu_pages as $menu_type => $value) {
		if ($menu_type == 'main') {
			add_menu_page ($value['title'], $value['title'], $value['capability'], $value['page_url'], '', $value['icon'], $value['menu_position']);
		}
		if ($menu_type == 'sub'){
			foreach ($value as $sub_value) {
				 add_submenu_page($sub_value['parent_url'], $sub_value['title'], $sub_value['title'], $sub_value['capability'], $sub_value['page_url']);
			}
		}
	}
}

/*-----------------------------------*/
function vosl_add_options_page() {
	global $vosl_dir, $vosl_base, $votext_domain, $vosl_top_nav_links, $vosl_vars, $vosl_version;
	$parent_url = VOSL_PARENT_URL; //SL_PAGES_DIR.'/information.php';
	$warning_count = 0;
	$warning_title = __("Update(s) currently available for VO Locator", VOSL_TEXT_DOMAIN) . ":";
	
	$notify = ($warning_count > 0)?  " <span class='update-plugins count-$warning_count' title='$warning_title'><span class='update-count'>" . $warning_count . "</span></span>" : "" ;
	
	add_menu_page( "VO Locator", "VO Locator", "administrator", VOSL_PAGES_DIR.'/locations.php', '', VOSL_BASE.'/images/logo.ico.png', 47 );
	add_submenu_page( VOSL_PAGES_DIR.'/locations.php', 'Listings', 'Listings', 'administrator', VOSL_PAGES_DIR.'/locations.php', '');
	add_submenu_page( VOSL_PAGES_DIR.'/locations.php', 'Settings', 'Settings', 'administrator', VOSL_PAGES_DIR.'/settings.php', '');
	//sl_menu_pages_filter($sl_menu_pages);
}

/*----------------------------*/
function vosl_install_tables() {
	global $wpdb, $vosl_db_version, $vosl_path, $vosl_hook, $vosl_db_prefix;

	if (!defined("VOSL_TABLE") || !defined("VOSL_SETTING_TABLE")){ 
		//add_option("sl_db_prefix", $wpdb->prefix); $sl_db_prefix = get_option('sl_db_prefix'); 
		$vosl_db_prefix = $wpdb->prefix; //better this way, in case prefix changes vs storing option - 1/29/15
	}
	if (!defined("VOSL_TABLE")){ define("VOSL_TABLE", $vosl_db_prefix."vostore_locator");}
	/*if (!defined("SL_TAG_TABLE")){ define("SL_TAG_TABLE", $sl_db_prefix."sl_tag"); }*/
	if (!defined("VOSL_SETTING_TABLE")){ define("VOSL_SETTING_TABLE", $vosl_db_prefix."vosl_setting"); }
	if (!defined("VOSL_PEOPLE_TABLE")){ define("VOSL_PEOPLE_TABLE", $vosl_db_prefix."vosl_people"); }
	
	$table_name = VOSL_TABLE;
	$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(8) unsigned NOT NULL auto_increment,
			store_name varchar(255) NULL,
			address varchar(255) NULL,
			address2 varchar(255) NULL,
			city varchar(255) NULL,
			state varchar(255) NULL,
			country varchar(255) NULL,
			zip varchar(255) NULL,
			latitude varchar(255) NULL,
			longitude varchar(255) NULL,
			description mediumtext NULL,
			url varchar(255) NULL,
			phone varchar(255) NULL,
			fax varchar(255) NULL,
			email varchar(255) NULL,
			image varchar(255) NULL,
			hours varchar(255) NULL,
			show_address_publicly int(3),
			_wpnonce varchar(255) NULL,
			_wp_http_referer text NULL,
			PRIMARY KEY  (id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
			
	$table_name_2 = VOSL_PEOPLE_TABLE;
	$sql .= "CREATE TABLE " . $table_name_2 . " (
			people_id bigint(20) unsigned NOT NULL auto_increment,
			place_id int(11) NULL,
			name varchar(255) NULL,
			category_id mediumint(8) NULL,
			PRIMARY KEY  (people_id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
	
	$table_name_3 = VOSL_SETTING_TABLE;
	$sql .= "CREATE TABLE " . $table_name_3 . " (
			setting_id bigint(20) unsigned NOT NULL auto_increment,
			setting_name varchar(255) NULL,
			setting_value longtext NULL,
			PRIMARY KEY  (setting_id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
	//$sql .= "INSERT INTO " . $table_name_3 . " (sl_setting_name, sl_setting_value) VALUES ('sl_db_prefix', '" . $wpdb->prefix . "');";
			
	if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name || $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_3)) != $table_name_3 || $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_2)) != $table_name_2) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		vosl_data("vosl_db_version", 'add', $vosl_db_version);
	}
	
	$installed_ver = vosl_data("vosl_db_version");
	if( $installed_ver != $vosl_db_version ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		vosl_data("vosl_db_version", 'update', $vosl_db_version);
	}
	
	if (vosl_data("vosl_db_prefix")===""){
		vosl_data('vosl_db_prefix', 'update', $vosl_db_prefix);
	}
}
/*-------------------------------*/

function vosl_comma($a) {
	$a=str_replace('"', "&quot;", $a);
	$a=str_replace("'", "&#39;", $a);
	$a=str_replace(">", "&gt;", $a);
	$a=str_replace("<", "&lt;", $a);
	$a=str_replace(" & ", " &amp; ", $a);
	return str_replace("," ,"&#44;" ,$a);
	
}

function vosl_location_form($mode="add", $pre_html="", $post_html=""){
	$html="<form name='manualAddForm' method='post'>
	$pre_html
	<table cellpadding='0' class='widefat'>
	<thead><tr><th>".__("Add&nbsp;Listing", VOSL_TEXT_DOMAIN)."</th></tr></thead>
	<tr>
		<td>
		<div style='display:inline; width:50%'>
		<span id='format' style='display:none'><i>".__("Name of Listing", VOSL_TEXT_DOMAIN)."<br>
		".__("Address (Street - Line1)", VOSL_TEXT_DOMAIN)."<br>
		".__("Address (Street - Line2 - optional)", VOSL_TEXT_DOMAIN)."<br>
		".__("City, State Zip", VOSL_TEXT_DOMAIN)."</i></span>
		".__("Name of Listing", VOSL_TEXT_DOMAIN)."<br><input name='store_name' size=40 type='text'><br><br>
		".__("Address", VOSL_TEXT_DOMAIN)."<br><input name='address' size=21 type='text'>&nbsp;<small>(".__("Street - Line1", VOSL_TEXT_DOMAIN).")</small><br>
		<input name='address2' size=21 type='text'>&nbsp;<small>(".__("Street - Line 2 - optional", VOSL_TEXT_DOMAIN).")</small><br>
		<table cellpadding='0px' cellspacing='0px'><tr><td style='padding-left:0px' class='nobottom'><input name='city' size='21' type='text'><br><small>".__("City", VOSL_TEXT_DOMAIN)."</small></td>
		<td><input name='state' size='7' type='text'><br><small>".__("State", VOSL_TEXT_DOMAIN)."</small></td>
		<td><input name='zip' size='10' type='text'><br><small>".__("Zip", VOSL_TEXT_DOMAIN)."</small></td></tr>
		<tr><td style='padding-left:0px' class='nobottom'>
		<input name='show_address_publicly' type='checkbox' value='1' checked='checked' >&nbsp;<small>".__("Share Address Publicly", VOSL_TEXT_DOMAIN)."</small></td></tr>
		</table><br>
		</div><div style='display:inline; width:50%'>
		".__("Additional Information", VOSL_TEXT_DOMAIN)."<br>
		<textarea name='description' rows='5' cols='17'></textarea>&nbsp;<small>".__("Description", VOSL_TEXT_DOMAIN)."</small><br>
		<input name='url' type='text'>&nbsp;<small>".__("URL", VOSL_TEXT_DOMAIN)."</small><br>
		<input name='phone' type='text'>&nbsp;<small>".__("Phone", VOSL_TEXT_DOMAIN)."</small><br>
		<input name='fax' type='text'>&nbsp;<small>".__("Fax", VOSL_TEXT_DOMAIN)."</small><br>
		<input name='email' type='text'>&nbsp;<small>".__("Email", VOSL_TEXT_DOMAIN)."</small><br>
		<input id='upload_image' type='text' name='image'>&nbsp;<small>".__("Image URL (shown with location)", VOSL_TEXT_DOMAIN)."</small>&nbsp;<input type='button' value='Image upload' id='upload_image_button' class='button' /><br>
		<input name='hours' type='text'>&nbsp;<small>".__("Hours", VOSL_TEXT_DOMAIN)."</small>";
		
		$html.=(function_exists("do_sl_hook"))? do_sl_hook("vosl_add_location_fields",  "append-return") : "" ;
		$html.=wp_nonce_field("add-location_single", "_wpnonce", true, false);
		$html.="<br><br>
	<input type='submit' value='".__("Add Listing", VOSL_TEXT_DOMAIN)."' class='button-primary'>
	</div>
	</td>
		</tr>
	</table>
	$post_html
</form>
<script language=\"JavaScript\">
jQuery(document).ready(function() {
jQuery('#upload_image_button').click(function() {
formfield = jQuery('#upload_image').attr('name');
tb_show('', 'media-upload.php?type=image&TB_iframe=true');
return false;
});

window.send_to_editor = function(html) {
imgurl = jQuery('img',html).attr('src');
jQuery('#upload_image').val(imgurl);
tb_remove();
}

});
</script>";
	return $html;
}
function vosl_add_location() {
	global $wpdb;
	$fieldList=""; $valueList="";
	foreach ($_POST as $key=>$value) {
			$fieldList.="$key,";
			
			if (is_array($value)){
				$value=serialize($value); //for arrays being submitted
				$valueList.="'$value',";
				
			} else {
				$value = sanitize_text_field( $value );
				$valueList.=$wpdb->prepare("%s", vosl_comma(stripslashes($value))).",";
			}
	}
	
	$fieldList=substr($fieldList, 0, strlen($fieldList)-1);
	$valueList=substr($valueList, 0, strlen($valueList)-1);
	$wpdb->query("INSERT INTO ".VOSL_TABLE." ($fieldList) VALUES ($valueList)") or die(mysql_error());
	$new_loc_id=$wpdb->insert_id;
	$address="$_POST[address], $_POST[address2], $_POST[city], $_POST[state] $_POST[zip]";
	vosl_do_geocoding($address);
}

/*----------------------------------------------------*/
function vosl_single_location_info($value, $colspan, $bgcol) {
	//global $sl_hooks;
	$_GET['edit'] = $value['id']; //die("edit: ".var_dump($_GET)); die();
	
	print "<tr style='background-color:$bgcol' id='sl_tr_data-$value[id]'>";
	
	if($value['show_address_publicly']==1)
			$show_directions = ' checked="checked" ';
			
	print "<td colspan='$colspan'><form name='manualAddForm' method=post>
	<a name='a$value[id]'></a>
	<table cellpadding='0' class='manual_update_table'>
	<tr>
		<td style='vertical-align:top !important; width:30%'><b>".__("Name of Listing", VOSL_TEXT_DOMAIN)."</b><br><input name='store_name-$value[id]' id='store-$value[id]' value='$value[store_name]' size=30 type='text'><br><br>
		<b>".__("Address", VOSL_TEXT_DOMAIN)."</b><br><input name='address-$value[id]' id='address-$value[id]' value='$value[address]' size='13' type='text'>&nbsp;<small>(".__("Street - Line1", VOSL_TEXT_DOMAIN).")</small><br>
		<input name='address2-$value[id]' id='address2-$value[id]' value='$value[address2]' size='13' type='text'>&nbsp;<small>(".__("Street - Line 2 - optional", VOSL_TEXT_DOMAIN).")</small><br>
		<table cellpadding='0px' cellspacing='0px'><tr><td style='padding-left:0px' class='nobottom'><input name='city-$value[id]' id='city-$value[id]' value='$value[city]' size='13' type='text'><br><small>".__("City", VOSL_TEXT_DOMAIN)."</small></td>
		<td><input name='state-$value[id]' id='state-$value[id]' value='$value[state]' size='4' type='text'><br><small>".__("State", VOSL_TEXT_DOMAIN)."</small></td>
		<td><input name='zip-$value[id]' id='zip-$value[id]' value='$value[zip]' size='6' type='text'><br><small>".__("Zip", VOSL_TEXT_DOMAIN)."</small>
		</td></tr>
		<tr><td style='padding-left:0px' class='nobottom'><input name='show_address_publicly-$value[id]' $show_directions id='show_address_publicly-$value[id]' value='1' type='checkbox'>&nbsp;<small>".__("Share Address Publicly", VOSL_TEXT_DOMAIN)."</small></td></tr></table>";
		
		$cancel_onclick = "location.href=\"".str_replace("&edit=$_GET[edit]", "",$_SERVER['REQUEST_URI'])."\"";
		
		$show_directions = '';
		
		print "<br><br>
		<nobr><input type='submit' value='".__("Update", VOSL_TEXT_DOMAIN)."' class='button-primary'>&nbsp;&nbsp;<input type='button' class='button' value='".__("Cancel", VOSL_TEXT_DOMAIN)."' onclick='$cancel_onclick'></nobr>
		</td><td style='width:60%; vertical-align:top !important;'>
		<b>".__("Additional Information", VOSL_TEXT_DOMAIN)."</b><br>
		<textarea name='description-$value[id]' id='description-$value[id]' rows='5' cols='17'>$value[description]</textarea>&nbsp;<small>".__("Description", VOSL_TEXT_DOMAIN)."</small><br>		
		<input name='url-$value[id]' id='url-$value[id]' value='$value[url]' size='19' type='text'>&nbsp;<small>".__("URL", VOSL_TEXT_DOMAIN)."</small><br>
		<input name='phone-$value[id]' id='phone-$value[id]' value='$value[phone]' size='19' type='text'>&nbsp;<small>".__("Phone", VOSL_TEXT_DOMAIN)."</small><br>
		<input name='fax-$value[id]' id='fax-$value[id]' value='$value[fax]' size='19' type='text'>&nbsp;<small>".__("Fax", VOSL_TEXT_DOMAIN)."</small><br>
		<input name='email-$value[id]' id='email-$value[id]' value='$value[email]' size='19' type='text'>&nbsp;<small>".__("Email", VOSL_TEXT_DOMAIN)."</small><br>
		<input id='upload_image' name='image-$value[id]' id='image-$value[id]' value='$value[image]' size='19' type='text'>&nbsp;<small>".__("Image URL (shown with location)", VOSL_TEXT_DOMAIN)."</small>&nbsp;<input type='button' value='Image upload' class='button' id='upload_image_button' /><br /><input name='hours-$value[id]' id='hours-$value[id]'  type='text' value='$value[hours]' size='19'>&nbsp;<small>".__("Hours", VOSL_TEXT_DOMAIN)."</small>";
		
		print "</td><td style='vertical-align:top !important; width:40%'>";
	
	print "</td></tr>
	</table>
</form>
<script language=\"JavaScript\">
jQuery(document).ready(function() {
jQuery('#upload_image_button').click(function() {
formfield = jQuery('#upload_image').attr('name');
tb_show('', 'media-upload.php?type=image&TB_iframe=true');
return false;
});

window.send_to_editor = function(html) {
imgurl = jQuery('img',html).attr('src');
jQuery('#upload_image').val(imgurl);
tb_remove();
}

});
</script>
</td>";

print "</tr>";
	}
/*-------------------------------------------*/

/*-----------------------------------*/
if (!function_exists("vosl_do_geocoding")){
 function vosl_do_geocoding($address, $sl_id="") {
   if (empty($_POST['no_geocode']) || $_POST['no_geocode']!=1){
	global $wpdb, $text_domain;

	// Initialize delay in geocode speed
	$delay = 100000;
	$base_url = "https://maps.googleapis.com/maps/api/geocode/json?";

	if ($sensor!="" && !empty($sensor) && ($sensor === "true" || $sensor === "false" )) {$base_url .= "sensor=".$sensor;} else {$base_url .= "sensor=false";}

	// Iterate through the rows, geocoding each address
		$request_url = $base_url . "&address=" . urlencode(trim($address)); //print($request_url );
    
	//New code to accomdate those without 'file_get_contents' functionality for their server - added 3/27/09 8:56am - provided by Daniel C. - thank you
	if (extension_loaded("curl") && function_exists("curl_init")) {
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $request_url);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
		$resp_json = curl_exec($cURL);
		curl_close($cURL);  
	}else{
		$resp_json = file_get_contents($request_url) or die("url not loading");
	}
	//End of new code

	$resp = json_decode($resp_json, true); //var_dump($resp);
    $status = $resp['status']; //$status = "";
    $lat = (!empty($resp['results'][0]['geometry']['location']['lat']))? $resp['results'][0]['geometry']['location']['lat'] : "" ;
    $lng = (!empty($resp['results'][0]['geometry']['location']['lng']))? $resp['results'][0]['geometry']['location']['lng'] : "" ;
	//die("<br>compare: ".strcmp($status, "OK")."<br>status: $status<br>");
    if (strcmp($status, "OK") == 0) {
		// successful geocode
		$geocode_pending = false;
		$lat = $resp['results'][0]['geometry']['location']['lat'];
		$lng = $resp['results'][0]['geometry']['location']['lng'];

		if ($sl_id==="") {
			$query = $wpdb->prepare("UPDATE ".VOSL_TABLE." SET latitude = '%s', longitude = '%s' WHERE id = %d LIMIT 1;", $lat, $lng, (int)$wpdb->insert_id);
			//$query = sprintf("UPDATE ".VOSL_TABLE." SET latitude = '%s', longitude = '%s' WHERE id = '%s' LIMIT 1;", esc_sql($lat), esc_sql($lng), esc_sql($wpdb->insert_id)); //die($query); 
		} else {
			$query = $wpdb->prepare("UPDATE ".VOSL_TABLE." SET latitude = '%s', longitude = '%s' WHERE id = %d LIMIT 1;", $lat, $lng, (int)$sl_id);
			//$query = sprintf("UPDATE ".VOSL_TABLE." SET latitude = '%s', longitude = '%s' WHERE id = '%s' LIMIT 1;", esc_sql($lat), esc_sql($lng), esc_sql($sl_id)); 
		}
		$update_result = $wpdb->query($query);
		if ($update_result === FALSE) {
			die("Invalid query: " . $wpdb->last_error);
		}
    } else if (strcmp($status, "OVER_QUERY_LIMIT") == 0) {
		// sent geocodes too fast
		$delay += 100000;
    } else {
		// failure to geocode
		$geocode_pending = false;
		echo __("Address " . $address . " <font color=red>failed to geocode</font>. ", VOSL_TEXT_DOMAIN);
		//if (!empty($status)) {
			echo __("Received status " . $status , VOSL_TEXT_DOMAIN)."\n<br>";
		/*} else {
			echo __("No status received from Google", VOSL_TEXT_DOMAIN)."\n<br>"; 
		}*/
    }
    usleep($delay);
  } else {
  	//print __("Geocoding bypassed ", VOSL_TEXT_DOMAIN);
  } @ob_flush(); flush();
 }
}
/*-------------------------------*/

/*-----------------------------------------------------------*/
function vo_url_test($url){
	if (preg_match("@^https?://@i", $url)) {
		return TRUE; 
	} else {
		return FALSE; 
	}
}
/*---------------------------------------------------------------*/
/*--------------------------------------------------*/
function vosl_define_db_tables() {
	//since it can't use sl_data() in the sl-define.php, placed here
	//$sl_db_prefix = get_option('sl_db_prefix'); 
	global $wpdb; 
	$sl_db_prefix = $wpdb->prefix; //better this way, in case prefix changes vs storing option - 1/29/15
	if (!defined('VOSL_DB_PREFIX')){ define('VOSL_DB_PREFIX', $sl_db_prefix); }
	if (!empty($sl_db_prefix)) {
		if (!defined('VOSL_TABLE')){ define('VOSL_TABLE', VOSL_DB_PREFIX."vostore_locator"); }
		if (!defined('VOSL_SETTING_TABLE')){ define('VOSL_SETTING_TABLE', VOSL_DB_PREFIX."vosl_setting"); }
	}
}
vosl_define_db_tables(); 
/*-----------------------------------------------*/
add_action('admin_bar_menu', 'vosl_admin_toolbar', 183);
function vosl_admin_toolbar($admin_bar){
	
	
	$sl_admin_toolbar_array[] = array(
		'id'    => 'sl-menu',
		'title' => __('VO Locator', VOSL_TEXT_DOMAIN),
		'href'  => preg_replace('@wp-admin\/[^\.]+\.php|index\.php@', 'wp-admin/admin.php', VOSL_INFORMATION_PAGE),	
		'meta'  => array(
			'title' => 'VO Locator',			
		),
	);

	$sl_admin_toolbar_array[] = array(
		'id'    => 'sl-menu-locations',
		'parent' => 'sl-menu',
		'title' => __('Locations', VOSL_TEXT_DOMAIN),
		'href'  => preg_replace('@wp-admin\/[^\.]+\.php|index\.php@', 'wp-admin/admin.php', VOSL_MANAGE_LOCATIONS_PAGE),
		'meta'  => array(
			'title' => __('Locations', VOSL_TEXT_DOMAIN),
			'target' => '_self',
			'class' => 'sl_menu_class'
		),
	);
	
	if (function_exists('do_sl_hook')){ do_sl_hook('sl_admin_toolbar_filter', '', array(&$sl_admin_toolbar_array)); }
	
	foreach ($sl_admin_toolbar_array as $toolbar_page) {
		$admin_bar->add_menu($toolbar_page);
	}
	
} 

/*-----------------------------------------------*/
### Loading VOSL Variables ###
$vosl_vars=vosl_data('sl_vars');

if (!is_array($vosl_vars)) {
	//print($vosl_vars."<br><br>");
	function vosl_fix_corrupted_serialized_string($string) {
		$tmp = explode(':"', $string);
		$length = count($tmp);
		for($i = 1; $i < $length; $i++) {    
			list($string) = explode('"', $tmp[$i]);
        		$str_length = strlen($string);    
        		$tmp2 = explode(':', $tmp[$i-1]);
        		$last = count($tmp2) - 1;    
        		$tmp2[$last] = $str_length;         
        		$tmp[$i-1] = join(':', $tmp2);
    		}
    		return join(':"', $tmp);
	}
	$vosl_vars = vosl_fix_corrupted_serialized_string($vosl_vars); //die($vosl_vars);
	vosl_data('vosl_vars', 'update', $vosl_vars);
	$vosl_vars = unserialize($vosl_vars); //var_dump($vosl_vars);
	//die($vosl_vars);
}

function vo_wp_gear_manager_admin_scripts() {
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_script('jquery');
}

function vo_wp_gear_manager_admin_styles() {
wp_enqueue_style('thickbox');
}

add_action('admin_print_scripts', 'vo_wp_gear_manager_admin_scripts');
add_action('admin_print_styles', 'vo_wp_gear_manager_admin_styles');

// load the locator js for front end
add_action('wp_print_scripts', 'vo_load_locator');

function vo_load_locator() {
	// load our jquery file that sends the $.post request
	wp_enqueue_script( "voloadlocator", plugin_dir_url( __FILE__ ) . '/js/locator.js', array( 'jquery' ) );
	// make the ajaxurl var available to the above script
	wp_localize_script( 'voloadlocator', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
}

function vo_find_more_locations_process_request()
{
	global $wpdb, $vosl_base; 
	
	// first check if data is being sent and that it is the data we want
	$offset = 0;
	if(isset($_POST['offset']))
		$offset = (int)$_POST['offset'] + 1;
	
  	if ( isset( $_POST["address"] ) ) {
		// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
		$data = trim($_POST["address"]);
		// send the response back to the front end
		if($data!='')
		{
			$address = $data;
			$address = urlencode($address);
			$address = str_replace(" ", "%20", $address);
			
			$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";	
			//$url = urlencode($url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			curl_close($ch);
			$resp = json_decode($response, true);
			
			$status = $resp['status']; //$status = "";
			$lat = (!empty($resp['results'][0]['geometry']['location']['lat']))? $resp['results'][0]['geometry']['location']['lat'] : "" ;
			$lng = (!empty($resp['results'][0]['geometry']['location']['lng']))? $resp['results'][0]['geometry']['location']['lng'] : "" ;
			
			$sql = $wpdb->prepare("SELECT *, ( 3959 * acos( cos( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." order by distance ASC limit ".$offset.", ".VOSL_LOCATIONS_PAGESIZE, $lat,$lng,$lat);
			
			//$sql = "SELECT *, ( 3959 * acos( cos( radians(".$lat.") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." order by distance ASC limit ".$offset.", ".VOSL_LOCATIONS_PAGESIZE;
			
		}else
		{
			$sql = "SELECT * FROM ".VOSL_TABLE." order by store_name ASC limit ".$offset.", ".VOSL_LOCATIONS_PAGESIZE;
		}
		
		$result_row = $wpdb->get_results($sql,ARRAY_A);
		//echo $response;
		if(!empty($result_row))
		{
			$cc = $offset;
			foreach($result_row as $row){ 
			
				
				$address = '';
				$address .= (!empty($row['address']))?$row['address']:'';
				$address .= (!empty($row['address2']))?", ".$row['address2']:'';
				$address .= (!empty($row['city']))?", ".$row['city']:'';
				$address .= (!empty($row['state']))?", ".$row['state']:'';
				$address .= (!empty($row['zip']))?" ".$row['zip']:'';
				
				$add = str_replace("","%20", $address);
				//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;
				$addr_link_src = "http://maps.google.com/maps?daddr=".$add;	
				
				$callout = "<h4 style='float:left;width:90%;'>".$row['store_name']."</h4><h4><a href='#' style='float:right;' onclick='closeMarker(".$row['id']."); return false;'>X</a></h4>";
				
				/*if($row['show_address_publicly']==1)
					$callout .= "<p><img src='".$vosl_base."/images/icons/address.png' /> <a href='".$addr_link_src."' target='_blank'>".$address."</a></p>";*/
					
				if($row['show_address_publicly']==1)	
					$callout .= "<p><img src='".$vosl_base."/images/icons/address.png' /> <a href='#' onclick=\"showDrivingDirections('".$addr_link_src."'); return false;\">".$address."</a></p>";
				
				if($row['url']!='')
					$callout .= "<p><img src='".$vosl_base."/images/icons/URL.png' /> ".$row['url']."</p>";
				
				if($row['phone']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/phone.png' /> ".$row['phone']."</p>";
				
				if($row['fax']!='')
					$callout .= "<p><img src='".$vosl_base."/images/icons/fax.png' /> ".$row['fax']."</p>";
				
				if($row['email']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/email.png' /> <a href='mailto:".$row['email']."'>".$row['email']."</a></p>";
					
				if($row['hours']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/hours.png' /> ".$row['hours']."</p>";
					
				if($row['description']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/description.png' /> ".$row['description']."</p>";	
					
				if($row['image']!='')
					$image = $row['image'];
				else
					$image = $vosl_base."/images/locationimg.jpg";	
				
				$htm = '<div class="row"><div class="col-lg-5"><div class="img_placeholder"><img src="'.$image.'" class="img-responsive" /></div></div><div class="col-lg-7">'.$callout.'</div></div>';		
			
				//$address="$row[address], $row[address2], $row[city], $row[state] $row[zip]"; ?>
<div class="row locationlist" id="location_<?=$row['id']?>" data-offset="<?=$cc?>">
<div class="lat" style="display:none"><?=$row['latitude']?></div>
<div class="long" style="display:none"><?=$row['longitude']?></div>
<div class="callout" style="display:none"><?=$htm?></div>
<h4><?=$row['store_name']?>
<?php if($row['distance']!=''){ 
			$distance = number_format ( $row['distance'], 2 );
			?>
<span><?=$distance?> mi</span>
<?php } ?>
</h4>
<?php /*?> <?php if($row['show_address_publicly']){ ?>
            <p><?=$address?></p>
            <?php } ?><?php */?>
<div class="locationdetails">
<div class="row">
<?php if($row['image']!=''){ ?>
<div class="col-md-2 col-sm-2">
<div class="row imagerow"><div class="img_placeholder"><img src="<?=$row['image']?>" class="img-responsive" /></div></div>
</div>
<?php } ?>
<div class="col-md-10 col-sm-10">
<div class="row mainrow">
<?php if($row['description']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/description.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['description']?></div></div>
<?php } ?>
<?php if($row['show_address_publicly']==1){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/address.png' /></strong></div><div class="col-md-10 col-sm-10"><a href='#' onclick="showDrivingDirections('<?=$addr_link_src?>');return false"><?=$address?></a></div></div>
<?php } ?>
<?php if($row['url']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/URL.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['url']?></div></div>
<?php } ?>
<?php if($row['phone']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/phone.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['phone']?></div></div>
<?php } ?>
<?php if($row['fax']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/fax.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['fax']?></div></div>
<?php } ?>
<?php if($row['email']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/email.png' /></strong></div><div class="col-md-10 col-sm-10"><a href="mailto:<?=$row['email']?>"><?=$row['email']?></a></div></div>
<?php } ?>
<?php if($row['hours']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/hours.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['hours']?></div></div>
<?php } ?>
</div>
</div>
</div>
</div>
</div>
<?php $cc = $cc + 1; } 
			
		}
		die();
	}
}

function vo_find_locations_process_request() {
	global $wpdb, $vosl_base; 
	
	// first check if data is being sent and that it is the data we want
  	if ( isset( $_POST["address"] ) ) {
		// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
		$data = trim($_POST["address"]);
		
		
		$address = $data;
		$address = urlencode($address);
		$address = str_replace(" ", "%20", $address);
		
		if($_POST["lat"]!='' and $_POST["long"]!='')
		{
			$status = 'OK';
			$lat = $_POST["lat"];
			$lng = $_POST["long"];
			
		}else
		{
			$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";	
			//$url = urlencode($url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			curl_close($ch);
			$resp = json_decode($response, true);
			
			$status = $resp['status']; //$status = "";
			$lat = (!empty($resp['results'][0]['geometry']['location']['lat']))? $resp['results'][0]['geometry']['location']['lat'] : "" ;
			$lng = (!empty($resp['results'][0]['geometry']['location']['lng']))? $resp['results'][0]['geometry']['location']['lng'] : "" ;
		}	
		//die("<br>compare: ".strcmp($status, "OK")."<br>status: $status<br>");
		if (strcmp($status, "OK") == 0) {
			//$sql = "SELECT *, ( 3959 * acos( cos( radians(".$lat.") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." order by distance ASC limit 0, ".VOSL_LOCATIONS_PAGESIZE;	
			$sql = $wpdb->prepare("SELECT *, ( 3959 * acos( cos( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." order by distance ASC limit 0, ".VOSL_LOCATIONS_PAGESIZE, $lat,$lng,$lat);	
		}else
		{
			$sql = "SELECT * FROM ".VOSL_TABLE." order by store_name ASC limit 0, ".VOSL_LOCATIONS_PAGESIZE;		
		}
		
		
		$result_row = $wpdb->get_results($sql,ARRAY_A);
		//echo $response;
		if(!empty($result_row))
		{
			$cc = 0;
			foreach($result_row as $row){ 
			
				
				$address = '';
				$address .= (!empty($row['address']))?$row['address']:'';
				$address .= (!empty($row['address2']))?", ".$row['address2']:'';
				$address .= (!empty($row['city']))?", ".$row['city']:'';
				$address .= (!empty($row['state']))?", ".$row['state']:'';
				$address .= (!empty($row['zip']))?" ".$row['zip']:'';
				
				$add = str_replace("","%20", $address);
				//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;
				$addr_link_src = "http://maps.google.com/maps?daddr=".$add;
		
				$callout = "<h4 style='float:left;width:90%;'>".$row['store_name']."</h4><h4><a href='#' style='float:right;' onclick='closeMarker(".$row['id']."); return false;'>X</a></h4>";
				
				if($row['show_address_publicly']==1)
					$callout .= "<p><img src='".$vosl_base."/images/icons/address.png' /> <a href='#' onclick=\"showDrivingDirections('".$addr_link_src."'); return false;\">".$address."</a></p>";
				
				if($row['url']!='')
					$callout .= "<p><img src='".$vosl_base."/images/icons/URL.png' /> ".$row['url']."</p>";
				
				if($row['phone']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/phone.png' /> ".$row['phone']."</p>";
				
				if($row['fax']!='')
					$callout .= "<p><img src='".$vosl_base."/images/icons/fax.png' /> ".$row['fax']."</p>";
				
				if($row['email']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/email.png' /> <a href='mailto:".$row['email']."'>".$row['email']."</a></p>";
					
				if($row['hours']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/hours.png' /> ".$row['hours']."</p>";
					
				if($row['description']!='')	
					$callout .= "<p><img src='".$vosl_base."/images/icons/description.png' /> ".$row['description']."</p>";	
					
				if($row['image']!='')
					$image = $row['image'];
				else
					$image = $vosl_base."/images/locationimg.jpg";	
				
				$htm = '<div class="row"><div class="col-lg-5"><div class="img_placeholder"><img src="'.$image.'" class="img-responsive" /></div></div><div class="col-lg-7">'.$callout.'</div></div>';		
	
			
				//$address="$row[address], $row[address2], $row[city], $row[state] $row[zip]"; ?>
<div class="row locationlist" id="location_<?=$row['id']?>" data-offset="<?=$cc?>">
<div class="lat" style="display:none"><?=$row['latitude']?></div>
<div class="long" style="display:none"><?=$row['longitude']?></div>
<div class="callout" style="display:none"><?=$htm?></div>
<h4><?=$row['store_name']?>
<?php if($row['distance']!=''){ 
			$distance = number_format ( $row['distance'], 2 );
			?>
<span><?=$distance?> mi</span>
<?php } ?>
</h4>
<?php /*?> <?php if($row['show_address_publicly']){ ?>
            <p><?=$address?></p>
            <?php } ?><?php */?>
<div class="locationdetails">
<div class="row">
<?php if($row['image']!=''){ ?>
<div class="col-md-2 col-sm-2">
<div class="row imagerow"><div class="img_placeholder"><img src="<?=$row['image']?>" class="img-responsive" /></div></div>
</div>
<?php } ?>
<div class="col-md-10 col-sm-10">
<div class="row mainrow">
<?php if($row['description']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/description.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['description']?></div></div>
<?php } ?>
<?php if($row['show_address_publicly']==1){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/address.png' /></strong></div><div class="col-md-10 col-sm-10"><a href='#' onclick="showDrivingDirections('<?=$addr_link_src?>');return false"><?=$address?></a></div></div>
<?php } ?>
<?php if($row['url']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/URL.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['url']?></div></div>
<?php } ?>
<?php if($row['phone']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/phone.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['phone']?></div></div>
<?php } ?>
<?php if($row['fax']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/fax.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['fax']?></div></div>
<?php } ?>
<?php if($row['email']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/email.png' /></strong></div><div class="col-md-10 col-sm-10"><a href="mailto:<?=$row['email']?>"><?=$row['email']?></a></div></div>
<?php } ?>
<?php if($row['hours']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src='<?=$vosl_base?>/images/icons/hours.png' /></strong></div><div class="col-md-10 col-sm-10"><?=$row['hours']?></div></div>
<?php } ?>
</div>
</div>
</div>
</div>
</div>
<?php $cc = $cc + 1; } 
			
		}else
		{
			echo "NO";
		}
		die();
	}
}

add_action('wp_ajax_load_more_locations', 'vo_find_more_locations_process_request');
add_action('wp_ajax_nopriv_load_more_locations', 'vo_find_more_locations_process_request'); 

add_action('wp_ajax_find_locations', 'vo_find_locations_process_request');
add_action('wp_ajax_nopriv_find_locations', 'vo_find_locations_process_request'); 

add_action( 'admin_enqueue_scripts', 'vo_add_color_picker' );
function vo_add_color_picker( $hook ) {
 
    if( is_admin() ) { 
     
        // Add the color picker css file       
        wp_enqueue_style( 'wp-color-picker' ); 
         
        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'custom-script-handle', plugin_dir_url( __FILE__ ) . '/js/color-script.js', array( 'wp-color-picker' ), false, true ); 
    }
}
?>