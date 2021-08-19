<?php

namespace App\CMS\Audience;

use App\CMS\User\Role;
use Illuminate\Database\QueryException;

class Rule {

    public const ID_MANUAL_USER_SELECTION = 1;
    public const ID_JOINED = 2;
    public const ID_USER_ACTIVITY = 3;
    public const ID_SUBSCRIBER = 4;
    public const ID_ENGAGE = 5;
    public const ID_RESOURCE_USER = 6;
    public const ID_MIDDLE_NAME = 7;
    public const ID_SECONDARY_EMAIL = 8;
    public const ID_MARRIED = 9;
    public const ID_USER_DEPARTMENT = 10;

    private $id;

    public function __construct($rule =null) {

        if ($rule !== null) {
            if(is_numeric($rule)){
                $this->id = (int)$rule;
            } else {
                throw new \RuntimeException('Invalid Audience rule ID please use int.');
            }
        }

    }

    /**
     * @param false $include_user_scope
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function get() {

        try {

            $query = db()->table('audience_rule')
                ->select([
                    'id',
                    'static',
                    'multiple',
                    'title',
                    'description',
                    'rule'
                ]);

            if ($this->id !== null) {
                $query->where('audience_rule.id', '=', $this->id);
            }

            return $query->get();

        } catch (\Exception | QueryException $e) {
            throw new \Exception($e->getMessage(), 500);
        }

    }


    /**
     * @return array
     * @throws \Exception
     */
    public function get_options() {

        $options = [];

        //@todo: this could be totally different based on the tenant settings and dynamic fields

        switch ($this->id()) {
            case self::ID_USER_DEPARTMENT;
            //@todo: return user departments
                break;
            case self::ID_SECONDARY_EMAIL;
                //@todo: return user secondary email
                break;
            case self::ID_MIDDLE_NAME;
                //@todo: return user middle name
                break;
        }

        return $options;

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
            throw new \Exception('Missing audience rule id.', 400);
        }
    }

}
