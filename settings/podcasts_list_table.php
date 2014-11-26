<?php
namespace PodloveSubscribeButton;

if( ! class_exists( 'WP_List_Table' ) ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Podcast_List_Table extends \WP_List_Table {

	function __construct(){
		global $status, $page;
		        
		// Set parent defaults
		parent::__construct( array(
		    'singular'  => 'feed',   // singular name of the listed records
		    'plural'    => 'feeds',  // plural name of the listed records
		    'ajax'      => false  // does this table support ajax?
		) );
	}
	
	function column_name( $podcast ) {

		$actions = array(
			'edit'   => Settings\Podcasts::get_action_link( $podcast, __( 'Edit', 'podlove' ), 'edit' ),
			'delete' => Settings\Podcasts::get_action_link( $podcast, __( 'Delete', 'podlove' ), 'confirm_delete' )
		);
	
		return sprintf('%1$s %2$s',
		    /*$1%s*/ $podcast->title . '<br><code>[podlove-subscribe-button id="' . $podcast->name . '"]</code>',
		    /*$3%s*/ $this->row_actions( $actions )
		);
	}

	function column_button_preview( $podcast ) {

		if ( ! $podcast->feeds )
			return;

		return $podcast->button();
	}
	
	function column_id( $podcast ) {
		return $podcast->id;
	}

	function get_columns(){
		return array(
			'name'    => __( 'Title & Shortcode', 'podlove' ),
			'button_preview'    => __( 'Preview', 'podlove' )
		);
	}
	
	function prepare_items() {
		// number of items per page
		$per_page = 1000;
		
		// define column headers
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		// retrieve data
		// TODO select data for current page only
		$data = \PodloveSubscribeButton\Model\Podcast::all();
		
		// get current page
		$current_page = $this->get_pagenum();
		// get total items
		$total_items = count( $data );
		// extrage page for current page only
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ) , $per_page );
		// add items to table
		$this->items = $data;
		
		// register pagination options & calculations
		$this->set_pagination_args( array(
		    'total_items' => $total_items,
		    'per_page'    => $per_page,
		    'total_pages' => ceil( $total_items / $per_page )
		) );
	}

}
