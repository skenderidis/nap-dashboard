
<html>

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


    if (isset($_POST["git"])) 
    {
        $file = "/etc/fpm/git.json";
        file_put_contents($file, $_POST['git']);
        echo '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Git Details saved.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';	
    
    }else
    {  
        echo '
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Git Details were not saved.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';	
    }


?>


</html>
