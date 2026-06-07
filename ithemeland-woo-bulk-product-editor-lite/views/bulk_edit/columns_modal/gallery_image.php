<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<?php if (!empty($image_ids) && is_array($image_ids)) : ?>
    <?php foreach ($image_ids as $wcbel_image_id) : ?>
        <div class="wcbe-inline-edit-gallery-item">
            <?php echo wp_get_attachment_image(intval($wcbel_image_id)); ?>
            <input type="hidden" class="wcbe-inline-edit-gallery-image-ids" value="<?php echo intval($wcbel_image_id); ?>">
            <button type="button" class="wcbe-inline-edit-gallery-image-item-delete">x</button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>