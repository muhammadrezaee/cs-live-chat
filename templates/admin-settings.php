<?php
if (!defined('ABSPATH')) exit;
if (isset($_POST['cslc_save_settings']) && check_admin_referer('cslc_save_settings')) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'cslc_') === 0) {
            if (is_array($value)) {
                $value = array_map('sanitize_text_field', $value);
            } else {
                $value = sanitize_text_field($value);
            }
            CSLC_Settings::update(str_replace('cslc_', '', $key), $value);
        }
    }
    echo '<div class="notice notice-success"><p>تنظیمات با موفقیت ذخیره شد.</p></div>';
}
$settings = CSLC_Settings::get_all();
?>
<div class="wrap cslc-settings-wrap">
    <h1>⚙️ تنظیمات چت زنده</h1>

    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('cslc_save_settings'); ?>

        <div class="cslc-settings-tabs">
            <nav class="cslc-tabs-nav">
                <a href="#general" class="cslc-tab active" data-tab="general">عمومی</a>
                <a href="#appearance" class="cslc-tab" data-tab="appearance">ظاهر</a>
                <a href="#branding" class="cslc-tab" data-tab="branding">برندینگ</a>
                <a href="#features" class="cslc-tab" data-tab="features">امکانات</a>
                <a href="#notifications" class="cslc-tab" data-tab="notifications">اعلان‌ها</a>
                <a href="#auto-reply" class="cslc-tab" data-tab="auto-reply">پاسخ خودکار</a>
            </nav>

            <div class="cslc-tabs-content">
                <!-- عمومی -->
                <div class="cslc-tab-content active" id="tab-general">
                    <table class="form-table">
                        <tr>
                            <th>فعال‌سازی چت</th>
                            <td><label><input type="checkbox" name="cslc_enable_chat" value="1" <?php checked($settings['enable_chat'], 1); ?>> نمایش ویجت چت در سایت</label></td>
                        </tr>
                        <tr>
                            <th>نام نمایشی ادمین</th>
                            <td><input type="text" name="cslc_admin_name" value="<?php echo esc_attr($settings['admin_name']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>پیام خوش‌آمدگویی</th>
                            <td><textarea name="cslc_welcome_message" rows="3" class="large-text"><?php echo esc_textarea($settings['welcome_message']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>پیام آفلاین</th>
                            <td><textarea name="cslc_offline_message" rows="3" class="large-text"><?php echo esc_textarea($settings['offline_message']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>شماره واتس‌اپ</th>
                            <td><input type="text" name="cslc_whatsapp_number" value="<?php echo esc_attr($settings['whatsapp_number']); ?>" placeholder="989123456789" class="regular-text ltr"></td>
                        </tr>
                        <tr>
                            <th>ایمیل ادمین</th>
                            <td><input type="email" name="cslc_admin_email" value="<?php echo esc_attr($settings['admin_email']); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                </div>

                <!-- ظاهر -->
                <div class="cslc-tab-content" id="tab-appearance">
                    <table class="form-table">
                        <tr>
                            <th>رنگ اصلی</th>
                            <td><input type="color" name="cslc_primary_color" value="<?php echo esc_attr($settings['primary_color']); ?>"></td>
                        </tr>
                        <tr>
                            <th>رنگ ثانویه</th>
                            <td><input type="color" name="cslc_secondary_color" value="<?php echo esc_attr($settings['secondary_color']); ?>"></td>
                        </tr>
                        <tr>
                            <th>موقعیت ویجت</th>
                            <td>
                                <select name="cslc_chat_position">
                                    <option value="bottom-right" <?php selected($settings['chat_position'], 'bottom-right'); ?>>پایین - راست</option>
                                    <option value="bottom-left" <?php selected($settings['chat_position'], 'bottom-left'); ?>>پایین - چپ</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>حالت تاریک</th>
                            <td><label><input type="checkbox" name="cslc_enable_dark_mode" value="1" <?php checked($settings['enable_dark_mode'], 1); ?>> فعال‌سازی حالت تاریک</label></td>
                        </tr>
                    </table>
                </div>

                <!-- برندینگ -->
                <div class="cslc-tab-content" id="tab-branding">
                    <table class="form-table">
                        <tr>
                            <th>آدرس سایت</th>
                            <td><input type="text" name="cslc_site_address" value="<?php echo esc_attr($settings['site_address']); ?>" class="regular-text ltr"></td>
                        </tr>
                        <tr>
                            <th>لوگوی سایت</th>
                            <td>
                                <input type="file" id="cslc-logo-upload" accept="image/*">
                                <input type="hidden" name="cslc_site_logo" id="cslc_site_logo" value="<?php echo esc_attr($settings['site_logo']); ?>">
                                <div id="cslc-logo-preview" style="margin-top:10px;">
                                    <?php if (!empty($settings['site_logo'])): ?>
                                        <img src="<?php echo esc_url($settings['site_logo']); ?>" style="max-width:150px;">
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>آواتار ادمین</th>
                            <td>
                                <input type="file" id="cslc-avatar-upload" accept="image/*">
                                <input type="hidden" name="cslc_admin_avatar" id="cslc_admin_avatar" value="<?php echo esc_attr($settings['admin_avatar']); ?>">
                                <div id="cslc-avatar-preview" style="margin-top:10px;">
                                    <?php if (!empty($settings['admin_avatar'])): ?>
                                        <img src="<?php echo esc_url($settings['admin_avatar']); ?>" style="max-width:80px;border-radius:50%;">
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- امکانات -->
                <div class="cslc-tab-content" id="tab-features">
                    <table class="form-table">
                        <tr>
                            <th>امکانات فعال</th>
                            <td>
                                <label><input type="checkbox" name="cslc_enable_sound" value="1" <?php checked($settings['enable_sound'], 1); ?>> صدای نوتیفیکیشن</label><br>
                                <label><input type="checkbox" name="cslc_enable_emoji" value="1" <?php checked($settings['enable_emoji'], 1); ?>> ایموجی پیکر</label><br>
                                <label><input type="checkbox" name="cslc_enable_file_upload" value="1" <?php checked($settings['enable_file_upload'], 1); ?>> آپلود فایل</label><br>
                                <label><input type="checkbox" name="cslc_enable_pre_chat_form" value="1" <?php checked($settings['enable_pre_chat_form'], 1); ?>> فرم قبل از چت</label>
                            </td>
                        </tr>
                        <tr>
                        <tr>
                            <th>فرمت‌های مجاز</th>
                            <td>
                                <input type="text" name="cslc_allowed_extensions"
                                    value="<?php echo esc_attr($settings['allowed_extensions']); ?>"
                                    class="regular-text ltr"
                                    placeholder="jpg,jpeg,png,gif,pdf,doc,docx,zip">
                                <p class="description">فرمت‌ها را با کاما جدا کنید (بدون نقطه)</p>
                            </td>
                        </tr>
                        <tr>
                            <th>حداکثر حجم فایل</th>
                            <td>
                                <input type="number" name="cslc_max_file_size"
                                    value="<?php echo esc_attr($settings['max_file_size'] / 1048576); ?>"
                                    class="small-text" min="1" max="100">
                                <span>مگابایت</span>
                                <p class="description">حداکثر حجم فایل‌های ارسالی کاربران</p>
                            </td>
                        </tr>
                        </tr>
                    </table>
                </div>

                <!-- اعلان‌ها -->
                <div class="cslc-tab-content" id="tab-notifications">
                    <table class="form-table">
                        <tr>
                            <th>اعلان ایمیل</th>
                            <td><label><input type="checkbox" name="cslc_enable_email_notification" value="1" <?php checked($settings['enable_email_notification'], 1); ?>> ارسال ایمیل برای پیام‌های جدید</label></td>
                        </tr>
                        <tr>
                            <th>اعلان مرورگر</th>
                            <td><label><input type="checkbox" name="cslc_enable_browser_notification" value="1" <?php checked($settings['enable_browser_notification'], 1); ?>> نمایش نوتیفیکیشن در مرورگر</label></td>
                        </tr>
                    </table>
                </div>

                <!-- پاسخ خودکار -->
                <div class="cslc-tab-content" id="tab-auto-reply">
                    <table class="form-table">
                        <tr>
                            <th>پاسخ خودکار</th>
                            <td><label><input type="checkbox" name="cslc_enable_auto_reply" value="1" <?php checked($settings['enable_auto_reply'], 1); ?>> فعال‌سازی پاسخ خودکار</label></td>
                        </tr>
                        <tr>
                            <th>متن پاسخ</th>
                            <td><textarea name="cslc_auto_reply" rows="3" class="large-text"><?php echo esc_textarea($settings['auto_reply']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>تاخیر (ثانیه)</th>
                            <td><input type="number" name="cslc_auto_reply_delay" value="<?php echo esc_attr($settings['auto_reply_delay']); ?>" min="1" class="small-text"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <p class="submit">
            <input type="submit" name="cslc_save_settings" class="button-primary" value="ذخیره تنظیمات">
        </p>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        // تب‌ها
        $('.cslc-tab').on('click', function(e) {
            e.preventDefault();
            var tab = $(this).data('tab');
            $('.cslc-tab').removeClass('active');
            $(this).addClass('active');
            $('.cslc-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');
        });

        // آپلود لوگو
        $('#cslc-logo-upload').on('change', function() {
            var formData = new FormData();
            formData.append('action', 'cslc_admin_upload_avatar');
            formData.append('avatar', this.files[0]);
            formData.append('nonce', '<?php echo wp_create_nonce('cslc_admin_nonce'); ?>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#cslc_site_logo').val(res.data.url);
                        $('#cslc-logo-preview').html('<img src="' + res.data.url + '" style="max-width:150px;">');
                    }
                }
            });
        });

        // آپلود آواتار
        $('#cslc-avatar-upload').on('change', function() {
            var formData = new FormData();
            formData.append('action', 'cslc_admin_upload_avatar');
            formData.append('avatar', this.files[0]);
            formData.append('nonce', '<?php echo wp_create_nonce('cslc_admin_nonce'); ?>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#cslc_admin_avatar').val(res.data.url);
                        $('#cslc-avatar-preview').html('<img src="' + res.data.url + '" style="max-width:80px;border-radius:50%;">');
                    }
                }
            });
        });
    });
</script>