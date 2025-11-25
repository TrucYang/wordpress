// jQuery(document).ready(function($) {
//     $(document).on("click", ".sr-btn", function() {
//         let type = $(this).data("type");
//         let post = $(this).data("post");

//         $.post(sr_ajax.url, {
//             action: "sr_add_reaction",
//             type: type,
//             post_id: post,
//             nonce: sr_ajax.nonce
//         }, function(res) {
//             if (!res.success) return;

//             const counts = res.data.counts || {};

//             // Cập nhật toàn bộ counts theo server trả về
//             Object.keys(counts).forEach(function(r) {
//                 let selector = "button[data-type='" + r + "'][data-post='" + post + "'] .sr-count";
//                 $(selector).text(counts[r]);
//             });
//         });
//     });
// });

jQuery(document).ready(function($) {
    $(document).on("click", ".sr-btn", function() {
        let type = $(this).data("type");
        let post = $(this).data("post");

        $.post(sr_ajax.url, {
            action: "sr_add_reaction",
            type: type,
            post_id: post,
            nonce: sr_ajax.nonce
        }, function(res) {
            if (!res.success) return;

            const counts = res.data.counts || {};

            // Cập nhật toàn bộ counts theo server trả về
            Object.keys(counts).forEach(function(r) {
                let selector = "button[data-type='" + r + "'][data-post='" + post + "'] .sr-count";
                $(selector).text(counts[r]);
            });
        });
    });
});

