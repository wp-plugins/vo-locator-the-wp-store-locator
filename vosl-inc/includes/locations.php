<?php
ob_start();
$location_value = vosl_data('sl_location_map');
$current_location_lookup = vosl_data('sl_current_location_lookup');
$find_location_text = vosl_data('sl_find_location_text');

if($find_location_text=='')
	$find_location_text = 'Find a Location';

if($current_location_lookup=='')
	$current_location_lookup = 0;
	
if($location_value=='')
	$location_value = 1;	

if($current_location_lookup==0)
{
	$sql = $wpdb->prepare("SELECT * FROM ".VOSL_TABLE." order by store_name ASC limit 0, %d", VOSL_LOCATIONS_PAGESIZE);
	$result_row = $wpdb->get_results($sql,ARRAY_A);
}	

$bg_highlight_color = vosl_data('sl_highlight_color');
$bg_highlight_text_color = vosl_data('sl_highlight_text_color');
$bg_listing_color = vosl_data('sl_listing_bg_color');

if($bg_listing_color=='')
	$bg_listing_color = '#FFFFFF';
	
if($bg_highlight_text_color=='')
	$bg_highlight_text_color = '#000000';
	
if($bg_highlight_color=='')
	$bg_highlight_color = '#3DA1D9';		

?>
<link href="<?=$vosl_base?>/css/bootstrap.min.css" rel="stylesheet">
<link href="<?=$vosl_base?>/css/custom.css" rel="stylesheet">
<script src="<?=$vosl_base?>/js/bootstrap.min.js"></script>
<script src="<?=$vosl_base?>/js/gmap3.min.js"></script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&libraries=places"></script>
<div id="zipcodelookup" class="row">
<h2><?=$find_location_text?>:</h2>
<form id="retailFinder" name="retailFinder" method="post">
<input type="hidden" value="<?=$location_value?>" id="sl_location_map" />
<div class="row">
<div class="col-lg-4">
<div class="input-group">
<input type="text" class="form-control" name="place_address" id="place_address" placeholder="Enter address or zipcode">
<span class="input-group-btn">
<button class="btn btn-default" type="button" id="btnFind">Go!</button>
</span>
</div>
</div>
<div class="col-lg-8">
<img id="loadingIcon" src="<?=$vosl_base?>/images/loading.gif" />
</div>
</div>
</form>
<div style="clear:both"></div>
</div>
<div class="row" id="maplist">
<div class="col-lg-3<?php if($location_value==1){ ?> overflowscroll<?php } ?>">
<?php if($current_location_lookup==0){ ?>
<?php $cc = 0; foreach($result_row as $row){ 
	
	$address = '';
	$address .= (!empty($row['address']))?$row['address']:'';
	$address .= (!empty($row['address2']))?", ".$row['address2']:'';
	$address .= (!empty($row['city']))?", ".$row['city']:'';
	$address .= (!empty($row['state']))?", ".$row['state']:'';
	$address .= (!empty($row['zip']))?" ".$row['zip']:'';
	
	$add = str_replace("","%20", $address);
	//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;
	$addr_link_src = "http://maps.google.com/maps?daddr=".$add;	
	
?>
<div class="row locationlist" id="location_<?=$row['id']?>" data-offset="<?=$cc?>">
<h4><?=$row['store_name']?></h4>
<div class="locationdetails">
<div class="row">
<?php if($row['image']!=''){ ?>
<div class="col-md-2 col-sm-2">
<div class="row imagerow"><div class="img_placeholder"><img src="<?=$row['image']?>" class="img-responsive" /></div></div>
</div>
<?php } ?>
<div class="col-md-10 col-sm-10"><div class="row mainrow">
<?php if($row['description']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src="<?=$vosl_base?>/images/icons/description.png" /></strong></div><div class="col-md-10 col-sm-10"><?=$row['description']?></div></div>
<?php } ?>
<?php if($row['show_address_publicly']==1){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src="<?=$vosl_base?>/images/icons/address.png" /></strong></div><div class="col-md-10 col-sm-10"><a href='#' onclick="showDrivingDirections('<?=$addr_link_src?>');return false"><?=$address?></a></div></div>
<?php } ?>
<?php if($row['url']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src="<?=$vosl_base?>/images/icons/URL.png" /></strong></div><div class="col-md-10 col-sm-10"><?=$row['url']?></div></div>
<?php } ?>
<?php if($row['phone']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src="<?=$vosl_base?>/images/icons/phone.png" /></strong></div><div class="col-md-10 col-sm-10"><?=$row['phone']?></div></div>
<?php } ?>
<?php if($row['fax']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src="<?=$vosl_base?>/images/icons/fax.png" /></strong></div><div class="col-md-10 col-sm-10"><?=$row['fax']?></div></div>
<?php } ?>
<?php if($row['email']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src="<?=$vosl_base?>/images/icons/email.png" /></strong></div><div class="col-md-10 col-sm-10"><a href="mailto:<?=$row['email']?>"><?=$row['email']?></a></div></div>
<?php } ?>
<?php if($row['hours']!=''){ ?>
<div class="row"><div class="col-md-1 col-sm-1"><strong><img src="<?=$vosl_base?>/images/icons/hours.png" /></strong></div><div class="col-md-10 col-sm-10"><?=$row['hours']?></div></div>
<?php } ?>
</div></div>
</div>
</div>
</div>
<?php $cc = $cc + 1; } ?>
<?php } ?>
<div id="load_more_list">Load More</div>
</div>
<div class="col-lg-9">
<?php if($location_value==1){ ?>
<script type="text/javascript">/*<![CDATA[*/google.maps.Map.prototype.panToWithOffset=function(e,a,d){var c=this;var b=new google.maps.OverlayView();b.onAdd=function(){var f=this.getProjection();var g=f.fromLatLngToContainerPixel(e);g.x=g.x+a;g.y=g.y+d;c.panTo(f.fromContainerPixelToLatLng(g))};b.draw=function(){};b.setMap(this)};jQuery(document).ready(function(e){var c;et_active_marker=null;initializeLocationMap();<?php if($current_location_lookup==0){ ?><?php foreach($result_row as $row){ 
		if($row['latitude']!='' and $row['longitude']!='')
		{
		
		$address = '';
		$address .= (!empty($row['address']))?$row['address']:'';
		$address .= (!empty($row['address2']))?", ".$row['address2']:'';
		$address .= (!empty($row['city']))?", ".$row['city']:'';
		$address .= (!empty($row['state']))?", ".$row['state']:'';
		$address .= (!empty($row['zip']))?" ".$row['zip']:'';
		$add = str_replace("","%20", $address);
		//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;	
		$addr_link_src = "http://maps.google.com/maps?daddr=".$add;
	
		$callout = "<h4 style=\'width:90%; float:left;\'>".$row['store_name']."</h4><h4><a href=\'#\' style=\'float:right;\' onclick=\'closeMarker(".$row['id']."); return false;\'>X</a></h4>";
		
		if($row['show_address_publicly']==1)
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/address.png\' /> <a href=\'#\' onclick=\"showDrivingDirections(\'".$addr_link_src."\'); return false;\">".$address."</a></p>";
		
		if($row['url']!='')
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/URL.png\' /> ".$row['url']."</p>";
		
		if($row['phone']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/phone.png\' /> ".$row['phone']."</p>";
		
		if($row['fax']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/fax.png\' /> ".$row['fax']."</p>";
		
		if($row['email']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/email.png\' /> <a href=\'mailto:".$row['email']."\'>".$row['email']."</a></p>";
		
		if($row['hours']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/hours.png\' /> ".$row['hours']."</p>";
			
		if($row['description']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/description.png\' /> ".$row['description']."</p>";	
			
		if($row['image']!='')
			$image = $row['image'];
		else
			$image = $vosl_base."/images/locationimg.jpg";
		
		$htm = '<div class="row"><div class="col-lg-5"><div class="img_placeholder"><img src="'.$image.'" class="img-responsive" /></div></div><div class="col-lg-7">'.$callout.'</div></div>';	
		
		
		?>et_add_marker(<?php printf( '"%1$s", %2$s, %3$s, \'<div id="et_marker_%1$s" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">%4$s</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->\'',
				$row['id'],
				$row['latitude'],
				$row['longitude'],
				$htm
			); ?>);<?php } } ?><?php } ?>var j=jQuery("#maplist").width();var h=jQuery("#maplist .col-lg-3").width();var f=j-h-50;jQuery("#map_placeholder").css("width",f+"px")});/*]]>*/</script>
<div id="map_placeholder"></div>
<?php }else{ ?>
<style type="text/css">#maplist .col-lg-3{width:100%}</style>
<div id="details_placeholder">
</div>
<?php } ?>
<?php if($current_location_lookup==1){ ?>
<script type="text/javascript">/*<![CDATA[*/var timedOut=false;function codeLatLng(lat,long){geocoder=new google.maps.Geocoder();var lat=parseFloat(lat);var lng=parseFloat(long);var latlng=new google.maps.LatLng(lat,lng);geocoder.geocode({'latLng':latlng},function(results,status){if(status==google.maps.GeocoderStatus.OK){if(results[1]){jQuery("#place_address").val(results[1].formatted_address);}else{alert('No results found');}}else{alert('Geocoder failed due to: '+status);}});}
jQuery(document).ready(function($){timedOut=false;timerId=setTimeout(function(){timedOut=true;$("#btnFind").click();$("#loadingIcon").hide();},10000);$("#loadingIcon").show();if(navigator.geolocation){browserSupportFlag=true;navigator.geolocation.getCurrentPosition(function(position){if(timedOut==false){clearTimeout(timerId);var data={action:'find_locations',address:"",lat:position.coords.latitude,long:position.coords.longitude};codeLatLng(position.coords.latitude,position.coords.longitude);$.post(the_ajax_script.ajaxurl,data,function(response){if(response!='')
{if(response!='NO')
{var listItems=$("#maplist .col-lg-3 .locationlist");listItems.each(function(idx,li){var location_id=jQuery(this).attr('id');location_id=location_id.split("_");location_id=location_id[1];jQuery("#map_placeholder").gmap3({clear:{id:"et_marker_"+location_id}});jQuery("#et_marker_"+location_id).parent().remove();});jQuery("#maplist .col-lg-3 .locationlist").remove();jQuery("#load_more_list").before(response);jQuery("#load_more_list").show();listItems=$("#maplist .col-lg-3 .locationlist");listItems.each(function(idx,li){var location_id=jQuery(this).attr('id');location_id=location_id.split("_");location_id=location_id[1];et_add_marker(location_id,parseFloat(jQuery("#maplist #location_"+location_id+" .lat").html()),parseFloat(jQuery("#maplist #location_"+location_id+" .long").html()),'<div id="et_marker_'+location_id+'" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">'+jQuery("#maplist #location_"+location_id+" .callout").html()+'</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div>');});}else
{jQuery("#load_more_list").hide();jQuery("#maplist .col-lg-3 .locationlist").remove();}}else
{jQuery("#load_more_list").hide();}
$("#loadingIcon").hide();});return false;$("#loadingIcon").hide();}},function(){handleNoGeolocation(browserSupportFlag);});}
else{browserSupportFlag=false;handleNoGeolocation(browserSupportFlag);}
function handleNoGeolocation(errorFlag){clearTimeout(timerId);$("#loadingIcon").hide();$("#btnFind").click();if(errorFlag==true){}else{}}});/*]]>*/</script>
<?php } ?>
<style type="text/css">/*<![CDATA[*/#maplist .col-lg-3,#maplist .col-lg-3 a{color:<?=$bg_highlight_text_color?>!important}#maplist .col-lg-3 .row:hover{background:<?=$bg_highlight_color?>!important}#maplist .col-lg-3 .locationlist.active{background:<?=$bg_highlight_color?>!important}#maplist .col-lg-3 .locationlist strong{color:<?=$bg_highlight_text_color?>!important}#maplist .col-lg-3{background:<?=$bg_listing_color?>!important}/*]]>*/</style>
</div>
</div>
<script type="text/javascript">/*<![CDATA[*/var autocomplete;jQuery(document).ready(function(){autocomplete=new google.maps.places.Autocomplete((document.getElementById('place_address')),{types:['geocode']});});function fillInAddress(){var place=autocomplete.getPlace();for(var component in componentForm){document.getElementById(component).value='';document.getElementById(component).disabled=false;}
for(var i=0;i<place.address_components.length;i++){var addressType=place.address_components[i].types[0];if(componentForm[addressType]){var val=place.address_components[i][componentForm[addressType]];document.getElementById(addressType).value=val;}}}/*]]>*/</script>
<?php
$form = ob_get_clean();
?>