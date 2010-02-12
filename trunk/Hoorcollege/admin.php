<?php    
    include_once('./includes/TinyButStrong.php');
    include_once('./includes/gebruiker.class.php');
    $TBS = new clsTinyButStrong;

    session_start();

    $foutboodschap = '';
   
    if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == "99" && isset ($_GET['actie'])) {
        //bepalen welke content geladen moet worden
        $config["pagina"] = "/admin/" . $_GET['actie'] . ".html";
       
        if($_GET['actie'] == 'student') {
            //table aanmaken voor de studentenoverzicht op student.html
            $cnx_id = mysql_connect('localhost','web_k_be','pJ7xtbvU');
            mysql_select_db('web_k_be',$cnx_id) ;
            $TBS->LoadTemplate('./html/template.html') ;
            $TBS->MergeBlock('blk1', $cnx_id, 'SELECT * FROM `hoorcollege_gebruiker`');
            mysql_close($cnx_id) ;
            $TBS->Show() ;
        }
        else {
            $TBS->LoadTemplate('./html/template.html') ;
            $TBS->Show() ;
        }
               
        
    }
    else if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == "99" ) {
        //bepalen welke content geladen moet worden
        $config["pagina"] = "./admin/admin.html";
        
        if(isset ($_POST['knopvoegtoe'])) {
            //controleren of alle gegevens correct zijn
            if(true) {
                $foutboodschap = "Gegevens zijn niet correct ingevuld en/of email adres is al aan een andere gebruiker toegekent!";
                //content wijzigen, omdat er een fout is, moet terug de content van student.html opgehaald worden
                $config["pagina"] = "./admin/student.html";
            }
            else {
                //gebruiker toevoegen aan databank
            }
        }             
        
        $gebruiker = $_SESSION['gebruiker'];        
        $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }
    else {
        echo 'Niet ingelogd';
    }
    
?>
