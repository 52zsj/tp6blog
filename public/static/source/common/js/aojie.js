import axios from 'axios';
import 'toastr/toastr.less'
import Toastr from 'toastr';

var Aojie = {
    events: {
        onAxiosSuccess: function (ret, onAxiosSuccess) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var msg = typeof ret.msg !== 'undefined' ? ret.msg : '';
            if (typeof onAxiosSuccess === 'function') {
                var result = onAxiosSuccess.call(this, data, ret);
                if (result === false)
                    return;
            }
            console.log(onAxiosSuccess);
            console.log(ret);
            if (msg != '') {
                Toastr.success(msg);
            }

        },
        onAxiosError: function (ret, onAxiosError) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var msg = typeof ret.msg !== 'undefined' && ret.msg != '' ? ret.msg : '未定义错误';
            if (typeof onAxiosError === 'function') {
                var result = onAxiosError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            Toastr.error(msg);
        },
        onAxiosResponse: function (response) {
            try {
                response = typeof response === 'object' ? response : JSON.parse(response);
                if (!response.hasOwnProperty('data')) {
                    var ret = {};
                    $.extend(ret, {code: -2, msg: response, data: null});
                } else {
                    var ret = response.data;
                }
            } catch (e) {
                var ret = {code: -1, msg: e.message, data: null};
            }
            return ret;
        }
    },
    api: {
        axios: function (options, success, error) {
            options = typeof options === 'string' ? {url: options} : options;
            axios(options).then(function (response) {
                var ret = Aojie.events.onAxiosResponse(response);
                if (ret.code === 1) {
                    Aojie.events.onAxiosSuccess(ret, success)
                } else {
                    Aojie.events.onAxiosError(ret, error);
                }
            }).catch(function (response) {
                var ret = Aojie.events.onAxiosResponse(response);
                Aojie.events.onAxiosError(ret, error);
            });
        }
    }
};
export default Aojie;