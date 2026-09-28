jQuery(document).ready(function ($) {
  console.log("🚀 CS Live Chat Loaded");

  let sessionId = localStorage.getItem("cslc_session_id");
  let lastMessageId = 0;
  let chatId = null;
  let pollingInterval = null;
  let chatStarted = !cslc_vars.settings.enable_pre_chat_form;

  if (!sessionId) {
    sessionId =
      "cslc_" + Math.random().toString(36).substr(2, 9) + "_" + Date.now();
    localStorage.setItem("cslc_session_id", sessionId);
  }

  // باز/بسته کردن
  $("#cslc-chat-toggle").on("click", function () {
    const $window = $("#cslc-chat-window");
    if ($window.is(":visible")) {
      $window.slideUp(300);
      stopPolling();
    } else {
      $window.slideDown(300);
      if (chatStarted) {
        ensureMessagesVisible(); // ✅ تضمین نمایش
        loadMessages();
        startPolling();
      }
      $("#cslc-chat-badge").hide();
    }
  });

  $("#cslc-chat-close").on("click", function () {
    $("#cslc-chat-window").slideUp(300);
    stopPolling();
  });

  // شروع چت
  $("#cslc-start-chat").on("click", function () {
    const name = $("#cslc-user-name").val().trim();
    const email = $("#cslc-user-email").val().trim();

    if (!name || !email) {
      alert("لطفاً نام و ایمیل را وارد کنید");
      return;
    }

    $.post(
      cslc_vars.ajax_url,
      {
        action: "cslc_update_user_info",
        nonce: cslc_vars.nonce,
        session_id: sessionId,
        name: name,
        email: email,
      },
      function (res) {
        if (res.success) {
          chatStarted = true;
          $("#cslc-pre-chat-form").fadeOut(300, function () {
            ensureMessagesVisible(); // ✅ تضمین نمایش
            loadMessages();
            startPolling();
          });
        }
      },
    );
  });

  // ✅ تابع جدید: تضمین نمایش المان‌های پیام
  function ensureMessagesVisible() {
    const $messages = $("#cslc-chat-messages");
    const $input = $("#cslc-input-area");

    if ($messages.is(":hidden")) {
      $messages.show();
      console.log("✅ Messages area shown");
    }
    if ($input.is(":hidden")) {
      $input.show();
      console.log("✅ Input area shown");
    }
  }

  // ارسال پیام
  $("#cslc-chat-send").on("click", sendMessage);
  $("#cslc-chat-input").on("keypress", function (e) {
    if (e.which === 13 && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  function sendMessage() {
    const message = $("#cslc-chat-input").val().trim();
    if (!message) return;

    $("#cslc-chat-input").val("");

    // ✅ نمایش فوری پیام کاربر
    appendMessage({
      sender_type: "user",
      message: message,
      created_at: new Date().toISOString(),
    });

    $.post(
      cslc_vars.ajax_url,
      {
        action: "cslc_send_message",
        nonce: cslc_vars.nonce,
        session_id: sessionId,
        message: message,
      },
      function (res) {
        if (res.success) {
          chatId = res.data.chat_id;
          lastMessageId = res.data.message_id;
        }
      },
    );
  }

  // دریافت پیام‌ها
  function loadMessages() {
    $.post(
      cslc_vars.ajax_url,
      {
        action: "cslc_get_messages",
        nonce: cslc_vars.nonce,
        session_id: sessionId,
        last_message_id: lastMessageId,
      },
      function (res) {
        if (res.success) {
          chatId = res.data.chat_id;

          if (lastMessageId === 0) {
            // بارگذاری اولیه
            $("#cslc-chat-messages").empty();
            if (res.data.messages && res.data.messages.length > 0) {
              res.data.messages.forEach((msg) => appendMessage(msg));
              lastMessageId =
                res.data.messages[res.data.messages.length - 1].id;
            }
          } else {
            // پیام‌های جدید
            if (res.data.messages && res.data.messages.length > 0) {
              res.data.messages.forEach((msg) => {
                appendMessage(msg);
                if (msg.id > lastMessageId) lastMessageId = msg.id;
              });

              if (
                !$("#cslc-chat-window").is(":visible") &&
                cslc_vars.settings.enable_sound == 1
              ) {
                playSound();
                updateBadge();
              }
            }
          }
        }
      },
    );
  }

  function appendMessage(msg) {
    console.log("📩 Appending:", msg.sender_type, msg.message);

    const $messages = $("#cslc-chat-messages");

    // ✅ اطمینان از وجود المان
    if ($messages.length === 0) {
      console.error("❌ #cslc-chat-messages not found!");
      return;
    }

    const time = new Date(msg.created_at).toLocaleTimeString("fa-IR", {
      hour: "2-digit",
      minute: "2-digit",
    });

    let content = escapeHtml(msg.message);
    if (msg.file_url) {
      content =
        '📎 <a href="' +
        msg.file_url +
        '" target="_blank" style="color:inherit;">' +
        escapeHtml(msg.file_name || "فایل") +
        "</a>";
    }

    const html =
      '<div class="cslc-message ' +
      msg.sender_type +
      '">' +
      '<div class="cslc-message-bubble">' +
      content +
      "</div>" +
      '<div class="cslc-message-time">' +
      time +
      "</div>" +
      "</div>";

    $messages.append(html);

    // اسکرول به پایین
    $messages.stop().animate(
      {
        scrollTop: $messages[0].scrollHeight,
      },
      300,
    );

    console.log(
      "✅ Message appended. Total messages:",
      $messages.children().length,
    );
  }

  function escapeHtml(text) {
    if (!text) return "";
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return text.replace(/[&<>"']/g, (m) => map[m]);
  }

  function updateBadge() {
    const current = parseInt($("#cslc-chat-badge").text() || 0);
    $("#cslc-chat-badge")
      .text(current + 1)
      .show();
  }

  function playSound() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.frequency.value = 800;
      osc.type = "sine";
      gain.gain.setValueAtTime(0.3, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
      osc.start(ctx.currentTime);
      osc.stop(ctx.currentTime + 0.5);
    } catch (e) {}
  }

  function startPolling() {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(loadMessages, 3000);
  }

  function stopPolling() {
    if (pollingInterval) {
      clearInterval(pollingInterval);
      pollingInterval = null;
    }
  }

  // ایموجی
  // ===== ایموجی پیکر =====
  const $emojiBtn = $("#cslc-emoji-btn");
  const $emojiPicker = $("#cslc-emoji-picker");
  const $emojiSearch = $("#cslc-emoji-search-input");
  const $emojiItems = $(".cslc-emoji-item");
  const $chatInput = $("#cslc-chat-input");

  // باز/بسته کردن پنل ایموجی
  $emojiBtn.on("click", function (e) {
    e.stopPropagation();
    $emojiPicker.toggleClass("show");
    $emojiBtn.toggleClass("active");

    if ($emojiPicker.hasClass("show")) {
      $emojiSearch.focus();
    }
  });

  // جلوگیری از بسته شدن هنگام کلیک داخل پنل
  $emojiPicker.on("click", function (e) {
    e.stopPropagation();
  });

  // بستن با کلیک بیرون
  $(document).on("click", function (e) {
    if (!$emojiPicker.is(e.target) && $emojiPicker.has(e.target).length === 0) {
      $emojiPicker.removeClass("show");
      $emojiBtn.removeClass("active");
    }
  });

  // انتخاب ایموجی
  $emojiItems.on("click", function () {
    const emoji = $(this).data("emoji");
    const cursorPos = $chatInput[0].selectionStart;
    const textBefore = $chatInput.val().substring(0, cursorPos);
    const textAfter = $chatInput.val().substring(cursorPos);

    // درج ایموجی در محل کرسر
    $chatInput.val(textBefore + emoji + textAfter);
    $chatInput.focus();

    // حرکت کرسر بعد از ایموجی
    $chatInput[0].selectionStart = cursorPos + emoji.length;
    $chatInput[0].selectionEnd = cursorPos + emoji.length;

    // انیمیشن کوچک
    $(this).css("transform", "scale(0.8)");
    setTimeout(() => $(this).css("transform", ""), 150);
  });

  // جستجوی ایموجی
  $emojiSearch.on("input", function () {
    const query = $(this).val().toLowerCase().trim();

    if (query === "") {
      $emojiItems.removeClass("hidden");
      return;
    }

    // جستجو بر اساس نام یا شکل ایموجی
    $emojiItems.each(function () {
      const emoji = $(this).data("emoji");
      // جستجوی ساده: اگر ایموجی شامل کاراکترهای جستجو باشه
      if (emoji.includes(query)) {
        $(this).removeClass("hidden");
      } else {
        $(this).addClass("hidden");
      }
    });
  });

  // بستن با Escape
  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $emojiPicker.hasClass("show")) {
      $emojiPicker.removeClass("show");
      $emojiBtn.removeClass("active");
      $chatInput.focus();
    }
  });

  // attachment
  // ===== آپلود فایل حرفه‌ای =====
  const $attachBtn = $("#cslc-attach-btn");
  const $fileInput = $("#cslc-file-input");
  const $chatMessages = $("#cslc-chat-messages");

  // گرفتن فرمت‌های مجاز از سرور
  const allowedExtensions = cslc_vars.allowed_extensions || [
    "jpg",
    "jpeg",
    "png",
    "gif",
    "pdf",
    "doc",
    "docx",
    "zip",
  ];
  const maxFileSize = cslc_vars.max_file_size || 5242880; // 5MB پیش‌فرض

  // نمایش خطا
  function showErrorMessage(message) {
    const errorHtml = `
            <div class="cslc-error-message">
                <span>⚠️</span>
                <span>${message}</span>
                <button class="cslc-close-error" onclick="$(this).parent().remove()">×</button>
            </div>
        `;
    $chatMessages.append(errorHtml);
    $chatMessages.scrollTop($chatMessages[0].scrollHeight);

    // حذف خودکار بعد از 5 ثانیه
    setTimeout(() => {
      $(".cslc-error-message").fadeOut(300, function () {
        $(this).remove();
      });
    }, 5000);
  }

  // نمایش پیشرفت آپلود
  function showUploadProgress(fileName) {
    const progressHtml = `
            <div class="cslc-upload-progress" id="cslc-upload-progress">
                <div class="cslc-spinner"></div>
                <div class="cslc-file-name">در حال آپلود: ${fileName}</div>
            </div>
        `;
    $chatMessages.append(progressHtml);
    $chatMessages.scrollTop($chatMessages[0].scrollHeight);
  }

  function hideUploadProgress() {
    $("#cslc-upload-progress").fadeOut(300, function () {
      $(this).remove();
    });
  }

  // بررسی فرمت فایل
  function validateFile(file) {
    const fileName = file.name.toLowerCase();
    const extension = fileName.split(".").pop();

    // بررسی فرمت
    if (!allowedExtensions.includes(extension)) {
      const allowedList = allowedExtensions
        .map((ext) => ext.toUpperCase())
        .join("، ");
      return {
        valid: false,
        message: `فرمت فایل مجاز نیست. فرمت‌های مجاز: ${allowedList}`,
      };
    }

    // بررسی حجم
    if (file.size > maxFileSize) {
      const maxSizeMB = (maxFileSize / 1048576).toFixed(2);
      return {
        valid: false,
        message: `حجم فایل بیش از حد مجاز است. حداکثر حجم: ${maxSizeMB} مگابایت`,
      };
    }

    return { valid: true };
  }

  // تغییر فایل
  $fileInput.on("change", function (e) {
    const file = e.target.files[0];

    if (!file) return;

    console.log(
      "📎 File selected:",
      file.name,
      "Size:",
      file.size,
      "Type:",
      file.type,
    );

    // اعتبارسنجی
    const validation = validateFile(file);
    if (!validation.valid) {
      showErrorMessage(validation.message);
      $fileInput.val(""); // پاک کردن input
      return;
    }

    // نمایش پیشرفت
    showUploadProgress(file.name);
    $attachBtn.addClass("uploading");

    // ارسال فایل
    const formData = new FormData();
    formData.append("action", "cslc_upload_file");
    formData.append("nonce", cslc_vars.nonce);
    formData.append("session_id", sessionId);
    formData.append("file", file);

    $.ajax({
      url: cslc_vars.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        hideUploadProgress();
        $attachBtn.removeClass("uploading");

        if (res.success) {
          lastMessageId = res.data.message_id;
          appendMessage({
            sender_type: "user",
            message: "📎 " + res.data.file_name,
            file_url: res.data.file_url,
            file_name: res.data.file_name,
            created_at: new Date().toISOString(),
          });
          console.log("✅ File uploaded successfully");
        } else {
          showErrorMessage(res.data.message || "خطا در آپلود فایل");
          console.error("❌ Upload failed:", res);
        }
      },
      error: function (xhr, status, error) {
        hideUploadProgress();
        $attachBtn.removeClass("uploading");
        showErrorMessage("خطا در ارتباط با سرور. لطفاً دوباره تلاش کنید.");
        console.error("❌ Upload error:", error);
      },
      complete: function () {
        $fileInput.val(""); // پاک کردن input برای انتخاب مجدد
      },
    });
  });
});
