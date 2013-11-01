<?php
/*
Name: WPU Base Plugin Utilities
Version: 1.0
*/

class wpuBasePluginUtilities {

    public $version = 1.0;

    /* ----------------------------------------------------------
      Requests
    ---------------------------------------------------------- */

    function get_pager_limit( $perpage, $table ) {
        global $wpdb;
        $elements_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$table );
        $max_pages = ceil( $elements_count / $perpage );
        $pagenum = ( isset( $_GET['pagenum'] ) && is_numeric( $_GET['pagenum'] ) ? $_GET['pagenum'] : 1 );
        $pagenum = min( $pagenum, $max_pages );
        $limit = 'LIMIT ' . ( $pagenum * $perpage - $perpage ) . ', '.$perpage;

        return array(
            'pagenum' => $pagenum,
            'max_pages' => $max_pages,
            'limit' => $limit,
        );

    }

    /* ----------------------------------------------------------
      Display
    ---------------------------------------------------------- */

    function get_wrapper_start( $title ) {
        return '<div class="wrap"><div id="icon-options-general" class="icon32"></div><h2 class="title">'.$title.'</h2><br />';
    }

    function get_wrapper_end() {
        return '</div>';
    }

    function get_admin_table( $values, $args = array() ) {
        $pagination = '';
        if ( isset( $args['pagenum'], $args['max_pages'] ) ) {
            $page_links = paginate_links( array(
                    'base' => add_query_arg( 'pagenum', '%#%' ),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $args['max_pages'],
                    'current' => $args['pagenum']
                ) );

            if ( $page_links ) {
                $pagination = '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
            }
        }

        $content = '<table class="widefat">';
        if ( isset( $args['columns'] ) && is_array( $args['columns'] ) && !empty( $args['columns'] ) ) {
            $labels = '<tr><th>' . implode( '</th><th>', $args['columns'] ).'</th></tr>';
            $content .= '<thead>'.$labels.'</thead>';
            $content .= '<tfoot>'.$labels.'</tfoot>';
        }
        $content .= '<tbody>';
        foreach ( $values as $id => $vals ) {
            $content .= '<tr>';
            foreach ( $vals as $val ) {
                $content .= '<td>'.$val.'</td>';
            }
            $content .= '</tr>';
        }
        $content .= '</tbody>';
        $content .= '</table>';
        $content .= $pagination;

        return $content;
    }
}
