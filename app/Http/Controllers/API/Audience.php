<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Audience extends Controller {

    public function index(Request $request) {

        $return = [];
        $success = false;

        $include_user_scope = $request->has('include_user_scope');
        $include_rules = $request->has('include_rules');

        try {
            $return = cms()->audience()->get($include_rules, $include_user_scope);
            $success = true;
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() ?? 500;
        }

        if (!$success) {
            return $this->wants_json ? response($return['error'], $return['code']) : redirect()->back()->withErrors($return['error']);
        }

        return response()->json($return);

    }

    public function show($id, Request $request) {

        $return = [];
        $success = false;

        $include_user_scope = $request->has('include_user_scope');
        $include_rules = $request->has('include_rules');

        try {
            $return = cms()->audience($id)->get($include_rules, $include_user_scope)->first();
            $success = true;
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() ?? 500;
        }

        if (!$success) {
            return $this->wants_json ? response($return, $return['code']) : redirect()->back()->withErrors($return['error']);
        }

        return response()->json($return);

    }

    public function store(Request $request) {

        $return['id'] = null;
        $success = false;

        try {

            $title = $request->json('title');
            $description = $request->json('description');
            $defined_by_type = $request->json('defined_by_type');
            $rule_values = $request->json('rule_values');

            $post = cms()->audience()->add(
                $title,
                $description,
                $defined_by_type,
                $rule_values
            );

            $return['id'] = $post->id();
            $success = true;

        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() ?? 500;
        }

        if (!$success) {
            return $this->wants_json ? response()->json($return,$return['code']) : redirect()->back()->withErrors($return['error']);
        }

        return response()->json($return);

    }

    public function create() {}
    public function edit() {}

    public function update($id, Request $request) {

        $return['updated'] = false;
        $success = false;

        try {

            $type = $this->wants_json ? $request->json('type') : $request->post('type');
            $save_as_status = $this->wants_json ? $request->json('save_as_status') : $request->post('save_as_status');
            $title = $this->wants_json ? $request->json('title') : $request->post('title');
            $description = $this->wants_json ? $request->json('description') : $request->post('description');
            $body = $this->wants_json ? $request->json('body') : $request->post('body');
            $thumbnail = $request->file('thumbnail');
            $resource = $this->wants_json ? $request->json('resource') : $request->post('resource');
            $json_data = $this->wants_json ? $request->json('json_data') : $request->post('json_data');

            $return['updated'] = cms()->post($id)->update(
                $save_as_status,
                $type,
                $title,
                $description,
                $body,
                $thumbnail,
                $resource,
                $json_data
            );

            $success = true;

        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() ?? 500;
        }

        if (!$success) {
            return $this->wants_json ? response()->json($return,$return['code']) : redirect()->back()->withErrors($return['error']);
        }

        return $this->wants_json ? response()->json($return) : response($return);

    }

    public function destroy($id, Request $request) {

        $return['deleted'] = false;
        $success = false;

        $hard_delete = $this->wants_json ? (bool)$request->json('hard_delete',false) : (bool)$request->post('hard_delete', false);

        try {
            $return['deleted'] = cms()->post($id)->delete($hard_delete);
            $success = true;

        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            $return['code'] = $e->getCode() ?? 500;
        }

        if (!$success) {
            return $this->wants_json ? response()->json($return,$return['code']) : redirect()->back()->withErrors($return['error']);
        }

        return $this->wants_json ? response()->json($return) : response($return);
    }

}
