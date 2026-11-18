<?php
defined('ABSPATH') || exit();
use wcbel\classes\bootstrap\AiInterest;
?>
<dialog id="wcbel-ai-dialog" class="wcbel-ai-dialog" aria-labelledby="wcbel-ai-title">
    <button type="button" class="ai-close" aria-label="<?php esc_attr_e('Close', AiInterest::DOMAIN); ?>">&times;</button>
    <div class="ai-intro"<?php if ($subscribed) { echo ' hidden'; } ?>>
        <h2 id="wcbel-ai-title"><?php esc_html_e('Edit thousands of products with a single sentence.', AiInterest::DOMAIN); ?></h2>
        <p><?php esc_html_e('Soon, you won’t need complex filter rules—just tell AI Bulk Assistant what to update, preview the changes, and apply safely.', AiInterest::DOMAIN); ?></p>
        <p class="ai-error" role="alert" hidden></p>
        <div class="ai-actions">
            <button type="button" class="ai-later"><?php esc_html_e('Maybe later', AiInterest::DOMAIN); ?></button>
            <button type="button" class="ai-notify"><?php esc_html_e('⚡️ I Want This Feature (Early Access)', AiInterest::DOMAIN); ?></button>
        </div>
    </div>
    <div class="ai-thanks"<?php if (!$subscribed) { echo ' hidden'; } ?> role="status" tabindex="-1">
        <h2><?php esc_html_e('Awesome! You’re on the priority list.', AiInterest::DOMAIN); ?></h2>
        <div class="ai-actions"><button type="button" class="ai-done"><?php esc_html_e('Done', AiInterest::DOMAIN); ?></button></div>
    </div>
</dialog>
