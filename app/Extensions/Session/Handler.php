<?php

namespace App\Extensions\Session;

use Illuminate\Database\DatabaseManager;
use SessionHandlerInterface;

class Handler implements SessionHandlerInterface {

    /**
     * Basically the constructor
     *
     * Using DatabaseManager allows the connection to be shared between the session handler and the rest of the lumen project
     *
     * @param string $path
     * @param string $name
     *
     * @return bool
     */
    public function open($path, $name){
        if(db('cms')){
            return true;
        }
        return false;
    }

    public function close(){return true;}

    public function read($id){
        return db('cms')->table('user_session')->select(['data'])->where('session', $id)->value('data') ?? '';
    }

    public function write($id, $data){
        if(db('cms')->insert('INSERT INTO user_session(`session`, `data`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `data`=?',[
            $id, $data, $data
        ])){
            return true;
        }
        throw new \Exception('Unable to write session.');
    }

    public function destroy($id){
        db('cms')->table('user_session')->where('session','=', $id)->delete();
        return true;
    }

    public function gc($maxLife){
        // sorry $maxLife, I don't want you...
        $expire = time() - 21600; // expires after 6 hours
        return db('cms')->table('user_session')->where('updated_at', '<', date('Y-m-d H:i:s', $expire))->delete();
    }

}
