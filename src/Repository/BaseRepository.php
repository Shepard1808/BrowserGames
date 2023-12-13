<?php

namespace Fbartz\BrowserGames\Repository;

use Fbartz\BrowserGames\Service\Database;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

class BaseRepository
{

    use SingletonRepository;

    private ConnectionInterface $connection;
    protected string $tableName = '';


    public function initialize(): void
    {
        $this->connection = Database::getConnection();
        $this->getTableName();
    }

    private function getTableName(): void
    {
        $name = explode('\\', get_class($this));
        $name = end($name);
        $name = str_replace('Repository', '', $name);
        if (empty($this->tableName)) {
            $name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $name);
            $this->tableName = $name;
        }
    }

    public function getAll(): PromiseInterface
    {
        return $this->connection->query("SELECT * FROM $this->tableName;")
            ->then(function (QueryResult $result){
                return $result->resultRows ?? [];
            });
    }

    public function find(int $id): PromiseInterface
    {
        return $this->connection->query("SELECT * FROM $this->tableName where id = ?", [$id])
            ->then(function (QueryResult $result){
           return $result->resultRows ?? [];
        });
    }

    public function findBy(array $args): PromiseInterface
    {
        $whereStatement = " WHERE 1 = 1 ";
        foreach ($args as $column => $value) {
            $whereStatement .= " AND $column = ? ";
        }
        return $this->connection->query("SELECT * FROM $this->tableName $whereStatement;", array_values($args))
            ->then(function (QueryResult $result) {
                return $result->resultRows ?? [];
            });
    }

    public function delete(string $column, $value): PromiseInterface
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE " . $column . " = ?;";
        return $this->connection->query($query, [$value])->then(function (QueryResult $result) {
            return $result->affectedRows;
        });
    }

    public function deleteIn(string $column, array $args): PromiseInterface
    {
        $prep = array_fill(0, count($args), "?");
        $prep = implode(",", $prep);
        $whereStatement = " WHERE $column in ( $prep )";
        return $this->connection->query("DELETE FROM " . $this->tableName . " " . $whereStatement, $args)
            ->then(function (QueryResult $result) {
                return $result->affectedRows;
            });
    }

    public function create(array $data): PromiseInterface
    {
        $keys = array_keys($data);
        $query = "INSERT INTO " . $this->tableName . "(";
        $keysAmount = count($keys);
        for ($i = 0; $i < $keysAmount; $i++) {
            $query .= " $keys[$i],";
        }
        $query = substr($query, 0, -1) . ") VALUES (";
        $query .= str_repeat("?,", count($keys));
        $query = substr($query, 0, -1) . ");";
        return $this->connection->query($query, array_values($data))->then(function (QueryResult $result) {
            return $result;
        });
    }

    public function update(array $data, int $id):PromiseInterface
    {
        $query = "UPDATE " . $this->tableName . " SET ";
        foreach (array_keys($data) as $key) {
            $query .= "$key = ?,";
        }
        $query = substr($query,0,-1) . " WHERE id = ?;";

        return $this->connection->query($query, array_merge(array_values($data),[$id]))
            ->then(function (QueryResult $result){
                return [200, $result];
            });
    }

}