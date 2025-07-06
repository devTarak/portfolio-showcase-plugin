<?php get_header(); ?>
<div class="single-portfolio">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article <?php post_class(); ?>>
            <h1 class="project_si_title"><?php the_title(); ?></h1>

            <?php if (has_post_thumbnail()) : ?>
                <div class="portfolio-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <!-- Meta Data Section -->
            <div class="portfolio-meta">
                <ul>
                    <?php
                    // Correct meta keys based on debugging output
                    $start_date = get_post_meta(get_the_ID(), '_portfolio_start_date', true);
                    $end_date = get_post_meta(get_the_ID(), '_portfolio_end_date', true);
                    $technologies = get_post_meta(get_the_ID(), '_portfolio_technologies', true);
                    $live_link = get_post_meta(get_the_ID(), '_portfolio_live_link', true);
                    ?>

                    <?php if ($start_date): ?>
                        <li><strong>Project Start Date:</strong> <?php echo esc_html($start_date); ?></li>
                    <?php endif; ?>

                    <?php if ($end_date): ?>
                        <li><strong>Project End Date:</strong> <?php echo esc_html($end_date); ?></li>
                    <?php endif; ?>

                    <?php if ($technologies): ?>
                        <li><strong>Used Technologies:</strong> <?php echo esc_html($technologies); ?></li>
                    <?php endif; ?>

                    <?php if ($live_link): ?>
                        <li><strong>Live Perview Link:</strong> <a href="<?php echo esc_url($live_link); ?>" target="_blank">Visit Project</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="portfolio-content">
                <h6>Project Details: </h6>
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>