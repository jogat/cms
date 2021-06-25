<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;

class Session extends BaseController {

    public function login_view(Request $request) {

        if (auth()->active()) {
            return redirect('/home');
        }

        return view('cms.auth.login', [
            'next'=> $request->get('next')
        ]);

    }

    public function login(Request $request) {

        if (auth()->active()) {
            auth()->logout();
        }

        $return = [
            'as_json'=> $request->expectsJson(),
            'success'=> false,
            'json'=> response([
                'error'=>'Invalid login.',
            ], 401),
            'web'=> Redirect::back()->withErrors(['Invalid credentials'])
        ];

        $email = $return['as_json'] ? $request->json('email') : $request->post('email');
        $password = $return['as_json'] ? $request->json('password') : $request->post('password');
        $next = $return['as_json'] ? $request->json('next') : $request->post('next');


        try {

            if (auth()->login($email, $password)) {

                auth()->log('cms/user/login', ['ip'=>$_SERVER['REMOTE_ADDR']]);

                $return['success'] = true;
                $return['json'] = response()->json(auth()->data());
                $return['web'] = redirect(!empty($next) ? $next : '/home');

            }

        } catch(\Exception $e) {
            $return['json'] = response($e->getMessage(), $e->getCode() ?? 500);
            $return['web'] = Redirect::back()->withErrors([$e->getMessage()]);
        }



        if ($return['success']) {
            try {
                sleep(random_int(1,5));
            } catch(\Exception $e){}
        }

        return $return['as_json'] ? $return['json'] : $return['web'];

    }

    public function logout(Request $request) {

        $return = [
            'as_json'=> $request->expectsJson(),
            'json'=> response('Logged out.'),
            'web'=> redirect('/login')
        ];

        if (!auth()->active()) {

            $return['json']= response([
                'error' => 'session not active',
            ], 409);
            $return['web'] = Redirect::back()->withErrors(['session not active']);

        }

        auth()->logout();
        auth()->log('eo/user/logout');
        return $return['as_json'] ? $return['json'] : $return['web'];

    }

    public function data(){

        $data = new Collection(auth()->data());

        return response()->json(
            $data->only([
                'session',
                'info',
                'access',
                'meta',
                'customer',
                'cart',
                'menu',
            ])->all(),

        );

    }


    public function active(){}
    public function refresh(){}
    public function dev(){}
    public function password_reset(){}
    public function password_set(){}
    public function password_key(){}
    public function sign_up(){}


}
