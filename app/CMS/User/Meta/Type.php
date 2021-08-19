<?php

namespace App\CMS\User\Meta;

class Type {

    const ID_AUDIENCE = 1;

    private $id;

    public function __construct($type) {
        $this->id = $type;
    }

    public function get() {

        $query = db()->table('user_meta');

        if ($this->id !== null) {
            $query->where('id', '=', $this->id);
        }

        return $query->get();

    }

}
