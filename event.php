<?php
if(!isset($_SESSION))
{
    session_start();
}
require_once 'includes/dbh.inc.php';
require 'tools/error.tool.php';

if (isset($_GET['id'])) {
    try {

        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM events JOIN games ON events.game_id = games.id WHERE events.id = :event_id');
        $stmt->bindParam(':event_id', $_GET['id']);
        $stmt->execute();
        $event = $stmt->fetch();
        if (empty($event)) {
            raiseError('event does not exist (id: '.$_GET["id"].')');
        }
    } catch (PDOException $e) {
        echo $e->getMessage(); die();
    } finally {
        $stmt = null;
        $pdo = null;
    }

} else {
    $_SESSION['errorType'] = 'event does not exist (not has even been chosen).';
    header('Location: error.php');
    die();
}








?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>VRMate</title>
    <link rel="icon" type="image/x-icon" href="/images/icons/favicon.ico">
</head>



<body>

    <?php include "header.php" ?>

    <div class="event_content">
        <div class="container">
            <img src="<?php echo $event['img_big'] ?>" alt="eleven table tennis logo">
            <div class="text_section">
                <div class="title"><?php echo $event['name'] ?></div>
                <div class="text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laudantium rem distinctio
                    quisquam repellat fugiat et officia sapiente, sequi iure officiis consequatur in velit, debitis
                    exercitationem! Delectus nihil quis vero at.</div>
            </div>
            <div class="info_section">
                <div class="counter">2/4</div>
                <div class="datetime">today 17:45</div>
            </div>
        </div>
        <button class="btn" value="event_id">i'am in!</button>
    </div>

</body>

</html>

<?php

include "footer.php";