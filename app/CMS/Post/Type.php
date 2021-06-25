<?php


namespace App\CMS\Post;


class Type {

    public const ID_IMAGE = 1;
    public const ID_VIDEO = 2;
    public const ID_LINK = 3;
    public const ID_ARTICLE = 4;
    public const ID_POLL = 5;
    public const ID_SURVEY = 6;

    private $id;

    public function __construct($type = null) {

        if ($type !== null) {
            if (is_numeric($type)) {
                $this->id = (int)$type;
            } elseif ($id = db('cms')->table('post_type')->where('slug', $type)->first('id')) {
                $this->id = (int)$id->id;
            } else {
                throw new \RuntimeException('Failed to assign post type to Post\Type class.');
            }
        }

    }

    public function get() {

        $query = db('cms')->table('post_type');

        if ($this->id !== null) {
            $query->where('id', '=', $this->id);
        }

        return $query->get();
    }

}
