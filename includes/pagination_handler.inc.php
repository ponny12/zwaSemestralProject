<?php
if(!isset($_SESSION))
{
    session_start();
}

include "../tools/dbh.tool.php";
global $pdo;


if (isset($_GET['page_number'])) {

}