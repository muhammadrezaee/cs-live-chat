<?php
if (!defined('ABSPATH')) exit;

class CSLC_Notifications {
    
    public static function send_email_notification($chat, $message) {
        $settings = CSLC_Settings::get_all();
        if (empty($settings['enable_email_notification'])) return;
        
        $to = $settings['admin_email'];
        $subject = 'پیام جدید در چت زنده - ' . get_bloginfo('name');
        
        $user_info = '';
        if (!empty($chat->user_name)) $user_info .= "نام: {$chat->user_name}\n";
        if (!empty($chat->user_email)) $user_info .= "ایمیل: {$chat->user_email}\n";
        $user_info .= "آی‌پی: {$chat->user_ip}\n";
        
        $body = "
        <div style='font-family:Tahoma,Arial,sans-serif;direction:rtl;max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1)'>
            <div style='background:linear-gradient(135deg," . esc_attr($settings['primary_color']) . "," . esc_attr($settings['secondary_color']) . ");color:#fff;padding:25px;text-align:center'>
                <h2 style='margin:0'>💬 پیام جدید در چت زنده</h2>
                <p style='margin:10px 0 0;opacity:0.9'>" . esc_html(get_bloginfo('name')) . "</p>
            </div>
            <div style='padding:25px'>
                <h3 style='color:#333;border-bottom:2px solid " . esc_attr($settings['primary_color']) . ";padding-bottom:10px'>اطلاعات کاربر</h3>
                <div style='background:#f8f9fa;padding:15px;border-radius:8px;margin-bottom:20px;white-space:pre-line'>" . esc_html($user_info) . "</div>
                
                <h3 style='color:#333;border-bottom:2px solid " . esc_attr($settings['primary_color']) . ";padding-bottom:10px'>پیام</h3>
                <div style='background:#f0f4ff;padding:20px;border-radius:8px;border-right:4px solid " . esc_attr($settings['primary_color']) . "'>" . nl2br(esc_html($message)) . "</div>
                
                <div style='text-align:center;margin-top:25px'>
                    <a href='" . admin_url('admin.php?page=cslc-chat') . "' style='background:" . esc_attr($settings['primary_color']) . ";color:#fff;padding:12px 30px;border-radius:25px;text-decoration:none;display:inline-block;font-weight:bold'>پاسخ در پیشخوان</a>
                </div>
            </div>
            <div style='background:#f8f9fa;padding:15px;text-align:center;color:#999;font-size:12px;border-top:1px solid #eee'>
                ارسال شده توسط CS Live Chat | <a href='https://codsoft.ir' style='color:" . esc_attr($settings['primary_color']) . "'>codsoft.ir</a>
            </div>
        </div>
        ";
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $body, $headers);
    }
}