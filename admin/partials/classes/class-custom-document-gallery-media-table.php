<?php
/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our theme for this list table is going to be set.
 */
class CDG_Media_Table extends WP_List_Table {


    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'media',     //singular name of the listed records
            'plural'    => 'medias',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'name', it would first see if a method named $this->column_name()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_name() method later on, this method doesn't
     * need to concern itself with any column with a name of 'name'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'name':
            case 'id':
                return $item[$column_name];
            case 'thumbnail':
                ?>
                    <img src="<?php echo $item['thumbnail_url']; ?>" alt="thumbnail" height="60px" width="60px" />
                <?php return;
            case 'sort_order':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'name'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_name} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (media name only)
     **************************************************************************/
    function column_name($item){

        //Build row actions
        $current = ( !empty($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');
        $gallery_id = $item['document_gallery_id'];
        if($current == 'trashed'){
            $actions = array(
                'restore'    => sprintf('<a href="?page=%s&action=%s&id=%s&media=%s">Restore</a>',$_REQUEST['page'], 'restore', $gallery_id, $item['id']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s&media=%s">Delete Permanently</a>',$_REQUEST['page'],'delete', $gallery_id, $item['id']),
            );
        }else{
            $actions = array(
                'view' => sprintf('<a href="%s" target="_blank">%s</a>', $item['document_url'], __('View', 'mgpv_example')),
                'edit'      => sprintf('<a href="?page=%s&gallery-id=%s&id=%s">Edit</a>', CUSTOM_DOCUMENT_GALLERY_PLUGIN_NAME . '-media-edit', $item['document_gallery_id'], $item['id']),
                'trash'    => sprintf('<a href="?page=%s&action=%s&id=%s&media=%s">Trash</a>',$_REQUEST['page'], 'trash', $gallery_id, $item['id']),
            );
        }

        //Return the name contents
        return sprintf('%1$s%3$s',
            /*$1%s*/ $item['name'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (media name only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("media")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and names. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's name text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'id'        => 'ID',
            'name'     => 'name',
            'thumbnail' => 'thumbnail',
            'sort_order' => 'sort_order',
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'sort_order' => array('sort_order', true),     //true means it's already sorted
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $current = ( !empty($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');
        if ($current == 'trashed') {
            $actions = array(
                'restore' => 'Restore',
                'delete' => 'Delete'
            );
        }
        else {
            $actions = array(
                'trash'    => 'Trash'
            );
        }
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function trash($media){
        global $wpdb;

        $table_name = $wpdb->prefix . 'document_media';
        $data = array(
            'deleted_date' => date('Y-m-d H:i:s'),
            );
        $where = array(
            'id' => $media
            );
        $wpdb->update($table_name, $data, $where);

    }

    function restore($media){
        global $wpdb;

        $table_name = $wpdb->prefix . 'document_media';
        $data = array(
            'deleted_date' => NULL
            );
        $where = array(
            'id' => $media
            );
        $wpdb->update($table_name, $data, $where);
    }

    function permanent_delete($media){
        global $wpdb;

        $table_name = $wpdb->prefix . 'document_media';
        $wpdb->delete( $table_name, [ 'ID' => $media ], [ '%d' ] );
    }

    function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if( 'trash'===$this->current_action() ) {
            if(is_array($_REQUEST['media'])){
                foreach ($_REQUEST['media'] as $media) {
                    $this->trash($media);
                }
            }else{
                $this->trash($_REQUEST['media']);
            }
            // echo('Items deleted (or they would be if we had items to delete)!');
        }else if( 'delete'===$this->current_action()) {
            if(is_array($_REQUEST['media'])){
                foreach ($_REQUEST['media'] as $media) {
                    $this->permanent_delete($media);
                }
            }else{
                $this->permanent_delete($_REQUEST['media']);
            }
        }else if( 'restore'===$this->current_action()) {
            if(is_array($_REQUEST['media'])){
                foreach ($_REQUEST['media'] as $set) {
                    $this->restore($set);
                }
            }else{
                $this->restore($_REQUEST['media']);
            }
        }

    }

    //used for the all/publish/trash links typically above tables
    function get_views(){
        global $wpdb;
       $views = array();
       $current = ( !empty($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');
       $gallery_id = 0;
        if (isset($_GET['id'])) {
            $gallery_id = $_GET['id'];
        }
       $table_name = $wpdb->prefix . 'document_media';
       $all_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $table_name . " WHERE document_gallery_id='" . $gallery_id . "' AND deleted_date IS NULL;");
       $delete_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $table_name . " WHERE document_gallery_id='" . $gallery_id . "' AND deleted_date IS NOT NULL;");
       //All link
       $class = ($current == 'all' ? ' class="current"' :'');
       // $all_url = remove_query_arg('customvar');
       $views['all'] = "<a href='?page=" . $_REQUEST['page'] . "&id=" . $gallery_id . "' {$class} >All(" . $all_count . ")</a>";

       // //active link
       // $active_url = add_query_arg('customvar','active');
       // $class = ($current == 'active' ? ' class="current"' :'');
       // $views['active'] = "<a href='{$active_url}' {$class} >Active</a>";

       //deleted link
       // $deleted_url = add_query_arg('customvar','deleted');
       $class = ($current == 'trashed' ? ' class="current"' :'');
       $views['trashed'] = "<a href='?page=" . $_REQUEST['page'] . "&id=" . $gallery_id . "&customvar=trashed' {$class} >Trashed(" . $delete_count . ")</a>";

       return $views;
    }

    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should media $this->items and
     * $this->media_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->media_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & names), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array('id', 'sort_order');
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        // $data = $this->example_data;

        //this gets all/active/deleted
        $current = ( !empty($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');
        $table_name = $wpdb->prefix . 'document_media';
        $id = 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        $query = 'SELECT * FROM ' . $table_name . ' WHERE document_gallery_id=' . $id . ';';
        if ($current == 'all'){
            $query = 'SELECT * FROM ' . $table_name . ' WHERE document_gallery_id=' . $id . ' AND deleted_date IS NULL ORDER BY sort_order;';
        }else if ($current == 'trashed'){
            $query = 'SELECT * FROM ' . $table_name . ' WHERE document_gallery_id=' . $id . ' AND deleted_date IS NOT NULL;';
        }
        $data = $wpdb->get_results($query, ARRAY_A);
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'name'; //If no sort, default to name
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        // usort($data, 'usort_reorder');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);

        /**
         * Decide how many records per page to show
         */

        $per_page = 1;

        if ( $total_items > 0 ) {
            $per_page = $total_items;
        }


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->media_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }


}

// class Update_Media_Order {

//     public static function update_media_order(){
//         $order_list = $_POST['order'];
//         // echo "function working";
//         global $wpdb;
//         $table_name = $wpdb->prefix . 'document_media';

//         for($i = 0; $i<count($order_list); $i++){
//             $data = array(
//                 'sort_order' => $i
//                 );
//             $where = array(
//                 'id' => $order_list[$i]
//                 );
//             $wpdb->update($table_name, $data, $where);
//         }
//     }

// }

// function update_media_order(){
//     $order_list = $_POST['order'];
//     // echo "function working";
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'document_media';

//     for($i = 0; $i<count($order_list); $i++){
//         $data = array(
//             'sort_order' => $i
//             );
//         $where = array(
//             'id' => $order_list[$i]
//             );
//         $wpdb->update($table_name, $data, $where);
//     }
// }
