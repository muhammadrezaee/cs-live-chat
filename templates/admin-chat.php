<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap cslc-admin-wrap">
    <h1>💬 چت زنده - مدیریت پیام‌ها</h1>
    
    <div class="cslc-admin-container">
        <div class="cslc-admin-sidebar">
            <div class="cslc-sidebar-header">
                <h3>چت‌های فعال</h3>
                <button class="cslc-refresh-btn" id="cslc-refresh-chats">🔄</button>
            </div>
            <div id="cslc-admin-chats-list" class="cslc-admin-chats-list">
                <p class="cslc-loading">در حال بارگذاری...</p>
            </div>
        </div>
        
        <div class="cslc-admin-main">
            <div class="cslc-admin-chat-header" id="cslc-admin-chat-header" style="display:none;">
                <div class="cslc-chat-header-info">
                    <h3 id="cslc-admin-chat-title">چت</h3>
                    <div class="cslc-chat-meta" id="cslc-chat-meta"></div>
                </div>
                <div class="cslc-chat-actions">
                    <button class="cslc-action-btn cslc-export-btn" id="cslc-export-csv" title="خروجی CSV">📥</button>
                    <button class="cslc-action-btn cslc-block-btn" id="cslc-block-user" title="مسدود کردن">🚫</button>
                    <button class="cslc-action-btn cslc-close-btn" id="cslc-close-chat" title="بستن چت">✓</button>
                    <button class="cslc-action-btn cslc-delete-btn" id="cslc-delete-chat" title="حذف">🗑️</button>
                </div>
            </div>
            
            <div class="cslc-admin-chat-messages" id="cslc-admin-chat-messages">
                <div class="cslc-empty-state">
                    <div class="cslc-empty-icon">💬</div>
                    <h3>یک چت را انتخاب کنید</h3>
                    <p>از لیست سمت راست یک چت را انتخاب کنید تا پیام‌ها نمایش داده شوند</p>
                </div>
            </div>
            
            <div class="cslc-admin-chat-input-area" id="cslc-admin-input-area" style="display:none;">
                <textarea id="cslc-admin-chat-input" placeholder="پیام خود را بنویسید..." rows="2"></textarea>
                <button id="cslc-admin-chat-send" class="cslc-admin-send-btn">ارسال</button>
            </div>
        </div>
    </div>
</div>