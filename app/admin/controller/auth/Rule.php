<?php
/**
 * User: zsj
 * Date: 2020/7/17
 * Time: 9:22
 */

namespace app\admin\controller\auth;


use app\admin\controller\Base;
use app\common\model\AuthRule;
use think\App;
use think\facade\Db;
use think\facade\View;


class Rule extends Base
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new AuthRule();
    }

    protected $noNeedLogin = [];
    protected $noNeedRight = [];

}