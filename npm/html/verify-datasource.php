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

	 
	function verify_elastic($url) {
		$headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; ',
			);

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl,CURLOPT_TIMEOUT,3);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		
			$curl_response = curl_exec($curl);
		
			if ($curl_response === false) {
				$info = curl_getinfo($curl);
				curl_close($curl);
				return -2;
			}
			curl_close($curl);
			$result = json_decode($curl_response, true);
			$validated = False;

			if (array_key_exists("cluster_name", $result))
			{
				$validated = True;
			}
			if (!$validated )
				return -1;
			
			return 0;
	}


	if( !(isset($_POST['url'])) )
	{
			echo '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong>Datasource URL not Set.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';					
			exit();
	}
	else
	{
			$url = $_POST['url'];
	}



	#### Verify that the Project exists and get ID
	$id = verify_elastic($url);
	if ($id == -2)
	{
		echo '
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					Unable to connect to <strong>"'.$url.'"</strong>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		exit();
	}

	if ($id == -1)
	{

		echo '
			<div class="alert alert-warning alert-dismissible fade show" role="alert">
				Wrong URL (<b>"'.$url.'"</b>)å
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';
		exit();
	}
	else
	{
	
		echo '
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<b>Success!</b> Connection to Elasticsearch was successful
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';
		
	}
	

?>
