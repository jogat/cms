<?php

namespace App\CMS;

use App\CMS\Audience\Rule;
use App\CMS\User\Role;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class Audience {

    const TYPE_STATIC = 1;
    const TYPE_DYNAMIC = 0;

    private $id;

    public function __construct($audience =null) {

        if ($audience !== null) {
            if(is_numeric($audience)){
                $this->id = (int)$audience;
            } else {
                throw new \RuntimeException('Invalid Audience ID please use int.');
            }
        }

    }

    /**
     * @throws \Exception
     */
    public function get($include_rules = false, $include_user_scope = false) {

        try {

            $query = db()->table('audience')
                ->select([
                    'audience.id',
                    db()->raw('MAX(audience.title) as title'),
                    db()->raw('MAX(audience.description) as description'),
                    db()->raw('MAX(audience.defined_by_type) as defined_by_type'),
                    db()->raw('MAX(audience.updated_at) as updated_at'),
                    db()->raw('GROUP_CONCAT(audience_has_rule.rule) as rules'),
                ])->join('audience_has_rule','audience.id','=','audience_has_rule.audience')
                ->groupBy(['audience.id']);

            if ($this->id !== null) {
                $query->where('audience.id', '=', $this->id);
            }

            $result = $query->get();

            if ($include_rules) {

                $rules = $this->rule()->get();

                $result->each(function ($item) use ($rules, $include_user_scope) {

                    $rule_ids = explode(',', $item->rules);

                    $item->rules = $rules->whereIn('id',
                        $rule_ids
                    );

                    $item->users = $include_user_scope ? $this->has_users($item->id, $rule_ids) : [];

                });

            }


            return $result;


        } catch (QueryException | \Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }

    }

    /**
     * @throws \Exception
     */
    public function add($title, $description, $defined_by_type = 1, $rule_values = []) {

        if (empty($title)) {
            throw new \Exception('Missing title', 400);
        }

        if (empty($description)) {
            throw new \Exception('Missing Description', 400);
        }

        if (!is_numeric($defined_by_type) || in_array($defined_by_type, [self::TYPE_STATIC, self::TYPE_DYNAMIC])) {
            throw new \Exception('Invalid defined by type value', 400);
        }

        if (empty($rule_values) || !is_array($rule_values)) {
            throw new \Exception('Missing/Invalid Rules', 400);
        }

        try {

            $this->id = db()->table('audience')
                ->insertGetId([
                    'title'=> $title,
                    'description'=> $description,
                    'defined_by_type'=> $defined_by_type,
                ]);

            $this->set_rules($rule_values);

            return $this;

        } catch (\Exception | QueryException $e) {
            throw new \Exception($e->getMessage(), 500);
        }

    }

    private function set_rules($rule_values) {

        try {

            db()->table('audience_has_rule')
                ->where('audience_has_rule.audience','=', $this->id())
                ->delete();

            if (!empty($rule_values)) {

                $values = [];
                foreach ($rule_values as $rule=> $value) {
                    $values[] = [
                        'audience'=> $this->id(),
                        'rule'=> $rule,
                        'value'=> $value
                    ];
                }

                db()->table('audience_has_rule')->insert($values);

                // Apply audience to users

                $query = db()->table('user')
                    ->join('user_has_role','user.id','=','user_has_role.user')
                    ->where('user_has_role.role','=', Role::ID_SUBSCRIBER);

                foreach ($rule_values as $rule_id=> $value) {

                    if ($rule_id === $this->rule()::ID_MANUAL_USER_SELECTION) {
                        $query->whereIn('user.id', explode(',', $value));
                    }
                    //@todo: build the rest of the rules

                }

                $user_meta = [];
                foreach ($query->pluck('id')->toArray() as $user_id) {
                    $user_meta[] = [
                        'user'=> $user_id,
                        'meta'=> cms()->user()->meta()->type()::ID_AUDIENCE,
                        'value'=> $this->id()
                    ];
                }

                cms()->user()->meta()->bulk_add($user_meta);

            }

        } catch (\Exception | QueryException $e) {
            throw new \Exception($e->getMessage(), 500);
        }



    }

    private function has_users($audience, $rule_ids) {

        $users = [];

        $rule_values = db()->table('audience_has_rule')
            ->where('audience','=', $audience)
            ->whereIn('rule',$rule_ids)
            ->pluck('value', 'rule')
            ->toArray();

        if (!empty($rule_values)) {

            $query = db()->table('user')
                ->select([
                    'user.id',
                    'user.first_name',
                    'user.last_name',
                    'user.email',
                    'user.status',
                ])->join('user_has_role','user.id','=','user_has_role.user')
                ->where('user_has_role.role','=', Role::ID_SUBSCRIBER);

            foreach ($rule_values as $rule_id=> $value) {

                if ($rule_id === $this->rule()::ID_MANUAL_USER_SELECTION) {
                    $query->whereIn('user.id', explode(',', $value));
                }
                //@todo: build the rest of the rules

            }

            $users = $query->get();

        }

        return $users;

    }

    /**
     * @param null $rule
     * @return Rule
     */
    public function rule($rule=null) {
        return new Rule($rule);
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
            throw new \Exception('Missing audience id.');
        }
    }

}
