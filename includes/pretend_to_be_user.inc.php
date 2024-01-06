<?php
include '../tools/dbh.tool.php';
include '../tools/error.tool.php';
global $pdo;

if (!isset($_SESSION) || !$_SESSION['isAdmin']) {
    header('Location: index.php?message=you are not the admin');
    die();
}

if (!isset($_GET['id'])) {
    header('Location: index.php?message=id was not given');
    die();
} else {
    $id = $_GET['id'];
}
# get info about user
try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE users.id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# get info about role
try {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE admins.user_id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    if (empty($result)) {
        $isAdmin = false;
    } else {
        $isAdmin = true;
    }
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}

$_SESSION['loginID'] = $user['id'];
$_SESSION['loginName'] = $user['username'];
$_SESSION['loginImage'] = $user['img_small'];
$_SESSION['isAdmin'] = $isAdmin;

header('Location: ../index.php?message=you pretend to be a '.htmlspecialchars($user['username']));
die();
