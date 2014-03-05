<?php

namespace fewphp;

class Config {

    public static function read($path) {
        $array = explode('.', $path);
        $config = include APP . 'Config' . DS . $array[0] . '.php';
        unset($array[0]);
        if (!empty($array)) {
            foreach ($array as $v) {
                $config = $config[$v];
            }
        }
        return $config;
    }

}

// end