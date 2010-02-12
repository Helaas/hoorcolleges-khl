<?php
    include_once('./includes/gebruiker.class.php');
    $gebruiker = new Gebruiker('2', 'Laeremans', 'Tom', 'z', 'x', '20');

    session_start();

    $_SESSION['gebruiker'] = $gebruiker;
    header('location: Addfile.php')
?>
