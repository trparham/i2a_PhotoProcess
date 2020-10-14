<?php

class ServiceBase {
    public function _construct() {
        
    }
    
    protected static function getDI(){
        return Phalcon\DI::getDefault();
    }
    
    protected static function cleansePost($post) {
        foreach(array_keys($post) as $key) {
            if(empty($post[$key])) {
                $post[$key] = null;
            }
        }
        return $post;
    }
    
    public static function currentTime() {
        return date('Y-m-d H:i:s');
    }
}
