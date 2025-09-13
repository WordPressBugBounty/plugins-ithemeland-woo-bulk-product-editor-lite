<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<span><?php echo (!empty($complete_message) && !empty($complete_message['message'])) ? esc_html($complete_message['message']) : esc_html__('Your changes have been applied', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>