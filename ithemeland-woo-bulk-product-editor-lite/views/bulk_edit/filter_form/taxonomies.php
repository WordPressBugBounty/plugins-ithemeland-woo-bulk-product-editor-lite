<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $filter_form_items['taxonomies'];

foreach ($items as $name => $item):
    if ($name === 'taxonomy_section'):
?>
        <div class="wcbe-form-group">
            <div>
                <strong><?php echo esc_html($item['label']); ?></strong>
                <hr>
                <div class="wcbe-mb20"></div>
            </div>
        </div>

        <?php if (!empty($taxonomies['taxonomy'])) : ?>
            <?php foreach ($taxonomies['taxonomy'] as $tax_name => $taxonomy) :
            ?>
                <div class="wcbe-form-group" data-filter-type="taxonomy" data-field-type="select_multiple" data-name="<?php echo esc_attr($tax_name); ?>">
                    <label for="wcbe-filter-form-product-attr-<?php echo esc_attr($tax_name); ?>"><?php echo esc_html($taxonomy['label']); ?></label>
                    <select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> id="wcbe-filter-form-product-attr-operator-<?php echo esc_attr($tax_name); ?>" title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-field="operator">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/filter_form/operators/taxonomy.php"; ?>
                    </select>

                    <select <?php echo (isset($item['disabled']) && $item['disabled'] && $taxonomy['label'] === 'Brands') ? 'disabled="disabled"' : ''; ?> class="wcbe-select2-taxonomies wcbe-select2-item wcbe-filter-form-select2-option-values" data-output="term_id" data-option-name="<?php echo esc_attr($tax_name); ?>" data-field="value" id="wcbe-filter-form-product-attr-<?php echo esc_attr($tax_name); ?>" multiple></select>
                    <?php if (isset($item['disabled']) && $item['disabled'] && $taxonomy['label'] === 'Brands') : ?>
                        <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="wcbe-alert wcbe-alert-warning">
                <span><?php esc_html_e('There is not any added Custom Taxonomies', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </div>
        <?php endif; ?>

    <?php elseif ($name === 'attribute_section'): ?>
        <div class="wcbe-form-group">
            <div>
                <strong><?php echo esc_html($item['label']); ?></strong>
                <hr>
                <div class="wcbe-mb20"></div>
            </div>
        </div>

        <?php if (!empty($taxonomies['attribute'])) : ?>
            <?php foreach ($taxonomies['attribute'] as $attr_name => $taxonomy) :
            ?>
                <div class="wcbe-form-group" data-filter-type="attribute" data-field-type="select_multiple" data-name="<?php echo esc_attr($attr_name); ?>">
                    <label for="wcbe-filter-form-product-attr-<?php echo esc_attr($attr_name); ?>"><?php echo esc_html($taxonomy['label']); ?></label>
                    <select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> id="wcbe-filter-form-product-attr-operator-<?php echo esc_attr($attr_name); ?>" title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-field="operator">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/filter_form/operators/taxonomy.php"; ?>
                    </select>
                    <select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-select2-taxonomies wcbe-select2-item wcbe-filter-form-select2-option-values" data-output="slug" data-option-name="<?php echo esc_attr($attr_name); ?>" data-field="value" id="wcbe-filter-form-product-attr-<?php echo esc_attr($attr_name); ?>" multiple></select>
                    <?php if (isset($item['disabled']) && $item['disabled']) : ?>
                        <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="wcbe-alert wcbe-alert-warning">
                <span><?php esc_html_e('There is not any added Attributes', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>