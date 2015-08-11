<?php
error_reporting(E_ALL ^ E_NOTICE);  
  
if( isset($_POST['dele']) and $_POST['dele']==1 and $_POST['loc_ids']!='')
{
	if($_POST['loc_ids']!='')
	{
		$loc_ids = sanitize_text_field( $_POST['loc_ids'] );
		$location_ids = trim($loc_ids);
		$explode_ids = explode(",",$location_ids);
		
		foreach($explode_ids as $id)
		{
			$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TABLE." WHERE id=%d", (int)$id)) or die(mysql_error()); 
		}
		//$wpdb->query("DELETE FROM ".VOSL_TABLE." WHERE id IN (".mysql_escape_string($location_ids).")");
		header("Location:".VOSL_PAGES_DIR.'/locations.php');
		//exit;
	}	
}

if(!isset($_GET['o']) and @$_GET['o']=='')
	$o = " store_name ASC ";
else
	$o = $_GET['o']." ASC ";	
?>
<div class='wrap'>

<?php if(empty($_GET['edit'])){ ?>
<h2>Listings</h2>
<?php } ?>
<?php

if (!empty($_GET['edit'])){ print "<style>#wpadminbar {display:none !important;}</style>"; }

$hidden="";
foreach($_GET as $key=>$val) {
	//hidden keys to keep same view after form submission
	if ($key!="q" && $key!="o" && $key!="d" && $key!="changeView" && $key!="start") {
		$hidden.="<input type='hidden' value='$val' name='$key'>\n"; 
	}
}

require_once(VOSL_ACTIONS_PATH."/processLocationData.php");

print "<table style='width:100%'><tr><td>";
print "<div class='mng_loc_forms_links'>";

if (empty($_GET['q'])){ $_GET['q']=""; }
$search_value = ($_GET['q']==="")? "Search" : vosl_comma(stripslashes($_GET['q'])) ;
print "</div>";

print "</div>";
print "</td><td>";

//establishes WHERE clause in query from URL querystring
//sl_set_query_defaults();

$vosl_vars['admin_locations_per_page'] = 100;

//for search links
	$where = '';
	/*$numMembers=$wpdb->get_results("SELECT id FROM ".VOSL_TABLE." $where");
	$numMembers2=count($numMembers); */
	$start=(empty($_GET['start']))? 0 : $_GET['start'];
	$num_per_page=$vosl_vars['admin_locations_per_page']; //edit this to determine how many locations to view per page of 'Manage Locations' page
	/*if ($numMembers2!=0) {include(VOSL_INCLUDES_PATH."/search-links.php");}*/
//end of for search links

print "</td></tr></table>";

print "<form name='locationForm' id='locationForm' method='post'><input type='hidden' name='dele' id='dele' value='0' /><input type='hidden' name='loc_ids' id='loc_ids' /> ";

if(empty($_GET['d'])) {$_GET['d']="";} if(empty($_GET['o'])) {$_GET['o']="";}

//print "<br>";
$master_check = (!empty($master_check))? $master_check : "" ;
//include(VOSL_INCLUDES_PATH."/mgmt-buttons-links.php");
print "<table class='widefat' cellspacing=0 id='loc_table'>
<thead><tr >
<th colspan='1'><input type='checkbox' id='master_checkbox' $master_check></th>
<th colspan='1'>".__("Actions", VOSL_TEXT_DOMAIN)."</th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=id&d=$d'>".__("ID", VOSL_TEXT_DOMAIN)."</a></th>";

$vosl_vars['location_table_view'] = 'Normal';

//th_co = th_close_open
$th_co = ($is_normal_view)? "</th>\n<th>" : ", " ;
$th_style = ($is_normal_view)? "" : "style='white-space: nowrap;' " ;

print "<th {$th_style}><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=store_name&d=$d'>".__("Name", VOSL_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=address&d=$d'>".__("Street", VOSL_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=address2&d=$d'>".__("Street2", VOSL_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=city&d=$d'>".__("City", VOSL_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=state&d=$d'>".__("State", VOSL_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=zip&d=$d'>".__("Zip", VOSL_TEXT_DOMAIN)."</a></th>";

if ($vosl_vars['location_table_view']!="Normal") {
	print "<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=description&d=$d'>".__("Description", VOSL_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=url&d=$d'>".__("URL", VOSL_TEXT_DOMAIN)."</a></th>

<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=phone&d=$d'>".__("Phone", VOSL_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=fax&d=$d'>".__("Fax", VOSL_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=email&d=$d'>".__("Email", VOSL_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=image&d=$d'>".__("Image", VOSL_TEXT_DOMAIN)."</a></th>";
}


print "<th>(Lat, Lon)</th>
</tr></thead>";

	$o=esc_sql($o); $d=esc_sql($d); 
	$start=esc_sql($start); $num_per_page=esc_sql($num_per_page); 
	if ($locales=$wpdb->get_results("SELECT * FROM ".VOSL_TABLE." ORDER BY $o $d LIMIT $start, $num_per_page", ARRAY_A)) { 
		$colspan=($vosl_vars['location_table_view']!="Normal")? 18 : 11;
		
		$bgcol="";
		
		foreach ($locales as $value) {
			$bgcol=($bgcol==="" || $bgcol=="#eee")?"#fff":"#eee";			
			$bgcol=($value['latitude']=="" || $value['longitude']=="")? "#CBE4EF" : $bgcol;			
			$value=array_map("trim",$value);
			
			if (!empty($_GET['edit']) && $value['id']==$_GET['edit']) {
				vosl_single_location_info($value, $colspan, $bgcol);
			}
			else {
				$value['url']=(!vo_url_test($value['url']) && trim($value['url'])!="")? "http://".$value['url'] : $value['url'] ;
				$value['url']=($value['url']!="")? "<a href='$value[url]' target='blank'>".__("View", VOSL_TEXT_DOMAIN)."</a>" : "" ;
				$value['image']=($value['image']!="")? "<a href='$value[image]' target='blank'>".__("View", VOSL_TEXT_DOMAIN)."</a>" : "" ;
				$value['description']=($value['description']!="")? "<a href='#description-$value[id]' rel='sl_pop'>".__("View", VOSL_TEXT_DOMAIN)."</a><div id='description-$value[id]' style='display:none;'>".vosl_comma($value['description'])."</div>" : "" ;
			
				if(empty($_GET['edit'])) {$_GET['edit']="";}
				$edit_link = str_replace("&edit=$_GET[edit]", "",$_SERVER['REQUEST_URI'])."&edit=" . $value['id'] ."#a$value[id]'";
				
				print "<tr style='background-color:$bgcol' id='sl_tr-$value[id]'>
			<th><input type='checkbox' name='id[]' value='$value[id]' class='checkbox1'></th>
			<td><a class='edit_loc_link' href='".$edit_link." id='$value[id]'>".__("Edit", VOSL_TEXT_DOMAIN)."</a>&nbsp;|&nbsp;<a class='del_loc_link' href='".wp_nonce_url("$_SERVER[REQUEST_URI]&delete=$value[id]", "delete-location_".$value['id'])."' onclick=\"confirmClick('Sure?', this.href); return false;\" id='$value[id]'>".__("Delete", VOSL_TEXT_DOMAIN)."</a></td>
			<td> $value[id] </td>";

				
				if ($is_normal_view) {
					//tco = td_close_open
					$tco_address = $tco_address2 = $tco_city = $tco_state = $tco_zip = "</td>\n<td>";
					$strong_addr_open = $strong_addr_close = "";
				} else {
					$tco_address = (!empty($value['address']) && !empty($value['store']))? "<br>" : "" ;
					$tco_address2 = (!empty($value['address2']))? ", " : "" ; 
					$tco_address2 = (empty($value['address']) && !empty($value['address2']))? "<br>" : $tco_address2 ;
					$tco_city = (!empty($value['city']) || !empty($value['state']) || !empty($value['zip']))? "<br>" : "" ;
					$tco_state = (!empty($value['city']))? ", " : "" ;
					$tco_zip = (!empty($value['zip']))? " " : "" ;
					$strong_addr_open = "<strong>"; $strong_addr_close = "</strong>";
				}
				
				print "<td> $value[store_name]{$tco_address}
$value[address]{$tco_address2}
$value[address2]{$tco_city}
$value[city]{$tco_state}
$value[state]{$tco_zip}
$value[zip]</td>";

				if ($vosl_vars['location_table_view']!="Normal") {
					print "<td>$value[description]</td>
<td>$value[url]</td>
<td>$value[phone]</td>
<td>$value[fax]</td>
<td>$value[email]</td>
<td>$value[image]</td>";
				}
			
				
				print "<td title='(".$value['latitude'].", ".$value['longitude'].")' style='cursor:help;'>(".round($value['latitude'],2).", ".round($value['longitude'],2).")</td></tr>";
			}
		}
	} else {
		$cleared=(!empty($_GET['q']))? str_replace("q=".str_replace(" ", "+", $_GET['q']) , "", $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'] ;
		$notice=(!empty($_GET['q']))? __("No Locations Showing for this Search of ", VOSL_TEXT_DOMAIN)."<b>\"$_GET[q]\"</b> | <a href='$cleared'>".__("Clear&nbsp;Results", VOSL_TEXT_DOMAIN)."</a> $view_link" : __("No Locations Currently in Database", VOSL_TEXT_DOMAIN);
		print "<tr><td colspan='5'>$notice | <a href='".VOSL_ADD_LOCATIONS_PAGE."'>".__("Add Listing", VOSL_TEXT_DOMAIN)."</a></td></tr>";
	}
	print "</table>
	<br />";
	if(empty($_GET['edit']))
	{
		//echo VOSL_ADD_LOCATIONS_PAGE;
		//die;
		print "<input type='button' value='".__("Add Listing", VOSL_TEXT_DOMAIN)."' class='button-primary' onclick=\"location.href='".VOSL_ADD_LOCATIONS_PAGE."'\">";
		print "&nbsp;&nbsp;<input type='button' value='".__("Delete Selected Listings", VOSL_TEXT_DOMAIN)."' class='button-primary' onclick=\"deleteAllListings();\">";
		
	}
	print "<input name='act' type='hidden'><br>";
	wp_nonce_field("manage-locations_bulk");

/*if ($numMembers2!=0) {include(VOSL_INCLUDES_PATH."/search-links.php");}*/

print "</form>"; 
?>
</div>
<script type="text/javascript">
function deleteAllListings()
{
	var atLeastOneIsChecked = false;
	jQuery('.checkbox1').each(function() { //loop through each checkbox
		if(jQuery(this).is(':checked'))
			atLeastOneIsChecked = true;  //select all checkboxes with class "checkbox1"              
	});
	
	if(atLeastOneIsChecked)
	{
		if(confirm("Do you want to delete selected listings?"))
		{
			var valuesArray = jQuery('.checkbox1:checked').map(function () {  
			return this.value;
			}).get().join(",");
			
			jQuery("#loc_ids").val(valuesArray);
			jQuery("#dele").val(1);
			jQuery("#locationForm").submit();
			
			//location.href = "admin.php?page=vo-locator/vosl-admin/pages/locations.php&del=1&ids="+valuesArray;
		}
		
	}else
	{
		alert("Please select atleast one listing");
	}
}

jQuery(document).ready(function() {
    jQuery('#master_checkbox').click(function(event) {  //on click
        if(this.checked) { // check select status
            jQuery('.checkbox1').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"              
            });
        }else{
            jQuery('.checkbox1').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
        }
    });
   
});
</script>
<?php //include(VOSL_INCLUDES_PATH."/sl-footer.php"); ?>