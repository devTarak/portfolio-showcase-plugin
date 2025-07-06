<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

class Portfolio_Widget extends Widget_Base {

    public function get_name() {
        return 'portfolio_widget';
    }

    public function get_title() {
        return 'Portfolio';
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        // Add controls for the widget
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'portfolio-widget'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'portfolio-widget'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 6,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Get Portfolio Categories
        $categories = get_terms([
            'taxonomy' => 'portfolio_category',
            'hide_empty' => false,
        ]);

        // Add tabs for each portfolio category
        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<div class="portfolio-tabs">';
            echo '<ul class="portfolio-category-tabs">';
            foreach ($categories as $category) {
                echo '<li data-category-id="' . esc_attr($category->term_id) . '" class="portfolio-category-tab">';
                echo esc_html($category->name);
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';

            // Portfolio Grid
            echo '<div class="portfolio-grid" id="portfolio-grid">';
            $this->render_portfolio_grid(); // Render default grid (no category filter)
            echo '</div>';
        }
    }

    // Function to render the portfolio grid based on category
    private function render_portfolio_grid($category_id = null) {
        $args = [
            'post_type' => 'portfolio', // Query for portfolio post type
            'posts_per_page' => $this->get_settings_for_display('posts_per_page'),
        ];

        if ($category_id) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'portfolio_category',
                    'field'    => 'id',
                    'terms'    => $category_id,
                ],
            ];
        }

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) : $query->the_post();
                echo '<div class="portfolio-item" data-post-id="' . get_the_ID() . '">';
                if (has_post_thumbnail()) {
                    echo '<div class="portfolio-thumbnail">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</div>';
                }
                echo '<h4>' . get_the_title() . '</h4>';
                echo '<p>' . get_the_excerpt() . '</p>';
                echo '</div>';
            endwhile;
            wp_reset_postdata();
        } else {
            echo '<p>No portfolio items found.</p>';
        }
    }
}

add_action('elementor/widgets/widgets_registered', function() {
    Plugin::instance()->widgets_manager->register_widget_type(new Portfolio_Widget());
});