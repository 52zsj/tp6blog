import axios from 'axios';
import 'toastr/toastr.less'
import Toastr from 'toastr';

var Aojie = {
    event: {
        onAxiosSuccess: function (ret, onAxiosSuccess) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var msg = typeof ret.msg !== 'undefined' ? ret.msg : '';
            if (typeof onAxiosSuccess === 'function') {
                var result = onAxiosSuccess.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            if (msg != '') {
                Toastr.success(msg);
            }

        },
        onAxiosError: function (ret, onAxiosError) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var msg = typeof ret.msg !== 'undefined' ? ret.msg : '操作失败';
            if (typeof onAjaxError === 'function') {
                var result = onAjaxError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            Toastr.error(msg);
        },
        onAxiosResponse: function (response) {
            console.log(response);
            try {
                var ret = typeof response === 'object' ? response : JSON.parse(response);
                if (!ret.hasOwnProperty('code')) {
                    $.extend(ret, {code: -2, msg: response, data: null});
                }
            } catch (e) {
                var ret = {code: -1, msg: e.message, data: null};
            }
            return ret;
        }
    },
    api: {
        axios: function (options,success,error) {
            options = typeof options === 'string' ? {url: options} : options;
            axios(options).then(function (response) {
                $('.form-group', form).removeClass('has-feedback has-success has-error');
                var data = response.data;
                if (data && typeof data === 'object') {

                }
                if (typeof success === 'function') {
                    if (false === success.call(form, data, ret)) {
                        return false;
                    }
                }
            }).catch(function (error) {
                console.log(error);
            });
        }
    }
};
export default Aojie;