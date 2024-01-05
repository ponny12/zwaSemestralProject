<?php
if(!isset($_SESSION))
{
    session_start();
}

require 'tools/dbh.tool.php';
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


//if (isset($_GET['sorted_by'])) {
//    switch ($_GET['sorted_by']) {
//        case 'name':
//            $sorted_by = 'name';
//            break;
//        default:
//            $sorted_by = 'name';
//    }
//} else {
//    $sorted_by = 'name';
//}


$sorted_by = 'name'; # always must be by name, games have not other criteria

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
//
//if ($platform == 'meta') {
//    $stmt = $pdo->query('SELECT COUNT(*)
//                                    FROM games g
//                                    LEFT JOIN games_platforms p ON g.id = p.id
//                                    WHERE p.meta = 1');
//} else if ($platform == 'pico') {
//    $stmt = $pdo->query('SELECT COUNT(*)
//                                    FROM games g
//                                    LEFT JOIN games_platforms p ON g.id = p.id
//                                    WHERE p.pico = 1');
//} else if ($platform == 'pcvr') {
//    $stmt = $pdo->query('SELECT COUNT(*)
//                                    FROM games g
//                                    LEFT JOIN games_platforms p ON g.id = p.id
//                                    WHERE p.pcvr = 1');
//}

try {
    if ($platform == 'all') {
        $stmt = $pdo->query('SELECT COUNT(*)
                                        FROM games g
                                        WHERE (g.name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%")');
    } else {
        $stmt = $pdo->query('SELECT COUNT(*)
                                        FROM games g
                                        LEFT JOIN games_platforms p ON g.id = p.id
                                        WHERE (g.name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%") AND p.'.$platform.' = 1');
    }
    $result = $stmt->fetch();
} catch (Exception $e) {
    $_SESSION['errorType'] = 'database request failed: '.$e;
    header('Location: error.php');
    die();
}


$row_count = $result[0];

# calculating on which page we are

#http://localhost/semestralka/game_list.php?page=2&search=_&sorted_by=name&order=ASC&items_per_page=2&platform=all
#http://localhost/semestralka/game_list.php?page=2&search=a&sorted_by=name&order=ASC&items_per_page=2&platform=all

$num_of_page = ceil($row_count / $items_per_page);
if (isset($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $num_of_page) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}
$from_item = ($page - 1) * $items_per_page;

try {
    if ($platform == 'all') {
        $stmt = $pdo->query('SELECT g.id, g.img_small, g.name, g.description, COALESCE (p.meta, 0) as meta, COALESCE (p.pico, 0) as pico, COALESCE (p.pcvr, 0) as pcvr
                                    FROM games g
                                    LEFT JOIN games_platforms p ON g.id = p.id
                                    WHERE (g.name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%")
                                    ORDER BY '.$sorted_by.' '.$order.'
                                    LIMIT '.$from_item.', '.$items_per_page);
    } else {
        $stmt = $pdo->query('SELECT g.id, g.img_small, g.name, g.description, COALESCE (p.meta, 0) as meta, COALESCE (p.pico, 0) as pico, COALESCE (p.pcvr, 0) as pcvr
                                    FROM games g
                                    LEFT JOIN games_platforms p ON g.id = p.id
                                    WHERE (g.name LIKE "%'.$sanitized_search.'%" OR g.description LIKE "%'.$sanitized_search.'%") AND p.'.$platform.' = 1
                                    ORDER BY '.$sorted_by.' '.$order.'
                                    LIMIT '.$from_item.', '.$items_per_page);
    }
    $stmt->execute();
    $games = $stmt->fetchAll();
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
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>VRMate</title>
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
</head>

<body>

<?php include "header.php" ?>



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
                <label for="items_per_page">games on one page</label>
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


<div class="results_container">
    <?php
    if (empty($games)) {
        echo "nothing found(";
    } else {
        echo $row_count.' results found';
    }

    foreach ($games as $game) { ?>

        <a href="game.php?id=<?php echo $game['id'] ?>" title="<?php echo $game['description'] ?>" class="item">
            <div class="group">
                <img src="<?php echo $game['img_small'] ?>" alt="game_image">

                <div class="title"><?php
                    echo htmlspecialchars($game['name']);
                    ?>
                </div>
            </div>
            <div class="label_section">
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

        </a>
    <?php } ?>
</div>

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

<a href="suggest_new_game.php" class="suggestions orange_button">suggest a new game</a>


<?php include "footer.php"; ?>
</body>

</html>
