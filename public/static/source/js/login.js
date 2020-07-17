import '../css/backend.css'
import '../css/login.css'

import Form from '../plugins/aojie/form'

var form = $('form[role="form"]');
Form.api.bindevent(form, function (data, ret) {
    $(".captcha-img").trigger('click');
    $("button[type='submit']").addClass('disabled').attr('disabled', true);
    location.href = ret.url;
}, function (data, ret) {
    $(".captcha-img").trigger('click');
    $("button[type='submit']").removeClass('disabled').attr('disabled', false);
});


