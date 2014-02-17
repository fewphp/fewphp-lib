<?php

namespace fewphp;

class View {

    public $title = '';
    public $url = array();

    public function render($name) {
        $this->url = explode('/', trim($name));
    }

    public function fetch($name) {
        switch ($name) {
//            case $value:
//
//                break;
            default:
                $this->url[1] = isset($this->url[1]) ? strtolower($this->url[1]) : 'index';
                include APP . VIEW . $this->url[0] . DS . $this->url[1] . '.php';
                break;
        }
    }

    public function layout($layout) {
        require APP . VIEW . 'layout' . DS . $layout . '.php';
    }

}
