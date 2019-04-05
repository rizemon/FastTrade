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

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!isset($_POST['loginid']) || !isset($_POST['password'])){
            $_SESSION['ERROR_MSG'] = "Something went wrong.";
        }elseif(empty($_POST['loginid']) || empty($_POST['password'])){
            $_SESSION['ERROR_MSG'] = "Please fill the necessary fields.";
        }else{
            $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (mysqli_connect_errno()) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $loginid = $_POST['loginid'];
            $password = $_POST['password'];
            
            $stmt = $connection->prepare("SELECT * FROM auth where loginid = ?");
            $stmt->bind_param("s",$loginid);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if( $user && password_verify($password,$user['password'])){
                if($user["verified"] == 0){
                    $_SESSION['ERROR_MSG'] = "Your account has not been verified via your email.";
                }elseif($user["enabled"] == 0){
                    $_SESSION['ERROR_MSG'] = "Your account has been disabled. Please contact the administrator.";
                }else{
                    session_regenerate_id();
                    $_SESSION['UID'] = $user['UID'];
                    $_SESSION['admined'] = $user['admined'];
                    header('Location: index.php');
                }
            }else{
                $_SESSION['ERROR_MSG'] = "Invalid username or password.";
            }
            $stmt->close();
            $connection->close();
        }
        
      }
  ?>

    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">

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
                        <h5 class="card-title text-center">Sign In</h5>
                        <form class="form-signin" action="login.php" method="POST">
                            <div class="form-label-group">
                                <input type="text" name="loginid" id="loginid" class="form-control"
                                    placeholder="Username" required>
                                <label for="loginid">Username</label>
                            </div>

                            <div class="form-label-group">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>

                            <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Sign
                                in</button>
                            <hr class="my-4">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container -->

    <?php include 'footer.inc.php';?>


</body>

</html>