<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Post extends BaseController {

    public function index(Request $request) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        try {
            $return['result'] = cms()->post()->get();
            $return['success'] = true;
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
        }

        if (!$return['success']) {
            return $request->expectsJson() ? response($return['error'], 500) : redirect()->back()->withErrors($return['error']);
        }

        return $request->expectsJson() ? response()->json($return['result']) : view('cms.auth.login', $return['result']);

    }

    public function show($id, Request $request) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        try {
            $return['result'] = cms()->post($id)->get();
            $return['success'] = true;
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
        }

        if (!$return['success']) {
            return $request->expectsJson() ? response($return['error'], 500) : redirect()->back()->withErrors($return['error']);
        }

        return $request->expectsJson() ? response()->json($return['result']) : view('cms.auth.login', $return['result']);
    }

    public function store(Request $request) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        $expects_json = $request->expectsJson();

        try {

            $type = !$expects_json ? $request->post('type') : $request->json('type');
            $save_as = !$expects_json ? $request->post('save_as') : $request->json('save_as');
            $title = !$expects_json ? $request->post('title') : $request->json('title');
            $description = !$expects_json ? $request->post('description') : $request->json('description');
            $body = !$expects_json ? $request->post('body') : $request->json('body');
            $thumbnail = $request->file('thumbnail');
            $resource = !$expects_json ? $request->post('resource') : $request->json('resource');
            $json_data = !$expects_json ? $request->post('json_data') : $request->json('json_data');

            $post = cms()->post()->add(
                $save_as,
                $type,
                $title,
                $description,
                $body,
                $thumbnail,
                $resource,
                $json_data
            );

            $return['result'] = $post->id();
            $return['success'] = true;

        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode();
        }

        if (!$return['success']) {
            return response($return['error'], $return['code']);
        }

        return $request->expectsJson() ? response()->json($return['result']) : response($return['result']);

    }

    public function create() {}
    public function edit() {}

    public function update($id, Request $request) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        $expects_json = $request->expectsJson();

        try {

            $type = !$expects_json ? $request->post('type') : $request->json('type');
            $save_as = !$expects_json ? $request->post('save_as') : $request->json('save_as');
            $title = !$expects_json ? $request->post('title') : $request->json('title');
            $description = !$expects_json ? $request->post('description') : $request->json('description');
            $body = !$expects_json ? $request->post('body') : $request->json('body');
            $thumbnail = $request->file('thumbnail');
            $resource = !$expects_json ? $request->post('resource') : $request->json('resource');
            $json_data = !$expects_json ? $request->post('json_data') : $request->json('json_data');

            $post = cms()->post($id)->update(
                $save_as,
                $type,
                $title,
                $description,
                $body,
                $thumbnail,
                $resource,
                $json_data
            );

            $return['result'] = $post->id();
            $return['success'] = true;

        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() > 0 ? $e->getCode() : 500;
        }

        if (!$return['success']) {
            return response($return['error'], $return['code']);
        }

        return $request->expectsJson() ? response()->json($return['result']) : response($return['result']);

    }

    public function archive($id, Request $request) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        try {
            $return['result'] = cms()->post($id)->archive();
            $return['success'] = true;

        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() > 0 ? $e->getCode() : 500;
        }

        if (!$return['success']) {
            return response($return['error'], $return['code']);
        }

        return $request->expectsJson() ? response()->json($return['result']) : response($return['result']);
    }

}
