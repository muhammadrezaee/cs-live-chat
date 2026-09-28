<?php
if (!defined('ABSPATH')) exit;

class CSLC_Database {
    
    public static function create_tables() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        
        $tables = array();
        
        // جدول چت‌ها
        $tables[] = "CREATE TABLE {$wpdb->prefix}cslc_chats (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            user_name varchar(255) DEFAULT '',
            user_email varchar(255) DEFAULT '',
            user_ip varchar(45) DEFAULT '',
            user_agent text,
            status enum('active','closed','blocked') DEFAULT 'active',
            operator_id bigint(20) DEFAULT 0,
            rating tinyint(1) DEFAULT 0,
            rating_comment text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id),
            KEY status (status)
        ) $charset;";
        
        // جدول پیام‌ها
        $tables[] = "CREATE TABLE {$wpdb->prefix}cslc_messages (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            chat_id bigint(20) NOT NULL,
            sender_type enum('user','admin','system') NOT NULL,
            sender_id bigint(20) DEFAULT 0,
            message text,
            file_url varchar(500) DEFAULT '',
            file_name varchar(255) DEFAULT '',
            is_read tinyint(1) DEFAULT 0,
            is_typing tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY chat_id (chat_id),
            KEY sender_type (sender_type),
            KEY is_read (is_read)
        ) $charset;";
        
        // جدول تنظیمات
        $tables[] = "CREATE TABLE {$wpdb->prefix}cslc_settings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            setting_key varchar(100) NOT NULL,
            setting_value longtext,
            PRIMARY KEY (id),
            UNIQUE KEY setting_key (setting_key)
        ) $charset;";
        
        // جدول اپراتورها
        $tables[] = "CREATE TABLE {$wpdb->prefix}cslc_operators (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            is_online tinyint(1) DEFAULT 0,
            last_seen datetime DEFAULT CURRENT_TIMESTAMP,
            chats_count bigint(20) DEFAULT 0,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset;";
        
        // جدول بلاک‌ها
        $tables[] = "CREATE TABLE {$wpdb->prefix}cslc_blocks (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ip_address varchar(45) NOT NULL,
            reason text,
            blocked_by bigint(20) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ip_address (ip_address)
        ) $charset;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ($tables as $sql) {
            dbDelta($sql);
        }
    }
    
    public static function get_or_create_chat($session_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cslc_chats';
        
        // بررسی بلاک بودن
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if (self::is_blocked($ip)) {
            return new WP_Error('blocked', 'شما مسدود شده‌اید');
        }
        
        $chat = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE session_id = %s AND status = 'active' LIMIT 1",
            $session_id
        ));
        
        if (!$chat) {
            $wpdb->insert($table, array(
                'session_id' => $session_id,
                'user_ip' => $ip,
                'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '')
            ));
            $chat = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $wpdb->insert_id));
        }
        
        return $chat;
    }
    
    public static function save_message($chat_id, $sender_type, $message, $file_url = '', $file_name = '', $sender_id = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'cslc_messages';
        
        $wpdb->insert($table, array(
            'chat_id' => $chat_id,
            'sender_type' => $sender_type,
            'sender_id' => $sender_id,
            'message' => $message,
            'file_url' => $file_url,
            'file_name' => $file_name
        ));
        
        // آپدیت زمان چت
        $wpdb->update($wpdb->prefix . 'cslc_chats', 
            array('updated_at' => current_time('mysql')),
            array('id' => $chat_id)
        );
        
        return $wpdb->insert_id;
    }
    
    public static function get_messages($chat_id, $limit = 100) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cslc_messages WHERE chat_id = %d ORDER BY created_at ASC LIMIT %d",
            $chat_id, $limit
        ));
    }
    
    public static function get_new_messages($chat_id, $last_id = 0) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cslc_messages WHERE chat_id = %d AND id > %d ORDER BY created_at ASC",
            $chat_id, $last_id
        ));
    }
    
    public static function get_all_chats($status = 'active') {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cslc_chats WHERE status = %s ORDER BY updated_at DESC",
            $status
        ));
    }
    
    public static function mark_as_read($chat_id) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'cslc_messages',
            array('is_read' => 1),
            array('chat_id' => $chat_id, 'sender_type' => 'user', 'is_read' => 0)
        );
    }
    
    public static function count_unread($chat_id = null) {
        global $wpdb;
        if ($chat_id) {
            return (int)$wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}cslc_messages WHERE chat_id = %d AND sender_type = 'user' AND is_read = 0",
                $chat_id
            ));
        }
        return (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cslc_messages WHERE sender_type = 'user' AND is_read = 0");
    }
    
    public static function is_blocked($ip) {
        global $wpdb;
        return (bool)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cslc_blocks WHERE ip_address = %s",
            $ip
        ));
    }
    
    public static function block_ip($ip, $reason = '', $by = 0) {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'cslc_blocks', array(
            'ip_address' => $ip,
            'reason' => $reason,
            'blocked_by' => $by
        ));
    }
    
    public static function unblock_ip($ip) {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'cslc_blocks', array('ip_address' => $ip));
    }
    
    public static function update_chat_info($chat_id, $data) {
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'cslc_chats', $data, array('id' => $chat_id));
    }
    
    public static function delete_chat($chat_id) {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'cslc_messages', array('chat_id' => $chat_id));
        $wpdb->delete($wpdb->prefix . 'cslc_chats', array('id' => $chat_id));
    }
}