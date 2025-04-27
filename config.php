<?php 

define('HOST', 'localhost');
define('DBNAME', 'mini_muji');
define('USERNAME', 'root');
define('PASSWORD', '');

$conn = new mysqli(HOST, USERNAME, PASSWORD, DBNAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

?>
