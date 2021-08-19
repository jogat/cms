<?php


namespace App\CMS\User;


class Role {

    public const ID_SUPER_ADMIN = 1;
    public const ID_ADMIN = 2;
    public const ID_EDITOR = 3;
    public const ID_AUTHOR = 4;
    public const ID_CONTRIBUTOR = 5;
    public const ID_SUBSCRIBER = 6;

    private $id;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function get(){

        $query = db()->table('user_role');

        if ($this->id !== null) {
            if (is_numeric($this->id)) {
                $query->where('id', '=', $this->id);
            } else {
                $query->where('slug', '=', $this->id);
            }
        }

        return $query->get();
    }

}
