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
    protected $noNeedRight = ['login', 'logout'];


    public function index()
    {
        return View::fetch();
    }


    public function login()
    {
        // $str   = '8a6e65d0bbb541538e0338b029b2f0ad:13d0fa60d700403dad6e86a9d23c2895';
        // $array = str_split($str, 1);
        // $b=[];
        // foreach ($array as $k => $v) {
        //     $b[$k] = ord($v);
        // }
        // $b = array_map('ord', str_split($str, 1));
        // $s = join('', array_map('chr', $b));
        // echo base64_encode('[B@659e0bfd');
        // exit();

        if ($this->request->isPost()) {
            $params = $this->request->param();
            $this->success('操作成功','',$params);
        }
        return View::fetch();
    }

}
