<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://2bytecode.com/author/tassawer
 * @since      1.0.0
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/admin
 * @author     2ByteCode <support@2bytecode.com>
 */
class Digital_Asset_Manager_Admin_Menu {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

    /**
	 * Initialize admin menu.
	 *
	 * @since    1.0.0
	 */
    public function add_admin_menu() {
		
		// Create top-level menu item
		$this->assets_main_page = add_menu_page( 
			__( 'Digital Asset Manager', 'digital-asset-manager' ), //$page_title
			__( 'Assets', 'digital-asset-manager' ), //$menu_title
            'manage_options', // $capability
            'dam-assets', // $menu_slug
            array($this, 'assets_list' ), // $function
			'dashicons-database-add', // $icon_url
			31
		); 
 
		// Create a sub-menu under the top-level menu
		$this->add_asset_sub_page = add_submenu_page( 
			'dam-assets', // $parent_slug
			__( 'Add Asset - Digital Asset Manager', 'digital-asset-manager' ), //$page_title
			__( 'Add Asset', 'digital-asset-manager' ), //$menu_title
			'manage_options', //$capability
			'dam-add-asset', //$menu_slug
			array($this, 'dam_placeholder' ), //$function 
		); 

		// Create a sub-menu under the top-level menu
		$this->tokens_sub_page = add_submenu_page( 
			'dam-assets', // $parent_slug
			__( 'Tokens - Digital Asset Manager', 'digital-asset-manager' ), //$page_title
			__( 'Tokens', 'digital-asset-manager' ), //$menu_title
			'manage_options', //$capability
			'dam-tokens', //$menu_slug
			array($this, 'dam_placeholder' ), //$function 
		);

		add_action( "load-$this->assets_main_page", [ $this , 'screen_option' ] );
		add_filter('set-screen-option', [ $this , 'set_screen'], 20, 3);
		
		
		/**
		 * The class responsible for listing assets using data list
		 */
		require_once 'class-digital-asset-manager-assets-list-table.php';
		$this->assets_list_table = new Digital_Asset_Manager_Assets_List_Table();
    }

	public function assets_list() {
		$this->assets_list_table->display_asset_list();
	}

    public function dam_placeholder() {
        echo "Hi";
    }


	public function set_screen($status, $option, $value) {
		die();
        return $value;
    }

	/**
     * Screen options
     */
    public function screen_option() {

        $option = 'per_page';
        $args = [
            'label' => 'Assets',
            'default' => 20,
            'option' => 'assets_per_page',
        ];

        add_screen_option($option, $args);
    }

}

