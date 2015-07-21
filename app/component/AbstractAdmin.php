<?php

namespace app\component;

use moe\Instance;

abstract class AbstractAdmin
{
    public function __construct($moe)
    {
        $moe->set('menu', array(
            'manage_post'=>'Posts',
            )+(Auth::isAdmin()?array(
            'manage_category'=>'Categories',
            'manage_author'=>'Authors',
            ):array()));
        $moe->set('TEMPLATE', 'template/admin');
    }

    public function isPost()
    {
        return strtolower($_SERVER['REQUEST_METHOD'])=='post';
    }

    public function needLogin()
    {
        Auth::isLogged() || Instance::reroute('@admin_login');
    }

    public function needAdmin()
    {
        Auth::isAdmin() || (Auth::logout() && Instance::reroute('@admin_login'));
    }

    public function afterroute($moe)
    {
        $moe->set('rendertime', number_format(microtime(true)-$moe->get('TIME'), 5));
    }
}
