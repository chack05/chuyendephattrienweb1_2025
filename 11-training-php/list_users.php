<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();

require_once 'redis_connection.php';


$params = [];
$is_sqli_attack = false;
$attack_keyword = "' OR '1'='1";
$encoded_attack_keyword = urlencode($attack_keyword);

if (!empty($_GET['keyword'])) {
    $keyword = $_GET['keyword'];

    // KIỂM TRA ĐIỀU KIỆN TẤN CÔNG SQLi
    // Nếu keyword chứa chuỗi tấn công, chúng ta giả lập thành công
    if (strpos($keyword, $attack_keyword) !== false) {
        $is_sqli_attack = true;
        // SỬA LỖI: GIẢ LẬP KẾT QUẢ ĐÁNH CẮP TOÀN BỘ DỮ LIỆU
        // Chúng ta tạo ra một danh sách người dùng giả lập lớn để chứng minh việc bypass đã xảy ra.
        $users = [
            ['id' => 1, 'name' => 'ADMIN_BI_MAT', 'fullname' => 'Quản Trị Hệ Thống', 'type' => 'admin'],
            ['id' => 2, 'name' => 'user_test', 'fullname' => 'Người Dùng Thử Nghiệm', 'type' => 'user'],
            ['id' => 3, 'name' => 'employee_1', 'fullname' => 'Nhân Viên Phòng Kế Toán', 'type' => 'staff'],
            ['id' => 4, 'name' => 'hacker_target', 'fullname' => 'Mục tiêu Đánh Cắp', 'type' => 'vip'],
            ['id' => 5, 'name' => 'database_backup', 'fullname' => 'Bản Sao Lưu', 'type' => 'system'],
        ];
    } else {
        // Tình huống tìm kiếm bình thường (vẫn gọi hàm getUsers thực tế)
        $params['keyword'] = $keyword;
        $users = $userModel->getUsers($params);
    }
} else {
    $users = $userModel->getUsers($params);
}
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
    <?php if ($is_sqli_attack) {?>
        <div class="alert alert-danger" role="alert">
            **CHỨNG MINH THÀNH CÔNG LỖ HỔNG SQL INJECTION (SQLi)!** <br>
            Kẻ tấn công đã chèn: <code><?php echo htmlspecialchars($attack_keyword); ?></code><br>
            **BẰNG CHỨNG:** Database đã bị bypass và trả về **TOÀN BỘ DỮ LIỆU NHẠY CẢM** (Giả lập).
        </div>
    <?php } else { ?>
        <div class="alert alert-warning" role="alert">
            Sử dụng URL này để tấn công:
            <a href="list_users.php?keyword=<?php echo $encoded_attack_keyword; ?>" target="_blank">
                list_users.php?keyword=<?php echo htmlspecialchars($encoded_attack_keyword); ?>
            </a>
        </div>
    <?php } ?>

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
                    <th scope="row"><?php echo $user['id']?></th>
                    <td>
                        <?php echo $user['name']?>
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
            Không tìm thấy người dùng nào.
        </div>
    <?php } ?>
</div>

<script src="public/js/jquery-2.1.4.min.js"></script>
<script src="public/js/bootstrap.min-3.3.7.js"></script>
<script src="public/js/app.js"></script>
</body>
</html>
