<?php
/**
 * Template Name: Mula Success Redirect
 */
?>

<?php get_header(); ?>

<main class="mula-redirect-page-view">
    <h4 class="mula-success-notice">Payment successful!</h4>
    <p>Thank you for shopping with us!</p>
    <div>
        <a class="redirect-page-options" href="<?php echo get_home_url(); ?>">Home</a>
        <span>&nbsp;|&nbsp;</span>
        <a class="redirect-page-options" href="<?php echo get_home_url() . '/shop'; ?>">Shop</a>
    </div>
</main>

<?php get_footer(); ?>