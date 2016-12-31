function tab_refresh() {
    $('.menu .item').tab('ache remove', 'manage_order');
    $('.menu .item').tab('change tab', 'manage_order');
}


$('body').on('click', ".div-refresh", function () {
    tab_refresh();
});

$(document).on('click', ".div-vacate", function () {

    /* 注：这段代码里modal可能弹出多次 */
    $('.small.test.modal').eq(0).modal({
        closable  : false,
        onDeny    : function(){
        },
        onApprove : function() {
            $.ajax({
                url: "<?= Path::url(array(), 'api', 'vacate_room') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    'id_card': $('.div-vacate').data("id_card"),
                    'room_name': $('.div-vacate').data('room_name'),
                    'order_time': $('.div-vacate').data('order_time')
                },
                success: function(response) {
                    if (response.success) {
                        tab_refresh();
                    }
                }});
        }
    }).modal('show');
});
