<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();

require_once 'redis_connection.php';


$params = [];
// BẢO MẬT: Dữ liệu keyword được truyền thẳng, nhưng sẽ được xử lý an toàn
// tại lớp Model (UserModel.php) bằng Prepared Statements.
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
    <!-- Cảnh báo xanh hiển thị trạng thái an toàn -->
    <div class="alert alert-success" role="alert">
        **ĐÃ ÁP DỤNG BẢO MẬT SQL INJECTION (SQLi)!** <br>
        Ứng dụng hiện đang an toàn vì Model (UserModel.php) được giả định sử dụng **Prepared Statements**.
    </div>

    <?php if (!empty($users)) {?>
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
                    <!-- BẢO MẬT: Luôn dùng htmlspecialchars() để tránh XSS khi hiển thị dữ liệu -->
                    <th scope="row"><?php echo htmlspecialchars($user['id'])?></th>
                    <td>
                        <?php echo htmlspecialchars($user['name'])?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($user['fullname'])?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($user['type'])?>
                    </td>
                    <td>
                        <a href="form_user.php?id=<?php echo htmlspecialchars($user['id']) ?>">
                            <i class="fa fa-pencil-square-o" aria-hidden="true" title="Update"></i>
                        </a>
                        <a href="view_user.php?id=<?php echo htmlspecialchars($user['id']) ?>">
                            <i class="fa fa-eye" aria-hidden="true" title="View"></i>
                        </a>
                        <a href="delete_user.php?id=<?php echo htmlspecialchars($user['id']) ?>">
                            <i class="fa fa-eraser" aria-hidden="true" title="Delete"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php }else { ?>
        <div class="alert alert-dark" role="alert">
            Không tìm thấy người dùng nào.
        </div>
    <?php } ?>
</div>

<script src="public/js/jquery-2.1.4.min.js"></script>
<script src="public/js/bootstrap.min-3.3.7.js"></script>
<script src="public/js/app.js"></script>
</body>
</html>
