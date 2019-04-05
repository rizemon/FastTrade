<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

<body>


    <?php include 'header.inc.php';?>
    <?php
        $stmt = null;
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

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(isset($_POST['color'])){
                if(empty($_POST['color'])){
                    $_SESSION['ERROR_MSG'] = "Something went wrong";
                }else{
                    if(!preg_match('/^#?(([a-f0-9]{3}){1,2})$/i', $_POST['color'])){
                        $_SESSION['ERROR_MSG'] = "Invalid hex color!";
                    }else{
                        
                        $color = $_POST['color'];
                        $setting = "colortone";
                        $stmt = $connection->prepare("UPDATE appsettings set value = ? where name = ?");
                        $stmt->bind_param("ss", $color, $setting);
                        $stmt->execute();
                        if($stmt->affected_rows >= 0){
                            $_SESSION['SUCCESS_MSG'] = "The color tone has been successfullly updated.";
                        }else{
                            $_SESSION['ERROR_MSG'] = "Something went wrong.";
                        }
                    }
                }
            }elseif(isset($_POST["newCategoryName"])){
                if(empty($_POST["newCategoryName"])){
                    $_SESSION['ERROR_MSG'] = "Please fill the necessary fields.";
                }else{
                    $newCategoryName = $_POST["newCategoryName"];
                    $stmt = $connection->prepare("INSERT into category (categoryName) values (?);");
                    $stmt->bind_param("s", $newCategoryName);
                    $stmt->execute();
                    if($stmt->affected_rows >= 0){
                        $_SESSION['SUCCESS_MSG'] = "New category has been successfully created";
                    }else{
                        $_SESSION['ERROR_MSG'] = "Something went wrong.";
                    }
                }
            }elseif(isset($_POST["categoryID"]) && isset($_POST["categoryName"])){
                if(empty($_POST['categoryID'])){
                    $_SESSION['ERROR_MSG'] = "Something went wrong";
                }elseif(empty($_POST['categoryName'])){
                    $_SESSION['ERROR_MSG'] = "Please fill the necessary fields.";
                }else{
                    $categoryName = $_POST['categoryName'];
                    $categoryID = $_POST['categoryID'];
                    if(isset($_POST["update"])){
                        $stmt = $connection->prepare("UPDATE category set categoryName = ? where categoryID = ?");
                        $stmt->bind_param("si", $categoryName, $categoryID);
                        $stmt->execute();
                        if($stmt->affected_rows >= 0){
                            $_SESSION['SUCCESS_MSG'] = "The category has been successfullly updated.";
                        }else{
                            $_SESSION['ERROR_MSG'] = "Something went wrong.";
                        }
                    }elseif(isset($_POST["delete"])){
                        $stmt = $connection->prepare("DELETE from category where categoryID = ?");
                        $stmt->bind_param("i", $categoryID);
                        $stmt->execute();
                        if($stmt->affected_rows >= 0){
                            $_SESSION['SUCCESS_MSG'] = "The category has been successfullly deleted.";
                        }else{
                            $_SESSION['ERROR_MSG'] = "Something went wrong.";
                        }
                    }else{
                        $_SESSION['ERROR_MSG'] = "Something went wrong.";
                    }
                    
                }
            }else{
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }
        
        }
        $stmt = $connection->prepare("SELECT * from appsettings where name = 'colortone';");
        $stmt->execute();
        $colortone = $stmt->get_result()->fetch_assoc();


        $stmt = $connection->prepare("SELECT * from category");

        $stmt->execute();

        $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


        $stmt->close();
        $connection->close();

        
    ?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-10 mx-auto">

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
                        <h5 class="card-title text-center">Update application settings</h5>

                        <h5 class="text-center">Add/Delete/Update categories</h5>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Category ID</th>
                                    <th scope="col">Category Name</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category) { ?>
                                <form action="appsettings.php" method="POST">
                                    <tr>
                                        <input type="hidden" id="categoryID" name="categoryID"
                                            value="<?php echo $category["categoryID"];?>">
                                        <th scope="row"><?php echo $category["categoryID"]; ?></th>
                                        <td>
                                            <input value="<?php echo $category["categoryName"]; ?>" name="categoryName"
                                                type="text" class="form-control" id="categoryName" required>
                                        </td>
                                        <td>
                                            <button name="update" class="btn btn-primary btn-sm">Update <i
                                                    class="fa fa-sync"></i></button>
                                            <button name="delete" class="btn btn-danger btn-sm">Delete <i
                                                    class="fa fa-ban"></i></button>
                                        </td>
                                    </tr>
                                </form>
                                <?php } ?>
                                <form action="appsettings.php" method="POST">
                                    <tr>
                                        <th scope="row">New</th>
                                        <td>
                                            <input name="newCategoryName"
                                                type="text" class="form-control" id="newCategoryName" required>
                                        </td>
                                        <td>
                                            <button type="submit" name="update" class="btn btn-success btn-sm">Add <i
                                                    class="fa fa-plus"></i></button>
                                                    
                                        </td>
                                    </tr>
                                </form>

                            </tbody>
                        </table>
                        
                        <h5 class="text-center">Change color tone</h5>
                                    
                        <form method="POST" action="appsettings.php">
                            <div class="form-group">
                                <div id="cp2" class="input-group " title="Using input value">
                                    <input type="text" name="color" class="form-control input-lg" value="<?php echo $colortone["value"]; ?>" />
                                    <span class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                    </span>
                                    <button name="update" class="btn btn-primary btn-sm ml-4 ">Update <i
                                                    class="fa fa-sync"></i></button>
                                </div>
                            </div>
                            
                        </form>

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
    <script>
    $(function() {
        // Basic instantiation:
        $('#cp2').colorpicker({
            format:'hex'
        });

        // Example using an event, to change the color of the .jumbotron background:
        $('#cp2').on('colorpickerChange', function(event) {
            $('.bg-dark').attr('style', 'background-color: ' + event.color.toHexString() + ' !important');
        });
    });
    
    </script>
    
</body>

</html>