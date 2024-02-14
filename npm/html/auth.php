<?php

	session_start();
  $_SESSION["auth"] = false;
	if(empty($_POST['username']) || empty($_POST['password']) )
	{
    $_SESSION["error"] = 1;
    $_SESSION["error_msg"] = "Invalid credentials";
		header("Location: login.php"); 
		exit();
	}

  if( !(isset($_POST['username'])) || !(isset($_POST['password'])) )
	{
    $_SESSION["error"] = 1;
    $_SESSION["error_msg"] = "Invalid credentials";
		header("Location: login.php"); 
		exit();
	}


	$user=$_POST["username"];
	$pass=$_POST["password"];

  if (!file_exists('/etc/fpm/user.json'))
  {
    file_put_contents("/etc/fpm/user.json", "admin");
  }
  if (!file_exists('/etc/fpm/datasource.json'))
  {
    file_put_contents("/etc/fpm/datasource.json", "");
  }
  if (!file_exists('/etc/fpm/gitlab.json'))
  {
    file_put_contents("/etc/fpm/gitlab.json", "[]");
  }

  $saved_password = trim(file_get_contents('/etc/fpm/user.json'));
  $authenticated=false;

  if (strcmp($saved_password, $pass) == 0 &&	$user=="admin")
  {
    $authenticated=true;
  }

  if ($authenticated)
  {
    $_SESSION["login"] = 'yes';
    $ds_content=file_get_contents("/etc/fpm/datasource.json");
    $ds_content_json = json_decode($ds_content, true);
    $git_content=file_get_contents("/etc/fpm/gitlab.json");
    $git_content_json = json_decode($git_content, true);
    
    if(sizeof($ds_content_json)==0 || sizeof($git_content_json)==0) {
      $_SESSION["auth"] = true;
      header("Location: settings.php"); 
    }
    else
    {
      $_SESSION["auth"] = true;
      if(isset($_SESSION['support_id']))
        header("Location: violation.php");
      else
        header("Location: index.php");
    }
  }
  else
  {
    $_SESSION["auth"] = false;
    $_SESSION["error"] = 1;
    $_SESSION["error_msg"] = "Wrong username/password";
    header("Location: login.php"); 
  }

  exit();
?>
