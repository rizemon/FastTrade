<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

<?php include 'header.inc.php';?>
<?php
        
        require_once('config.php');
        if(!isset($_GET['itemID'])){
            header('Location: index.php');
            exit();
        }elseif(empty($_GET['itemID'])){
            header('Location: index.php');
            exit();
        }

        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if (mysqli_connect_errno()) {
            die("Connection failed: " . mysqli_connect_error());
        }
        

        $itemID = $_GET['itemID'];
        
        

        $stmt = $connection->prepare("
        SELECT *,  (endDate > current_date or endDate is NULL) as active from item join category
        on item.categoryID = category.categoryID
        where itemID = ?");
        $stmt->bind_param("d",$itemID);
        $stmt->execute();

        $item = $stmt->get_result()->fetch_assoc();
        
        
                
        
        $stmt = $connection->prepare("
       SELECT * FROM `userinfo` where `UID` = (select ownerID from item where itemID = ?)");
        $stmt->bind_param("d",$itemID);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();
                
        
        function sentoffer()
            {
                
                $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                if (mysqli_connect_errno()) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                $destUID = $_POST['destid'];
                $srcUID = $_SESSION['UID'];
                $itemID = $_GET['itemID'];
                $stmt = $connection->prepare("
                INSERT into chat
                (srcUID, destUID, itemID, time)
                values
                (?,?,?, CURRENT_TIMESTAMP());");
                $stmt->bind_param("iii", $srcUID, $destUID, $itemID);
                $stmt->execute();
                header('Location: chat.php?destUID='.$destUID);
                
            }
        if(isset($_POST['makeoffer']))
        {
               sentoffer();
               exit();
        } 
        
        if(!$item){
            header('Location: 404.php');
            exit();
        }
        $stmt->close();
        $connection->close();
    ?>
<!-- Page Content -->
<div class="container">


    <div class="card mb-4 mt-4">
        <?php 
            if($item['active']){
                if ($item['type'] == 'B') {
                    echo '<button type="button" class="btn btn-danger d-inline">BUYING</button>';
                }elseif ($item['type'] == 'S'){
                    echo '<button type="button" class="btn btn-success d-inline">SELLING</button>';
                }
            }else{
                echo '<button type="button" class="btn btn-secondary d-inline">DEACTIVATED</button>';
            }
        ?>
 
    </button>
        <div class="card-header">
        
            <?php
                   echo '<h1>'.$item['title'].'';
                   echo ' <small>'.$item['categoryName'].'</small>';
                ?>
                
            <?php
            
             echo ' </h1>';
 
                  ?>
        </div>
        <div class="card-body mt-2">
            <div class="row">
                <div class="col-md-8 col-lg-8">
                    <div class="card-body">
                        <?php


                            if($item['picture'] == null){
                                $item['picture'] = "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";
                            }



                            echo '<img class="card-img img-fluid" src="data:image/jpeg;base64,'.$item['picture'].'" alt="">';
                            ?>
                    </div>
                </div>

                <div class="col-md-8 col-lg-4">
                    
                    <div class="card mb-4">
                    
                        <div class="card-body">
                            <h4 class="card-title">Item Description</h4>
                            <?php
                                        echo '<p class="card-text">'.$item['description'].'</p>';
                                        ?>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Item Details</h4>
                            <?php
                                         echo '<ul>';
                               echo '<li>Item Condition(0-10): '.$item['itemcondition'].'</li>';
                                echo '<li>Price: $'.bcdiv($item['price'], 1, 2).'</li>';
                                $year = intdiv($item['age'],12);
                                $months = $item['age']%12;
                                if(!$item['age']== 0){
                                    if($item['age']== 1){
                                    echo '<li>Age: '.$item['age'].' month</li>';
                                        }
                                    if($item['age']>1 && $item['age']<12)  {
                                        echo '<li>Age: '.$item['age'].' months</li>';
                                        
                                        }
                                    else if($item['age']== 12){
                                    echo '<li>Age: 1 year</li>';
                                        }    
                                    else if($item['age']>12){
                                        
                                $year = intdiv($item['age'],12);
                                
                                $months = $item['age']%12;
                                       if($year ==1){
                                           if($months== 1){
                                            echo '<li>Age: '.$year.' year '.$months.' month</li>';
                                                }
                                            else if($months>1 && $months<12)  {
                                                echo '<li>Age: '.$year.' year '.$months.' months</li>';

                                            }
                                        }else
                                        {
                                            if($months== 0){
                                            echo '<li>Age: '.$year.' years</li>';
                                                }
                                            else if($months== 1){
                                            echo '<li>Age: '.$year.' years '.$months.' month</li>';
                                                }
                                            else if($months>1 && $months<12){
                                                echo '<li>Age: '.$year.' years '.$months.' months</li>';
                                        }
                                          
                                       }
                                    
                                
                                            }
                                
                                    }
                                if(!$item['venue']== ''){
                                    echo '<li>Venue for Collection: '.$item['venue'].'</li>';
                                }
                                
                            echo '</ul>';
                                        ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Listed by</h4>
                            <?php
                                        echo '<a href="otherprofile.php?destUID='.$user['UID'].'"><p class="card-text">'.$user['username'].'</p></a>';
                                        echo '<p class="card-text">'.$user['email'].'</p>';
                                        echo'<form method="post" action="item.php?itemID='.$itemID.'">';
                                        echo '<input type="hidden" name="destid" value='.$user['UID'].' />';
                                        if(!isset($_SESSION['UID'])){
                                            if($item['active']){
                                                echo '<input class="form-control form-control-lg form-control-borderless btn-success" type="submit" name="makeoffer" value="Make Offer"/>'; 
                                            }else{
                                                echo '<input class="form-control form-control-lg form-control-borderless btn-secondary disabled" type="button" value="Unable to make offer"/>'; 
                                            }
                                        }else{
                                            if($user['UID'] != $_SESSION['UID']){
                                                if($item['active']){
                                                    echo '<input class="form-control form-control-lg form-control-borderless btn-success" type="submit" name="makeoffer" value="Make Offer"/>'; 
                                                }else{
                                                    echo '<input class="form-control form-control-lg form-control-borderless btn-secondary disabled" type="button" value="Unable to make offer"/>'; 
                                                }
                                            }
                                        }

                                        

                                        
                                        
                                        echo '</form>';
                                          ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>
<!-- /.container -->

<?php include 'footer.inc.php';?>

</body>

</html>