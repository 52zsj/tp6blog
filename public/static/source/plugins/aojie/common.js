import '../layui/layui'

layui.config({
    dir: '/static/source/plugins/layui/'
});
layui.use('form', function () {
    var form = layui.form;
    form.render();
});
