<?php
if(!isset($_SESSION))
{
    session_start();
}
require "../tools/dbh.tool.php";
global $pdo;

if (!isset($_GET['q'])) {
    $_SESSION['errorType'] = 'checking if user exist but no name received';
    header('Location: error.php');
    die();
}

$username = $_GET['q'];
# if id of user given, check that username is actually new, not the same
$username_old = '';
if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare('SELECT username FROM users WHERE users.id = :user_id');
        $stmt->bindParam(':user_id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch();
        if (!empty($results)) {
            $username_old = $results['username'];
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
# if it's new username
if ($username != $username_old) {
    # check if it's already in db
    try {
        $query = "SELECT username FROM users";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $user) {
            if ($username == $user[0]) {
                echo "this nick is not available";
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
