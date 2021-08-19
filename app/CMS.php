<?php


namespace App;


use App\CMS\Category;
use App\CMS\Menu;
use App\CMS\Post;
use App\CMS\User;
use App\CMS\Audience;

class CMS {

    /**
     * @param null $id
     * @return User
     */
    public function user($id = null) {
        return new User($id);
    }

    /**
     * @param null $id
     * @return Menu
     */
    public function menu($id = null){
        return new Menu($id);
    }

    /**
     * @param null $post
     * @return Post
     */
    public function post($post = null) {
        return new Post($post);
    }

    /**
     * @param null $audience
     * @return Audience
     */
    public function audience($audience = null) {
        return new Audience($audience);
    }


}
