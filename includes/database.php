<?php

    class DB {

        var $objs = array();
        var $numQueries = 0;
        var $lastError = null;
        static $instance = null;

        function DB($conf) {
            $this->connect($conf);
        }

        static function getInstance($conf = null) {
            if (self::$instance == null) {
                self::$instance = new DB($conf == null ? $GLOBALS['config']['database'] : $conf);
            }
            return self::$instance;
        }

        function connect($conf) {
            $this->conn = new PDO($conf['dsn'], $conf['user'], $conf['pass']);
        }

        function query($sql, $params) {
            $this->numQueries++;
            $prep = $this->conn->prepare($sql);
            if ($prep->execute($params)) {
                return $prep;
            } else {
                $error = $prep->errorInfo();
                $this->lastError = $error[2];
                return false;
            }
        }

        function rsToArray($rs) {
            $arr = $rs->fetchAll(PDO::FETCH_ASSOC);
            $res = array();
            for ($i = 0; $i < count($arr); $i++) {
                $res[] = array_change_key_case($arr[$i], CASE_LOWER);
            }
            return $res;
        }

        function queryA($sql, $params) {
            $rs = $this->query($sql, $params);
            if ($rs) {
                return $this->rsToArray($rs);
            } else {
                return false;
            }
        }

        function emptyRow($table) {
            $row = array();
            $r = $this->conn->query('select * from ' . $table . ' limit 0');
            for ($i = 0; $i < $r->columnCount(); $i++) {
                $col = $r->getColumnMeta($i);
                $row[$col['name']] = '';
            }

            return $row;
        }

        static function getObjectsArray($table, $where = '', $order = '') {
            if (!empty($where)) {
                $where = ' WHERE ' . $where;
            }
            if (!empty($order)) {
                $order = ' ORDER BY ' . $order;
            }
            $query = 'SELECT * FROM ' . $table . $where . $order;

            return DB::getInstance()->QueryA($query, array());
        }

        static function getObject($table, $id = 0, $idCol = 'id') {
            for ($i = 0; $i < count(DB::getInstance()->objs); $i++) {
                if ((DB::getInstance()->objs[$i]->row[$idCol] == $id) && (DB::getInstance()->objs[$i]->table == $table)) {
                    return DB::getInstance()->objs[$i];
                }
            }

            DB::getInstance()->objs[] = new DBObject($table, $id, $idCol);

            return DB::getInstance()->objs[count(DB::getInstance()->objs) - 1];
        }

        static function getObjects($table, $where = '', $order = '') {
            $rowobjs = DB::getInstance()->getObjectsArray($table, $where, $order);

            if ($rowobjs) {
                $objs = array();
                foreach ($rowobjs as $row) {
                    $newObj = new DBObject($table, $row);
                    $objs[] = $newObj;
                    DB::getInstance()->objs[] = $newObj;
                }
                return $objs;
            } else {
                return false;
            }
        }

    }

    class DBObject {

        var $row = array();
        var $table = 'undefined';

        function DBObject($table, $id, $idCol = 'id') {
            if (is_array($id)) {
                $this->row = $id;
            } else {
                if ($id == '0') {
                    $this->row = DB::getInstance()->emptyRow($table);
                } else {
                    $query = 'SELECT * FROM ' . $table . ' WHERE ' . $idCol . ' = ?';

                    $tmp = DB::getInstance()->queryA($query, array($id));

                    if ($tmp) {
                        $this->row = $tmp[0];
                    } else {
                        $this->row = DB::getInstance()->emptyRow($table);
                    }
                }
            }

            $this->table = $table;
        }

        function save() {
            if ($this->row['id'] > 0) {
                $query = $this->updateRow();
            } else {
                $query = $this->insertRow();
            }

            $result = DB::getInstance()->query($query['q'], $query['values']);

            if ($result && $this->row['id'] < 1) {
                $this->row['id'] = DB::getInstance()->conn->lastInsertId();
            }

            return $result;
        }

        function insertRow() {
            $fields = array();
            $values = array();
            $subst = array();
            foreach ($this->row as $field => $value) {
                $fields[] = $field;
                if (strtolower($field) == 'id') {
                    $values[] = 0;
                } else {
                    $values[] = $value;
                }
                $subst[] = '?';
            }

            return array('q' => 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $subst) . ')',
                'values' => $values);
        }

        function updateRow() {
            $fields = array();
            $values = array();
            foreach ($this->row as $field => $value) {
                if (strtolower($field) != 'id') {
                    $fields[] = $field . " = ?";
                    $values[] = $value;
                }
            }
            $values[] = $this->row['id'];
            return array('q' => 'UPDATE ' . $this->table . ' SET ' . implode(',', $fields) . ' WHERE id = ?',
                'values' => $values);
        }

        function delete() {
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
            return DB::getInstance()->query($query, array($this->row['id']));
        }

        /**
         * Note that although we use "safe" parametised queries, it is the 
         * responsibility of the caller to sanitise data before saving if
         * required.
         */
        function fromArray($array) {
            foreach (array_keys($this->row) as $field) {
                if (in_array($field, array_keys($array))) {
                    $this->row[$field] = $array[$field];
                }
            }
        }

        function __get($var) {
            if (in_array($var, array_keys($this->row))) {
                return stripslashes($this->row[$var]);
            }
        }

        function __set($var, $value) {
            if (in_array($var, array_keys($this->row))) {
                $this->row[$var] = $value;
            } else {
                $this->$var = $value;
            }
        }

        function loadRelations($tables = null) {
            $fields = array_keys($this->row);
            foreach ($fields as $k) {
                $table = substr($k, 0, -3);
                if (substr($k, -3) == '_id' && ($tables == null || in_array($table, $tables))) {
                    if ($this->row[$k] != null && $this->row[$k] > 0) {
                        $table = substr($k, 0, -3);
                        $this->$table = DB::getObject($table, $this->row[$k]);
                    } else {
                        $this->$table = null;
                    }
                }
            }
        }

        function __call($func, $params) {
            if (strpos($func, "get_") === 0) {
                $table = substr($func, 4);

                // one-to-one: get_table()
                if (isset($this->row[$table . '_id'])) {
                    return DB::getInstance()->getObject($table, $this->row[$table . '_id']);
                }
                // many-to-many using link table: get_linked($table, $linkTable, $order)
                else if (substr($table, -6) == 'linked') {
                    $table = $params[0];
                    $linkTable = $params[1];

                    if (!empty($params[2])) {
                        $order = ' ORDER BY ' . $order;
                    }
                    $query = 'SELECT ' . $table . '.* FROM ' . $linkTable . ' WHERE ' . $this->table . '_id = ' . $this->row['id'] . $order;
                    $links = DB::getInstance()->queryA($query, array());

                    $linked = array();
                    for ($i = 0; $i < count($links); $i++) {
                        $linked[] = DB::getInstance()->getObject($otherTable, $links[$i]['lnk']);
                    }
                }
                // one-to-many without link: get_table_list($order)
                else if (substr($table, -5) == '_list') {
                    if (!isset($params[0])) {
                        $params[0] = '';
                    }
                    if (isset($params[1]) && (trim($params[1]) != '')) {
                        $params[1] = ' and ' . $params[1];
                    } else {
                        $params[1] = '';
                    }
                    return DB::getInstance()->getObjects(substr($table, 0, -5), $this->table . '_id = ' . $this->row['id'] . $params[1], $params[0]);
                }
            }

            throw new Exception('Call to unknown function ' . $func . ' on ' . get_class($this));
        }

    }

?>