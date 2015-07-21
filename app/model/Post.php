<?php

namespace app\model;

use moe\AbstractModel;
use moe\Instance;
use moe\tools\Web;

class Post extends AbstractModel
{
    protected function schema()
    {
        return array(
            'id_post'=>'ID Post',
            'title'=>array(
                'Post Title',
                array(
                    'trim', 'required', 'alphanumeric'
                    ),
                ),
            'slug'=>array(
                'Slug',
                array(
                    'slug'
                    ),
                ),
            'content'=>array(
                'Post Content',
                array(
                    'required',
                    ),
                ),
            'id_category'=>array(
                'ID Category',
                array(
                    'required', 'exists'=>'app\\model\\Category->exists'
                    ),
                ),
            'id_author'=>array(
                'ID Author',
                array(
                    'required', 'exists'=>'app\\model\\Author->exists'
                    ),
                ),
            'hits'=>array(
                'Hits',
                null,
                0,
                ),
            'date_created'=>array(
                'Date Created',
                null,
                date('Y-m-d H:i:s'),
                ),
            );
    }

    public function primaryKey()
    {
        return 'id_post';
    }

    public function relation()
    {
        return array(
            'category'=>'join {join} on {join}.id_category = {table}.id_category',
            'author'=>'join {join} on {join}.id_author = {table}.id_author',
            );
    }

    public function slug($slug)
    {
        $slug || $slug = Web::instance()->slug($this->title);
        $that = clone $this;
        $that->findBySlugBegin($slug)->get();
        return $slug.($that->dry()?'':'-'.($that->count()+1));
    }

    public function popularList()
    {
        return $this->select('slug,title')
            ->where('hits > 10')
            ->order('hits desc')
            ->all(false, 10);
    }

    public function archiveList()
    {
        $archive = array();
        foreach ($this
            ->select('year(date_created) as tahun,monthname(date_created) as bulan,slug,title')
            ->order('date_created desc')
            ->all() as $row) {
            $tahun = array_shift($row);
            $bulan = array_shift($row);
            isset($archive[$tahun]) || $archive[$tahun] = array();
            isset($archive[$tahun][$bulan]) || $archive[$tahun][$bulan] = array();
            $archive[$tahun][$bulan][] = $row;
        }
        return $archive;
    }

    public function latest($page)
    {
        return $this->useRelation('all')
            ->select('category.*, author.*, {table}.*, category.slug as category_slug')
            ->order('{table}.date_created desc')
            ->page($page, 3);
    }

    public function categories($slug, $page)
    {
        $this->where('category.slug=:s', array(':s'=>$slug));
        return $this->latest($page);
    }

    public function search($keyword, $page)
    {
        $this->findByTitleLike($keyword);
        return $this->latest($page);
    }

    public function archives($year, $month, $page)
    {
        $this->where('year(date_created)=:y and month(date_created)=:m', array(':y'=>$year,':m'=>$month));
        return $this->latest($page);
    }

    public function post($slug)
    {
        $this->useRelation('all')
            ->select('category.*, author.*, {table}.*, category.slug as category_slug')
            ->findBySlug($slug)->get(1);
        $this->hits++;
        $this->save();
        return $this->cast();
    }
}
