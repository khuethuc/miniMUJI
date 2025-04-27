<?php
    class UserModel {
        private $pdo;

        public function __construct() {
            $this->pdo = Database::connect();
        }

        
    }
?>
