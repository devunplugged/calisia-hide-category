<h1><?php echo $args['title']; ?></h1>
<form action="options.php" method="post">
    <?php 
        settings_fields( 'calisia-hide-category-option-group' );
        do_settings_sections( 'calisia-hide-category-settings-page' ); 
    ?>
    <input name="submit" class="button button-primary" type="submit" value="<?php _e( 'Save', 'calisia-hide-category' ); ?>" />
</form>