<?php
/*
This file is part of Custom Banners.

Custom Banners is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Custom Banners is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with The Custom Banners.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once("custom_banners_config.php");

class customBannersOptions
{
	var $textdomain = '';
	
	function __construct(){
		//may be running in non WP mode (for example from a notification)
		if(function_exists('add_action')){
			//add a menu item
			add_action( 'admin_menu', array($this, 'add_admin_menu_item') );	
			add_action( 'admin_init', array( $this, 'admin_scripts' ) );
			add_action( 'admin_head', array($this, 'admin_css') );
			add_action( 'custom_banners_admin_settings_page_top', array($this, 'settings_page_top') );
			//add_action( 'custom_banners_admin_settings_page_bottom', array($this, 'settings_page_bottom') );
		}
		
		//instantiate Sajak so we get our JS and CSS enqueued
		new GP_Sajak();		
		
		$this->is_pro = isValidCBKey();
		
		$this->shed = new Custom_Banners_GoldPlugins_BikeShed();
		
		$coupon_box_atts = array(
			'plugin_name' => 'Custom Banners Pro',
			'pitch' => "When you upgrade, you'll instantly unlock the rotating banner widget, all style & typography options, 50+ professionaly designed themes, and more!",
			'learn_more_url' => 'https://goldplugins.com/downloads/custom-banners-pro/?utm_source=cpn_box&utm_campaign=upgrade&utm_banner=learn_more',
			'upgrade_url' => 'https://goldplugins.com/downloads/custom-banners-pro/?utm_source=cpn_box&utm_campaign=upgrade&utm_banner=upgrade',
			'upgrade_url_promo' => 'https://goldplugins.com/downloads/custom-banners-pro/?discount=newsub10&utm_source=cpn_box&utm_campaign=upgrade&utm_banner=upgrade',
			'text_domain' => 'wordpress',
			'testimonial' => false,			
		);
		
		$this->coupon_box = new GP_MegaSeptember($coupon_box_atts);

		add_filter('update_option_custom_banners_registered_key', array($this, 'recheck_key') );
	}
	
	function add_admin_menu_item(){
		$title = "Custom Banners Settings";
		$page_title = "Custom Banners Settings";
		$top_level_slug = "custom-banners-settings";
		
		$submenu_pages = array(
			array(
				'parent_slug' => $top_level_slug,
				'page_title' => 'Basic Options',
				'menu_title' => 'Basic Options',
				'capability' => 'administrator',
				'menu_slug' => $top_level_slug,
				'callback' =>  array($this, 'basic_settings_page')
			),
			array(
				'parent_slug' => $top_level_slug,
				'page_title' => 'Theme Options',
				'menu_title' => 'Theme Options',
				'capability' => 'administrator',
				'menu_slug' => 'custom-banners-themes',
				'callback' => array($this, 'themes_page')
			),
			array(
				'parent_slug' => $top_level_slug,
				'page_title' => 'Style Options',
				'menu_title' => 'Style Options',
				'capability' => 'administrator',
				'menu_slug' => 'custom-banners-style-settings',
				'callback' => array($this, 'style_settings_page')
			),
			array(
				'parent_slug' => $top_level_slug,
				'page_title' => 'Help & Instructions',
				'menu_title' => 'Help & Instructions',
				'capability' => 'administrator',
				'menu_slug' => 'custom-banners-help',
				'callback' => array($this, 'help_page')
			),
		);
		
		$submenu_pages = apply_filters("custom_banners_admin_settings_submenu_pages", $submenu_pages, $top_level_slug);
		
		//create new top-level menu
		add_menu_page($page_title, $title, 'administrator', $top_level_slug, array($this, 'basic_settings_page'));
		
		foreach($submenu_pages as $submenu_page){
			add_submenu_page($submenu_page['parent_slug'] , $submenu_page['page_title'], $submenu_page['menu_title'], $submenu_page['capability'],$submenu_page['menu_slug'], $submenu_page['callback']);
		}

		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	
	}

	function get_submenu_pages($top_level_slug)
	{
		$style_menu_label = $this->is_pro ? __('Style Options', $this->textdomain) : __('Style Options (Pro)', $this->textdomain);
		$submenu_pages = array();
		
		$submenu_pages[] = array(
			'parent_slug' => $top_level_slug,
			'page_title' => 'Basic Options',
			'menu_title' => 'Basic Options',
			'capability' => 'administrator',
			'menu_slug' => $top_level_slug,
			'callback' => array($this, 'basic_settings_page')
		);
		
		$submenu_pages[] = array(
			'parent_slug' => $top_level_slug,
			'page_title' => 'Style Options',
			'menu_title' => $style_menu_label,
			'capability' => 'administrator',
			'menu_slug' => 'custom-banners-style-settings',
			'callback' => array($this, 'style_settings_page')
		);
				
		$submenu_pages[] = array(
			'parent_slug' => $top_level_slug,
			'page_title' => 'Help &amp; Instructions',
			'menu_title' => 'Help &amp; Instructions',
			'capability' => 'administrator',
			'menu_slug' => 'custom-banners-help',
			'callback' => array($this, 'help_page')
		);
		
		return apply_filters('custom_banners_admin_settings_submenu_pages', $submenu_pages, $top_level_slug);
	}

	function get_admin_tabs($top_level_slug)
	{
		$submenu_pages = $this->get_submenu_pages($top_level_slug);
		$tabs = array();
		foreach ($submenu_pages as $page) {
			$slug = $page['menu_slug'];
			if ( empty($slug) ) {
				$slug = $top_level_slug;
			}
			$tabs[$slug] = $page['menu_title'];
		}
		return apply_filters('custom_banners_admin_tabs', $tabs, $top_level_slug);
	}

	function register_settings(){
		//register our settings
		register_setting( 'custom-banners-settings-group', 'custom_banners_custom_css' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_use_big_link' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_open_link_in_new_window' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_never_show_captions' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_never_show_cta_buttons' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_default_width' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_default_height' );
		
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_name' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_url' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_key' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_cache_buster', array($this, 'bust_options_cache') );

		register_setting( 'custom-banners-theme-settings-group', 'custom_banners_theme' );
		register_setting( 'custom-banners-theme-settings-group', 'custom_banners_preview_window_background' );

		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_background_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_background_opacity' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_background_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_border_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_border_radius' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_size' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_style' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_family' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_size' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_style' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_family' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cache_buster', array($this, 'bust_options_cache') );
	}
	
	//function to produce tabs on admin screen
	function admin_tabs($current = 'homepage' ) {
		$style_label = $this->is_pro ? __('Style Options', $this->textdomain) : __('Style Options (Requires Pro)', $this->textdomain);
		$tabs = array( 	
			array(
				'menu_slug' => 'custom-banners-settings',
				'menu_title' => __('Basic Options', $this->textdomain),
			),
			array(
				'menu_slug' => 'custom-banners-themes',
				'menu_title' =>	__('Theme Options', $this->textdomain),
			),
			array(
				'menu_slug' => 'custom-banners-style-settings',
				'menu_title' => $style_label,
			),
			array(
				'menu_slug' => 'custom-banners-help',
				'menu_title' => __('Help &amp; Instructions', $this->textdomain),
			)
		);
				
		$tabs = apply_filters('custom_banners_admin_settings_submenu_pages', $tabs);
						
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab){
				$class = ( $tab['menu_slug'] == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab{$class}' href='?page={$tab['menu_slug']}'>{$tab['menu_title']}</a>";
			}
		echo '</h2>';
	}
	
	function admin_scripts()
	{
		wp_enqueue_script(
			'gp-admin_v2',
			plugins_url('../assets/js/gp-admin_v2.js', __FILE__),
			array( 'jquery' ),
			false,
			true
		);	
	}
		
	function admin_css()
	{
		if(is_admin()) {
			$admin_css_url = plugins_url( '../assets/css/admin_style.css' , __FILE__ );
			wp_register_style('custom-banners-admin', $admin_css_url);
			wp_enqueue_style('custom-banners-admin');
		}	
	}

	function settings_page_top(){
		$title = "Custom Banners Settings";
		$message = "Custom Banners Settings Updated.";
		
		global $pagenow;
	?>
	<div class="wrap gold_plugins_settings <?php if(!$this->is_pro): ?>not_pro<?php endif; ?>">
		<h2><?php echo $title; ?></h2>
		
		<p class="cb_need_help">Need Help? <a href="http://goldplugins.com/documentation/custom-banners-documentation/" target="_blank">Click here</a> to read instructions, see examples, and find more information on how to add, edit, update, and output your custom banners.</p>
		
		<?php if(!$this->is_pro): ?>		
			<script type="text/javascript">
				jQuery(function () {
					if (typeof(gold_plugins_init_coupon_box) == 'function') {
					gold_plugins_init_coupon_box();
					}
				});
			</script>
			<?php $this->coupon_box->form(); ?>
		<?php endif; ?>
		
		<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
		<?php endif;
		
		$this->get_and_output_current_tab($pagenow);
	}
	
	function output_mailing_list_form()
	{
		global $current_user;
?>
		<script type="text/javascript">
			jQuery(function () {
				if (typeof(gold_plugins_init_coupon_box) == 'function') {
				gold_plugins_init_coupon_box();
				}
			});
		</script>
		<!-- Begin MailChimp Signup Form -->		
		<div id="signup_wrapper">
			<div class="topper yellow_orange_bg">
				<h3>Save 10% When You Upgrade To Custom Banners Pro</h3>
				<p class="pitch">When you upgrade, you'll instantly unlock the rotating banner widget, all style &amp; typography options, 50+ professionaly designed themes, and more! </p>
				<a class="upgrade_link" href="http://goldplugins.com/our-plugins/custom-banners?utm_source=cpn_box&utm_campaign=upgrade&utm_banner=learn_more" title="Learn More">Learn More About Custom Banners Pro &raquo;</a>
			</div>
			<div id="mc_embed_signup">
				<div class="save_now">
					<h3>Save 10% Now!</h3>
					<p class="pitch">Subscribe to our newsletter now, and we’ll send you a coupon for 10% off your upgrade to the Pro version.</p>
				</div>	
				<form action="" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
					<div class="fields_wrapper">
						<label for="mce-NAME">Your Name:</label>
						<input type="text" value="<?php echo (!empty($current_user->display_name) ? $current_user->display_name : ''); ?>" name="NAME" class="name" id="mce-NAME" placeholder="Your Name">
						<label for="mce-EMAIL">Your Email:</label>
						<input type="email" value="<?php echo (!empty($current_user->user_email) ? $current_user->user_email : ''); ?>" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
						<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
						<div style="position: absolute; left: -5000px;"><input type="text" name="b_403e206455845b3b4bd0c08dc_6ad78db648" tabindex="-1" value=""></div>
					</div>
					<div class="clear"><input type="submit" value="Send Me The Coupon" name="subscribe" id="mc-embedded-subscribe" class="smallBlueButton"></div>
					<p class="secure"><img src="<?php echo plugins_url( '../assets/img/lock.png', __FILE__ ); ?>" alt="Lock" width="16px" height="16px" />We respect your privacy.</p>
					<input type="hidden" name="PRODUCT" value="Custom Banners Pro" />
					<input type="hidden" id="mc-upgrade-plugin-name" value="Custom Banners Pro" />
					<input type="hidden" id="mc-upgrade-link-per" value="http://goldplugins.com/purchase/custom-banners-pro/single?promo=newsub10" />
					<input type="hidden" id="mc-upgrade-link-biz" value="http://goldplugins.com/purchase/custom-banners-pro/business?promo=newsub10" />
					<input type="hidden" id="mc-upgrade-link-dev" value="http://goldplugins.com/purchase/custom-banners-pro/developer?promo=newsub10" />
				</form>
			</div>
			<p class="u_to_p"><a href="http://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_source=plugin&utm_campaign=small_text_signup">Upgrade to Custom Banners Pro now</a> to remove banners like this one.</p>
		</div>
		<!--End mc_embed_signup-->
<?php	
	}
	
	function get_and_output_current_tab($pagenow){
		$tab = $_GET['page'];
		
		$this->admin_tabs($tab); 
				
		return $tab;
	}	
	
	//this function outputs the Basic settings tab and everything within it.
	function basic_settings_page(){	
		//load additional label string based upon pro status
		$pro_string = $this->is_pro ? "" : " (Pro)";
		
		//add upgrade button if free version
		$extra_buttons = array();
		if(!$this->is_pro){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/special-offers/upgrade-to-custom-banners-pro/'
				)
			);
		}
		
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Basic Options',
			'settings_field_key' => 'custom-banners-settings-group', // can be an array	
			'show_save_button' => true, // hide save buttons for all panels   		
			'extra_buttons_header' => $extra_buttons, // extra header buttons
			'extra_buttons_footer' => $extra_buttons // extra footer buttons
		) );
	
		$this->settings_page_top();
		$tabs->add_tab(
			'general_options', // section id, used in url fragment
			'General Settings', // section label
			array($this, 'output_general_settings'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
	
		$tabs->add_tab(
			'registration_options', // section id, used in url fragment
			'Registration Settings', // section label
			array($this, 'output_registration_settings'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'key' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	} // end basic_settings_page function	
	
	//output the options found in the General Settings tab
	function output_general_settings(){
		?>					
			<h3>General Options</h3>
			<p>These options control the banner default settings.</p>
		
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_custom_css">Custom CSS</a></th>
					<td><textarea name="custom_banners_custom_css" id="custom_banners_custom_css" style="width: 250px; height: 250px;"><?php echo get_option('custom_banners_custom_css'); ?></textarea>
					<p class="description">Input any Custom CSS you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.<br/> For a list of available classes, click <a href="http://goldplugins.com/documentation/custom-banners-documentation/html-css-information-for-custom-banners/" target="_blank">here</a>.</p></td>
				</tr>
			</table>
			
			<table class="form-table">
			<?php
				$this->text_input('custom_banners_default_width', 'Default Banner Width', 'Enter a default width for your banners, in pixels. If not specified, the banner will try to fit its container.', '');
				$this->text_input('custom_banners_default_height', 'Default Banner Height', 'Enter a default height for your banners, in pixels. If not specified, the banner will try to fit its container.', '');
			?>
			</table>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_use_big_link">Link Entire Banner</label></th>
					<td><input type="checkbox" name="custom_banners_use_big_link" id="custom_banners_use_big_link" value="1" <?php if(get_option('custom_banners_use_big_link')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, the entire banner will be linked to the Target URL - not just the CTA.</p>
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_open_link_in_new_window">Open Link in New Window</label></th>
					<td><input type="checkbox" name="custom_banners_open_link_in_new_window" id="custom_banners_open_link_in_new_window" value="1" <?php if(get_option('custom_banners_open_link_in_new_window')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, the Banner Link / CTA will open in a New Window.</p>
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_never_show_captions">Never Show Captions</label></th>
					<td><input type="checkbox" name="custom_banners_never_show_captions" id="custom_banners_never_show_captions" value="1" <?php if(get_option('custom_banners_never_show_captions')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, your banners will not show their captions, even if you enter one.</p>
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_never_show_cta_buttons">Never Show CTA Buttons</label></th>
					<td><input type="checkbox" name="custom_banners_never_show_cta_buttons" id="custom_banners_never_show_cta_buttons" value="1" <?php if(get_option('custom_banners_never_show_cta_buttons')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, your banners will not show their buttons, even if you have entered a call to action.</p>
					</td>
				</tr>
			</table>
		<?php
	}
	
	//this function outputs the Registration settings section
	function output_registration_settings(){
		?>
			<h3>Pro Registration</h3>			
			<?php if($this->is_pro): ?>	
			<p class="plugin_is_registered">Your plugin is succesfully registered and activated. Thank you!</p>
			<?php else: ?>
			<p class="plugin_is_not_registered">Custom Banners Pro is not activated. You will not be able to use the Pro features until you activate the plugin. <br /><br /><a class="button" href="http://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_campaign=registration&utm_source=custom_banners_settings" target="_blank">Click Here To Upgrade To Pro</a> <br /> <br /><em>You'll unlock powerful new features including advanced styling options and JavaScript transitions for your slideshows.</em></p>
			<p>Please enter your email address and API key to activate Custom Banners Pro. </p>
			<?php endif; ?>	
			<?php if(!isValidMSCBKey()): ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_registered_name">Email Address</label></th>
					<td><input type="text" name="custom_banners_registered_name" id="custom_banners_registered_name" value="<?php echo get_option('custom_banners_registered_name'); ?>"  style="width: 250px" />
					<p class="description">This is the e-mail address that you used when you purchased the plugin.</p>
					</td>
				</tr>
			</table>

			<table class="form-table" style="display: none;">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_registered_url">Website Address</label></th>
					<td><input type="text" name="custom_banners_registered_url" id="custom_banners_registered_url" value="<?php echo get_option('custom_banners_registered_url'); ?>"  style="width: 250px" />
					<p class="description">This is the Website Address that you used when you purchased the plugin.</p>
					</td>
				</tr>
			</table>
				
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_registered_key">API Key</label></th>
					<td><input type="text" name="custom_banners_registered_key" id="custom_banners_registered_key" value="<?php echo get_option('custom_banners_registered_key'); ?>"  style="width: 250px" />
					<p class="description">This is the API Key that you received after purchasing the plugin.</p>
					</td>
				</tr>
			</table>
			<p class="description">Please Note: Submitting your API Key will call home to our servers to confirm your key, before unlocking the Pro features.</p>

			<?php endif; ?>
		<?php
	}
	
	//this function outputs the Help tab with Galahad and everything within it.
	function help_page(){	
		//load additional label string based upon pro status
		$pro_string = $this->is_pro ? "" : " (Pro)";
		
		//add upgrade button if free version
		$extra_buttons = array();
		if(!$this->is_pro){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/special-offers/upgrade-to-custom-banners-pro/'
				)
			);
		}
		
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Help &amp; Instructions',
			'settings_field_key' => '', // can be an array	
			'show_save_button' => false, // hide save buttons for all panels   		
			'extra_buttons_header' => $extra_buttons, // extra header buttons
			'extra_buttons_footer' => $extra_buttons // extra footer buttons
		) );
	
		$this->settings_page_top();
		$tabs->add_tab(
			'help', // section id, used in url fragment
			'Help Center', // section label
			array($this, 'output_help_page'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'life-buoy' // icons here: http://fontawesome.io/icons/
			)
		);
	
		$tabs->add_tab(
			'contact', // section id, used in url fragment
			'Contact Support' . $pro_string, // section label
			array($this, 'output_contact_page'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'envelope-o' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
		
	} // end help_page function
	
	function output_contact_page(){
		if($this->is_pro){		
			//load all plugins on site
			$all_plugins = get_plugins();
			//load current theme object
			$the_theme = wp_get_theme();
			//load current easy t options
			$the_options = $this->load_all_options();
			//load wordpress area
			global $wp_version;
			
			$site_data = array(
				'plugins'	=> $all_plugins,
				'theme'		=> $the_theme,
				'wordpress'	=> $wp_version,
				'options'	=> $the_options
			);
			
			$current_user = wp_get_current_user();
			?>
			<h3>Contact Support</h3>
			<p>Would you like personalized support? Use the form below to submit a request!</p>
			<p>If you aren't able to find a helpful answer in our Help Center, go ahead and send us a support request!</p>
			<p>Please be as detailed as possible, including links to example pages with the issue present and what steps you've taken so far.  If relevant, include any shortcodes or functions you are using.</p>
			<p>Thanks!</p>
			<div class="gp_support_form_wrapper">
				<div class="gp_ajax_contact_form_message"></div>
				
				<div data-gp-ajax-form="1" data-ajax-submit="1" class="gp-ajax-form" method="post" action="https://goldplugins.com/tickets/galahad/catch.php">
					<div style="display: none;">
						<textarea name="your-details" class="gp_galahad_site_details">
							<?php
								echo htmlentities(json_encode($site_data));
							?>
						</textarea>
						
					</div>
					<div class="form_field">
						<label>Your Name (required)</label>
						<input type="text" aria-invalid="false" aria-required="true" size="40" value="<?php echo (!empty($current_user->display_name) ?  $current_user->display_name : ''); ?>" name="your_name">
					</div>
					<div class="form_field">
						<label>Your Email (required)</label>
						<input type="email" aria-invalid="false" aria-required="true" size="40" value="<?php echo (!empty($current_user->user_email) ?  $current_user->user_email : ''); ?>" name="your_email"></span>
					</div>
					<div class="form_field">
						<label>URL where problem can be seen:</label>
						<input type="text" aria-invalid="false" aria-required="false" size="40" value="" name="example_url">
					</div>
					<div class="form_field">
						<label>Your Message</label>
						<textarea aria-invalid="false" rows="10" cols="40" name="your_message"></textarea>
					</div>
					<div class="form_field">
						<input type="hidden" name="include_wp_info" value="0" />
						<label for="include_wp_info">
							<input type="checkbox" id="include_wp_info" name="include_wp_info" value="1" />Include information about my WordPress environment (server information, installed plugins, theme, and current version)
						</label>
					</div>					
					<p><em>Sending this data will allow the Gold Plugins can you help much more quickly. We strongly encourage you to include it.</em></p>
					<input type="hidden" name="registered_email" value="<?php echo htmlentities(get_option('custom_banners_registered_name')); ?>" />
					<input type="hidden" name="site_url" value="<?php echo htmlentities(site_url()); ?>" />
					<input type="hidden" name="challenge" value="<?php echo substr(md5(sha1('bananaphone' . get_option('custom_banners_registered_key') )), 0, 10); ?>" />
					<div class="submit_wrapper">
						<input type="submit" class="button submit" value="Send">			
					</div>
				</div>
			</div>
			<?php
		} else {
			?>
			<h3>Contact Support</h3>
			<p>Would you like personalized support? Upgrade to Pro today to receive hands on support and access to all of our Pro features!</p>
			<p><a class="button upgrade" href="https://goldplugins.com/downloads/custom-banners-pro/?utm_source=galahad&utm_campaign=upgrade&utm_banner=learn_more">Click Here To Learn More</a></p>			
			<?php
		}
	}
	
	function output_help_page(){
		?>
		<h3>Help Center</h3>
		<div class="help_box">
			<h4>Have a Question?  Check out our FAQs!</h4>
			<p>Our FAQs contain answers to our most frequently asked questions.  This is a great place to start!</p>
			<p><a class="custom_banners_support_button" target="_blank" href="https://goldplugins.com/documentation/custom-banners-documentation/custom-banners-faqs/?utm_source=help_page">Click Here To Read FAQs</a></p>
		</div>
		<div class="help_box">
			<h4>Looking for Instructions? Check out our Documentation!</h4>
			<p>For a good start to finish explanation of how to add Banners and then display them on your site, check out our Documentation!</p>
			<p><a class="custom_banners_support_button" target="_blank" href="https://goldplugins.com/documentation/custom-banners-documentation/?utm_source=help_page">Click Here To Read Our Docs</a></p>
		</div>
		<?php		
	}
	
	//this function displays the Theme tab contents
	function themes_page()
	{
		//load additional label string based upon pro status
		$pro_string = $this->is_pro ? "" : " (Pro)";
		
		//add upgrade button if free version
		$extra_buttons = array();
		if(!$this->is_pro){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/special-offers/upgrade-to-custom-banners-pro/'
				)
			);
		}
		
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Theme Options',
			'settings_field_key' => 'custom-banners-theme-settings-group', // can be an array	
			'show_save_button' => true, // hide save buttons for all panels   		
			'extra_buttons_header' => $extra_buttons, // extra header buttons
			'extra_buttons_footer' => $extra_buttons // extra footer buttons
		) );
	
		$this->settings_page_top();
		$tabs->add_tab(
			'themes', // section id, used in url fragment
			'Theme Settings', // section label
			array($this, 'output_theme_settings'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'paint-brush' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	}
	
	//outputs the theme preview / selector interface
	function output_theme_settings(){
		wp_enqueue_style( 'custom-banners-admin' );	
		settings_fields( 'custom-banners-theme-settings-group' ); 
		
		?>				
		<h3>Custom Banners Themes</h3>			
		<p>Please select a theme to use with your Banners. This theme will become  your default choice, but you can always specify a different theme for each widget if you like!</p>
		
		<?php if (!$this->is_pro): ?>
			<?php 
				$upgrade_link = '<a class="button" target="_blank" href="https://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_source=plugin_settings&utm_campaign=themes_upgrade_box">Upgrade Now</a>';
			?>
			<p style="color:green; font-weight: bold;"><em>Note: You are using the free edition of Custom Banners, which includes a limited number of themes. <?php echo $upgrade_link ?> to unlock all 50+ themes!</em></p>
		<?php  endif; ?>
		<table class="form-table custom-banners-options-table">
			<?php
				$cb_config = new CustomBanners_Config();
			
				$current_theme = get_option('custom_banners_theme');
				$themes = $cb_config->all_themes($this->is_pro,false);
				$desc = 'Select a theme to see how it would look with your Banners. <br /><br /> If \'No Theme\' is selected, only your theme\'s own CSS, and any Custom CSS you\'ve added, will be applied to your Banners.';
				if (!$this->is_pro)
				{
					$desc = 'Select a theme to see how it would look with your Banners.<br /><br /> If \'No Theme\' is selected, only your theme\'s own CSS, and any Custom CSS you\'ve added, will be applied to your Banners.';						
				}
				$this->shed->grouped_select( array('name' => 'custom_banners_theme', 'options' => $themes, 'label' =>'Banners Theme', 'value' => $current_theme, 'description' => $desc) );

			?>
		</table>
		
		<div id="custom_banners_theme_preview">			
			<div id="custom_banners_theme_preview_color_picker">
				<table class="form-table">
				<?php
					$cur_prev_bg = get_option('custom_banners_preview_window_background', '#fff');
					$this->shed->color( array('name' => 'custom_banners_preview_window_background', 'label' =>'Set Background Color:', 'value' => $cur_prev_bg, 'description' => '') );
				?>
				</table>
			</div>
			<div id="custom_banners_theme_preview_browser"></div>
			<div id="custom_banners_theme_preview_content">
				<div style="" data-cycle-auto-height="container" class="custom-banners-cycle-slideshow cycle-slideshow custom-b-317582994 custom-banners-cycle-slideshow-theme-standard custom-banners-cycle-slideshow-standard-black" data-cycle-fx="fade" data-cycle-timeout="4000" data-cycle-pause-on-hover="" data-cycle-slides="> div.banner_wrapper" data-cycle-paused=""data-cycle-prev=".custom-b-317582994 .custom-b-cycle-prev"  data-cycle-next=".custom-b-317582994 .custom-b-cycle-next"data-cycle-pager=".custom-b-317582994 .custom-b-cycle-pager"data-cycle-pager-template="<span><a href=#>{{slideNum}}</a></span>"><div class="banner_wrapper" style=""><div class="banner  banner-5811 has_cta bottom vert custom-banners-theme-standard custom-banners-theme-standard-black" style=""><img width="580" height="270" src="<?php echo plugins_url( '../assets/img/Untitled-4.png', __FILE__ ); ?>" class="attachment-full size-full" alt="Untitled-4" /><div class="banner_caption" style=""><div class="banner_caption_inner" style="">Hello!  For a short time, register for free!<div class="banner_call_to_action"><a href="#"  class="banner_btn_cta" style="">Click Here!</a></div></div></div></div></div><div class="banner_wrapper" style="display:none; "><div class="banner  banner-5810 has_cta bottom vert custom-banners-theme-standard custom-banners-theme-standard-black" style=""><img width="580" height="270" src="<?php echo plugins_url( '../assets/img/Untitled-3.png', __FILE__ ); ?>" class="attachment-full size-full" alt="Untitled-3" /><div class="banner_caption" style=""><div class="banner_caption_inner" style="">For a short time, buy one get one free!<div class="banner_call_to_action"><a href="#"  class="banner_btn_cta" style="">Click Here to Learn More</a></div></div></div></div></div><div class="custom-b-cycle-controls custom-banners-controls-theme-standard custom-banners-controls-theme-standard-black"><div class="custom-b-cycle-prev">&lt;&lt;</div><div class="custom-b-cycle-pager"></div><div class="custom-b-cycle-next">&gt;&gt;</div></div></div><!-- end slideshow -->
			</div>
		</div>
		
		<?php if(!$this->is_pro): ?>			
		<div id="custom_banners_themes_pro_warning">
			<h3>Upgrade to Unlock More Themes</h3>
			<p>Preview our available Pro themes <a href="https://goldplugins.com/documentation/custom-banners-documentation/custom-banners-pro-examples/?utm_source=themes_page">here!</a></p>
			<p class="click_to_upgrade">
				<a class="button" target="_blank" href="http://goldplugins.com/our-plugins/custom-banners-details/upgrade-to-custom-banners-pro/?utm_source=plugin_settings&utm_campaign=themes_upgrade_box">Upgrade Now</a>
			</p>
		</div>
		<?php endif; ?>
		<?php
	}
	
	//outputs the contents of the style settings tab	
	function style_settings_page()
	{
		//load additional label string based upon pro status
		$pro_string = $this->is_pro ? "" : " (Pro)";
		
		//add upgrade button if free version
		$extra_buttons = array();
		if(!$this->is_pro){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/special-offers/upgrade-to-custom-banners-pro/'
				)
			);
		}
		
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Style Options',
			'settings_field_key' => 'custom-banners-style-settings-group', // can be an array	
			'show_save_button' => ( $this->is_pro ?
									true :
									false ), // hide save buttons for all panels when not pro  		
			'extra_buttons_header' => $extra_buttons, // extra header buttons
			'extra_buttons_footer' => $extra_buttons // extra footer buttons
		) );
	
		$this->settings_page_top();
		$tabs->add_tab(
			'themes', // section id, used in url fragment
			'Style Settings', // section label
			array($this, 'output_style_settings'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();			
	}
	
	//output the Style Settings
	function output_style_settings(){
		?>		
		<h3>Typography and Style Options</h3>
		<p>These options control the appearance of your banners and their captions.</p>
		<?php
	
		$disabled = !($this->is_pro);
		
		if (!$this->is_pro): ?>
		<div class="plugin_is_not_registered">
			<p>Custom Banners Pro is required to use these features. The options below will become instantly available once you have registered and activated your plugin. <br /><br /><a class="button" href="http://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_campaign=registration&utm_source=custom_banners_settings" target="_blank">Click Here To Upgrade To Pro</a> <br /> <br /><em>You'll receive your API keys as soon as you complete your payment, instantly unlocking these features and more!</em></p>
		</div>
		<?php endif; ?>
		<fieldset>
			<legend>Caption Background</legend>				
			<table class="form-table">
				<?php 
					$this->color_input('custom_banners_caption_background_color', 'Background Color', '#000000', $disabled);
					$this->text_input('custom_banners_caption_background_opacity', 'Background Opacity (percentage)', '', '70', $disabled);
				?>
			</table>
		</fieldset>

		<fieldset>
			<legend>Caption Text</legend>
			<table class="form-table">
				<?php
					$this->typography_input('custom_banners_caption_*', 'Caption Font', 'Please note: these settings can be overridden for each banner by using the Visual Editor.', '', '', '', '#ffffff', $disabled );
				?>
			</table>
		</fieldset>
					
		<fieldset>
			<legend>Call To Action (CTA) Button</legend>
			<table class="form-table">
				<?php
					$this->typography_input('custom_banners_cta_button_*', 'Font', '', '', '', '', '', $disabled);
					$this->color_input('custom_banners_cta_background_color', 'Background Color', '#ffa500', $disabled);
					$this->color_input('custom_banners_cta_border_color', 'Border Color', '#ff8c00', $disabled);
					$this->text_input('custom_banners_cta_border_radius', 'Border Radius', '', '5', $disabled);
				?>
			</table>
		</fieldset>
		<?php
	}
	
	function text_input($name, $label, $description = '', $default_val = '', $disabled = false)
	{
		$val = get_option($name, $default_val);
		if (empty($val)) {
			$val = $default_val;
		}
		$this->shed->text(
			array(
				'name' => $name,
				'label' => $label,
				'value' => $val,
				'description' => $description,
				'disabled' => $disabled
			)
		);
	}
	
	function color_input($name, $label, $default_color = '#000000', $disabled = false)
	{		
		$val = get_option($name, $default_color);
		if (empty($val)) {
			$val = $default_color;
		}
		$this->shed->color(
			array(
				'name' => $name,
				'label' => $label,
				'default_color' => $default_color,
				'value' => $val,
				'disabled' => $disabled
			)
		);
	}
	
	function typography_input($name, $label, $description = '', $default_font_family = '', $default_font_size = '', $default_font_style = '', $default_font_color = '#000080', $disabled = false)
	{
		$options = array(
			'name' => $name,
			'label' => $label,
			'default_color' => $default_font_color,
			'description' => $description,
			'google_fonts' => true,
			'values' => array(),
			'disabled' => $disabled			
		);
		$fields = array(
			'font_size' => $default_font_size,
			'font_family' => $default_font_family,
			'font_color' => $default_font_color,
			'font_style' => $default_font_style
		);
		foreach ($fields as $key => $default_value)
		{
			list($field_name, $field_id) = $this->shed->get_name_and_id($options, $key);
			$val = get_option($field_name, $default_value);
			if (empty($val)) {
				$val = $default_value;
			}			
			$options['values'][$key] = $val;
		}
		$this->shed->typography($options);
	}
	
	function bust_options_cache()
	{
		delete_transient('_custom_bs_webfont_str');
	}
	
	function recheck_key()
	{
		$kc = new GoldPlugins_Key_Checker('custom-banners-pro');
		$license_email = get_option('custom_banners_registered_name');
		$license_key = get_option('custom_banners_registered_key');
		$key_status = $kc->get_key_status($license_email, $license_key);
		$option_key = '_custom_banners_pro_license_status';
		switch ( $key_status ) {
			
			case 'ACTIVE':			
			case 'EXPIRED':						
				update_option( $option_key, $key_status );
				break;
				
			case 'INVALID':
				delete_option( $option_key );
				break;
			
			// do nothing - couldn't reach the activation server 			
			case 'UNKNOWN': 
			default: 
				break;
		}
	}
	
	//loads all options
	//builds array of options matching our prefix
	//returns our array
	private function load_all_options(){
		$my_options = array();
		$all_options = wp_load_alloptions();
		
		$patterns = array(
			'custom_banners_(.*)',
		);
		
		foreach ( $all_options as $name => $value ) {
			if ( $this->preg_match_array( $name, $patterns ) ) {
				$my_options[ $name ] = $value;
			}
		}
		
		return $my_options;
	}
	
	function preg_match_array( $candidate, $patterns )
	{
		foreach ($patterns as $pattern) {
			$p = sprintf('#%s#i', $pattern);
			if ( preg_match($p, $candidate, $matches) == 1 ) {
				return true;
			}
		}
		return false;
	}
} // end class
?>