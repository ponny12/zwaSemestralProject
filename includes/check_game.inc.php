<?php

require 'dbh.inc.php';
global $pdo;

if (!isset($_GET['q'])) {
    $_SESSION['errorType'] = 'check if game exist but no name received';
    header('Location: error.php');
    die();
}



$stmt = $pdo->prepare('SELECT * FROM games WHERE LOWER(games.name) = :name');
$strtolower = strtolower($_GET['q']);
$stmt->bindParam(':name',$strtolower);
$stmt->execute();
$result = $stmt->fetch();


if (!empty($result)) {
    echo 'this game is already in game list!';
}
