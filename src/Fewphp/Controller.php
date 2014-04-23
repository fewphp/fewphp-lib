<?php

namespace fewphp;

class Controller {

    public $layout = 'default';
    public $viewVars = array();
    
    public function __construct() {
        $this->controller  = substr(get_class($this), 0, -10);
    }

    /**
     *
     * @param string $name Name of the model
     * @param string $path Location of the models
     */
    public function loadModel($name) {
        $path = APP . MODEL . $name . '.php';
        if (file_exists($path)) {
            $this->{$name} = new $name();
        }
    }

    public function set($one, $two = null) {
        if (is_array($one)) {
            if (is_array($two)) {
                $data = array_combine($one, $two);
            }
            else {
                $data = $one;
            }
        }
        else {
            $data = array($one => $two);
        }
        $this->viewVars = $data + $this->viewVars;
    }
    
    public function redirect($url) {
        if (!$this->is_url($url)) {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') 
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) 
                ? 'https://' : 'http://';
            $url = $http_type . $_SERVER['HTTP_HOST'] . '/' . $url;
        }
        header('Location: ' . $url);
        exit;
    }
    
    
    public function is_url($str) {
        return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $str);
    }

}

// end