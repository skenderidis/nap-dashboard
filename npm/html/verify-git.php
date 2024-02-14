<?php

   ## ----------------  Check that the session exists and the user is authenticated  -------------------###
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

   #--------   Validate that all the POST parameters are received. Otherwise give back an error
   $error="none";
	if( !(isset($_POST['git_fqdn'])) )
      $error="Git FQDN variable missing";
	else
		$git_fqdn = $_POST['git_fqdn'];

   if( !(isset($_POST['project_name'])) || $_POST['project_name']=="")
      $error="ProjectName variable missing";
	else
		$project_name = $_POST['project_name'];
	
	if( !(isset($_POST['token'])) || $_POST['token']=="" )
      $error = "Token variable missing";
	else
		$token = $_POST['token'];
	
	if( !(isset($_POST['branch'])) || $_POST['branch']=="" )
      $error = "Branch variable missing";
	else
		$branch = $_POST['branch'];
	
   if( !(isset($_POST['format'])))
      $error = "Format variable missing";
	else
		$format = $_POST['format'];
	

	if( !(isset($_POST['path'])) )
      $error = "Path variable missing";
	else
	{
		$path = $_POST['path'];
		if ($path == "")
			$path= ".";
	}

	if( !(isset($_POST['type'])) )
      $error = "Type variable missing";
  
	else
	{
		$type = $_POST['type'];
	}

   if($error!="none")
   {
      echo '
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Failed!</strong> '.$error.'.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
      exit();
   }
   #--------------------------------------------------------------------------------------------------------
	
	function get_id_gitlab($git_fqdn, $project_repo, $token) {
      ### --------  Setup headers required -------- ####
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'PRIVATE-TOKEN: '.$token
      );
      
      ### --------  API endpoint -------- ####
      $url = $git_fqdn."/api/v4/projects";
      
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl,CURLOPT_TIMEOUT,4);
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
         $response = json_decode($curl_response, true);
          
         /* ----- Gitlab returns an array of all the projects. We will scan all of them 
         to match the Project/Name with the "path_with_namespace" returned by gitlab ---- */

         foreach ($response as $repo)
         {  
            if ($repo["path_with_namespace"] == $project_repo)
            { 
               # Success if it matches... The project/repo exists   
               $result["status"]=1;
               $result["msg"]="Success! Repo found";
               $result["id"]=$repo["id"];
               return $result; ##  Return Success (Repo ID)
            }
         }
         $result["status"]=0;
         $result["msg"]= "Project/Repo Not found (".$project_repo.") on the list received"; 
         return $result; ##  Return Error         
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= "Project/Repo Not found (".$project_repo."). Response received from ".$git_fqdn. " was '" . $httpcode ."'"; 
         return $result; ##  Return Error
      }
	}

   function verify_branch_gitlab($git_fqdn, $project_repo, $token, $id, $branch) {
      ### --------  Setup headers required  -------- ####
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'PRIVATE-TOKEN: ' . $token
         );
      ### --------  API endpoint -------- ####
      $url = $git_fqdn."/api/v4/projects/".$id."/repository/branches";

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
         ## -------------- Save response to a JSON variable  -------------- ###
         $response = json_decode($curl_response, true);
         /* ----- Gitlab returns an array of all the branches.
          We will scan all of them to match the Name  ---- */

         foreach ($response as $temp_path)
         {
            if ($temp_path["name"] == $branch)
            { 
               # Success if it matches... The folder/path exists   
               $result["status"]=1;
               $result["msg"]="Success! Branch Found";
               return $result; ##  Return Success
            }
         }
         $result["status"]=0;
         $result["msg"]= "Branch (".$branch.") not found on the list received."; 
         return $result; ##  Return Error
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= "Error while receiving the branch list. Response received from ".$git_fqdn. " was '" . $httpcode ."'"; 
         return $result; ##  Return Error         
      }

	}

	function verify_path_gitlab($git_fqdn, $project_repo, $token, $id, $path, $branch) {
      ### --------  Setup headers required  -------- ####
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'PRIVATE-TOKEN: ' . $token
         );
      ### --------  API endpoint -------- ####
      $url = $git_fqdn."/api/v4/projects/".$id."/repository/tree?ref=".$branch;

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
         ## -------------- Save response to a JSON variable  -------------- ###
         $response = json_decode($curl_response, true);
         /* ----- Gitlab returns an array of all the files/folders.
          We will scan all of them 
         to match the Name and the Type should be Folder (tree) ---- */

         foreach ($response as $temp_path)
         {
            if ($temp_path["path"] == $path && $temp_path["type"] == "tree")
            { 
               # Success if it matches... The folder/path exists   
               $result["status"]=1;
               $result["msg"]="Success! Path Exists";
               return $result; ##  Return Success
            }
         }
         $result["status"]=0;
         $result["msg"]= "Folder (".$path.") not found on the list received."; 
         return $result; ##  Return Error
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= "Error while receiving the content list. Response received from ".$git_fqdn. " was '" . $httpcode ."'"; 
         return $result; ##  Return Error         
      }

	}

	function verify_repo_gitea ($git_fqdn, $project_repo, $token) {
      ### --------  Setup headers required  -------- ####
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'Authorization: token ' . $token
         );

      ### --------  API endpoint -------- ####
      $url = $git_fqdn."/api/v1/repos/".$project_repo;            
	
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl,CURLOPT_TIMEOUT,4);
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
         $response = json_decode($curl_response, true);
         # check if the "full_name" is equal to the project/repo"
         if ($response["full_name"]==$project_repo)
         {
            # Success if it matches... The project/repo exists   
            $result["status"]=1;
            $result["id"]=$response["id"];
            $result["msg"]="Success";
            return $result; ##  Return Success (Repo ID)
         }
         else
         {
            $result["status"]=0;
            $result["msg"]= "Project/Repo Not found (".$project_repo.") on the list received"; 
            return $result; ##  Return Error
         }
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= "Project/Repo Not found (".$project_repo."). Response received from ".$git_fqdn. " was '" . $httpcode ."'"; 
         return $result; ##  Return Error         
      }
	}

   function verify_path_gitea($git_fqdn, $project_repo, $token, $path, $branch) {
      ### --------  Setup headers required -------- ####
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'Authorization: token ' . $token
         );
      
      ### --------  API endpoint -------- ####
      $url = $git_fqdn."/api/v1/repos/".$project_repo."/contents?ref=".$branch;
      
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
         ## -------------- Save response to a JSON variable  -------------- ###
         $response = json_decode($curl_response, true);         
         
         ##  ----------  If the path is the root folder, then a simple 200-OK is enough to verify it ----#
         if ($path==".")
         {
            $result["status"]=1;
            $result["msg"]="Success";
            return $result; ##  Return Success
         }
         else
         {
            /* ----- Gitea returns an array of all the files/folders.
            We will scan all of them 
            to match the Name and the Type should be Folder (dir) ---- */

            foreach ($response as $temp_path)
            {
               if ($temp_path["path"] == $path && $temp_path["type"] == "dir")
               {
                  # Success if it matches... The folder/path exists   
                  $result["status"]=1;
                  $result["msg"]="Success";
                  return $result; ##  Return Success
               }
            }
            $result["status"]=0;
            $result["msg"]= "Folder (".$path.") not found on the list received."; 
            return $result; ##  Return Error            
         }
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= "Error while receiving the content list. Response received from ".$git_fqdn. " was '" . $httpcode ."'"; 
         return $result; ##  Return Error           
      }

	}

	function get_key_bitbucket($git_fqdn, $project_repo, $token) {

      ### -------- Split Project/Repo as bitbucket needs them seperately -------- ####
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
      $url = $git_fqdn."/rest/api/latest/projects/";            
	
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl,CURLOPT_TIMEOUT,4);
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
         $response = json_decode($curl_response, true);

         /* ----- BitBucket returns an array of all the projects.
         We will scan all of them, match the Name and retrieve the key. 
         The key is required by the subsequent transactions  */ 

         foreach ($response["values"] as $temp_project)
         {
            if ($temp_project["name"] == $project)
            {
               # Success if it matches... The Project exists   
               $result["status"]=1;
               $result["msg"]="Success! Project found";
               $result["key"] = $temp_project["key"];
               return $result; ##  Return Success (Repo Key)           
            }
         }
         $result["status"]=0;
         $result["msg"]= "Project Not found (".$project_repo.") on the list received"; 
         return $result; ##  Return Error    
      } 
      else
      {
         $result["status"]=0;
         $result["msg"]= "Error! Response received from ".$git_fqdn. " was '" . $httpcode ."'";
         return $result; ##  Return Error           
      }
	}

	function verify_repo_bitbucket($git_fqdn, $project_repo, $token, $key) {

      ### -------- Split Project/Repo as bitbucket needs them seperately -------- ####

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
      $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos";            
	
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl,CURLOPT_TIMEOUT,4);
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
         $response = json_decode($curl_response, true);

         /* ----- BitBucket returns an array of all the repos.
         We will scan all of them, match the Name   */ 
         foreach ($response["values"] as $temp_repo)
         {
            if ($temp_repo["name"] == $repo)
            {
               # Success if it matches... The Project exists   
               $result["status"]=1;
               $result["msg"] = "Success. Repo Found";
               return $result; ##  Return Success                
            }
         }
         $result["status"]=0;
         $result["msg"]= "Repo Not found (".$repo.") on the list received"; 
         return $result; ##  Return Error    
      } 
      else
      {
         $result["status"]=0;
         $result["msg"]= "Error! Response received from ".$git_fqdn. " was '" . $httpcode ."'";
         return $result; ##  Return Error        
   	}
   
   }

   function verify_path_bitbucket($git_fqdn, $project_repo, $token, $key, $path, $branch) {

      ### -------- Split Project/Repo as bitbucket needs them seperately -------- ####
      $pos = strpos($project_repo, "/");
      $project = substr($project_repo, 0, $pos);
      $repo = substr($project_repo, $pos+1);

      ### --------  Setup headers required  -------- ####
      $headers = array(
         'Content-Type: application/json',
         'Accept: application/json, text/javascript, */*; ',
         'Authorization: Bearer ' . $token
         );

       ### --------  API endpoint. Depending on the path, the endpoint is different. -------- ####        
      if ($path==".")
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/files?at=refs/heads/".$branch;            
      else
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/files/".$path."?at=refs/heads/".$branch;
      
 
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl,CURLOPT_TIMEOUT,4);
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
         # Success if it matches... The Project exists   
         $result["status"]=1;
         $result["msg"] = "Success! Path found";
         return $result; ##  Return Success
      } 
      elseif ($httpcode >= 400 && $httpcode < 500) 
      {
         ## -------------- Save response to a JSON variable to get the error details  -------------- ###
         $reponse = json_decode($curl_response, true); ## Save response to a JSON variable
         $result["status"]=0;
         $result["msg"]= "Error! Response received from ".$git_fqdn. " was '" . $httpcode ."' and the error message was: ". $reponse["errors"][0]["message"];
         return $result; ##  Return Error   
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= "Error! Response received from ".$git_fqdn. " was '" . $httpcode ."'";
         return $result; ##  Return Error            
      }

   }


   if ($type=="gitlab")
   {
      #### Verify that the Project exists and get ID
      $response = get_id_gitlab($git_fqdn, $project_name, $token);

      #### If status is 0 , then give the error description
      if ($response["status"]==0)
      {
         echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
         exit();
      }

      $id=$response["id"];

      $response = verify_branch_gitlab($git_fqdn, $project_name, $token, $id, $branch);

      #### If ID is not Integer, then give the error description
      if ($response["status"]==0)
      {
         echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
         exit();
      }

      if ($path != ".")
      {
         $response = verify_path_gitlab($git_fqdn, $project_name, $token, $id, $path, $branch);
         if ($response["status"]==0)
         {
            echo '
                  <div class="alert alert-warning alert-dismissible fade show" role="alert">
                     <b>Failed!</b> '.$response["msg"].'
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            exit();
         }
      }
      
      echo '{"id":"'.$id.'", "uuid":"'.md5($git_fqdn.$project_name.$path.$token.$branch.$format).'"}';


   }
 
   if ($type=="gitea")
   {

      #### Verify that the Project exists and get ID
      $response = verify_repo_gitea($git_fqdn, $project_name, $token,  $path, $branch);

      #### If ID is not Integer, then give the error description
      if ($response["status"]==0)
      {
         echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
         exit();
      }
      $id = $response["id"];

      $response = verify_path_gitea($git_fqdn, $project_name, $token,  $path, $branch);

      if ($response["status"]==0)
      {
         echo '
               <div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <b>Failed!</b> '.$response["msg"].'
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>';
         exit();
      }
   
      echo '{"id":"'.$id.'", "uuid":"'.md5($git_fqdn.$project_name.$path.$token.$branch.$format).'"}';

   }

   if ($type=="bitbucket")
   {

      #### Verify that the Project exists and get the Key
      $response = get_key_bitbucket($git_fqdn, $project_name, $token);

      if ($response["status"]==0)
      {
         echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
         exit();
      }
      $key = $response["key"];

      $response = verify_repo_bitbucket($git_fqdn, $project_name, $token, $key);
   
      if ($response["status"]==0)
      {
         echo '
               <div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <b>Failed!</b> '.$response["msg"].'
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>';
         exit();
      }

      $response = verify_path_bitbucket($git_fqdn, $project_name, $token, $key, $path, $branch);

      if ($response["status"]==0)
      {
         echo '
               <div class="alert alert-warning alert-dismissible fade show" role="alert">
               <b>Failed!</b> '.$response["msg"].'
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>';
         exit();
      }
   
      echo '{"id":"'.$key.'", "uuid":"'.md5($git_fqdn.$project_name.$path.$token.$branch.$format).'"}';


   }


	

?>
