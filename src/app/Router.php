<?php

namespace src\app;

use Exception;
use src\app\Route;

class Router
{
    /**
     * Run request
     */
    public function run(){
        try {
            Route::resource('patients');
            Route::resource('patients.metrics');
        } catch (Exception $e) {
            echo "Error : {$e->getMessage()}";
        }
    }
}
