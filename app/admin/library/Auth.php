<?php
/**
 * User: zsj
 * Date: 2020/7/14
 * Time: 13:29
 */

namespace app\admin\library;


use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;

/**
 * Class Auth
 * @package app\admin\library
 */
class Auth extends \think\wenhainan\Auth
{

    protected $adminId = null;
    protected $requestUri;
    // protected $id;
    protected $logined = false; //登录状态

    public function __get($name)
    {
        return Session::get('admin');
    }

    /**
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


    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return bool
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }

        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr)) {
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

    public function getGroups($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getGroups($uid);
    }

    /**
     * 检查权限
     * @param $name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param $uid  int           认证用户的id
     * @param int $type 认证类型
     * @param string $mode 执行check的模式
     * @param string $relation 如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * return bool               通过验证返回true;失败返回false
     */
    public function check($name, $uid = null, $type = 1, $mode = 'url', $relation = 'or')
    {
        $uid = is_null($uid) ? $this->id : $uid;
        if (!$this->config['auth_on']) {
            return true;
        }
        // 获取用户需要验证的所有有效规则列表
        $authList = $this->getAuthList($uid, $type);
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }
        $list = []; //保存验证通过的规则名
        if ('url' == $mode) {
            $REQUEST = unserialize(strtolower(serialize(Request::param())));
        }
        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ('url' == $mode && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else {
                if (in_array($auth, $name)) {
                    $list[] = $auth;
                }
            }
        }
        if ('or' == $relation && !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ('and' == $relation && empty($diff)) {
            return true;
        }
        return false;
    }

    /**
     * 检测是否登录
     *
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
        //判断是否同一时间同一账号只能在一个地方登录
        if (Config::get('fastadmin.login_unique')) {
            $my = Admin::get($admin['id']);
            if (!$my || $my['token'] != $admin['token']) {
                $this->logout();
                return false;
            }
        }
        //判断管理员IP是否变动
        if (Config::get('fastadmin.loginip_check')) {
            if (!isset($admin['loginip']) || $admin['loginip'] != request()->ip()) {
                $this->logout();
                return false;
            }
        }
        $this->logined = true;
        return true;
    }
}