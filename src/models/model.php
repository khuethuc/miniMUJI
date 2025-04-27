<?php
    include_once "config.php";

    if (!defined('HOST')) {
        define('HOST', 'localhost');
    }

    if (!defined('DBNAME')) {
        define('DBNAME', 'mini_muji');
    }

    if (!defined('USERNAME')) {
        define('USERNAME', 'root');
    }

    if (!defined('PASSWORD')) {
        define('PASSWORD', '');
    }

    class Database {
        private static $host = HOST;
        private static $dbname = DBNAME;
        private static $username = USERNAME; 
        private static $password = PASSWORD;  
        private static $conn = null;

        public static function connect() {
            if (self::$conn === null) {
                try {
                    self::$conn = new PDO(
                        "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                        self::$username,
                        self::$password
                    );
                    self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } 
                catch (PDOException $e) {
                    die("Database connection failed: " . $e->getMessage());
                }
            }
            return self::$conn;
        }
        public static function disconnect() {
            self::$conn = null;
        }
    }
?>
