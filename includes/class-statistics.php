<?php
if (!defined('ABSPATH')) exit;

class CSLC_Statistics {
    
    public static function is_admin_online() {
        $last_activity = get_transient('cslc_admin_activity');
        return $last_activity && (time() - $last_activity) < 300; // 5 دقیقه
    }
    
    public static function set_admin_online() {
        set_transient('cslc_admin_activity', time(), 600);
    }
    
    public static function count_chat_messages($chat_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cslc_messages WHERE chat_id = %d",
            $chat_id
        ));
    }
    
    public static function get_all_stats() {
        global $wpdb;
        
        $total_chats = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cslc_chats");
        $active_chats = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cslc_chats WHERE status='active'");
        $closed_chats = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cslc_chats WHERE status='closed'");
        $total_messages = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cslc_messages");
        $unread_messages = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cslc_messages WHERE sender_type='user' AND is_read=0");
        
        $avg_rating = $wpdb->get_var("SELECT AVG(rating) FROM {$wpdb->prefix}cslc_chats WHERE rating > 0");
        $rated_chats = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cslc_chats WHERE rating > 0");
        
        // چت‌های امروز
        $today_chats = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cslc_chats WHERE DATE(created_at) = %s",
            current_time('Y-m-d')
        ));
        
        // چت‌های 7 روز اخیر
        $week_chats = array();
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = (int)$wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}cslc_chats WHERE DATE(created_at) = %s",
                $date
            ));
            $week_chats[] = array('date' => $date, 'count' => $count);
        }
        
        return array(
            'total_chats' => $total_chats,
            'active_chats' => $active_chats,
            'closed_chats' => $closed_chats,
            'total_messages' => $total_messages,
            'unread_messages' => $unread_messages,
            'avg_rating' => round($avg_rating ?: 0, 1),
            'rated_chats' => $rated_chats,
            'today_chats' => $today_chats,
            'week_chats' => $week_chats,
            'is_admin_online' => self::is_admin_online()
        );
    }
}