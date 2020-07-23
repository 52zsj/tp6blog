define(['jquery', 'bootstrap', 'toastr', 'axios','layer'], function ($, undefined, Toastr, axios,layer) {
    var Aojie = {
        config: {
            //toastr默认配置
            toastr: {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        },
        events: {
            onAxiosSuccess: function (ret, onAxiosSuccess) {
                var data = typeof ret.data !== 'undefined' ? ret.data : null;
                var msg = typeof ret.msg !== 'undefined' ? ret.msg : '';
                if (typeof onAxiosSuccess === 'function') {
                    var result = onAxiosSuccess.call(this, data, ret);
                    if (result === false)
                        return;
                }
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
                var index;
                console.log(typeof options.loading);return
                if (typeof options.loading != 'undefined') {
                    index = layer.load(options.loading || 0);
                }
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
            },
            toastr: Toastr,
            layer: layer
        }
    };
    //公共代码
    //配置Toastr的参数
    Toastr.options = Aojie.config.toastr;
    //将Layer暴露到全局中去
    window.layer = layer;
    //将Toastr暴露到全局中去
    window.Toastr = Toastr;
    return Aojie;
});