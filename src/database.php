<?php

class Database extends PDO
{
    /**
     * @var array
     */
    protected $options = array(
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false
    );

    /**
     * @param string $dsn
     * @param string $username
     * @param string $passwd
     * @param array $options
     */
    public function __construct($dsn, $username, $passwd, array $options = null)
    {
        if ($options !== null) {
            $this->options = array_merge($this->options, $options);
        }

        parent::__construct($dsn, $username, $passwd, $this->options);

        $name = $this->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($name === 'mysql') {
            if (version_compare(PHP_VERSION, '5.3.6', '<')) {
                $this->exec('SET NAMES utf8');
            } else {
                // use charset=utf8 in DSN
            }
        }
    }

    /**
     * @param string $sql
     * @param array $bind
     * @param int $style
     * @return array
     */
    public function fetch($sql, array $bind = null, $style = PDO::FETCH_ASSOC)
    {
        $stm = $this->prepare($sql);

        $stm->execute($bind);

        return $stm->fetch($style);
    }

    /**
     * @param string $sql
     * @param array $bind
     * @return mixed
     */
    public function fetchOne($sql, array $bind = null)
    {
        $result = $this->fetch($sql, $bind);

        return $result ? current($result) : false;
    }

    /**
     * @param string $sql
     * @param array $bind
     * @param int $style
     * @return array
     */
    public function fetchAll($sql, array $bind = null, $style = PDO::FETCH_ASSOC)
    {
        $stm = $this->prepare($sql);

        $stm->execute($bind);

        return $stm->fetchAll($style);
    }

    /**
     * @param string $sql
     * @param array $bind
     * @return array
     */
    public function fetchPairs($sql, array $bind = null)
    {
        $pairs = array();

        foreach ($this->fetchAll($sql, $bind, PDO::FETCH_NUM) as $row) {
            $pairs[$row[0]] = $row[1];
        }

        return $pairs;
    }

    /**
     * @param string $table
     * @param array $params
     * @return int
     */
    public function insert($table, array $params)
    {
        $prepared = array();

        foreach ($params as $key => $value) {
            $prepared[':' . $key] = $value;
        }

        $sql = "INSERT INTO {$table} (" . implode(',', array_keys($params)) . ") VALUES (" . implode(',', array_keys($prepared)) . ")";

        $stm = $this->prepare($sql);

        $stm->execute($prepared);

        return $this->lastInsertId();
    }

    /**
     * @param string $table
     * @param array $params
     * @param string|array $where
     * @return number
     */
    public function update($table, array $params, $where = null)
    {
        $prepared = array();
        $set = array();

        foreach ($params as $key => $value) {
            $prepared[] = $value;
            $set[] = $key . ' = ?';
        }

        $preparedWhere = array();

        if ($where === null || empty($where)) {
            $where = array(
                1
            );
        } else
            if (!is_array($where)) {
                $where = array(
                    $where
                );
            }

        foreach ($where as $key => $value) {
            if (is_int($key)) {
                $preparedWhere[] = $value;
            } else {
                $prepared[] = $value;
                $preparedWhere[] = $key . ' = ?';
            }
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $preparedWhere) . "";

        $stm = $this->prepare($sql);

        $stm->execute($prepared);

        return $stm->rowCount();
    }

    /**
     * @param string $table
     * @param string|array $where
     * @return number
     */
    public function delete($table, $where = null)
    {
        $prepared = array();
        $preparedWhere = array();

        if ($where === null || empty($where)) {
            $where = array(
                1
            );
        } else
            if (! is_array($where)) {
                $where = array(
                    $where
                );
            }

        foreach ($where as $key => $value) {
            if (is_int($key)) {
                $preparedWhere[] = $value;
            } else {
                $prepared[] = $value;
                $preparedWhere[] = $key . ' = ?';
            }
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $preparedWhere) . "";

        $stm = $this->prepare($sql);

        $stm->execute($prepared);

        return $stm->rowCount();
    }
}