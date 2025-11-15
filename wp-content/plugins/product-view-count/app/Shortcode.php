<?php
/**
 * Handles Product Grid Shortcode functionality with WooCommerce design and pagination.
 */
namespace WPPlugines\Product_View_Count\App;

use Codexpert\Plugin\Base;
use WP_Query;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Shortcode
 */
class Shortcode extends Base {

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
     * Render the Product Grid Shortcode with pagination.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_product_view_grid( $atts ) {
        $atts = shortcode_atts(
            array(
                'columns'           => 4,
                'orderby'           => 'view_count',
                'order'             => 'DESC',
                'products_per_page' => 6,
            ),
            $atts,
            'product_view_grid'
        );

        $this->enqueue_wc_styles();

        $paged  = max( 1, get_query_var( 'paged' ) );
        $args   = array(
            'post_type'      => 'product',
            'posts_per_page' => $atts['products_per_page'],
            'meta_key'       => 'product_view_count',
            'orderby'        => $atts['orderby'] === 'view_count' ? 'meta_value_num' : $atts['orderby'],
            'order'          => $atts['order'],
            'paged'          => $paged,
        );

        $query = new WP_Query( $args );

        ob_start();

        if ( $query->have_posts() ) {
            echo '<div class="woocommerce"><ul class="products columns-' . esc_attr( $atts['columns'] ) . '">';

            wc_set_loop_prop( 'columns', $atts['columns'] );

            while ( $query->have_posts() ) {
                $query->the_post();
                wc_get_template( 'content-product.php' );
            }

            echo '</ul></div>';

            $this->render_pagination( $query );
            
            wp_reset_postdata();
        } 
        else {
            echo '<p>' . __( 'No products found.', 'product-view-count' ) . '</p>';
        }

        return ob_get_clean();
    }

    /**
     * Render pagination links.
     *
     * @param WP_Query $query WP_Query instance.
     */
    private function render_pagination( $query ) {
        $big = 999999999;

        $pagination = paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'total'     => $query->max_num_pages,
            'prev_text' => __( '←', 'product-view-count' ),
            'next_text' => __( '→', 'product-view-count' ),
            'type'      => 'list',
        ) );

        if ( $pagination ) {
            echo '<div class="woocommerce-pagination">';
            echo $pagination;
            echo '</div>';
        }
    }

    /**
     * Enqueue WooCommerce styles and scripts if not already loaded.
     */
    private function enqueue_wc_styles() {
        if ( ! wp_style_is( 'woocommerce-general', 'enqueued' ) ) {
            wp_enqueue_style( 'woocommerce-general' );
        }

        if ( ! wp_style_is( 'woocommerce-layout', 'enqueued' ) ) {
            wp_enqueue_style( 'woocommerce-layout' );
        }

        if ( ! wp_style_is( 'woocommerce-smallscreen', 'enqueued' ) ) {
            wp_enqueue_style( 'woocommerce-smallscreen' );
        }
    }
}
