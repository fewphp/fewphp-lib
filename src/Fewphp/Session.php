<?php

namespace fewphp;

class Session {

    public static function init() {
        @session_start();
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
    }

    public static function destroy() {
        //unset($_SESSION);
        session_destroy();
    }

    public static function check() {

    }

    public function flash($key = 'flash', $attrs = array()) {
        $out = false;

        if (CakeSession::check('Message.' . $key)) {
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
                if (isset($flash['params']['plugin'])) {
                    $options['plugin'] = $flash['params']['plugin'];
                }
                $tmpVars = $flash['params'];
                $tmpVars['message'] = $message;
                $out = $this->_View->element($flash['element'], $tmpVars, $options);
            }
            self::delete('Message.' . $key);
        }
        return $out;
    }

}
