<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Portfolio_Widget extends Widget_Base {

    public function get_name() {
        return 'portfolio_widget';
    }

    public function get_title() {
        return esc_html__( 'Portfolio', 'portfolio-showcase-by-devtarak' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    /**
     * Enqueue the JS script and localize AJAX data
     */
    public function get_script_depends() {
        return [ 'portfolio-script' ];
    }
}

/**
 * Register and enqueue portfolio.js, localize AJAX URL and nonce
 */
add_action( 'wp_enqueue_scripts', function() {
    wp_register_script(
        'portfolio-script',
        plugins_url( 'assets/js/portfolio.js', __FILE__ ), // adjust path if needed
        ['jquery'],
        '1.0',
        true
    );

    wp_localize_script(
        'portfolio-script',
        'portfolio_ajax_obj',
        [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'portfolio_nonce' ),
        ]
    );
} );

/**
 * Render the portfolio widget content
 */
add_action( 'elementor/widgets/widgets_registered', function() {
    class Portfolio_Widget_Renderer extends Portfolio_Widget {

        protected function register_controls() {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__( 'Content', 'portfolio-showcase-by-devtarak' ),
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'posts_per_page',
                [
                    'label'   => esc_html__( 'Posts Per Page', 'portfolio-showcase-by-devtarak' ),
                    'type'    => Controls_Manager::NUMBER,
                    'min'     => 1,
                    'max'     => 20,
                    'step'    => 1,
                    'default' => 6,
                ]
            );

            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();

            $categories = get_terms( [
                'taxonomy'   => 'portfolio_category',
                'hide_empty' => false,
            ] );

            if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                echo '<div class="portfolio-tabs">';
                echo '<ul class="portfolio-category-tabs">';
                foreach ( $categories as $category ) {
                    echo '<li data-category-id="' . esc_attr( $category->term_id ) . '" class="portfolio-category-tab">';
                    echo esc_html( $category->name );
                    echo '</li>';
                }
                echo '</ul>';
                echo '</div>';

                echo '<div class="portfolio-grid" id="portfolio-grid">';
                // Initially render first category's items (optional)
                $first_category_id = $categories[0]->term_id;
                $this->render_portfolio_grid( $first_category_id, $settings['posts_per_page'] );
                echo '</div>';
            } else {
                echo '<p>' . esc_html__( 'No portfolio categories found.', 'portfolio-showcase-by-devtarak' ) . '</p>';
            }
        }

        private function render_portfolio_grid( $category_id = null, $posts_per_page = 6 ) {
            $args = [
                'post_type'      => 'portfolio',
                'posts_per_page' => $posts_per_page,
            ];

            if ( $category_id ) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'portfolio_category',
                        'field'    => 'term_id',
                        'terms'    => $category_id,
                    ],
                ];
            }

            $query = new WP_Query( $args );

            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    echo '<div class="portfolio-item" data-post-id="' . esc_attr( get_the_ID() ) . '">';
                    if ( has_post_thumbnail() ) {
                        echo '<div class="portfolio-thumbnail">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</div>';
                    }
                    echo '<h4>' . esc_html( get_the_title() ) . '</h4>';
                    echo '<p>' . esc_html( get_the_excerpt() ) . '</p>';
                    echo '</div>';
                }
                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html__( 'No portfolio items found.', 'portfolio-showcase-by-devtarak' ) . '</p>';
            }
        }
    }

    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Portfolio_Widget_Renderer() );
} );

/**
 * AJAX callback for filtering portfolio items
 */
add_action( 'wp_ajax_filter_portfolio', 'portfolio_filter_callback' );
add_action( 'wp_ajax_nopriv_filter_portfolio', 'portfolio_filter_callback' );

function portfolio_filter_callback() {
    check_ajax_referer( 'portfolio_nonce', 'nonce' );

    $category_id    = isset( $_POST['category_id'] ) ? intval( $_POST['category_id'] ) : 0;
    $posts_per_page = isset( $_POST['posts_per_page'] ) ? intval( $_POST['posts_per_page'] ) : 6;

    $args = [
        'post_type'      => 'portfolio',
        'posts_per_page' => $posts_per_page,
    ];

    if ( $category_id ) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'portfolio_category',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ],
        ];
    }

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        ob_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            ?>
            <div class="portfolio-item" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="portfolio-thumbnail"><?php the_post_thumbnail( 'medium' ); ?></div>
                <?php endif; ?>
                <h4><?php the_title(); ?></h4>
                <p><?php echo esc_html( get_the_excerpt() ); ?></p>
            </div>
            <?php
        }
        wp_reset_postdata();
        $content = ob_get_clean();
        wp_send_json_success( $content );
    } else {
        wp_send_json_error( '<p>' . esc_html__( 'No portfolio items found.', 'portfolio-showcase-by-devtarak' ) . '</p>' );
    }
}