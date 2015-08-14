<?php
if ( !is_user_logged_in() ) {
	 die("You are not authorized to access this page.");
}
	
	if (!empty($_GET['delete'])) {
		//If delete link is clicked
		if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], "delete-location_".$_GET['delete'])){
			$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TABLE." WHERE id=%d", (int)$_GET['delete'])); 
			
		}
	}
	
	if (!empty($_POST) && !empty($_GET['edit']) && $_POST['act']!="delete") {
		$field_value_str=""; 
		
		if(!isset($_POST['show_address_publicly-'.$_GET['edit']]))
			$_POST['show_address_publicly-'.$_GET['edit']] = 0;
		
		foreach ($_POST as $key=>$value) {
			if (preg_match("@\-$_GET[edit]@", $key)) {
				$key=str_replace("-$_GET[edit]", "", $key); // stripping off number at the end (giving problems when constructing address string below)
				
				if (is_array($value)){
					$value=serialize($value); //for arrays being submitted
					$field_value_str.=$key."='$value',";
				} else {
					$value = sanitize_text_field( $value );
					$field_value_str.=$key."=".$wpdb->prepare("%s", trim(vosl_comma(stripslashes($value)))).", "; 
				}
				$_POST["$key"]=$value; 
			}
		}
		
		$field_value_str=substr($field_value_str, 0, strlen($field_value_str)-2);
		$edit=$_GET['edit']; extract($_POST);
		$the_address="$address, $address2, $city, $state $zip";

		if (empty($_POST['no_geocode']) || $_POST['no_geocode']!=1) { //no_geocode sent by addons that manually edit the the coordinates. Prevents sl_do_geocoding() from overwriting the manual edit.
			$sql = $wpdb->prepare("SELECT * FROM ".VOSL_TABLE." WHERE id=%d", (int)$_GET['edit']);
			$old_address=$wpdb->get_results($sql, ARRAY_A); 
		}
		
		$wpdb->query($wpdb->prepare("UPDATE ".VOSL_TABLE." SET ".str_replace("%", "%%", $field_value_str)." WHERE id=%d", (int)$_GET['edit']));  
		
		if ((empty($_POST['longitude']) || $_POST['longitude']==$old_address[0]['longitude']) && (empty($_POST['latitude']) || $_POST['latitude']==$old_address[0]['latitude'])) {
			if ($the_address!=$old_address[0]['address']." ".$old_address[0]['address2'].", ".$old_address[0]['city'].", ".$old_address[0]['state']." ".$old_address[0]['zip'] || ($old_address[0]['latitude']==="" || $old_address[0]['longitude']==="")) {
				
				vosl_do_geocoding($the_address,$_GET['edit']);
			}
		}
		
		print "<script>location.replace('".str_replace("&edit=$_GET[edit]", "", $_SERVER['REQUEST_URI'])."');</script>";
	}
	
	if (!empty($_GET['changeView']) && $_GET['changeView']==1) {
		if ($vosl_vars['location_table_view']=="Normal") {
			$vosl_vars['location_table_view']='Expanded';
			vosl_data('vosl_vars', 'update', $vosl_vars);
			//$tabViewText="Expanded";
		} else {
			$vosl_vars['location_table_view']='Normal';
			vosl_data('vosl_vars', 'update', $vosl_vars);
			//$tabViewText="Normal";
		}
		print "<script>location.replace('".str_replace("&changeView=1", "", $_SERVER['REQUEST_URI'])."');</script>";
	}
?>