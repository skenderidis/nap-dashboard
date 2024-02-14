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

	// Read the JSON GIT file 
	$json = file_get_contents('/etc/fpm/git.json');
	$json_data = json_decode($json,true);

	# If request doesn't contain the git variable return an error.
	# Git variable is meant to be a UUID.
	if( !(isset($_POST['git'])) )
	{
			echo '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong>Git Destination not Set.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';					
			exit();
	}
	else
	{
		# Run through all the git entries and try to match the ID.
		$found_git = "false";
		foreach($json_data as $git)
		{
			# Get all details for the Git to be used.
			if($git["uuid"] == $_POST['git'])
			{
				$found_git="true";
				$token = $git["token"];
				$git_fqdn = $git["fqdn"];
				$project = $git["project"];
				$branch = $git["branch"];
				$format = $git["format"];
				$path = $git["path"];
				$id = $git["id"];
            $type = $git["type"];
				if ($path == ".")
					$path = "";
			}
		}
		# If the ID is not found return an error.
		if ($found_git == "false")
		{
			echo '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> Git not found on list. Click <a href="settings.php">here</a> to add it..
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';						
			exit();
		}
	}
	# If request doesn't contain the policy name as a variable, return an error.
	# We assume that the policy name is going to match with the file name and 
	# the file extension will be either .json or .yaml depending on the format.
	if( !(isset($_POST['policy'])) )
	{
		echo '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> Policy not set.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';				
		exit();
	}
	else
		$policy = $_POST['policy'].".". strtolower($format);

	# The policy_data indicate what changed are required to be done on the policy.
	# Without this we return an error. 
	if( !(isset($_POST['policy_data'])) )
	{
		echo '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong>Data are missing from the request..
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';			
		exit();
	}
	else
		$policy_data = $_POST['policy_data'];

	# Check if the support ID is sent as a parameter.
		if( !(isset($_POST['support_id'])) )
		$support_id = "none";
	else
		$support_id = $_POST['support_id'];

	# Check if the Git comment is sent as a parameter.
		if( !(isset($_POST['comment'])) )
		$comment = "none";
	else
		$comment = base64_decode($_POST['comment']);
		
	$comment = "(" . $support_id . ") - ". $comment;

	function json_validate($string)
	{
			// decode the JSON data
			$result = json_decode($string);

			// switch and check possible JSON errors
			switch (json_last_error()) {
					case JSON_ERROR_NONE:
							$error = ''; // JSON is valid // No error has occurred
							break;
					case JSON_ERROR_DEPTH:
							$error = 'The maximum stack depth has been exceeded.';
							break;
					case JSON_ERROR_STATE_MISMATCH:
							$error = 'Invalid or malformed JSON.';
							break;
					case JSON_ERROR_CTRL_CHAR:
							$error = 'Control character error, possibly incorrectly encoded.';
							break;
					case JSON_ERROR_SYNTAX:
							$error = 'Syntax error, malformed JSON.';
							break;
					// PHP >= 5.3.3
					case JSON_ERROR_UTF8:
							$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
							break;
					// PHP >= 5.5.0
					case JSON_ERROR_RECURSION:
							$error = 'One or more recursive references in the value to be encoded.';
							break;
					// PHP >= 5.5.0
					case JSON_ERROR_INF_OR_NAN:
							$error = 'One or more NAN or INF values in the value to be encoded.';
							break;
					case JSON_ERROR_UNSUPPORTED_TYPE:
							$error = 'A value of a type that cannot be encoded was given.';
							break;
					default:
							$error = 'Unknown JSON error occured.';
							break;
			}

			if ($error !== '') {
					// throw the Exception or exit // or whatever :)
					exit($error);
			}

			// everything is OK
			return $result;
	}
	
	# This function will download the policy from Gitlab in Base64 format.
	function download_policy_gitlab($git_fqdn, $project, $token, $id, $path, $policy, $branch) {
      ### --------  Setup headers required  -------- ####
		$headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; ',
			'PRIVATE-TOKEN: ' . $token
		);

      ### --------  API endpoint -------- ####
      if ($path=="")
         $url = $git_fqdn."/api/v4/projects/".$id."/repository/files/".urlencode($policy)."?ref=".$branch;
      else
         $url = $git_fqdn."/api/v4/projects/".$id."/repository/files/".urlencode($path."/".$policy)."?ref=".$branch;

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

      ###  -------------- Execute the transaction ------------- ####   
      $curl_response = curl_exec($curl);

      ###  -------------- Create an array to store the result ------------- ####
      $result  = array("status" => 0, "msg" => "-");
      
      ### -------------- verify that the transaction was successful  -------------- ###
      if (curl_errno($curl))
      {
         $result["status"]=0;
         $result["msg"]=curl_error($curl);
         curl_close($curl);
         return $result;
      }


      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      ###  --------------  Wrong Password -------------- ####
      if ($httpcode == 401) 
      {
         $result["status"]=0;
         $result["msg"]="Error! Authentication failure";
         curl_close($curl);
         return $result;
      } 
		curl_close($curl);

      
      if ($httpcode == 200)
      {
         ## -------------- Save response to a JSON variable  -------------- ###
         $policy_data = json_decode($curl_response, true);

         $result["status"]=1;
         $result["msg"]="Success! Policy downloaded.";
         $result["policy"]=$policy_data;
         return $result;
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while getting the policy '".$policy. "' from " .$project."/".$path;
         return $result;
      }

	}
   # This function will upload the policy file to Gitlab in Base64 format.
   function update_policy_gitlab($git_fqdn, $project, $token, $id, $path, $policy, $branch, $payload) {
      ### --------  Setup headers required -------- ####      
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'PRIVATE-TOKEN: ' . $token
      );
      ### --------  API endpoint -------- ####
      if ($path == "")
         $url = $git_fqdn."/api/v4/projects/".$id."/repository/files/".urlencode($policy)."?ref=".$branch;
      else
         $url = $git_fqdn."/api/v4/projects/".$id."/repository/files/".urlencode($path."/".$policy)."?ref=".$branch;

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   
      ###  -------------- Execute the transaction ------------- ####
		$curl_response = curl_exec($curl);

      ###  -------------- Create an array to store the result ------------- ####
      $result  = array("status" => 0, "msg" => "-");


      ### -------------- verify that the transaction was successful  -------------- ###
      if (curl_errno($curl))
      {
         $result["status"]=0;
         $result["msg"]=curl_error($curl);
         curl_close($curl);
         return $result;
      }

      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


      ###  --------------  Wrong Password -------------- ####
      if ($httpcode == 401) 
      {
         $result["status"]=0;
         $result["msg"]="Error! Authentication failure";
         curl_close($curl);
         return $result;
      } 
		curl_close($curl);

      
      if ($httpcode==200)
      { 
         # Success if HTTP code 200   
         $result["status"]=1;
         $result["msg"]="Policy updated";
         return $result; ##  Return Success
      }      
      elseif ($httpcode==400)
      {
         $error = json_decode($curl_response, true);

         $result["status"]=0;
         $result["msg"]= "Failed updating policy. Response received from ".$git_fqdn. " was '400' and the error message is: " . $error["message"]; 
         return $result; ##  Return Error  
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= "Failed updating policy. Response received from ".$git_fqdn. " was '" . $httpcode ."'"; 
         return $result; ##  Return Error         
      }      
   }
	# Download the file from Gitea in Base64 format.
	function download_policy_gitea($git_fqdn, $project, $token, $path, $policy, $branch) {
      ### --------  Setup headers required  -------- ####
		$headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; ',
			'Authorization: token ' . $token
		);

      ### --------  API endpoint -------- ####
      if ($path=="")
         $url = $git_fqdn."/api/v1/repos/".$project."/contents/".urlencode($policy)."?ref=".$branch;
      else
         $url = $git_fqdn."/api/v1/repos/".$project."/contents/".urlencode($path."/".$policy)."?ref=".$branch;

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

      ###  -------------- Execute the transaction ------------- ####
      $curl_response = curl_exec($curl);
      
      ###  -------------- Create an array to store the result ------------- ####
      $result  = array("status" => 0, "msg" => "-");
      
      ### -------------- verify that the transaction was successful  -------------- ###
      if (curl_errno($curl))
      {
         $result["status"]=0;
         $result["msg"]=curl_error($curl);
         curl_close($curl);
         return $result;
      }

      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      ###  --------------  Wrong Password -------------- ####
      if ($httpcode == 401) 
      {
         $result["status"]=0;
         $result["msg"]="Error! Authentication failure";
         curl_close($curl);
         return $result;
      } 
		curl_close($curl);


      if ($httpcode == 200)
      {
         ## -------------- Save response to a JSON variable  -------------- ###
         $policy_data = json_decode($curl_response, true);
         $result["status"]=1;
         $result["msg"]="Success! Policy downloaded.";
         $result["policy"]=$policy_data;
         return $result;
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while getting the policy '".$policy. "' from " .$project."/".$path;
         return $result;
      }

	}   
   # This function will upload the policy file to Gitea in Base64 format.
   function update_policy_gitea($git_fqdn, $project, $token, $path, $policy, $branch, $payload) {
      ### --------  Setup headers required -------- ####
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'Authorization: token ' . $token
      );

      ### --------  API endpoint -------- ####
      if ($path == "")
         $url = $git_fqdn."/api/v1/repos/".$project."/contents/".urlencode($policy)."?ref=".$branch;
      else
         $url = $git_fqdn."/api/v1/repos/".$project."/contents/".urlencode($path."/".$policy)."?ref=".$branch;
      
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   
      ###  -------------- Execute the transaction ------------- ####
      $curl_response = curl_exec($curl);

      ###  -------------- Create an array to store the result ------------- ####
      $result  = array("status" => 0, "msg" => "-");
      
      
      ### -------------- verify that the transaction was successful  -------------- ###
      if (curl_errno($curl))
      {
         $result["status"]=0;
         $result["msg"]=curl_error($curl);
         curl_close($curl);
         return $result;
      }

      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


      ###  --------------  Wrong Password -------------- ####
      if ($httpcode == 401) 
      {
         $result["status"]=0;
         $result["msg"]="Error! Authentication failure";
         curl_close($curl);
         return $result;
      } 
		curl_close($curl);
      
      if ($httpcode==200)
      { 
         # Success if HTTP code 200   
         $result["status"]=1;
         $result["msg"]="Policy updated";
         return $result; ##  Return Success
      }      
      else
      {
         $result["status"]=0;
         $result["msg"]= "Failed updating policy. Response received from ".$git_fqdn. " was '" . $httpcode ."'"; 
         return $result; ##  Return Error         
      }      
   }
	# Download the file from BitBucket in plain text format.
   function download_policy_bitbucket($git_fqdn, $project_repo, $token, $key, $path, $policy, $branch) {
      
      ### --------  Split Project/Repo  -------- ####
      $pos = strpos($project_repo, "/");
      $project = substr($project_repo, 0, $pos);
      $repo = substr($project_repo, $pos+1);

      ### --------  Setup headers required  -------- ####
      $headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; ',
         'Authorization: Bearer ' . $token
      );
      ### --------  API endpoint -------- ####
      if ($path=="")
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/raw/".rawurlencode($policy)."?at=refs/heads/".$branch;            
      else
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/raw/".$path."/".rawurlencode($policy)."?at=refs/heads/".$branch;
      
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl,CURLOPT_TIMEOUT,5);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   
      ###  -------------- Execute the transaction ------------- ####
		$curl_response = curl_exec($curl);

      ###  -------------- Create an array to store the result ------------- ####
      $result  = array("status" => 0, "msg" => "-");


      ### -------------- verify that the transaction was successful  -------------- ###
      if (curl_errno($curl))
      {
         $result["status"]=0;
         $result["msg"]=curl_error($curl);
         curl_close($curl);
         return $result;
      }

      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      ###  --------------  Wrong Password -------------- ####
      if ($httpcode == 401) 
      {
         $result["status"]=0;
         $result["msg"]="Error! Authentication failure";
         curl_close($curl);
         return $result;
      } 
		curl_close($curl);

      if ($httpcode == 200)
      {
         $result["status"]=1;
         $result["msg"]="Success! Policy downloaded.";
         $result["policy"]=$curl_response;
         return $result;
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while getting the policy '".$url. "' from " .$project_repo."/".$path;
         return $result;
      }
	}
   # This function will get the latest commitid for the branch.
   function get_commit_bitbucket($git_fqdn, $project_repo, $token, $key, $branch) {
      ### --------  Split Project/Repo  -------- #### 
      $pos = strpos($project_repo, "/");
      $project = substr($project_repo, 0, $pos);
      $repo = substr($project_repo, $pos+1);

      ### --------  Setup headers required  -------- ####
      $headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; ',
         'Authorization: Bearer ' . $token
		);

      ### --------  API endpoint -------- ####
      $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/branches";            

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl,CURLOPT_TIMEOUT,5);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   
      ###  -------------- Execute the transaction ------------- ####
		$curl_response = curl_exec($curl);

      ###  -------------- Create an array to store the result ------------- ####
      $result  = array("status" => 0, "msg" => "-");

      ### -------------- verify that the transaction was successful  -------------- ###
      if (curl_errno($curl))
      {
         $result["status"]=0;
         $result["msg"]=curl_error($curl);
         curl_close($curl);
         return $result;
      }

      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      ###  --------------  Wrong Password -------------- ####
      if ($httpcode == 401) 
      {
         $result["status"]=0;
         $result["msg"]="Error! Authentication failure";
         curl_close($curl);
         return $result;
      } 
		curl_close($curl);

      
      if ($httpcode == 200)
      {
         $data = json_decode($curl_response, true);

         foreach($data["values"] as $my_branch)
         {
            if($my_branch["displayId"]==$branch)
            {
               $result["status"]=1;
               $result["msg"]="Success";
               $result["latestCommit"]=$my_branch["latestCommit"];
               return $result;                  
            }
         }
         $result["status"]=0;
         $result["msg"]="Branch not found";
         return $result;
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while getting the latest commit '".$git_fqdn. "' from " .$project_repo." and branch ".$branch;
         return $result;
      }
	}
   # This function will upload the policy file to bitbucket in plain text format.
   function update_policy_bitbucket($git_fqdn, $project_repo, $token, $key, $path, $policy, $branch, $file_location, $commit_id, $comment) {

      ### --------  Split Project/Repo  -------- ####      
      $pos = strpos($project_repo, "/");
      $project = substr($project_repo, 0, $pos);
      $repo = substr($project_repo, $pos+1);

      ### --------  Setup headers required  -------- ####
      $headers = array(
			'Content-Type: multipart/form-data',
			'Accept: */*; ',
         'Authorization: Bearer ' . $token
		);

      ### --------  Payload of new policy -------- ####
      $payload = [
         'content' => curl_file_create($file_location, "text/plain"),
         'message' => $comment,
         'branch' => "main",
         'sourceCommitId' => $commit_id
      ];
      
      ### --------  API endpoint -------- ####
      if ($path=="")
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/browse/".rawurlencode($policy);      
      else
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/browse/".$path."/".rawurlencode($policy);

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);         
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_TIMEOUT,5);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   
      ###  -------------- Execute the transaction ------------- ####
		$curl_response = curl_exec($curl);

      ###  -------------- Create an array to store the result ------------- ####
      $result  = array("status" => 0, "msg" => "-");


      ### -------------- verify that the transaction was successful  -------------- ###
      if (curl_errno($curl))
      {
         $result["status"]=0;
         $result["msg"]=curl_error($curl);
         curl_close($curl);
         return $result;
      }

      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      ###  --------------  Wrong Password -------------- ####
      if ($httpcode == 401) 
      {
         $result["status"]=0;
         $result["msg"]="Error! Authentication failure";
         curl_close($curl);
         return $result;
      } 
		curl_close($curl);

      
      if ($httpcode == 200)
      {
         $result["status"]=1;
         $result["msg"]="Success!";
         $result["policy"] = json_decode($curl_response,true);
         return $result;
      }
      else
      {
         $bit_bucket_error = json_decode($curl_response,true);
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while updating the policy '".$url. "' from " .$project_repo."/".$path ."' and the error message was: ". $bit_bucket_error["errors"][0]["message"];
         return $result;
      }
	} 

   ##-----------------------  Download Policy  ----------------------------
   if ($type == "gitlab")
   {
	   #### Verify that the Policy exists and get contents exists
      $response = download_policy_gitlab($git_fqdn, $project, $token, $id,  $path, $policy, $branch);

      # if the result of the get_policy function is 0 then it is an issue
      if ($response["status"] == 0)
      {
         echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';         
         exit();
      }
      # store the policy in a file
      file_put_contents("policy",base64_decode($response["policy"]["content"]));  
   }

   if ($type == "gitea")
   {
	   #### Verify that the Policy exists and get contents exists
      $response = download_policy_gitea($git_fqdn, $project, $token, $path, $policy, $branch);
     
      # if the result of the get_policy function is 0 then it is an issue
      if ($response["status"] == 0)
      {
         echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';         
         exit();
      }      
      
      file_put_contents("policy",base64_decode($response["policy"]["content"]));  # store the policy to a file      
      #SHA will be used later to update the policy
      $sha = $response["policy"]["sha"];
   }

   if ($type == "bitbucket")
   {
      $response = download_policy_bitbucket($git_fqdn, $project, $token, $id, $path, $policy, $branch);      
      if ($response["status"] == 0)
      {
         echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';         
         exit();
      }
      # store the policy in a file
      file_put_contents("policy",$response["policy"]);
   }

   ##-----------------------  Python merge script  ----------------------------

	# Run the python script to make the policy changes
	$run_python_script = 'python3 modify-nap.py ' . strtolower($format) . ' ' . $policy_data ;
	$command = escapeshellcmd($run_python_script);
	$output = shell_exec($command);
	
	# if the output of the script includes Success word then the script was successful.
	if(!(strpos($output, 'Success') !== false))
	{
		echo '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
  		Python Error: '.$output.'
  		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';
   	# Delete Temp files
      unlink('policy');
      unlink('policy_mod');
      exit();
	}

   ##-----------------------   Update Policy   ----------------------------
	if ($type=="gitlab")
	{ 
      # the python script will have created a file called "policy_mod".
      $new_policy = base64_encode(file_get_contents('policy_mod'));

		# create the payload to send to Gitlab
		$payload = '{"encoding":"base64", "branch": "'.$branch.'", "content": "'.$new_policy.'", "commit_message": "'.$comment.'"}';

		# run function that will upload the updated file.
		$response = update_policy_gitlab($git_fqdn, $project, $token, $id, $path, $policy, $branch, $payload);
      
      if ($response["status"]==1)
      {
         echo '
         <div class="alert alert-success alert-dismissible fade show" role="alert">
         <b>Success!</b> '.$output.'
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
      }
      else
      {
         echo '
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
         <b>Failed!</b> '.$response["msg"].'
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
      }

	}
	
	if ($type=="gitea")
	{ 
      # the python script will have created a file called "policy_mod".
      $new_policy = base64_encode(file_get_contents('policy_mod'));      
		
      # create the payload to send to Gitea
		$payload = '{"branch": "'.$branch.'", "content": "'.$new_policy.'", "sha": "'.$sha.'", "commit_message": "'.$comment.'"}';

		# run function that will upload the updated file.
		$response = update_policy_gitea($git_fqdn, $project, $token, $path, $policy, $branch, $payload);

      if ($response["status"]==1)
      {
         echo '
         <div class="alert alert-success alert-dismissible fade show" role="alert">
         <b>Success!</b> '.$output.'
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
      }
      else
      {
         echo '
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
         <b>Failed!</b> '.$response["msg"].'
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
      }
	}

	if ($type=="bitbucket")
	{ 
      # the python script will have created a file called "policy_mod". For bitbucket we only need to reference the filename.
      $new_policy = 'policy_mod';
      
      # Get latest Commit ID from branch
		$commit = get_commit_bitbucket($git_fqdn, $project, $token, $id, $branch) ;
      
      if ($commit["status"]==0)
      {
         echo '
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <b>Failed!</b> '.$response["msg"].'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
         exit();
      }

      $response = update_policy_bitbucket($git_fqdn, $project, $token, $id, $path, $policy, $branch, $new_policy, $commit["latestCommit"], $comment);
      if ($response["status"] == 1)
      {
         echo '
         <div class="alert alert-success alert-dismissible fade show" role="alert">
            <b>Success!</b> '.$output.'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
      }
      else
      {
         echo '
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <b>Failed!</b> '.$response["msg"].'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
      }
	}


   # Delete Temp files
   unlink('policy');
   unlink('policy_mod');      
   exit();

?>
