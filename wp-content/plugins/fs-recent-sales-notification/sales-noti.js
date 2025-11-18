jQuery(function ($) {
    function showNotification(data) {
        let minutes = data.minutes_ago;
        let ago = minutes <= 1 ? 'just now' : (minutes + ' min ago');
        let product_url = data.product_url || '#';
        let product_image = data.product_image || 'https://via.placeholder.com/80';

        let html = `
        <div class="fs-sales-noti">
            <div class="fs-thumb">
                <img src="${product_image}" alt="${data.product}">
            </div>
            <div class="fs-content">
                <div class="fs-name">${data.customer}</div>
                <div class="fs-product">${data.product}</div>
                <a href="${product_url}" class="fs-view-item" target="_blank">View Item</a>
                <div class="fs-time">${ago}</div>
            </div>
        </div>`;

        let $el = $(html).hide().appendTo('body').fadeIn(300);
        setTimeout(() => {
            $el.addClass('fs-fade-out');
            setTimeout(() => $el.remove(), 500);
        }, 5000);
    }

    // hiển thị ngay order vừa đặt
    window.fs_show_notification_immediately = function(data) {
        showNotification(data);
    }

    // show random
    function fetchAndShow() {
        $.get(fs_sales_ajax.ajax_url, { action: 'fs_get_recent_orders' }, function (arr) {
            if (arr && arr.length) {
                let n = Math.floor(Math.random() * arr.length);
                showNotification(arr[n]);
            }
        });
    }

    setTimeout(fetchAndShow, 1200);
    setInterval(fetchAndShow, 10000);

});
