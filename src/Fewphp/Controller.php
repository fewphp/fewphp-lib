<?php

namespace fewphp;

class Controller {

    public $layout = 'default';
    public $viewVars = array();

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

        if ($url !== null) {
            header('Location ' . $url);
        }
    }

}

// end