<?php 
if (!defined('ABSPATH')) exit;
global $wpdb;
$blocks = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cslc_blocks ORDER BY created_at DESC");
?>
<div class="wrap">
    <h1>🚫 کاربران مسدود شده</h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>آی‌پی</th>
                <th>دلیل</th>
                <th>تاریخ مسدودسازی</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($blocks)): ?>
                <tr><td colspan="4" style="text-align:center;">هیچ کاربر مسدودی وجود ندارد</td></tr>
            <?php else: foreach ($blocks as $block): ?>
                <tr>
                    <td><code><?php echo esc_html($block->ip_address); ?></code></td>
                    <td><?php echo esc_html($block->reason ?: '-'); ?></td>
                    <td><?php echo esc_html($block->created_at); ?></td>
                    <td>
                        <button class="button cslc-unblock-btn" data-ip="<?php echo esc_attr($block->ip_address); ?>">آزاد کردن</button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('.cslc-unblock-btn').on('click', function() {
        var ip = $(this).data('ip');
        if (!confirm('آیا مطمئن هستید؟')) return;
        
        $.post(ajaxurl, {
            action: 'cslc_admin_unblock_user',
            nonce: '<?php echo wp_create_nonce('cslc_admin_nonce'); ?>',
            ip: ip
        }, function(res) {
            if (res.success) location.reload();
        });
    });
});
</script>