<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("Backend.php");

$server = new Backend("http://become.weblife.co.il/api/users.php","become","become-2019");
$server->processData();
?>
<?php include 'views/header.html';?>
<?php include 'views/navbar.html';?>

<?php echo('users'); ?>

<?php include 'views/scripts.html'; ?>
<?php include 'views/footer.html'; ?>