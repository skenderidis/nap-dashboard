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

?>

<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
      <title>NAP Logs Analyzer</title>
      <link href="css/dataTables.bootstrap5.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/font-awesome.min.css">
      <!-- Bootstrap core CSS -->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/flags16.css" rel="stylesheet">
      <link href="css/flags32.css" rel="stylesheet">
      <style>

      </style>
      <!-- Custom styles for this template -->
      <link href="dashboard.css" rel="stylesheet">

   </head>
   <body>

	   <nav class="navbar navbar-dark bg-dark sticky-top " style="padding:0px 50px 0px 10px;">
         <a class="navbar-brand" href="#"><img src="images/app-protect.svg" width=32/> &nbsp; NGINX App Protect</a>
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
               <div class="position-sticky pt-3" style="width:99%">
                  <ul class="nav flex-column">
							<li class="nav-item" style="background-color:#d2d8dc">
                        <a class="nav-link active" href="#">
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
	
					<div class="col-4">
						<div class="panel">
							<div class="title">Search for violation </div>
							<div class="line"></div>
							<div class="content">


								<form action="violation.php">
									<div class="row g-3">
										<div class="col-md-10" style="margin-top: 0px;">
										<label for="support_id" class="form-label">Support ID</label>
										<input type="text" class="form-control" name="support_id" placeholder="" value="" required>
										<div class="invalid-feedback">
											Valid support ID is required.
										</div>
										</div>


										<div class="col-md-2">
											<button class="btn btn-primary btn-xs" style="float:right; margin-top:8px;" type="submit">Search</button>
										</div>

									</div>
									<br>

								</form>		


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

