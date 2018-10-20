<?php

require 'config.php';

// Also spl_autoload_register (Take a look at it if you like)
// function __autoload($class) {
//     require LIBS . $class .".php";
// }
spl_autoload_register(function ($class) {
    include LIBS.$class.'.php';
});

// Load the Bootstrap!
$bootstrap = new Bootstrap();

$bootstrap->init();
