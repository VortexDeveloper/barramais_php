<?php
/*
  Plugin Name:  Contact Form Integrated With Google Maps
  Plugin URI: http://www.formget.com
  Description:  Contact Form Integrated With Google Maps allows to colligate geolocation information along with contact form in simple way.
  Version: 2.7
  Author: FormGet
  Author URI: http://www.formget.com
 */
 function fggm_script() {
	wp_deregister_script('popup_script');
	wp_register_script('popup_script', plugins_url('js/popup_script.js', __FILE__), array('jquery'));
    wp_enqueue_script('popup_script'); 
}
add_action( 'wp_enqueue_scripts', 'fggm_script' );
function fggm_admin_notice() {
    $fggm_iframe_form = get_option('fggm_iframe_embed_code');
    $string = "iframe";
    $pos = strpos($fggm_iframe_form, $string);
    if ($pos == false) {
        global $current_user;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
        if (!get_user_meta($user_id, 'admin_ignore_notice')) {
            ?>
			<div class="fg_trial-notify updated below-h2">
                <p class="fg_division_note">
                    Welcome to FormGet Integrated With Google Maps - You're almost ready to create your form <a class="fg_button_prime click_notice" href='<?php echo admin_url('admin.php?page=fggm_page'); ?>'>Click to Create.</a><?php printf(__('<a class="fg_hide_notice fg_button_prime", href="%1$s">Hide Notice</a>'), '?admin_nag_ignore=0'); ?>
                </p>
            </div>
            <?php
        }
    }
}
if (isset($_GET['page']) && $_GET['page'] != 'fggm_page'){
add_action('admin_notices', 'fggm_admin_notice');
}
	function value_option_table(){
	add_option('googleMap_latitude_position', '23.2500');
    add_option('googleMap_longitude_position','77.4167');
	add_option('map_zoom_value', '11');		
	}
register_activation_hook( __FILE__, 'value_option_table' );
function fggm_admin_nag_ignore() {
    global $current_user;
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if (isset($_GET['admin_nag_ignore']) && '0' == $_GET['admin_nag_ignore']) {
        add_user_meta($user_id, 'admin_ignore_notice', 'true', true);
    }
}
add_action('admin_init', 'fggm_admin_nag_ignore');
function fggm_delete_user_entry() {
    global $current_user;
    $user_id = $current_user->ID;
    delete_user_meta($user_id, 'admin_ignore_notice', 'true', true);
}
register_deactivation_hook(__FILE__, 'fggm_delete_user_entry');
function fggm_add_style() {
	wp_deregister_style('fggm_option_style');
	wp_register_style('fggm_option_style', plugins_url('css/fggmstyle.css', __FILE__));
    wp_enqueue_style('fggm_option_style');
}
if (isset($_GET['page']) && $_GET['page'] == 'fggm_page') {
   add_action("init", "fggm_add_style");
}
function fggm_popup_style() {
	wp_deregister_style('fggm_form_style');
	wp_register_style('fggm_form_style', plugins_url('css/formstyle.css', __FILE__));
    wp_enqueue_style('fggm_form_style');
    }
    add_action("init", "fggm_popup_style");
//setting page
add_action('admin_menu', 'fggmgm_menu_page');
function fggmgm_menu_page() {
    add_menu_page('fggm', 'Contact Form', 'manage_options', 'fggm_page', 'fggmgm_setting_page', plugins_url('image/favicon.ico', __FILE__), 119);
}
function fggmgm_setting_page() {
    $url = plugins_url();
    ?>	
		
	<div id="fggm_of_container" class="fggm_wrap">
        <form id="fggm_ofform" action="" method="POST">
            <div id="fggm_header">
                <div class="fggm_logo">
                    <h2>Contact Form Integrated With Google Maps</h2>                   
                </div>
                <a target="#">
                    <div class="fggm_icon-option"> </div>
                </a>
                <div class="clear"></div>
            </div>
            <div id="fggm_main">

                <div id="fggm_of-nav">
                    <ul>
                        <li> <a class="pn-view-a" href="#pn_content" title="Form Builder">Contact Form Builder </a></li>
                        <li> <a class="pn-view-a" href="#pn_displaysetting" title="Embed Code">Settings</a></li>
                        <li> <a class="pn-view-a" href="#pn_template" title="Help">Help</a></li>
                        <li> <a class="pn-view-a" href="#pn_gopro" title="Go Pro">Extensions</a></li>
						<li> <a class="pn-view-a" href="#pn_login" title="Login">Login</a></li>						
                        <li> <a class="pn-view-a" href="#pn_contactus" title="Help">Plugin Support</a></li>
                    </ul>
                </div>
                <div id="fggm_content">
                    <div class="fggm_group" id="pn_content">
                        <h2>Contact Form Builder</h2>
                        <div class="fggm_section section-text">
                            <h3 class="fggm_heading"> Create your custom form by just clicking the fields on left side of the panel. Visibility Problem Click here - <a href="http://www.formget.com/app" target="_blank">Building Contact Form</a></h3>
                            <div class="outer_div_builder_iframe">
							<div class="inner_div_builder_iframe">
                            <iframe src="https://www.formget.com/app" name="iframe" id="iframebox" style="width:100%; height:800px; border:1px solid #dfdfdf; align:center;">
                            </iframe>
							</div>
							</div>
                        </div>
                    </div>	
                    <div class="fggm_group" id="pn_displaysetting">
                        <h2>Settings</h2>
                        <div class="fggm_section section-text">
                            <h3 class="fggm_heading">Paste here Iframe code for Contact Form. For more information click on Help tab.</h3>
							<div class="note_for_embed" style="width:500px; color:#b22b38;"> Recommended: Please paste Iframe code of Embed Form 2(Plain Form). </div>
                            <div class="option">
                                <div class="fggm_controls">
                                    <textarea name="content[html]" cols="60" rows="10"   class="regular-text"  id="fggm_content_html" style="width:500px"><?php embeded_code(); ?></textarea>  
                                      <div class="color_selector">
                                        <h3 class="fg_heading" style="width:480px;">Paste or Select the background color that will complement your form</h3>
                                        <?php
                                        global $wpdb;
                                        $bg_color_value = get_option('fggm_form_bgcolor');
										$GoogleMap_iframe = get_option('googleMap_iframe_code');
                                        $address_written = get_option('location_address');
                                        ?>
                                        <input class="bg_color color {hash:true,caps:false}" value="<?php echo $bg_color_value; ?>" style="width:250px;" >
                                    </div>
									 
									 <div class="fggm_google_map">
                                        <div class="address_text">
                                            <h3 class="fggm_heading">Write here Your Address </h3>
                                            <textarea name="content[html]" cols="60" rows="10"   class="address"  id="address" style="width:500px; text-align: start; margin: 0px;"> <?php echo $address_written; ?></textarea>     
                                        </div>
                                        <div class="google_map_iframe" style="width:700px;">
                                            <h3 class="fggm_heading">Paste here Iframe code for google map </h3><p> <b style="font-weight: bold;">Tip:</b> Set the height of Google Map. Find height in the Iframe code and adjust it.</p>
                                            <textarea name="content[html]" cols="60" rows="10"   class="googlemap_iframe"  id="googlemap_iframe" style="width:500px;"> <?php echo stripslashes($GoogleMap_iframe); ?> </textarea>											
                                        </div>
                                        <div class="lati_logi" >
                                            <h3 class="fggm_heading" style="width:450px;">OR You can write your address in given input field and get the latitude and longitude OR you can also write latitude and longitude directly in respective fields</h3>
                                            <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
                                            <div class="get_lat_lng">                              
                                                <input type="text" id="my-address" style="width:300px;"> 
                                                <div id="getCords" onClick="codeAddress();">Get Lat & Lng</div>
                                            </div>
                                            <label style="margin-right: 25px;"><b>Latitude </b>  </label><input type="text" class="latitude_value" id="latitude_value" name="latitude" value="<?php echo $GoogleMap_latitude = get_option('googleMap_latitude_position'); ?>" style="width:150px;"><br />
                                            <label style="margin-right: 15px;"><b>Longitude </b>  </label><input type="text" class="longitude_value" id="longitude_value" name="longituted" value="<?php echo $GoogleMap_longitude = get_option('googleMap_longitude_position'); ?>" style="width:150px;">
                                        </div>

                                        <label style="margin-right: 32.3px;"><b>Zoom : </b></label><input type="text" class="zoom_level" id="zoom_level" name="zoom" value="<?php echo  $GoogleMap_zoom = get_option('map_zoom_value');?>" style="width:150px;"><br />

                                        <label style="margin-right: 10.3px;"><b>Map Type : </b></label> <select name="map_type" id="map_type" style="width:250px;">
                                            <option value="ROAD">ROAD - Normal,default 2D map</option>
                                            <option value="SATELLITE">SATELLITE - Display satellite images </option>
                                            <option value="HYBRID">HYBRID - Photographic map  + road + city name</option>
                                            <option value="TERRAIN">TERRAIN - Display map with physical feature such as mountain, river ect</option>
                                        </select><br /><br />
                                    </div>									
									<div class="fggm_color_selector" >
								  <h3 class="fggm_heading" style="width:400px;">Select the contact us button color that will complement your form as well as your website. </h3>
								   <?php
                                        global $wpdb;
                                        $button_bg_color = get_option('fggm_tab_bg_color');
                                        ?>
								  <input class="fggm_button_color color {hash:true,caps:false}" style="width:300px;" value="<?php
                                        if ($button_bg_color == "") {
                                            echo "#a2b23d";
                                        } else {
                                            echo $button_bg_color;
                                        }
                                        ?>" >
								  </div>
								<div class="fggm_button_text">
								 <?php
                                        global $wpdb;
                                        $contact_button_value = get_option('fggm_tab_text');
                                        ?>
									<h3 class="fggm_heading" style="width:400px;">The text to be displayed in place of Contact Us. </h3>
									<input class="fggm_button_text_display" style="width:300px;" value="<?php
                                        if ($contact_button_value == "") {
                                            echo "Contact Us";
                                        } else {
                                            echo $contact_button_value;
                                        }
                                        ?>">
									</div>
									
									<div class="fggm_shortcode_menu" style="width:700px;">
                                    <h3 class="fggm_heading">If you want to display form on menu click then paste this shortcode in particular menu page.</h3><p> <b style="font-weight: bold;">Example:</b> In order to display popup form on "contact" menu click, then paste this shortcode in contact page,.</p>
									<div class="shortcode_wrap">
                                   <input class="fggm_button_text_display" id="code_shortcode" style="width:300px;"  value="<?php echo "[formget_menu_form]"; ?>" readonly>	
									<div id="target_tabbed" data-clipboard-target="code_shortcode" data-path="<?php echo plugins_url('js/ZeroClipboard.swf', __FILE__); ?>" style="text-align:center;">Copy</div>
									</div>
									</div>
									
									
                                    <input id="submit-form" class="fggm_embed_code_save" type="button" value="Save Changes" name="submit_form" style="display:none;">		
                                    <div id="loader_img" align="center" style="margin-left:460px; display:none;">
                                        <img src="<?php echo plugins_url('image/ajax-loader.gif', __FILE__); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fggm_group" id="pn_template">
                        <h2>Steps to use Contact Form Integrated With Google Maps Plugin</h2>

                        <div class="fggm_section section-text">
                            <h3></h3>
                            <div id="help_txt" style="width:900px;">
                                <img src="<?php echo plugins_url('image/help_img.png', __FILE__); ?>"><br /><br />
                                <div style="font-size:15px;"> <b> Thanks,</br>
                                        FormGet Team</i></b></div>
                            </div>
                        </div>
                    </div>     
					     <div class="fggm_group" id="pn_gopro">
                        <h2>FormGet Extensions</h2>
                        <div class="fggm_section section-text">
                            <h3></h3>
                            <iframe src="http://www.formget.com/app/pricing" name="iframe" style="width:1010px; height:900px; overflow-y:scroll;" >
                                    </iframe>
                        </div>
                    </div>
					
					 <div class="fggm_group" id="pn_login">
                        <h2>Get Login</h2>
                        <div class="fggm_section section-text">
                            <h3></h3>
                            <iframe src="http://www.formget.com/app/login" name="iframe" style="width:1010px; height:900px; overflow-y:scroll;" >
                                    </iframe>
                        </div>
                    </div>
					
					
                    <div class="fggm_group" id="pn_contactus">
                        <iframe height='570' allowTransparency='true' frameborder='0' scrolling='no' style='width:100%;border:none'  src='https://www.formget.com/app/embed/form/qQvs-639'>Your Contact </iframe>
                    </div>	
                </div>
                <div class="clear"></div>
            </div>
            <div class="fggm_save_bar_top">
            </div>
        </form>
    </div>
    <?php
}
if (isset($_GET['page']) && $_GET['page']== 'fggm_page') {
    add_action( 'admin_enqueue_scripts', 'fggm_embeded_script' );
}
function fggm_embeded_script() {
	wp_deregister_script( 'colorpicker_script' );
	wp_register_script('colorpicker_script', plugins_url('jscolor/jscolor.js', __FILE__), array('jquery'));
    wp_enqueue_script('colorpicker_script');
    wp_deregister_script('embeded_script');
	wp_register_script('embeded_script', plugins_url('js/fggm_script.js', __FILE__), array('jquery'));
    wp_enqueue_script('embeded_script');
    wp_localize_script('embeded_script', 'script_call', array('ajaxurl' => admin_url('admin-ajax.php')));
	
   wp_deregister_script('clip_script');
   wp_register_script('clip_script', plugins_url('js/ZeroClipboard.js', __FILE__), array('jquery'));
   wp_enqueue_script('clip_script');
}
function fggm_text_ajax_process_request() {
    $text_value = $_POST['value'];
    $form_background_color = $_POST['color'];
	$tab_bgcolour = $_POST['tab_bg_color'];
	$tab_text = $_POST['tab_bg_text'];
	$google_map_iframe_code = $_POST['iframe'];
    $map_latitude_position = $_POST['latitude'];
    $map_longitude_position = $_POST['longitude'];
    $location_address = $_POST['address'];
    $map_zoom_value = $_POST['zoom'];
    $map_type_id = $_POST['map_type'];
	update_option('fggm_iframe_embed_code', $text_value);
    update_option('fggm_form_bgcolor', $form_background_color);
	update_option('fggm_tab_bg_color', $tab_bgcolour);
	update_option('fggm_tab_text', $tab_text);
    update_option('googleMap_iframe_code', $google_map_iframe_code);
    update_option('googleMap_latitude_position', $map_latitude_position);
    update_option('googleMap_longitude_position', $map_longitude_position);
    update_option('location_address', $location_address);
    update_option('map_zoom_value', $map_zoom_value);
    update_option('map_type_id', $map_type_id);
    echo 1;
    die();
}
add_action('wp_ajax_master_response', 'fggm_text_ajax_process_request');
if (!function_exists('embeded_code')) {
    function embeded_code() {
        global $wpdb;
        $fggm_iframe_form = get_option('fggm_iframe_embed_code');
        $string = "iframe";
        $pos = strpos($fggm_iframe_form, $string);
        if ($pos == true) {
            echo stripslashes($fggm_iframe_form);
        }
    }
}
if (!function_exists('display_iframe_form')) {
    function display_iframe_form() {
        global $wpdb;
        $fggm_iframe_form = get_option('fggm_iframe_embed_code');
        $form_bg_color = get_option('fggm_form_bgcolor');
        $address_of_location = get_option('location_address');
        $GoogleMap_iframe = get_option('googleMap_iframe_code');
        $GoogleMap_latitude = get_option('googleMap_latitude_position');
        $GoogleMap_longitude = get_option('googleMap_longitude_position');
        $GoogleMap_zoom = get_option('map_zoom_value');
        $GoogleMap_type_id = get_option('map_type_id');
        if ($GoogleMap_iframe == "" && $GoogleMap_latitude == "" && $GoogleMap_longitude == "") {
            $part_before_latitude = "23.2500 ";
            $part_before_longitude = "77.4167 ";
        } else {
            $string = "W";
            $strings = "S";
            $lat_pos = strpos($GoogleMap_latitude, $string);
            $lat_position = strpos($GoogleMap_latitude, $strings);
            if ($lat_pos == true || $lat_position == true) {
                $str = "-";
                $add_prefix_in_position_lat = sprintf("%s%s", $str, $GoogleMap_latitude);
                preg_match('/([^°#&%=]+)/', $add_prefix_in_position_lat, $matches);
                $part_before_latitude = $matches[1];
            } else {
                preg_match('/([^°#&%=]+)/', $GoogleMap_latitude, $matches);
                $part_before_latitude = $matches[1];
            }
            $lng_pos = strpos($GoogleMap_longitude, $string);
            $lng_position = strpos($GoogleMap_longitude, $strings);
            if ($lng_pos == true || $lng_position == true) {
                $strs = "-";
                $add_prefix_in_position_lng = sprintf("%s%s", $strs, $GoogleMap_longitude);
                preg_match('/([^°#&%=]+)/', $add_prefix_in_position_lng, $matche);
                $part_before_longitude = $matche[1];
            } else {
                preg_match('/([^°#&%=]+)/', $GoogleMap_longitude, $matche);
                $part_before_longitude = $matche[1];
            }
        }
        $string = "iframe";
        $pos = strpos($fggm_iframe_form, $string);
        if ($pos == true) {
        ?>
        <div class="fggmgm_contac_us_img" id="fggmgm_contac_us_img" style="display:none; background-color: <?php echo get_option('fggm_tab_bg_color'); ?>"><img src="http://www.formget.com/app/code/contact_tab?c=<?php echo get_option('fggm_tab_text'); ?>&amp;t_color=ffffff&amp;b_color=<?php echo str_replace("#", "", get_option('fggm_tab_bg_color')); ?>&amp;f_size=18" alt="Feedback &amp; Support" style="border:0; background-color: transparent; padding-left:7px; margin:0; padding-top: 25px;"> </div>
        <div id="fggmgm-box-popup"  style="display:none">
            <div id="fggmgm_form_show">
                <div id="fggm_close_box" ><img class="close_popup_box" src="<?php echo plugins_url('/image/close.png', __FILE__); ?>"/></div>
                <div class="fggm_form_area fg_display_form" style="background-color:<?php echo $form_bg_color; ?>" >        
        <?php
        echo stripslashes($fggm_iframe_form);
        ?>
                </div> 
                    <?php
                    $chech_string = "iframe";
                    $check_position = strpos($GoogleMap_iframe, $chech_string);
                    if ($check_position == true) {
                        ?>
					<div class="fgg_address_section">		
                    <div class="map_heading"> Business Location </div>
                    <div class="contact_address"> <?php echo $address_of_location; ?></div>
                    <div id="googleMap" style="overflow:scroll;">
                        <?php
                        echo stripslashes($GoogleMap_iframe);
                        ?> 
                    </div>
					</div>
                </div>
            </div>
        <?php } else {
            ?>
            <script
                src="http://maps.googleapis.com/maps/api/js?&sensor=false">
            </script>
            <div class="gm_lat" style="color: black; display:none;"> <?php echo $part_before_latitude; ?> </div>
            <div class="gm_lon" style="color: black; display:none;"> <?php echo $part_before_longitude; ?></div>
            <div class="gm_zoom" style="color: black; display:none;"> <?php echo $GoogleMap_zoom; ?></div>
            <div class="gm_type" style="color: black; display:none;"> <?php echo $GoogleMap_type_id; ?></div>
			<div class="fgg_address_section">
            <div class="map_heading"> Business Address </div>
			<?php  
			$val = ltrim($address_of_location);
			if( $val != null){ ?>			
            <div class="contact_address"><?php echo $address_of_location; ?> </div>
			<div id="googleMap" style="height:75.8%;"></div>
			<?php } 
              else{
			?>
            <div id="googleMap" style="height:93.8%;"></div>
			<?php } ?>
			</div>
            </div>
            </div> 
            <?php
        }
    }
	}
}
add_action('wp_head', 'display_iframe_form');
//schort code function
if (!function_exists('formget_shortcode')) {
function formget_shortcode($atts, $content = null) {
        extract(shortcode_atts(array(
            'user' => '',
            'formcode' => '',
			'tabtext' => 'Contact Us',
			'position' => 'right',
			'bgcolor' => 'e54040',
			'textcolor' => 'ffffff',
			'fontsize' => '16',
			'width' => '350',
            'allowTransparency' => true,
            'height' => '500',
            'tab' => ''
                        ), $atts));
        $iframe_formget = '';
        $url = "https://www.formget.com/app/embed/form/" . $formcode;
        if ($tab == 'page') {
            $iframe_formget .="<iframe height='" . $height . "' allowTransparency='true' frameborder='0' scrolling='no' style='width:100%;border:none'  src='" . $url . "' >";
            $iframe_formget .="</iframe>";
            add_filter('widget_text', 'do_shortcode');
            return $iframe_formget;
        }
        if ($tab == 'tabbed') {
		
            $tabbed_formget = <<<EOD
<script type='text/javascript'>
(function(s) 
{var head = document.getElementsByTagName('HEAD').item(0);
var s= document.createElement('script');
s.type = 'text/javascript';
s.src='//www.formget.com/app/app_data/new-widget/popup.js';
head.appendChild(s); 
var options = {
'tabKey': '{$formcode}',
'tabtext':'{$tabtext}',
'height': '{$height}',
'width': '{$width}',
'tabPosition':'{$position}',
'textColor': '{$textcolor}',
'tabBackground': '{$bgcolor}',
'fontSize': '{$fontsize}',
'tabbed':''
};
s.onload = s.onreadystatechange = function() {
var rs = this.readyState;
if (rs)
if (rs != 'complete')
if (rs != 'loaded')
return;
try {
sideBar = new buildTabbed();
sideBar.initializeOption(options);
sideBar.loadContent();sideBar.buildHtml();
} 
catch (e) {}  
};
var scr = document.getElementsByTagName(s)[0];
})(document, 'script');
</script>
EOD;
            return $tabbed_formget;
        }
    }
}
add_shortcode('formget', 'formget_shortcode');

if (!function_exists('formget_shortcode_menu')) {
function formget_shortcode_menu() {
$tabbed_formget_menu = <<<EOD
<script type='text/javascript'>
(function(s) 
{
	var screen_height = window.screen.height;
    var screen_width = window.screen.width;
    var canvaswidth = jQuery(window).width();
    var canvasheight = jQuery(document).height();
	var display_positon_left = screen_width - 800;
    var display_positon_top = 13 * screen_height;
	
	jQuery('div#fggmgm_form_show').css({
        "margin-top": display_positon_top / 100,
        "margin-left": display_positon_left / 2
    });
	
	jQuery('div#fggmgm-box-popup').css({
            'height': canvasheight,
            'width': canvaswidth,
            'opacity': '1.95',
            'display': 'block',
            'color': 'white',
            'z-index': '200000',
            'position': 'fixed',
            'top': '0'
        });
	
    var lat = jQuery('div.gm_lat').text();
    var lng = jQuery('div.gm_lon').text();
    var zoom = parseInt(jQuery('div.gm_zoom').text());
    var maptype = jQuery('div.gm_type').text();
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
        var map = new google.maps.Map(document.getElementById('googleMap'), mapProp);
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
        var map = new google.maps.Map(document.getElementById('googleMap'), mapProp);
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
        var map = new google.maps.Map(document.getElementById('googleMap'), mapProp);
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
        var map = new google.maps.Map(document.getElementById('googleMap'), mapProp);
        marker = new google.maps.Marker({
            position: myCenter,
            animation: google.maps.Animation.BOUNCE
        });
        marker.setMap(map);  
 }
 
})(document, 'script');
</script>
EOD;
            return $tabbed_formget_menu;
        }
    }

add_shortcode('formget_menu_form', 'formget_shortcode_menu');
?>