<!-- Navigation -->
<?php session_start();?>

<?php
// function to get the current page name
function PageName() {
    return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
  }
  
  $current_page = PageName();
  
?>
<?php $type = isset($_GET["type"]) ? $_GET["type"] : "B";?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark ">
    <div class="container">
        <a class="navbar-brand" href="index.php">FastTrade</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
            aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li
                    class="nav-item <?php if( $current_page=="index.php") { ?> active   <?php   }  ?>">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li
                    class="nav-item <?php if( $current_page=="buysell.php" && $type=='B') { ?> active   <?php   }  ?>">
                    <a class="nav-link" href="buysell.php?type=B">Buy</a>
                </li>
                <li
                    class="nav-item <?php if( $current_page=="buysell.php" && $type=='S') { ?> active   <?php   }  ?>">
                    <a class="nav-link" href="buysell.php?type=S">Sell</a>
                </li>
                <li
                    class="nav-item <?php if( $current_page=="feedback.php") { ?> active   <?php   }  ?>">
                    <a class="nav-link" href="feedback.php">Feedback</a>
                </li>
                <?php
                    if(!isset($_SESSION['UID'])){
                ?>
                <li
                    class="nav-item <?php if( $current_page=="register.php") { ?> active   <?php   }  ?>">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <li
                    class="nav-item <?php if( $current_page=="login.php") { ?> active   <?php   }  ?>">
                    <a class="nav-link" href="login.php">Login <i class="fas fa-sign-in-alt"></i></a>
                </li>
                <?php
                    }else{
                ?>
                 <li class="nav-item dropdown <?php if( $current_page=="otherprofile.php" ||  $current_page=="settings.php" ||  $current_page=="userlisting.php" || $current_page=="inbox.php") { ?> active   <?php   }  ?>">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">Profile</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="otherprofile.php?destUID=<?php echo $_SESSION['UID']?>">Reviews</a>
                        <a class="dropdown-item" href="userlisting.php">Listings</a>
                        <a class="dropdown-item" href="inbox.php">Inbox</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="settings.php">Settings</a>
                    </div>
                </li>


            
               
                <?php
                if($_SESSION['admined'] == 1){
                ?>
                <li class="nav-item dropdown <?php if( $current_page=="usermanagement.php" ||  $current_page=="appsettings.php" ||  $current_page=="adminfeedback.php") { ?> active   <?php   }  ?>">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">Admin</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="usermanagement.php">User Management</a>
                        <a class="dropdown-item" href="adminfeedback.php">Feedbacks</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="appsettings.php">Application Settings</a>
                    </div>
                </li>
                <?php
                }
                ?>
                <li
                    class="nav-item <?php if( $current_page=="logout.php") { ?> active   <?php   }  ?>">
                    <a class="nav-link" href="logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
                </li>
                <?php
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>