<?php

namespace Hnllyrp\PhpSupport\Library;

use PDO;
use Exception;

class DB
{
    protected $link = null;
    protected $options = ['table' => '', 'field' => '', 'limit' => '', 'order' => '', 'where' => ''];

    public function __construct($config)
    {
        $this->connect($config);
    }

    public function connect($config)
    {
        if (is_null($this->link)) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $config['host'], $config['db']);
            $this->link = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return $this->link;
    }

    public function query($sql, array $vars = [])
    {
        $sth = $this->link->prepare($sql);
        $sth->execute($vars);
        return $sth->fetchAll();
    }

    public function execute($sql, array $vars = [])
    {
        $sth = $this->link->prepare($sql);
        return $sth->execute($vars);
    }

    public function field(...$fields)
    {
        $this->options['field'] = '`' . implode('`,`', $fields) . '`';
        return $this;
    }

    public function table(string $table)
    {
        $this->options['table'] = $table;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT {$this->options['field']} FROM {$this->options['table']} {$this->options['where']} {$this->options['order']} {$this->options['limit']}";
        return $this->link->query($sql);
    }

    public function orderBy(string $order)
    {
        $this->options['order'] = " ORDER BY " . $order;
        return $this;
    }

    public function offset($lastId = 0, $limit = 10)
    {
        //  id > 100 limit 10;
        $this->options['limit'] = " id > " . $lastId . " LIMIT " . $limit;
        return $this;
    }

    public function limit(...$limit)
    {
        $this->options['limit'] = " LIMIT " . implode(',', $limit);
        return $this;
    }

    public function where(string $where)
    {
        $this->options['where'] = " WHERE " . $where;
        return $this;
    }

    public function insert(array $vars)
    {
        $sql = "INSERT INTO {$this->options['table']} (" . implode(',', array_keys($vars)) . ") VALUES(" . implode(',', array_fill(0, count($vars), '?')) . ")";
        return $this->execute($sql, array_values($vars));
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->options['table']} {$this->options['where']}";
        return $this->execute($sql);
    }

    public function update(array $vars)
    {
        if (empty($this->options['where']))
            throw new Exception('不能缺少条件');
        $sql = "UPDATE {$this->options['table']} SET " . implode('=?,', array_keys($vars)) . "=? {$this->options['where']}";
        return $this->execute($sql, array_values($vars));
    }
}
