if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $user_id = $_POST['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $sql_user = "SELECT * FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql_user);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($old_password, $user['password'])) {
            $sql_update = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
            $conn->query($sql_update);
            echo json_encode(["success" => true, "message" => "Password updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect old password."]);
        }
    }
    exit();
}