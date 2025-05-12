<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $new_form_items['taxonomies'];
$taxonomies = $taxonomies;

foreach ($items as $group_name => $group_item):
    $field_id = 'wcbe-bulk-new-form-' . esc_attr($group_name);
    $disabled = isset($group_item['disabled']) && $group_item['disabled'] ? 'disabled="disabled"' : '';
?>
    <div class="wcbe-form-group">
        <div>
            <strong><?php echo esc_html($group_item['label']); ?></strong>
            <hr>
            <div class="wcbe-mb20"></div>
        </div>
    </div>

    <?php if (!empty($taxonomies[$group_name])): ?>
        <?php foreach ($taxonomies[$group_name] as $name => $taxonomy):
            $tax_field_id = $field_id . '-' . esc_attr($name);
            $tax_disabled = isset($group_item['disabled']) && $group_item['disabled'] && ($group_name === 'attribute' || $taxonomy['label'] === 'Brands');
        ?>
            <div class="wcbe-form-group" data-name="<?php echo esc_attr($name); ?>" data-type="<?php echo esc_attr($group_item['update_type']); ?>">
                <div>
                    <label for="<?php echo esc_attr($tax_field_id); ?>"><?php echo esc_html($taxonomy['label']); ?></label>

                    <select <?php echo ($tax_disabled) ? 'disabled="disabled"' : ''; ?>
                        id="<?php echo esc_attr($tax_field_id); ?>-operator"
                        data-field="operator"
                        title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                        <?php foreach ($group_item['operators'] as $operator_name => $operator_label): ?>
                            <option value="<?php echo esc_attr($operator_name); ?>"><?php echo esc_html($operator_label); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select <?php echo ($tax_disabled) ? 'disabled="disabled"' : ''; ?>
                        class="wcbe-select2-taxonomies wcbe-select2-item"
                        data-output="<?php echo ($name == 'product_tag') ? 'slug' : 'term_id'; ?>"
                        data-field="value"
                        id="<?php echo esc_attr($tax_field_id); ?>"
                        multiple>
                    </select>

                    <?php if ($group_name === 'attribute' && isset($group_item['attribute_fields'])): ?>
                        <div style="width: 100%; float: left; padding: 8px 0 10px 180px; box-sizing: border-box;">
                            <?php foreach ($group_item['attribute_fields'] as $field_name => $field_config): ?>
                                <label for="<?php echo esc_attr($tax_field_id) . '-' . esc_attr($field_name); ?>" style="width: auto; padding-right: 8px; line-height: 28px; font-size: 13px;">
                                    <?php echo esc_html($field_config['label']); ?>
                                </label>
                                <select <?php echo ($tax_disabled) ? 'disabled="disabled"' : ''; ?>
                                    id="<?php echo esc_attr($tax_field_id) . '-' . esc_attr($field_name); ?>"
                                    data-field="attribute_<?php echo esc_attr($field_name); ?>"
                                    style="width: auto; height: 28px; font-size: 13px;">
                                    <?php foreach ($field_config['options'] as $value => $label): ?>
                                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($tax_disabled): ?>
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
                    $group_name === 'taxonomy' ? esc_html__('Taxonomies', 'ithemeland-woo-bulk-product-editor-lite') : esc_html__('Attributes', 'ithemeland-woo-bulk-product-editor-lite')
                ), Sanitizer::allowed_html()); ?>
            </span>
        </div>
    <?php endif; ?>
<?php endforeach; ?>