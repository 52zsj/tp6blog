<?php

use think\facade\Route;


//首页
Route::group('index', function () {
    Route::rule('index', 'index')->name('index_index');
    Route::rule('login', 'login')->name('index_login');
    Route::rule('profile', 'profile')->name('index_profile');
    Route::rule('logout', 'logout')->name('index_logout');
})->prefix('admin/index/');

Route::group('dashboard', function () {
    Route::rule('index', 'index')->name('dashboard_index');
});

Route::group('auth/rule', function () {
    Route::rule('index', 'index')->name('auth_rule_index');
    Route::rule('add', 'add')->name('auth_rule_add');
    Route::rule('edit', 'edit')->name('auth_rule_edit');
    Route::rule('del', 'del')->name('auth_rule_del');
})->prefix('admin/auth.rule/');

//管理员
Route::group('auth/admin', function () {
    Route::rule('index', 'index')->name('auth_admin_index');
    Route::rule('add', 'add')->name('auth_admin_add');
    Route::rule('edit', 'edit')->name('auth_admin_edit');
})->prefix('admin/auth.admin/');


Route::get('captcha/[:s]', '\\think\\captcha\\CaptchaController@index');

