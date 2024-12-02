<?php

class BaseModel
{
    protected $connect;

    // protected $id;
    // protected $tableName;
    // protected $columns = [];
    // protected $queryBuilder;

    public function __construct()
    {
        try {
            $this->connect = new PDO("mysql:host=$this->host; dbname=$this->db;charset=utf8", $this->user, $this->pass);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
        }
    }
    private $host = "localhost";
    private $db = "__productDB";
    private $user = "root";
    private $pass = "";
    public static function getAll()
    {
        $model = new static();
        $sql = "select * from $model->tableName";
        $stmt = $model->connect->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($model));
        return $result;
    }
    public function insert()
    {
        $this->queryBuilder = "insert into $this->tableName (";
        foreach ($this->columns as $col) {
            if ($this->{$col} == null && !is_string($this->{$col}))
                continue;
            $this->queryBuilder .= "$col, ";
        }
        $this->queryBuilder = rtrim($this->queryBuilder, ", ");
        $this->queryBuilder .= ") values ( ";
        foreach ($this->columns as $col) {
            if ($this->{$col} == null)
                continue;
            $this->queryBuilder .= "'" . $this->{$col} . "', ";
        }
        $this->queryBuilder = rtrim($this->queryBuilder, ", ");
        $this->queryBuilder .= ")";

        $stmt = $this->connect->prepare($this->queryBuilder);
        try {

            $stmt->execute();
            $this->id = $this->connect->lastInsertId();

            return $this;
        } catch (Exception $ex) {
            return null;
        }
    }
    function update()
    {
        $this->queryBuilder = "update $this->tableName set ";

        foreach ($this->columns as $col) {
            if ($this->{$col} == null) {
                continue;
            }
            $this->queryBuilder .= " $col = '" . $this->{$col} . "', ";
        }
        $this->queryBuilder = rtrim($this->queryBuilder, ", ");
        $this->queryBuilder .= " where id = $this->id";

        $stmt = $this->connect->prepare($this->queryBuilder);
        // var_dump($stmt);die;
        try {
            $stmt->execute();
            return $this;
        } catch (Exception $ex) {
            return null;
        }
    }
    public function delete()
    {
        $this->queryBuilder = "delete from $this->tableName where id = $this->id";
        $stmt = $this->connect->prepare($this->queryBuilder);
        try {
            $stmt->execute();
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    // --------------------------------------------------------------------------------------------------
    public function get()
    {
        try {
            $stmt = $this->connect->prepare($this->queryBuilder);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
            return $result;
        } catch (Exception $ex) {
            return null;
        }
    }
    public static function find($id)
    {
        $model = new static();
        $sql = "select * from $model->tableName where id = $id";
        $stmt = $model->connect->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($model));
        // var_dump($result);die;
        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }
    public static function where($arr = [])
    {
        $model = new static();
        $model->queryBuilder = "select * from $model->tableName where ";

        if (count($arr) == 2) {
            $model->queryBuilder .= "$arr[0] = '$arr[1]'";
        }
        if (count($arr) == 3) {
            $model->queryBuilder .= "$arr[0] $arr[1] '$arr[2]'";
        }
        return $model;
    }
    public function andWhere($arr = [])
    {
        $this->queryBuilder .= "and ";
        if (count($arr) == 2) {
            $this->queryBuilder .= "$arr[0] = '$arr[1]'";
        }
        if (count($arr) == 3) {
            $this->queryBuilder .= "$arr[0] $arr[1] '$arr[2]'";
        }
        return $this;
    }
    public function orWhere($arr = [])
    {
        $this->queryBuilder .= "or ";
        if (count($arr) == 2) {
            $this->queryBuilder .= "$arr[0] = '$arr[1]'";
        }
        if (count($arr) == 3) {
            $this->queryBuilder .= "$arr[0] $arr[1] '$arr[2]'";
        }
        return $this;
    }
}
?>