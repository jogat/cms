<?php

namespace Database\Seeders;

use App\CMS\Post\Status;
use App\CMS\Post\Type;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {

        db()->table('post_type')->insert([
            [
                'id'=> Type::ID_IMAGE,
                'slug'=> 'image',
                'title'=> 'Image',
            ],
            [
                'id'=> Type::ID_VIDEO,
                'slug'=> 'video',
                'title'=> 'Video',
            ],
            [
                'id'=> Type::ID_LINK,
                'slug'=> 'link',
                'title'=> 'Link',
            ],
            [
                'id'=> Type::ID_ARTICLE,
                'slug'=> 'article',
                'title'=> 'Article',
            ],
            [
                'id'=> Type::ID_POLL,
                'slug'=> 'poll',
                'title'=> 'Poll',
            ],
            [
                'id'=> Type::ID_SURVEY,
                'slug'=> 'survey',
                'title'=> 'Survey',
            ],

        ]);

        db()->table('post_status')->insert([
            [
                'id'=> Status::ID_DRAFT,
                'slug'=> 'draft',
                'title'=> 'Draft',
            ],
            [
                'id'=> Status::ID_SUBMITTED,
                'slug'=> 'submitted',
                'title'=> 'Submitted',
            ],
            [
                'id'=> Status::ID_PUBLISHED,
                'slug'=> 'published',
                'title'=> 'Published',
            ],
            [
                'id'=> Status::ID_ARCHIVED,
                'slug'=> 'archived',
                'title'=> 'Archived',
            ],
        ]);

    }
}
