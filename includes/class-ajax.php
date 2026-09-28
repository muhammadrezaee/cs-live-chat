<?php
if (!defined('ABSPATH')) exit;

class CSLC_Ajax
{

    public function __construct()
    {
        // کاربر
        add_action('wp_ajax_cslc_send_message', array($this, 'send_message'));
        add_action('wp_ajax_nopriv_cslc_send_message', array($this, 'send_message'));
        add_action('wp_ajax_cslc_get_messages', array($this, 'get_messages'));
        add_action('wp_ajax_nopriv_cslc_get_messages', array($this, 'get_messages'));
        add_action('wp_ajax_cslc_update_user_info', array($this, 'update_user_info'));
        add_action('wp_ajax_nopriv_cslc_update_user_info', array($this, 'update_user_info'));
        add_action('wp_ajax_cslc_upload_file', array($this, 'upload_file'));
        add_action('wp_ajax_nopriv_cslc_upload_file', array($this, 'upload_file'));
        add_action('wp_ajax_cslc_submit_rating', array($this, 'submit_rating'));
        add_action('wp_ajax_nopriv_cslc_submit_rating', array($this, 'submit_rating'));
        add_action('wp_ajax_cslc_typing_status', array($this, 'typing_status'));
        add_action('wp_ajax_nopriv_cslc_typing_status', array($this, 'typing_status'));

        // ادمین
        add_action('wp_ajax_cslc_admin_send', array($this, 'admin_send'));
        add_action('wp_ajax_cslc_admin_get_chats', array($this, 'admin_get_chats'));
        add_action('wp_ajax_cslc_admin_get_messages', array($this, 'admin_get_messages'));
        add_action('wp_ajax_cslc_admin_close_chat', array($this, 'admin_close_chat'));
        add_action('wp_ajax_cslc_admin_delete_chat', array($this, 'admin_delete_chat'));
        add_action('wp_ajax_cslc_admin_block_user', array($this, 'admin_block_user'));
        add_action('wp_ajax_cslc_admin_unblock_user', array($this, 'admin_unblock_user'));
        add_action('wp_ajax_cslc_admin_export_csv', array($this, 'admin_export_csv'));
        add_action('wp_ajax_cslc_admin_get_stats', array($this, 'admin_get_stats'));
        add_action('wp_ajax_cslc_admin_upload_avatar', array($this, 'admin_upload_avatar'));
    }

    public function send_message()
    {
        check_ajax_referer('cslc_chat_nonce', 'nonce');

        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');

        if (empty($session_id) || empty($message)) {
            wp_send_json_error(array('message' => 'خطا در ارسال پیام'));
        }

        $chat = CSLC_Database::get_or_create_chat($session_id);
        if (is_wp_error($chat)) {
            wp_send_json_error(array('message' => $chat->get_error_message()));
        }

        $msg_id = CSLC_Database::save_message($chat->id, 'user', $message);

        // نوتیفیکیشن ایمیل
        CSLC_Notifications::send_email_notification($chat, $message);

        // پاسخ خودکار
        $settings = CSLC_Settings::get_all();
        if (!empty($settings['enable_auto_reply']) && !empty($settings['auto_reply'])) {
            wp_schedule_single_event(time() + (int)$settings['auto_reply_delay'], 'cslc_auto_reply_event', array($chat->id, $settings['auto_reply']));
        }

        wp_send_json_success(array('message_id' => $msg_id, 'chat_id' => $chat->id));
    }

    public function get_messages()
    {
        check_ajax_referer('cslc_chat_nonce', 'nonce');

        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $last_id = intval($_POST['last_message_id'] ?? 0);

        $chat = CSLC_Database::get_or_create_chat($session_id);
        if (is_wp_error($chat)) {
            wp_send_json_error(array('message' => $chat->get_error_message()));
        }

        $messages = $last_id > 0 ? CSLC_Database::get_new_messages($chat->id, $last_id) : CSLC_Database::get_messages($chat->id);

        wp_send_json_success(array('messages' => $messages, 'chat_id' => $chat->id));
    }

    public function update_user_info()
    {
        check_ajax_referer('cslc_chat_nonce', 'nonce');

        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');

        $chat = CSLC_Database::get_or_create_chat($session_id);
        if (is_wp_error($chat)) {
            wp_send_json_error(array('message' => $chat->get_error_message()));
        }

        CSLC_Database::update_chat_info($chat->id, array(
            'user_name' => $name,
            'user_email' => $email
        ));

        wp_send_json_success();
    }

    public function upload_file()
    {
        check_ajax_referer('cslc_chat_nonce', 'nonce');

        if (empty($_FILES['file'])) {
            wp_send_json_error(array('message' => 'فایلی انتخاب نشده است'));
        }

        $file = $_FILES['file'];

        // بررسی خطای آپلود
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = array(
                UPLOAD_ERR_INI_SIZE => 'حجم فایل از حد مجاز سرور بیشتر است',
                UPLOAD_ERR_FORM_SIZE => 'حجم فایل از حد مجاز فرم بیشتر است',
                UPLOAD_ERR_PARTIAL => 'فایل به صورت ناقص آپلود شد',
                UPLOAD_ERR_NO_FILE => 'فایلی آپلود نشد',
                UPLOAD_ERR_NO_TMP_DIR => 'پوشه موقت سرور یافت نشد',
                UPLOAD_ERR_CANT_WRITE => 'خطا در نوشتن فایل روی دیسک',
                UPLOAD_ERR_EXTENSION => 'یک افزونه PHP جلوی آپلود را گرفت'
            );
            $error_msg = $error_messages[$file['error']] ?? 'خطای ناشناخته در آپلود';
            wp_send_json_error(array('message' => $error_msg));
        }

        // گرفتن تنظیمات
        $settings = CSLC_Settings::get_all();
        $allowed_extensions = array_map('trim', explode(',', $settings['allowed_extensions'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx,zip'));
        $max_file_size = intval($settings['max_file_size'] ?? 5242880);

        // بررسی فرمت
        $file_name = $file['name'];
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_extensions)) {
            $allowed_list = implode('، ', array_map('strtoupper', $allowed_extensions));
            wp_send_json_error(array('message' => "فرمت فایل مجاز نیست. فرمت‌های مجاز: {$allowed_list}"));
        }

        // بررسی حجم
        if ($file['size'] > $max_file_size) {
            $max_size_mb = number_format($max_file_size / 1048576, 2);
            wp_send_json_error(array('message' => "حجم فایل بیش از حد مجاز است. حداکثر حجم: {$max_size_mb} مگابایت"));
        }

        // آپلود فایل
        require_once ABSPATH . 'wp-admin/includes/file.php';

        // ساخت پوشه اختصاصی برای چت
        $upload_dir = wp_upload_dir();
        $chat_upload_dir = $upload_dir['basedir'] . '/cslc-uploads/' . date('Y/m');

        if (!file_exists($chat_upload_dir)) {
            wp_mkdir_p($chat_upload_dir);
        }

        // تغییر نام فایل برای جلوگیری از تداخل
        $unique_name = md5(time() . $file_name) . '.' . $extension;
        $file['name'] = $unique_name;

        $upload = wp_handle_upload($file, array(
            'test_form' => false,
            'upload_dir' => $chat_upload_dir,
            'unique_filename_callback' => function ($dir, $name, $ext) use ($unique_name) {
                return $unique_name;
            }
        ));

        if (isset($upload['error'])) {
            wp_send_json_error(array('message' => $upload['error']));
        }

        // ذخیره پیام
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $chat = CSLC_Database::get_or_create_chat($session_id);

        if (is_wp_error($chat)) {
            wp_send_json_error(array('message' => $chat->get_error_message()));
        }

        $msg_id = CSLC_Database::save_message(
            $chat->id,
            'user',
            '[فایل پیوست شد]',
            $upload['url'],
            $file_name // نام اصلی فایل برای نمایش
        );

        wp_send_json_success(array(
            'message_id' => $msg_id,
            'file_url' => $upload['url'],
            'file_name' => $file_name
        ));
    }

    public function submit_rating()
    {
        check_ajax_referer('cslc_chat_nonce', 'nonce');

        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $rating = intval($_POST['rating'] ?? 0);
        $comment = sanitize_textarea_field($_POST['comment'] ?? '');

        $chat = CSLC_Database::get_or_create_chat($session_id);
        CSLC_Database::update_chat_info($chat->id, array(
            'rating' => $rating,
            'rating_comment' => $comment,
            'status' => 'closed'
        ));

        wp_send_json_success();
    }

    public function typing_status()
    {
        check_ajax_referer('cslc_chat_nonce', 'nonce');

        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $is_typing = intval($_POST['is_typing'] ?? 0);

        $chat = CSLC_Database::get_or_create_chat($session_id);

        // ذخیره وضعیت تایپ
        set_transient('cslc_typing_' . $chat->id, $is_typing, 10);

        wp_send_json_success();
    }

    // ===== ادمین =====

    public function admin_send()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(array('message' => 'دسترسی ندارید'));

        $chat_id = intval($_POST['chat_id'] ?? 0);
        $message = sanitize_textarea_field($_POST['message'] ?? '');

        if (empty($chat_id) || empty($message)) {
            wp_send_json_error(array('message' => 'خطا'));
        }

        $msg_id = CSLC_Database::save_message($chat_id, 'admin', $message, '', '', get_current_user_id());

        wp_send_json_success(array('message_id' => $msg_id));
    }

    public function admin_get_chats()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(array('message' => 'دسترسی ندارید'));

        $chats = CSLC_Database::get_all_chats();
        foreach ($chats as &$chat) {
            $msgs = CSLC_Database::get_messages($chat->id, 1);
            $chat->last_message = !empty($msgs) ? end($msgs) : null;
            $chat->unread_count = CSLC_Database::count_unread($chat->id);
            $chat->messages_count = (int)CSLC_Statistics::count_chat_messages($chat->id);
        }

        wp_send_json_success(array('chats' => $chats));
    }

    public function admin_get_messages()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(array('message' => 'دسترسی ندارید'));

        $chat_id = intval($_POST['chat_id'] ?? 0);
        $messages = CSLC_Database::get_messages($chat_id);
        CSLC_Database::mark_as_read($chat_id);

        // دریافت وضعیت تایپ کاربر
        $is_user_typing = get_transient('cslc_typing_' . $chat_id);

        wp_send_json_success(array(
            'messages' => $messages,
            'is_user_typing' => $is_user_typing ? true : false
        ));
    }

    public function admin_close_chat()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $chat_id = intval($_POST['chat_id'] ?? 0);
        CSLC_Database::update_chat_info($chat_id, array('status' => 'closed'));
        wp_send_json_success();
    }

    public function admin_delete_chat()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $chat_id = intval($_POST['chat_id'] ?? 0);
        CSLC_Database::delete_chat($chat_id);
        wp_send_json_success();
    }

    public function admin_block_user()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $chat_id = intval($_POST['chat_id'] ?? 0);
        $reason = sanitize_text_field($_POST['reason'] ?? '');

        global $wpdb;
        $chat = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cslc_chats WHERE id = %d", $chat_id));

        if ($chat) {
            CSLC_Database::block_ip($chat->user_ip, $reason, get_current_user_id());
            CSLC_Database::update_chat_info($chat_id, array('status' => 'blocked'));
        }

        wp_send_json_success();
    }

    public function admin_unblock_user()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $ip = sanitize_text_field($_POST['ip'] ?? '');
        CSLC_Database::unblock_ip($ip);
        wp_send_json_success();
    }

    public function admin_export_csv()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $chat_id = intval($_POST['chat_id'] ?? 0);
        $messages = CSLC_Database::get_messages($chat_id, 10000);

        $csv = "زمان,فرستنده,پیام\n";
        foreach ($messages as $msg) {
            $sender = $msg->sender_type === 'user' ? 'کاربر' : 'ادمین';
            $csv .= '"' . $msg->created_at . '","' . $sender . '","' . str_replace('"', '""', $msg->message) . "\"\n";
        }

        wp_send_json_success(array('csv' => $csv));
    }

    public function admin_get_stats()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        wp_send_json_success(array('stats' => CSLC_Statistics::get_all_stats()));
    }

    public function admin_upload_avatar()
    {
        check_ajax_referer('cslc_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        if (empty($_FILES['avatar'])) wp_send_json_error(array('message' => 'فایلی انتخاب نشده'));

        require_once ABSPATH . 'wp-admin/includes/file.php';
        $upload = wp_handle_upload($_FILES['avatar'], array('test_form' => false));

        if (isset($upload['error'])) {
            wp_send_json_error(array('message' => $upload['error']));
        }

        wp_send_json_success(array('url' => $upload['url']));
    }
}

// پاسخ خودکار زمان‌بندی شده
add_action('cslc_auto_reply_event', function ($chat_id, $message) {
    CSLC_Database::save_message($chat_id, 'system', $message);
}, 10, 2);
