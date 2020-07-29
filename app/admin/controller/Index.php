<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 13:53
 */

namespace app\admin\controller;

use app\common\model\AuthRule;
use think\App;
use think\facade\Session;
use think\facade\Validate;
use think\facade\View;


class Index extends Base
{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];


    public function index()
    {
        // //获取所有权限菜单
        $module     = $this->appName;
        $refererUrl = Session::get('referer');
        $userRule   = $this->auth->getRuleList();
        $pinyin     = new \Overtrue\Pinyin\Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        $badgeList  = $selected = $referer = [];
        $fixedPage  = 'dashboard';
        //所有的菜单
        $ruleList = AuthRule::where('status', '=', '1')
            ->where('is_menu', 1)
            ->cache('__menu__')
            ->select()
            ->toArray();

        $indexRuleList = AuthRule::where('status', '1')
            ->where('is_menu', 0)
            ->where('name', 'like', '%/index')
            ->column('name,pid');

        $pidArr = array_filter(array_unique(array_map(function ($item) {
            return $item['pid'];
        }, $ruleList)));

        //
        foreach ($ruleList as $k => &$v) {
            if (!in_array($v['name'], $userRule)) {
                unset($ruleList[$k]);
                continue;
            }
            $indexRuleName = $v['name'];
            if (isset($indexRuleList[$indexRuleName]) && !in_array($indexRuleName, $userRule)) {
                unset($ruleList[$k]);
                continue;
            }
            $v['icon']   = $v['icon'] . ' fa-fw';
            $v['url']    = '/' . $v['name'];
            $v['badge']  = isset($badgeList[$v['name']]) ? $badgeList[$v['name']] : '';
            $v['py']     = $pinyin->abbr($v['title'], '');
            $v['pinyin'] = $pinyin->permalink($v['title'], '');
            $selected    = $v['name'] == $fixedPage ? $v : $selected;
            $referer     = url($v['url']) == $refererUrl ? $v : $referer;
            dump($selected);
            dump($referer);exit();
        }

        $lastArr = array_diff($pidArr, array_filter(array_unique(array_map(function ($item) {
            return $item['pid'];
        }, $ruleList))));
        foreach ($ruleList as $index => $item) {
            if (in_array($item['id'], $lastArr)) {
                unset($ruleList[$index]);
            }
        }
        var_dump($selected);
        var_dump($referer);


        exit();
        // if ($selected == $referer) {
        //     $referer = [];
        // }
        // $selected && $selected['url'] = url($selected['url']);
        // $referer && $referer['url'] = url($referer['url']);
        //
        // $select_id = $selected ? $selected['id'] : 0;
        // $menu = $nav = '';
        // // 构造菜单数据
        // Tree::instance()->init($ruleList);
        // //   <li class="nav-item has-treeview"><a href="@url" url="@url"  py="@py" pinyin="@pinyin" class="nav-link"><i class="nav-icon @icon"></i><p>@title<i class="right fa fa-angle-left"></i></p></a>@childlist<li>'
        // $menu = Tree::instance()->getTreeMenu(
        //     0,
        //     '<li class="nav-item has-treeview"><a href="@url" url="@url"  py="@py" pinyin="@pinyin" class="nav-link"><i class="nav-icon @icon"></i><p>@title<i class="right fa fa-angle-left"></i></p></a>@childlist<li>',
        //     $select_id,
        //     '',
        //     'ul',
        //     'class="nav nav-treeview"'
        // );
        // View::assign('menu',$menu);
        return View::fetch();
    }


    public function login()
    {
        $defaultCalback = url('index_index');
        $calback        = $this->request->get('callback', $defaultCalback);
        if ($this->auth->isLogin()) {
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
