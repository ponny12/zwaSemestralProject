<?php
function raiseError($text, $prefix='') {
    if(!isset($_SESSION)) session_start();
    $url = $prefix.'error.php';
    try {
        $_SESSION['errorType'] = strval($text);
        echo $url;
        header('Location: '.$url);
        die();
    } catch (Exception $e) {
        echo htmlspecialchars('BAD ERROR PREFIX!!! '.$text);
        die();
    }
}
