<?php
/**
 * The template for displaying the Porfolio MDQ Archive
 *
 * @package Porfolio_MDQ
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="mdq-portfolio-archive-container">
            <?php
            // Render the professional portfolio grid
            // The shortcode logic will now handle the title/subtitle from global options
            $shortcode = new MDQ_Shortcode();
            echo $shortcode->render_portfolio( array() );
            ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>
