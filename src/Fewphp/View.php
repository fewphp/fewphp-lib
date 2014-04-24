<?php

namespace fewphp;

class View {

    public $title = '';
    public $url = array();
    public $sqlLog = array();

    public function render($name) {
        $this->url = explode('/', trim($name));
    }

    /**
     *
     * @param type $name
     * @param type $a
     * @return type
     */
    public function fetch($name, $param = array()) {
        switch ($name) {
            case 'sql_dump':
                $sql_dump = '';
                if (!empty($this->sqlLog)) {
                    foreach ($this->sqlLog as $nr => $sql) {
                        $sql_dump .= "<tr><td>" . $nr . "</td><td> " . $sql . " </td></tr>";
                    }
                }
                return '<table class="sql_dump"><tr><th>Nr</th><th>Query</th></tr>' . $sql_dump . '</table>';
                break;
            case 'flash':
                return Session::flash('flash', $param);
                break;
            default:
                $this->url[1] = isset($this->url[1]) ? strtolower($this->url[1]) : 'index';
                include APP . VIEW . $this->url[0] . DS . $this->url[1] . '.php';
                break;
        }
    }

    /**
     * 公共布局载入
     * @param type $layout
     */
    public function layout($layout) {
        include APP . VIEW . 'layout' . DS . $layout . '.php';
    }

    /**
     * 记录sql
     * @param type $sql
     */
    public function log($sql) {
        $this->sqlLog[] = $sql;
    }

    public function element($element, $param, $options) {
        foreach ($param as $k => $v) {
            $this->$k = $v;
        }

        return $this->layout($element);
    }

}

// end