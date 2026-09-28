<?php
if (!defined('ABSPATH')) exit;

class CSLC_Settings
{

    private static $defaults = array(
        'enable_chat' => 1,
        'welcome_message' => 'سلام! 👋 چطور می‌تونیم کمکتون کنیم؟',
        'offline_message' => 'در حال حاضر آفلاین هستیم. لطفاً پیامتون رو بذارید، به زودی پاسخ می‌دیم.',
        'primary_color' => '#667eea',
        'secondary_color' => '#764ba2',
        'whatsapp_number' => '',
        'admin_name' => 'پشتیبانی',
        'admin_avatar' => '',
        'site_logo' => '',
        'site_address' => 'codsoft.ir',
        'enable_sound' => 1,
        'enable_emoji' => 1,
        'enable_file_upload' => 1,
        'enable_pre_chat_form' => 1,
        'enable_email_notification' => 1,
        'enable_browser_notification' => 1,
        'enable_auto_reply' => 0,
        'auto_reply' => 'پیام شما دریافت شد. به زودی پاسخ می‌دیم.',
        'auto_reply_delay' => 5,
        'enable_dark_mode' => 0,
        'chat_position' => 'bottom-right',
        'admin_email' => '',
        'allowed_extensions' => 'jpg,jpeg,png,gif,pdf,doc,docx,zip',
        'max_file_size' => 5242880,
    );

    public static function set_defaults()
    {
        foreach (self::$defaults as $key => $value) {
            if (get_option('cslc_' . $key) === false) {
                update_option('cslc_' . $key, $value);
            }
        }
    }

    public static function get_all()
    {
        $settings = array();
        foreach (self::$defaults as $key => $default) {
            $settings[$key] = get_option('cslc_' . $key, $default);
        }
        if (empty($settings['admin_email'])) {
            $settings['admin_email'] = get_option('admin_email');
        }
        return $settings;
    }

    public static function get($key)
    {
        return get_option('cslc_' . $key, self::$defaults[$key] ?? '');
    }

    public static function update($key, $value)
    {
        return update_option('cslc_' . $key, $value);
    }

    public static function register()
    {
        register_setting('cslc_settings_group', 'cslc_enable_chat');
        register_setting('cslc_settings_group', 'cslc_welcome_message');
        register_setting('cslc_settings_group', 'cslc_offline_message');
        register_setting('cslc_settings_group', 'cslc_primary_color');
        register_setting('cslc_settings_group', 'cslc_secondary_color');
        register_setting('cslc_settings_group', 'cslc_whatsapp_number');
        register_setting('cslc_settings_group', 'cslc_admin_name');
        register_setting('cslc_settings_group', 'cslc_admin_avatar');
        register_setting('cslc_settings_group', 'cslc_site_logo');
        register_setting('cslc_settings_group', 'cslc_site_address');
        register_setting('cslc_settings_group', 'cslc_enable_sound');
        register_setting('cslc_settings_group', 'cslc_enable_emoji');
        register_setting('cslc_settings_group', 'cslc_enable_file_upload');
        register_setting('cslc_settings_group', 'cslc_enable_pre_chat_form');
        register_setting('cslc_settings_group', 'cslc_enable_email_notification');
        register_setting('cslc_settings_group', 'cslc_enable_browser_notification');
        register_setting('cslc_settings_group', 'cslc_enable_auto_reply');
        register_setting('cslc_settings_group', 'cslc_auto_reply');
        register_setting('cslc_settings_group', 'cslc_auto_reply_delay');
        register_setting('cslc_settings_group', 'cslc_enable_dark_mode');
        register_setting('cslc_settings_group', 'cslc_chat_position');
        register_setting('cslc_settings_group', 'cslc_admin_email');
    }
}
