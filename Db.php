<?php

class Dbconnect {
    private $host;
    private $user;
    private $pass;
    private $name;

    private $connection;

    public function __construct() {
        try {
            $this->host = "localhost";
            $this->user = "root";
            $this->pass = "";
            $this->name = "shop";

            $this->connection = new mysqli(
                $this->host,
                $this->user,
                $this->pass,
                $this->name
            );
        } catch(Exception $e) {
            throw New Exception($e->getMessage());
        }
    }

    public function Select($query) {
        try {
            $result = [];

            $query_result = $this->connection->query($query);

            while ($row = $query_result->fetch_assoc())
			    array_push($result, $row);
            
            return $result;
        } catch(Exception $e) {
            throw New Exception($e->getMessage());
        }
    }

    public function Escape($param) {
        return $this->connection->real_escape_string($param);
    }
}