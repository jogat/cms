<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Post extends Controller {

    public function index() {

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
            return $this->wants_json ? response($return['error'], 500) : redirect()->back()->withErrors($return['error']);
        }

        return $this->wants_json ? response()->json($return['result']) : view('cms.auth.login', $return['result']);

    }

    public function show($id) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        try {
            $return['result'] = cms()->post($id)->get()->first();
            $return['success'] = true;
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
        }

        if (!$return['success']) {
            return $this->wants_json ? response($return['error'], 500) : redirect()->back()->withErrors($return['error']);
        }

        return $this->wants_json ? response()->json($return['result']) : view('cms.auth.login', $return['result']);
    }

    public function store(Request $request) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        try {

            $type = $this->wants_json ? $request->json('type') : $request->post('type');
            $save_as_status = $this->wants_json ? $request->json('save_as_status') : $request->post('save_as_status');
            $title = $this->wants_json ? $request->json('title') : $request->post('title');
            $description = $this->wants_json ? $request->json('description') : $request->post('description');
            $body = $this->wants_json ? $request->json('body') : $request->post('body');
            $thumbnail = $request->file('thumbnail');
            $resource = $this->wants_json ? $request->json('resource') : $request->post('resource');
            $json_data = $this->wants_json ? $request->json('json_data') : $request->post('json_data');

            $post = cms()->post()->add(
                $save_as_status,
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
            $return['code'] = $e->getCode() ?? 500;
        }

        if (!$return['success']) {
            return $this->wants_json ? response()->json($return,$return['code']) : redirect()->back()->withErrors($return['error']);
        }

        return $this->wants_json ? response()->json($return['result']) : response($return['result']);

    }

    public function create() {}
    public function edit() {}

    public function update($id, Request $request) {

        $return = [
            'result'=> [],
            'success'=> false
        ];

        try {

            $type = $this->wants_json ? $request->json('type') : $request->post('type');
            $save_as_status = $this->wants_json ? $request->json('save_as_status') : $request->post('save_as_status');
            $title = $this->wants_json ? $request->json('title') : $request->post('title');
            $description = $this->wants_json ? $request->json('description') : $request->post('description');
            $body = $this->wants_json ? $request->json('body') : $request->post('body');
            $thumbnail = $request->file('thumbnail');
            $resource = $this->wants_json ? $request->json('resource') : $request->post('resource');
            $json_data = $this->wants_json ? $request->json('json_data') : $request->post('json_data');

            cms()->post($id)->update(
                $save_as_status,
                $type,
                $title,
                $description,
                $body,
                $thumbnail,
                $resource,
                $json_data
            );

            $return['success'] = true;

        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() ?? 500;
        }

        if (!$return['success']) {
            return $this->wants_json ? response()->json($return,$return['code']) : redirect()->back()->withErrors($return['error']);
        }

        return $this->wants_json ? response()->json($return['result']) : response($return['result']);

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
            return $this->wants_json ? response()->json($return,500) : redirect()->back(500)->withErrors($return['error']);
        }

        return $this->wants_json ? response()->json($return['result']) : response($return['result']);
    }

}
