<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); 
$dotenv->load();

/**
 * Database class
 * 
 * This class is responsible for handling the database connection.
 */
class Database {
    private $host;
    private $db_name; 
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host = $_ENV['DATABASE_HOST'];
        $this->db_name = $_ENV['DATABASE_NAME'];
        $this->username = $_ENV['DATABASE_USER'];
        $this->password = $_ENV['DATABASE_PASS'];
    }

    /**
     * Database connection method
     * 
     * This method is used to establish the connection to the database.
     * 
     * @return PDO connection object
     */
    public function dbConnection() {
        $this->conn = null;

        try {
            // Initialize a new PDO connection
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, 
                $this->password
            );

            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // If a connection error occurs, terminate the script and output the error
            exit("Database connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
