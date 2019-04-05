<?php session_start();?>
<?php
    $msgs = null;
    require_once('config.php');
    if(!isset($_SESSION['UID'])){
        exit();
    }
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno()) {
        die("Connection failed: " . mysqli_connect_error());
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!isset($_POST['destUID']) || !isset($_POST['content'])){
            exit();
        }
        if(empty($_POST['destUID']) || empty($_POST['content'])){
            exit();
        }
        $destUID = $_POST['destUID'];
        $srcUID = $_SESSION['UID'];
        $content = $_POST['content'];
        
        echo $destUID;
        echo $content;

        $stmt = $connection->prepare("
        INSERT into chat
        (srcUID, destUID, content, time)
        values
        (?,?,?, CURRENT_TIMESTAMP());");
        $stmt->bind_param("iis", $srcUID, $destUID, $content);
        $stmt->execute();
        
        echo "ok";

        $stmt->close();
        $connection->close();


    }
        
?>