<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="urbantaxi-book-wrapper">

    <button class="urbantaxi-book-btn">
        <?php echo esc_html($settings['button_text']); ?>
    </button>

    <div class="urbantaxi-modal" role="dialog" aria-modal="true">
        <div class="urbantaxi-modal-content position-<?php echo esc_attr($modal_position); ?>">

            <button class="urbantaxi-close" aria-label="Close modal">&times;</button>
            <?php echo do_shortcode('[mptbm_booking]'); ?>

        </div>
    </div>

</div> 