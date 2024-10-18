<?php

class Database {
    // Propiedades para la conexión a la base de datos
    private $host = 'localhost';
    private $dbname = 'sistema';
    private $username = 'root'; 
    private $password = 'serial123';
    private $conn;

    // Método para conectar a la base de datos
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
