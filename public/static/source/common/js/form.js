import 'nice-validator/dist/jquery.validator.css'
import axios from 'axios';
import 'nice-validator';
import 'nice-validator/dist/local/zh-CN';

var Form = {
    events: {
        validator: function (form, success, error, submit) {
            if (!form.is("form"))
                return;
            //绑定表单事件
            form.validator($.extend({
                validClass: 'has-success',
                invalidClass: 'has-error',
                bindClassTo: '.form-group',
                formClass: 'n-default n-bootstrap',
                msgClass: 'n-bottom',
                stopOnError: true,
                display: function (elem) {
                    var text = $(elem).closest('.form-group').find(".control-label").text().replace(/\:\：/, '');
                    if (text == '') {
                        var text = $(elem).attr('data-control-label');
                    }
                    return text;
                },
                dataFilter: function (data) {
                    if (data.code === 1) {
                        return data.msg ? {"ok": data.msg} : '';
                    } else {
                        return data.msg;
                    }
                },
                target: function (input) {
                    var target = $(input).data("target");
                    if (target && $(target).size() > 0) {
                        return $(target);
                    }
                    var $formitem = $(input).closest('.form-group'),
                        $msgbox = $formitem.find('span.msg-box');
                    if (!$msgbox.length) {
                        return [];
                    }
                    return $msgbox;
                },
                valid: function (ret) {
                    var that = this, submitBtn = $(".footer-button [type=submit]", form);
                    that.holdSubmit(true);
                    submitBtn.addClass("disabled");
                    //验证通过提交表单
                    var submitResult = Form.api.submit($(ret), function (data, ret) {
                        that.holdSubmit(false);
                        submitBtn.removeClass("disabled");
                        if (false === $(this).triggerHandler("success.form", [data, ret])) {
                            return false;
                        }
                        if (typeof success === 'function') {
                            if (false === success.call($(this), data, ret)) {
                                return false;
                            }
                        }
                        //提示及关闭当前窗口
                        var msg = ret.hasOwnProperty("msg") && ret.msg !== "" ? ret.msg : '操作完成';
                        parent.Toastr.success(msg);
                        parent.$(".btn-refresh").trigger("click");
                        var index = parent.Layer.getFrameIndex(window.name);
                        parent.Layer.close(index);
                        return false;
                    }, function (data, ret) {
                        that.holdSubmit(false);
                        if (false === $(this).triggerHandler("error.form", [data, ret])) {
                            return false;
                        }
                        submitBtn.removeClass("disabled");
                        if (typeof error === 'function') {
                            if (false === error.call($(this), data, ret)) {
                                return false;
                            }
                        }
                    }, submit);
                    //如果提交失败则释放锁定
                    if (!submitResult) {
                        that.holdSubmit(false);
                        submitBtn.removeClass("disabled");
                    }
                    return false;
                }
            }, form.data("validator-options") || {}));

            //移除提交按钮的disabled类
            $(".footer-button [type=submit]", form).removeClass("disabled");
        },
        bindevent: function (form) {

        },
    },
    api: {
        submit: function (form, success, error, submit) {
            if (form.length === 0) {
                Toastr.error("表单未初始化完成,无法提交");
                return false;
            }
            if (typeof submit === 'function') {
                if (false === submit.call(form, success, error)) {
                    return false;
                }
            }
            var type = form.attr("method") ? form.attr("method").toUpperCase() : 'GET';
            type = type && (type === 'GET' || type === 'POST') ? type : 'GET';
            var url = form.attr("action");
            url = url ? url : location.href;
            //修复当存在多选项元素时提交的BUG
            var params = {};
            var multipleList = $("[name$='[]']", form);
            if (multipleList.length > 0) {
                var postFields = form.serializeArray().map(function (obj) {
                    return $(obj).prop("name");
                });
                $.each(multipleList, function (i, j) {
                    if (postFields.indexOf($(this).prop("name")) < 0) {
                        params[$(this).prop("name")] = '';
                    }
                });
            }
            //调用Ajax请求方法
            axios({
                method: type,
                url: url,
                data: form.serialize() + (Object.keys(params).length > 0 ? '&' + $.param(params) : ''),
            }).then(function (response) {
                $('.form-group', form).removeClass('has-feedback has-success has-error');
                console.log(response);
            }).catch(function (error) {
                console.log(error);
            });
            // Fast.api.ajax({
            //     type: type,
            //     url: url,
            //     data: form.serialize() + (Object.keys(params).length > 0 ? '&' + $.param(params) : ''),
            //     dataType: 'json',
            //     complete: function (xhr) {
            //         var token = xhr.getResponseHeader('__token__');
            //         if (token) {
            //             $("input[name='__token__']").val(token);
            //         }
            //     }
            // }, function (data, ret) {
            //     $('.form-group', form).removeClass('has-feedback has-success has-error');
            //     if (data && typeof data === 'object') {
            //         //刷新客户端token
            //         if (typeof data.token !== 'undefined') {
            //             $("input[name='__token__']").val(data.token);
            //         }
            //         //调用客户端事件
            //         if (typeof data.callback !== 'undefined' && typeof data.callback === 'function') {
            //             data.callback.call(form, data);
            //         }
            //     }
            //     if (typeof success === 'function') {
            //         if (false === success.call(form, data, ret)) {
            //             return false;
            //         }
            //     }
            // }, function (data, ret) {
            //     if (data && typeof data === 'object' && typeof data.token !== 'undefined') {
            //         $("input[name='__token__']").val(data.token);
            //     }
            //     if (typeof error === 'function') {
            //         if (false === error.call(form, data, ret)) {
            //             return false;
            //         }
            //     }
            // });
            return true;
        },

        bindevent: function (form, success, error, submit) {
            form = typeof form === 'object' ? form : $(form);
            var events = Form.events;

            events.bindevent(form);

            events.validator(form, success, error, submit);

            /*  events.selectpicker(form);

              events.daterangepicker(form);

              events.selectpage(form);

              events.cxselect(form);

              events.citypicker(form);

              events.datetimepicker(form);

              events.plupload(form);

              events.faselect(form);

              events.fieldlist(form);

              events.slider(form);

              events.switcher(form);*/
        },
    }


};
export default Form;