<?php

namespace fewphp\Model;

use fewphp\Database;

class Model {

    public $useTable = null;
    public $table = null;
    public $id = null;
    public $primaryKey = 'id';
    public $order = null;
    public $alias = null;
    public $findMethods = array(
        'all' => true, 'first' => true, 'count' => true,
        'neighbors' => true, 'list' => true, 'threaded' => true
    );

    public function __construct() {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $className = get_class($this);
        if (empty($this->useTable)) {
            $this->useTable = strtolower($className);
        }

        if (empty($this->alias)) {
            $this->alias = $className;
        }

        $this->table = $this->useTable;
    }

    public function find($type = 'first', $query = array()) {
        $this->findQueryType = $type;
        $this->id = $this->getID();

        $query = $this->buildQuery($type, $query);
        if ($query === null) {
            return null;
        }
        return $this->_readDataSource($type, $query);
    }

    public function query($sql) {
        return $this->db->fetchAll($sql);
    }

    public function save() {

    }

    public function saveAll() {

    }

    public function update() {

    }

    public function updateAll() {

    }

    public function delete() {

    }

    public function deleteAll() {

    }

    public function getID($list = 0) {
        if (empty($this->id) || (is_array($this->id) && isset($this->id[0]) && empty($this->id[0]))) {
            return false;
        }

        if (!is_array($this->id)) {
            return $this->id;
        }

        if (isset($this->id[$list]) && !empty($this->id[$list])) {
            return $this->id[$list];
        }

        if (isset($this->id[$list])) {
            return false;
        }

        return current($this->id);
    }

    public function buildQuery($type = 'first', $query = array()) {
        $query = array_merge(array(
            'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
            'offset' => null, 'order' => null, 'page' => 1, 'group' => null, 'callbacks' => true,
                ), (array) $query
        );

        if ($this->findMethods[$type] === true) {
            $query = $this->{'_find' . ucfirst($type)}('before', $query);
        }

        if (!is_numeric($query['page']) || intval($query['page']) < 1) {
            $query['page'] = 1;
        }

        if ($query['page'] > 1 && !empty($query['limit'])) {
            $query['offset'] = ($query['page'] - 1) * $query['limit'];
        }

        if ($query['order'] === null && $this->order !== null) {
            $query ['order'] = $this->order;
        }

        $query['order'] = array($query['order']);

        return $query;
    }

    protected function _readDataSource($type, $query) {
        $results = $this->db->read($this, $query);

        $this->findQueryType = null;
        if ($this->findMethods[$type] === true) {
            return $this->{'_find' . ucfirst($type)}('after', $query, $results);
        }
    }

    protected function _findFirst($state, $query, $results = array()) {
        if ($state === 'before') {
            $query['limit'] = 1;
            return $query;
        }

        if (empty($results[0])) {
            return array();
        }
        return $results[0];
    }

    protected function _findAll($state, $query, $results = array()) {
        if ($state === 'before') {
            return $query;
        }
        return $results;
    }

    protected function _findCount($state, $query, $results = array()) {
        if ($state === 'before') {
            $query['fields'] = array('count(*)');
            return $query;
        }
        return $results[0]['count(*)'];
    }

}

// end
