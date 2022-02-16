<?php

namespace App\System\Database;

use App\Config\Database;
use PDO;

class SQLite
{
    public $pdo;

    public $query;

    public function __construct()
    {
        $this->pdo = new PDO("sqlite:" . Database::PATH_TO_SQLITE_FILE, "", "", array(
            PDO::ATTR_PERSISTENT => true
        ));

        if ($this->pdo == null) {
            throw new \Exception("Unable to connect to database", 500);
        }
    }

    public function execute($command)
    {
        return $this->pdo->exec($command);
    }

    public function insert($table, $data = [])
    {
        $preparedData = [];
        array_map(function ($key, $value) use (&$preparedData) {
            $preparedData[":{$key}"] = $value;
        }, array_keys($data), $data);

        $sql = "INSERT INTO {$table}(";
        $sql .= implode(",", array_keys($data));
        $sql .= ") VALUES('";
        $sql .= implode("','", array_values($preparedData));
        $sql .= "')";

        $this->pdo->query($sql);

        if ($this->error()->code) {
            return $this->error()->message;
        }

        return true;
    }

    public function lastId()
    {
        return $this->pdo->lastInsertId();
    }

    public function get($table, $whereCondition = [], $fields = ["*"], $order = [])
    {
        $preparedWhere = [];
        $preparedWhereValues = [];
        array_map(function ($key, $value) use (&$preparedWhere, &$preparedWhereValues, $whereCondition) {
            $re = '/^[=!><]*$/';

            $condition = isset($whereCondition[$key]) && is_array($whereCondition[$key]) ? $whereCondition[$key] : $whereCondition;

            $field = !preg_match($re, $condition[0]) ? $condition[0] : $condition;
            $operand = preg_match($re, $condition[1]) ? $condition[1] : $condition[2];
            $value = (!$operand && !preg_match($re, $condition[2])) ? $condition[1] : $condition[2];

            $preparedWhere["{$key}"] = "{$field} {$operand} '{$value}'";
            $preparedWhereValues[":{$field}"] = $value;
        }, array_keys($whereCondition), $whereCondition);

        $sql = "SELECT ";
        $sql .= sizeof($fields) > 0 ? implode(",", $fields) : "*";
        $sql .= " FROM {$table} ";

        if (sizeof($whereCondition) > 0) {
            $sql .= " WHERE ";
            $sql .= implode(" ", $preparedWhere);
        }

        if (sizeof($order) > 0) {
            $sql .= " ORDER BY  ";
            $sql .= $order['order']['field'];
            $sql .= " ";
            $sql .= $order['order']['dir'];
        }

        $query = $this->query($sql);

        return $this->fetchAll();
    }

    public function show($table, $whereCondition = [], $fields = ["*"], $order = [])
    {
        $preparedWhere = [];
        $preparedWhereValues = [];
        array_map(function ($key, $value) use (&$preparedWhere, &$preparedWhereValues, $whereCondition) {
            $re = '/^[=!><]*$/';

            $condition = isset($whereCondition[$key]) && is_array($whereCondition[$key]) ? $whereCondition[$key] : $whereCondition;

            $field = !preg_match($re, $condition[0]) ? $condition[0] : $condition;
            $operand = preg_match($re, $condition[1]) ? $condition[1] : $condition[2];
            $value = (!$operand && !preg_match($re, $condition[2])) ? $condition[1] : $condition[2];

            $preparedWhere["{$key}"] = "{$field} {$operand} {$value}";
            $preparedWhereValues[":{$field}"] = $value;
        }, array_keys($whereCondition), $whereCondition);


        $sql = "SELECT ";
        $sql .= implode(",", $fields);
        $sql .= " FROM {$table} ";

        if (sizeof($whereCondition) > 0) {
            $sql .= " WHERE ";
            $sql .= implode(" ", $preparedWhere);
        }

        $query = $this->query($sql);

        return $this->fetch();
    }

    public function update($table, $data = [], $whereCondition = [])
    {
        $preparedDataKeys = [];
        $preparedDataValues = [];
        array_map(function ($key, $value) use (&$preparedDataKeys, &$preparedDataValues) {
            $preparedData[":{$key}"] = $value;
            $preparedDataKeys[] = "$key=:{$key}";
            $preparedDataValues[] = "$key='{$value}'";
        }, array_keys($data), $data);

        $preparedWhere = [];
        $preparedWhereValues = [];
        array_map(function ($key, $value) use (&$preparedWhere, &$preparedWhereValues, $whereCondition) {
            $re = '/^[=!><]*$/';

            $condition = isset($whereCondition[$key]) && is_array($whereCondition[$key]) ? $whereCondition[$key] : $whereCondition;

            $field = !preg_match($re, $condition[0]) ? $condition[0] : $condition;
            $operand = preg_match($re, $condition[1]) ? $condition[1] : $condition[2];
            $value = (!$operand && !preg_match($re, $condition[2])) ? $condition[1] : $condition[2];

            $preparedWhere["{$key}"] = "{$field} {$operand} {$value}";
            $preparedWhereValues[":{$field}"] = $value;
        }, array_keys($whereCondition), $whereCondition);

        $sql = "UPDATE {$table} SET ";
        $sql .= implode(",", array_values($preparedDataValues));
        if (sizeof($whereCondition) > 0) {
            $sql .= " WHERE ";
            $sql .= implode(" ", $preparedWhere);
        }

        $this->pdo->query($sql);

        if ($this->error()->code) {
            return $this->error()->message;
        }

        return true;
    }

    public function delete($table, $whereCondition = [])
    {
        $preparedWhere = [];
        $preparedWhereValues = [];
        array_map(function ($key, $value) use (&$preparedWhere, &$preparedWhereValues, $whereCondition) {
            $re = '/^[=!><]*$/';

            $condition = isset($whereCondition[$key]) && is_array($whereCondition[$key]) ? $whereCondition[$key] : $whereCondition;

            $field = !preg_match($re, $condition[0]) ? $condition[0] : $condition;
            $operand = preg_match($re, $condition[1]) ? $condition[1] : $condition[2];
            $value = (!$operand && !preg_match($re, $condition[2])) ? $condition[1] : $condition[2];

            $preparedWhere["{$key}"] = "{$field} {$operand} {$value}";
            $preparedWhereValues[":{$field}"] = $value;
        }, array_keys($whereCondition), $whereCondition);

        $sql = "DELETE FROM {$table} ";
        if (sizeof($whereCondition) > 0) {
            $sql .= " WHERE ";
            $sql .= implode(" ", $preparedWhere);
        }

        $this->pdo->query($sql);

        if ($this->error()->code) {
            return $this->error()->message;
        }

        return true;
    }

    private function error()
    {
        return (object) [
            'code' => intval($this->pdo->errorCode()),
            'message' => $this->pdo->errorInfo()[1]
        ];
    }

    private function query($sql)
    {
        $this->query = $this->pdo->query($sql);
    }

    private function fetchAll()
    {

        $data = [];
        while ($row = $this->query->fetch(\PDO::FETCH_ASSOC)) {
            $rowData = [];
            array_map(function ($key) use (&$rowData, $row) {
                $rowData["{$key}"] =  $row[$key];
            }, array_keys($row), $row);

            $data[] = $rowData;
        }

        return $data;
    }

    private function fetch()
    {
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }
}
