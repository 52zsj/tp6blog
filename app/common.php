<?php
// 应用公共文件
/*if (!function_exists('assign_config')) {
    function assign_config($name, $value = ''):void
    {
        $view         = \think\facade\View::instance();
        $view->config = array_merge($view->config ?: [], is_array($name) ? $name : [$name => $value]);
    }
}*/
if (!function_exists('format_time')) {
    /**
     * @param string $format 格式化内容
     * @param null $time 数值型数据
     * @return false|string
     */
    function format_time($format = 'Y-m-d H:i:s', $time = null)
    {
        $time = $time ?: time();
        $result = is_numeric($time) ? date($format, $time) : date($format, strtotime($time));
        return $result;

    }
}
if (!function_exists('password_make_or_verify')) {
    /**
     * @param string $password 需要加密或校验的密码
     * @param string $checkPassword 需要检测的密码
     * @param int $algo 一个用来在散列密码时指示算法的密码算法常量
     * @return bool|string 存在校验密码时返回bool 否则返回 string
     */
    function password_make_or_verify(string $password, string $checkPassword = '', int $algo = PASSWORD_DEFAULT, array $option = [])
    {
        if ($checkPassword !== '') {
            return password_verify($password, $checkPassword);
        } else {
            return password_hash($password, $algo, $option);
        }
    }
}

if (!function_exists('chat_center')) {
    /**
     * @param int|float $fontsize 需要局中的文字的字体大小
     * @param string $font 需要局中的文字的字体URL
     * @param string $content 需要局中的文字
     * @param int|float $maxLength 大背景的宽度|高度
     * @param int|float $type 类型 1 为宽度 2 为高度
     * @author zsj 2019-05-13
     * @return float
     */
    function chat_center($fontsize,$font,$content, $maxLength, $type = 1)
    {
        $array = imagettfbbox($fontsize, 0, $font, $content);
        if ($type == 1) {
            return ceil(($maxLength - $array[2] - $array[0]) / 2);
        } else {
            return ceil(($maxLength - $array[1] - $array[7]) / 2);
        }
    }

}
