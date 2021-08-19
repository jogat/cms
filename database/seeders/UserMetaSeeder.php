<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CMS\Audience\Rule;

class UserMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run() {

        $meta = [
            [
                'id'=> cms()->user()->meta()->type()::ID_AUDIENCE,
                'slug'=> 'audiences',
                'title'=> 'Audiences',
                'description'=> 'User is attached to audience'
            ]
        ];

        db()->table('user_meta')->insert($meta);


    }
}
