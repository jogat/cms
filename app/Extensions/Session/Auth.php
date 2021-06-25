<?php

namespace App\Extensions\Session;

use App\Extensions\User as user;
//use App\Extensions\Customer as customer;
//use App\Helpers\Recaptcha;
//use App\Mail\ResetUserPassword;
use DateTime;
use Illuminate\Database\QueryException;

class Auth {

    //public $number = 0;
    private $app;
    private $id;

    /**
     * Auth constructor.
     *
     * @param $app
     *
     * @throws \Exception
     */
    public function __construct($app) {

        $this->app = $app;

        session_set_save_handler(new Handler());
        session_name(env('APP_SESSION_NAME'));

        $currentCookie = session_get_cookie_params();
        session_set_cookie_params(
            $currentCookie['lifetime'],
            '/',
            $currentCookie['domain'],
            $currentCookie['secure'],
            true
        );

        if(!session_start()){
            throw new \Exception('Failed to start session.');
        }

        $lifetime = 21600; // 6 hours
        setcookie(session_name(),session_id(),time() + $lifetime,'/',$currentCookie['domain']);

        if($this->active()){
            $this->id = $this->id();
        }

    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function initiate_session(){

        if(empty($this->id)){
            throw new \Exception('Failed to initiate session, empty user ID');
        }

        $user = new user($this->id);
        $_SESSION = [
            'session' => true,
            'info' => $user->info(),
            'access' => $user->access(),
            'meta' => $user->meta(),
            'role' => $user->role(),
            'menu' => $user->menu(),
        ];

        return true;
    }

    /**
     * Much login, very bad title for login function
     *
     * @param $email
     * @param $password
     * @return bool
     * @throws \Exception
     */
    public function login($email, $password){

        $log_data = [
            'email' => $email,
            'user' => null,
            'success' => 0,
            'session' => session_id(),
            'ip' => $_SERVER['REMOTE_ADDR'],
        ];

        $log_attempt = function($data){
            db('cms')->table('user_login_attempts')->insert($data);
        };

        if(!empty($email) && !empty($password)){

            $email_pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
            if(preg_match($email_pattern, $email) !== 1){
                $log_data['success'] = -1;
                $log_attempt($log_data);
                return false;
            }

            $row = db('cms')->table('user')
                ->where('email','like', $email)
                ->where('status','>=',0)
                ->first();

            if(empty($row)){
                $log_data['success'] = -2;
                $log_attempt($log_data);
                return false;
            }

            $log_data['user'] = $row->id;

            if(password_verify($password, $row->password)){

                // SUCCESS

                $this->id = $row->id;
                $this->initiate_session();
                $log_data['success'] = 1;
                $log_attempt($log_data);

                return true;

            }

            $log_data['success'] = -3;

        }

        $log_attempt($log_data);
        return false;

    }

    /**
     * Logout
     *
     * @return bool true: logged the user out. false: no auth data found
     */
    public function logout(){
        if($this->active()){
            return session_destroy();
        }
        return false;
    }

    /**
     * Return session data
     *
     * @return array|bool
     */
    public function data(){
        return $_SESSION;
    }

    /**
     * Returns true if valid session is active.
     *
     * @return bool
     */
    public function active(){
        if(isset($_SESSION['session'])){
            return $_SESSION['session'] === true;
        }
        return false;
    }

    /**
     * Refresh session data from the DB
     *
     * @throws \Exception
     */
    public function refresh(){
        if($this->active()){
            $this->initiate_session();
            return true;
        }
        return false;
    }

    /**
     * Returns true if valid session is active.
     *
     * @return bool
     */
    public function id(){
        return $_SESSION['info']['id'] ?? false;
    }

    /**
     * @param array|string $slugs
     * @return bool
     */
    public function info(){
        if($this->active()){ // if active
            return $_SESSION['info'];
        }
        return false;
    }

    /**
     * @param string $format full, first or last
     *
     * @return bool|mixed
     */
    public function name($format = 'full'){
        if($this->active()){ // if active
            $name = $_SESSION['info']['name'];
            switch($format){
                case 'first':
                    return $name['first'];
                case 'last':
                    return $name['last'];
                case 'full':
                default:
                    return "{$name['first']} {$name['last']}";
            }
        }
        return false;
    }

    /**
     * @param array|string $slugs
     * @return bool|array
     */
    public function access($slugs = null){
        if($this->active()){ // if active
            if($slugs===null){
                return $_SESSION['access'];
            }
            if(is_string($slugs) && in_array($slugs, $_SESSION['access'], true)){
                return true;
            }
            if(is_array($slugs)){
                foreach($slugs as $slug){
                    if(in_array($slug, $_SESSION['access'], true)){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get active user meta information.
     *
     * @param string $slug
     *
     * @return bool|array Returns the meta array/string value saved in session. False if not found.
     */
    public function meta($slug = null, $value = null){

        if(!$this->active()){
            return false;
        }

        if($slug===null){
            return $_SESSION['meta'];
        }

        if(empty($_SESSION['meta'][$slug]) && $value===null){
            return false;
        }

        if($value!==null){

            $meta_id = db('cms')
                ->table('user_meta')
                ->where('slug', '=', $slug)
                ->value('id');

            if(empty($meta_id)){
                throw new \RuntimeException("Invalid meta slug: $slug");
            }

            db('cms')
                ->table('user_has_meta')
                ->updateOrInsert([
                    'user' => $this->id(),
                    'meta' => $meta_id,
                ],[
                    'value' => $value
                ]);

            $_SESSION['meta'][$slug] = $value;

        }

        return $_SESSION['meta'][$slug];

    }

    /**
     * @param array|string $slugs
     * @return bool
     */
    public function role($slugs = null){
        if($this->active()){ // if active
            if($slugs===null){
                return $_SESSION['role'];
            }
            if(is_string($slugs) && in_array($slugs, $_SESSION['role'], true)){
                return true;
            }
            if(is_array($slugs)){
                foreach($slugs as $slug){
                    if(in_array($slug, $_SESSION['role'], true)){
                        return true;
                    }
                }
            }
        }
        return false;
    }




    /**
     * @param string $key
     * @param null|array $data
     * @return bool|mixed
     */
    public function search_meta($key, $data=null){
        if($data === null){
            if(isset($_SESSION['search']['meta']['key'])){
                if($_SESSION['search']['meta']['key'] === $key){
                    return $_SESSION['search']['meta']['data'];
                }
            }
        } else {
            $_SESSION['search']['meta'] = [];
            return $_SESSION['search']['meta'] = [
                'key' => $key,
                'data' => $data
            ];
        }

        return false;

    }

    /**
     * Record user action and store details about the action.
     * If action detail array is too deep, it will turn the value into json
     *
     * @param string $action
     * @param array $detailArray
     *
     * @return bool|int Return record ID, false on failure
     */
    public function log($action, $detailArray=[]){

        $user_id = (int)$this->id();

        if(empty($detailArray) || !is_array($detailArray)){
            $detailArray = [];
        }

        if(empty($user_id)){
            return false;
        }

        $id = db('cms')
            ->table('user_track_action')
            ->insertGetId([
                'user' => $user_id,
                'action' => $action,
                'session' => session_id(),
            ]);

        if(empty($id)){
            return false;
        }

        if(!empty($detailArray)){

            $query = db('cms')->table('user_track_action_detail');

            foreach($detailArray as $title=>$value){
                if(is_array($value)){
                    $value = json_encode($value);
                }
                $query->insert([
                    'action' => $id,
                    'title' => $title,
                    'value' => $value,
                ]);
            }

        }

        return $id;

    }

    public function temp($name, $value = null){

        if($value===null){
            return $_SESSION['temp'][$name] ?? false;
        }

        return $_SESSION['temp'][$name] = $value;

    }



    /**
     * @param $recaptcha_key
     * @param $customer_number
     * @param $name_first
     * @param $name_last
     * @param $zip_code
     * @param $password_1
     * @param $password_2
     * @param $pin_customer
     * @param $email
     * @throws \RuntimeException
     */
    public static function sign_up($recaptcha_key, $customer_number, $name_first, $name_last, $zip_code, $password_1, $password_2, $pin_customer, $email) {


        //~~~~~~~~~~~~~~~~~~~~~~~//
        //  Validate parameters  //
        //~~~~~~~~~~~~~~~~~~~~~~~//

        if (!Recaptcha::recaptcha($recaptcha_key)) {
            throw new \RuntimeException('Invalid reCAPTCHA.');
        }
        if (empty($customer_number)) {
            throw new \RuntimeException('Invalid Customer Number.');
        }
        if (empty($customer_number)) {
            throw new \RuntimeException('Invalid Customer Number.');
        }
        if (empty($name_first)) {
            throw new \RuntimeException('Invalid First Name.');
        }
        if (empty($name_last)) {
            throw new \RuntimeException('Invalid Last Name.');
        }
        if (empty($zip_code)) {
            throw new \RuntimeException('Invalid Zip Code.');
        }
        if (empty($password_1)) {
            throw new \RuntimeException('Invalid Password.');
        }
        if (empty($password_2)) {
            throw new \RuntimeException('Invalid Password.');
        }
        if (!is_numeric($customer_number )) {
            throw new \RuntimeException('Invalid customer number, please use numeric');
        }
        if (Customer::pin($customer_number) !== $pin_customer) {
            throw new \RuntimeException('Invalid customer pin number.');
        }

        $query = db('cms')->table('customer')->where('number', '=', $customer_number);

        if ($zip_code !== $query->value('billAdr6')) {
            throw new \RuntimeException('Invalid billing zip code.');
        }

        if (!empty(self::check_email($email))) {
            throw new \RuntimeException('Email already in use.');
        }

        $minPasswordStrLen = min([strlen($password_1),strlen($password_2)]);
        if ($minPasswordStrLen < 7) {
            throw new \RuntimeException('Password is not long enough.');
        }

        if ($password_1 !== $password_2) {
            throw new \RuntimeException('Passwords do not match.');
        }

        //~~~~~~~~~~~~~~~//
        //  Create User  //
        //~~~~~~~~~~~~~~~//

        try {

            $user_id = db('cms')->table('user')
                ->insertGetId([
                    'email'=> $email,
                    'name_first'=> $name_first,
                    'name_last'=> $name_last,
                    'password'=> password_hash($password_1,PASSWORD_DEFAULT )
                ]);

            // tell quick that they are a customer, nothing more or less
            db('cms')->table('user_has_role')->insert([
                'user'=> $user_id,
                'role'=> 2
            ]);

            // then their customer number
            db('cms')->table('user_has_meta')->insert([
                'user'=> $user_id,
                'meta'=> 3,
                'value'=> $customer_number
            ]);

        } catch (QueryException $e) {
            throw new \RuntimeException('An error occurred trying to create user');
        }

    }

    /**
     * @param $email
     * @return array
     */
    private static function check_email($email) {

        return (array)db('cms')->table('user')
            ->where('email', 'like', $email)
            ->where('status','>=', 0)
            ->first();

    }

    /**
     * @param $recaptcha_key
     * @param $email
     * @throws \RuntimeException
     */
    public static function reset_password_request($recaptcha_key, $email) {

        if (empty($recaptcha_key) || empty($email)) {
            throw new \RuntimeException('Invalid reCAPTCHA and/or email address.');
        }

        if (!Recaptcha::recaptcha($recaptcha_key)) {
            throw new \RuntimeException('Invalid reCAPTCHA.');
        }

        if (!$user_info = self::check_email($email)) {
            throw new \RuntimeException('Invalid email address.');
        }

        try {

            $temporary_key = md5(random_int(555,99999).'jared'.date('z'));

            $values = [
                'id'=> null,
                'user'=> $user_info['id'],
                'code'=> $temporary_key,
                'created'=> date('Y-m-d H:i:s'),
                'used'=> null,
            ];

            if (db('cms')->table('user_reset')->insert($values)) {

                $data['link'] = env('APP_ORIGIN') . "/reset/?key=$temporary_key";

                email()
                    ->to([$user_info['email']])
                    ->send( new ResetUserPassword($data));

            } else {
                throw new \RuntimeException('Failed to request reset password.');
            }

        } catch (QueryException | \Exception | \ErrorException $e) {
            throw new \RuntimeException('Failed to reset password.');
        }

    }

    /**
     * @param $key
     * @param $password_1
     * @param $password_2
     */
    public static function set_password($key, $password_1, $password_2) {

        if (!$user_info = self::check_reset_password_key($key)) {
            throw new \RuntimeException('The key provided is Invalid/Expired');
        }

        if (empty($password_1) || empty($password_2)) {
            throw new \RuntimeException('Missing password.');
        }

        $minPasswordStrLen = min([strlen($password_1),strlen($password_2)]);
        if ($minPasswordStrLen < 7) {
            throw new \RuntimeException('Password is not long enough.');
        }

        if ($password_1 !== $password_2) {
            throw new \RuntimeException('Passwords do not match.');
        }

        try {

            $values = [
                'password'=> password_hash($password_1, PASSWORD_DEFAULT)
            ];

            $query = db('cms')->table('user')
                ->where('id','=',$user_info['id']);

            if ($query->update($values)) {

                db('cms')->table('user_reset')
                    ->where('code','=', $key)
                    ->update([
                        'used'=> db()->raw('now()')
                    ]);

            } else {
                throw new \RuntimeException('Failed to set new password');
            }


        } catch (QueryException $e) {
            throw new \RuntimeException('Failed to set new password');
        }

    }

    /** Verifies key is valid and returns user info
     * @param $key
     * @return array|bool
     */
    public static function check_reset_password_key($key) {

        if (!empty($key)) {

            $user_reset = (array)db('cms')->table('user_reset')
                ->select([
                    'user.*',
                    'user_reset.created'
                ])
                ->leftJoin('user', 'user_reset.user','=','user.id')
                ->where('code','=', $key)
                ->whereNull('used')
                ->first();

            if (!empty($user_reset)) {

                $current_date = new DateTime();
                $expire_date = new DateTime($user_reset['created']);
                $expire_date->modify('+30 minutes');

                if ($current_date < $expire_date) {
                    return $user_reset;
                }

            }

        }

        return false;

    }

}
