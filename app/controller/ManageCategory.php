<?php

namespace app\controller;

use app\component\AbstractAdmin;
use app\model\Category;

class ManageCategory extends AbstractAdmin
{
    public function index($moe)
    {
        $moe->set('data', Category::instance()->all(false));
        $moe->send('admin/category');
    }

    public function data($moe)
    {
        $self = $moe->siteUrl('manage_category');
        $moe->send(array('data'=>Category::instance()
            ->select(<<<SEL
concat('<a href="{$self}/input?id=', {table}.id_category, '" class="text-green" title="Edit"><i class="fa fa-edit"></i></a>',
'<a href="{$self}/delete?id=', {table}.id_category, '" class="text-red" title="Delete" data-bootbox="confirm"><i class="fa fa-remove"></i></a>') as actions,
{table}.id_category,
{table}.category
SEL
)->fetchMode('num')->all()));
    }

    public function input($moe)
    {
        $category = Category::instance()
            ->findByPK($moe->get('GET.id'))->get(1);
        if ($moe->isPost()) {
            $category->copyfrom('POST');
            !$category->save() || $moe->reroute('@manage_category');
            $moe->set('message', 'Data gagal disimpan!');
            !$category->hasMessage() || $moe->concat('message', '<br>'.
                $category->asList('messages'));
        } else
            $category->copyto('POST');
        $moe->send('admin/category_input');
    }

    public function delete($moe)
    {
        $moe->send(array('result'=>Category::instance()
            ->findByPK($moe->get('GET.id'))
            ->get(1)
            ->delete()));
    }

    public function __construct($moe)
    {
        parent::__construct($moe);
        $this->needAdmin();
    }
}
