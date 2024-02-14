<?php

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
         $result["msg"]= $httpcode . " HTTP code received while downloading the policy '".$policy. "' from " .$project."/".$path;
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

	if( !(isset($_POST['policy'])) || !(isset($_POST['uuid'])) ||  !(isset($_POST['comment'])))
	{
         header("HTTP/1.1 500 Variables Not Defined");					
			exit();
	}
	else
	{
      $policy = $_POST['policy'];
      $uuid = $_POST['uuid'];
      $comment = base64_decode($_POST['comment']);
	}

	// Read the Policy and Base64 Encode it.

   $file = "config_files/".$_POST["policy"]."/".$_POST["policy"];
   $policy_data = base64_encode(file_get_contents($file));

	// Read the JSON GIT file 
	$json = file_get_contents('/etc/fpm/git.json');
	$json_data = json_decode($json,true);

   
   $found_git = "false";
   foreach($json_data as $git)
   {
      # Get all details for the Git to be used.
      if($git["uuid"] == $uuid)
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
         <strong>Failed!</strong> Git not found on list. Click <a href="settings.php">here</a> to add it..
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';						
      exit();
   }

    ##-----------------------   Update Policy   ----------------------------
	if ($type=="gitlab")
	{ 

      # create the payload to send to Gitlab
		$payload = '{"encoding":"base64", "branch": "'.$branch.'", "content": "'.$policy_data.'", "commit_message": "'.$comment.'"}';

		# run function that will upload the updated file.
		$response = update_policy_gitlab($git_fqdn, $project, $token, $id, $path, $policy, $branch, $payload);

      if ($response["status"]==1)
      {
         echo '
         <div class="alert alert-success alert-dismissible fade show" role="alert">
         <b>Success!</b> '.$response["msg"].'
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

		# create the payload to send to Gitlab
		$payload = '{"branch": "'.$branch.'", "content": "'.$policy_data.'", "sha": "'.$response["policy"]["sha"].'", "message": "'.$comment.'"}';

		# run function that will upload the updated file.
		$response = update_policy_gitea($git_fqdn, $project, $token, $path, $policy, $branch, $payload);

      if ($response["status"]==1)
      {
         echo '
         <div class="alert alert-success alert-dismissible fade show" role="alert">
         <b>Success!</b> '.$response["msg"].'
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

   if ($type == "bitbucket")
   {
      #location of the file that we will be uploading
      $new_policy = "config_files/".$_POST["policy"]."/".$_POST["policy"];

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
            <b>Success!</b> '.$response["policy"]["id"].'
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

  
   $file= "config_files/".$policy."/sync";
   unlink($file);
   sleep (1);
?>

