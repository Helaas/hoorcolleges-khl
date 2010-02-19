<?php
session_start();
include_once('./includes/kern.php');
session_destroy();

header('location: index.php');



?>
