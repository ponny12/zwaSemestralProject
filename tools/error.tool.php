<?php
function raiseError($text, $prefix='') {
    require_once $prefix.'config/config.php';
    $url = $prefix.'error.php';
    try {
        $_SESSION['errorType'] = strval($text);
        echo $url;
//        header('Location: '.$url);
        echo $text;
        die();
    } catch (Exception $e) {
        echo htmlspecialchars('BAD ERROR PREFIX!!! '.$text);
        die();
    }
}
