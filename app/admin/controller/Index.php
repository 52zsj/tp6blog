<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 13:53
 */

namespace app\admin\controller;

use think\App;
use think\facade\Validate;
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
        $defaultCalback = url('index_index');
        $calback        = $this->request->get('callback', $defaultCalback);
        if($this->auth->isLogin()){
            $this->redirect($calback);
        }
        if ($this->request->isPost()) {
            $useraName = $this->request->param('user_name', '');
            $password  = $this->request->param('password', '');
            $captcha   = $this->request->param('captcha', '');
            $keepLogin = $this->request->post('keep_login');
            $rule      = [
                'user_name|用户名' => 'require|length:3,30',
                'password|密码'   => 'require|length:3,30',
                'captcha|验证码'   => 'require|captcha',
            ];
            $data      = [
                'user_name' => $useraName,
                'password'  => $password,
                'captcha'   => $captcha,
            ];
            $validate  = Validate::rule($rule);
            $result    = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), $calback);
            }
            $result = $this->auth->login($useraName, $password, $keepLogin ? 86400 : 0);
            if ($result === false) {
                $this->error($this->auth->getError());
            }
            $this->success('登陆成功', $calback, $result);
        }
        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->redirect($calback);
        }
        return View::fetch();
    }


    public function logout()
    {
        $this->auth->logout();
        $url = url('index_login');
        $this->redirect($url, 200);

    }

}
