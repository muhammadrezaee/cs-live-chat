<?php
if (!defined('ABSPATH')) exit;

class CSLC_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menus'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function add_menus() {
        add_menu_page(
            'CS چت زنده',
            'چت زنده CS',
            'manage_options',
            'cslc-chat',
            array($this, 'page_chat'),
            'dashicons-format-chat',
            30
        );
        
        add_submenu_page('cslc-chat', 'پیام‌ها', 'پیام‌ها', 'manage_options', 'cslc-chat', array($this, 'page_chat'));
        add_submenu_page('cslc-chat', 'تنظیمات', 'تنظیمات', 'manage_options', 'cslc-settings', array($this, 'page_settings'));
        add_submenu_page('cslc-chat', 'آمار', 'آمار', 'manage_options', 'cslc-statistics', array($this, 'page_statistics'));
        add_submenu_page('cslc-chat', 'کاربران مسدود', 'کاربران مسدود', 'manage_options', 'cslc-blocked', array($this, 'page_blocked'));
    }
    
    public function enqueue_scripts($hook) {
    if (strpos($hook, 'cslc') === false) return;
    
    // همیشه admin.css رو لود کن
    wp_enqueue_style('cslc-admin-style', CSLC_PLUGIN_URL . 'assets/css/admin.css', array(), CSLC_VERSION);
    
    // فقط برای صفحه آمار، فایل stats.css رو هم لود کن
    if (strpos($hook, 'cslc-statistics') !== false || strpos($hook, 'cslc_page_cslc-statistics') !== false) {
        wp_enqueue_style('cslc-stats-style', CSLC_PLUGIN_URL . 'assets/css/stats.css', array(), CSLC_VERSION . '-new');
        
        wp_enqueue_script('cslc-stats-script', CSLC_PLUGIN_URL . 'assets/js/stats.js', array('jquery'), CSLC_VERSION . '-new', true);
        wp_localize_script('cslc-stats-script', 'cslc_stats_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cslc_admin_nonce')
        ));
    }
    
    if ($hook === 'toplevel_page_cslc-chat') {
        wp_enqueue_script('cslc-admin-chat', CSLC_PLUGIN_URL . 'assets/js/admin-chat.js', array('jquery'), CSLC_VERSION, true);
        wp_localize_script('cslc-admin-chat', 'cslc_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cslc_admin_nonce'),
            'enable_sound' => CSLC_Settings::get('enable_sound')
        ));
    }
}
    
    public function page_chat() {
        include CSLC_PLUGIN_DIR . 'templates/admin-chat.php';
    }
    
    public function page_settings() {
        include CSLC_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    public function page_statistics() {
        include CSLC_PLUGIN_DIR . 'templates/admin-statistics.php';
    }
    
    public function page_blocked() {
        include CSLC_PLUGIN_DIR . 'templates/admin-blocked.php';
    }
}