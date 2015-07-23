var is_mobile = false;

jQuery(document).ready( function($) {
								 
	$("#place_address").keydown(function(event){
		if( event.keyCode == 13 ) {
		  event.preventDefault();
		  $("#btnFind").click();
		  return false;
		}
 	 });							 
								 
    if( $('#map_placeholder').css('display')=='none') {
        is_mobile = true;       
    }
	
	$("#btnFind").click( function() {
		
		$("#loadingIcon").show();
								  
		var data = {
			action: 'find_locations',
            address: jQuery("#place_address").val()
		};
		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	$.post(the_ajax_script.ajaxurl, data, function(response) {
			//alert(response);
			
			if(response!='')
			{
				if(response!='NO')
				{
					var listItems = $("#maplist .col-lg-3 .locationlist");
					listItems.each(function(idx, li) {		
					
					   	var location_id = jQuery(this).attr('id');
						location_id = location_id.split("_"); 
						location_id = location_id[1];
						
						jQuery("#map_placeholder").gmap3({
							clear: {
							  id: "et_marker_"+location_id
							}
						  });
						  
						jQuery("#et_marker_"+location_id).parent().remove(); 
										
					});	
					 /*jQuery('#map_placeholder').gmap3({
						action: 'destroy'
					});
					 
					initializeLocationMap();*/
					jQuery("#maplist .col-lg-3 .locationlist").remove();
					jQuery( "#load_more_list" ).before( response );
					jQuery( "#load_more_list" ).show();
					//jQuery("#maplist .col-lg-3").html(response);
					
					listItems = $("#maplist .col-lg-3 .locationlist");
					listItems.each(function(idx, li) {
					   	var location_id = jQuery(this).attr('id');
						location_id = location_id.split("_"); 
						location_id = location_id[1];
						
						et_add_marker(location_id,parseFloat(jQuery("#maplist #location_"+location_id+" .lat").html()),parseFloat(jQuery("#maplist #location_"+location_id+" .long").html()),'<div id="et_marker_'+location_id+'" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">'+jQuery("#maplist #location_"+location_id+" .callout").html()+'</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div>');
										
					});	
						
				}else
				{
					jQuery( "#load_more_list" ).hide();
					jQuery("#maplist .col-lg-3 .locationlist").remove();
					//jQuery("#maplist .col-lg-3").html("Results not found.");
				}
			}else
			{
				jQuery( "#load_more_list" ).hide();
			}
			
			$("#loadingIcon").hide();
	 	});
	 	return false;
	});
	
	jQuery( "#load_more_list" ).live( "click", function()
   	{
		$("#loadingIcon").show();	
		var lastOffset = jQuery("#maplist .col-lg-3 .locationlist").last();
		lastOffset = lastOffset.attr('data-offset');
		
		var data1 = {
			action: 'load_more_locations',
            address: jQuery("#place_address").val(),
			offset: lastOffset
		};
		
		$.post(the_ajax_script.ajaxurl, data1, function(response) {
			//alert(response);
			if(response!='')
			{
				var listItems = $("#maplist .col-lg-3 .locationlist");
				jQuery( "#load_more_list" ).before( response );
				
				listItems = $("#maplist .col-lg-3 .locationlist");
				listItems.each(function(idx, li) {
					var currentLoopOffset = jQuery(this).attr('data-offset');											
					var location_id = jQuery(this).attr('id');
					location_id = location_id.split("_"); 
					location_id = location_id[1];
					
					if(currentLoopOffset > lastOffset)
					{
						et_add_marker(location_id,parseFloat(jQuery("#maplist #location_"+location_id+" .lat").html()),parseFloat(jQuery("#maplist #location_"+location_id+" .long").html()),'<div id="et_marker_'+location_id+'" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">'+jQuery("#maplist #location_"+location_id+" .callout").html()+'</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div>');
					}
									
				});
				
			}else
			{
				jQuery("#load_more_list").hide();
			}
			
			$("#loadingIcon").hide();
	 	});
	 	return false;
	});
	
	jQuery( "#maplist .col-lg-3 .locationlist" ).live( "click", function()
   	{
		jQuery("#maplist .col-lg-3 .locationlist").removeClass("active");	
		var location_id = jQuery(this).attr('id');
		location_id = location_id.split("_"); 
		location_id = location_id[1];
			
		if(jQuery("#sl_location_map").val()==0)
		{
			//jQuery(".locationdetails").hide();
			if(jQuery("#location_"+location_id+" .locationdetails").is(":hidden"))
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeIn("slow");
				
			}else
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeOut("slow");
			}
			
			
			
			
		}else if(jQuery("#sl_location_map").val()==1 && is_mobile==false)
		{
			var lastMarker = $("#map_placeholder").gmap3({
				get: {
				  id: "et_marker_"+location_id
				}
			  });
			
			/*jQuery( '.et_marker_info' ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 500, function() {
				jQuery(this).css( { 'display' : 'none' } );
				
				var marker_id = 'et_marker_' + location_id;
				jQuery("#map_placeholder").gmap3("get").panToWithOffset(lastMarker.position, 0, -100);
				jQuery( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 500 );
				
				
				
			} );*/
			
			jQuery( '.et_marker_info' ).hide();
			var marker_id = 'et_marker_' + location_id;
			jQuery("#map_placeholder").gmap3("get").panToWithOffset(lastMarker.position, 0, -100);
			jQuery( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 500 );
			
		}else if(jQuery("#sl_location_map").val()==1 && is_mobile==true)
		{
			// if show map is enbaled but users view on mobile device then only show details after click
			//jQuery(".locationdetails").hide();
			//jQuery("#location_"+location_id+" .locationdetails").fadeIn("slow");
			
			if(jQuery("#location_"+location_id+" .locationdetails").is(":hidden"))
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeIn("slow");
				
			}else
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeOut("slow");
			}
		}
		
		jQuery("#maplist .col-lg-3 #location_"+location_id).addClass("active");
		
   	});
});
	
function closeMarker(order)
{
	var marker_id = 'et_marker_' + order;
	jQuery( '#' + marker_id ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 500, function() {
								jQuery(this).css( { 'display' : 'none' } );
							} );
}

function initializeLocationMap()
{
	jQuery('#map_placeholder').gmap3({
	 map:{
		options:{
		 center:[22.49156846196823, 89.75802349999992],
		 zoom:2,
		 mapTypeId: google.maps.MapTypeId.ROADMAP,
		 mapTypeControl: true,
		 mapTypeControlOptions: {
		   style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		 },
		 navigationControl: true,
		 scrollwheel: true,
		 streetViewControl: true
		}
	 }
	});
	
	//et_main_map = jQuery("#map_placeholder").gmap3("get");
}

function et_add_marker( marker_order, marker_lat, marker_lng, marker_description ){
			var marker_id = 'et_marker_' + marker_order;
			
			jQuery("#map_placeholder").gmap3({
				marker : {
					id : marker_id,
					latLng : [marker_lat, marker_lng],
					options: {
						//icon : "<?php echo get_template_directory_uri(); ?>/images/red-marker.png"
					},
					events : {
						click: function( marker ){
							if ( et_active_marker ){
								et_active_marker.setAnimation( null );
								//et_active_marker.setIcon( '<?php echo get_template_directory_uri(); ?>/images/red-marker.png' );
							}
							et_active_marker = marker;
							jQuery( '.et_marker_info' ).hide();
							//marker.setAnimation( google.maps.Animation.BOUNCE);
							//marker.setIcon( '<?php echo get_template_directory_uri(); ?>/images/blue-marker.png' );
							jQuery(this).gmap3("get").panToWithOffset( marker.position,0,-100 );
							jQuery( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 500 );
							
							jQuery( '#' + marker_id ).css("bottom","15px");
							//jQuery( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { opacity : 1 }, 500 );
							
						},
						mouseover: function( marker ){
							//jQuery( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 500 );
						},
						mouseout: function( marker ){
							/*jQuery( '#' + marker_id ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 500, function() {
								jQuery(this).css( { 'display' : 'none' } );
							} );*/
						}
					}
				},
				overlay : {
					latLng : [marker_lat, marker_lng],
					options : {
						content : marker_description,
						offset : {
							y:-42,
							x:-203
						}
					}
				}
			});
		}
		
function showDrivingDirections(address)
{
	if(confirm("Would you like driving directions from your current location?"))
	{
		window.open(address);
		
	}else
	{
		address += "&saddr="+jQuery( '#place_address' ).val();
		window.open(address);
	}
}