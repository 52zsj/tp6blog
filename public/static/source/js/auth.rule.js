import '../css/backend.css'
import Moment from 'moment'


var Controller = {
    index: function () {
        console.log('auth.rule index方法被执行');
        // var Table = {
        //     // Bootstrap-table 基础配置
        //     defaults: {
        //         url: '',
        //         sidePagination: 'server',
        //         method: 'get', //请求方法
        //         toolbar: ".toolbar", //工具栏
        //         search: true, //是否启用快速搜索
        //         cache: false,
        //         commonSearch: true, //是否启用通用搜索
        //         searchFormVisible: false, //是否始终显示搜索表单
        //         titleForm: '', //为空则不显示标题，不定义默认显示：普通搜索
        //         idTable: 'commonTable',
        //         showExport: true,
        //         exportDataType: "all",
        //         exportTypes: ['json', 'xml', 'csv', 'txt', 'doc', 'excel'],
        //         exportOptions: {
        //             fileName: 'export_' + Moment().format("YYYY-MM-DD"),
        //             ignoreColumn: [0, 'operate'] //默认不导出第一列(checkbox)与操作(operate)列
        //         },
        //         pageSize: 10,
        //         pageList: [10, 25, 50, 'All'],
        //         pagination: true,
        //         clickToSelect: true, //是否启用点击选中
        //         dblClickToEdit: true, //是否启用双击编辑
        //         singleSelect: false, //是否启用单选
        //         showRefresh: false,
        //         showJumpto: true,
        //         locale: 'zh-CN',
        //         showToggle: true,
        //         showColumns: true,
        //         pk: 'id',
        //         sortName: 'id',
        //         sortOrder: 'desc',
        //         paginationFirstText: '第一页',
        //         paginationPreText: '上一页',
        //         paginationNextText: '下一页',
        //         paginationLastText: '最后一页',
        //         cardView: false, //卡片视图
        //         checkOnInit: true, //是否在初始化时判断
        //         escape: true, //是否对内容进行转义
        //         extend: {
        //             index_url: '',
        //             add_url: '',
        //             edit_url: '',
        //             del_url: '',
        //             import_url: '',
        //             multi_url: '',
        //             dragsort_url: '',//ajax/weigh
        //         }
        //     },
        //
        // };
        $("#table").bootstrapTable({
            url: '/auth/rule/index',
            sidePagination: 'server',
            method: 'get', //请求方法
            toolbar: ".toolbar", //工具栏
            search: true, //是否启用快速搜索
            cache: false,
            commonSearch: true, //是否启用通用搜索
            searchFormVisible: false, //是否始终显示搜索表单
            titleForm: '', //为空则不显示标题，不定义默认显示：普通搜索
            idTable: 'commonTable',
            exportDataType: "all",
            exportTypes: ['json', 'xml', 'csv', 'txt', 'doc', 'excel'],
            exportOptions: {
                fileName: 'export_' + Moment().format("YYYY-MM-DD"),
                ignoreColumn: [0, 'operate'] //默认不导出第一列(checkbox)与操作(operate)列
            },
            pageSize: 10,
            pageList: [10, 25, 50, 'All'],
            pagination: true,
            clickToSelect: true, //是否启用点击选中
            dblClickToEdit: true, //是否启用双击编辑
            singleSelect: false, //是否启用单选
            showRefresh: false,
            showJumpto: true,
            locale: 'zh-CN',
            showToggle: true,
            showColumns: true,
            pk: 'id',
            sortName: 'id',
            sortOrder: 'desc',
            paginationFirstText: '第一页',
            paginationPreText: '上一页',
            paginationNextText: '下一页',
            paginationLastText: '最后一页',
            cardView: false, //卡片视图
            checkOnInit: true, //是否在初始化时判断
            escape: true, //是否对内容进行转义
            columns: [{
                field: 'id',
                title: 'ID'
            }, {
                field: 'name',
                title: 'url路径'
            }, {
                field: 'operator',
                title: '操作',
                formatter:function () {
                    return '<button>我是你爹</button>'
                }
            }],
        })
    },
    add: function () {
        console.log('add方法被执行');
    }
};
window.Controller = Controller;