<?php


namespace App;


use App\CMS\Menu;
use App\CMS\Post;
use App\CMS\User;

class CMS {


    public function user($id = null) {
        return new User($id);
    }

    public function menu($id = null){
        return new Menu($id);
    }

    public function post($post = null) {
        return new Post($post);
    }

}
