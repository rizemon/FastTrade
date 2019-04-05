<!DOCTYPE html>
<html lang="en">

<?php include 'title.php' ?>

    <?php include 'header.inc.php';?>
    <!-- Page Content -->
    <div class="container">
        <div class="my-5">
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
        </div>

        <!-- Page Features -->



    </div>
    <!-- /.container -->
    <div class="container">
            <div class="row">
                <div class="card-body">
                <div class="span12">
                  <div class="card-header">
                      <h1 class="justify-content-center">Page Not Found <small><font face="Tahoma" color="red">Error 404</font></small></h1>
                      <br /> 
                      <p>The page you requested could not be found . Use your browsers <b>Back</b> button to navigate to the page you have prevously come from</p>
                      <p><b>Or you could just press this neat little button:</b></p>
                      <a href="index.php" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> Take Me Home</a>
                    </div>
                </div>
                </div>
            </div>
        </div>
    <!-- /.container -->


    <!-- Footer -->
    <?php include 'footer.inc.php'; ?>

</body>

</html>