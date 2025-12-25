
<?php


include ('config.php');

date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }



      public function Login($username, $password)
        {
            $query = $this->conn->prepare("SELECT * FROM `users` WHERE `user_username` = ?");
            $query->bind_param("s", $username);

            if ($query->execute()) {
                $result = $query->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    if (password_verify($password, $user['user_password'])) {
                        // 🔍 Check if inactive
                        if ($user['user_status'] == 0) {
                            $query->close();
                            return [
                                'success' => false,
                                'message' => 'Your account is not active.'
                            ];
                        }

                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['user_type'] = $user['user_type']; 

                        $query->close();
                        return [
                            'success' => true,
                            'message' => 'Login successful.',
                            'data' => [
                                'user_id' => $user['user_id'],
                                'user_type' => $user['user_type'], 
                            ]
                        ];
                    } else {
                        $query->close();
                        return ['success' => false, 'message' => 'Incorrect password.'];
                    }
                } else {
                    $query->close();
                    return ['success' => false, 'message' => 'User not found.'];
                }
            } else {
                $query->close();
                return ['success' => false, 'message' => 'Database error during execution.'];
            }
        }









}