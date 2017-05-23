jQuery(document).ready(function() {
	var id="target_tabbed";
	var path =jQuery('#target_tabbed').attr("data-path");
	ZeroClipboard.setDefaults({moviePath: path});   
	var clip = new ZeroClipboard(jQuery('#' + id));		
    jQuery('.fggm_group').hide();
    jQuery('.fggm_group:first').fadeIn();
    jQuery('#fggm_of-nav li:first').addClass('current');
    jQuery('#fggm_of-nav li a').click(function(evt) {
        jQuery('#fggm_of-nav li').removeClass('current');
        jQuery(this).parent().addClass('current');
        if (jQuery(this).attr("title") == "Embed Code") {
            jQuery(".fggm_embed_code_save").css("display", "block");
            jQuery(".fggm_embed_code_save").css("float", "left");
            jQuery(".fggm_embed_code_save").css("width", "120px");
            jQuery(".fggm_embed_code_save").css("height", "40px");
        }
        else {
            jQuery(".fggm_embed_code_save").css("display", "none");
        }
        var clicked_group = jQuery(this).attr('href');
        jQuery('.fggm_group').hide();
        jQuery(clicked_group).fadeIn();
        evt.preventDefault();
    });
    jQuery('.fggm_embed_code_save').click(function() {        
        var text_value = jQuery('textarea#fggm_content_html').val();
        var bg_color = jQuery('input.bg_color').val();
		//alert(bg_color);
       var googlemap_code = jQuery('textarea.googlemap_iframe').val();
        var latitude_map = jQuery('input.latitude_value').val();
	    var longitude_map = jQuery('input.longitude_value').val();
		var tab_bgcolor = jQuery('input.fggm_button_color').val();
		var tab_text = jQuery('input.fggm_button_text_display').val();
		var location_address = jQuery('textarea.address').val();
        var zoom_level = jQuery('input.zoom_level').val();
        var map_type_id = jQuery('#map_type option:selected').val();
        if (text_value == "") {
            alert("Please insert iframe code");
            jQuery('div#loader_img').css("display", "none");
        }
        else {
            var pattern = /iframe/;
            var exists = pattern.test(text_value);
            if (exists) {
				jQuery('div#loader_img').css("display", "block");
                var data = {
                    action: 'master_response',
                    value: text_value,
                    color: bg_color,
					tab_bg_color: tab_bgcolor,
					tab_bg_text: tab_text,
                    iframe: googlemap_code,
                    latitude: latitude_map,
                    longitude: longitude_map,
                    address: location_address,
                    zoom: zoom_level,
                    map_type:map_type_id 
                };
				jQuery.post(script_call.ajaxurl, data, function(response) {
                    if (response) {
                        //alert(response);
                        jQuery('div#loader_img').css("display", "none");
                    }
                    else {
                        //alert("error");
                        jQuery('div#loader_img').css("display", "none");
                    }
                });
            }
            if (!exists) {
                alert("Please paste the correct iframe code");
                jQuery('div#loader_img').css("display", "none");
            }
        }
    });	
	 function initialize(){
        var address = (document.getElementById('my-address'));
        var autocomplete = new google.maps.places.Autocomplete(address);
        autocomplete.setTypes(['geocode']);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }
            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
});
function codeAddress() {
    geocoder = new google.maps.Geocoder();
    var address = document.getElementById("my-address").value;
    //alert(address);
    geocoder.geocode({'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var latitude = results[0].geometry.location.lat();
            var longitude = results[0].geometry.location.lng();
            document.getElementById("latitude_value").value = latitude;
            document.getElementById("longitude_value").value = longitude;
        }
        else {
            alert("Geocode was not successful for the following reason: " + status);
        }
    });
}