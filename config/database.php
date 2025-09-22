<?php
/**
 * Configuración de Base de Datos
 * Sistema de Administración de Mercado
 */

class Database
{
    private static $instance = null;
    private $connection;

    // Configuración de base de datos
    private $host = 'localhost';
    private $dbname = 'sistema_mercado1';
    private $username = 'hacker';
    private $password = '-Shania2012';
    private $charset = 'utf8mb4';

    private function __construct()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    // Método para ejecutar consultas preparadas
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en consulta: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para obtener un solo registro
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    // Método para obtener múltiples registros
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    // Método para obtener el último ID insertado
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    // Método para contar registros
    public function count($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }

    // Iniciar transacción
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    // Confirmar transacción
    public function commit()
    {
        return $this->connection->commit();
    }

    // Rollback transacción
    public function rollback()
    {
        return $this->connection->rollback();
    }
}