<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CMS\Audience\Rule;

class AudienceRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run() {

        $rules = [
            [
                'id'=> Rule::ID_MANUAL_USER_SELECTION,
                'static'=> true,
                'multiple'=> null,
                'title'=> 'Manual selection of users',
                'description'=> 'Manual selection of users',
                'rule'=> null
            ],
            [
                'id'=> Rule::ID_JOINED,
                'static'=> false,
                'multiple'=> false,
                'title'=> 'Joined',
                'description'=> 'Manual selection of users',
                'rule'=> json_encode([
                    'operators'=> [
                        'includes'=> 'Includes the User',
                        'not_includes'=> 'Does not include the User',
                    ],
                    'options'=> [
                        'never'=> 'Never',
                        'this_week'=> 'This Week',
                        'this_month'=> 'This Month',
                        'last_month'=> 'Last Month',
                        'this_year'=> 'This Year',
                        'custom'=> 'Custom',
                    ]
                ])
            ],
            [
                'id'=> Rule::ID_USER_ACTIVITY,
                'static'=> false,
                'multiple'=> false,
                'title'=> 'User Activity',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [
                        'includes'=> 'Includes the User',
                        'not_includes'=> 'Does not include the User',
                    ],
                    'options'=> [
                        'never'=> 'Never',
                        'this_week'=> 'This Week',
                        'this_month'=> 'This Month',
                        'last_month'=> 'Last Month',
                        'this_year'=> 'This Year',
                        'custom'=> 'Custom',
                    ]
                ])
            ],
            [
                'id'=> Rule::ID_SUBSCRIBER,
                'static'=> false,
                'multiple'=> false,
                'title'=> 'Subscriber',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [],
                    'options'=> [
                        'none'=> 'None',
                        'up_to_5'=> 'Up to 5',
                        'up_to_10'=> 'Up to 10',
                        'up_to_15'=> 'Up to 15',
                        'more_than_15'=> 'More than 15',
                        'custom'=> 'Custom',
                    ]
                ])
            ],
            [
                'id'=> Rule::ID_ENGAGE,
                'static'=> false,
                'multiple'=> false,
                'title'=> 'Engage',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [],
                    'options'=> [
                        'none'=> 'None',
                        'up_to_5'=> 'Up to 5',
                        'up_to_10'=> 'Up to 10',
                        'up_to_15'=> 'Up to 15',
                        'more_than_15'=> 'More than 15',
                        'custom'=> 'Custom',
                    ]
                ])
            ],
            [
                'id'=> Rule::ID_RESOURCE_USER,
                'static'=> false,
                'multiple'=> false,
                'title'=> 'Resource User',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [],
                    'options'=> [
                        'none'=> 'None',
                        'up_to_5'=> 'Up to 5',
                        'up_to_10'=> 'Up to 10',
                        'up_to_15'=> 'Up to 15',
                        'more_than_15'=> 'More than 15',
                        'custom'=> 'Custom',
                    ]
                ])
            ],
            [
                'id'=> Rule::ID_MIDDLE_NAME,
                'static'=> false,
                'multiple'=> true,
                'title'=> 'Middle Name',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [
                        'is_in'=> 'Is',
                        'is_not_in'=> 'Is not',
                        'is_empty'=> 'Is empty',
                        'is_not_empty'=> 'Is not empty',
                        'contains'=> 'Contains',
                        'does_not_contains'=> 'Does not contains',
                    ],
                    'options'=> []
                ])
            ],
            [
                'id'=> Rule::ID_SECONDARY_EMAIL,
                'static'=> false,
                'multiple'=> true,
                'title'=> 'Secondary email',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [
                        'is_in'=> 'Is',
                        'is_not_in'=> 'Is not',
                        'is_empty'=> 'Is empty',
                        'is_not_empty'=> 'Is not empty',
                        'contains'=> 'Contains',
                        'does_not_contains'=> 'Does not contains',
                    ],
                    'options'=> []
                ])
            ],
            [
                'id'=> Rule::ID_MARRIED,
                'static'=> false,
                'multiple'=> false,
                'title'=> 'Married',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [
                        'is_in'=> 'Is',
                        'is_not_in'=> 'Is not',
                        'is_empty'=> 'Is empty',
                        'is_not_empty'=> 'Is not empty',
                        'contains'=> 'Contains',
                        'does_not_contains'=> 'Does not contains',
                    ],
                    'options'=> [
                        1=> 'True',
                        0=> 'False'
                    ]
                ])
            ],
            [
                'id'=> Rule::ID_USER_DEPARTMENT,
                'static'=> false,
                'multiple'=> true,
                'title'=> 'User Department',
                'description'=> '',
                'rule'=> json_encode([
                    'operators'=> [
                        'is_in'=> 'Is',
                        'is_not_in'=> 'Is not',
                        'is_empty'=> 'Is empty',
                        'is_not_empty'=> 'Is not empty',
                        'contains'=> 'Contains',
                        'does_not_contains'=> 'Does not contains',
                    ],
                    'options'=> []
                ])
            ],
        ];

        db()->table('audience_rule')->insert($rules);


    }
}
