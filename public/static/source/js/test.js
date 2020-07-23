
({
    optimizeCss: "standard",
    optimize: "none",   //可使用uglify|closure|none
    preserveLicenseComments: false,
    removeCombined: false,
    baseUrl: "E:/phpstudy_pro/WWW/tp6blog/public/static/source/js/",    //JS文件所在的基础目录
    name: "require-backend.js",
    out: "E:/phpstudy_pro/WWW/tp6blog/public/static/source/js/require-backend.min.js",
    paths: {
        'adminlte': 'adminlte.min',
        'validator': 'require-validator',
        'jquery': '../libs/jquery/dist/jquery.min',
        'toastr': '../libs/toastr/toastr',
        'validator-core': '../libs/nice-validator/dist/jquery.validator',
        'validator-lang': '../libs/nice-validator/dist/local/zh-CN',
        'bootstrap':'../libs/bootstrap/dist/js/bootstrap.min'
    },
})