<?php

namespace app\model;

use moe\AbstractModel;
use moe\DB;

class Author extends AbstractModel
{
    protected function schema()
    {
        return array(
            'id_author'=>'ID Author',
            'name'=>array(
                'Name',
                array(
                    'trim', 'required', 'alpha'
                    ),
                ),
            'username'=>array(
                'Username',
                array(
                    'trim', 'required', 'alpha', 'min_length'=>3, 'uniqueUsername'
                    ),
                ),
            'password'=>array(
                'Password',
                array(
                    'required', 'moe\\instance::hash',
                    ),
                ),
            'is_admin'=>'Is Admin',
            );
    }

    public function primaryKey()
    {
        return 'id_author';
    }

    public function profile($username)
    {
        return $this->select('name,username')->findByUsername($username)->get(1)->cast();
    }
}
