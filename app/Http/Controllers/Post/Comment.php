<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Comment extends Controller {

    public function index($post, Request $request) {

        try {
            return cms()->post($post)->comment()->get();
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

    }

    public function show($post, $comment) {
        try {
            return cms()->post($post)->comment($comment)->get();
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function store($post, Request $request) {

        $content = $request->post('content') ?? $request->json('content');
        $parent = $request->post('parent') ?? $request->json('parent');

        try {

            $comment = cms()->post($post)->comment()->add($content, $parent);

            return $comment->get();

        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

    }

    public function update($post, $comment, Request $request) {

        $content = $request->post('content') ?? $request->json('content');
        $published = $request->post('published') ?? $request->json('published');

        try {

            return  cms()->post($post)->comment($comment)->update(
                (bool)$published,
                $content,
            );

        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

    }

    public function delete($post, $comment) {

        try {
            return  cms()->post($post)->comment($comment)->delete();
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

    }
}
