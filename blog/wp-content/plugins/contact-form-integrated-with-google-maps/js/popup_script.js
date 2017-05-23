jQuery(document).ready(function() {
    var screen_height = window.screen.height;
    var screen_width = window.screen.width;
    var mid_height = screen_height / 2;
    var canvaswidth = jQuery(window).width();
    var canvasheight = jQuery(document).height();
    var display_positon_left = screen_width - 800;
    var display_positon_top = 13 * screen_height;
    jQuery("#fggm_close_box").click(function() {
        jQuery('div#fggmgm-box-popup').css({
            "display": "none"
        });
    });
    jQuery('div.fggmgm_contac_us_img').css({
        "display": "block",
        "top": "0",
        "margin-top": mid_height - 100,
        "margin-left": canvaswidth - 35,
		"z-index": "200000",
    });
    jQuery('div.fggmgm_contac_us_img').click(function() {
	      jQuery('div#fggmgm-box-popup').css({
            "height": canvasheight,
            "width": canvaswidth,
            "opacity": "1.95",
            "display": "block",
            "color": "white",
            "z-index": "200000",
            "position": "fixed",
            "top": "0"
        });
        var lat = jQuery("div.gm_lat").text();
        var lng = jQuery("div.gm_lon").text();
      var zoom = parseInt(jQuery("div.gm_zoom").text());
    var maptype = jQuery("div.gm_type").text();
    var pattern = /ROAD/;
var exists = pattern.test(maptype);
var prototype = /SATELLITE/;
var occur = prototype.test(maptype);
var model = /HYBRID/;
var present = model.test(maptype);
var formatt = /TERRAIN/;
var obtain = formatt.test(maptype);
if(exists){
   var myCenter = new google.maps.LatLng(lat, lng);
        var marker;
        var mapProp = {
            'center': myCenter,
            'zoom': zoom,
            'mapTypeId': google.maps.MapTypeId.ROAD,
            'panControl': true,
            'scaleControl': true,
            'zoomControl': true,
            'mapTypeControl': true,
            'scrollWheel': true
        };
        var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        marker = new google.maps.Marker({
            position: myCenter,
            animation: google.maps.Animation.BOUNCE
        });
        marker.setMap(map);
}
if(occur){
 var myCenter = new google.maps.LatLng(lat, lng);
        var marker;
        var mapProp = {
            'center': myCenter,
            'zoom': zoom,
            'mapTypeId': google.maps.MapTypeId.SATELLITE,
            'panControl': true,
            'scaleControl': true,
            'zoomControl': true,
            'mapTypeControl': true,
            'scrollWheel': true
        };
        var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        marker = new google.maps.Marker({
            position: myCenter,
            animation: google.maps.Animation.BOUNCE
        });
        marker.setMap(map);    
}    
 if(present){
   var myCenter = new google.maps.LatLng(lat, lng);
        var marker;
        var mapProp = {
            'center': myCenter,
            'zoom': zoom,
            'mapTypeId': google.maps.MapTypeId.HYBRID,
            'panControl': true,
            'scaleControl': true,
            'zoomControl': true,
            'mapTypeControl': true,
            'scrollWheel': true
        };
        var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        marker = new google.maps.Marker({
            position: myCenter,
            animation: google.maps.Animation.BOUNCE
        });
        marker.setMap(map);   
 }   
 if(obtain){
   var myCenter = new google.maps.LatLng(lat, lng);
        var marker;
        var mapProp = {
            'center': myCenter,
            'zoom': zoom,
            'mapTypeId': google.maps.MapTypeId.TERRAIN,
            'panControl': true,
            'scaleControl': true,
            'zoomControl': true,
            'mapTypeControl': true,
            'scrollWheel': true
        };
        var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        marker = new google.maps.Marker({
            position: myCenter,
            animation: google.maps.Animation.BOUNCE
        });
        marker.setMap(map);  
 }
    });
    jQuery('div#fggmgm_form_show').css({
        "margin-top": display_positon_top / 100,
        "margin-left": display_positon_left / 2
    });
});	   