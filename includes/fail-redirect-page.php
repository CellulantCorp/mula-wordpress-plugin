<?php
/**
 * Template Name: Mula Fail Redirect
 */

get_header();
?>

<main class="mula-redirect-page-view">
    <h4 class="mula-failed-notice">Payment failed!</h4>
    <p>Sorry, for the inconvinience!</p>
    <div>
        <a class="redirect-page-options" href="<?php echo get_home_url(); ?>">Home</a>
        <span>&nbsp;|&nbsp;</span>
        <a class="redirect-page-options" href="<?php echo get_home_url() . '/shop'; ?>">Shop</a>
    </div>
</main>

<?php get_footer(); ?>