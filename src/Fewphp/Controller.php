<?php

namespace fewphp;

class Controller {

    public $layout = 'default';
    public $viewVars = array();

    function __construct() {
        $this->view = new View();
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
        else {
            echo "No  {$name}";
            exit;
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

}
