jQuery(document).ready(function($) {
    let currentChatId = null;
    let lastMessageId = 0;
    let lastUnreadCount = 0;
    
    // 🔊 صدای بیب برای ادمین
    function playAdminBeep() {
        if (cslc_admin.enable_sound != 1) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            [0, 0.2].forEach(delay => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 1000;
                osc.type = 'sine';
                gain.gain.setValueAtTime(0.4, ctx.currentTime + delay);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + delay + 0.15);
                osc.start(ctx.currentTime + delay);
                osc.stop(ctx.currentTime + delay + 0.15);
            });
        } catch(e) {}
    }
    
    function loadChats() {
        $.post(cslc_admin.ajax_url, { 
            action: 'cslc_admin_get_chats', 
            nonce: cslc_admin.nonce 
        }, function(res) {
            if (res.success) {
                renderChatsList(res.data.chats);
                const totalUnread = res.data.chats.reduce((sum, c) => sum + parseInt(c.unread_count), 0);
                if (totalUnread > lastUnreadCount && lastUnreadCount > 0) {
                    playAdminBeep();
                    flashTitle();
                }
                lastUnreadCount = totalUnread;
            }
        });
    }
    
    let flashInterval = null;
    function flashTitle() {
        const original = document.title;
        let flash = true;
        if (flashInterval) clearInterval(flashInterval);
        flashInterval = setInterval(() => {
            document.title = flash ? '🔔 پیام جدید!' : original;
            flash = !flash;
        }, 1000);
        setTimeout(() => { 
            clearInterval(flashInterval); 
            document.title = original; 
        }, 5000);
        $(window).on('focus', function() { 
            clearInterval(flashInterval); 
            document.title = original; 
        });
    }
    
    function renderChatsList(chats) {
        const $list = $('#cslc-admin-chats-list');
        $list.empty();
        if (chats.length === 0) { 
            $list.html('<p class="cslc-loading">هیچ چتی نیست</p>'); 
            return; 
        }
        
        chats.forEach(chat => {
            const lastMsg = chat.last_message ? chat.last_message.message : 'بدون پیام';
            const badge = chat.unread_count > 0 ? 
                '<span class="cslc-admin-chat-item-badge">' + chat.unread_count + '</span>' : '';
            const userName = chat.user_name || 'کاربر ناشناس';
            const time = new Date(chat.updated_at).toLocaleTimeString('fa-IR', {
                hour:'2-digit', 
                minute:'2-digit'
            });
            
            const html = '<div class="cslc-admin-chat-item ' + 
                        (chat.id == currentChatId ? 'active' : '') + 
                        '" data-chat-id="' + chat.id + '">' +
                        '<div class="cslc-admin-chat-item-title">' +
                        '<span>👤 ' + escapeHtml(userName) + '</span>' + badge + 
                        '</div>' +
                        '<div class="cslc-admin-chat-item-preview">' + 
                        escapeHtml(lastMsg) + '</div>' +
                        '<div style="font-size:11px;opacity:0.6;margin-top:5px;">' + 
                        time + '</div></div>';
            $list.append(html);
        });
        
        $('.cslc-admin-chat-item').on('click', function() { 
            selectChat($(this).data('chat-id')); 
        });
    }
    
    function selectChat(chatId) {
        console.log("🎯 Selecting chat:", chatId);
        currentChatId = chatId;
        lastMessageId = 0;
        
        $('.cslc-admin-chat-item').removeClass('active');
        $('.cslc-admin-chat-item[data-chat-id="' + chatId + '"]').addClass('active');
        
        $('#cslc-admin-chat-header').show();
        $('#cslc-admin-input-area').show();
        $('#cslc-admin-chat-title').text('چت #' + chatId);
        
        // ✅ پاک کردن کامل و نمایش پیام‌ها
        const $messages = $('#cslc-admin-chat-messages');
        $messages.empty().show();
        
        loadChatMessages();
    }
    
    function loadChatMessages() {
        if (!currentChatId) return;
        
        $.post(cslc_admin.ajax_url, { 
            action: 'cslc_admin_get_messages', 
            nonce: cslc_admin.nonce, 
            chat_id: currentChatId 
        }, function(res) {
            console.log("📥 Admin received messages:", res);
            if (res.success) {
                renderMessages(res.data.messages);
                if (res.data.messages.length > 0) {
                    lastMessageId = res.data.messages[res.data.messages.length - 1].id;
                }
            }
        });
    }
    
    function renderMessages(messages) {
        console.log("🎨 Rendering", messages.length, "messages");
        const $messages = $('#cslc-admin-chat-messages');
        
        if ($messages.length === 0) {
            console.error("❌ #cslc-admin-chat-messages not found!");
            return;
        }
        
        $messages.empty();
        
        if (!messages || messages.length === 0) {
            $messages.html('<div class="cslc-empty-state"><p>هنوز پیامی نیست</p></div>');
            return;
        }
        
        messages.forEach(msg => appendMessage(msg));
        
        // اسکرول به پایین
        $messages.stop().animate({
            scrollTop: $messages[0].scrollHeight
        }, 300);
    }
    
    function appendMessage(msg) {
        console.log("📩 Admin appending:", msg.sender_type, msg.message);
        const $messages = $('#cslc-admin-chat-messages');
        
        const time = new Date(msg.created_at).toLocaleTimeString('fa-IR', {
            hour:'2-digit', 
            minute:'2-digit'
        });
        
        let content = escapeHtml(msg.message);
        if (msg.file_url) {
            content = '📎 <a href="' + msg.file_url + '" target="_blank" style="color:inherit;">' + 
                      escapeHtml(msg.file_name) + '</a>';
        }
        
        const html = '<div class="cslc-message ' + msg.sender_type + '">' +
                     '<div class="cslc-message-bubble">' + content + '</div>' +
                     '<div class="cslc-message-time">' + time + '</div>' +
                     '</div>';
        
        $messages.append(html);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    // ارسال پیام ادمین
    $('#cslc-admin-chat-send').on('click', sendAdminMessage);
    $('#cslc-admin-chat-input').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) { 
            e.preventDefault(); 
            sendAdminMessage(); 
        }
    });
    
    function sendAdminMessage() {
        if (!currentChatId) return;
        const message = $('#cslc-admin-chat-input').val().trim();
        if (!message) return;
        
        $('#cslc-admin-chat-input').val('');
        
        // ✅ نمایش فوری
        appendMessage({ 
            sender_type: 'admin', 
            message: message, 
            created_at: new Date().toISOString() 
        });
        
        // اسکرول
        const $messages = $('#cslc-admin-chat-messages');
        $messages.stop().animate({
            scrollTop: $messages[0].scrollHeight
        }, 300);
        
        $.post(cslc_admin.ajax_url, { 
            action: 'cslc_admin_send', 
            nonce: cslc_admin.nonce, 
            chat_id: currentChatId, 
            message: message 
        });
    }
    
    // دکمه‌های اکشن
    $('#cslc-close-chat').on('click', function() {
        if (!currentChatId || !confirm('بسته شود؟')) return;
        $.post(cslc_admin.ajax_url, { 
            action: 'cslc_admin_close_chat', 
            nonce: cslc_admin.nonce, 
            chat_id: currentChatId 
        }, function() { 
            loadChats(); 
            alert('بسته شد'); 
        });
    });
    
    $('#cslc-delete-chat').on('click', function() {
        if (!currentChatId || !confirm('حذف شود؟')) return;
        $.post(cslc_admin.ajax_url, { 
            action: 'cslc_admin_delete_chat', 
            nonce: cslc_admin.nonce, 
            chat_id: currentChatId 
        }, function() {
            currentChatId = null;
            $('#cslc-admin-chat-header, #cslc-admin-input-area').hide();
            $('#cslc-admin-chat-messages').html(
                '<div class="cslc-empty-state"><div class="cslc-empty-icon">💬</div>' +
                '<h3>یک چت را انتخاب کنید</h3></div>'
            );
            loadChats();
        });
    });
    
    $('#cslc-block-user').on('click', function() {
        if (!currentChatId) return;
        const reason = prompt('دلیل مسدودسازی:');
        if (reason === null) return;
        $.post(cslc_admin.ajax_url, { 
            action: 'cslc_admin_block_user', 
            nonce: cslc_admin.nonce, 
            chat_id: currentChatId, 
            reason: reason 
        }, function() { 
            loadChats(); 
            alert('مسدود شد'); 
        });
    });
    
    $('#cslc-export-csv').on('click', function() {
        if (!currentChatId) return;
        $.post(cslc_admin.ajax_url, { 
            action: 'cslc_admin_export_csv', 
            nonce: cslc_admin.nonce, 
            chat_id: currentChatId 
        }, function(res) {
            if (res.success) {
                const blob = new Blob(['\uFEFF' + res.data.csv], {type: 'text/csv;charset=utf-8;'});
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'chat-' + currentChatId + '.csv';
                link.click();
            }
        });
    });
    
    $('#cslc-refresh-chats').on('click', loadChats);
    
    // شروع
    loadChats();
    setInterval(loadChats, 5000);
    setInterval(function() { 
        if (currentChatId) loadChatMessages(); 
    }, 3000);
});