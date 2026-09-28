<?php

/**
 * Plugin Name: CS Live Chat
 * Plugin URI: https://codsoft.ir
 * Description: سیستم چت زنده حرفه‌ای با قابلیت‌های پیشرفته - محصول codsoft.ir
 * Version: 2.0.0
 * Author: CodSoft
 * Author URI: https://codsoft.ir
 * Text Domain: cs-live-chat
 * Domain Path: /languages
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) exit;

// تعریف ثابت‌ها
define('CSLC_VERSION', '2.0.0');
define('CSLC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CSLC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CSLC_PLUGIN_BASENAME', plugin_basename(__FILE__));

// بارگذاری فایل‌ها
require_once CSLC_PLUGIN_DIR . 'includes/class-database.php';
require_once CSLC_PLUGIN_DIR . 'includes/class-ajax.php';
require_once CSLC_PLUGIN_DIR . 'includes/class-admin.php';
require_once CSLC_PLUGIN_DIR . 'includes/class-settings.php';
require_once CSLC_PLUGIN_DIR . 'includes/class-notifications.php';
require_once CSLC_PLUGIN_DIR . 'includes/class-statistics.php';

class CS_Live_Chat
{
    private static $instance = null;

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('init', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend'));
        add_action('wp_footer', array($this, 'render_widget'));

        // مقداردهی کلاس‌ها
        new CSLC_Database();
        new CSLC_Ajax();
        new CSLC_Admin();
        new CSLC_Settings();
        new CSLC_Notifications();
        new CSLC_Statistics();
    }

    public function activate()
    {
        CSLC_Database::create_tables();
        CSLC_Settings::set_defaults();
    }

    public function deactivate()
    {
        // پاکسازی در صورت نیاز
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('cs-live-chat', false, dirname(CSLC_PLUGIN_BASENAME) . '/languages');
    }

    public function enqueue_frontend()
    {
        // عدم نمایش در صفحات ادمین
        if (is_admin()) return;

        $settings = CSLC_Settings::get_all();

        // اگر چت غیرفعاله، بارگذاری نکن
        if (empty($settings['enable_chat'])) return;

        wp_enqueue_style('cslc-chat-style', CSLC_PLUGIN_URL . 'assets/css/chat.css', array(), CSLC_VERSION);
        wp_enqueue_script('cslc-chat-script', CSLC_PLUGIN_URL . 'assets/js/chat.js', array('jquery'), CSLC_VERSION, true);

        wp_localize_script('cslc-chat-script', 'cslc_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cslc_chat_nonce'),
            'site_name' => get_bloginfo('name'),
            'site_url' => home_url(),
            'settings' => array(
                'welcome_message' => $settings['welcome_message'],
                'primary_color' => $settings['primary_color'],
                'whatsapp_number' => $settings['whatsapp_number'],
                'admin_name' => $settings['admin_name'],
                'admin_avatar' => $settings['admin_avatar'],
                'enable_sound' => $settings['enable_sound'],
                'enable_emoji' => $settings['enable_emoji'],
                'enable_file_upload' => $settings['enable_file_upload'],
                'enable_pre_chat_form' => $settings['enable_pre_chat_form'],
                'site_logo' => $settings['site_logo'],
                'site_address' => $settings['site_address'],
                'offline_message' => $settings['offline_message'],
                'auto_reply' => $settings['auto_reply'],
                'auto_reply_delay' => $settings['auto_reply_delay'],
                'is_admin_online' => CSLC_Statistics::is_admin_online(),
                'enable_file_upload' => $settings['enable_file_upload'],
                'allowed_extensions' => explode(',', $settings['allowed_extensions'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx,zip'),
                'max_file_size' => intval($settings['max_file_size'] ?? 5242880),
            ),
            'max_file_size' => wp_max_upload_size(),
            'allowed_extensions' => array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip')
        ));
    }

    public function render_widget()
    {
        if (is_admin()) return;
        $settings = CSLC_Settings::get_all();
        if (empty($settings['enable_chat'])) return;

        include CSLC_PLUGIN_DIR . 'templates/chat-widget.php';
    }
}

CS_Live_Chat::get_instance();
