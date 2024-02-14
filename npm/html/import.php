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


	// Read the JSON git file 
	$json = file_get_contents('/etc/fpm/git.json');
	$json_data = json_decode($json,true);

	# If request doesn't contain the git variable return an error.
	# git variable is meant to be an ID.
	if( !(isset($_POST['git_uuid'])) )
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
		foreach($json_data as $key)
		{
			# Get all details for the git to be used.
			if($key["uuid"] == $_POST['git_uuid'])
			{
				$found_git="true";
				$token = $key["token"];
				$git_fqdn = $key["fqdn"];
				$project = $key["project"];
				$branch = $key["branch"];
				$format = $key["format"];
				$path = $key["path"];
				$id = $key["id"];
				$type = $key["type"];
				$uuid = $key["uuid"];
				if ($path == ".")
					$path = "";
			}
		}
		# If the ID is not found return an error.
		if ($found_git == "false")
		{
			echo '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong>Git not found on list. Click <a href="settings.php">here</a> to add it..
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';						
			exit();
		}
	}

   function get_policies_gitlab($git_fqdn, $project, $token, $id, $path, $branch, $uuid, $format) {
      ### --------  Setup headers required  -------- ####
		$headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; ',
			'PRIVATE-TOKEN: ' . $token
		);

      ### --------  API endpoint -------- ####
      $url = $git_fqdn."/api/v4/projects/".$id."/repository/tree/?path=".rawurlencode($path)."&per_page=100&ref=".$branch;
      
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
         $result = json_decode($curl_response, true);
         $list=[];
         foreach ($result as $key)
         {
            $ext = ".".strtolower($format);
            if ($key["type"] == "blob" && strpos($key['name'], $ext) !== false )
            {
               $list[] = ['name' => $key['name'], 'id' => $key['id'], 'uuid' => $uuid];
            }
         }
         $result["status"]=1;
         $result["msg"]=$list;
         return $result;
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while getting the policies from '".$git_fqdn. "' and for " .$project."/".$path;
         return $result;
      }
	}

   function get_policies_gitea($git_fqdn, $project, $token, $path, $branch, $uuid, $format) {
      ### --------  Setup headers required  -------- ####
		$headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; ',
         'Authorization: token ' . $token
		);

      ### --------  API endpoint -------- ####
      if ($path=="")
         $url = $git_fqdn."/api/v1/repos/".$project."/contents/?ref=".$branch;
      else
         $url = $git_fqdn."/api/v1/repos/".$project."/contents/".rawurlencode($path)."/?ref=".$branch;

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
         $result = json_decode($curl_response, true);
         $list=[];
         foreach ($result as $key)
         {
            $ext = ".".strtolower($format);
            if ($key["type"] == "file" && strpos($key['name'], $ext) !== false )
            {
               $list[] = ['name' => $key['name'], 'id' => '-', 'uuid' => $uuid];
            }
         }
         $result["status"]=1;
         $result["msg"]=$list;
         return $result;
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while getting the policies from '".$git_fqdn. "' and for " .$project."/".$path;
         return $result;
      }
	}   

   function get_policies_bitbucket($git_fqdn, $project_repo, $token, $key, $path, $branch, $uuid, $format) {
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
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/browse?limit=100&at=refs/heads/".$branch;            
      else
         $url = $git_fqdn."/rest/api/latest/projects/".$key."/repos/".$repo."/browse/".$path."?limit=100&at=refs/heads/".$branch;
  
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
         $result = json_decode($curl_response, true);

         $list=[];
         foreach ($result["children"]["values"] as $file)
         {
            $ext = ".".strtolower($format);
            if ($file["type"] == "FILE" && strpos($file["path"]["name"], $ext) !== false )
            {
               $list[] = ['name' => $file["path"]["name"], 'id' => '-', 'uuid' => $uuid];
            }
         }
         $result["status"]=1;
         $result["msg"]=$list;
         return $result;
      }
      else
      {
         $result["status"]=0;
         $result["msg"]= $httpcode . " HTTP code received while getting the policies from '".$git_fqdn. "' and for " .$project_repo."/".$path;
         return $result;
      }

	}


   if ($type=="gitlab")
   {
      $result = get_policies_gitlab($git_fqdn, $project, $token, $id, $path, $branch, $uuid, $format);

      if ($result["status"]==1)
      {
         $policies = "var policies = " . json_encode($result["msg"])  . ";";       
      }
      else
      {
         $policies = "var policies = [];";
      }
     
   }
   if ($type=="gitea")
   {
      $result = get_policies_gitea($git_fqdn, $project, $token, $path, $branch, $uuid, $format);
      if ($result["status"]==1)
      {
         $policies = "var policies = " . json_encode($result["msg"])  . ";";       
      }
      else
      {
         $policies = "var policies = [];";
      }
   }

   if ($type=="bitbucket")
   {
      $result = get_policies_bitbucket($git_fqdn, $project, $token, $id, $path, $branch, $uuid, $format);

      if ($result["status"]==1)
      {
         $policies = "var policies = " . json_encode($result["msg"])  . ";";       
      }
      else
      {
         $policies = "var policies = [];";
      }
     
   }


?>
<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="Kostas Skenderidis">
      <title>NAP Policy Review</title>
      <link href="css/dataTables.bootstrap5.min.css" rel="stylesheet">
      <link  href="css/font-awesome.min.css" rel="stylesheet">
      
      <!-- Bootstrap core CSS -->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/flags16.css" rel="stylesheet">
      <link href="css/flags32.css" rel="stylesheet">

      <!-- Custom styles for this template -->
      <link href="dashboard.css" rel="stylesheet">

   </head>
   <body>
      <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
         <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#"><img src="images/app-protect.svg" width=32/> &nbsp; NGINX App Protect</a>
         <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
         </button>
         <div class="navbar-nav">
            <div class="nav-item text-nowrap">
               <a class="nav-link px-3" href="https://docs.nginx.com/nginx-app-protect/">Sign out</a>
            </div>
         </div>
      </header>
      <div class="container-fluid">
         <div class="row">
            <nav id="sidebarMenu" class="col-md-1 col-lg-1 d-md-block bg-light sidebar collapse">
               <div class="position-sticky pt-3">
                  <ul class="nav flex-column">
                     <li class="nav-item" >
                        <a class="nav-link" href="violation.php">
                        <span data-feather="file"></span>
                        Violations
                        </a>
                     </li>
							<li class="nav-item" style="background-color:#d2d8dc">
                        <a class="nav-link active" aria-current="page" href="policies.php">
                        <span data-feather="home"></span>
                        	Policies
                        </a>
                     </li>
							<li class="nav-item">
                        <a class="nav-link" aria-current="page" href="settings.php">
                        <span data-feather="home"></span>
                        	Settings
                        </a>
                     </li>
                  </ul>
               </div>
            </nav>
            <main class="col-md-11 ms-sm-auto col-lg-09 px-md-4">


               <div class="row">
                  <div class="col-8">
                     <div class="panel">
                        <div class="title"> NAP Policies </div>
                        <div class="line"></div>
                        <?php 
                           if ($result["status"]==0)
                           {
                              echo '<div class="alert alert-warning d-flex align-items-center" role="alert">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                                          <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                       </svg>
                                       <div>
                                          Error. '.$result["msg"].'                          
                                       </div>
                                    </div>';
                           }
                        ?>

                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                              <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                           </svg>
                           <div>
                              <?php
                                 if ($format=="YAML")
                                    echo "This Git is marked as YAML and therefore will only import files with extension <b>.yaml</b>";
                                 else
                                    echo "This Git is marked as JSON and therefore will only import files with extension <b>.json</b>";
                              ?>
                           </div>
                        </div>
                        <div class="content">
                           <table id="policies" class="table table-striped table-bordered" style="width:100%; font-size:12px">
                              <thead>
                                 <tr>
                                 <th>Name</th>
                                 <th style="width: 24px; text-align:center;">Edit</th>
                                 </tr>
                              </thead>
                           </table>
                        </div>

                        <div class="row" style="text-align:right">

                           <button type="button" class="btn btn-success btn" id="analyze" style="float:right">Analyze</button>
                     </div>

                     </div>
                  </div>
                     
                  <div class="col-4 hidden" id="status_tab" >
                     <div class="panel">
                        <div class="title"> Import Status </div>
                        <div class="line"></div>
                        <div class="content">
                           <div class="results">
						
                           </div>
                           <i class='fa fa-spinner fa-pulse fa-3x del_2'></i>
                           <h5 class='del_2'> Please wait.. It can take up to 30-40 seconds per policy.</h5>                        
   
                        </div>
                     </div>									

                  </div>

               </div>






            </main>
         </div>
      </div>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.5.1.js"></script>
      <script src="js/jquery.dataTables.min.js"></script>
      <script src="js/dataTables.bootstrap5.min.js"></script>
   </body>

</html>

<script>
      $(document).ready(function () {
        $('#general').DataTable(
			{
				"searching": false,
				"info": false,
				"paging":false,
				"ordering":false,

			}
		);
      });
      
   </script>

<script>

   <?php echo $policies; ?>
   
	$(document).ready(function() {
		var table = $('#policies').DataTable( {
            "data": policies,
				"autoWidth": false,
				"processing": true,
				"order": [[0, 'desc']],
            "createdRow": function( row, data, dataIndex ) {
               $('td', row).eq(1).html("<i class='fa fa-trash' ></i> ");                 
            },
            "columnDefs": [
            {target: 2,visible: false,searchable: false,},
            {target: 3,visible: false,searchable: false,}

            ],            
            "columns": [
            { "className": 'bold',"data": "name" },
				{ "className": 'delete_button',"data": null, "orderable":false},
            { "className": 'bold',"data": "id" },
            { "className": 'bold',"data": "uuid" }
			],            
		} );	
      $('#policies tbody').on('click', '.delete_button', function() {
         var idx = table.row(this).index();
         table.row(this).remove().draw(false);
      });
	} );
</script>

<script>
   $("#analyze").click(function() {
      var table = $('#policies').DataTable();
      console.log(table);
      var payload = "["
      var i;
      for (i = 0; i < table.rows().count(); i++) {
         
         var name = table.cell(i, 0).data();
         var id = table.cell(i, 2).data();
         var uuid = table.cell(i, 3).data();
         

         if (i > 0) {
            payload = payload + ', ';
         }
         payload = payload + '{"name":"' + name + '","id":"' + id + '","uuid":"' + uuid + '"}';
        
      }
      payload = payload + "]"
      $("#status_tab").removeClass("hidden");
      var policies = JSON.parse(payload);
      var i = 0;
      doLoop(policies);

      function doLoop(policies) {
         //exit condition
         if (i >= table.rows().count()) {
            $(".results").append("<h7 style='color:green'> <b>You will be redirected to policy list in 10 seconds</b></h7><br>");
            $(".del_2").hide();
            setTimeout(function() {
               window.location.replace("policies.php");
               return;
            }, 10000);

            
         }
         $(".results").append(" <h6> Started parsing NAP policy<span style='color:blue'><b>: " + policies[i].name + " </b></span></h6>");
         $.ajax({
            method: "POST",
            url: "import-policies.php",
            data: {
               name: policies[i].name,
               uuid: policies[i].uuid,
               id: policies[i].id
            }
         })
         .done(function(msg) {
            if(msg.completed_successfully)
            {
               $(".results").append(" <h6> Parsing completed <span style='color:green'>successfully</span>. <span style='font-size:14px'>File size: <b>("+msg.file_size+")</b> </span></h6> ");
               if(msg.warnings.length>0)
               {
                  $(".results").append("<h7 style='color:red'> <b>Warnings:</b> <span style='color:red'>"+JSON.stringify(msg.warnings, null, 2)+"</span>. </h7><br>");
               }
               else
               {
                  $(".results").append("<h7 style='color:green'> <b>No Warnings</b></h7><br>");
               }               
            }
            else
            {
               if("error_message" in msg )
               {
                  $(".results").append("<h6> Parsing error:<span style='color:red'> "+msg.error_message+"</span>. </h6>");
               }
            }
            i++; 
            doLoop(policies);
         })
         .fail(function(msg) {
            $(".results").append(msg.responseText);
            i++; 
            doLoop(policies);
         });
         }
      });
</script>