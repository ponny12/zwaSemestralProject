<?php
require_once '../config/config.php';
require '../tools/dbh.tool.php';
global $pdo;
require '../tools/error.tool.php';

if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header('Location: ../index.php?message=you are not the admin!');
    die();
}
# get all id's of past events
try {
    $stmt = $pdo->prepare('SELECT events.id FROM events WHERE events.event_time < CURDATE()');
    $stmt->execute();
    $results = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}

foreach ($results as $result) {
    $event_id = $result['id'];
    # delete all participants for each event
    try {
        $stmt = $pdo->prepare('DELETE FROM events_users WHERE events_users.event_id = :event_id');
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        raiseError('db failed: '.$e, '../');
    }
    # delete each event
    try {
        $stmt = $pdo->prepare('DELETE FROM events WHERE events.id = :event_id');
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        raiseError('db failed: '.$e, '../');
    }
}
# redirect
header('Location: ../index.php?message=all past events have been deleted');
