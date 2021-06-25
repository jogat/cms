<?php


namespace App\CMS;


use App\CMS\User\Role;
use Illuminate\Database\QueryException;

class User {

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS = [
        self::STATUS_INACTIVE=> 'Inactive',
        self::STATUS_ACTIVE=> 'Active',
    ];

    private $id;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function get(){ }

    public function add($role, $first_name, $last_name, $email, $password_1, $password_2){

        if (empty($first_name)) {
            throw new \RuntimeException('Missing first name');
        }

        if (empty($last_name)) {
            throw new \RuntimeException('Missing last name');
        }

        if (empty($email)) {
            throw new \RuntimeException('Missing email');
        }

        if (empty($password_1)) {
            throw new \RuntimeException('Missing password');
        }

        if (empty($password_2)) {
            throw new \RuntimeException('Missing password confirmation');
        }

        if ($password_1 != $password_2) {
            throw new \RuntimeException('Passwords do not match');
        }

        if (!valid_email($email)) {
            throw new \RuntimeException('Invalid email');
        }

        $role_id =  $this->role($role)->get();

        if ($role_id->isEmpty() || $role_id->count() !== 1) {
            throw new \RuntimeException('Invalid Role');
        }

        if (db()->table('user')->where('email','=',$email)->count()) {
            throw new \RuntimeException('Email already in use');
        }

        try {

            $this->id = db()->table('user')
                ->insertGetId([
                    'first_name'=> $first_name,
                    'last_name'=> $last_name,
                    'status'=> self::STATUS_INACTIVE,
                    'email'=> $email,
                    'password'=> password_hash($password_1,PASSWORD_DEFAULT )
                ]);

            db()->table('user_has_role')
                ->insert([
                    'role'=> $role_id->pluck('id')->toArray()[0],
                    'user'=> $this->id
                ]);

            //@todo: send email verification out

            return $this;

        } catch (QueryException $e) {
            throw new \RuntimeException($e->getMessage());
        }

    }

    public function update(){ }

    /**
     * @param $id
     * @return Role
     */
    public function role($id=null){
        return new Role($id);
    }

    /**
     * Get user id
     * @return int
     */
    public function id(){
        $this->part_required();
        return $this->id;
    }

    private function part_required(){
        if(empty($this->id) || !is_numeric($this->id)){
            throw new \RuntimeException('Missing part number.');
        }
    }

}
