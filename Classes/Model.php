<?php

namespace Lnw\Core;

abstract class Model
{
    protected $adapter;
    protected $sql;
    protected $fillable;
    protected $table;
    private $stmt;
    private $dba;

    public function __construct()
    {
        $this->adapter = new Zend\Db\Adapter\Adapter([
            'driver' => 'Mysqli',
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASS,
            'charset' => 'utf8',
        ]);
        $this->sql = new Zend\Db\Sql\Sql($this->adapter);
    }

    public function insert(array $data)
    {
        $this->dba = $this->sql->insert();
        $values = $this->get_values($data);
        $this->dba->into($this->table)->values($values);
        $statement = $this->sql->prepareStatementForSqlObject($this->dba);
        $stmt = $statement;

        return $statement->execute();
    }

    public function select($table = null)
    {
        $this->dba = $this->sql->select();
        $table = !empty($table) ? $table : $this->table;
        $this->dba = $this->dba->from($table);

        return $this;
    }

    public function findWhere(array $conditions)
    {
        if (count($conditions) > 0) {
            $where = $this->get_values($conditions);
            $this->dba = $this->dba->where($where);
        }

        return $this;
    }

    public function order($conditions)
    {
        if (!empty($conditions)) {
            $this->dba = $this->dba->order($conditions);
        }

        return $this;
    }

    public function get()
    {
        $statement = $this->sql->prepareStatementForSqlObject($this->dba);

        return $statement->execute();
    }

    public function get_values($data)
    {
        foreach ($this->fillable as $key) {
            if (array_key_exists($key, $data)) {
                $result[$key] = $data[$key];
            }
        }

        return $result;
    }

    public function sqlQuery()
    {
        $statement = $this->sql->buildSqlString($this->dba);

        return $statement;
    }
}
