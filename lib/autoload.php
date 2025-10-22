<?php

// load dependencies
include_once __DIR__ . "/mc/http.php";
include_once __DIR__ . "/mc/logger.php";

// autoload alpaca classes
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        include_once $file;
    }
});
