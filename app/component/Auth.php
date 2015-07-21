<?php

namespace app\component;

use app\model\Author;
use moe\Instance;

class Auth
{
    public static function data($what = 'all')
    {
        return Instance::get('SESSION.data'.($what=='all'?'':'.'.$what));
    }

    public static function attempt($username, $password)
    {
        $author = Author::instance()
            ->findByUsernamePassword($username, Instance::hash($password))
            ->get(1);
        ($dry = $author->dry()) || self::login($author->cast(), $author->is_admin);
        return !$dry;
    }

    public static function update($data)
    {
        $author = Author::instance()->findByPK(self::data('id_author'))->get(1);
        $author->copyfrom('POST', function($data){
            return $data;
        });
        $data['password'] || $author->password = null;
        $saved = $author->save();
        !$saved || self::login($author->cast(), $author->is_admin);
        return $saved;
    }

    public static function isLogged()
    {
        return (bool) Instance::get('SESSION.login');
    }

    public static function isAdmin()
    {
        return (bool) Instance::get('SESSION.admin');
    }

    public static function login($data, $admin)
    {
        Instance::mset(array(
            'SESSION.login'=>true,
            'SESSION.admin'=>$admin,
            'SESSION.data'=>$data,
            ));
        return true;
    }

    public static function logout()
    {
        Instance::clear('SESSION');
        return true;
    }
}
