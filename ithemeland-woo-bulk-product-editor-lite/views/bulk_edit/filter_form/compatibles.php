<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (empty($has_compatible_fields) || empty($compatible_fields_status) || !is_array($compatible_fields_status) || empty($compatible_tabs_label) || empty($compatibles)) {
    return '';
}
?>

<!-- tabs title-->
<ul class="wcbe-sub-tab-titles">
    <?php
    $i = 1;
    foreach ($compatible_fields_status as $key => $status) :
        if (!$status || empty($compatible_tabs_label[$key]) || $key == 'pricing') {
            continue;
        }
    ?>
        <li><a href="#" class="wcbe-sub-tab-title <?php echo ($i == 1) ? 'active' : ''; ?>" data-content="<?php echo esc_attr($key); ?>"><?php echo esc_html($compatible_tabs_label[$key]); ?></a></li>
    <?php
        $i++;
    endforeach;
    ?>
</ul>

<!-- tabs content -->
<div class="wcbe-sub-tab-contents">
    <?php
    $i = 1;
    foreach ($compatible_fields_status as $key => $status) :
        if (!$status || empty($compatible_tabs_label[$key]) || empty($compatibles[$key]) || $key == 'pricing') {
            continue;
        }
    ?>
        <div class="wcbe-sub-tab-content" data-content="<?php echo esc_attr($key); ?>" style="<?php echo ($i == 1) ? 'display: block;' : ''; ?>">
            <?php
            foreach ($compatibles[$key] as $plugin_key => $data) {
                if (!$data['status'] || !file_exists($data['filter_fields'])) {
                    continue;
                }

                include $data['filter_fields'];
            }
            ?>
        </div>
    <?php
        $i++;
    endforeach;
    ?>
</div>