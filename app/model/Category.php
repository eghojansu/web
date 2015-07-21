<?php

namespace app\model;

use moe\AbstractModel;
use moe\Instance;
use moe\tools\Web;

class Category extends AbstractModel
{
    protected function schema()
    {
        return array(
            'id_category'=>'ID Category',
            'category'=>array(
                'Category',
                array(
                    'trim', 'required', 'alpha', 'uniqueCategory'
                    ),
                ),
            'slug'=>array(
                'Slug',
                array(
                    'slug'
                    ),
                ),
            );
    }

    public function primaryKey()
    {
        return 'id_category';
    }

    public function slug($slug)
    {
        $slug || $slug = Web::instance()->slug($this->category);
        $that = clone $this;
        $that->findBySlugBegin($slug)->get();
        return $slug.($that->dry()?'':'-'.($that->count()+1));
    }

    public function optionList()
    {
        $list = array();
        foreach ($this->select('id_category,category')
            ->order('category')->all(false) as $row)
            $list[array_shift($row)] = array_shift($row);
        return $list;
    }
}
