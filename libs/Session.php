<?php
class Session
{
    
    public static function init()
    {
        @session_start();       
    }
    
    public static function set($key, $value)        
    {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key)        
    {
        return isset($_SESSION[ $key ]) ? $_SESSION[ $key ] : false;
    }

    public static function clear($key) 
    {  
        unset($_SESSION[$key]);
    }
    
    public static function destroy()     
    {
        //unset($_SESSION);
        session_destroy();
    }
    
}