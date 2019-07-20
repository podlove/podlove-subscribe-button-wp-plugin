<?php
namespace PodloveSubscribeButton;

if( ! class_exists( 'WP_List_Table' ) ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Button_List_Table extends \WP_List_Table {

	function __construct(){
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
		    'singular'  => 'feed',   // singular name of the listed records
		    'plural'    => 'feeds',  // plural name of the listed records
		    'ajax'      => false  // does this table support ajax?
		) );
	}

	function column_name( $button ) {

		$actions = array(
			'edit'   => Settings\Buttons::get_action_link( $button, __( 'Edit', 'podlove-subscribe-button' ), 'edit' ),
			'delete' => Settings\Buttons::get_action_link( $button, __( 'Delete', 'podlove-subscribe-button' ), 'confirm_delete' )
		);

		return sprintf('%1$s %2$s',
		    /*$1%s*/ sanitize_title($button->title) . '<br><code>[podlove-subscribe-button button="' . sanitize_text_field($button->name) . '"]</code>',
		    /*$3%s*/ $this->row_actions( $actions )
		);
	}

	function column_button_preview( $button ) {

		if ( ! $button->feeds ) {
			return '<code>' . __( 'No preview. Please set a feed.', 'podlove-subscribe-button' ) . '</code>';
		} else {

			$preview = "<div class='podlove-button-preview-container'>";
			$preview .= $button->render(
				'big',
				'false',
				get_option( 'podlove_subscribe_button_default_style', 'filled' ),
				'rectangle'
			);
			$preview .= "</div>";

			return $preview;

		}

	}


	function column_id( $button ) {
		return $button->id;
	}

	function get_columns(){
		return array(
			'name'    => __( 'Title & Shortcode', 'podlove-subscribe-button' ),
			'button_preview'    => __( 'Preview', 'podlove-subscribe-button' ),
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
		$data = ( is_network_admin() ? \PodloveSubscribeButton\Model\NetworkButton::all() : \PodloveSubscribeButton\Model\Button::all() );

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
