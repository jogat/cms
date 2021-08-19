<?php

namespace App\CMS\User;

use App\CMS\User\Meta\Type;

class Meta {

    private $id;

    public function __construct($meta = null) {

        if ($meta !== null) {

            if(is_numeric($meta)){
                $this->id = (int)$meta;
            } elseif($id = db('cms')->table('user_meta')->where('slug', '=', $meta)->first('id')) {
                $this->id = (int)$id->id;
            } else {
                throw new \RuntimeException('Post ID not found.');
            }

        }

    }

    public function get() {

        $query = db()->table('user_has_meta');

        if ($this->id !== null) {
            $query->where('id', '=', $this->id());
        }

        return $query->get();

    }

    public function add($meta_id, $value, $user_id) {

    }

    /**
     * @param array $values
     * $user_meta[] = [
     *      'user'=> 1,
     *      'meta'=> 1,
     *      'value'=> 2
     *  ];
     */
    public function bulk_add($values= []) {

        $inserted = 0;

        foreach (array_chunk($values, 5000, true) as $values_chunk) {

            $inserted += db()->table('user_has_meta')->insert($values_chunk);

        }

        return $inserted;

    }

    public function edit($meta_id, $value, $user_id) {

    }

    public function delete($meta_id, $value, $user_id){

    }

    public function type() {
        return new Type($this->id);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function id(){
        $this->id_required();
        return $this->id;
    }

    /**
     * @throws \Exception
     */
    private function id_required(){
        if(empty($this->id)){
            throw new \Exception('Missing meta id.', 400);
        }
    }

}
