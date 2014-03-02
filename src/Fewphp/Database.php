<?php

namespace fewphp;

use PDO;

class Database extends PDO {

    public function __construct($DB_TYPE, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS) {
        parent::__construct($DB_TYPE . ':host=' . $DB_HOST . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASS);
    }

    /**
     * select
     * @param string $sql An SQL string
     * @param array $array Paramters to bind
     * @param constant $fetchMode A PDO Fetch mode
     * @return mixed
     */
    public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC) {
        $sth = $this->prepare($sql);
        foreach ($array as $key => $value) {
            $sth->bindValue("$key", $value);
        }

        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }

    /**
     * insert
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     */
    public function insert($table, $data) {
        ksort($data);

        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));

        $sth = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

    /**
     * update
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     * @param string $where the WHERE query part
     */
    public function update($table, $data, $where) {
        ksort($data);

        $fieldDetails = NULL;
        foreach ($data as $key => $value) {
            $fieldDetails .= "`$key`=:$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

    /**
     * delete
     *
     * @param string $table
     * @param string $where
     * @param integer $limit
     * @return integer Affected Rows
     */
    public function delete($table, $where, $limit = 1) {
        return $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
    }

    public function read($model, $queryData) {
        $null = null;
        $query = $this->generateAssociationQuery();
        $resultSet = $this->fetchAll($query);
        return $resultSet;
    }

    public function generateAssociationQuery() {
        return null;
    }

    public function fetchAll($sql, $params = array(), $options = array()) {
//        $result = $this->execute($sql, array(), $params);
//        if ($result) {
//            return $result;
//        }
        return false;
    }

    public function buildStatement($query, $model) {
        $query = array_merge($this->_queryDefaults, $query);
        if (!empty($query['joins'])) {
            $count = count($query['joins']);
            for ($i = 0; $i < $count; $i++) {
                if (is_array($query['joins'][$i])) {
                    $query['joins'][$i] = $this->buildJoinStatement($query['joins'][$i]);
                }
            }
        }
        return $this->renderStatement('select', array(
                    'conditions' => $this->conditions($query['conditions'], true, true, $model),
                    'fields' => implode(', ', $query['fields']),
                    'table' => $query['table'],
                    'alias' => $this->alias . $this->name($query['alias']),
                    'order' => $this->order($query['order'], 'ASC', $model),
                    'limit' => $this->limit($query['limit'], $query['offset']),
                    'joins' => implode(' ', $query['joins']),
                    'group' => $this->group($query['group'], $model)
        ));
    }

    public function renderStatement($type, $data) {
        extract($data);
        $aliases = null;

        switch (strtolower($type)) {
            case 'select':
                return trim("SELECT {$fields} FROM {$table} {$alias} {$joins} {$conditions} {$group} {$order} {$limit}");
            case 'create':
                return "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
            case 'update':
                if (!empty($alias)) {
                    $aliases = "{$this->alias}{$alias} {$joins} ";
                }
                return trim("UPDATE {$table} {$aliases}SET {$fields} {$conditions}");
            case 'delete':
                if (!empty($alias)) {
                    $aliases = "{$this->alias}{$alias} {$joins} ";
                }
                return trim("DELETE {$alias} FROM {$table} {$aliases}{$conditions}");
            case 'schema':
                foreach (array('columns', 'indexes', 'tableParameters') as $var) {
                    if (is_array(${$var})) {
                        ${$var} = "\t" . implode(",\n\t", array_filter(${$var}));
                    }
                    else {
                        ${$var} = '';
                    }
                }
                if (trim($indexes) !== '') {
                    $columns .= ',';
                }
                return "CREATE TABLE {$table} (\n{$columns}{$indexes}) {$tableParameters};";
            case 'alter':
                return;
        }
    }

}


// end
