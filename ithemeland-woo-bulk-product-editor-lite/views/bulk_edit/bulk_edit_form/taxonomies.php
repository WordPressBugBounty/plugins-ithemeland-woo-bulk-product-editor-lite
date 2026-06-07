<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_items = $edit_form_items['taxonomies'];

foreach ($wcbel_items as $wcbel_group_name => $wcbel_group_item):
    $wcbel_field_id = 'wcbe-bulk-edit-form-' . esc_attr($wcbel_group_name);
    $wcbel_disabled = isset($wcbel_group_item['disabled']) && $wcbel_group_item['disabled'] ? 'disabled="disabled"' : '';
?>
    <div class="wcbe-form-group">
        <div>
            <strong><?php echo esc_html($wcbel_group_item['label']); ?></strong>
            <hr>
            <div class="wcbe-mb20"></div>
        </div>
    </div>

    <?php if (!empty($taxonomies[$wcbel_group_name])): ?>
        <?php foreach ($taxonomies[$wcbel_group_name] as $wcbel_name => $wcbel_taxonomy):
            if (in_array($wcbel_name, ['pos_product_visibility'])) {
                continue;
            }
            $wcbel_tax_field_id = $wcbel_field_id . '-' . esc_attr($wcbel_name);
            $wcbel_tax_disabled = isset($wcbel_group_item['disabled']) && $wcbel_group_item['disabled'] && ($wcbel_group_name === 'attribute' || $wcbel_taxonomy['label'] === 'Brands');
        ?>
            <div class="wcbe-form-group" data-name="<?php echo esc_attr($wcbel_name); ?>" data-type="<?php echo esc_attr($wcbel_group_item['update_type']); ?>">
                <div>
                    <label for="<?php echo esc_attr($wcbel_tax_field_id); ?>"><?php echo esc_html($wcbel_taxonomy['label']); ?></label>

                    <select <?php echo ($wcbel_tax_disabled) ? 'disabled="disabled"' : ''; ?>
                        id="<?php echo esc_attr($wcbel_tax_field_id); ?>-operator"
                        data-field="operator"
                        title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                        <?php foreach ($wcbel_group_item['operators'] as $wcbel_operator_name => $wcbel_operator_label): ?>
                            <option value="<?php echo esc_attr($wcbel_operator_name); ?>"><?php echo esc_html($wcbel_operator_label); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select <?php echo ($wcbel_tax_disabled) ? 'disabled="disabled"' : ''; ?>
                        class="wcbe-select2-taxonomies wcbe-select2-item"
                        data-output="<?php echo ($wcbel_name == 'product_tag') ? 'slug' : 'term_id'; ?>"
                        data-field="value"
                        id="<?php echo esc_attr($wcbel_tax_field_id); ?>"
                        multiple>
                    </select>

                    <?php if ($wcbel_group_name === 'attribute' && isset($wcbel_group_item['attribute_fields'])): ?>
                        <div style="width: 100%; float: left; padding: 8px 0 10px 180px; box-sizing: border-box;">
                            <?php foreach ($wcbel_group_item['attribute_fields'] as $wcbel_field_name => $wcbel_field_config): ?>
                                <label for="<?php echo esc_attr($wcbel_tax_field_id) . '-' . esc_attr($wcbel_field_name); ?>" style="width: auto; padding-right: 8px; line-height: 28px; font-size: 13px;">
                                    <?php echo esc_html($wcbel_field_config['label']); ?>
                                </label>
                                <select <?php echo ($wcbel_tax_disabled) ? 'disabled="disabled"' : ''; ?>
                                    id="<?php echo esc_attr($wcbel_tax_field_id) . '-' . esc_attr($wcbel_field_name); ?>"
                                    data-field="attribute_<?php echo esc_attr($wcbel_field_name); ?>"
                                    style="width: auto; height: 28px; font-size: 13px;">
                                    <?php foreach ($wcbel_field_config['options'] as $wcbel_value => $wcbel_label): ?>
                                        <option value="<?php echo esc_attr($wcbel_value); ?>"><?php echo esc_html($wcbel_label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($wcbel_tax_disabled): ?>
                        <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="wcbe-alert wcbe-alert-warning">
            <span>
                <?php echo wp_kses(sprintf(
                    'There is not any added %s',
                    $wcbel_group_name === 'taxonomy' ? esc_html__('Taxonomies', 'ithemeland-woo-bulk-product-editor-lite') : esc_html__('Attributes', 'ithemeland-woo-bulk-product-editor-lite')
                ), Sanitizer::allowed_html()); ?>
            </span>
        </div>
    <?php endif; ?>
<?php endforeach; ?>