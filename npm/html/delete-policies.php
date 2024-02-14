<?php 

session_start();

if (!isset($_SESSION['auth']))
{
   header("Location: login.php"); 
   exit();
}
if (!$_SESSION["auth"])
{
   header("Location: login.php"); 
   exit();
}

if (isset($_POST['all']))
   $del_all = $_POST['all'];
else
   $del_all = "no";

if ($del_all === "no")
{
   if (isset($_POST['policy']))
      $policy = $_POST['policy'];
   else
   {
      header("HTTP/1.1 500 Policy not set");
      exit();
   }
}





function recursiveRemove($dir) {
    $structure = glob(rtrim($dir, "/").'/*');
    if (is_array($structure)) {
        foreach($structure as $file) {
            if (is_dir($file)) 
               recursiveRemove($file);
            elseif (is_file($file)) 
               unlink($file);
        }
    }
    rmdir($dir);
}

$dir = getcwd() . '/config_files/';
$reports_structure = glob(rtrim($dir, "/").'/*');


if ($del_all=="yes")
{
   if (is_array($reports_structure)) 
   {
      foreach($reports_structure as $file) 
      {
         if (is_dir($file)) 
            recursiveRemove($file);
         elseif (is_file($file)) 
            unlink($file);
      }
   }
   header("HTTP/1.1 200 OK");
}
else
{
   $length = strlen($policy);
   if (is_array($reports_structure)) 
   {
      foreach($reports_structure as $file) 
      {
         if ( substr($file, -$length)  == $policy)
         {
            if (is_dir($file)) 
               recursiveRemove($file);
            elseif (is_file($file)) 
               unlink($file);
         }

      }
   }
   header("HTTP/1.1 200 OK");

}
?>