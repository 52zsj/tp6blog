<?php
/**
 * User: zsj
 * Date: 2020/7/14
 * Time: 13:29
 */

namespace app\admin\library;


use think\facade\Config;
use think\facade\Db;

class Auth extends \think\wenhainan\Auth
{

    protected $adminId = null;

    /**
     * 类架构函数
     * Auth constructor.
     */
    public function __construct()
    {
        //可设置配置项 auth, 此配置项为数组。
        if ($auth = Config::get('auth')) {
            $this->config = array_merge($this->config, $auth);
        }
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库
     */
    function getUserInfo($uid)
    {
        static $userinfo = [];
        $user = Db::name($this->config['auth_user']);
        // 获取用户表主键
        $_pk = is_string($user->getPk()) ? $user->getPk() : 'uid';
        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = $user->where($_pk, $uid)->find();
        }
        return $userinfo[$uid];
    }

}