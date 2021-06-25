<?php


namespace App\CMS\User;


class Role {

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
