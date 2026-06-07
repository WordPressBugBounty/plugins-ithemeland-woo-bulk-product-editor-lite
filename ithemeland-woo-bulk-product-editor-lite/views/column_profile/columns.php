<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!empty($grouped_fields)) :
    $wcbel_compatibles = [];
    if (!empty($grouped_fields['compatibles'])) {
        $wcbel_compatibles = $grouped_fields['compatibles'];
        unset($grouped_fields['compatibles']);
    }
?>
    <div class="wcbe-column-profile-fields">
        <?php foreach ($grouped_fields as $wcbel_group_name => $wcbel_column_fields) : ?>
            <?php if (!empty($wcbel_column_fields)) : ?>
                <div class="wcbe-column-profile-fields-group">
                    <div class="group-title">
                        <h3><?php echo esc_html($wcbel_group_name); ?></h3>
                    </div>
                    <ul>
                        <?php foreach ($wcbel_column_fields as $wcbel_name => $wcbel_column_field) : ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="columns[]" value="<?php echo esc_attr($wcbel_name); ?>">
                                    <?php echo esc_html($wcbel_column_field['label']); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php
        endforeach;
        if (!empty($wcbel_compatibles) && is_array($wcbel_compatibles)) : ?>
            <div class="wcbe-column-profile-compatibles-group">
                <strong class="wcbe-column-profile-compatibles-group-title"><?php esc_html_e('Fields from third-party plugins', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong>
                <?php foreach ($wcbel_compatibles as $wcbel_compatible_name => $wcbel_compatible_fields) : ?>
                    <div class="wcbe-column-profile-fields-group">
                        <div class="group-title">
                            <h3><?php echo esc_html($wcbel_compatible_name); ?></h3>
                        </div>
                        <ul>
                            <?php foreach ($wcbel_compatible_fields as $wcbel_compatible_field_name => $wcbel_compatible_field) : ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="columns[]" value="<?php echo esc_attr($wcbel_compatible_field_name); ?>">
                                        <?php echo esc_html($wcbel_compatible_field['label']); ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>