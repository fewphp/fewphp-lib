<?php

namespace fewphp;

use Kint;

class Router {

    private $_url = null;
    private $_controller = null;
    private $_controllerPath = CONTROLLER;
    private $_errorFile = 'error.php';
    private $_defaultFile = 'Index';

    /**
     * 入口
     *
     */
    public function init() {
        // 获取url
        $this->_getUrl();

        $this->_loadExistingController();
        $this->_callControllerAction();
        $this->_loadView();
    }

    /**
     * 拆分url获取参数
     */
    private function _getUrl() {
        $url = filter_var(trim($_SERVER['REQUEST_URI'], DS), FILTER_SANITIZE_URL);
        $this->_url = explode(DS, $url);
    }

    /**
     * 加载控制器
     *
     * @return boolean|string
     */
    private function _loadExistingController() {

        // 没有参数加载默认控制器
        if (empty($this->_url[0])) {
            $this->_url[0] = $this->_defaultFile;
        }
        else {
            $this->_url[0] = ucfirst($this->_url[0]);
        }

        $file = APP . $this->_controllerPath . $this->_url[0] . 'Controller.php';
        if (file_exists($file)) {
            require $file;
            $controllerName = $this->_url[0] . 'Controller';
            $this->_controller = new $controllerName;
            $this->_controller->loadModel($this->_url[0]);
        }
        else {
            $this->_error();
            return false;
        }
    }

    /**
     * 带参数调用方法
     *
     *  http://localhost/controller/method/(param)/(param)/(param)
     *  url[0] = Controller
     *  url[1] = Method
     *  url[2] = Param
     *  url[3] = Param
     *  url[4] = Param
     */
    private function _callControllerAction() {
        $length = count($this->_url);
        if ($length > 1) {
            if (!method_exists($this->_controller, $this->_url[1])) {
                $this->_error();
            }
        }

        switch ($length) {
            case 5:
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3], $this->_url[4]);
                break;

            case 4:
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3]);
                break;

            case 3:
                $this->_controller->{$this->_url[1]}($this->_url[2]);
                break;

            case 2:
                $this->_controller->{$this->_url[1]}();
                break;

            default:
                $this->_controller->index();
                break;
        }
    }

    /**
     * 载入view
     */
    private function _loadView() {
        $this->view = new View();
        $this->view->url = $this->_url;
        foreach ($this->_controller->viewVars as $k => $v) {
            $this->view->$k = $v;
        }
        if ($this->_controller->layout === false) {
            $this->view->fetch('content');
        }
        else {
            $this->view->layout($this->_controller->layout);
        }
    }

    /**
     * 显示错误页面
     *
     * @return boolean
     */
    private function _error() {
        Kint::trace();
    }

}

// end