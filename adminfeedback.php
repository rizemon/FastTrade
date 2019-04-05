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
            if(!isset($_POST['feedbackID'])){
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }elseif(empty($_POST['feedbackID'])){
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }else{
                $feedbackID = $_POST['feedbackID'];
                
                if(isset($_POST['delete'])){
                    $stmt = $connection->prepare("DELETE from feedback where feedbackID = ?");
                    $stmt->bind_param("i", $feedbackID);
                    $stmt->execute();
                    if($stmt->affected_rows >= 0){
                        $_SESSION['SUCCESS_MSG'] = "Feedback has been successfully deleted";
                    }else{
                        $_SESSION['ERROR_MSG'] = "Something went wrong.";
                    }
                }else{
                    $_SESSION['ERROR_MSG'] = "Something went wrong.";
                }
            }

        }


        $stmt = $connection->prepare("SELECT * from feedback;");

        $stmt->execute();

        $feedbacks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


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
                        <h5 class="card-title text-center">Feedback from users</h5>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-20" scope="col">Name</th>
                                    <th class="w-20" scope="col">Email</th>
                                    <th class="w-50" scope="col">Message</th>
									<th class="w-10" scope="col">Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feedbacks as $feedback) { ?>
                                <tr>
                                    <th scope="row"><?php echo $feedback["name"] ?></th>
                                    <td style="word-break: break-all;"><?php echo $feedback["email"] ?></td>
                                    <td style="word-break: break-all;"><?php echo $feedback["message"] ?></td>
                                    <td>
                                    <form method="POST" action="adminfeedback.php">
                                    <input type="hidden" id="feedbackID" name="feedbackID" value="<?php echo $feedback["feedbackID"];?>">
                                    <input type="hidden" id="feedbackID" name="name" value="<?php echo $feedback["name"];?>">
                                    <input type="hidden" id="feedbackID" name="email" value="<?php echo $feedback["email"];?>">
                                    <input type="hidden" id="feedbackID" name="message" value="<?php echo $feedback["message"];?>">
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
</body>

</html>