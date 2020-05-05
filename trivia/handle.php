<?php

//remote connection
/*$servername = "spike2careorg.ipagemysql.com";
$username = "s2c_admin";
$dbname = "spike2care";
$password = "Admins2c_pw";*/

//local connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trivia";
    
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

date_default_timezone_set('America/Los_Angeles');
//define('URL', 'http://kimdoescommerce.com/trivia');
define('URL', 'http://kimdoescommerce.local/trivia');


// Add the user
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'register') {
    
    $name = $_POST['name-input'];
    $sql = "INSERT INTO users (name) VALUES ('{$name}')";
    $result = mysqli_query($conn, $sql);

    header("Location: ".URL."/ready");
	die();
}

?>