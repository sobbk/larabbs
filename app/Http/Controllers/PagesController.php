<?php

namespace App\Http\Controllers;

class PagesController extends Controller
{
    protected function root()
    {
        return view('pages.root');
    }


    public function permissionDenied()
    {
        // 如果当前用户有权限访问后台，直接跳转访问
        if (config('administrator.permission')()) {
            return redirect(url(config('administrator.uri')), 302);
        }

        // 否则使用视图
        return view('pages.permission_denied');
    }
}
