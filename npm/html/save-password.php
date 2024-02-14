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

	 
	if( !(isset($_POST['current_password']))) 
	{
			echo '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> Current password not valid.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';					
			exit();
	}
	else
		$current_password = $_POST['current_password'];


	if( !(isset($_POST['new_password'])) ||  !(isset($_POST['verify_password'])))
	{
			echo '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> Passwords dont match.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';					
			exit();
	}
	if( $_POST['new_password'] !== $_POST['verify_password'] )
	{
		echo '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> Passwords dont match.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';					
		exit();
		
	}
	else
		$new_password = $_POST['new_password'];


	$file = "/etc/fpm/user.json";
	$saved_password = file_get_contents($file);

	if (strcmp(trim($saved_password), $current_password) !== 0)
	{
		echo '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> Cannot authenticate the user.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';					
		exit();
		
	}		
	else
		file_put_contents($file, $new_password);

		echo '
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>Success!</strong> Password changed.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';	

?>
