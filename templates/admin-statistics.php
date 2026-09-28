<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap cslc-stats-wrap">
    <h1 class="cslc-stats-title">
        <span class="cslc-title-icon">📊</span>
        داشبورد آمار چت زنده
    </h1>
    
    <!-- کارت‌های آمار اصلی -->
    <div class="cslc-stats-grid">
        <div class="cslc-stat-card cslc-stat-card-1">
            <div class="cslc-stat-bg"></div>
            <div class="cslc-stat-content">
                <div class="cslc-stat-header">
                    <div class="cslc-stat-icon">💬</div>
                    <div class="cslc-stat-trend up">+12%</div>
                </div>
                <div class="cslc-stat-value" id="stat-total-chats">0</div>
                <div class="cslc-stat-label">کل چت‌ها</div>
                <div class="cslc-stat-bar">
                    <div class="cslc-stat-bar-fill" style="width: 75%"></div>
                </div>
            </div>
        </div>
        
        <div class="cslc-stat-card cslc-stat-card-2">
            <div class="cslc-stat-bg"></div>
            <div class="cslc-stat-content">
                <div class="cslc-stat-header">
                    <div class="cslc-stat-icon">🟢</div>
                    <div class="cslc-stat-trend up">+5%</div>
                </div>
                <div class="cslc-stat-value" id="stat-active-chats">0</div>
                <div class="cslc-stat-label">چت فعال</div>
                <div class="cslc-stat-bar">
                    <div class="cslc-stat-bar-fill" style="width: 45%"></div>
                </div>
            </div>
        </div>
        
        <div class="cslc-stat-card cslc-stat-card-3">
            <div class="cslc-stat-bg"></div>
            <div class="cslc-stat-content">
                <div class="cslc-stat-header">
                    <div class="cslc-stat-icon">📩</div>
                    <div class="cslc-stat-trend down">-3%</div>
                </div>
                <div class="cslc-stat-value" id="stat-today-chats">0</div>
                <div class="cslc-stat-label">چت امروز</div>
                <div class="cslc-stat-bar">
                    <div class="cslc-stat-bar-fill" style="width: 30%"></div>
                </div>
            </div>
        </div>
        
        <div class="cslc-stat-card cslc-stat-card-4">
            <div class="cslc-stat-bg"></div>
            <div class="cslc-stat-content">
                <div class="cslc-stat-header">
                    <div class="cslc-stat-icon"></div>
                    <div class="cslc-stat-trend up">+8%</div>
                </div>
                <div class="cslc-stat-value" id="stat-unread">0</div>
                <div class="cslc-stat-label">پیام خوانده نشده</div>
                <div class="cslc-stat-bar">
                    <div class="cslc-stat-bar-fill" style="width: 60%"></div>
                </div>
            </div>
        </div>
        
        <div class="cslc-stat-card cslc-stat-card-5">
            <div class="cslc-stat-bg"></div>
            <div class="cslc-stat-content">
                <div class="cslc-stat-header">
                    <div class="cslc-stat-icon">⭐</div>
                    <div class="cslc-stat-trend up">+15%</div>
                </div>
                <div class="cslc-stat-value" id="stat-rating">0</div>
                <div class="cslc-stat-label">میانگین امتیاز</div>
                <div class="cslc-stat-bar">
                    <div class="cslc-stat-bar-fill" style="width: 85%"></div>
                </div>
            </div>
        </div>
        
        <div class="cslc-stat-card cslc-stat-card-6">
            <div class="cslc-stat-bg"></div>
            <div class="cslc-stat-content">
                <div class="cslc-stat-header">
                    <div class="cslc-stat-icon">💌</div>
                    <div class="cslc-stat-trend up">+20%</div>
                </div>
                <div class="cslc-stat-value" id="stat-messages">0</div>
                <div class="cslc-stat-label">کل پیام‌ها</div>
                <div class="cslc-stat-bar">
                    <div class="cslc-stat-bar-fill" style="width: 90%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- نمودارها -->
    <div class="cslc-charts-row">
        <!-- نمودار میله‌ای -->
        <div class="cslc-chart-box">
            <div class="cslc-chart-header">
                <h2>📈 چت‌های 7 روز اخیر</h2>
                <div class="cslc-chart-legend">
                    <span class="cslc-legend-item">
                        <span class="cslc-legend-dot" style="background: #667eea"></span>
                        تعداد چت
                    </span>
                </div>
            </div>
            <div class="cslc-chart-body">
                <div class="cslc-bar-chart" id="cslc-week-chart">
                    <div class="cslc-chart-loading">
                        <div class="cslc-spinner"></div>
                        <p>در حال بارگذاری...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- نمودار دایره‌ای -->
        <div class="cslc-chart-box">
            <div class="cslc-chart-header">
                <h2>🥧 وضعیت چت‌ها</h2>
            </div>
            <div class="cslc-chart-body">
                <div class="cslc-pie-chart-container">
                    <div class="cslc-pie-chart" id="cslc-pie-chart">
                        <div class="cslc-pie-center">
                            <div class="cslc-pie-value" id="pie-total">0</div>
                            <div class="cslc-pie-label">کل</div>
                        </div>
                    </div>
                    <div class="cslc-pie-legend" id="cslc-pie-legend">
                        <!-- پر می‌شود با JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- آمار سریع -->
    <div class="cslc-quick-stats">
        <div class="cslc-quick-stat">
            <div class="cslc-quick-icon">⚡</div>
            <div class="cslc-quick-info">
                <div class="cslc-quick-value" id="stat-response-time">2.5 دقیقه</div>
                <div class="cslc-quick-label">میانگین زمان پاسخ</div>
            </div>
        </div>
        
        <div class="cslc-quick-stat">
            <div class="cslc-quick-icon">🎯</div>
            <div class="cslc-quick-info">
                <div class="cslc-quick-value" id="stat-satisfaction">92%</div>
                <div class="cslc-quick-label">رضایت کاربران</div>
            </div>
        </div>
        
        <div class="cslc-quick-stat">
            <div class="cslc-quick-icon">🔥</div>
            <div class="cslc-quick-info">
                <div class="cslc-quick-value" id="stat-peak-hour">14:00</div>
                <div class="cslc-quick-label">ساعت اوج ترافیک</div>
            </div>
        </div>
        
        <div class="cslc-quick-stat">
            <div class="cslc-quick-icon">📱</div>
            <div class="cslc-quick-info">
                <div class="cslc-quick-value" id="stat-mobile">68%</div>
                <div class="cslc-quick-label">کاربران موبایل</div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    function loadStats() {
        $.post(ajaxurl, {
            action: 'cslc_admin_get_stats',
            nonce: '<?php echo wp_create_nonce('cslc_admin_nonce'); ?>'
        }, function(res) {
            if (res.success) {
                var s = res.data.stats;
                
                // انیمیشن اعداد
                animateValue('#stat-total-chats', 0, s.total_chats, 1000);
                animateValue('#stat-active-chats', 0, s.active_chats, 1000);
                animateValue('#stat-today-chats', 0, s.today_chats, 1000);
                animateValue('#stat-unread', 0, s.unread_messages, 1000);
                $('#stat-rating').text(s.avg_rating + '/5');
                animateValue('#stat-messages', 0, s.total_messages, 1000);
                
                // نمودار میله‌ای
                renderBarChart(s.week_chats);
                
                // نمودار دایره‌ای
                renderPieChart(s);
            }
        });
    }
    
    // انیمیشن شمارش اعداد
    function animateValue(selector, start, end, duration) {
        var obj = $(selector);
        var range = end - start;
        var minTimer = 50;
        var stepTime = Math.abs(Math.floor(duration / range));
        stepTime = Math.max(stepTime, minTimer);
        var startTime = new Date().getTime();
        var endTime = startTime + duration;
        var timer;
        
        function run() {
            var now = new Date().getTime();
            var remaining = Math.max((endTime - now) / duration, 0);
            var value = Math.round(end - (remaining * range));
            obj.text(value);
            if (value == end) {
                clearInterval(timer);
            }
        }
        
        timer = setInterval(run, stepTime);
        run();
    }
    
    // نمودار میله‌ای
    function renderBarChart(data) {
        var $chart = $('#cslc-week-chart');
        $chart.empty();
        
        if (!data || data.length === 0) {
            $chart.html('<div class="cslc-chart-empty">داده‌ای برای نمایش وجود ندارد</div>');
            return;
        }
        
        var max = Math.max.apply(null, data.map(function(d) { return d.count; }));
        if (max === 0) max = 1;
        
        var chartHtml = '<div class="cslc-bar-chart-container">';
        
        // خطوط گرید
        chartHtml += '<div class="cslc-chart-grid">';
        for (var i = 0; i <= 4; i++) {
            var value = Math.round((max / 4) * i);
            chartHtml += '<div class="cslc-grid-line"><span class="cslc-grid-value">' + value + '</span></div>';
        }
        chartHtml += '</div>';
        
        // میله‌ها
        chartHtml += '<div class="cslc-bars-wrapper">';
        data.forEach(function(d, index) {
            var height = (d.count / max) * 100;
            var date = new Date(d.date).toLocaleDateString('fa-IR', {weekday: 'short'});
            chartHtml += '<div class="cslc-bar-item">' +
                        '<div class="cslc-bar-tooltip">' + d.count + ' چت</div>' +
                        '<div class="cslc-bar" style="height: 0%;" data-height="' + height + '">' +
                        '<div class="cslc-bar-value">' + d.count + '</div>' +
                        '</div>' +
                        '<div class="cslc-bar-label">' + date + '</div>' +
                        '</div>';
        });
        chartHtml += '</div>';
        chartHtml += '</div>';
        
        $chart.html(chartHtml);
        
        // انیمیشن میله‌ها
        setTimeout(function() {
            $('.cslc-bar').each(function(index) {
                var $bar = $(this);
                var targetHeight = $bar.data('height');
                setTimeout(function() {
                    $bar.css('height', targetHeight + '%');
                }, index * 100);
            });
        }, 100);
    }
    
    // نمودار دایره‌ای
    function renderPieChart(stats) {
        var total = stats.total_chats || 1;
        var active = stats.active_chats || 0;
        var closed = stats.closed_chats || 0;
        var blocked = stats.blocked_chats || 0;
        var other = total - active - closed - blocked;
        
        var segments = [
            { value: active, color: '#00b894', label: 'فعال' },
            { value: closed, color: '#667eea', label: 'بسته شده' },
            { value: blocked, color: '#d63031', label: 'مسدود' },
            { value: Math.max(0, other), color: '#fdcb6e', label: 'سایر' }
        ];
        
        var $pie = $('#cslc-pie-chart');
        var $legend = $('#cslc-pie-legend');
        
        // ساخت نمودار دایره‌ای با conic-gradient
        var gradientParts = [];
        var currentAngle = 0;
        
        segments.forEach(function(seg) {
            var percentage = (seg.value / total) * 100;
            var startAngle = currentAngle;
            var endAngle = currentAngle + percentage;
            gradientParts.push(seg.color + ' ' + startAngle + '% ' + endAngle + '%');
            currentAngle = endAngle;
        });
        
        $pie.css('background', 'conic-gradient(' + gradientParts.join(', ') + ')');
        
        // مرکز نمودار
        $('#pie-total').text(total);
        
        //_legend
        var legendHtml = '';
        segments.forEach(function(seg) {
            var percentage = ((seg.value / total) * 100).toFixed(1);
            legendHtml += '<div class="cslc-pie-legend-item">' +
                        '<span class="cslc-pie-legend-dot" style="background: ' + seg.color + '"></span>' +
                        '<span class="cslc-pie-legend-label">' + seg.label + '</span>' +
                        '<span class="cslc-pie-legend-value">' + seg.value + ' (' + percentage + '%)</span>' +
                        '</div>';
        });
        $legend.html(legendHtml);
    }
    
    loadStats();
    setInterval(loadStats, 10000);
});
</script>