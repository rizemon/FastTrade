<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

<body>


    <?php include 'header.inc.php';?>
    <?php
        
        require_once('config.php');
        if(!isset($_SESSION['UID']) || !isset($_SESSION['admined'])){
            $_SESSION['ERROR_MSG'] = "Please login to access this feature.";
            header('Location: login.php');
            exit();
        }
        if($_SESSION['admined'] == 0){
            $_SESSION['ERROR_MSG'] = "You need to be an admin to do that!";
            header('Location: index.php');
            exit();
        }


        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (mysqli_connect_errno()) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $stmt = null;
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(!isset($_POST['UID'])){
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }elseif(empty($_POST['UID'])){
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }else{
                $UID = $_POST['UID'];
                if(isset($_POST['enable'])){
                    $stmt = $connection->prepare("UPDATE auth set enabled = 1 where UID = ?");
                    $stmt->bind_param("i", $UID);
                    $stmt->execute();
                    if($stmt->affected_rows >= 0){
                        $_SESSION['SUCCESS_MSG'] = "User has been successfully enabled.";
                    }else{
                        $_SESSION['ERROR_MSG'] = "Something went wrong.";
                    }
                }elseif(isset($_POST['disable'])){
                    $stmt = $connection->prepare("UPDATE auth set enabled = 0 where UID = ?");
                    $stmt->bind_param("i", $UID);
                    $stmt->execute();
                    if($stmt->affected_rows >= 0){
                        $_SESSION['SUCCESS_MSG'] = "User has been successfully disabled.";
                    }else{
                        $_SESSION['ERROR_MSG'] = "Something went wrong.";
                    }
                }elseif(isset($_POST['delete'])){
                    $stmt = $connection->prepare("DELETE from userinfo where UID = ?");
                    $stmt->bind_param("i", $UID);
                    $stmt->execute();
                    if($stmt->affected_rows >= 0){
                        $_SESSION['SUCCESS_MSG'] = "User has been successfully deleted";
                    }else{
                        $_SESSION['ERROR_MSG'] = "Something went wrong.";
                    }
                }else{
                    $_SESSION['ERROR_MSG'] = "Something went wrong.";
                }
            }

        }


        $stmt = $connection->prepare("SELECT auth.UID, userinfo.username, auth.enabled from auth join userinfo on auth.UID = userinfo.UID;");

        $stmt->execute();

        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


        $stmt->close();
        $connection->close();
        
    ?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <div class="col-sm-12 col-md-12 col-lg-10 mx-auto">

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
                        <h5 class="card-title text-center">User Management</h5>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-10" scope="col">UID</th>
                                    <th class="w-50" scope="col">Username</th>
                                    <th class="w-40" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user) { ?>
                                <tr>
                                    <th scope="row"><?php echo $user["UID"] ?></th>
                                    <td><?php echo $user["username"] ?></td>
                                    <td>
                                    <form method="POST" action="usermanagement.php">
                                    <input type="hidden" id="UID" name="UID" value="<?php echo $user["UID"];?>">
                                <?php if($user["enabled"] == 0) {?> 

                                    <button name="enable" class="btn btn-success btn-sm my-1">Enable &nbsp;<i class="fa fa-toggle-on"></i></button> 
                                <?php } else { ?>  
                                    <button name="disable" class="btn btn-dark btn-sm my-1">Disable &nbsp;<i class="fa fa-toggle-off"></i></button> 
                                <?php } ?>
                                        <button name="delete" class="btn btn-danger btn-sm my-1">Delete <i class="fa fa-ban"></i></button>
                                </form>
                                    </td>
                                </tr>
                                <?php } ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container -->


    <!-- Footer -->
    <?php include 'footer.inc.php';?>

    <script>
    $('#picture').on('change', function() {
        //get the file name
        var fileName = $(this).val().replace("C:\\fakepath\\", "");
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
    </script>
</body>

</html>