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
        $breadcrumb = $this->auth->getBreadCrumb($this->path);
        array_pop($breadcrumb);
        $menu = $this->getMenu();
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
            'breadcrumb' => $breadcrumb,
            'menu' => $menu,
        ];
        View::assign($option);
    }

    protected function getMenu()
    {
        $userRule  = $this->auth->getRuleList();
        $pinyin    = new \Overtrue\Pinyin\Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        $badgeList = $selected = $referer = [];
        $fixedPage = 'dashboard';
        //所有的菜单
        $ruleList      = AuthRule::where('status', '=', '1')
            ->where('is_menu', 1)
            // ->cache('__menu__')
            ->select()
            ->toArray();
        $indexRuleList = AuthRule::where('status', '1')
            ->where('is_menu', 0)
            ->where('name', 'like', '%/index')
            ->column('pid', 'name');

        //找到非0 的父级ID  可能存在多级的情况
        $pidArr = array_filter(array_unique(array_column($ruleList, 'pid')));
        //筛查用户是否有主页权限
        foreach ($ruleList as $k => &$v) {
            //不是该用户的权限 剃掉
            if (!in_array($v['name'], $userRule)) {
                unset($ruleList[$k]);
                continue;
            }
            $indexRuleName = $v['name'] . '/index';
            if (isset($indexRuleList[$indexRuleName]) && !in_array($indexRuleName, $userRule)) {
                unset($ruleList[$k]);
                continue;
            }
            $v['icon']   = $v['icon'] . ' fa-fw';
            $v['url']    = '/' .$indexRuleName;
            $v['py']     = $pinyin->abbr($v['title'], '');
            $v['pinyin'] = $pinyin->permalink($v['title'], '');
            $selected    = $v['name'] == $fixedPage ? $v : $selected;
        }
        // dump($selected);exit();
        unset($v);
        $lastArr = array_diff($pidArr, array_filter(array_unique(array_column($ruleList, 'pid'))));
        foreach ($ruleList as $index => $item) {
            if (in_array($item['id'], $lastArr)) {
                unset($ruleList[$index]);
            }
        }

        //
        // $selectId = $selected ? $selected['id'] : 0;

        $menu = $nav = '';
        // // 构造菜单数据
        Tree::instance()->init($ruleList);
//        menu-open 'active'
        $template = ' <li class="nav-item @liclass">
                        <a href="@url" class="nav-link @class" py="@py" pinyin="@pinyin">
                            <i class="nav-icon fa @icon"></i>
                            <p>
                                @title
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        @childlist
            </li>';
        // //   <li class="nav-item has-treeview"><a href="@url" url="@url"  py="@py" pinyin="@pinyin" class="nav-link"><i class="nav-icon @icon"></i><p>@title<i class="right fa fa-angle-left"></i></p></a>@childlist<li>'
        $menu = Tree::instance()->getTreeMenu(
            0,
            $template,
            '',
            '',
            'ul',
            'class="nav nav-treeview"'
        );
        return $menu;
    }

}