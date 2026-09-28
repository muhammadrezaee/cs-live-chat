jQuery(document).ready(function($) {
    console.log('📊 Stats script loaded!');
    
    function loadStats() {
        $.post(cslc_stats_vars.ajax_url, {
            action: 'cslc_admin_get_stats',
            nonce: cslc_stats_vars.nonce
        }, function(res) {
            console.log('Stats response:', res);
            if (res.success) {
                var s = res.data.stats;
                
                // آپدیت اعداد
                $('#stat-total-chats').text(s.total_chats);
                $('#stat-active-chats').text(s.active_chats);
                $('#stat-today-chats').text(s.today_chats);
                $('#stat-unread').text(s.unread_messages);
                $('#stat-rating').text(s.avg_rating + '/5');
                $('#stat-messages').text(s.total_messages);
                
                // نمودار میله‌ای
                renderBarChart(s.week_chats);
                
                // نمودار دایره‌ای
                renderPieChart(s);
            }
        }).fail(function(xhr) {
            console.error('Stats AJAX failed:', xhr);
        });
    }
    
    function renderBarChart(data) {
        var $chart = $('#cslc-week-chart');
        $chart.empty();
        
        if (!data || data.length === 0) {
            $chart.html('<div style="text-align:center;padding:50px;color:#999;">داده‌ای نیست</div>');
            return;
        }
        
        var max = Math.max.apply(null, data.map(function(d) { return d.count; }));
        if (max === 0) max = 1;
        
        var html = '<div class="cslc-bar-chart-container">';
        data.forEach(function(d) {
            var height = (d.count / max) * 100;
            var date = new Date(d.date).toLocaleDateString('fa-IR', {weekday: 'short'});
            html += '<div class="cslc-bar-item">' +
                    '<div class="cslc-bar" style="height: ' + height + '%;">' +
                    '<div class="cslc-bar-value">' + d.count + '</div>' +
                    '</div>' +
                    '<div class="cslc-bar-label">' + date + '</div>' +
                    '</div>';
        });
        html += '</div>';
        
        $chart.html(html);
    }
    
    function renderPieChart(stats) {
        var total = stats.total_chats || 1;
        var active = stats.active_chats || 0;
        var closed = stats.closed_chats || 0;
        
        var segments = [
            { value: active, color: '#00b894', label: 'فعال' },
            { value: closed, color: '#667eea', label: 'بسته شده' },
            { value: Math.max(0, total - active - closed), color: '#fdcb6e', label: 'سایر' }
        ];
        
        var gradientParts = [];
        var currentAngle = 0;
        
        segments.forEach(function(seg) {
            var percentage = (seg.value / total) * 100;
            gradientParts.push(seg.color + ' ' + currentAngle + '% ' + (currentAngle + percentage) + '%');
            currentAngle += percentage;
        });
        
        $('#cslc-pie-chart').css('background', 'conic-gradient(' + gradientParts.join(', ') + ')');
        $('#pie-total').text(total);
        
        var legendHtml = '';
        segments.forEach(function(seg) {
            var pct = ((seg.value / total) * 100).toFixed(1);
            legendHtml += '<div class="cslc-pie-legend-item">' +
                        '<span class="cslc-pie-legend-dot" style="background: ' + seg.color + '"></span>' +
                        '<span class="cslc-pie-legend-label">' + seg.label + '</span>' +
                        '<span class="cslc-pie-legend-value">' + seg.value + ' (' + pct + '%)</span>' +
                        '</div>';
        });
        $('#cslc-pie-legend').html(legendHtml);
    }
    
    loadStats();
    setInterval(loadStats, 10000);
});