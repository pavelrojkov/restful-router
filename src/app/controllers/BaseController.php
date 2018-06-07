<?php

namespace src\app\controllers;

class BaseController
{
    /**
     * @param $function
     * @param $params
     */
    protected function output($function, $class, $params) {
        if ($params) {
            echo $function . " is called in " . $class . " with params: " . http_build_query ($params);
        } else {
            echo $function . " is called in " . $class . " with no params: ";
        }
    }
}