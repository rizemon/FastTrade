<!DOCTYPE html>
<html>
<?php include 'title.php' ?>
    <body>

        <?php include 'header.inc.php'; ?>
        <?php
        require_once('config.php');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!isset($_POST['name']) ||
                    !isset($_POST['email']) ||
                    !isset($_POST['message'])) {
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            } elseif (empty($_POST['name']) ||
                    empty($_POST['email']) ||
                    empty($_POST['message'])) {
                $_SESSION['ERROR_MSG'] = "Please fill the necessary fields.";
            } else {
                $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                if (mysqli_connect_errno()) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                $name = $_POST['name'];
                $email = $_POST['email'];
                $message = $_POST['message'];

                $stmt = $connection->prepare(" INSERT into feedback(name, email, message)
                    VALUES
                    (?,?,?);");
                $stmt->bind_param("sss", $name, $email, $message);
                $stmt->execute();

                if ($stmt->affected_rows >= 0) {
                    $_SESSION['SUCCESS_MSG'] = "Your feedback was successfully sent.";
                } else {
                    $_SESSION['ERROR_MSG'] = "Something went wrong.";
                }


                $stmt->close();
                $connection->close();
            }
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
						<h3 class="card-title text-center">
							Do you need some help or have a question or suggestion? All you have to do is to complete the form.
						</h3>
						<form class="mt-3"
							  method="POST" action="feedback.php" enctype="multipart/form-data">
							
								<div class="form-group" >
									<!-- Name -->
									<label for="name">Name:</label>	

									<div class="form-group">
										<input class="form-control" type="text" 
											   name="name" placeholder="" class="form-control" id="name" required>
									</div>
								</div>

								<div class="form-group">
									<!-- Email -->
									<label for="email">Email:</label>	

									<div class="size1 bo2 bo-rad-10 m-t-3 m-b-23">
										<input class="form-control" type="email" 
											   name="email" placeholder="" class="form-control" id="email" required>
									</div>
								</div>


								<div class="form-group">
									<!-- Message -->
									<label for="message">Message:</label>									
									<textarea class="form-control" 
											  name="message" placeholder="" type="text" class="form-control" id="message" required></textarea>
								</div>
							

							
								<!-- Button -->
								<button type="submit" class="btn btn-primary btn-block">
									Submit
								</button>
							
						</form>
					</div>
					</div>
				</div>
				</div>
				</div>




        <!-- /.container -->
        <?php include 'footer.inc.php'; ?>

    </body>

</html>