<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-filter-profiles">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Filter Profiles', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-filter-profiles-items wcbe-pb30">
                            <div class="wcbe-table-border-radius">
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('Profile Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            <th><?php esc_html_e('Date Modified', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            <th><?php esc_html_e('Use Always', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            <th><?php esc_html_e('Actions', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($filters_preset)) : ?>
                                            <?php foreach ($filters_preset as $filter_item) : ?>
                                                <?php include "filter_profile_item.php"; ?>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="4" style="text-align: center;"><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" width="20" height="20"></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>