<?php
  session_start();
  session_regenerate_id(true); 

  $error = 0;
  $error_msg = "";
  $_SESSION["auth"] = false;

  if (isset($_SESSION['error']) && ($_SESSION['error'] == 1))
  {
    $error=1;
    $error_msg = $_SESSION['error_msg'];
  }

  if(isset($_GET['support_id']))
  {
    if( $_GET['support_id'] !== "")
      { 
        $_SESSION['support_id'] = $_GET['support_id'];
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
    <title>NAP Policy Management</title>


    

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="login.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin">
  <form action="auth.php" method="post">
    <img class="mb-4" src="images/app-protect.svg" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">NAP Policy Management</h1>

    <div class="form-floating">
      <input type="username" class="form-control" id="floatingInput" placeholder="username" name="username">
      <label for="floatingInput">Username</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" id="floatingPassword" placeholder="password" name="password">
      <label for="floatingPassword">Password</label>
    </div>
      <br>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    <p class="mt-5 mb-3 text-muted">

      <?php  
					if ($error>0)
					{
						echo '<br>';
						echo '<strong style="color:red">'.$error_msg.'</strong>';
						echo '<br>';              
					}   
				?>

    </p>



  </form>
</main>


    
  </body>
</html>

<?php

  $_SESSION['error'] = 0;
  $_SESSION['error_msg'] = "";

?>