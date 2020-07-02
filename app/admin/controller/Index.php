<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 13:53
 */

namespace app\admin\controller;

use think\App;
use think\facade\View;

class Index extends Base
{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];


    public function index()
    {
        return View::fetch();
    }


    public function login()
    {

        return View::fetch();
    }
}
