<?php

namespace src\app;

use Exception;

class Route
{
    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_PATCH   = 'PATCH';
    const METHOD_DELETE  = 'DELETE';

    /**
     * Request method
     * @var string
     */
    private $method;

    /**
     * Array containing parameters passed through request URL
     * @var array
     */
    private $parameters = array();

    /**
     * @param $params
     */
    public function __construct($params)
    {
        $this->parameters = $params;
    }

    public static function resource ($path) {
        if ( empty($path)) {
            throw new Exception('Missing router path');
        }

        //check that path match current request
        $params = self::getParams();
        if (implode('.', array_keys($params)) == $path) {
            self::dispatch($params);
        }
    }

    /**
     * Get currect request
     */
    public static function getParams()
    {
        $currentDir = dirname($_SERVER['SCRIPT_NAME']);

        $requestUri = $_SERVER['REQUEST_URI'];
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        //detect script directory, if any
        if ('/' !== $currentDir) {
            $requestUri = str_replace($currentDir, '', $requestUri);
        }

        $request = explode('/', $requestUri);
        array_shift ($request);
        $params =[];
        while (count($request)) {
            if(count($request) >= 2) {
                list($key,$value) = array_splice($request, 0, 2);
                $params[$key] = $value;
            } else {
                $params[$request[0]] = '';
                array_shift ($request);
            }
        }
        return $params;
    }

    public static function dispatch($params)
    {
        $requestMethod = (
            isset($_POST['_method'])
            && ($_method = strtoupper($_POST['_method']))
            && in_array($_method, array(self::METHOD_PUT, self::METHOD_DELETE), true)
        ) ? $_method : $_SERVER['REQUEST_METHOD'];

        //build class and method to call
        $class = '\src\app\controllers\\'.implode('',array_keys($params)) . 'Controller';
        if (class_exists($class)) {
            $instance = new $class;

            end($params);
            $lastParam = key($params);
            if (empty($params[$lastParam]) || !$requestMethod) {
                call_user_func_array(array($instance, 'index'), [$params]);
            } else {
                call_user_func_array(array($instance, $requestMethod), [$params]);
            }
        } else {
            throw new Exception('Missing controller class ' . $class);
        }
    }
}
