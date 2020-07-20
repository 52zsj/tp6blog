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
use app\common\libray\Tree;
use app\common\model\AuthRule;
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

    protected $model =null;

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
        //获取所有权限菜单
        $menuList = AuthRule::where('status', '=', '1')->where('is_menu', 1)->select()->toArray();
        foreach ($menuList as $k => &$v) {
            $v['url']       = '/' . $v['name'];
            $v['route_url'] = (string)url($v['route_name']);
        }
        Tree::instance()->init($menuList);
        $tpl  = '<li><a href="javascript:;" onclick="xadmin.add_tab(\'@title\',\'@url\')"><i class="@icon" lay-tips="@title"></i><cite>@title</cite></i></a>@childlist</li>';
        $menu = Tree::instance()->getTreeUl(0, $tpl, '', '', 'ul', 'class="sub-menu"');


        //统一渲染
        $config = ['action' => $this->actionName];
        $option = [
            'auth'       => $this->auth,
            'admin'      => Session::get('admin'),
            'config'     => $config,
            'menu'       => $menu,
            'controller' => $this->controllerName,
            'action'     => $this->actionName,
            'app'        => $this->appName,
            'path'        => $this->path,
        ];
        View::assign($option);
    }

}