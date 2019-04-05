<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

<body>

    <?php include 'header.inc.php';?>
    <?php
        
        require_once('config.php');
        
        if(isset($_SESSION['UID'])){
          header('Location: index.php');
          exit();
        }
        if(!isset($_GET["confirmationCode"])){
            $_SESSION['ERROR_MSG'] = "Something went wrong.";
        }else{
            $confirmationCode = $_GET["confirmationCode"];
            $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if (mysqli_connect_errno()) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $stmt = $connection->prepare("UPDATE auth set verified = 1 where confirmationCode = ? and verified = 0; ");
            $stmt->bind_param("s",$confirmationCode);
            $stmt->execute();

            if($stmt->affected_rows == 1){
                $_SESSION['SUCCESS_MSG'] = "You have successfully verified your account.";
            }else{
                $_SESSION['ERROR_MSG'] = "Invalid confirmation code";
            }

            $stmt->close();
            $connection->close();
        }

    ?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <div class="col-sm-9 col-md-7 col-lg-6 mx-auto">

                <div class="card card-signin my-5">

                    <div class="card-body">
                        <?php
                        if(isset($_SESSION['ERROR_MSG'])){   
                        ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Oh snap!</strong> <?php echo $_SESSION['ERROR_MSG'] ?>
                        </div>
                        <?php 
                          unset($_SESSION['ERROR_MSG']);
                        }
                        ?>
                        <?php
                        if(isset($_SESSION['SUCCESS_MSG'])){   
                        ?>
                        <div class="alert alert-success" role="alert">
                            <strong>Yes!</strong> <?php echo $_SESSION['SUCCESS_MSG'] ?>
                        </div>
                        <?php 
                          unset($_SESSION['SUCCESS_MSG']);
                        }
                        ?>
                        <h5 class="card-title text-center">Thank you for joining FastTrade!</h5>
                        <a href="login.php">
                            <button type="button" class="btn btn-primary btn-block">Login now!</button>
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- /.container -->
    <?php include 'footer.inc.php';?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>