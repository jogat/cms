<?php


namespace App\CMS\Post;


class Category {

    private $id;

    public function __construct($category = null) {

        if ($category !== null) {
            if (is_numeric($category)) {
                $this->id = (int)$category;
            } elseif ($id = db('cms')->table('post_category')->where('slug', $category)->first('id')) {
                $this->id = (int)$id->id;
            } else {
                throw new \RuntimeException('Failed to assign category id to Post\Category class.');
            }
        }

    }

    public function get() {

        $query = db('cms')->table('post_category');

        if ($this->id !== null) {
            $query->where('id', '=', $this->id);
        }

        return $query->get();
    }

}
