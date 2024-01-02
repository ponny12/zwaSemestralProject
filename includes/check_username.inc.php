<?php
if(!isset($_SESSION))
{
    session_start();
}


if (!isset($_GET['q'])) {
    $_SESSION['errorType'] = 'checking if user exist but no name received';
    header('Location: error.php');
    die();
}

$username = $_GET['q'];

try {
    require "dbh.inc.php";
    global $pdo;
    $query = "SELECT username FROM users";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $user) {
        if ($username == $user[0]) {
            echo "this nick is not available. php";
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}