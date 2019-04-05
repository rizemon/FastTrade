<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

<body>

    <?php include 'header.inc.php';?>
    <?php
    require_once('config.php');
    if(!isset($_SESSION['UID'])){
        $_SESSION['ERROR_MSG'] = "Please login to add a listing.";
        header('Location: login.php');
        exit();
    }
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno()) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $connection->prepare("SELECT * from category");
    $stmt->execute();
    $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $type = isset($_GET["type"]) ? $_GET["type"] : "B";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!isset($_POST['categoryID']) ||
        !isset($_POST['title']) || 
        !isset($_POST['description']) || 
        !isset($_POST['itemcondition']) ||
        !isset($_POST['price']) ||
        !isset($_POST['age']) ||
        !isset($_POST['endDate']) ||
        !isset($_POST['venue']) ||
        !isset($_FILES['picture'])){
            $_SESSION['ERROR_MSG'] = "Something went wrong.";
        }elseif(empty($_POST['categoryID']) ||
        empty($_POST['title']) || 
        empty($_POST['description']) || 
        empty($_POST['itemcondition']) ||
        empty($_POST['price'])){
            $_SESSION['ERROR_MSG'] = "Please fill the necessary fields.";
        }else{
            $ownerID = $_SESSION['UID']; 

            $categoryID = $_POST['categoryID'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $itemcondition = $_POST['itemcondition'] - 1;
            $price = $_POST['price'];
            $sold = 0;
            $age = !empty($_POST['age']) ? $_POST['age'] : null; 
            $endDate = !empty($_POST['endDate']) ? $_POST['endDate'] : null; 
            $venue = !empty($_POST['venue']) ? $_POST['venue'] : null; 
            $picture = !empty($_FILES['picture']['tmp_name']) ? $_FILES['picture']['tmp_name'] : null;

            if($picture != null){
                $picture = base64_encode(file_get_contents($picture));
            }
            $stmt = $connection->prepare("
            INSERT into item 
            (ownerID, categoryID, type, title, description, itemcondition, price, sold, age, startDate, endDate, venue, picture)
            VALUES
            (?,?,?,?,?,?,?,?,?,CURDATE(),?,?,?);");
            $stmt->bind_param("iisssidiisss", $ownerID, $categoryID, $type, $title, $description, $itemcondition, $price, $sold, $age, $endDate, $venue, $picture );
            $stmt->execute();
            if($stmt->affected_rows >= 0){
                $_SESSION['SUCCESS_MSG'] = "Your listing has been posted successfully.";
            }else{
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }
        }
    }
    $stmt->close();
    $connection->close();
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
                        <h5 class="card-title text-center">Add an item</h5>

                        <form class="mt-3" method="POST" action="additem.php?type=<?php echo $type; ?>"
                            enctype="multipart/form-data">
                            
                            <div class="form-group">
                                <label for="type">Want to:</label>
                                <select name="type" class="form-control" id="type" required>
                                    <option value="B" <?php if($type == 'B') echo "selected" ?>>Buy</option>
                                    <option value="S" <?php if($type == 'S') echo "selected" ?>>Sell</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input name="title" type="text" class="form-control" id="title" required>
                            </div>
                            <div class="form-group">
                                <label for="categoryID">Category:</label>
                                <select name="categoryID" class="form-control" id="categoryID" required>
                                    <?php foreach($categories as $category){ echo "<option value=".$category["categoryID"].">".$category["categoryName"]."</option>";}  ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea name="description" class="form-control" id="description" rows="4"
                                    required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="itemcondition">Condition:</label>
                                <select name="itemcondition" class="form-control" id="itemcondition">
                                    <?php foreach (range(1,11) as $i) { echo "<option value=".$i.">".($i-1)."</option>";} ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input name="price" type="number" value=0.00 min=0 step=0.01 class="form-control" id="price"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="age">Age: (optional)</label>
                                <input name="age" min=0 type="number" class="form-control" id="age">
                            </div>
                            <div class="form-group">
                                <label for="endDate">Expires on: (optional)</label>
                                <input name="endDate" type="date" class="form-control" id="endDate">
                            </div>
                            <div class="form-group">
                                <label for="venue">Venue: (optional)</label>
                                <input name="venue" type="text" class="form-control" id="venue">
                            </div>

                            <div class="form-group">
                                <label for="picture">Picture: (optional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="picture" id="picture">
                                    <label class="custom-file-label" for="picture">Choose file</label>
                                </div>
                            </div>
                    

                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- /.container -->

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