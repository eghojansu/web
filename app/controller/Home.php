<?php

namespace app\controller;

use app\component\Helper;
use app\model\Author;
use app\model\Post;
use moe\Instance;

class Home
{
    public function index($moe)
    {
        $page = $moe->get('PARAMS.number');
        $page>0 || $page = 1;
        $posts = Post::instance()->latest($page);
        $this->pageTitle($page>1?'Page '.$page.' - ':'Home', false);
        $moe->set('posts', $posts['data']);
        $moe->set('pages_url', $moe->siteUrl('page', 'number={page}'));
        $moe->set('pages', array(
            'prev'=>$page>1?$page-1:0,
            'next'=>$page<$posts['totalPage']?$page+1:0,
            ));
        $moe->send('home/news_list');
    }

    public function post($moe, $params)
    {
        $moe->set('post', Post::instance()->post($params['slug']));
        $this->pageTitle($moe->get('post.title')?:'Post Not Found: '.$params['slug']);
        $moe->send('home/post');
    }

    public function profile($moe, $params)
    {
        $this->pageTitle('Profile of '.$params['username']);
        $moe->set('profile', Author::instance()->profile($params['username']));
        $moe->send('home/profile');
    }

    public function category($moe, $params)
    {
        $page = $moe->get('GET.page');
        $page>0 || $page = 1;
        $posts = Post::instance()->categories($params['slug'], $page);
        $moe->set('ptitle', 'Category "'.$params['slug'].'"');
        $this->pageTitle($moe->get('ptitle'));
        $moe->set('posts', $posts['data']);
        $moe->set('pages_url', $moe->siteUrl(__FUNCTION__).'?page={page}');
        $moe->set('pages', array(
            'prev'=>$page>1?$page-1:0,
            'next'=>$page<$posts['totalPage']?$page+1:0,
            ));
        $moe->send('home/news_list');
    }

    public function archive($moe, $params)
    {
        $page = $moe->get('GET.page');
        $page>0 || $page = 1;
        $posts = Post::instance()->archives($params['year'], Helper::monthNumber($params['month']), $page);
        $moe->set('ptitle', 'Archive at '.$params['month'].' '.$params['year']);
        $this->pageTitle($moe->get('ptitle'));
        $moe->set('posts', $posts['data']);
        $moe->set('pages_url', $moe->siteUrl(__FUNCTION__).'?page={page}');
        $moe->set('pages', array(
            'prev'=>$page>1?$page-1:0,
            'next'=>$page<$posts['totalPage']?$page+1:0,
            ));
        $moe->send('home/news_list');
    }

    public function search($moe, $params)
    {
        $page = $moe->get('GET.page');
        $page>0 || $page = 1;
        $posts = Post::instance()->search($params['keyword'], $page);
        $moe->set('ptitle', 'Search Result: "'.$params['keyword'].'"');
        $this->pageTitle($moe->get('ptitle'));
        $moe->set('posts', $posts['data']);
        $moe->set('pages_url', $moe->siteUrl(__FUNCTION__).'?page={page}');
        $moe->set('pages', array(
            'prev'=>$page>1?$page-1:0,
            'next'=>$page<$posts['totalPage']?$page+1:0,
            ));
        $moe->send('home/news_list');
    }

    public function sitemap($moe)
    {
        $this->pageTitle('Sitemap');
        $moe->send('home/sitemap');
    }

    public function about($moe)
    {
        $this->pageTitle('About');
        $moe->send('home/about');
    }

    private function pageTitle($prepend, $hypen = true)
    {
        Instance::set('pageTitle', $prepend.' '.($hypen?'- ':'').Instance::get('pageTitle'));
    }

    public function afterroute($moe)
    {
        $moe->set('rendertime', number_format(microtime(true)-$moe->get('TIME'), 5));
    }

    public function __construct($moe)
    {
        $moe->copy('app.name', 'pageTitle');
        $moe->set('menu', [
            'home'=>'Home',
            'sitemap'=>'Sitemap',
            'about'=>'About',
            ]);
        $moe->set('external', [
            'https://github.com/eghojansu/moe'=>'eghojansu/moe',
            'http://fatfreeframework.com'=>'Fatfree Framework',
            'https://github.com'=>'Github',
            'http://packagist.org'=>'Packagist',
            ]);
        $post = Post::instance();
        $moe->set('populars', $post->popularList());
        $moe->set('archives', $post->archiveList());
    }
}
