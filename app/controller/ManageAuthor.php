<?php

namespace app\controller;

use app\component\AbstractAdmin;
use app\component\Auth;
use app\model\Author;

class ManageAuthor extends AbstractAdmin
{
    public function index($moe)
    {
        $moe->send('admin/author');
    }

    public function data($moe)
    {
        $self = $moe->siteUrl('manage_author');
        $moe->send(array('data'=>Author::instance()
            ->select(<<<SEL
concat('<a href="{$self}/input?id=', {table}.id_author, '" class="text-green" title="Edit"><i class="fa fa-edit"></i></a>',
'<a href="{$self}/delete?id=', {table}.id_author, '" class="text-red" title="Delete" data-bootbox="confirm"><i class="fa fa-remove"></i></a>') as actions,
{table}.id_author,
{table}.name,
{table}.username,
if({table}.is_admin, 'Yes', 'No') as is_admin
SEL
)->findByNotId_author(Auth::data('id_author'))->fetchMode('num')->all()));
    }

    public function input($moe)
    {
        $author = Author::instance()
            ->findByPK($moe->get('GET.id'))
            ->findByNotId_author(Auth::data('id_author'))->get(1);
        if ($moe->isPost()) {
            $author->copyfrom('POST');
            $moe->get('POST.password') || $author->password = null;
            !$author->save() || $moe->reroute('@manage_author');
            $moe->set('message', 'Data gagal disimpan!');
            !$author->hasMessage() || $moe->concat('message', '<br>'.
                $author->asList('messages'));
        } else
            $author->copyto('POST');
        $moe->send('admin/author_input');
    }

    public function delete($moe)
    {
        $moe->send(array('result'=>Author::instance()
            ->findByPK($moe->get('GET.id'))
            ->findByNotId_author(Auth::data('id_author'))
            ->get(1)
            ->delete()));
    }

    public function __construct($moe)
    {
        parent::__construct($moe);
        $this->needAdmin();
    }
}
