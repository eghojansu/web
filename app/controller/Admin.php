<?php

namespace app\controller;

use app\component\AbstractAdmin;
use app\component\Auth;

class Admin extends AbstractAdmin
{
    public function index($moe)
    {
        $this->needLogin();
        $moe->send('admin/index');
    }

    public function profile($moe)
    {
        $this->needLogin();
        if ($moe->isPost()) {
            !Auth::update($moe->get('POST')) ||
                $moe->reroute($moe->get('REALM'));
            $moe->set('message', 'Update gagal!');
        }
        $moe->set('data', array(
            'name'=>Auth::data('name'),
            'username'=>Auth::data('username'),
            ));

        $moe->send('admin/profile');
    }

    public function login($moe)
    {
        !Auth::isLogged() || $moe->reroute('@admin');
        if ($moe->isPost()) {
            !Auth::attempt($moe->get('POST.username'),
                    $moe->get('POST.password')) ||
                $moe->reroute('@admin');
            $moe->set('message', 'Login gagal!');
        }

        $moe->set('TEMPLATE', null);
        $moe->send('admin/login');
    }

    public function logout($moe)
    {
        Auth::logout();
        $moe->reroute('@admin');
    }
}
