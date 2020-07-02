<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 13:53
 */
declare(strict_types=1);

namespace app\index\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        dump(is_numeric($this->add(1)));
        dump(is_string($this->add(2)));

    }

    public function hello(string $name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
