<?php 
/**
 * Template Name: Mula Fail Redirect
 */
?>

<?php if ( current_user_can( 'manage_options' ) ) : ?>
    <?php $assoc_array = json_decode(file_get_contents('php://input'), true); ?>
    <?php $callbackData = !empty($_POST)? $_POST : $assoc_array; ?>
    <!-- use the data as appropriate to you -->
    <pre>
        <?php echo "Mula callback data: "?>
        <?php echo json_encode($callbackData); ?>
    </pre>
<?php else: ?>
    <?php header('Location: ' . get_home_url()); ?>
<?php endif; ?>