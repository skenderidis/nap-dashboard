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


$policies = [];
$asm_go = 0;
$dir = getcwd() . '/config_files/';
$scan = scandir($dir);

foreach($scan as $file)
{
   
    if (is_dir($dir.$file) and !($file=="." || $file==".."))
    {
		array_push ($policies, $file);
    }
}
$policies_count = sizeof($policies);
$config_files = [];

if ($policies_count >0 )
{

	foreach($policies as $file)
	{

		if(file_exists("config_files/".$file."/info.json"))
		{
         if(file_exists("config_files/".$file."/sync"))
            $sync="";
         else
            $sync="hidden";
         
			$string = file_get_contents("config_files/".$file."/info.json");
			$config = json_decode($string, true);

			$name =$config['name'];
			$git =$config['git'];
			$nap_name =$config['nap_name'];
			$enforcement =$config['enforcement'];
         $format=$config['format'];
         $uuid=$config['uuid'];
         array_push ($config_files, json_decode('{"name":"'.$name.'", "git":"'.$git.'", "nap_name":"'.$nap_name.'", "format":"'.$format.'", "uuid":"'.$uuid.'", "sync":"'.$sync.'", "enforcement":"'.$enforcement.'"}', true));
		}

	}
   $display="";
}
else 
{
array_push ($config_files, json_decode('{"name":"-", "git":"-", "nap_name":"No Policies found", "enforcement":"-"}', true));
$display="hidden";
}

// Read the JSON file 
$file = file_get_contents('/etc/fpm/git.json');
      
// Decode the JSON file
$git_data = json_decode($file,true);

//print_r($config_files);
//exit();

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
                           <div class="title"> Parameters </div>
                           <div class="line"></div>
                           <div class="content">
                              <table id="overall" class="table table-striped table-bordered" style="width:100%; font-size:12px">
                                 <thead>
                                    <tr>
                                    <th>Policy Name</th>
                                    <th style="width: 100px; text-align:center;;">Policy Filename</th>
                                    <th style="width: 350px; text-align:left;">Git Repository</th>
                                    <th style="width: 80px; text-align:center;">Mode</th>
                                    <th style="width: 60px; text-align:center;">Actions</th>

                                    </tr>
                                 </thead>
                                 <tbody style="text-align: center; vertical-align: middle;">
                              
                                    
                                    <?php
                                       $x=0;
                                       foreach($config_files as $key)
                                       {	
                                          $mode = $key["enforcement"];	
                                          $mode_value ="";
                                          if ($mode =="blocking")
                                             $mode_value = '<b><span style="font-size:16px; padding:7px 15px; color:green;">'.$mode.'</span></b>';
                                          if ($mode =="transparent")
                                             $mode_value = '<b><span style="font-size:16px; padding:7px 15px; color:#fd7e14;">'.$mode.'</span></b>';
                                          echo '
                                             <tr id="row_'.$x.'">
                                                <td style="text-align: left; font-weight: bold; "><form action="policy.php" method="post"><input type="text" name="policy" value="'.$key['name'].'" hidden><input type="text" name="format" value="'.$key['format'].'" hidden><input type="text" name="uuid" value="'.$key['uuid'].'" hidden><button type="submit" class="btn-ahref">'.$key['nap_name'].'</button></form></td>
                                                <td style="text-align: left;">'.$key['name'].'</td>
                                                <td style="text-align: left;">'.$key['git'].'</td>
                                                <td>'.$mode_value.'</td>
                                                <td class="'.$display.'"><button val="'.$key['name'].'" uuid="'.$key['uuid'].'" class="btn btn-sm btn-primary sync_button sync_'.$x.' '.$key['sync'].'" id="sync_'.$x.'"  data-bs-toggle="modal" data-bs-target="#syncModal"><i class="fa fa-refresh"></i> </button> <button val="'.$key['name'].'" class="btn btn-sm btn-dark delete_button del_'.$x.'" id="del_'.$x.'"><i class="fa fa-trash"></i> </button></td>
                                             </tr>';
                                          $x=$x+1;
                                       }
                                       ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                  </div>
                        
                  <div class="col-4">
                        <div class="panel">
                           <div class="title"> Import Policies </div>
                           <div class="line"></div>
                           <div class="content">

                              <form  class="row g-3" action="import.php" method="post" autocomplete="off">

                                 <div class="col-md-12 violation_form">
                                    <label class="form-label">Repositories</label>
                                    <select id="git" name="git_uuid" class="form-select">
                                       <option value=0>Choose...</option>
                                          <?php
                                          
                                          foreach ($git_data as $instance)
                                          {
                                             echo '<option value="'.$instance['uuid'].'">'.$instance['fqdn'].'/'.$instance['project'].'/'.$instance['path'].'   ('.$instance["format"].') </option>';
                                          }
                                          
                                          ?>
                                    </select>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-9 mb-9" style="text-align:left">
                                       <button class="btn btn-success" type="submit"> Import</button>
                                    </div>
                                    
                                 </div>	

                              </form>

                           </div>
                        </div>

                        <div class="panel">
                           <div class="title"> Delete All Policies  </div>
                           <div class="line"></div>
                           <div class="content">

                              <div class="row">
                                 <div class="col-md-9 mb-9" style="text-align:left">
                                    <button class="btn btn-danger" id="delete" onclick="return confirm('This will delete the existing audit files')"> Delete</button>
                                 </div>
                              </div>	

                           </div>
                        </div>											


                        <div class="col-md-12" id="change_results_sync" style="display: none;">
                           <div class="alert alert-warning ">
                              <i class="fa fa-spinner fa-pulse fa-3x" style="float:left; margin-right:10px "></i>
                              <h6 style="margin-top:10px"> Please wait.. It can take up to 10 seconds.</h6>  
                           </div>
                        </div>

                  </div>

               </div>






            </main>
         </div>
      </div>

   <!-- Modal -->
   <div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModal" aria-hidden="true">
	   <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal_title">Sync Policy with Git</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
      
            <div class="modal-body">

                  <div class="col-md-12" id="change_results_sync" style="display: none;">
                     <div class="alert alert-warning ">
                        <i class="fa fa-spinner fa-pulse fa-3x" style="float:left; margin-right:10px "></i>
                        <h6 style="margin-top:10px"> Please wait.. It can take up to 10 seconds.</h6>  
                     </div>
                  </div>
                  <div class="col-md-6">
                     <label class="form-label">Policy to Sync</label>
                     <input type="text" class="form-control" id="modal_policy" aria-describedby="text" disabled> 
                  </div>
                  <div class="col-md-6" hidden>
                     <label class="form-label">UUID</label>
                     <input type="text" class="form-control" id="modal_uuid" aria-describedby="text" > 
                  </div>
                  <div class="col-md-6" hidden>
                     <label class="form-label">sync-id</label>
                     <input type="text" class="form-control" id="modal_id" aria-describedby="text" > 
                  </div>                    
                  <br>
                  <div class="col-md-12">
                     <label class="form-label">Git Comment</label>
                     <input type="text" class="form-control" id="modal_comment" aria-describedby="text" value="Changes made by NAP Policy Management Tool">
                  </div>
                  <br><br>


                  <div class="col-md-2 branch form-check" style="text-align:center;" hidden>
                     <label class="form-label" for="branch" style="width:100%; ">Create Branch</label>
                     <input class="" type="checkbox" id="branch" style="height:18px; width:18px; margin-top: 6px;" disabled>
                  </div>					


               </form>
               
            </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-secondary btn_bottom_form" data-bs-dismiss="modal">Close</button>
         <button type="button" class="btn btn-primary btn_bottom_form" data-bs-dismiss="modal" id="deploy">Deploy</button>
         </div>
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
      //	"order": [],
      //	"pageLength": 25
      }
   );
   });
   
</script>

<script>
	$(document).ready(function() {
		var table = $('#overall').DataTable( {
				"autoWidth": false,
				"processing": true,
				"order": [[0, 'desc']]
		} );	
	} );
</script>


<script>
   $(".delete_button").click(function() {
      var button = this.id;
      var row = $(this).closest("tr").attr('id')
      var policy = $(this).closest("button").attr('val')
      $(this).html('<i class="fa fa-spinner fa-pulse"></i>');
      $.ajax({
            method: "POST",
            url: "delete-policies.php",
            data: {
               policy: policy,
               all: "no"
            }
         })
         .done(function() {
            setTimeout(function() {
               $("."+button).html('<i class="fa fa-trash"></i>');
               $("#"+row).remove();
            }, 500);
            
         })
         .fail(function(jqXHR, textStatus, Status) {
            setTimeout(function() {
               alert("Failed to delete policy");
               $("."+button).html('<i class="fa fa-trash"></i>');
            }, 500);            

         });
      });
</script>


<script>
   $("#delete").click(function() {
      $.ajax({
            method: "POST",
            url: "delete-policies.php",
            data: {
               all: "yes"
            }
         })
         .done(function() {
            setTimeout(function() {
               location.reload();
            }, 1000);
            
         })
         .fail(function(jqXHR, textStatus, Status) {
            setTimeout(function() {
               alert("Failed to delete policies");
            }, 1000);            

         });
   
      });
</script>


<script>
   $(".sync_button").click(function() {
      var button_id = this.id;
      var uuid = $(this).closest("button").attr('uuid')
      var policy = $(this).closest("button").attr('val')

      $("#modal_uuid").val(uuid);
      $("#modal_id").val(button_id);
      $("#modal_policy").val(policy);
      
   });
</script>



<script>
   $("#deploy").click(function() {

      $("#change_results_sync").html(' <div class="alert alert-warning "><i class="fa fa-spinner fa-pulse fa-3x" style="float:left; margin-right:10px "></i><h6 style="margin-top:10px"> Please wait.. It can take up to 10 seconds.</h6></div>');
      var uuid = $("#modal_uuid").val();
      var policy = $("#modal_policy").val();
      var comment = btoa($("#modal_comment").val());
      var button_id = $("#modal_id").val();
      $("#change_results_sync").show();
      $("#"+button_id).hide();
      $.ajax({
         method: "POST",
         url: "sync-policy.php",
         data: {
            policy: policy,
            uuid: uuid,
            comment: comment
         }
      })
         .done(function(msg) {
            $("#change_results_sync").html(msg);
            $("#"+button_id).hide();
         })
         .fail(function(jqXHR, textStatus, Status) {
            $("#change_results_sync").html("<h6> Parsing error:<span style='color:red'> Undetermined Error </span>. </h6>");
         });
      });
</script>


