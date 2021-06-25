<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserRolesAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        $role_access = [
            'super-admin' => [
                'create-admin',
                'edit-admin',
                'delete-admin',
                'create-user',
                'update-user',
                'edit-delete-approve-post-comments',
                'add-edit-delete-post-categories',
                'add-edit-delete-links',
                'edit-delete-any-published-post',
                'write-own-pages',
                'manage-media',
                'update-media',
                'view-comments',
                'write-own-posts',
                'edit-own-posts',
                'write-post-comment',
                'manage-profile',
            ],
            'admin' => [
                'create-user',
                'update-user',
                'edit-delete-approve-post-comments',
                'add-edit-delete-post-categories',
                'add-edit-delete-links',
                'edit-delete-any-published-post',
                'write-own-pages',
                'manage-media',
                'update-media',
                'view-comments',
                'write-own-posts',
                'edit-own-posts',
                'write-post-comment',
                'manage-profile',
            ],
            'editor' => [
                'edit-delete-approve-post-comments',
                'add-edit-delete-post-categories',
                'add-edit-delete-links',
                'edit-delete-any-published-post',
                'write-own-pages',
                'manage-media',
                'update-media',
                'view-comments',
                'write-own-posts',
                'edit-own-posts',
                'write-post-comment',
                'manage-profile',
            ],
            'author' => [
                'update-media',
                'view-comments',
                'write-own-posts',
                'edit-own-posts',
                'write-post-comment',
                'manage-profile',
            ],
            'contributor' => [
                'view-comments',
                'write-own-posts',
                'edit-own-posts',
                'write-post-comment',
                'manage-profile',
            ],
            'subscriber' => [
                'write-own-posts',
                'write-post-comment',
                'manage-profile',
            ],
        ];



        db()->table('access')->insert([
            [
                'slug'=> 'create-admin',
                'group-slug'=> 'admin-users',
                'title'=> 'Create admin',
                'description'=> 'Can create admin',
            ],
            [
                'slug'=> 'edit-admin',
                'group-slug'=> 'admin-users',
                'title'=> 'Edit admin',
                'description'=> 'Can edit admin',
            ],
            [
                'slug'=> 'delete-admin',
                'group-slug'=> 'admin-users',
                'title'=> 'Delete admin',
                'description'=> 'Can delete admin',
            ],
            [
                'slug'=> 'create-user',
                'group-slug'=> 'admin-users',
                'title'=> 'Create user',
                'description'=> 'Can create user',
            ],
            [
                'slug'=> 'update-user',
                'group-slug'=> 'admin-users',
                'title'=> 'Update user',
                'description'=> 'Can update user',
            ],

            [
                'slug'=> 'edit-delete-approve-post-comments',
                'group-slug'=> 'post-comments',
                'title'=> 'Manage comments',
                'description'=> 'Edit, Delete or approve post comments',
            ],
            [
                'slug'=> 'add-edit-delete-post-categories',
                'group-slug'=> 'post',
                'title'=> 'Manage categories',
                'description'=> 'Add, Edit or Delete post categories',
            ],
            [
                'slug'=> 'add-edit-delete-links',
                'group-slug'=> 'post',
                'title'=> 'Manage Links',
                'description'=> 'Add, Edit or Delete links',
            ],
            [
                'slug'=> 'edit-delete-any-published-post',
                'group-slug'=> 'post',
                'title'=> 'Manage Published post',
                'description'=> 'Edit or Delete Published posts by any user',
            ],
            [
                'slug'=> 'write-own-pages',
                'group-slug'=> 'pages',
                'title'=> 'Manage Own pages',
                'description'=> 'Write own ages',
            ],
            [
                'slug'=> 'manage-media',
                'group-slug'=> 'media',
                'title'=> 'Manage media',
                'description'=> 'Edit or delete media files',
            ],

            [
                'slug'=> 'update-media',
                'group-slug'=> 'media',
                'title'=> 'Update media',
                'description'=> 'Update media files',
            ],

            [
                'slug'=> 'view-comments',
                'group-slug'=> 'post-comments',
                'title'=> 'View comments',
                'description'=> 'View comments',
            ],
            [
                'slug'=> 'write-own-posts',
                'group-slug'=> 'post',
                'title'=> 'Write own posts',
                'description'=> 'Write owns posts',
            ],
            [
                'slug'=> 'edit-own-posts',
                'group-slug'=> 'post',
                'title'=> 'edit own posts',
                'description'=> 'Edit owns posts',
            ],
            [
                'slug'=> 'write-post-comment',
                'group-slug'=> 'post-comments',
                'title'=> 'Write comment',
                'description'=> 'Write comment in a post',
            ],
            [
                'slug'=> 'manage-profile',
                'group-slug'=> 'profile',
                'title'=> 'Manage profile',
                'description'=> 'Manage profile',
            ],
        ]);

        db()->table('user_role')->insert([
            [
                'slug'=> 'super-admin',
                'title'=> 'Super admin',
                'description'=> 'Same as admin plus create/edit/delete admins',
            ],
            [
                'slug'=> 'admin',
                'title'=> 'Administrator',
                'description'=> 'can do everything and has complete access to posts, pages, plugins, comments, themes, settings, assign user roles and are even able to delete the blog.',
            ],
            [
                'slug'=> 'editor',
                'title'=> 'Editor',
                'description'=> 'is able to publish posts/pages, manage posts/pages, upload files, moderate comments as well as manage other people’s posts/pages.',
            ],
            [
                'slug'=> 'author',
                'title'=> 'Author',
                'description'=> 'can upload files plus write and publish own posts.',
            ],
            [
                'slug'=> 'contributor',
                'title'=> 'Contributor',
                'description'=> 'can write own posts but can’t publish their own post.  Their posts are submitted pending review and an administrator or editor must review and publish their posts. Contributors can’t edit their own publish posts or submit pages as pending review.',
            ],
            [
                'slug'=> 'subscriber',
                'title'=> 'Subscriber',
                'description'=> 'can read comments and write comments.',
            ]
        ]);

        foreach ($role_access as $role=> $access) {

            $role_id = db()->table('user_role')
                ->where('slug','=', $role)
                ->value('id');

            $role_has_access_values = db()->table('access')
                ->whereIn('slug',$access)
                ->pluck('id')
                ->map(function ($access_id) use($role_id) {
                    return [
                        'role'=> $role_id,
                        'access'=> $access_id
                    ];
                });

            db()->table('user_role_has_access')
                ->insert($role_has_access_values->toArray());

        }
    }
}
