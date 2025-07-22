<?php get_header(); ?>
<div class="portfolio-archive">
    <h1><?php post_type_archive_title(); ?></h1>
    <?php if (have_posts()) : ?>
        <div class="portfolio-grid">
            <?php while (have_posts()) : the_post(); ?>
                <div class="portfolio-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="portfolio-thumbnail"><?php the_post_thumbnail('medium'); ?></div>
                    <?php endif; ?>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php the_excerpt(); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p>No portfolio items found.</p>
    <?php endif; ?>
</div>
<?php get_footer(); ?>