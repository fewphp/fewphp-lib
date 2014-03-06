<?php

namespace fewphp;

use DebugBar\StandardDebugBar;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\Yui\JsCompressorFilter as JsYuiCompressorFilter;
use Assetic\Filter\Yui\CssCompressorFilter as CssYuiCompressorFilter;

class View {

    public $title = '';
    public $url = array();

    public function render($name) {
        $this->url = explode('/', trim($name));
    }

    public function fetch($name) {
        switch ($name) {
            case 'debug':
                $debugbar = new StandardDebugBar();
                $debugbarRenderer = $debugbar->getJavascriptRenderer();

                list($cssFiles, $jsFiles) = $debugbarRenderer->getAssets();

                if (!file_exists(JS . 'debug.js')) {
                    $js = new AssetCollection(array(
                        new FileAsset($jsFiles[0]),
                        new FileAsset($jsFiles[1]),
                        new FileAsset($jsFiles[2]),
                        new FileAsset($jsFiles[3]),
                        ), array(
                        new JsYuiCompressorFilter(VENDOR . 'nervo/yuicompressor/yuicompressor.jar'),
                    ));

                    $strJs = $js->dump();
                    file_put_contents(JS . 'debug.js', $strJs);
                }

                if (!file_exists(CSS . 'debug.css')) {

                    $css = new AssetCollection(array(
                        new FileAsset($cssFiles[0]),
                        new FileAsset($cssFiles[1]),
                        new FileAsset($cssFiles[2]),
                        new FileAsset($cssFiles[3]),
                        ), array(
                        new CssYuiCompressorFilter(VENDOR . 'nervo/yuicompressor/yuicompressor.jar'),
                    ));

                    $strCss = $css->dump();
                    file_put_contents(CSS . 'debug.css', $strCss);
                }
                
                
                if (!file_exists(IMAGES . 'debug.css')) {

                    $css = new AssetCollection(array(
                        new FileAsset($cssFiles[0]),
                        new FileAsset($cssFiles[1]),
                        new FileAsset($cssFiles[2]),
                        new FileAsset($cssFiles[3]),
                        ), array(
                        new CssYuiCompressorFilter(VENDOR . 'nervo/yuicompressor/yuicompressor.jar'),
                    ));

                    $strCss = $css->dump();
                    file_put_contents(CSS . 'debug.css', $strCss);
                }
                

                echo '</style><link rel="stylesheet" href="/css/debug.css" />'
                . '<script type="text/javascript" src="/js/debug.js"></script>';
                echo $debugbarRenderer->render();
                break;
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

// end