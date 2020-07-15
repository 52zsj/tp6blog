<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 16:08
 */

namespace app\admin\controller\auth;


use app\admin\controller\Base;
use think\facade\View;

class Admin extends Base
{
    public function index()
    {
        return View::fetch();
    }
}