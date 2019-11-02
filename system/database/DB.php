<?php 
namespace system\database;

class DB{
    private static $_singleton = null;
    private $_connection = null;

    /**
     * Singleton... constructor is private 
     */
    private function __construct(){
        $host = DB_HOST;
        $port = DB_PORT;
        $db   = DB_DATABASE;
        $user = DB_USERNAME;
        $pass = DB_PASSWORD;

        try {
            $this->_connection = new \PDO("mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db", $user, $pass);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Close connection. Destory the object.
     */
    public function __destruct(){
        $this->_connection = null;  
    }

    /**
     * Get a instance of this class object
     *
     * @return DB
     */
    public static function getInstance(){
        if(!self::$_singleton){
            self::$_singleton = new DB();
        }
        return self::$_singleton;
    }

    /**
     * Get database connection
     *
     * @return mixed
     */
    public function getConnection(){
        return $this->_connection;
    }
}
