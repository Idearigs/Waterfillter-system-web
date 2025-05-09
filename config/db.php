<?php
/* Database credentials */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'waterfillter_system');

/* First connect without database to create it if needed */
$temp_mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

/* Check connection */
if($temp_mysqli->connect_error){
    die("ERROR: Could not connect to server. " . $temp_mysqli->connect_error);
}

/* Create the database if it doesn't exist */
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if($temp_mysqli->query($sql) === false){
    die("ERROR: Could not create database. " . $temp_mysqli->error);
}

$temp_mysqli->close();

/* Now connect to the database */
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

/* Check connection */
if($mysqli->connect_error){
    die("ERROR: Could not connect to database. " . $mysqli->connect_error);
}

// Create partners table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS partners (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    logo_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if($mysqli->query($sql) === false){
    die("ERROR: Could not create table. " . $mysqli->error);
}
?>
