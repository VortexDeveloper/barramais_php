<?php
/**
 * @package WPSEO\Admin
 */

/**
 * This class registers all the necessary styles and scripts. Also has methods for the enqueing of scripts and styles. It automatically adds a prefix to the handle.
 */
class WPSEO_Admin_Asset_Manager {

	/**
	 *  Prefix for naming the assets.
	 */
	const PREFIX = 'yoast-seo-';

	/**
	 * Enqueues scripts.
	 *
	 * @param string $script The name of the script to enqueue.
	 */
	public function enqueue_script( $script ) {
		wp_enqueue_script( self::PREFIX . $script );
	}

	/**
	 * Enqueues styles.
	 *
	 * @param string $style The name of the style to enqueue.
	 */
	public function enqueue_style( $style ) {
		wp_enqueue_style( self::PREFIX . $style );
	}

	/**
	 * Registers scripts based on it's parameters.
	 *
	 * @param WPSEO_Admin_Asset $script The script to register.
	 */
	public function register_script( WPSEO_Admin_Asset $script ) {
		wp_register_script(
			self::PREFIX . $script->get_name(),
			$script->get_url( WPSEO_Admin_Asset::TYPE_JS, WPSEO_FILE ),
			$script->get_deps(),
			$script->get_version(),
			$script->is_in_footer()
		);
	}

	/**
	 * Registers styles based on it's parameters.
	 *
	 * @param WPSEO_Admin_Asset $style The style to register.
	 */
	public function register_style( WPSEO_Admin_Asset $style ) {
		wp_register_style(
			self::PREFIX . $style->get_name(),
			$style->get_url( WPSEO_Admin_Asset::TYPE_CSS, WPSEO_FILE ),
			$style->get_deps(),
			$style->get_version(),
			$style->get_media()
		);
	}

	/**
	 * Calls the functions that register scripts and styles with the scripts and styles to be registered as arguments.
	 */
	public function register_assets() {
		$this->register_scripts( $this->scripts_to_be_registered() );
		$this->register_styles( $this->styles_to_be_registered() );
	}

	/**
	 * Registers all the scripts passed to it.
	 *
	 * @param array $scripts The scripts passed to it.
	 */
	public function register_scripts( $scripts ) {
		foreach ( $scripts as $script ) {
			$script = new WPSEO_Admin_Asset( $script );
			$this->register_script( $script );
		}
	}

	/**
	 * Registers all the styles it recieves.
	 *
	 * @param array $styles Styles that need to be registerd.
	 */
	public function register_styles( $styles ) {
		foreach ( $styles as $style ) {
			$style = new WPSEO_Admin_Asset( $style );
			$this->register_style( $style );
		}
	}

	/**
	 * A list of styles that shouldn't be registered but are needed in other locations in the plugin.
	 *
	 * @return array
	 */
	public function special_styles() {
		$flat_version = $this->flatten_version( WPSEO_VERSION );

		return array(
			'inside-editor' => new WPSEO_Admin_Asset( array(
				'name' => 'inside-editor',
				'src' => 'inside-editor-' . $flat_version,
			) ),
		);
	}

	/**
	 * Flattens a version number for use in a filename
	 *
	 * @param string $version The original version number.
	 * @return string The flattened version number.
	 */
	public function flatten_version( $version ) {
		$parts = explode( '.', $version );

		if ( count( $parts ) === 2 ) {
			$parts[] = '0';
		}

		return implode( '', $parts );
	}

	/**
	 * Returns the scripts that need to be registered.
	 *
	 * @TODO data format is not self-documenting. Needs explanation inline. R.
	 *
	 * @return array scripts that need to be registered.
	 */
	private function scripts_to_be_registered() {

		$select2_language = 'en';
		$user_locale      = WPSEO_Utils::get_user_locale();
		$language         = WPSEO_Utils::get_language( $user_locale );

		if ( file_exists( WPSEO_PATH . "js/dist/select2/i18n/{$user_locale}.js" ) ) {
			$select2_language = $user_locale; // Chinese and some others use full locale.
		}
		elseif ( file_exists( WPSEO_PATH . "js/dist/select2/i18n/{$language}.js" ) ) {
			$select2_language = $language;
		}

		$flat_version = $this->flatten_version( WPSEO_VERSION );

		return array(
			array(
				'name' => 'admin-script',
				'src'  => 'wp-seo-admin-' . $flat_version,
				'deps' => array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-progressbar',
					self::PREFIX . 'select2',
					self::PREFIX . 'select2-translations',
				),
			),
			array(
				'name' => 'admin-media',
				'src'  => 'wp-seo-admin-media-' . $flat_version,
				'deps' => array(
					'jquery',
					'jquery-ui-core',
				),
			),
			array(
				'name' => 'bulk-editor',
				'src'  => 'wp-seo-bulk-editor-' . $flat_version,
				'deps' => array( 'jquery' ),
			),
			array(
				'name' => 'dismissible',
				'src'  => 'wp-seo-dismissible-' . $flat_version,
				'deps' => array( 'jquery' ),
			),
			array(
				'name' => 'admin-global-script',
				'src'  => 'wp-seo-admin-global-' . $flat_version,
				'deps' => array( 'jquery' ),
			),
			array(
				'name' => 'metabox',
				'src'  => 'wp-seo-metabox-' . $flat_version,
				'deps' => array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-autocomplete',
					self::PREFIX . 'select2',
					self::PREFIX . 'select2-translations',
				),
				'in_footer' => false,
			),
			array(
				'name' => 'featured-image',
				'src'  => 'wp-seo-featured-image-' . $flat_version,
				'deps' => array(
					'jquery'
				),
			),
			array(
				'name'      => 'admin-gsc',
				'src'       => 'wp-seo-admin-gsc-' . $flat_version,
				'deps'      => array(),
				'in_footer' => false,
			),
			array(
				'name' => 'post-scraper',
				'src'  => 'wp-seo-post-scraper-' . $flat_version,
				'deps' => array(
					self::PREFIX . 'replacevar-plugin',
					self::PREFIX . 'shortcode-plugin',
					'wp-util',
				),
			),
			array(
				'name' => 'term-scraper',
				'src'  => 'wp-seo-term-scraper-' . $flat_version,
				'deps' => array(
					self::PREFIX . 'replacevar-plugin',
				),
			),
			array(
				'name' => 'replacevar-plugin',
				'src'  => 'wp-seo-replacevar-plugin-' . $flat_version,
			),
			array(
				'name' => 'shortcode-plugin',
				'src'  => 'wp-seo-shortcode-plugin-' . $flat_version,
			),
			array(
				'name' => 'recalculate',
				'src'  => 'wp-seo-recalculate-' . $flat_version,
				'deps' => array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-progressbar',
				),
			),
			array(
				'name' => 'primary-category',
				'src'  => 'wp-seo-metabox-category-' . $flat_version,
				'deps' => array(
					'jquery',
					'wp-util',
				),
			),
			array(
				'name'   => 'select2',
				'src'    => 'select2/select2',
				'suffix' => '.min',
				'deps'   => array(
					'jquery',
				),
				'version' => '4.0.3',
			),
			array(
				'name' => 'select2-translations',
				'src'  => 'select2/i18n/' . $select2_language,
				'deps' => array(
					'jquery',
					self::PREFIX . 'select2',
				),
				'version' => '4.0.3',
				'suffix' => '',
			),
			array(
				'name' => 'configuration-wizard',
				'src'  => 'configuration-wizard-' . $flat_version,
				'deps' => array(
					'jquery',
				),
			),
		);
	}

	/**
	 * Returns the styles that need to be registered.
	 *
	 * @TODO data format is not self-documenting. Needs explanation inline. R.
	 *
	 * @return array styles that need to be registered.
	 */
	private function styles_to_be_registered() {
		$flat_version = $this->flatten_version( WPSEO_VERSION );

		return array(
			array(
				'name' => 'admin-css',
				'src'  => 'yst_plugin_tools-' . $flat_version,
				'deps' => array( self::PREFIX . 'toggle-switch' ),
			),
			array(
				'name'   => 'toggle-switch',
				'src'    => 'toggle-switch-' . $flat_version,
			),
			array(
				'name' => 'dismissible',
				'src'  => 'wpseo-dismissible-' . $flat_version,
			),
			array(
				'name' => 'alerts',
				'src'  => 'alerts-' . $flat_version,
			),
			array(
				'name' => 'edit-page',
				'src'  => 'edit-page-' . $flat_version,
			),
			array(
				'name' => 'featured-image',
				'src'  => 'featured-image-' . $flat_version,
			),
			array(
				'name' => 'metabox-css',
				'src'  => 'metabox-' . $flat_version,
				'deps' => array(
					self::PREFIX . 'select2',
				),
			),
			array(
				'name' => 'wp-dashboard',
				'src'  => 'dashboard-' . $flat_version,
			),
			array(
				'name' => 'scoring',
				'src'  => 'yst_seo_score-' . $flat_version,
			),
			array(
				'name' => 'snippet',
				'src'  => 'snippet-' . $flat_version,
			),
			array(
				'name' => 'adminbar',
				'src'  => 'adminbar-' . $flat_version,
			),
			array(
				'name' => 'primary-category',
				'src'  => 'metabox-primary-category-' . $flat_version,
			),
			array(
				'name'    => 'select2',
				'src'     => 'dist/select2/select2',
				'suffix'  => '.min',
				'version' => '4.0.1',
				'rtl'     => false,
			),
			array(
				'name' => 'kb-search',
				'src'  => 'kb-search-' . $flat_version,
			),
			array(
				'name' => 'help-center',
				'src'  => 'help-center-' . $flat_version,
			),
			array(
				'name' => 'admin-global',
				'src'  => 'admin-global-' . $flat_version,
			),
			array(
				'name' => 'yoast-components',
				'src'  => 'yoast-components-' . $flat_version,
			),
			array(
				'name' => 'extensions',
				'src'  => 'yoast-extensions-' . $flat_version,
			),
		);
	}
}
