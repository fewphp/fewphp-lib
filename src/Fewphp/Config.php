<?php

namespace fewphp;

class Config {

    /**
     * 读取配置文件
     * @param type $path
     * @return type
     */
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