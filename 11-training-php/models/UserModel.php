<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $sql = 'SELECT * FROM users WHERE id = '.$id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword) {
        $sql = 'SELECT * FROM users WHERE user_name LIKE %'.$keyword.'%'. ' OR user_email LIKE %'.$keyword.'%';
        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
//    public function auth($userName, $password) {
//        $md5Password = md5($password);
//        $sql = 'SELECT * FROM users WHERE name = "' . $userName . '" AND password = "'.$md5Password.'"';
//
//        $user = $this->select($sql);
//        return $user;
//    }

    public function auth($userName, $password) {
        $md5Password = md5($password);

        // 1. Sử dụng Prepared Statement với placeholders (?)
        // Tên và mật khẩu được mã hóa sẽ được thay thế an toàn
        $sql = 'SELECT * FROM users WHERE name = ? AND password = ?';

        // 2. Chuẩn bị (Prepare) câu lệnh SQL
        $stmt = self::$_connection->prepare($sql);

        // KIỂM TRA LỖI: Luôn kiểm tra xem lệnh prepare có thành công không
        if (!$stmt) {
            die('Lỗi prepare SQL: ' . self::$_connection->error);
        }

        // 3. Liên kết tham số (Bind Parameters)
        // "ss" chỉ định rằng cả hai tham số đều là chuỗi (string)
        $stmt->bind_param("ss", $userName, $md5Password);

        // 4. Thực thi (Execute)
        $stmt->execute();

        // 5. Lấy kết quả
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        // 6. Đóng Statement
        $stmt->close();

        return $rows;
    }


    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        $sql = 'DELETE FROM users WHERE id = '.$id;
        return $this->delete($sql);

    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) .'", 
                 password="'. md5($input['password']) .'"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input) {
        $sql = "INSERT INTO `app_web1`.`users` (`name`, `password`) VALUES (" .
                "'" . $input['name'] . "', '".md5($input['password'])."')";

        $user = $this->insert($sql);

        return $user;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = []) {
        //Keyword
        if (!empty($params['keyword'])) {
            $sql = 'SELECT * FROM users WHERE name LIKE "%' . $params['keyword'] .'%"';

            //Keep this line to use Sql Injection
            //Don't change
            //Example keyword: abcef%";TRUNCATE banks;##
            $users = self::$_connection->multi_query($sql);

            //Get data
            $users = $this->query($sql);
        } else {
            $sql = 'SELECT * FROM users';
            $users = $this->select($sql);
        }

        return $users;
    }
}