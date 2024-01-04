<?php
if(!isset($_SESSION))
{
    session_start();
}


require 'includes/dbh.inc.php';
global $pdo;


//    <!----------------------------->
//    <!--                         -->
//    <!--      SCRIPT STAFF       -->
//    <!--                         -->
//    <!----------------------------->


#sanitizing all variables before using in sql query

if (isset($_GET['search']) && $_GET['search'] != '' && $_GET['search'] != '_') {
    $search = $_GET['search'];
    $sanitized_search = $search;
    #sanitizing
    $sanitized_search = str_replace('\\', '\\\\', $sanitized_search);
    $sanitized_search = str_replace('%', '\%', $sanitized_search);
    $sanitized_search = str_replace('"', '\"', $sanitized_search);
    $sanitized_search = str_replace('_', '\_', $sanitized_search);
    $sanitized_search = str_replace('-', '\-', $sanitized_search);

} else {
    $search = '_';
    $sanitized_search = $search;
}


if (isset($_GET['sorted_by'])) {
    switch ($_GET['sorted_by']) {
        case 'name':
            $sorted_by = 'name';
            break;
        case 'max_people':
            $sorted_by = 'max_people';
            break;
        default:
            $sorted_by = 'event_time';
    }
} else {
    $sorted_by = 'event_time';
}

if (isset($_GET['items_per_page'])) {
    switch ($_GET['items_per_page']) {
        case '2':
            $items_per_page = 2;
            break;
        case '5':
            $items_per_page = 5;
            break;
        case '10':
            $items_per_page = 10;
            break;
        case '25':
            $items_per_page = 25;
            break;
        case '50':
            $items_per_page = 50;
            break;
        default:
            $items_per_page = 5;
    }
} else {
    $items_per_page = 5;
}

if (isset($_GET['order'])) {
    switch ($_GET['order']) {
        case 'ASC':
            $order = 'ASC';
            break;
        case 'DESC':
            $order = 'DESC';
            break;
        default:
            $order = 'ASC';
    }
} else {
    $order = 'ASC';
}

if (isset($_GET['platform'])) {
    switch ($_GET['platform']) {
        case 'all':
            $platform = 'all';
            break;
        case 'meta':
            $platform = 'meta';
            break;
        case 'pico':
            $platform = 'pico';
            break;
        case 'pcvr':
            $platform = 'pcvr';
            break;
        default:
            $platform = 'all';
    }
} else {
    $platform = 'all';
}

#saving all GET args to string in order to not lose them after page transition

$all_args = "&search=$search&sorted_by=$sorted_by&order=$order&items_per_page=$items_per_page&platform=$platform";

#selecting only right platform and finding out how many rows we have
try {
    if ($platform == 'all') {
        $stmt = $pdo->query('SELECT COUNT(*)
                                        FROM events e 
                                        JOIN games g ON e.game_id = g.id
                                        LEFT JOIN games_platforms p ON g.id = p.id
                                        WHERE (g.name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%" OR e.description LIKE "%'.$sanitized_search.'%")');
    } else {
        $stmt = $pdo->query('SELECT COUNT(*)
                                        FROM events e 
                                        JOIN games g ON e.game_id = g.id
                                        LEFT JOIN games_platforms p ON g.id = p.id
                                        WHERE (g.name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%" OR e.description LIKE "%'.$sanitized_search.'%") AND p.'.$platform.' = 1');
    }
    $result = $stmt->fetch();
} catch (Exception $e) {
    $_SESSION['errorType'] = 'database request failed: '.$e;
    header('Location: error.php');
    die();
}


$row_count = $result[0];

# calculating on which page we are

$num_of_page = ceil($row_count / $items_per_page);
if (isset($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $num_of_page) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}
$from_item = ($page - 1) * $items_per_page;













//        $stmt = $pdo->prepare('SELECT e.id as event_id, g.id as game_id, g.img_big, g.name, g.description,
//                                    e.registered_people, e.max_people, e.event_time, COALESCE (p.meta, 0) as meta, COALESCE (p.pico, 0) as pico, COALESCE (p.pcvr, 0) as pcvr
//                                    FROM events e
//                                    JOIN games g ON e.game_id = g.id
//                                    LEFT JOIN events_platforms p ON e.id = p.event_id
//                                    ORDER BY '.$sorted_by.'
//                                    LIMIT :from_item, :items_per_page');



try {
    if ($platform == 'all') {
        $stmt = $pdo->query('SELECT e.id as event_id, g.id as game_id, g.img_big, g.name as name, g.description, e.description as event_description,
                                    e.registered_people, e.max_people as max_people, e.event_time as event_time, COALESCE (p.meta, 0) as meta, COALESCE (p.pico, 0) as pico, COALESCE (p.pcvr, 0) as pcvr
                                    FROM events e 
                                    JOIN games g ON e.game_id = g.id 
                                    LEFT JOIN games_platforms p ON g.id = p.id
                                    WHERE (name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%" OR e.description LIKE "%'.$sanitized_search.'%")
                                    ORDER BY '.$sorted_by.' '.$order.'
                                    LIMIT '.$from_item.', '.$items_per_page);
    } else {
        $stmt = $pdo->query('SELECT e.id as event_id, g.id as game_id, g.img_big, g.name as name, g.description, e.description as event_description,
                                    e.registered_people, e.max_people as max_people, e.event_time as event_time, COALESCE (p.meta, 0) as meta, COALESCE (p.pico, 0) as pico, COALESCE (p.pcvr, 0) as pcvr
                                    FROM events e 
                                    JOIN games g ON e.game_id = g.id 
                                    LEFT JOIN games_platforms p ON g.id = p.id
                                    WHERE (name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%" OR e.description LIKE "%'.$sanitized_search.'%") AND p.'.$platform.' = 1
                                    ORDER BY '.$sorted_by.' '.$order.'
                                    LIMIT '.$from_item.', '.$items_per_page);
    }
    $stmt->execute();
    $events = $stmt->fetchAll();
} catch (Exception $e) {
    $_SESSION['errorType'] = 'database request failed: '.$e;
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
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">

    <title>VRMates - discover events</title>
</head>

<body>

    <?php include "header.php" ?>


    <!----------------------------->
    <!--                         -->
    <!--      SEARCH STAFF       -->
    <!--                         -->
    <!----------------------------->

    <div class="search_container">
        <form action="#" class="search_section" method="GET">
            <div class="search_bar">
                <label for="search"></label>
                <input type="search" class="line" name="search" id="search"
                    placeholder="search for specific game or activities"
                       <?php if ($search != '_') echo 'value="'.htmlspecialchars($search).'"' ?>>
                <input type="submit" class="submit" value="search">
            </div>
            <div class="settings_bar">
                <div class="sorted">
                    <label for="sorted_by_select">sorted by</label>
                    <select name="sorted_by" id="sorted_by_select" class="sorted_by_select">
                        <option value="event_time" <?php if ($sorted_by == 'event_time') echo 'selected' ?>>date</option>
                        <option value="max_people" <?php if ($sorted_by == 'max_people') echo 'selected' ?>>max people</option>
                        <option value="name" <?php if ($sorted_by == 'name') echo 'selected' ?>>name of game</option>
                    </select>
                    <label for="order"></label>
                    <select name="order" id="order" class="order">
                        <option value="ASC" <?php if ($order == 'ASC') echo 'selected' ?>>↓</option>
                        <option value="DESC" <?php if ($order == 'DESC') echo 'selected' ?>>↑</option>
                    </select>
                </div>
                <div class="filters">
                    <label for="platform_select">platform</label>
                    <select name="platform" id="platform_select" class="platform_select">
                        <option value="all" <?php if ($platform == 'all') echo 'selected' ?>>all</option>
                        <option value="meta" <?php if ($platform == 'meta') echo 'selected' ?>>META QUEST</option>
                        <option value="pico" <?php if ($platform == 'pico') echo 'selected' ?>>PICO</option>
                        <option value="pcvr" <?php if ($platform == 'pcvr') echo 'selected' ?>>PC VR</option>
                    </select>
                    <label for="items_per_page">events on one page</label>
                    <select name="items_per_page" id="items_per_page" class="filter">
                        <option value="2" <?php if ($items_per_page == '2') echo 'selected' ?>>2</option>
                        <option value="5" <?php if ($items_per_page == '5') echo 'selected' ?>>5</option>
                        <option value="10" <?php if ($items_per_page == '10') echo 'selected' ?>>10</option>
                        <option value="25" <?php if ($items_per_page == '25') echo 'selected' ?>>25</option>
                        <option value="50" <?php if ($items_per_page == '50') echo 'selected' ?>>50</option>
                    </select>
                </div>
            </div>
        </form>
    </div>


    <!----------------------------->
    <!--                         -->
    <!--    RESULT OUT STAFF     -->
    <!--                         -->
    <!----------------------------->


    <div class="result_section">


        <?php

        if (empty($events)) {
            echo "nothing found(";
        } else {
            echo $row_count.' results found';
        }

        foreach ($events as $event) {
//            $stmt = $pdo->prepare('SELECT * FROM games WHERE id = :game_id');
//            $stmt->bindParam(':game_id', $event['game_id']);
//            $stmt->execute();
//            $game = $stmt->fetch();
//
            $php_date = strtotime($event['event_time']);
            $dateYMD = date( 'M d, Y', $php_date );
            $dateDOFW = date( 'l', $php_date );
            $dateTIME = date( 'H:i', $php_date );

            ?>
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
                        <div><?php echo $dateYMD ?></div>
                        <div><?php echo $dateDOFW ?></div>
                        <div><?php echo $dateTIME ?></div>
                    </div>
                </div>
            </a>
        <?php }
        ?>
    </div>

    <!----------------------------->
    <!--                         -->
    <!--    PAGINATION STAFF     -->
    <!--                         -->
    <!----------------------------->

    <div class="pagination_content">
        <!--prev btn-->
        <a href="?page=<?php echo $page - 1 ?><?php echo $all_args?>" <?php
            if ($page <= 1) {
                echo 'class="prev btn disabled"';
            } else {
                echo 'class="prev btn"';
            }
        ?>>prev</a>

        <!-- prev 2 pages btns -->
        <?php
        if ($page > 2) { ?>
            <a href="?page=<?php echo $page - 2 ?><?php echo $all_args?>" class="prev_page btn small"><?php echo $page - 2 ?></a>
        <?php }
        ?>

        <?php
        if ($page > 1) { ?>
            <a href="?page=<?php echo $page - 1 ?><?php echo $all_args?>" class="prev_page btn small"><?php echo $page - 1 ?></a>
        <?php }
        ?>

        <!-- curr page btn -->
        <a href="?page=<?php echo $page ?><?php echo $all_args?>" class="selected btn"><?php echo $page ?></a>

        <!-- next 2 pages btns -->
        <?php
        if ($page < $num_of_page) { ?>
            <a href="?page=<?php echo $page + 1 ?><?php echo $all_args?>" class="next_page btn small"><?php echo $page + 1 ?></a>
        <?php }
        ?>

        <?php
        if ($page < $num_of_page - 1) { ?>
            <a href="?page=<?php echo $page + 2 ?><?php echo $all_args?>" class="next_page btn small"><?php echo $page + 2 ?></a>
        <?php }
        ?>



        <!--next btn-->
        <a href="?page=<?php echo $page + 1 ?><?php echo $all_args?>" <?php
        if ($page >= $num_of_page) {
            echo 'class="next btn disabled"';
        } else {
            echo 'class="next btn"';
        }
        ?>>next</a>





    </div>



    <?php include "footer.php" ?>

</body>



</html>
