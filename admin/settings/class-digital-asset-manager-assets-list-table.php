<?php

/**
 * The admin-specific functionality of the plugin. List assets record in list table format.
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

if ( !class_exists('WP_List_Table') ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Digital_Asset_Manager_Assets_List_Table extends WP_List_Table {

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {

		parent::__construct( [
			'singular' => __( 'Asset', 'digital-asset-manager' ), //singular name of the listed records
			'plural'   => __( 'Assets', 'digital-asset-manager' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );
	}

    /**
     * Retrieve assets’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_assets( $per_page = 5, $page_number = 1 ) {

        global $wpdb;
    
        $sql = "SELECT * FROM {$wpdb->prefix}dam_records";
    
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
    
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
    
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
    
        return $result;
    }

    /**
     * Delete an asset record.
     *
     * @param int $id asset ID
     */
    public static function delete_asset( $id ) {
        global $wpdb;
    
        $wpdb->delete(
            "{$wpdb->prefix}dam_records",
            [ 'id' => $id ],
            [ '%d' ]
        );
    }

    /**
     * Returns the count of assets in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
    
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}dam_records";
    
        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No assets avaliable.', 'digital-asset-manager' );
    }

    /**
     * Associative array of columns
     * List of column that appear as a table head
     *
     * @return array
     */
    function get_columns() {
        $columns = [
        'cb'                    => '<input type="checkbox" />',
        'srno'                  => __( 'Sr #', 'digital-asset-manager' ),
        'name'                  => __( 'Name', 'digital-asset-manager' ),
        'version'               => __( 'Version', 'digital-asset-manager' ),
        'record_type'           => __( 'Type', 'digital-asset-manager' ),
        'drive_url'             => __( 'Download', 'digital-asset-manager' ),
        'license_key'           => __( 'License Key', 'digital-asset-manager' ),
        'token'                 => __( 'Token', 'digital-asset-manager' ),
        'account_credentials'   => __( 'Credentials', 'digital-asset-manager' ),
        'comments'              => __( 'Comments', 'digital-asset-manager' ),
        ];
    
        return $columns;
    }

    /**
     * Render a column value when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'srno':
            case 'version':
            case 'record_type':
            case 'license_key':
            case 'account_credentials':
            case 'comments':
                return $item[ $column_name ];
        default:
            return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
        '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'dam_delete_assets' );
    
        $title = '<strong><a href="' . $item['live_url'] . '" target="_blank">' . $item['name'] . '</a></strong>';
    
        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&asset=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        ];
    
        return $title . $this->row_actions( $actions );
    }

    /**
     * Method for Drive URL column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_drive_url( $item ) {

        $download = '<strong><a href="' . $item['drive_url'] . '">Download</a></strong>';
    
        return $download;
    }

    /**
     * Method for token column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_token( $item ) {

        $title = '<strong>' . $item['name'] . '</strong>';
    
        return $title;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true ),
            'record_type' => array( 'record_type', false )
        );
    
        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
        'bulk-delete' => 'Delete'
        ];
    
        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
        
        $this->_column_headers = $this->get_column_info();
    
        /** Process bulk action */
        $this->process_bulk_action();
    
        $per_page     = $this->get_items_per_page( 'assets_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
    
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );
    
    
        $this->items = self::get_assets( $per_page, $current_page );
    }

    /**
     * Takes care of the deleting customers record either when the delete link is clicked or
     * when a group of records is checked and the delete option is selected from the bulk action
     */
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
      
          // In our file that handles the request, verify the nonce.
          $nonce = esc_attr( $_REQUEST['_wpnonce'] );
      
          if ( ! wp_verify_nonce( $nonce, 'dam_delete_asset' ) ) {
            die( 'Go get a life script kiddies' );
          }
          else {
            self::delete_asset( absint( $_GET['asset'] ) );
      
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
          }
      
        }
      
        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
             || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {
      
          $delete_ids = esc_sql( $_POST['bulk-delete'] );
      
          // loop over the array of record IDs and delete them
          foreach ( $delete_ids as $id ) {
            self::delete_asset( $id );
          }
      
          wp_redirect( esc_url( add_query_arg() ) );
          exit;
        }
    }


    public function display_asset_list() { ?>
        <div class="wrap">
            <h2>
                <?php echo __('Digital Asset Manager', 'digital-asset-manager'); ?>
                <a href="#" class="page-title-action"><?php echo __('Add New', 'digital-asset-manager'); ?></a>
                <p style="margin-top: 10px;"><?php echo __('Add the ZIP Code(s) that you want to display on custom post type while restricting the content', 'digital-asset-manager') ?></p>
            </h2>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php $this->prepare_items();
        							$this->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
    <?php }

}