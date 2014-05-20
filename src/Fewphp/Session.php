<?php

namespace fewphp;

class Session {

    public static function init() {
        session_start();
    }

    public static function write($key, $value) {
        if (stripos($key, '.')) {
            $write = array($key => $value);
            foreach ($write as $k => $val) {
                self::_overwrite($_SESSION, self::_simpleOp('insert', $_SESSION,  explode('.', $k), $val));
            }
            return true;
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
        if (stripos($key, '.')) {
            self::_overwrite($_SESSION, self::_simpleOp('remove', $_SESSION, explode('.', $key)));
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

    protected static function _overwrite(&$old, $new) {
        if (!empty($old)) {
            foreach ($old as $key => $var) {
                if (!isset($new[$key])) {
                    unset($old[$key]);
                }
            }
        }
        foreach ($new as $key => $var) {
            $old[$key] = $var;
        }
    }

    protected static function _simpleOp($op, $data, $path, $values = null) {
        $_list = & $data;

        $count = count($path);
        $last = $count - 1;

        foreach ($path as $i => $key) {
            if (is_numeric($key) && intval($key) > 0 || $key === '0') {
                $key = intval($key);
            }
            if ($op === 'insert') {
                if ($i === $last) {
                    $_list[$key] = $values;
                    return $data;
                }
                if (!isset($_list[$key])) {
                    $_list[$key] = array();
                }
                $_list = & $_list[$key];
                if (!is_array($_list)) {
                    $_list = array();
                }
            }
            elseif ($op === 'remove') {
                if ($i === $last) {
                    unset($_list[$key]);
                    return $data;
                }
                if (!isset($_list[$key])) {
                    return $data;
                }
                $_list = & $_list[$key];
            }
        }
    }

}

// end