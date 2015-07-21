<?php

namespace app\controller;

use app\component\AbstractAdmin;
use app\component\Auth;
use app\model\Category;
use app\model\Post;

class ManagePost extends AbstractAdmin
{
    public function index($moe)
    {
        $moe->send('admin/post');
    }

    public function data($moe)
    {
        $self = $moe->siteUrl('manage_post');
        $moe->send(array('data'=>Post::instance()
            ->useRelation('category')
            ->select(<<<SEL
concat('<a href="{$self}/input?id=', {table}.id_post, '" class="text-green" title="Edit"><i class="fa fa-edit"></i></a>',
'<a href="{$self}/delete?id=', {table}.id_post, '" class="text-red" title="Delete" data-bootbox="confirm"><i class="fa fa-remove"></i></a>') as actions,
{table}.title,
category.category,
{table}.hits,
date_format({table}.date_created, '%d-%m-%Y') as date_created
SEL
)->findById_author(Auth::data('id_author'))->fetchMode('num')->all()));
    }

    public function input($moe)
    {
        $post = Post::instance()
            ->findByPK($moe->get('GET.id'))
            ->findById_author(Auth::data('id_author'))->get(1);
        if ($moe->isPost()) {
            $post->copyfrom('POST');
            $post->id_author = Auth::data('id_author');
            !$post->save() || $moe->reroute('@manage_post');
            $moe->set('message', 'Data gagal disimpan!');
            !$post->hasMessage() || $moe->concat('message', '<br>'.
                $post->asList('messages'));
        } else
            $post->copyto('POST');
        $moe->set('categoryList', Category::instance()->optionList());
        $moe->send('admin/post_input');
    }

    public function delete($moe)
    {
        $moe->send(array('result'=>Post::instance()
            ->findByPK($moe->get('GET.id'))
            ->findById_author(Auth::data('id_author'))
            ->get(1)
            ->delete()));
    }

    public function __construct($moe)
    {
        parent::__construct($moe);
        $this->needAdmin();
    }
}
