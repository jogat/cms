<?php


namespace App\CMS\Post;


class Status {

    public const ID_DRAFT = 1;
    public const ID_SUBMITTED = 2;
    public const ID_PUBLISHED = 3;
    public const ID_ARCHIVED = 4;

    public function get() {
        return db('cms')->table('post_status')->get();
    }

}
