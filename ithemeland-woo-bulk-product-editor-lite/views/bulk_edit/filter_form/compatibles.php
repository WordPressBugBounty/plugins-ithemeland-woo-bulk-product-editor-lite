<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (empty($has_compatible_fields) || empty($compatible_fields_status) || !is_array($compatible_fields_status) || empty($compatible_tabs_label) || empty($compatibles)) {
    return '';
}
?>

<!-- tabs title-->
<ul class="wcbe-sub-tab-titles">
    <?php
    $wcbel_i = 1;
    foreach ($compatible_fields_status as $wcbel_key => $wcbel_status) :
        if (!$wcbel_status || empty($compatible_tabs_label[$wcbel_key]) || $wcbel_key == 'pricing') {
            continue;
        }
    ?>
        <li><a href="#" class="wcbe-sub-tab-title <?php echo ($wcbel_i == 1) ? 'active' : ''; ?>" data-content="<?php echo esc_attr($wcbel_key); ?>"><?php echo esc_html($compatible_tabs_label[$wcbel_key]); ?></a></li>
    <?php
        $wcbel_i++;
    endforeach;
    ?>
</ul>

<!-- tabs content -->
<div class="wcbe-sub-tab-contents">
    <?php
    $wcbel_i = 1;
    foreach ($compatible_fields_status as $wcbel_key => $wcbel_status) :
        if (!$wcbel_status || empty($compatible_tabs_label[$wcbel_key]) || empty($compatibles[$wcbel_key]) || $wcbel_key == 'pricing') {
            continue;
        }
    ?>
        <div class="wcbe-sub-tab-content" data-content="<?php echo esc_attr($wcbel_key); ?>" style="<?php echo ($wcbel_i == 1) ? 'display: block;' : ''; ?>">
            <?php
            foreach ($compatibles[$wcbel_key] as $wcbel_plugin_key => $wcbel_data) {
                if (!$wcbel_data['status'] || !file_exists($wcbel_data['filter_fields'])) {
                    continue;
                }

                include $wcbel_data['filter_fields'];
            }
            ?>
        </div>
    <?php
        $wcbel_i++;
    endforeach;
    ?>
</div>