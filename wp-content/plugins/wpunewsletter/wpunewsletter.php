<?php
/*
Plugin Name: WP Utilities Newsletter
Description: Newsletter
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Admin page & menus
---------------------------------------------------------- */

// Menu item
add_action( 'admin_menu', 'wpunewsletter_menu_page' );
function wpunewsletter_menu_page() {
    add_menu_page( 'Newsletter', 'Newsletter', 'manage_options', 'wpunewsletter', 'wpunewsletter_page' );
}

// Admin page content
function wpunewsletter_page() {
    global $wpdb;
    $table_name = $wpdb->prefix."wpunewsletter_subscribers";

    echo '<div class="wrap"><div id="icon-options-general" class="icon32"></div><h2>Newsletter</h2>';

    // Obtain results
    $results = $wpdb->get_results( "SELECT * FROM ".$table_name );
    $nb_results = count( $results );

    // If empty
    if ( $nb_results < 1 ) {
        // - Display blank slate message
        echo '<p>No subscriber for now.</p>';
    }
    else {
        // - Display results
        echo '<table class="widefat">';
        $cols = '<tr><th>ID</th><th>Email</th><th>Date</th></tr>';
        echo '<thead>'.$cols.'</thead>';
        echo '<tfoot>'.$cols.'</tfoot>';
        foreach ( $results as $result ) {
            echo '<tbody><tr>
        <td>'.$result->id.'</td>
        <td>'.$result->email.'</td>
        <td>'.$result->date_register.'</td>
        </tr></tbody>';
        }
        echo '</table>';
    }
    echo '</div>';
}

/* ----------------------------------------------------------
  Widget
---------------------------------------------------------- */

$wpunewsletter_messages = array();

// Create widget Form
add_action( 'widgets_init', 'wpunewsletter_form_register_widgets' );
function wpunewsletter_form_register_widgets() {
    register_widget( 'wpunewsletter_form' );
}
class wpunewsletter_form extends WP_Widget {
    function wpunewsletter_form() {parent::WP_Widget( false,
            '[WPU] Newsletter Form',
            array( 'description' => 'Newsletter Form' )
        );}
    function form( $instance ) {}
    function update( $new_instance, $old_instance ) {return $new_instance;}
    function widget( $args, $instance ) {
        global $wpunewsletter_messages;
        echo $args['before_widget'];
        if ( !empty( $wpunewsletter_messages ) ) {
            echo '<p>'.implode( '<br />', $wpunewsletter_messages ).'</p>';
        } ?>
        <form action="" method="post">
            <div>
                <label for="wpunewsletter_email"><?php echo __( 'Email', 'wputh' ); ?></label>
                <input type="email" name="wpunewsletter_email" id="wpunewsletter_email" value="" required />
                <button type="submit" class="cssc-button"><?php echo __( 'Register', 'wputh' ); ?></button>
            </div>
        </form>
        <?php echo $args['after_widget'];
    }
}

// Widget POST Action
add_action( 'init', 'wpunewsletter_postaction' );
function wpunewsletter_postaction() {
    global $wpunewsletter_messages, $wpdb;
    $table_name = $wpdb->prefix."wpunewsletter_subscribers";
    // If there is a valid email address
    if ( isset( $_POST['wpunewsletter_email'] ) && filter_var( $_POST['wpunewsletter_email'] , FILTER_VALIDATE_EMAIL ) ) {
        // Is it already in our base ?
        $testbase = $wpdb->get_row( $wpdb->prepare( 'SELECT email FROM '.$table_name.' WHERE email = %s', $_POST['wpunewsletter_email'] ) );
        if ( isset( $testbase->email ) ) {
            $wpunewsletter_messages[] = __( 'This mail is already registered', 'wputh' );
        }
        else {
            $insert = $wpdb->insert(
                $table_name,
                array(
                    'email' => $_POST['wpunewsletter_email'],
                )
            );
            if ( $insert === false ) {
                $wpunewsletter_messages[] = __( "This mail can't be registered", 'wputh' );
            }
            else {
                $wpunewsletter_messages[] = __( 'This mail is now registered', 'wputh' );
            }
        }
    }
}

/* ----------------------------------------------------------
  Hooks Install
---------------------------------------------------------- */

register_activation_hook( __FILE__, 'wpunewsletter_activate' );
function wpunewsletter_activate() {
    global $wpdb;
    // Create or update database
    $table_name = $wpdb->prefix."wpunewsletter_subscribers";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( "CREATE TABLE ".$table_name." (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `email` varchar(100) DEFAULT NULL,
        `date_register` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `is_valid` tinyint(1) unsigned DEFAULT '0' NOT NULL,
        PRIMARY KEY (`id`)
    );" );
}
