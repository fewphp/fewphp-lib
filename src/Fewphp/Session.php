<?php

namespace fewphp;

class Session {

    public static function init() {
        session_start();
    }

    public static function write($key, $value) {
        if (stripos($key, '.')) {
            $array = explode('.', $key);
//            $array = array_flip($array);
            $tmp = array();
            foreach ($array as $k => $v) {
//                $tmp[$v] =  
            }
            var_dump($array);
        }
        else {
            $_SESSION[$key] = $value;
        }
    }

    public static function read($key) {
        if (stripos($key, '.')) {
            $array = explode('.', $key);
            $tmp = $_SESSION;
            foreach ($array as $v) {
                $tmp = $tmp[$v];
            }
            return $tmp;
        }
        elseif (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    public static function delete($key = null) {
        if ($key) {
            unset($_SESSION[$key]);
        }
        else {
            session_destroy();
        }
    }

    public static function check($key) {
        if (stripos($key, '.')) {
            $array = explode('.', $key);
            $tmp = $_SESSION;
            foreach ($array as $v) {
                $tmp = $tmp[$v];
            }
            if (!empty($tmp)) {
                return true;
            }
        }
        if (isset($_SESSION[$key])) {
            return true;
        }
        return false;
    }
    
    
    public function setFlash($message, $params = array(), $key = 'flash', $element = 'default') {
		self::write('Message.' . $key, compact('message', 'element', 'params'));
	}

    public function flash($key = 'flash', $attrs = array()) {
        $out = false;
        if (self::check('Message.' . $key)) {
            $flash = self::read('Message.' . $key);
            $message = $flash['message'];
            unset($flash['message']);

            if (!empty($attrs)) {
                $flash = array_merge($flash, $attrs);
            }

            if ($flash['element'] === 'default') {
                $class = 'message';
                if (!empty($flash['params']['class'])) {
                    $class = $flash['params']['class'];
                }
                $out = '<div id="' . $key . 'Message" class="' . $class . '">' . $message . '</div>';
            }
            elseif (!$flash['element']) {
                $out = $message;
            }
            else {
                $options = array();
                $tmpVars = $flash['params'];
                $tmpVars['message'] = $message;
                $view = new View();
                $out = $view->element($flash['element'], $tmpVars, $options);
            }
            self::delete('Message.' . $key);
        }
        return $out;
    }

}
// end