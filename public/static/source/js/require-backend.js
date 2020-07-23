require.config({
    // urlArgs: "v=" + requirejs.s.contexts._.config.config.version,
    baseUrl: '/static/source/js/', //资源基础路径
    packages: [{
        name: 'moment',
        location: '../libs/moment',
        main: 'moment'
    }],
    //在打包压缩时将会把include中的模块合并到主文件中
    include: ['css','layer'],

    map: {
        '*': {
            'css': '../libs/require-css/css.min'
        }
    },
    paths: {
        'adminlte': 'adminlte.min',
        'validator': 'require-validator',
        'aojie': './aojie/aojie',
        'form': './aojie/form',
        'jquery': '../libs/jquery/dist/jquery.min',
        'bootstrap': '../libs/bootstrap/dist/js/bootstrap.bundle.min',
        'toastr': '../libs/toastr/toastr',
        'validator-core': '../libs/nice-validator/dist/jquery.validator',
        'validator-lang': '../libs/nice-validator/dist/local/zh-CN',
        'axios': '../libs/axios/dist/axios.min',
        'layer':'../libs/layer/dist/layer'
    },
    shim: {
        'adminlte': {
            deps: ['bootstrap'],
            exports: '$.AdminLTE'
        },
        'axios': {
            exports: 'axios',
        }
    },
    waitSeconds: 30,
    charset: 'utf-8' // 文件编码
});

require(['jquery', 'bootstrap'], function ($, undefined) {
    //初始配置
    var Config = requirejs.s.contexts._.config.config;
    //将Config渲染到全局
    window.Config = Config;
    // 配置语言包的路径
    // var paths = {};
    // // 避免目录冲突
    // paths['backend/'] = 'backend/';
    // require.config({paths: paths});
    // 初始化
    $(function () {
        require(['aojie'], function (Aojie) {
            //加载相应模块
            //加载相应模块
            if (Config.js_name) {
                require([Config.js_name], function (Controller) {
                    if (Controller.hasOwnProperty(Config.action_name)) {
                        Controller[Config.action_name]();
                    } else {
                        if (Controller.hasOwnProperty("_empty")) {
                            Controller._empty();
                        }
                    }
                }, function (e) {
                    console.error(e);
                    // 这里可捕获模块加载的错误
                });
            }
        });
    });
});