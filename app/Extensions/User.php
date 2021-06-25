<?php

namespace App\Extensions;

class User {

    private $id;

    /**
     * A DB heavy user data retrieval
     *
     * @param $id
     *
     * @throws \Exception
     */
    public function __construct($id){
        if(empty($id)){
            throw new \Exception('Please provide a user id.');
        }
        $this->id = $id;
    }

    function info(){
        $row = db('cms')->table('user')
            ->where('id','=', $this->id)
            ->first();

        return [
            'id'=>$row->id,
            'email'=>$row->email,
            'name'=>[
                'first'=> $row->first_name,
                'last'=> $row->last_name,
            ],
            'mod'=>$row->updated_at,
            'status'=>(int)$row->status,
        ];
    }

    /**
     * Get user access or check if user has access
     *
     * @param null $slug
     *
     * @return array|bool
     */
    public function access($slug = null){

        $query = db('cms')->select('
            SELECT access.*
            FROM (
                SELECT access as id
                FROM user_has_role
                LEFT JOIN user_role_has_access ON user_has_role.role=user_role_has_access.role
                WHERE user=?
                UNION
                SELECT access
                FROM user_has_access
                WHERE user=?
            ) as results
            LEFT JOIN access ON access.id=results.id
        ', [$this->id, $this->id]);

        $access = [];

        foreach($query as $row){

            if($slug!==null && $slug===$row->slug) {
                return true;
            }

            if($slug===null && !in_array($row->slug, $access, true)) {
                $access[]=$row->slug;
            }

        }

        if($slug!==null){
            return false;
        }

        return $access;

    }

    public function meta($slug = null){

        $query = db('cms')->select('
            SELECT *
            FROM user_has_meta
            LEFT JOIN user_meta ON user_has_meta.meta=user_meta.id
            WHERE user_has_meta.user=?
        ', [$this->id]);

        $meta = [];
        foreach($query as $row){

            if($slug!==null && $slug===$row->slug) {
                return $row->value;
            }

            if($slug===null) {
                $meta[$row->slug] = $row->value;
            }

        }

        if($slug!==null){
            return false;
        }

        return $meta;

    }

    public function role($slug = null){

        $query = db('cms')->table('user_has_role')
            ->leftJoin('user_role','user_has_role.role','=','user_role.id')
            ->where('user_has_role.user','=',$this->id);

        $role = [];
        foreach($query->get() as $row){

            if($slug!==null && $slug===$row->slug) {
                return $row->value;
            }

            if($slug===null) {
                $role[] = $row->slug;
            }

        }

        if($slug!==null){
            return false;
        }

        return $role;

    }

    public function menu() {

        $user_access = $this->access();

        $menu = db('cms')->table('menu')
            ->select([
                'menu.id',
                'menu.parent_id',
                'menu.title',
                'menu.url',
            ])
            ->orderBy('menu.parent_id')
            ->get();

        $menu_access = db('cms')->table('menu_has_access')
            ->select([
                'menu_has_access.menu',
                'access.slug'
            ])
            ->join('access','access.id','=','menu_has_access.access')
            ->get();

        $results = $menu->each(function ($item) use ($menu_access) {
            $item->access = $menu_access->where('menu','=',$item->id)->pluck('slug');
        })->filter(function($item) use ($user_access) {

            if ($item->access->isEmpty() || array_intersect($item->access->toArray(), $user_access)) {
                return $item;
            }

        });

        $return = [];

        foreach ($results as $item) {

            if (!$item->parent_id) {
                $return[$item->id] = $item;
                $return[$item->id]->children = [];
                continue;
            }

            $return[$item->parent_id]->children[] = $item;
        }

        return array_values($return);

    }
}
