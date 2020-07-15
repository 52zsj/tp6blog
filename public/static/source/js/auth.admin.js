import '../css/backend.css'
import '../css/theme.css'
import 'perfect-scrollbar/css/perfect-scrollbar.css'

import '../common/js/theme'
var Controller = {
    index: function () {
        console.log('auth.admin index方法被执行');
    },
    add: function () {
        console.log('add方法被执行');
    }
};
window.Controller = Controller;