<?php
if (!isset($_SESSION)) {
    session_start();
}
require 'tools/dbh.tool.php';
global $pdo;

# check GET and set game_id from it
if (!isset($_GET['id']) || $_GET['id'] == '') {
    header('Location: index.php?message=game id was not selected :(');
    die();
} else {
    $game_id = $_GET['id'];
}
if (isset($_GET['edit'])) {
    $edit_parameter = $_GET['edit'];
} else {
    $edit_parameter = 'false';
}
# select all info about game from db
try {
    $stmt = $pdo->prepare('SELECT * FROM games JOIN games_platforms p ON games.id = p.id WHERE games.id = :game_id');
    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
    $stmt->execute();
    $game = $stmt->fetch();
    if (empty($game)) {
        $_SESSION['errorType'] = 'game with id: '.$game_id.' does not exist';
        header('Location: error.php');
        die();
    }

} catch (Exception $e) {
    $_SESSION['errorType'] = 'db failed: '.$e.'. While loading game with id: '.$game_id;
} finally {
    $stmt = null;
}

# if it is admin who visit page, then we will show him an edit button etc
if ($_SESSION['isAdmin']) {
    $is_admin = true;
} else {
    $is_admin = false;
}
# check if admin wants to edit game
if ($is_admin && $edit_parameter == 'true') {
    $edit = true;
} else {
    $edit = false;
}















# select three events with this game from the db
try {
    $stmt = $pdo->prepare('SELECT *, e.description as event_description, e.id as event_id
                                FROM events e
                                JOIN games g ON e.game_id = g.id
                                JOIN games_platforms p ON g.id = p.id
                                WHERE g.id = :game_id
                                ORDER BY e.event_time
                                LIMIT 3');
    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll();
    if (empty($events)) {
        $events = null;
    }

} catch (Exception $e) {
    $_SESSION['errorType'] = 'db failed: '.$e.'. While loading game with id: '.$game_id;
    header('Location: error.php');
    die();
}




?>









<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>VRMates - create new event</title>
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">

    <script defer src=""></script>
</head>

<body>

    <?php include "header.php" ?>


    <?php
    # edit view
    if ($edit) { ?>
        <form class="game_content" method="POST" enctype="multipart/form-data" action="includes/game_edit_handler.inc.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($game_id)?>">
            <div class="image">
                <img src="<?php echo htmlspecialchars($game['img_big'])?>" alt="game logo">
                <input type="file" name="image" class="image_edit" id="image_edit" accept="image/webp, image/jpeg, image/png">

            </div>
            <div class="text">
                <div class="name_and_platforms">
                    <input type="text" name="name" class="name_edit" id="name_edit" value="<?php echo htmlspecialchars($game['name'])?>">
                    <label for="name_edit"></label>
                    <input type="checkbox" name="meta" id="meta" <?php if ($game['meta']) echo 'checked' ?>>
                    <label for="meta">meta</label>
                    <input type="checkbox" name="pico" id="pico" <?php if ($game['pico']) echo 'checked' ?>>
                    <label for="pico">pico</label>
                    <input type="checkbox" name="pcvr" id="pcvr" <?php if ($game['pcvr']) echo 'checked' ?>>
                    <label for="pcvr">pcvr</label>
                </div>
                <label for="description_edit" class="description_label">description:</label>
                <textarea name="description" class="description_edit" id="description_edit"><?php echo htmlspecialchars($game['description'])?></textarea>
            </div>
            <input type="submit" name="submit" id="submit" class="orange_button save" value="save">
        </form>
    <?php }
    # display view
    else { ?>
        <div class="game_content">
            <div class="image">
                <img src="<?php echo htmlspecialchars($game['img_big'])?>" alt="game logo">
            </div>
            <div class="text">
                <div class="name_and_platforms">
                    <div class="name"><?php echo htmlspecialchars($game['name'])?></div>
                    <?php
                    if ($game['meta']) { ?>
                        <div class="platform_label">META</div>
                    <?php }
                    if ($game['pico']) { ?>
                        <div class="platform_label">PICO</div>
                    <?php }
                    if ($game['pcvr']) { ?>
                        <div class="platform_label">PC VR</div>
                    <?php } ?>
                </div>
                <div class="description_label">description:</div>
                <div class="description"><?php echo htmlspecialchars($game['description'])?></div>
            </div>
            <?php if ($is_admin) { ?>
                <a href="?id=<?php echo $game_id?>&edit=true" class="edit orange_button">edit</a>
            <?php }?>
        </div>

    <?php } ?>

<!--    --><?php //if ($is_admin && !$edit) { ?>
<!--        <a href="?id=--><?php //echo $game_id?><!--&edit=true" class="edit orange_button">edit</a>-->
<!--    --><?php //} else if ($is_admin && $edit) { ?>
<!--        <a href="?id=--><?php //echo $game_id?><!--" class="save orange_button">save</a>-->
<!--    --><?php //}?>


    <div class="event_with_game_content">




        <?php
        if ($events == null) { ?>
            <div class="create_first">
                <div class="no_games">there are no upcoming events with this game :(</div>
                <a href="create.php?id=<?php echo htmlspecialchars($game['id'])?>" class="create_first_btn orange_button">create first!</a>
            </div>
        <?php } else { ?>
            <div class="info">
                upcoming events with this game:
            </div>
            <div class="result_section">
                <?php
                foreach ($events as $event) { ?>
                    <a href="event.php?id=<?php echo $event['event_id'] ?>" class="item">
                        <img src="<?php echo $event['img_big'] ?>" alt="game_image">
                        <div class="text_section">
                            <div class="title"><?php
                                echo strlen($event['name'] > 20) ? htmlspecialchars(substr($event['name'], 0, 20)) : htmlspecialchars($event['name']);
                                ?>
                                <div class="label_section">
                                    <?php
                                    if ($event['meta']) { ?>
                                        <div class="platform_label">META</div>
                                    <?php }
                                    if ($event['pico']) { ?>
                                        <div class="platform_label">PICO</div>
                                    <?php }
                                    if ($event['pcvr']) { ?>
                                        <div class="platform_label">PC VR</div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="text"><?php echo (strlen($event['event_description']) > 250) ? htmlspecialchars(substr($event['event_description'], 0, 250).'...') : htmlspecialchars($event['event_description']) ?></div>
                        </div>
                        <div class="info_section">
                            <div class="counter"><?php echo $event['registered_people'] ?>/<?php echo $event['max_people'] ?></div>
                            <div class="datetime">
                                <?php
                                $php_date = strtotime($event['event_time']);
                                $dateYMD = date( 'M d, Y', $php_date );
                                $dateDOFW = date( 'l', $php_date );
                                $dateTIME = date( 'g:i A', $php_date );
                                ?>


                                <div><?php echo $dateYMD ?></div>
                                <div><?php echo $dateDOFW ?></div>
                                <div><?php echo $dateTIME ?></div>
                            </div>
                        </div>
                    </a>
                <?php }
                ?>
            </div>
            <div class="view_all">
                <a href="discover.php?search=<?php echo htmlspecialchars($game['name'])?>" class="view_all_btn orange_button">view all</a>
            </div>
        <?php }


        ?>
        


    </div>

    <?php include "footer.php"; ?>

</body>

</html>