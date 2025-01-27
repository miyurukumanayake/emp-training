<?php
$servername = "localhost";
$username = "root";
$password = "9315@119Vm";
$dbname = "emp-training";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}