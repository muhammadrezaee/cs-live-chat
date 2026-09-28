<?php if (!defined('ABSPATH')) exit; ?>
<div id="cslc-chat-widget" class="cslc-chat-widget">
    <div class="cslc-chat-button" id="cslc-chat-toggle">
        <span class="cslc-chat-icon">💬</span>
        <span class="cslc-chat-badge" id="cslc-chat-badge" style="display:none;">0</span>
        <span class="cslc-pulse"></span>
    </div>

    <div class="cslc-chat-window" id="cslc-chat-window" style="display:none;">
        <div class="cslc-chat-header">
            <div class="cslc-header-info">
                <?php if (!empty($settings['site_logo'])): ?>
                    <img src="<?php echo esc_url($settings['site_logo']); ?>" alt="Logo" class="cslc-header-logo">
                <?php endif; ?>
                <div>
                    <h3><?php echo esc_html($settings['admin_name']); ?></h3>
                    <div class="cslc-status" id="cslc-status">
                        <span class="cslc-status-dot"></span>
                        <span id="cslc-status-text">آنلاین</span>
                    </div>
                </div>
            </div>
            <button class="cslc-chat-close" id="cslc-chat-close">×</button>
        </div>

        <!-- فرم قبل از چت -->
        <div class="cslc-pre-chat-form" id="cslc-pre-chat-form">
            <div class="cslc-welcome">
                <?php echo nl2br(esc_html($settings['welcome_message'])); ?>
            </div>
            <input type="text" id="cslc-user-name" placeholder="نام شما">
            <input type="email" id="cslc-user-email" placeholder="ایمیل شما">
            <button id="cslc-start-chat" class="cslc-start-btn">شروع گفتگو</button>
        </div>

        <!-- ناحیه پیام‌ها -->
        <div class="cslc-chat-messages" id="cslc-chat-messages" style="display:none;">
            <div class="cslc-typing-indicator" id="cslc-typing" style="display:none;">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- ناحیه ورودی -->
        <div class="cslc-chat-input-area" id="cslc-input-area" style="display:none;">
            <?php if ($settings['enable_emoji']): ?>
                <button type="button" class="cslc-emoji-btn" id="cslc-emoji-btn" title="انتخاب ایموجی">😊</button>
                <div class="cslc-emoji-picker" id="cslc-emoji-picker">
                    <div class="cslc-emoji-search">
                        <input type="text" id="cslc-emoji-search-input" placeholder="جستجوی ایموجی...">
                    </div>
                    <div class="cslc-emoji-list" id="cslc-emoji-list">
                        <?php
                        $emojis = array(
                            '😀',
                            '😃',
                            '😄',
                            '😁',
                            '😆',
                            '😅',
                            '😂',
                            '',
                            '😊',
                            '😇',
                            '🙂',
                            '🙃',
                            '😉',
                            '😌',
                            '😍',
                            '🥰',
                            '😘',
                            '',
                            '😙',
                            '😚',
                            '',
                            '😛',
                            '😝',
                            '😜',
                            '🤪',
                            '🤨',
                            '🧐',
                            '🤓',
                            '😎',
                            '🤩',
                            '🥳',
                            '😏',
                            '😒',
                            '😞',
                            '😔',
                            '😟',
                            '😕',
                            '🙁',
                            '☹️',
                            '😣',
                            '😖',
                            '😫',
                            '😩',
                            '🥺',
                            '😢',
                            '😭',
                            '😤',
                            '😠',
                            '😡',
                            '🤬',
                            '🤯',
                            '😳',
                            '',
                            '🥶',
                            '😱',
                            '😨',
                            '😰',
                            '😥',
                            '😓',
                            '',
                            '🤔',
                            '',
                            '🤫',
                            '🤥',
                            '😶',
                            '😐',
                            '😑',
                            '😬',
                            '',
                            '😯',
                            '😦',
                            '',
                            '😮',
                            '😲',
                            '🥱',
                            '😴',
                            '🤤',
                            '😪',
                            '😵',
                            '🤐',
                            '🥴',
                            '🤢',
                            '🤮',
                            '🤧',
                            '😷',
                            '🤒',
                            '🤕',
                            '',
                            '🤠',
                            '👍',
                            '👎',
                            '👌',
                            '️',
                            '🤞',
                            '🤟',
                            '',
                            '🤙',
                            '',
                            '👉',
                            '👆',
                            '👇',
                            '☝️',
                            '',
                            '🤚',
                            '🖐️',
                            '✋',
                            '🖖',
                            '',
                            '🙌',
                            '🤲',
                            '🤝',
                            '🙏',
                            '️',
                            '💪',
                            '🦾',
                            '❤️',
                            '🧡',
                            '💛',
                            '💚',
                            '💙',
                            '💜',
                            '',
                            '🤍',
                            '🤎',
                            '',
                            '❣️',
                            '💕',
                            '💞',
                            '💓',
                            '💗',
                            '💖',
                            '💘',
                            '💝',
                            '',
                            '☮️',
                            '✝️',
                            '️',
                            '🕉️',
                            '☸️',
                            '✡️',
                            '🔯',
                            '🕎',
                            '☯️',
                            '☦️',
                            '🛐',
                            '',
                            '♈',
                            '♉',
                            '',
                            '♋',
                            '♌',
                            '',
                            '♎',
                            '♏',
                            '♐',
                            '♑',
                            '♒',
                            '♓',
                            '🔥',
                            '✨',
                            '🎉',
                            '🎊',
                            '🎈',
                            '🎁',
                            '🏆',
                            '🥇',
                            '',
                            '💯',
                            '⭐',
                            '🌟',
                            '💫',
                            '💥',
                            '',
                            '💨',
                            '💦',
                            '',
                            '🎵',
                            '🎶',
                            '',
                            '🍵',
                            '',
                            '🍻',
                            '🥂',
                            '',
                            '🍸',
                            '🍹',
                            '🍾',
                            '🍽️',
                            '',
                            '🥄'
                        );
                        foreach ($emojis as $emoji):
                        ?>
                            <span class="cslc-emoji-item" data-emoji="<?php echo $emoji; ?>"><?php echo $emoji; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <textarea id="cslc-chat-input" placeholder="پیام خود را بنویسید..." rows="2"></textarea>

            <?php if (!empty($settings['enable_file_upload'])): ?>
                <div class="cslc-attach-wrapper">
                    <button type="button" class="cslc-attach-btn" id="cslc-attach-btn" title="پیوست فایل">📎</button>
                    <input type="file" id="cslc-file-input" class="cslc-file-input-hidden">
                </div>
            <?php endif; ?>

            <button id="cslc-chat-send" class="cslc-send-btn">➤</button>
        </div>

        <!-- فوتر -->
        <div class="cslc-chat-footer">
            <?php if (!empty($settings['whatsapp_number'])): ?>
                <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $settings['whatsapp_number'])); ?>" target="_blank" class="cslc-whatsapp-link">
                    📱 انتقال به واتس‌اپ
                </a>
            <?php endif; ?>
            <div class="cslc-footer-info">
                <span>قدرت گرفته از <a href="https://<?php echo esc_attr($settings['site_address']); ?>" target="_blank"><?php echo esc_html($settings['site_address']); ?></a></span>
            </div>
        </div>
    </div>

    <!-- مودال امتیازدهی -->
    <div class="cslc-rating-modal" id="cslc-rating-modal" style="display:none;">
        <h3>به پشتیبانی ما امتیاز دهید</h3>
        <div class="cslc-stars" id="cslc-stars">
            <span data-rating="1">★</span>
            <span data-rating="2">★</span>
            <span data-rating="3">★</span>
            <span data-rating="4">★</span>
            <span data-rating="5">★</span>
        </div>
        <textarea id="cslc-rating-comment" placeholder="نظر شما (اختیاری)"></textarea>
        <button id="cslc-submit-rating" class="cslc-submit-rating-btn">ثبت امتیاز</button>
    </div>
</div>