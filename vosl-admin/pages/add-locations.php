<?php
if (!empty($_GET['pg']) && isset($wpdb) && $_GET['pg']=='add-locations') { /*include_once(SL_INCLUDES_PATH."/top-nav.php"); */}

if (!defined("VOSL_INCLUDES_PATH")) { include("../vosl-define.php"); }

print "<div class='wrap'>";

global $wpdb;

//Inserting addresses by manual input
if (!empty($_POST['store_name']) && (empty($_GET['mode']) || $_GET['mode']!="pca")) {
	if (!empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], "add-location_single")){
		vosl_add_location();
		print "<div class='sl_admin_success'>".__("Successful Addition",VOSL_TEXT_DOMAIN).". $view_link</div> <!--meta http-equiv='refresh' content='0'-->"; 
	} else {
		print "<div class='sl_admin_warning'>".__("Unsucessful addition due to security check failure",VOSL_TEXT_DOMAIN).". $view_link</div>"; 
	}
}

print "
<table cellpadding='' cellspacing='0' style='width:100%' class='manual_add_table'><tr>
<td style='/*border-right:solid silver 1px;*/ padding-top:0px; width:50%' valign='top'>".vosl_location_form("add")."</td>
<td style='/*border-right:solid silver 1px;*/ padding-top:0px;' valign='top'>";
print "</td>
</tr>
</table>
</div>";

//include(VOSL_INCLUDES_PATH."/sl-footer.php");
?>