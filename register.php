<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

<body>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php include 'header.inc.php';?>
    <?php
        
        require_once('config.php');
        include_once '.\vendor\phpmailer\PHPMailer.php';
        include_once '.\vendor\phpmailer\Exception.php';
        include_once '.\vendor\phpmailer\SMTP.php';
        
        if(isset($_SESSION['UID'])){
          header('Location: index.php');
          exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          if(!isset($_POST['username']) || 
          !isset($_POST['loginid']) || 
          !isset($_POST['email']) ||
          !isset($_POST['password']) ||
          !isset($_POST['cfmpassword']) ||  
          !isset($_POST['gender']) || 
          !isset($_POST['contact']) ||
          !isset($_FILES['picture']) ||
          !isset($_POST['g-recaptcha-response'])){
            $_SESSION['ERROR_MSG'] = "Something went wrong.";
          }elseif(empty($_POST['username']) ||
          empty($_POST['loginid']) ||
          empty($_POST['email']) ||
          empty($_POST['password']) ||
          empty($_POST['cfmpassword'] ||
          empty($_POST['g-recaptcha-response']))){
            $_SESSION['ERROR_MSG'] = "Please fill the necessary fields.";
          }else{
            $post_data = http_build_query(
                array(
                    'secret' => RECAPTCHA_SECRET,
                    'response' => $_POST['g-recaptcha-response'],
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                )
            );
            $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $post_data
                ),
                'ssl' => 
                array(
                    'cafile'            => './cacert.pem',
                    'verify_peer'       => true,
                    'verify_peer_name'  => true,
                )
            );
            $context  = stream_context_create($opts);
            $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
            $result = json_decode($response);

            if (!$result->success) {
                $_SESSION['ERROR_MSG'] = "Invalid captcha!";
                header('Location: register.php');
                exit();
            }


            $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if (mysqli_connect_errno()) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $username = $_POST['username'];
            $loginid = $_POST['loginid'];
            $email = $_POST['email'];
            $password = password_hash( $_POST['password'] , PASSWORD_BCRYPT);

            $gender = !empty($_POST['gender']) ? $_POST['gender'] : null;
            $contact = !empty($_POST['contact']) ? $_POST['contact'] : null;  
            $picture = !empty($_FILES['picture']['tmp_name']) ? $_FILES['picture']['tmp_name'] : null;

            $verified = 0;
            $enabled = 1;
            $admined = 0;

            $confirmationCode = md5(uniqid(rand(), true));

            if($picture != null){
                $picture = base64_encode(file_get_contents($picture));
            }

            $stmt = $connection->prepare(" SELECT * from auth where loginid = ?;");
            $stmt->bind_param("s",$loginid);
            $stmt->execute();
            if($stmt->get_result()->fetch_assoc()){
                $_SESSION['ERROR_MSG'] = "Username is already in use.";
            }else{
                $stmt = $connection->prepare("
                INSERT into userinfo 
                (username, email, gender, contact, picture)
                VALUES
                (?,?,?,?,?);");
                $stmt->bind_param("sssss", $username, $email, $gender, $contact, $picture );
                $stmt->execute();
    
                if($stmt->affected_rows >= 0){
                    $UID = $stmt->insert_id;
                    $stmt = $connection->prepare("
                    INSERT into auth
                    (UID, loginid, password, verified, enabled, admined, confirmationCode)
                    VALUES
                    (?,?,?,?,?,?,?);");
                    $stmt->bind_param("issiiis", $UID, $loginid, $password, $verified, $enabled, $admined, $confirmationCode);
                    $stmt->execute();
                    if($stmt->affected_rows >= 0){
                        $mail = new PHPMailer\PHPMailer\PHPMailer(true); // create a new object
                        $mail->IsSMTP(); // enable SMTP
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
                        $mail->SMTPAuth = true; // authentication enabled
                        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
                        $mail->Host = "smtp.gmail.com";
                        $mail->Port = 465; // or 587
                        $mail->IsHTML(true);
                        $mail->Username = EMAIL_USER;
                        $mail->Password = EMAIL_PASS;
                        $mail->SetFrom(EMAIL_USER);
                        $mail->Subject = "Confirmation email for FastTrade";
                        $mail->Body =  "
						<h1>Email verification </h1>
						
						<p>Dear $username,</p>
						<p>Thank you for registering with FastTrade!</p>
						<p>================================================</p>
						<p>The account registered under this username has been successfully created. </p>
						
						<p>Username: $loginid </p>
						
						
						<P>Please click this link to activate your account: <a href='".CONFIRMATION_LINK."?confirmationCode=$confirmationCode'>". CONFIRMATION_LINK."?confirmationCode=$confirmationCode</p></a>
						
						<p>This is an auto generated message, please do not reply.</P>";
                        $mail->AddAddress($email);

                        if(!$mail->Send()) {
                            $_SESSION['ERROR_MSG'] = "Something went wrong.";
                        } else {
                            $_SESSION['SUCCESS_MSG'] = "Please check your email for your verification mail.";
                        }
                    }
                    else{
                        $_SESSION['ERROR_MSG'] = "Something went wrong.";
                    }
                }else{
                    $_SESSION['ERROR_MSG'] = "Something went wrong.";
                }
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
                        <h5 class="card-title text-center">Register</h5>
                        <form class="mt-3"
                            oninput='cfmpassword.setCustomValidity(cfmpassword.value != password.value ? "Passwords do not match." : "")'
                            method="POST" action="register.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="username">Display Name:</label>
                                <input name="username" type="text" class="form-control" id="username" required>
                            </div>
                            <div class="form-group">
                                <label for="loginid">Username:</label>
                                <input name="loginid" type="text" class="form-control" id="loginid" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input name="email" type="text" class="form-control" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input name="password" type="password" class="form-control" id="password" required>
                            </div>
                            <div class="form-group">
                                <label for="cfmpassword">Confirm Password:</label>
                                <input name="cfmpassword" type="password" class="form-control" id="cfmpassword"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender: (optional)</label>
                                <select name="gender" class="form-control" id="gender">
                                    <option value=""></option>
                                    <option value="M">M</option>
                                    <option value="F">F</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contact">Contact: (optional)</label>
                                <input name="contact" type="text" class="form-control" id="contact">
                            </div>
                            <div class="form-group">
                                <label for="picture">Profile Picture: (optional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="picture" id="picture">
                                    <label class="custom-file-label" for="picture">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="recaptcha">Recaptcha</label>
                                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE?>"></div>
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