<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Web-Admin/db.php');




$username = $_POST['username'];
$password = hash('sha256', $_POST['password']);

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    header("Location: ../templates/dashboard.php");
    exit();
} else {
    echo "Invalid username or password.";
}
?>