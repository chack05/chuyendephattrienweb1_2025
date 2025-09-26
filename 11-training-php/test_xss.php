
<?php
$username = isset($_GET['name']) ? $_GET['name'] : 'Guest';
?>
<!DOCTYPE html>
<html>
<body>
    <h1>Chào mừng bạn, <?php echo $username; ?>!</h1>
</body>
</html>