<?php

use think\facade\Route;

Route::group('auth/test', function () {
    Route::rule('/', 'admin/auth.test/index')->name('auth_test_index');
    Route::rule('add', 'admin/auth.test/add')->name('auth_test_add');
    Route::rule('edit', 'admin/auth.test/edit')->name('auth_test_edit');
});

Route::group('index', function () {
    Route::rule('/', 'admin/index/index')->name('index_index');
    Route::rule('login', 'admin/index/login')->name('index_login');
});


