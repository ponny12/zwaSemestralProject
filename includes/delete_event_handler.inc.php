<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST['event_id']) || empty($_POST['user_id'])) {
    header('Location: ../index.php');
    die();
}



require '../tools/error.tool.php';
require '../includes/dbh.inc.php';
global $pdo;

$event_id = intval($_POST['event_id']);
$user_id = intval($_POST['user_id']);

# if delete text was not typed so nothing to do
if (empty($_POST['delete_text']) || $_POST['delete_text'] != 'DELETE') {
    header('Location: ../event.php?id='.$event_id);
    die();
}
# check rights
if ($user_id != $_SESSION['loginID'] && $_SESSION['loginID'] != 1) {
    raiseError('attempt to delete event (id: '.$event_id.') but have not enough rights', '../');
}

# delete all participants from db
try {
    $stmt = $pdo->prepare('DELETE FROM events_users WHERE events_users.event_id = :event_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# delete event from db
try {
    $stmt = $pdo->prepare('DELETE FROM events WHERE events.id = :event_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# redirect
header('Location: ../index.php?massage=you have successfully delete the event');
die();