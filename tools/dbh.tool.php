<?php

//
//$dsn = "mysql:host=localhost;dbname=vrmates_db";
//$dbusername = "root";
//$dbpassword = "";
//$pdo = null;
//
//try {
//    $pdo = new PDO($dsn, $dbusername, $dbpassword);
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//} catch (PDOException $e) {
//    echo "Connection failed: " . $e->getMessage();
//}


//$host = "localhost";
//$dbusername = "smyshdan";
//$db = "smyshdan";
//$dbpassword = "webove aplikace";
//$pdo = null;
//
//try {
//    $conn = new mysqli($host, $dbusername, $dbpassword, $db);
//} catch (Exception $e) {
//    echo "Connection failed: " . $e->getMessage();
//}

$dsn = "mysql:host=localhost;dbname=smyshdan";
$dbusername = "smyshdan";
$dbpassword = "webove aplikace";
$pdo = null;

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    $_SESSION['errorType'] = 'Connection to db failed.';
    header('Location: ../error.php');
    die();
}