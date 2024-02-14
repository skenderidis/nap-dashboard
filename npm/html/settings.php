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

   $json = file_get_contents('/etc/fpm/git.json');
   $json_data = json_decode($json,true);
	
   $git = "var git = " . json_encode($json_data)  . " ;";

	$ds_content=file_get_contents("/etc/fpm/datasource.json");
	$ds_content_json = json_decode($ds_content, true);


	$error_git=0;
	$error_git_msg="";

	if(sizeof($ds_content_json)==0) {
		$error_git=1;
		$error_git_msg='<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> Datasource NOT configured. You will need to configure a datasource to be able to retrieve the event logs.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';		
	}

	$error_ds=0;
	$error_ds_msg="";	
	if(sizeof($json_data)==0) {
		$error_ds=1;
		$error_ds_msg='<div class="alert alert-warning alert-dismissible fade show" role="alert">
			<strong>Error!</strong> Git NOT configured. You will need to configure at least 1 Git destination to be able to modify the NAP configuration.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';		
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
      <link rel="stylesheet" href="css/font-awesome.min.css">
      <!-- Bootstrap core CSS -->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/flags16.css" rel="stylesheet">
      <link href="css/flags32.css" rel="stylesheet">

      <!-- Custom styles for this template -->
      <link href="dashboard.css" rel="stylesheet">

   </head>
   <body style="min-width: 1280px;">
			<nav class="navbar navbar-dark bg-dark sticky-top " style="padding:0px 50px 0px 10px;">
					
					<a class="navbar-brand" href="#"><img src="images/app-protect.svg" width=32/> &nbsp; NGINX App Protect - False Positive Management</a>


					<li class="nav-item2 dropdown" style="list-style-type: none; ">
            <a class="nav-link dropdown-toggle" href="#" id="user" data-bs-toggle="dropdown"><i class="fa fa-user"></i>&nbsp; User &nbsp;</a>
            <ul class="dropdown-menu" aria-labelledby="user" style="font-size: 12px;left:-25px">
						<li><a class="dropdown-item" href="logout.php">Logout</a></li>
						<li><a class="dropdown-item" href="settings.php">Change Password</a></li>
						</ul>
					</li>
			</nav>
      <div class="container-fluid">
         <div class="row">
            <nav id="sidebarMenu" class="col-md-1 col-lg-1 d-md-block bg-light sidebar collapse">
               <div class="position-sticky pt-3">
                  <ul class="nav flex-column">
							<li class="nav-item">
                        <a class="nav-link " href="violation.php">
                        <span data-feather="file"></span>
                        Violations
                        </a>
                     </li>
							<li class="nav-item">
                        <a class="nav-link" aria-current="page" href="policies.php">
                        <span data-feather="home"></span>
                        	Policies
                        </a>
                     </li>
							<li class="nav-item"  style="background-color:#d2d8dc">
                        <a class="nav-link active" aria-current="page" href="settings.php">
                        <span data-feather="home"></span>
                        	Settings
                        </a>
                     </li>
                  </ul>
               </div>
            </nav>
            <main class="col-md-11 ms-sm-auto col-lg-09 px-md-4">
              	<div class="row align-items-center">
                  <div class="title"> Settings </div>
               	</div>
							 	<div class="row">
									<div class="col-12 error_msg">
										<?php if ($error_git==1) {echo $error_git_msg; } ?>
										<?php if ($error_ds==1) {echo $error_ds_msg; } ?>
									</div>
							
							 	</div>

							 	<div class="row">
									<div class="col-12">

										<div class="panel">
											<div class="title">Manage Git Repos  <button type="button" class="btn btn-success" id="save" style="float: right; padding: 0.2rem 0.5rem;" disabled>Save</button>  
											<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#git_modal"  id="add_new" style="float: right; margin-right: 20px; padding: 0.2rem 0.5rem;">Add new</button></div>
											<div class="line"></div>
											<div class="content">
												<table id="git" class="table table-striped table-bordered" style="width:100%">
														<thead>
															<tr>
															<th> Git FQDN</th>
                                                <th style="width: 150px; text-align: center;">Project</th>
                                                <th style="width: 150px; text-align: center;">Path</th>
                                                <th style="width: 150px; text-align: center;">Format</th>
                                                <th style="width: 150px; text-align: center;">Token</th>
                                                <th style="width: 100px; text-align: center;">Branch</th>
                                                <th style="width: 70px; text-align: center;">Type</th>
                                                <th style="width: 15px; text-align: center;"></th>
															</tr>
														</thead>
													</table>	
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-8">
										<div class="panel ">
											<div class="title"> Manage Datasource </div>
											<div class="line"></div>
											<div class="content">
												<div class="row">

													<div class="col-md-3">
														<label for="datasource" class="form-label">Datasource</label>
														<select class="form-select" id="datasource" required>
															<option value="elastic" selected>Elasticsearch</option>
															<option value="datadog" disabled>Datadog</option>
															<option value="cloudwatch" disabled>Cloudwatch</option>
															<option value="azure" disabled>Sentinel</option>
		
														</select>
													</div>

													<div class="col-md-5">
														<label class="form-label" id="current_length_form_label">URL</label>
														<input type="text" class="form-control" id="datasource_url"  placeholder="http://elasticsearch:9200" value="<?php if (sizeof($ds_content_json)>0) echo $ds_content_json["url"]; ?>">
													</div>

													<div class="col-md-3">
														<label class="form-label" id="current_length_form_label">Authentication</label>
														<input type="text" class="form-control" id="datasource_auth" value="None" disabled>
													</div>			
													<div class="col-md-1">
														<button type="button" class="btn btn-primary" id="test_datasource" style="margin-top: 27px; padding: 0.2rem 0.5rem;">Test</button>
													</div>													
													
												
												</div>
												<br>
												<div class="row">
													<div class="col-md-6">
														<div id="results_datasource"> 
														
														</div>
													</div>																
													<div class="col-md-6">
														<button type="button" class="btn btn-success" id="save_datasource" style="float: right; margin-top: 15px; padding: 0.2rem 0.95rem;" disabled>Save</button>  
														
													</div>
												</div>

												
											</div>
										</div>

									</div>

									<div class="col-4">
											<div class="panel ">
												<div class="title"> Change Password  </div>
												<div class="line"></div>
												<div class="content">
													
														<div style="margin-top:5px">
															<label class="form-label">Current Password</label>
															<input type="password" class="form-control" id="current_password" autocomplete="off"">
														</div>
														<div style="margin-top:10px">
															<label class="form-label">New Password</label>
															<input type="password" class="form-control" id="new_password" autocomplete="off"">
														</div>
														<div style="margin-top:10px">
															<label class="form-label">Verify New Password</label>
															<input type="password" class="form-control" id="verify_password" autocomplete="off"">
														</div>
														<div style="margin-top:15px">
															<button type="button" class="btn btn-primary" id="change_password" style="float:left;margin-right: 20px; padding: 0.2rem 0.5rem;">Apply</button>
														</div>													
														<br>																										
														<div style="margin-top:25px">
															<div id="results_password"> 

															</div>
														</div>
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

   <!-- Modal -->
	<div class="modal fade" id="git_modal" tabindex="-1" aria-labelledby="git_modal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
    		<div class="modal-header">
					<h5 class="modal-title" id="modal_title">Add new Git Repo</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      	</div>
	  

      		<div class="modal-body">

						<form class="row g-3">
							<div class="col-md-5">
								<label class="form-label">Repo FQDN</label>
								<input type="text" class="form-control" placeholder="https://www.git.com" id="git_fqdn">
							</div>
							<div class="col-md-4">
								<label class="form-label">Project</label>
								<input type="text" class="form-control" placeholder="user/project_name" id="project_name">
							</div>
							<div class="col-md-3">
								<label class="form-label">Format</label>
								<select class="form-select" id="format">
									<option value="JSON" selected>JSON</option>
									<option value="YAML">YAML</option>
								</select>
							</div>									
							<div class="col-md-4">
								<label class="form-label">Folder (leave empty for root)</label>
								<input type="text" class="form-control" placeholder="folder that the policies are stored" id="path">
							</div>

							<div class="col-md-3">
								<label class="form-label">Token</label>
								<input type="password" class="form-control" placeholder="token" id="token">
							</div>

							<div class="col-md-2">
								<label class="form-label">Branch</label>
								<input type="text" class="form-control" placeholder="branch" id="branch">
							</div>

							<div class="col-md-2" hidden>
								<label class="form-label">ID</label>
								<input type="text" class="form-control" placeholder="id" id="git_id">
								<input type="text" class="form-control" placeholder="uuid" id="git_uuid">
							</div>

							<div class="col-md-3">
								<label class="form-label">Type</label>
								<select class="form-select" id="type">
									<option value="gitlab" selected>GitLab</option>
									<option value="gitea">Gitea</option>
									<option value="bitbucket">Bitbucket</option>
									<option value="github" disabled>GitHub</option>
								</select>
							</div>									
							<div class="col-md-10 violation_form">
								<label class="form-label">Complete URL</label>
								<input type="text" class="form-control" id="complete_url" disabled>
							</div>
							<div class="col-md-2 violation_form">
								<button type="button" class="btn btn-sm btn-success" style="margin-top:32px; font-size:13px" id="validate">Validate</button>
							</div>
							<div class="clearfix"></div>

							<div class="col-md-12" class="results">
								<div id="results"> </div>
							</div>

						</form>
 					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 0.2rem 0.5rem;">Cancel</button>
					<button type="button" class="btn btn-primary" id="add_row" data-bs-dismiss="modal" style="padding: 0.2rem 0.5rem;" disabled>Add</button>
				</div>
    	</div>
  	</div>
	</div>



<!-- SENSITIVE PARAMS -->
<script>
	<?php echo $git; ?>
		$(document).ready(function() {
			var table = $('#git').DataTable( {
				"searching": false,
				"info": false,
				"data": git,
				"createdRow": function( row, data, dataIndex ) {
				  $('td', row).eq(4).html("xxxxxxxxxxxxxxx");  
				  $('td', row).eq(7).html("<i class='fa fa-trash fa-2x' ></i>");  
			   },
            "columnDefs": [
               {target: 8,visible: false,searchable: false,},
               {target: 9,visible: false,searchable: false,}
            ],				
				"columns": [
					{ "className": 'bold',"data": "fqdn" },
					{ "className": 'attacks',"data": "project" },
					{ "className": 'attacks',"data": "path" },
					{ "className": 'attacks',"data": "format" },
					{ "className": 'attacks',"data": "token" },
					{ "className": 'attacks',"data": "branch" },
					{ "className": 'attacks',"data": "type"},
					{ "className": 'delete_button',"data": null},
               { "className": 'attacks',"data": "id" },
					{ "className": 'attacks',"data": "uuid", "default":""}
					],
					"autoWidth": false,
					"processing": true,
					"language": {"processing": "Waiting.... " }
			} );	


			$('#git tbody').on( 'click', '.delete_button', function () {

				var idx = table.row(this).index();

				table.row(this).remove().draw( false );
				$('#save').removeAttr("disabled");

				} );

	} );
</script>


<script>
	$( "#save" ).click(function() {
		var table = $('#git').DataTable();
		var payload = "["
		var i;
		for (i = 0; i < table.rows().count(); i++) { 
			var fqdn = table.cell( i, 0).data();
			var project = table.cell( i, 1).data();
			var path = table.cell( i, 2).data();
			if (path=="")
			{
				path = ".";
			}
			var format = table.cell( i, 3).data();
			var token = table.cell( i, 4).data();
			var branch = table.cell( i, 5).data();
			var id = table.cell( i, 8).data();
			var uuid = table.cell( i, 9).data();
			var type = table.cell( i, 6).data();
			if(i>0)
			{
				payload = payload + ', ';
			}
			payload = payload + '{"id":"'+id+'","fqdn":"'+fqdn+'","project":"'+project+'","path":"'+ path +'","format":"'+ format +'","token":"'+token+'","branch":"'+branch+'","uuid":"'+uuid+'","type":"'+type+'"}';
		}
		payload = payload + "]"

	$.post( "save-git.php",  { git: payload})
	.done(function( data ) {
    $(".error_msg").append(data);
  });
	});
</script>

<script>
	$( ".form-control" ).change(function() {
		var git_fqdn = $('#git_fqdn').val();
		if (git_fqdn=="")
			var git_fqdn = "";
		else
			var git_fqdn = $('#git_fqdn').val()+"/";		
		var project_name = $('#project_name').val();
		if (project_name=="")
			var project_name = "";
		else
			var project_name = $('#project_name').val()+"/";
		var path = $('#path').val();
		if (path=="" || path==".")
			var path = "";
		else
			var path = $('#path').val()+"/";
		$('#complete_url').val(git_fqdn+project_name+path);
	});
</script>

<script>
	$( "#validate" ).click(function() {
		var git_fqdn = $('#git_fqdn').val();
		var project_name = $('#project_name').val();
		var path = $('#path').val();
		var branch = $('#branch').val();
		var token = $('#token').val();
		var type = $("#type option:selected").val();
		var format = $("#format option:selected").val();

      
		$.ajax({
         method: "POST",
         url: "verify-git.php",
         data:  {git_fqdn: git_fqdn, 
                     project_name:project_name, 
                     path:path, 
                     token:token, 
                     branch:branch,
                     format:format,
                     type:type
         }
		})
      .done(function( msg ) {
         $(".results").show();
         if (!msg.includes("Failed"))
         {
            $('#add_row').removeAttr("disabled");
            $("#results").append('<div class="alert alert-success alert-dismissible fade show" role="alert"><b>Success!</b><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            
            var json_data = JSON.parse(msg);
            $("#git_id").val(json_data.id);
            $("#git_uuid").val(json_data.uuid);

         }
         else
         {
            $("#add_row").attr("disabled", true);
            $("#results").append(msg);
         }
      })
      .fail(function( jqXHR, textStatus, Status  ) {
         $(".results").append("<h5><span style='color:red'> Something went wrong</span></h5>");
         $("#add_row").attr("disabled", true);

      })
	});
</script>

<script>
	$( "#add_row" ).click(function() {
		var table = $('#git').DataTable();
		var git_fqdn = $( "#git_fqdn" ).val();
		var project_name = $( "#project_name" ).val();
		var format = $("#format option:selected").val();
		var path = $("#path").val();
		var token = $("#token").val();
		var branch = $("#branch").val();
		var id = $("#git_id").val();
		var uuid = $("#git_uuid").val();
		var type = $("#type option:selected").val();
		table.row.add( {
				"fqdn": git_fqdn,
				"project": project_name,
				"path": path,
				"format": format,
				"token": token,
				"id":id,
				"branch": branch,
				"type": type,
            "uuid": uuid
			} ).draw();
			

			table.row(this).remove().draw( false );
			$('#save').removeAttr("disabled");	
		
	});
</script>

<script>
	$( "#change_password" ).click(function() {
		var current_password = $('#current_password').val();
		var new_password = $('#new_password').val();
		var verify_password = $('#verify_password').val();

		$.ajax({
				method: "POST",
				url: "save-password.php",
				data:  {current_password: current_password, 
								new_password:new_password, 
								verify_password:verify_password
				}
			})
			.done(function( msg ) {
				$("#results_password").html(msg);

			})
			.fail(function( jqXHR, textStatus, Status  ) {
				$("#results_password").html(msg);
			})
	});
</script>


<script>
	$( "#test_datasource" ).click(function() {
		var url = $('#datasource_url').val();
		$('#test_datasource').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
		$('#test_datasource').attr("disabled", true);					
		$.ajax({
				method: "POST",
				url: "verify-datasource.php",
				data:  {url: url}
			})
			.done(function( msg ) {
				$("#results_datasource").html(msg);
				$('#test_datasource').html('Test')
				$('#test_datasource').removeAttr("disabled");
				if (msg.includes("Success"))
				{
					$('#save_datasource').removeAttr("disabled");
				}
				else
				{
					$('#save_datasource').attr("disabled", true);					
				}
			})
			.fail(function( jqXHR, textStatus, Status  ) {
				$('#test_datasource').html('Test')
				$('#test_datasource').removeAttr("disabled");
				$(".results").append("<h5><span style='color:red'> Something went wrong</span></h5>");
				$('#save_datasource').attr("disabled", true);

			})
	});
</script>

<script>
	$( "#save_datasource" ).click(function() {
		var url = $('#datasource_url').val();
		$('#save_datasource').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
		$('#save_datasource').attr("disabled", true);					
		$.ajax({
				method: "POST",
				url: "save-datasource.php",
				data:  {url: url}
			})
			.done(function( msg ) {
				$("#results_datasource").html(msg);
				$('#save_datasource').html('Save')
				$('#save_datasource').removeAttr("disabled");
				if (msg.includes("Success"))
				{
					$('#save_datasource').removeAttr("disabled");
				}
				else
				{
					$('#save_datasource').attr("disabled", true);					
				}
			})
			.fail(function( jqXHR, textStatus, Status  ) {
				$('#save_datasource').html('Save')
				$('#save_datasource').removeAttr("disabled");
				$(".results").append("<h5><span style='color:red'> Something went wrong</span></h5>");
				$('#save_datasource').attr("disabled", true);

			})
	});
</script>
