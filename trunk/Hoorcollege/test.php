<?php
    include_once('gebruiker.class.php'); 
    $gebruiker = new Gebruiker('1', 'Beslic', 'Filip', 'lala', 'sdf', '99');

    session_start();

    $_SESSION['gebruiker'] = $gebruiker;
    header('location: admin.php')
?>
