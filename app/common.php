<?php
// 应用公共文件
if (!function_exists('assign_config')) {
    function assign_config($name, $value = '')
    {
        $view         = \think\facade\View::instance();
        $view->config = array_merge($view->config ?: [], is_array($name) ? $name : [$name => $value]);
    }
}