function tab_refresh(tab_name) {
    $('.menu.secondary .item').tab('cache remove', tab_name);
    $('.menu.secondary .item').tab('change tab', tab_name);
}


$('body').on('click', ".div-refresh", function () {
    tab_refresh('manage_order');
});

$(document).on('click', ".div-vacate", function () {
    var self = this;

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
                    'id_card': $(self).data("id_card"),
                    'room_name': $(self).data('room_name'),
                    'order_time': $(self).data('order_time')
                },
                success: function(response) {
                    if (response.success) {
                        tab_refresh('manage_order');
                    }
                }});
        }
    }).modal('show');
});

/* 客房管理那个页面　*/

$(document).on('click', ".button.div-add_room_type", function () {
    $('.menu .item').tab();

    var $modal = $('.modal.div-add').eq(0);
    $modal.find('.menu .item').tab('change tab', 'first');
    modal_form_init($modal);
    $modal.modal({
        onDeny    : function() {
        },
        onApprove : function() {
        }
    }).modal('show');
});


$(document).on('click', ".button.div-add_room", function () {
    $('.menu .item').tab();


    var $modal = $('.modal.div-add').eq(0);

    $modal.find('.menu .item').tab('change tab', 'second');

    modal_form_init($modal);

    $modal.modal({
        onDeny    : function() {
        },
        onApprove : function() {
        }
    }).modal('show');
});

function modal_form_init($modal)
{
    $('.form.div-add_room_type').form({
    }).api({
        url: '<?= Path::url(array(), "api", "add_room_type") ?>',
        serializeForm: true,
        method: 'post',
        successTest: function(response) {
            // test whether a JSON response is valid
            return response.success || false;
        },

        onSuccess: function(response) {

            /* 不管用，无法刷新 不清楚原因 */
            $modal.modal({
                onHidden: function () {
                    // tab_refresh('manage_room');
                    // setTimeout(function () {
                    //     tab_refresh('manage_room');
                    // }, 3000);

                }
            }).modal('hide');



        },
        onFailure: function(response) {
            alert(JSON.stringify(response));
            alert('失败。。。');
        }
    });


    $('.form.div-add_room').form({
    }).api({
        url: '<?= Path::url(array(), "api", "add_room") ?>',
        serializeForm: true,
        method: 'post',
        successTest: function(response) {
            // test whether a JSON response is valid
            return response.success || false;
        },

        onSuccess: function(response) {
            $modal.modal('hide');
            tab_refresh('manage_room');
        },
        onFailure: function(response) {
            alert(JSON.stringify(response));
            alert('失败。。。');
        }
    });
}


$(document).on('click', ".div-del_room_type", function () {
    var self = this;

    /* 注：这段代码里modal可能弹出多次 */
    $('.small.warn.modal').eq(0).modal({
        closable  : false,
        onDeny    : function(){
        },
        onApprove : function() {
            $.ajax({
                url: "<?= Path::url(array(), 'api', 'del_room_type') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    'type_id': $(self).data("type_id")
                },
                success: function(response) {
                    if (response.success) {
                        tab_refresh('manage_room');
                    }
                }});
        }
    }).modal('show');
});


$(document).on('click', ".div-del_room", function () {
    var self = this;

    /* 注：这段代码里modal可能弹出多次 */
    $('.small.warn.modal').eq(0).modal({
        closable  : false,
        onDeny    : function(){
        },
        onApprove : function() {
            $.ajax({
                url: "<?= Path::url(array(), 'api', 'del_room') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    'room_name': $(self).data("room_name")
                },
                success: function(response) {
                    if (response.success) {
                        tab_refresh('manage_room');
                    }
                }});
        }
    }).modal('show');
});
