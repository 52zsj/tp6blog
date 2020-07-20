import '../css/backend.css'
import Table from '../plugins/aojie/table'

var Controller = {
    index: function () {
        var option = {
            url_list: {
                index_url: 'menu/index',
                add_url: 'menu/add',
                edit_url: 'menu/edit',
                del_url: 'menu/del',
            },
            is_tree: true,
            elem: '#table',
            url: '/auth/rule/index',//数据接口
            title: '后台菜单',
            toolbar: '#tool-bar',
            page: false,
            cols: [[ //表头
                {type: 'checkbox', width: '5%'},
                {field: 'id', title: 'ID'},
                {field: 'title', title: '内容'},
                {field: 'url', title: '路径'},
                {field: 'icon', title: '图标', templet: '#icon'},
                {field: 'create_time', title: '创建时间'},
                {field: 'update_time', title: '创建时间'},
                {field: 'right', title: '操作', toolbar: '#operate', fixed: 'right'},
            ]],

        };
        Table.api.bindevent(option);
    },
    add: function () {
        console.log('add方法被执行');
    }
};
window.Controller = Controller;