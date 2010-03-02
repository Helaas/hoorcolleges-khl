<?php

$output = array();
$returnvar;
if(function_exists('exec')) {
    system("echo \"it works\"");

      error_reporting(E_ALL);
    echo exec('sh /home/birgit/scriptFlv.sh',$output,$returnvar);
    echo $returnvar . "</br>";
    print_r($output);
    system("echo \"it works\"");
}
////
//error_reporting(E_ALL);
//    $src = "/home/birgit/ffmpeg/sdcg204.avi";
//    $command = "/usr/bin/ffmpeg -i " . $src. " 2>&1";
//    echo "<B>",$command,"</B><br/>";
//
//    echo "</pre><br/>system:<br/><pre>";
//    echo system($command);
//
//    echo "</pre><br/>shell_exec:<br/><pre>";
//    echo shell_exec($command);
//
//    echo "</pre><br/>passthru:<br/><pre>";
//    passthru($command);
//
//    echo "</pre><br/>exec:<br/><pre>";
//    $output = array();
//    exec($command,$output,$status);
//    foreach($output AS $o)
//    {
//            echo $o , "<br/>";
//    }
//    echo "</pre><br/>popen:<br/><pre>";
//    $handle = popen($command,'r');
//    echo fread($handle,1048576);
//    pclose($handle);
//    echo "</pre><br/>";
//
?>
