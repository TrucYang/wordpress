<?php
/**
 * All Widget related functions
 */
namespace WPPlugines\Product_View_Count\App;

use Codexpert\Plugin\Base;
use WP_Widget;
use WP_Query;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Widget
 */
class Widget extends Base {

    public $plugin;

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->plugin   = $plugin;
        $this->slug     = $this->plugin['TextDomain'];
        $this->name     = $this->plugin['Name'];
        $this->version  = $this->plugin['Version'];
    }

    /**
     * Registers the Popular Products Widget
     */
    public function register_product_view_count_widget() {
        register_widget( Product_View_Count_Popular_Widget::class );
    }
}

/**
 * Popular Products Widget Class
 */
class Product_View_Count_Popular_Widget extends WP_Widget {

    /**
     * Widget Constructor
     */
    public function __construct() {
        parent::__construct(
            'product_view_count_popular_widget', // Widget ID
            __( 'Popular Products', 'product-view-count' ), // Widget Name
            array( 'description' => __( 'Displays the most viewed products.', 'product-view-count' ) )
        );
    }

    /**
     * Frontend display of the widget
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        // Display popular products
        $this->display_popular_products( $instance );

        echo $args['after_widget'];
    }

    /**
     * Backend widget form
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Popular Products', 'product-view-count' );
        $count = ! empty( $instance['count'] ) ? $instance['count'] : 5;
        $order = ! empty( $instance['order'] ) ? $instance['order'] : 'DESC';

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'product-view-count' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of Products:', 'product-view-count' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Sort Order:', 'product-view-count' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
                <option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php _e( 'ASC', 'product-view-count' ); ?></option>
                <option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php _e( 'DESC', 'product-view-count' ); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * Save widget settings
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['count'] = (int) $new_instance['count'];
        $instance['order'] = strip_tags( $new_instance['order'] );
        return $instance;
    }

    /**
     * Display the popular products
     */
    private function display_popular_products( $instance ) {
        $count = isset( $instance['count'] ) ? $instance['count'] : 5;
        $order = isset( $instance['order'] ) ? $instance['order'] : 'DESC';

        // Query WooCommerce products
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $count,
            'meta_key'       => 'product_view_count',
            'orderby'        => 'meta_value_num',
            'order'          => $order, // Use the order selected in the widget settings
        );

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            echo '<ul class="popular-products">';
            while ( $query->have_posts() ) {
                $query->the_post();
                echo '<li>';
                echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                echo ' (' . get_post_meta( get_the_ID(), 'product_view_count', true ) . ' views)';
                echo '</li>';
            }
            echo '</ul>';
        } 
        else {
            echo '<p>' . __( 'No products found.', 'product-view-count' ) . '</p>';
        }

        wp_reset_postdata();
    }
}
