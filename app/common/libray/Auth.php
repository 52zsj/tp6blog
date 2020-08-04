<?php
/**
 * User: zsj
 * Date: 2020/7/17
 * Time: 11:09
 */

namespace app\common\libray;


use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;

class Auth
{
    /**
     * 实例
     * @var $instance
     */
    protected static $instance;
    /**
     * 当前请求实例
     * @var $request
     */
    protected $request;
    protected $breadcrumb = [];

    protected $rules = [];
    //默认配置
    protected $config = [
        'auth_on'           => 1, // 权限开关
        'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => 'auth_group', // 用户组数据不带前缀表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系不带前缀表名
        'auth_rule'         => 'auth_rule', // 权限规则不带前缀表名
        'auth_user'         => 'admin', // 用户信息表不带前缀表名,主键自增字段为id
    ];

    public function __construct()
    {
        //可设置配置项 auth, 此配置项为数组。
        if ($auth = Config::get('auth')) {
            $this->config = array_merge($this->config, $auth);
        }
        $this->request = Request::instance();
    }

    /**
     * 初始化
     * @param array $options
     * @return Auth
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
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
    public function check($name, $uid, $relation = 'or', $mode = 'url')
    {
        //判断权限开关
        if (!$this->config['auth_on']) {
            return true;
        }
        // 获取用户需要验证的所有有效规则列表
        $ruleList = $this->getRuleList($uid);
        if (in_array('*', $ruleList)) {
            return true;
        }
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
            $REQUEST = unserialize(strtolower(serialize($this->request->param())));
        }
        foreach ($ruleList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ('url' == $mode && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth      = preg_replace('/\?.*$/U', '', $auth);
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
     * 获得权限列表
     * @param integer $uid 用户id
     * @param integer $type
     * return array
     */
    public function getRuleList($uid)
    {
        static $_ruleList = []; //保存用户验证通过的权限列表
        if (isset($_ruleList[$uid])) {
            return $_ruleList[$uid];
        }
        if (2 == $this->config['auth_type'] && Session::has('_auth_list_' . $uid)) {
            return Session::get('_auth_list_' . $uid);
        }
        $ids = $this->getRuleIds($uid);
        if (empty($ids)) {
            $_ruleList[$uid] = [];
            return [];
        }
        // 筛选条件
        $where = [
            'status' => '1'
        ];
        if (!in_array('*', $ids)) {
            $where = function ($query) use ($ids) {
                $query->whereIn('id', $ids);
            };
        }
        //读取用户组所有权限规则
        $this->rules = Db::name($this->config['auth_rule'])->where($where)->field('id,pid,condition,icon,name,title,is_menu')->select();
        //循环规则，判断结果。
        $ruleList = []; //
        if (in_array('*', $ids)) {
            $ruleList[] = "*";
        }
        foreach ($this->rules as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $user    = $this->getUserInfo($uid); //获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command); //debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $ruleList[] = strtolower($rule['name']);
                }
            } else {
                //只要存在就记录
                $ruleList[] = strtolower($rule['name']);
            }
        }
        $_ruleList[$uid] = $ruleList;
        if (2 == $this->config['auth_type']) {
            //规则列表结果保存到session
            Session::set('_auth_list_' . $uid, $ruleList);
        }
        return array_unique($ruleList);
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param  $uid int     用户id
     * return array       用户所属的用户组 array(
     *     array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *     ...)
     */
    public function getGroups($uid)
    {
        static $groups = [];
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        // 转换表名
        $authGroupAccess = $this->config['auth_group_access'];
        $authGroup       = $this->config['auth_group'];
        // 执行查询
        $hasGroups    = Db::view($authGroupAccess, 'uid,group_id')
            ->view($authGroup, 'title,rules', "{$authGroupAccess}.group_id={$authGroup}.id", 'LEFT')
            ->where("{$authGroupAccess}.uid='{$uid}' and {$authGroup}.status='1'")
            ->select();
        $groups[$uid] = $hasGroups ?: [];
        return $groups[$uid];
    }

    /**
     * 获取用户所有节点数据
     * @param $uid
     */
    public function getRuleIds($uid)
    {
        //读取用户的节点数据
        $groups = $this->getGroups($uid);
        $ids    = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        return $ids;
    }

    /**
     * 获取用户信息
     * @param $uid
     * @return mixed
     */
    public function getUserInfo($uid)
    {
        static $userInfo = [];
        $user = Db::name($this->config['auth_user']);
        // 获取用户表主键
        $_pk = is_string($user->getPk()) ? $user->getPk() : 'uid';
        if (!isset($userInfo[$uid])) {
            $userInfo[$uid] = $user->where($_pk, $uid)->find();
        }
        return $userInfo[$uid];
    }
    /**
     * 获得面包屑导航
     * @param string $path
     * @return array
     */
    public function getBreadCrumb($path = '')
    {
        if ($this->breadcrumb || !$path) {
            return $this->breadcrumb;
        }
        $titleArr = [];
        $menuArr = [];
        $urlArr = explode('/', $path);
        foreach ($urlArr as $index => $item) {
            $pathArr[implode('/', array_slice($urlArr, 0, $index + 1))] = $index;
        }
        if (!$this->rules && $this->id) {
            $this->getRuleList();
        }
        foreach ($this->rules as $rule) {
            if (isset($pathArr[$rule['name']])) {
                $rule['title'] =$rule['title'];
                $rule['url'] = $rule['name'];
                $titleArr[$pathArr[$rule['name']]] = $rule['title'];
                $menuArr[$pathArr[$rule['name']]] = $rule;
            }

        }
        ksort($menuArr);
        $this->breadcrumb = $menuArr;
        return $this->breadcrumb;
    }

}