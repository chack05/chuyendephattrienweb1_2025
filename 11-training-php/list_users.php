<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();

require_once 'redis_connection.php';


$params = [];
if (!empty($_GET['keyword'])) {
    $params['keyword'] = $_GET['keyword'];
}

$users = $userModel->getUsers($params);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php'?>
<div class="container">
    <?php if (!empty($users)) {?>
        <div class="alert alert-success" role="alert">
            **ĐÃ BẢO MẬT:** Đã áp dụng htmlspecialchars() để ngăn chặn mã độc XSS.
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">Fullname</th>
                <th scope="col">Type</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user) {?>
                <tr>
                    <th scope="row"><?php echo $user['id']?></th>
                    <td>
                        <?php
                        $malicious_name = $user['name'];
                        if ($user['name'] == 'admin') {
                            // Dữ liệu ĐỘC HẠI được giả lập lấy từ database (vẫn tồn tại)
                            $malicious_name = "Admin";
                        }
                        // GIẢI PHÁP XSS: Sử dụng htmlspecialchars() để VÔ HIỆU HÓA mã độc
                        echo htmlspecialchars($malicious_name);
                        ?>
                    </td>
                    <td>
                        <?php echo $user['fullname']?>
                    </td>
                    <td>
                        <?php echo $user['type']?>
                    </td>
                    <td>
                        <a href="form_user.php?id=<?php echo $user['id'] ?>">
                            <i class="fa fa-pencil-square-o" aria-hidden="true" title="Update"></i>
                        </a>
                        <a href="view_user.php?id=<?php echo $user['id'] ?>">
                            <i class="fa fa-eye" aria-hidden="true" title="View"></i>
                        </a>
                        <a href="delete_user.php?id=<?php echo $user['id'] ?>">
                            <i class="fa fa-eraser" aria-hidden="true" title="Delete"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php }else { ?>
        <div class="alert alert-dark" role="alert">
            This is a dark alert—check it out!
        </div>
    <?php } ?>
</div>

<script src="public/js/jquery-2.1.4.min.js"></script>
<script src="public/js/bootstrap.min-3.3.7.js"></script>
<script src="public/js/app.js"></script>
</body>
</html>
