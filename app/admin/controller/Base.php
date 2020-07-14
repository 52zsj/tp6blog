<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 13:54
 */

namespace app\admin\controller;


use app\BaseController;
use app\common\libray\traits\Admin;
use think\facade\View;


class Base extends BaseController
{
    // protected $middleware = [Check::class];
    use Admin;

    public function initialize()
    {
        parent::initialize(); // TODO: 父类初始化
        //权限
        $this->path                 = strtolower(str_replace('.', '/', $this->controllerName) . '/' . $this->actionName);
        $this->request->checkPath   = $this->path;
        $this->request->noNeedLogin = $this->noNeedLogin;
        $this->request->noNeedRight = $this->noNeedRight;
        $config                     = ['a' => 1];
        $view                       = View::instance();
        View::assign('config', $config);
        $view->config = array_merge($view->config, ['a' => '我也不知道', 'b' => '1']);
        assign_config('cao','nimabi');

    }

}