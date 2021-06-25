<?php

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

/**
 * @param null|string $db
 *
 * @return DatabaseManager|Connection
 */
function db($db=null){
    if($db===null){
        return app('db');
    }
    return db()->connection($db);
}

/**
 * @return App\CMS
 */
function cms() {
    return app('cms');
}


if (!function_exists('auth')) {

    /**
     * @return App\Extensions\Session\Auth
     */
    function auth(){
        return app('auth');
    }
}

/**
 * @return  Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\FilesystemAdapter
 */
function storage($disk=null){
    /** @var \Illuminate\Filesystem\FilesystemManager $filesystem */
    $filesystem = app('filesystem');
    if($disk===null){
        return $filesystem;
    }
    return $filesystem->disk($disk);
}


///**
// * @return App\Extensions\Session\Auth
// */
//function request(){
//    return app('auth');
//}
//
///**
// * @return Illuminate\Cache\Repository
// */
//function cache(){
//    return app('cache');
//}
//
///**
// * @return Illuminate\Filesystem\FilesystemManager
// *
// */
//function storage(){
//    return app('filesystem');
//}
//
///**
// * @return Illuminate\Mail\Mailer
// *
// */
//function email(){
//    return app('mailer');
//}
