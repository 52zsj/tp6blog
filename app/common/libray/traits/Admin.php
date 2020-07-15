<?php
/**
 * User: zsj
 * Date: 2020/7/2
 * Time: 14:08
 */

namespace app\common\libray\traits;


use think\facade\View;

Trait Admin
{
    public function index()
    {
        return View::fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit()
    {
        return View::fetch();
    }

    /**
     * 删除
     */
    public function del()
    {

    }

    /**
     * 回收站
     */
    public function recycleBin()
    {

    }

    /**
     * 真实删除
     */
    public function destroy()
    {

    }

    /**
     * 还原
     */
    public function restore()
    {

    }
}