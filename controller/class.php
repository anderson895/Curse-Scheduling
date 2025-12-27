
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


    public function get_faculty_and_gec() {
    $query = "SELECT * FROM `users` WHERE `user_type` IN (?, ?)";
    $stmt = $this->conn->prepare($query);
    
    $faculty = 'faculty';
    $gec = 'gec';
    
    $stmt->bind_param("ss", $faculty, $gec);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}




 public function get_schedules() {
    $query = "
        SELECT s.sch_id, s.sch_user_id, s.sch_schedule,
               u.user_fname, u.user_lname,u.user_type
        FROM schedule s
        JOIN users u ON s.sch_user_id = u.user_id
        WHERE u.user_type IN ('faculty', 'gec')
        ORDER BY s.sch_user_id ASC
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        // Add full faculty name
        $row['faculty_name'] = $row['user_fname'] . ' ' . $row['user_lname'];
        $row['user_type'] = $row['user_type'];
        // Decode JSON schedule for frontend
        $row['sch_schedule'] = json_decode($row['sch_schedule'], true);
        $schedules[] = $row;
    }

    return $schedules;
}





// -------------------------
// DELETE SCHEDULE
// -------------------------
public function delete_schedule($sch_id) {
    $sch_id = intval($sch_id); // ensure it's an integer

    $query = "DELETE FROM schedule WHERE sch_id = ?";
    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database prepare failed: ' . $this->conn->error];
    }

    $stmt->bind_param("i", $sch_id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Schedule deleted successfully.'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to delete schedule: ' . $this->conn->error];
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

        public function add_subject($subject_code, $subject_name, $units, $subject_type) {
            $query = "INSERT INTO `subjects`(`subject_code`, `subject_name`, `subject_unit`, `subject_type`) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssis", $subject_code, $subject_name, $units, $subject_type);

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

        public function update_subject($subject_id, $subject_code, $subject_name, $units, $subject_type) {
            $query = "UPDATE `subjects` 
                    SET `subject_code` = ?, `subject_name` = ?, `subject_unit` = ?, `subject_type` = ? 
                    WHERE `subject_id` = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssisi", $subject_code, $subject_name, $units, $subject_type, $subject_id);

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





// ---------------- CREATE SCHEDULE ----------------
public function create_schedule($sch_user_id, $sch_schedule_json) {
    $sch_user_id = intval($sch_user_id);

    // Decode the incoming JSON
    $scheduleData = json_decode($sch_schedule_json, true);

    if (!isset($scheduleData['schedule']) || empty($scheduleData['schedule'])) {
        return ['success' => false, 'message' => 'No subjects to schedule.'];
    }

    // Generate random time slots based on entry hours
    $scheduleData['schedule'] = $this->assign_random_slots($scheduleData['schedule']);

    // Encode back to JSON
    $sch_schedule_json = json_encode($scheduleData);

    $stmt = $this->conn->prepare("INSERT INTO schedule (sch_user_id, sch_schedule) VALUES (?, ?)");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
    }

    $stmt->bind_param("is", $sch_user_id, $sch_schedule_json);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Schedule created successfully.'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to create schedule: ' . $stmt->error];
    }
}

// ---------------- UPDATE SCHEDULE ----------------
public function update_schedule($sch_id, $sch_user_id, $sch_schedule_json) {
    $sch_id = intval($sch_id);
    $sch_user_id = intval($sch_user_id);

    $scheduleData = json_decode($sch_schedule_json, true);
    if (!isset($scheduleData['schedule']) || empty($scheduleData['schedule'])) {
        return ['success' => false, 'message' => 'No subjects to schedule.'];
    }

    // Generate random time slots based on entry hours
    $scheduleData['schedule'] = $this->assign_random_slots($scheduleData['schedule']);

    $sch_schedule_json = json_encode($scheduleData);

    $stmt = $this->conn->prepare("UPDATE schedule SET sch_user_id = ?, sch_schedule = ? WHERE sch_id = ?");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
    }

    $stmt->bind_param("isi", $sch_user_id, $sch_schedule_json, $sch_id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Schedule updated successfully.'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update schedule: ' . $stmt->error];
    }
}

// ---------------- ASSIGN RANDOM SLOTS ----------------
public function assign_random_slots($schedule) {
    // Available 30-min slots from 7:00 AM to 9:00 PM
    $available_slots = [];
    for ($h = 7; $h < 21; $h++) {
        $available_slots[] = sprintf("%02d:00", $h);
        $available_slots[] = sprintf("%02d:30", $h);
    }

    $newSchedule = [];
    foreach ($schedule as $day => $subjects) {
        $newSchedule[$day] = [];
        $day_slots = $available_slots;

        foreach ($subjects as $id => $entry) {
            $subject = $entry['subject'] ?? $entry;
            $hours = isset($entry['hours']) ? floatval($entry['hours']) : 0.5;
            $duration_slots = $hours * 2; // each 0.5 hour = 1 slot

            // Find possible start indices
            $possible_starts = [];
            for ($i = 0; $i <= count($day_slots) - $duration_slots; $i++) {
                $possible_starts[] = $i;
            }

            if (empty($possible_starts)) break;

            $start_index = $possible_starts[array_rand($possible_starts)];
            $start_time = $day_slots[$start_index];
            $end_time_index = $start_index + $duration_slots - 1;
            $end_time = $this->increment_slot($day_slots[$end_time_index], 30);

            // Assign schedule entry with subject, hours, and time
            $newSchedule[$day][] = [
                'subject' => $subject,
                'hours'   => $hours,
                'time'    => [
                    'from' => $start_time,
                    'to'   => $end_time
                ]
            ];

            // Remove used slots
            array_splice($day_slots, $start_index, $duration_slots);
        }
    }

    return $newSchedule;
}

// ---------------- HELPER: increment a time string ----------------
private function increment_slot($time, $minutes) {
    $t = DateTime::createFromFormat('H:i', $time);
    $t->modify("+{$minutes} minutes");
    return $t->format('H:i');
}











    


}