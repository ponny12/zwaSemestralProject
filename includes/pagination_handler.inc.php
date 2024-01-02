<?php
if(!isset($_SESSION))
{
    session_start();
}

include "dbh.inc.php";
global $pdo;


if (isset($_GET['page_number'])) {

}