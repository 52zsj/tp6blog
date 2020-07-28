define(['jquery', 'bootstrap','form'], function ($, undefined,Form) {
    var Controller = {
        index: function () {
            console.log('index方法被执行');
        },
        login: function () {
            var form = $('form[role="form"]');
            Form.api.bindevent(form, function (data, ret) {
                $(".captcha-img").trigger('click');
                $("button[type='submit']").addClass('disabled').attr('disabled', true);
                location.href = ret.url;
            }, function (data, ret) {
                $(".captcha-img").trigger('click');
                $("button[type='submit']").removeClass('disabled').attr('disabled', false);
            });

        }
    };
    return Controller;
});
