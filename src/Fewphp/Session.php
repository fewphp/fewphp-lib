<?php

namespace fewphp;

class Session {

    public static function init() {
        @session_start();
    }

    public static function write($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function read($key) {
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
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
        if (isset($_SESSION[$key])) {
            return true;
        }
        return false;
    }
    
    public function setFlash($message, $element = 'default', $params = array(), $key = 'flash') {
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
                $out = $this->_View->element($flash['element'], $tmpVars, $options);
            }
            self::delete('Message.' . $key);
        }
        return $out;
    }

}
// end