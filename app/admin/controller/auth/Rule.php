<?php
/**
 * User: zsj
 * Date: 2020/7/17
 * Time: 9:22
 */

namespace app\admin\controller\auth;


use app\admin\controller\Base;
use think\App;
use think\facade\Db;
use think\facade\View;


class Rule extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = [];


    public function index()
    {
        if ($this->request->isAjax()) {
            $total  = Db::name('auth_rule')->count();
            $list   = Db::name('auth_rule')->select();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return View::fetch();
    }
}