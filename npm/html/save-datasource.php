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

	if( !(isset($_POST['url']))) 
	{
			echo '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> Datasource URL not valid.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';					
			exit();
	}
	else
		$url = rtrim($_POST['url'], "/");
	

	$file = "/etc/fpm/datasource.json";
	$contents = '{"type":"elastic", "url":"'.$url.'"}';

	file_put_contents($file, $contents);

		echo '
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>Success!</strong> Datasource saved.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';	

?>
