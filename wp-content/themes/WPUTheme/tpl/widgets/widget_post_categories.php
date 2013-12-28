<?php
add_action( 'widgets_init', 'widget_post_categories_register_widgets' );
function widget_post_categories_register_widgets() {
    register_widget( 'widget_post_categories' );
}

// Widget Post Categories
class widget_post_categories extends WP_Widget {
    function widget_post_categories() {parent::WP_Widget( false,
            '[WPU] Post Categories',
            array( 'description' => 'Post Categories' )
        );}
    function form( $instance ) {
        $cat_id = 1;
        if ( isset( $instance['cat_id'] ) ) {
            $cat_id = $instance['cat_id'];
        }
        $posts_nb = 3;
        if ( isset( $instance['posts_nb'] ) ) {
            $posts_nb = $instance['posts_nb'];
        }
        ?><p><label for="<?php echo $this->get_field_name( 'posts_nb' ); ?>"><?php _e( 'Number of posts:', 'wputh' ); ?></label><br />
        <input class="widefat" id="<?php echo $this->get_field_id( 'posts_nb' ); ?>" name="<?php echo $this->get_field_name( 'posts_nb' ); ?>" type="text" value="<?php echo esc_attr( $posts_nb ); ?>" /></p><?php
        echo '<p>';
        ?><label for="<?php echo $this->get_field_name( 'cat_id' ); ?>"><?php _e( 'Category:', 'wputh' ); ?></label><br /><?php
        wp_dropdown_categories( array(
                'selected' => $cat_id,
                'name' => $this->get_field_name( 'cat_id' ),
            ) );
        echo '</p>';
    }
    function update( $new_instance, $instance ) {
        if ( isset( $new_instance['cat_id'] ) && ctype_digit( $new_instance['cat_id'] ) ) {
            $instance['cat_id'] = $new_instance['cat_id'];
        }
        if ( isset( $new_instance['posts_nb'] ) && ctype_digit( $new_instance['posts_nb'] ) ) {
            $instance['posts_nb'] = $new_instance['posts_nb'];
        }
        return $instance;
    }
    function widget( $args, $instance ) {
        $cat_id = 1;
        if ( isset( $instance['cat_id'] ) ) {
            $cat_id = $instance['cat_id'];
        }
        $posts_nb = 3;
        if ( isset( $instance['posts_nb'] ) ) {
            $posts_nb = $instance['posts_nb'];
        }
        $wpq_widget_post_cat = new WP_Query( array(
                'posts_per_page' => $posts_nb,
                'cat' => $cat_id
            ) );
        if ( $wpq_widget_post_cat->have_posts() ) {
            echo $args['before_widget'];
            echo $args['before_title'].get_cat_name( $cat_id ).$args['after_title'];
            echo '<ul>';
            while ( $wpq_widget_post_cat->have_posts() ) {
                $wpq_widget_post_cat->the_post();
                echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
            }
            echo '</ul>';
            echo $args['after_widget'];
        }
    }
}
