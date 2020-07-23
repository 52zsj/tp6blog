<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 13:54
 */

namespace app\admin\controller;


use app\admin\library\Auth;
use app\BaseController;
use app\common\libray\traits\Admin;
use think\facade\Session;
use think\facade\View;


class Base extends BaseController
{
    // protected $middleware = [Check::class];
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 权限实例
     * @var null
     */
    protected $auth = null;
    /**
     * 当前模型
     * @var null
     */
    protected $model = null;
    /**
     * 默认配置文件
     * @var array
     */
    protected $config = [];

    use Admin;

    public function initialize()
    {
        parent::initialize(); // TODO: 父类初始化
        //权限
        $this->path                 = strtolower(str_replace('.', '/', $this->controllerName) . '/' . $this->actionName);
        $this->request->checkPath   = $this->path;
        $this->request->noNeedLogin = $this->noNeedLogin;
        $this->request->noNeedRight = $this->noNeedRight;
        $this->auth                 = Auth::instance();

        // 设置当前请求的URI
        $this->auth->setRequestUri($this->path);
        // 检测是否需要验证登录

        if (!$this->auth->match($this->noNeedLogin)) {
            //检测是否登录
            if (!$this->auth->isLogin()) {
                $url      = $this->request->param('callback/s', '');
                $url      = $url ? $url : $this->request->url();
                $loginUrl = url('index_login', ['callback' => $url]);
                $this->redirect($loginUrl);
            }

            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($this->path)) {
                    $this->error('你没有权限访问');
                }
            }
        }
        // //获取所有权限菜单
        // $menuList = AuthRule::where('status', '=', '1')->where('is_menu', 1)->select()->toArray();
        // foreach ($menuList as $k => &$v) {
        //     $v['url']       = '/' . $v['name'];
        //     $v['route_url'] = (string)url($v['route_name']);
        // }
        // Tree::instance()->init($menuList);
        // $tpl  = '<li><a href="javascript:;" onclick="xadmin.add_tab(\'@title\',\'@url\')"><i class="@icon" lay-tips="@title"></i><cite>@title</cite></i></a>@childlist</li>';
        // $menu = Tree::instance()->getTreeUl(0, $tpl, '', '', 'ul', 'class="sub-menu"');
        // View::assign('config', $this->config);

        //统一渲染
        $this->config = [
            'app_name'        => $this->appName,
            'controller_name' => $this->controllerName,
            'action_name'     => $this->actionName,
            'js_name'         => 'backend/' . strtolower(str_replace('.', '/', $this->controllerName)),
            'module_url'      => rtrim(url('/' . $this->appName, [], false), '/'),
            'version'         => '0.0.1',
        ];
        $option       = [
            'auth'       => $this->auth,
            'admin'      => Session::get('admin'),
            'config'     => $this->config,
            'controller' => $this->controllerName,
            'action'     => $this->actionName,
            'app'        => $this->appName,
            'path'       => $this->path,
        ];
        View::assign($option);
    }

}