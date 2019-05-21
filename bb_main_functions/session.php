<?php
include_once( "classes/ezlog.php" );
session_start();
$_SESSION['grupoNom'] = $_POST['gpo'];
eZLog::writeNotice("Errorr....[".print_r($_SESSION['grupoNom'],true)."]");
?>