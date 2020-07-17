<?php
/**
 * User: zsj
 * Date: 2020/7/14
 * Time: 13:29
 */

namespace app\admin\library;

use app\common\model\Admin;
use think\facade\Cookie;
use think\facade\Session;

/**
 * Class Auth
 * @package app\admin\library
 */
class Auth extends \app\common\libray\Auth
{

    protected $requestUri;
    protected $logined = false; //登录状态
    protected $error = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function __get($name)
    {
        return Session::get('admin.' . $name);
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return bool
     */
    public function match($arr = [])
    {
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }

        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($this->request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }
        // 没找到匹配
        return false;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param  $uid int     用户id
     * return array       用户所属的用户组 array(
     *     array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *     ...)
     */
    public function getGroups($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getGroups($uid);
    }

    public function check($name, $uid = '', $relation = 'or', $mode = 'url')
    {
        $uid = $uid ?: $this->id;
        return parent::check($name, $uid, $relation, $mode);
    }

    /**
     * 登陆
     * @param $userName
     * @param $password
     * @param int $keepTime
     * @return bool
     */
    public function login($userName, $password, $keepTime = 0)
    {
        $adminInfo = Admin::where(['user_name|mobile' => $userName])->find();
        if (!$adminInfo) {
            $this->setError('用户不存在!');
            return false;
        }
        if (!password_make_or_verify($password, $adminInfo['password'])) {
            $this->setError('密码错误');
            return false;
        }

        if ($adminInfo['status'] !== 1) {
            $this->setError('用户已被禁用');
            return false;
        }
        $adminInfo->login_time = format_time();
        $adminInfo->save();
        Session::set('admin', $adminInfo->toArray());
        $this->keeplogin($keepTime);
        return true;
    }

    /**
     * 检测是否登录
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->logined) {
            return true;
        }
        $admin = Session::get('admin');
        if (!$admin) {
            return false;
        }
        //判断是否同一时间同一账号只能在一个地方登录 参考fastadmin
//        if (Config::get('fastadmin.login_unique')) {
//            $my = Admin::get($admin['id']);
//            if (!$my || $my['token'] != $admin['token']) {
//                $this->logout();
//                return false;
//            }
//        }
//        //判断管理员IP是否变动
//        if (Config::get('fastadmin.loginip_check')) {
//            if (!isset($admin['loginip']) || $admin['loginip'] != request()->ip()) {
//                $this->logout();
//                return false;
//            }
//        }
        $this->logined = true;
        return true;
    }


    /**
     * 刷新保持登录的Cookie
     *
     * @param int $keeptime
     * @return  boolean
     */
    protected function keeplogin($keeptime = 0)
    {
        if ($keeptime) {
            $expiretime = time() + $keeptime;
            $key        = md5(md5($this->id) . md5($keeptime) . md5($expiretime));
            $data       = [$this->id, $keeptime, $expiretime, $key];
            Cookie::set('keeplogin', implode('|', $data), 86400 * 30);
            return true;
        }
        return false;
    }

    /**
     * 自动登录
     * @return boolean
     */
    public function autologin()
    {
        $keeplogin = Cookie::get('keeplogin');
        if (!$keeplogin) {
            return false;
        }
        list($id, $keeptime, $expiretime, $key) = explode('|', $keeplogin);
        if ($id && $keeptime && $expiretime && $key && $expiretime > time()) {
            $admin = Admin::find($id);
            if (!$admin) {
                return false;
            }
            //token有变更
            if ($key != md5(md5($id) . md5($keeptime) . md5($expiretime))) {
                return false;
            }
            Session::set("admin", $admin->toArray());
            //刷新自动登录的时效
            $this->keeplogin($keeptime);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $admin = Admin::find(intval($this->id));
        if ($admin) {
            $admin->token = '';
            $admin->save();
        }
        Session::delete("admin");
        Cookie::delete("keeplogin");
        return true;
    }

    /**
     * 设置错误信息
     *
     * @param string $error 错误信息
     * @return Auth
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}