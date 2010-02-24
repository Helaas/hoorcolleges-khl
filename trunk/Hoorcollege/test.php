<?php
    include_once('./includes/kern.php');
    if (isset($_POST["submit"])){
        $gebruiker = new Gebruiker($_POST["id"], 'Beslic', 'Filip', 'lala', 'sdf', $_POST["level"]);

        session_start();

        $_SESSION['gebruiker'] = $gebruiker;
        if ($_POST["level"] == 99)
            header('location: admin.php');
        elseif ($_POST["level"] == 40)
            header('location: ./lector/index.php');
        else
            header('location: index.php');
        exit();
    }
?>

<form action="test.php" method="POST">
    Id: <input type="text" name="id" value="1" size="4" /> Niveau: <select name="level">
        <option value="1">Gebruiker (1)</option>
        <option value="40">Lector (40)</option>
        <option value="99">Admin (99)</option>
    </select>
    <input type="submit" value="Maak sessie" name="submit" />
</form>