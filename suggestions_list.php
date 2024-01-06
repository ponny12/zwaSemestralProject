<?php
require_once 'config/config.php';
require 'basedir/basedir.php';
global $base_dir;
include 'tools/dbh.tool.php';
global $pdo;
include "tools/error.tool.php";
if (empty($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header('Location: index.php?message=you are not allowed to be there');
    die();
}
# get suggestion info
try {
    $stmt = $pdo->prepare('SELECT * FROM suggestions');
    $stmt->execute();
    $suggestions = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: '.$e);
}


?>
<!DOCTYPE html>

<html lang="en">

<head>
    <base href="<?php echo $base_dir?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>VRMate</title>
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
    <script defer src="js/profileEditScript.js"></script>
</head>


<body>
<?php include "header.php" ?>
<div class="suggestions_container">
    <?php
    if (empty($suggestions)) { ?>
        <div class="no_suggestions">no suggestions yet :(</div>
    <?php }


    foreach ($suggestions as $suggestion) {
        # get creator info
        try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE users.id = :creator_id');
        $stmt->bindParam(':creator_id', $suggestion['suggested_by'], PDO::PARAM_INT);
        $stmt->execute();
        $creator = $stmt->fetch();
        } catch (Exception $e) {
        raiseError('db failed: '.$e);
        }
        ?>
        <div class="item">
            <img src="<?php echo htmlspecialchars($suggestion['img_big'])?>" alt="sss">
            <div class="text">
                <div class="title"><?php
                    echo htmlspecialchars($suggestion['name']);
                    ?>
                    <div class="label_section">
                        <?php
                        if ($suggestion['meta']) { ?>
                            <div class="platform_label">META</div>
                        <?php }
                        if ($suggestion['pico']) { ?>
                            <div class="platform_label">PICO</div>
                        <?php }
                        if ($suggestion['pcvr']) { ?>
                            <div class="platform_label">PC VR</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="description"><?php echo htmlspecialchars($suggestion['description'])?></div>
                <a href="profile.php?id=<?php echo htmlspecialchars($creator['id']) ?>" class="creator">
                    suggested by:
                    <img src="<?php echo htmlspecialchars($creator['img_small']) ?>" alt="creator photo">
                    <div class="name"><?php echo htmlspecialchars($creator['username'])?></div>
                </a>
            </div>
            <div class="decision">
                <a href="includes/accept_suggestion.inc.php?id=<?php echo $suggestion['id']?>" class="accept">accept</a>
                <a href="includes/reject_suggestion.inc.php?id=<?php echo $suggestion['id']?>" class="reject">reject</a>
            </div>
        </div>
    <?php }
    ?>
</div>

<?php include "footer.php";?>

</body>


</html>
