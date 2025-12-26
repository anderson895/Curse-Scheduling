
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





    public function get_all_accounts($user_type) {

        if($user_type !== 'all'){
            $query = "SELECT * FROM `users` WHERE `user_type` = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $user_type);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);

        } else {
            $query = "SELECT * FROM `users`";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC); // <-- missing return
        }
    }




    // Check if an email is already registered
    public function isEmailExist($email) {
        $query = "SELECT user_id FROM `users` WHERE `user_email` = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0; 
    }




    public function CreateAccount($username, $email, $first_name, $middle_name, $last_name, $password, $type, $user_status) {
        if ($this->isEmailExist($email)) {
            return [
                'success' => false,
                'message' => 'Email already registered.'
            ];
        }
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user
        $query = "INSERT INTO `users`(`user_username`, `user_email`, `user_fname`, `user_mname`, `user_lname`, `user_password`, `user_type`,`user_status`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssssi", $username, $email, $first_name, $middle_name, $last_name, $hashedPassword, $type, $user_status);
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Registration successful.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }

    public function update_account($user_id, $username, $email, $first_name, $middle_name, $last_name) {
        $query = "UPDATE `users` 
                  SET `user_username` = ?, `user_email` = ?, `user_fname` = ?, `user_mname` = ?, `user_lname` = ? 
                  WHERE `user_id` = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssi", $username, $email, $first_name, $middle_name, $last_name, $user_id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Account updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update account. Please try again.'
            ];
        }
    }

    public function add_subject($subject_code, $subject_name, $units) {
        $query = "INSERT INTO `subjects`(`subject_code`, `subject_name`, `subject_unit`) 
                  VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $subject_code, $subject_name, $units);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Subject added successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to add subject. Please try again.'
            ];
        }
    }


    public function get_all_subjects() {
        $query = "SELECT * FROM `subjects`";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_subject_by_id($subject_id) {
        $query = "SELECT * FROM `subjects` WHERE `subject_id` = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update_subject($subject_id, $subject_code, $subject_name, $units) {
        $query = "UPDATE `subjects` 
                  SET `subject_code` = ?, `subject_name` = ?, `subject_unit` = ? 
                  WHERE `subject_id` = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssii", $subject_code, $subject_name, $units, $subject_id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Subject updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update subject. Please try again.'
            ];
        }
    }

    public function delete_subject($subject_id) {
        $query = "DELETE FROM `subjects` WHERE `subject_id` = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $subject_id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Subject deleted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete subject. Please try again.'
            ];
        }
    }

    public function get_curriculum() {
        // Join curriculum with subjects to get subject details
        $sql = "SELECT c.id, c.year_semester, 
                    s.subject_id, s.subject_code, s.subject_name, s.subject_unit
                FROM curriculum c
                JOIN subjects s ON c.subject_id = s.subject_id
                ORDER BY c.year_semester, s.subject_code";
        
        $result = $this->conn->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }   

    public function get_curriculum_by_id($curriculum_id) {
        $id = intval($curriculum_id);

        // Join curriculum with subjects to get subject details
        $sql = "SELECT c.id, c.year_semester, 
                    s.subject_id, s.subject_code, s.subject_name, s.subject_unit
                FROM curriculum c
                JOIN subjects s ON c.subject_id = s.subject_id
                WHERE c.id = $id";
        
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_assoc();
        }
        return null;
    }
    

    public function add_curriculum($year_semester, $subject_id) {
        $year_semester = $this->conn->real_escape_string($year_semester);
        $subject_id = intval($subject_id);

        $sql = "INSERT INTO curriculum (year_semester, subject_id) VALUES ('$year_semester', $subject_id)";
        if ($this->conn->query($sql)) {
            return ['success'=>true, 'message'=>'Curriculum added successfully'];
        } else {
            return ['success'=>false, 'message'=>$this->conn->error];
        }
    }

    public function update_curriculum($id, $year_semester, $subject_id) {
        $id = intval($id);
        $year_semester = $this->conn->real_escape_string($year_semester);
        $subject_id = intval($subject_id);

        $sql = "UPDATE curriculum SET year_semester='$year_semester', subject_id=$subject_id WHERE id=$id";
        if ($this->conn->query($sql)) {
            return ['success'=>true, 'message'=>'Curriculum updated successfully'];
        } else {
            return ['success'=>false, 'message'=>$this->conn->error];
        }
    }

    public function delete_curriculum($id) {
        $id = intval($id);
        $sql = "DELETE FROM curriculum WHERE id=$id";
        if ($this->conn->query($sql)) {
            return ['success'=>true, 'message'=>'Curriculum deleted successfully'];
        } else {
            return ['success'=>false, 'message'=>$this->conn->error];
        }
    }


    public function toggle_account_status($user_id)
    {
        $user_id = (int) $user_id;

        // SQL toggles status directly
        $query = "UPDATE users 
                SET user_status = IF(user_status = 1, 0, 1)
                WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database prepare failed.'
            ];
        }

        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return [
                'success' => true,
                'message' => 'Account status updated successfully.'
            ];
        }

        $stmt->close();
        return [
            'success' => false,
            'message' => 'Failed to update account status.'
        ];
    }


}