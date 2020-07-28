<?php
/**
 * User: zsj
 * Date: 2020/7/28
 * Time: 15:17
 */

namespace app\admin\controller;


use think\facade\View;

class Dashboard extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = [];

    public function index()
    {
        dump(1111);
        exit();
        return View::fetch();
    }
}