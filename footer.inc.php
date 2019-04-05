<!-- Footer -->
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; FastTrade 2019</p>
    </div>
    <!-- /.container -->
</footer>

<?php 
    require_once('config.php');
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (mysqli_connect_errno()) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $connection->prepare("SELECT * from appsettings where name = 'colortone';");
    $stmt->execute();
    $colortone = $stmt->get_result()->fetch_assoc();

    $stmt->close();
    $connection->close();
?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/bootstrap-colorpicker-3.0.3/js/bootstrap-colorpicker.min.js"></script>

    <style>
        .bg-dark{
            background-color: <?php echo $colortone['value'] ?>!important;
        }
    </style>