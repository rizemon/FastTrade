<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

<body>


    <?php include 'header.inc.php';?>
    <?php
        
        require_once('config.php');
        if(!isset($_SESSION['UID'])){
            $_SESSION['ERROR_MSG'] = "Please login to access this feature.";
            header('Location: login.php');
            exit();
        }
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (mysqli_connect_errno()) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $msgs = [];
        $user = null;
        $destUID = 0;   

        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(!isset($_GET['destUID'])){
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }elseif(empty($_GET['destUID'])){
                $_SESSION['ERROR_MSG'] = "Something went wrong.";
            }elseif($_SESSION['UID'] == $_GET['destUID']){
                $_SESSION['ERROR_MSG'] = "You cannot talk to yourself!";

                
            }else{
                $destUID = $_GET['destUID'];
                $srcUID = $_SESSION['UID'];

                $stmt = $connection->prepare("SELECT * from userinfo where UID = ?");
                $stmt->bind_param("i",$destUID);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                if(!$user){
                    $_SESSION['ERROR_MSG'] = "User does not exist.";
                }else{
                    $stmt = $connection->prepare("
                    SELECT * FROM chat 
                    where (srcUID = ? or srcUID = ?) and (destUID = ? or destUID = ?)
                    order by time asc;");
                    $stmt->bind_param("iiii", $srcUID, $destUID, $srcUID, $destUID);
                    $stmt->execute();
                    $msgs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                }

               
                
                $stmt->close();
                $connection->close();
            }

        }
        
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
                        <h5 class="card-title text-center">You are now chatting with: <a href="otherprofile.php?destUID=<?php echo $destUID;?>"><?php echo $user["username"];?></a>
                      </h5>

                        <div class="mesgs w-100">
                            <div class="msg_history">
                                
                                <?php
                                        foreach($msgs as $msg){

                                    ?>
                                <?php
                                    require_once('config.php');
                                    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                                    if($msg['itemID']!=NULL){
                                                $item = $msg['itemID'];
                                                $stmt = $connection->prepare("SELECT * from item where itemID = ?");
                                                $stmt->bind_param("i",$item);
                                                $stmt->execute();
                                                $iteminfo = $stmt->get_result()->fetch_assoc();
                                            }
                                            
                                    $connection->close();
                                    if($msg['itemID']!=NULL){?>
                                    <?php
                                    if ($msg["srcUID"] == $srcUID){
                                        echo'<div class="outgoing_msg">';
                                        echo'<div class="sent_msg">';
                                    }
                                    if ($msg["srcUID"] == $destUID){
                                        echo '<div class="incoming_msg">';
                                        echo'<div class="received_msg">';
                                    }
                                    ?>
                                    
                                                <a href="item.php?itemID=<?php echo $item ?>"> 
                                                <div class="card h-25 w-100 mr-2 mb-2">  
                                                    <div class="d-flex align-items-center">
                                                        <?php echo '<img class="card-img-top w-25 profileimg" src="'; 
                                                        if($iteminfo['picture'] == null){
                                                            $iteminfo['picture'] = "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";
                                                        }
                                                        echo 'data:image/jpeg;base64,'.$iteminfo['picture'];
                                                        echo '" alt="">';  ?>
                                                        <div class="card-body">
                                                                
                                                        <h5 class="card-title mr-2">
                                                                    <?php echo $iteminfo['title'] ?>
                                                                    </h5>
                                                                    <h6 class="card-subtitle text-muted">
                                                                    <?php echo '$'.bcdiv($iteminfo['price'], 1, 2);?>
                                                                    </h6>
                                                        </div>
                                                        </div>
                                                        
                                                    </div>
                                                         
                                                    </a>
                                                
                                                            <?php
                                                            echo'</div></div>';
                                    }?>
                                                      
                                    

<?php if ($msg["srcUID"] == $srcUID && $msg['itemID']==NULL) { ?>
<div class="outgoing_msg">
    <div class="sent_msg">
        <p><?php echo $msg["content"]; ?></p>
        <span class="time_date"> <?php echo $msg["time"]; ?></span>
    </div>
</div>
<?php } ?>

<?php if ($msg["srcUID"] == $destUID && $msg['itemID']==NULL) { ?>
<div class="incoming_msg">
    <div class="received_msg">
        <div class="received_withd_msg">
            <p><?php echo $msg["content"]; ?></p>
            <span class="time_date"> <?php echo $msg["time"]; ?></span>
        </div>
    </div>
</div>
<?php } ?>



<?php

    }
?>
                            </div>
                            <button type="button" id="newMsg" class="btn btn-info btn-block" onclick="newMessage()">New message below!</button>
                            <div class="type_msg">
                                <div class="input_msg_write">
                                    <textarea id="msg" rows="2" class="write_msg"
                                        placeholder=" Type a message"> </textarea>
                                    <button id="onSubmit" class="msg_send_btn" type="button"><i class="fa fa-paper-plane"
                                            aria-hidden="true"></i></button>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- /.container -->


    <!-- Footer -->
    <?php include 'footer.inc.php'; ?>

    <script>
     var last_height = ($('.msg_history')[0].scrollHeight);
            
            function newMessage() {
                $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight);
                document.getElementById('newMsg').style.visibility = 'hidden';
            }
            
            
            document.onload = onLoadMsg();
            
            
            function onLoadMsg(){
                    document.getElementById('newMsg').style.visibility = 'hidden';
            }
            
            
            $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight);

            $(".msg_send_btn").click(function() {

                $.ajax({
                    url: "sendmsg.php",
                    type: "POST",
                    data: {
                        destUID: <?php echo $destUID; ?>,
                        content: $('#msg').val()
                    }
                });
                $('#msg').val("");
            });
            
            var elementIsClicked = false; // declare the variable that tracks the state
            function clickHandler(){ // declare a function that updates the state
              elementIsClicked = true;
            }

            var element = document.getElementById('onSubmit'); // grab a reference to your element

      
            $(document).ready(function() {
                setInterval(function() {
                    $.ajax({
                        url: "messages.php?destUID=<?php echo $destUID; ?>",
                        success: function(result) {
                            element.addEventListener('click', clickHandler);
                            $(".msg_history").html(result);
                            if ((last_height !== ($('.msg_history')[0].scrollHeight))&& !elementIsClicked ) {
                                    elementIsClicked = false;
                                    document.getElementById('newMsg').style.visibility = 'visible';
                                    last_height = $('.msg_history')[0].scrollHeight;
                               
                            }
                            else if((last_height !== ($('.msg_history')[0].scrollHeight)) && elementIsClicked){
                                elementIsClicked = false;
                                document.getElementById('newMsg').style.visibility = 'hidden';
                                $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight);
                                last_height = $('.msg_history')[0].scrollHeight;
                                
                            }

                        }
                    });


                }, 1000);
            });



    
    </script>
</body>

</html>