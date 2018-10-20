<?php

class Cookie
{
    private $path = "/";

    public static function set($key, $value, $expire='')
    {
        if( empty($expire) ){
            $expire = time() + 3600;
        }

        setcookie($key,$value,$expire,"/");
    }
    
    public static function get($key)
    {
        if (isset($_COOKIE[$key])) return $_COOKIE[$key];
        else return false;
    }
    
    public static function clear($key)
    {
        setcookie($key,null,-1, "/");
        unset ($_COOKIE[$key]);
    }
    
}