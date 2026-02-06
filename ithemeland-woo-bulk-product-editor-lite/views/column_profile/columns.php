<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!empty($grouped_fields)) :
    $compatibles = [];
    if (!empty($grouped_fields['compatibles'])) {
        $compatibles = $grouped_fields['compatibles'];
        unset($grouped_fields['compatibles']);
    }
?>
    <div class="wcbe-column-profile-fields">
        <?php foreach ($grouped_fields as $group_name => $column_fields) : ?>
            <?php if (!empty($column_fields)) : ?>
                <div class="wcbe-column-profile-fields-group">
                    <div class="group-title">
                        <h3><?php echo esc_html($group_name); ?></h3>
                    </div>
                    <ul>
                        <?php foreach ($column_fields as $name => $column_field) : ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="columns[]" value="<?php echo esc_attr($name); ?>">
                                    <?php echo esc_html($column_field['label']); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php
        endforeach;
        if (!empty($compatibles) && is_array($compatibles)) : ?>
            <div class="wcbe-column-profile-compatibles-group">
                <strong class="wcbe-column-profile-compatibles-group-title"><?php esc_html_e('Fields from third-party plugins', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong>
                <?php foreach ($compatibles as $compatible_name => $compatible_fields) : ?>
                    <div class="wcbe-column-profile-fields-group">
                        <div class="group-title">
                            <h3><?php echo esc_html($compatible_name); ?></h3>
                        </div>
                        <ul>
                            <?php foreach ($compatible_fields as $compatible_field_name => $compatible_field) : ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="columns[]" value="<?php echo esc_attr($compatible_field_name); ?>">
                                        <?php echo esc_html($compatible_field['label']); ?>
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