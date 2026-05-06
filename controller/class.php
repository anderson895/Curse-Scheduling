
<?php


include ('config.php');

date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }


     // -----------------------------
    // FETCH ALL REPORTS
    // -----------------------------
    public function fetchCurriculumReport() {
        $query = "SELECT * FROM curriculum ORDER BY program, year_level, semester, subject_code";
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchScheduleReport() {
        $query = "SELECT s.sch_schedule, u.user_fname, u.user_mname, u.user_lname 
                  FROM schedule s 
                  INNER JOIN users u ON s.sch_user_id = u.user_id
                  ORDER BY u.user_lname";
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchSubjectsReport() {
        $query = "SELECT * FROM curriculum ORDER BY program, subject_code";
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchUsersReport() {
        $query = "SELECT * FROM users ORDER BY user_lname";
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchDashboard() {
        $dashboard = [];

        // Total Users
        $userResult = $this->conn->query("SELECT COUNT(*) AS total_users FROM users");
        $dashboard['total_users'] = $userResult ? $userResult->fetch_assoc()['total_users'] : 0;

        // Total Subjects
        $subjectResult = $this->conn->query("SELECT COUNT(*) AS total_subjects FROM curriculum");
        $dashboard['total_subjects'] = $subjectResult ? $subjectResult->fetch_assoc()['total_subjects'] : 0;

        // Total Schedules
        $scheduleResult = $this->conn->query("SELECT COUNT(*) AS total_schedules FROM schedule");
        $dashboard['total_schedules'] = $scheduleResult ? $scheduleResult->fetch_assoc()['total_schedules'] : 0;

        return $dashboard;
    }



public function fetchAllSchedule($schId = null)
{
    // Step 1: Fetch schedule with faculty
    $sql = "SELECT s.sch_id, s.sch_schedule, u.user_fname, u.user_lname
            FROM schedule s
            LEFT JOIN users u ON u.user_id = s.sch_user_id";
    if ($schId !== null) {
        $sql .= " WHERE s.sch_id = " . intval($schId);
    }
    $sql .= " ORDER BY s.sch_id DESC";

    $result = $this->conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['faculty_name'] = $row['user_fname'] . ' ' . $row['user_lname'];
            $schedule = json_decode($row['sch_schedule'], true);

            foreach ($schedule['schedule'] as $day => &$daySlots) {
                foreach ($daySlots as &$slot) {
                    $subjectCode = $slot['subject'];
                    $subResult = $this->conn->query("SELECT * FROM curriculum WHERE subject_code = '" . $this->conn->real_escape_string($subjectCode) . "' LIMIT 1");
                    if ($subResult && $subRow = $subResult->fetch_assoc()) {
                        $slot['subject_details'] = $subRow; 
                    } else {
                        $slot['subject_details'] = null; 
                    }
                }
            }

            $row['sch_schedule'] = $schedule;
            unset($row['user_fname'], $row['user_lname']);
            $data[] = $row;
        }
    }

    return $data;
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
        $query = "SELECT * FROM `users` WHERE `user_type` IN (?, ?) AND `user_status` = 1";
        $stmt = $this->conn->prepare($query);
        
        $faculty = 'faculty';
        $gec = 'gec';
        
        $stmt->bind_param("ss", $faculty, $gec);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }



    public function get_schedule_gec_details($user_id) {
        $query = "SELECT * FROM `users` WHERE `user_type` IN (?, ?) AND `user_id` = ?";
        $stmt = $this->conn->prepare($query);
        
        $faculty = 'faculty';
        $gec = 'gec';
        
        $stmt->bind_param("ssi", $faculty, $gec, $user_id);
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



    public function get_schedules_with_subjects() {
    $query = "
        SELECT s.sch_id, s.sch_user_id, s.sch_schedule,
               u.user_fname, u.user_lname, u.user_type
        FROM schedule s
        JOIN users u ON s.sch_user_id = u.user_id
        WHERE u.user_type IN ('faculty', 'gec')
        ORDER BY s.sch_user_id DESC
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $row['faculty_name'] = $row['user_fname'] . ' ' . $row['user_lname'];
        $row['user_type'] = $row['user_type'];

        // Decode schedule JSON
        $schedule = json_decode($row['sch_schedule'], true);

        // Attach subject details to each slot
        foreach ($schedule['schedule'] as $day => &$daySlots) {
            foreach ($daySlots as &$slot) {
                $subjectCode = $slot['subject'];
                $subResult = $this->conn->query("SELECT * FROM curriculum WHERE subject_code = '" . $this->conn->real_escape_string($subjectCode) . "' LIMIT 1");
                if ($subResult && $subRow = $subResult->fetch_assoc()) {
                    $slot['subject_details'] = $subRow;
                } else {
                    $slot['subject_details'] = null;
                }
            }
        }

        $row['sch_schedule'] = $schedule;
        unset($row['user_fname'], $row['user_lname']);
        $schedules[] = $row;
    }

    return $schedules;
}









public function get_schedules_gec_details($user_id) {
    $query = "
        SELECT s.sch_id, s.sch_user_id, s.sch_schedule,
               u.user_fname, u.user_lname, u.user_type
        FROM schedule s
        JOIN users u ON s.sch_user_id = u.user_id
        WHERE u.user_type IN ('faculty', 'gec') AND s.sch_user_id = ?
        ORDER BY s.sch_user_id ASC
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $user_id);           
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $row['faculty_name'] = $row['user_fname'] . ' ' . $row['user_lname'];
        $row['user_type'] = $row['user_type'];

        // Decode schedule JSON
        $schedule = json_decode($row['sch_schedule'], true);

        // Attach subject details to each slot
        foreach ($schedule['schedule'] as $day => &$daySlots) {
            foreach ($daySlots as &$slot) {
                $subjectCode = $slot['subject'];
                $subResult = $this->conn->query("SELECT * FROM curriculum WHERE subject_code = '" . $this->conn->real_escape_string($subjectCode) . "' LIMIT 1");
                if ($subResult && $subRow = $subResult->fetch_assoc()) {
                    $slot['subject_details'] = $subRow;
                } else {
                    $slot['subject_details'] = null;
                }
            }
        }

        $row['sch_schedule'] = $schedule;
        unset($row['user_fname'], $row['user_lname']);
        $schedules[] = $row;
    }

    return $schedules;
}

// $schedule = $db->get_user_schedule($id);





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

        public function add_subject($program, $curriculum_year, $year_level, $semester, $subject_code, $subject_name, $lec_hours, $lab_hours, $lec_units, $lab_units, $prerequisite) {
            $query = "INSERT INTO `curriculum`(`program`,`curriculum_year`,`year_level`,`semester`,`subject_code`,`subject_name`,`lec_hours`,`lab_hours`,`lec_units`,`lab_units`,`prerequisite`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssiissiiiis", $program, $curriculum_year, $year_level, $semester, $subject_code, $subject_name, $lec_hours, $lab_hours, $lec_units, $lab_units, $prerequisite);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Subject added successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to add subject. Please try again.'];
            }
        }



        public function get_all_subjects() {
            $query = "SELECT * FROM `curriculum`";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function get_curriculum_by_id($curriculum_id) {
            $query = "SELECT * FROM `curriculum` WHERE `curriculum_id` = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $curriculum_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }

        public function update_subject($curriculum_id, $program, $curriculum_year, $year_level, $semester, $subject_code, $subject_name, $lec_hours, $lab_hours, $lec_units, $lab_units, $prerequisite) {
            $query = "UPDATE `curriculum` 
                    SET `program`=?, `curriculum_year`=?, `year_level`=?, `semester`=?, `subject_code`=?, `subject_name`=?, `lec_hours`=?, `lab_hours`=?, `lec_units`=?, `lab_units`=?, `prerequisite`=?
                    WHERE `curriculum_id`=?";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssiissiiiisi", $program, $curriculum_year, $year_level, $semester, $subject_code, $subject_name, $lec_hours, $lab_hours, $lec_units, $lab_units, $prerequisite, $curriculum_id);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Subject updated successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to update subject. Please try again.'];
            }
        }


    public function delete_subject($curriculum_id) {
        $query = "DELETE FROM `curriculum` WHERE `curriculum_id`=?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $curriculum_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Subject deleted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete subject. Please try again.'];
        }
    }

    public function get_curriculum() {
        // Join curriculum with curriculum to get subject details
        $sql = "SELECT c.id, c.year_semester, 
                    s.subject_id, s.subject_code, s.subject_name, s.subject_unit
                FROM curriculum c
                JOIN curriculum s ON c.subject_id = s.subject_id
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

   
    

    public function add_curriculum($year_semester, $curriculum_id) {
        $year_semester = $this->conn->real_escape_string($year_semester);
        $curriculum_id = intval($curriculum_id);

        $sql = "INSERT INTO curriculum (year_semester, subject_id) VALUES ('$year_semester', $curriculum_id)";
        if ($this->conn->query($sql)) {
            return ['success'=>true, 'message'=>'Curriculum added successfully'];
        } else {
            return ['success'=>false, 'message'=>$this->conn->error];
        }
    }

    public function update_curriculum($id, $year_semester, $curriculum_id) {
        $id = intval($id);
        $year_semester = $this->conn->real_escape_string($year_semester);
        $curriculum_id = intval($curriculum_id);

        $sql = "UPDATE curriculum SET year_semester='$year_semester', subject_id=$curriculum_id WHERE id=$id";
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

    // Decode to get semester for duplicate check
    $scheduleData = json_decode($sch_schedule_json, true);
    $semester = $scheduleData['semester'] ?? '';

    // Check if user already has a schedule for the SAME semester
    $check_stmt = $this->conn->prepare("SELECT sch_id FROM schedule WHERE sch_user_id = ? AND JSON_UNQUOTE(JSON_EXTRACT(sch_schedule, '$.semester')) = ?");
    if (!$check_stmt) {
        return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
    }
    $check_stmt->bind_param("is", $sch_user_id, $semester);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        return ['success' => false, 'message' => 'Schedule already exists for this user on the selected semester.'];
    }
    $check_stmt->close();

    // Decode the incoming JSON
    $scheduleData = json_decode($sch_schedule_json, true);

    if (!isset($scheduleData['schedule']) || empty($scheduleData['schedule'])) {
        return ['success' => false, 'message' => 'No curriculum to schedule.'];
    }

    // Generate time slots — avoid conflicts with ALL existing schedules
    $scheduleData['schedule'] = $this->assign_random_slots($scheduleData['schedule'], 0);

    // Encode back to JSON
    $sch_schedule_json = json_encode($scheduleData);

    $stmt = $this->conn->prepare("INSERT INTO schedule (sch_user_id, sch_schedule) VALUES (?, ?)");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
    }

    $stmt->bind_param("is", $sch_user_id, $sch_schedule_json);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Schedule created successfully.', 'saved_json' => $sch_schedule_json];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to create schedule: ' . $stmt->error];
    }
}


// ---------------- CHECK IF SCHEDULE EXISTS (per user + semester) ----------------
public function schedule_exists($sch_user_id, $semester = '') {
    $sch_user_id = intval($sch_user_id);

    if ($semester !== '') {
        $stmt = $this->conn->prepare("SELECT sch_id FROM schedule WHERE sch_user_id = ? AND JSON_UNQUOTE(JSON_EXTRACT(sch_schedule, '$.semester')) = ?");
        if (!$stmt) return false;
        $stmt->bind_param("is", $sch_user_id, $semester);
    } else {
        $stmt = $this->conn->prepare("SELECT sch_id FROM schedule WHERE sch_user_id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $sch_user_id);
    }

    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}


// ---------------- UPDATE SCHEDULE ----------------
public function update_schedule($sch_id, $sch_user_id, $sch_schedule_json) {
    $sch_id = intval($sch_id);
    $sch_user_id = intval($sch_user_id);

    $scheduleData = json_decode($sch_schedule_json, true);
    if (!isset($scheduleData['schedule']) || empty($scheduleData['schedule'])) {
        return ['success' => false, 'message' => 'No curriculum to schedule.'];
    }

    // Generate time slots — exclude this schedule's own slots to avoid false self-conflicts
    $scheduleData['schedule'] = $this->assign_random_slots($scheduleData['schedule'], $sch_id);

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

// Get all occupied time slots per day from ALL existing schedules (optionally excluding one sch_id)
public function get_occupied_slots($exclude_sch_id = 0) {
    $occupied = []; // ['Monday' => [['from'=>DateTime, 'to'=>DateTime], ...], ...]

    $stmt = $this->conn->prepare("SELECT sch_id, sch_schedule FROM schedule");
    if (!$stmt) return $occupied;
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if (intval($row['sch_id']) === intval($exclude_sch_id)) continue;
        $data = json_decode($row['sch_schedule'], true);
        foreach (($data['schedule'] ?? []) as $day => $entries) {
            foreach ($entries as $entry) {
                if (!isset($entry['time'])) continue;
                $occupied[$day][] = [
                    'from' => new DateTime($entry['time']['from']),
                    'to'   => new DateTime($entry['time']['to'])
                ];
            }
        }
    }
    $stmt->close();
    return $occupied;
}

public function assign_random_slots($schedule, $exclude_sch_id = 0) {
    $newSchedule = [];

    // Blocked ranges: lunch break
    $blocked_ranges = [
        ['from' => '12:00', 'to' => '13:00']
    ];

    // Get all slots already used by other schedules (conflict avoidance)
    $occupied = $this->get_occupied_slots($exclude_sch_id);

    // Generate all 30-min slots from 7:00 AM to 9:00 PM
    $all_slots = [];
    $current = new DateTime('07:00');
    $end     = new DateTime('21:00');
    while ($current < $end) {
        $slot_start = clone $current;
        $current->modify('+30 minutes');
        $slot_end = clone $current;
        $all_slots[] = ['from' => $slot_start, 'to' => $slot_end];
    }

    foreach ($schedule as $day => $curriculum) {
        $newSchedule[$day] = [];

        // Remove slots blocked by lunch and by existing schedules on this day
        $day_occupied = $occupied[$day] ?? [];
        $available_slots = array_values(array_filter($all_slots, function($slot) use ($blocked_ranges, $day_occupied) {
            // Check lunch
            foreach ($blocked_ranges as $range) {
                $rs = new DateTime($range['from']);
                $re = new DateTime($range['to']);
                if ($slot['from'] < $re && $slot['to'] > $rs) return false;
            }
            // Check other schedules
            foreach ($day_occupied as $occ) {
                if ($slot['from'] < $occ['to'] && $slot['to'] > $occ['from']) return false;
            }
            return true;
        }));

        // Re-index for array_slice / array_splice to work correctly
        $available_slots = array_values($available_slots);

        foreach ($curriculum as $id => $entry) {
            $subject      = $entry['subject'] ?? $entry;
            $hours        = isset($entry['hours']) ? floatval($entry['hours']) : 1;
            $room         = isset($entry['room']) ? trim($entry['room']) : '';
            $slots_needed = intval($hours * 2); // each 0.5h = 1 slot

            $assigned = [];

            for ($i = 0; $i <= count($available_slots) - $slots_needed; $i++) {
                $candidate = array_slice($available_slots, $i, $slots_needed);

                // Ensure slots are consecutive (no gaps)
                $consecutive = true;
                for ($c = 0; $c < count($candidate) - 1; $c++) {
                    if ($candidate[$c]['to'] != $candidate[$c+1]['from']) {
                        $consecutive = false;
                        break;
                    }
                }
                if (!$consecutive) continue;

                $assigned = $candidate;
                array_splice($available_slots, $i, $slots_needed);
                break;
            }

            if (empty($assigned)) {
                error_log("No available slots for {$subject} on {$day} — all slots occupied or not enough space.");
                // Still add the entry but mark time as unassigned so it doesn't silently disappear
                $entry_data = [
                    'subject' => $subject,
                    'hours'   => $hours,
                    'time'    => ['from' => '00:00', 'to' => '00:00'],
                    'conflict_warning' => true
                ];
                if ($room !== '') $entry_data['room'] = $room;
                $newSchedule[$day][] = $entry_data;
                continue;
            }

            // Mark these newly assigned slots as occupied for subsequent entries in this schedule
            foreach ($assigned as $a) {
                $day_occupied[] = ['from' => $a['from'], 'to' => $a['to']];
            }

            $assigned_entry = [
                'subject' => $subject,
                'hours'   => $hours,
                'time'    => [
                    'from' => $assigned[0]['from']->format('H:i'),
                    'to'   => end($assigned)['to']->format('H:i')
                ]
            ];
            if ($room !== '') $assigned_entry['room'] = $room;
            $newSchedule[$day][] = $assigned_entry;
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











    

// ---- CHECK TIME CONFLICT across all schedules ----
public function check_schedule_conflict($exclude_sch_id, $day, $time_from, $time_to, $subject = null) {
    $stmt = $this->conn->prepare("SELECT s.sch_id, s.sch_schedule, u.user_fname, u.user_lname FROM schedule s JOIN users u ON s.sch_user_id = u.user_id");
    if (!$stmt) return [];
    $stmt->execute();
    $result = $stmt->get_result();
    $conflicts = [];
    $new_from = new DateTime($time_from);
    $new_to   = new DateTime($time_to);
    while ($row = $result->fetch_assoc()) {
        if (intval($row['sch_id']) === intval($exclude_sch_id)) continue;
        $data = json_decode($row['sch_schedule'], true);
        $daySchedule = $data['schedule'][$day] ?? [];
        foreach ($daySchedule as $entry) {
            if (!isset($entry['time'])) continue;

            // ✅ Skip if different subject (only flag conflict if same subject)
            if ($subject !== null && isset($entry['subject']) && $entry['subject'] !== $subject) {
                continue;
            }

            $ex_from = new DateTime($entry['time']['from']);
            $ex_to   = new DateTime($entry['time']['to']);
            if ($new_from < $ex_to && $new_to > $ex_from) {
                $conflicts[] = $row['user_fname'] . ' ' . $row['user_lname'];
                break;
            }
        }
    }
    $stmt->close();
    return $conflicts;
}

// ---------------- CHECK ROOM CONFLICT ----------------
// Checks if a specific room is already occupied at a given day/time across ALL schedules
public function check_room_conflict($room, $day, $time_from, $time_to, $exclude_sch_id = 0, $exclude_entry_index = -1) {
    if ($room === null || trim($room) === '') return [];

    $room = trim($room);
    $stmt = $this->conn->prepare("SELECT s.sch_id, s.sch_schedule, u.user_fname, u.user_lname FROM schedule s JOIN users u ON s.sch_user_id = u.user_id");
    if (!$stmt) return [];
    $stmt->execute();
    $result = $stmt->get_result();
    $conflicts = [];
    $new_from_dt = new DateTime($time_from);
    $new_to_dt   = new DateTime($time_to);

    while ($row = $result->fetch_assoc()) {
        $is_same_schedule = (intval($row['sch_id']) === intval($exclude_sch_id));
        $data = json_decode($row['sch_schedule'], true);
        $daySchedule = $data['schedule'][$day] ?? [];

        foreach ($daySchedule as $idx => $entry) {
            if (!isset($entry['time'])) continue;

            // Skip the exact entry being edited (same schedule + same index)
            if ($is_same_schedule && intval($idx) === intval($exclude_entry_index)) continue;

            // Only check entries that have the SAME room
            $entry_room = isset($entry['room']) ? trim($entry['room']) : '';
            if ($entry_room === '' || strcasecmp($entry_room, $room) !== 0) continue;

            // Check time overlap
            $ex_from = new DateTime($entry['time']['from']);
            $ex_to   = new DateTime($entry['time']['to']);
            if ($new_from_dt < $ex_to && $new_to_dt > $ex_from) {
                $faculty_name = $row['user_fname'] . ' ' . $row['user_lname'];
                $subject = $entry['subject'] ?? 'Unknown';
                $conflicts[] = [
                    'faculty' => $faculty_name,
                    'subject' => $subject,
                    'time'    => $entry['time']['from'] . '-' . $entry['time']['to'],
                    'room'    => $entry_room
                ];
                break; // one conflict per schedule is enough
            }
        }
    }
    $stmt->close();
    return $conflicts;
}

// ---- MANUALLY EDIT A SPECIFIC ENTRY TIME ----
public function edit_entry_time($sch_id, $day, $entry_index, $new_from, $new_to, $new_room = null) {
    $sch_id = intval($sch_id);
    $stmt = $this->conn->prepare("SELECT sch_schedule FROM schedule WHERE sch_id = ?");
    if (!$stmt) return ['success' => false, 'message' => 'Prepare failed'];
    $stmt->bind_param("i", $sch_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$res) return ['success' => false, 'message' => 'Schedule not found'];
    $data = json_decode($res['sch_schedule'], true);
    if (!isset($data['schedule'][$day][$entry_index])) {
        return ['success' => false, 'message' => 'Entry not found'];
    }
    $from_dt = DateTime::createFromFormat('H:i', $new_from);
    $to_dt   = DateTime::createFromFormat('H:i', $new_to);
    if (!$from_dt || !$to_dt) return ['success' => false, 'message' => 'Invalid time format'];
    if ($from_dt >= $to_dt)   return ['success' => false, 'message' => 'Start time must be before end time'];

    // ✅ Self-conflict check — ibang entries ng sariling schedule sa parehong araw
    $own_entries = $data['schedule'][$day] ?? [];
    foreach ($own_entries as $idx => $entry) {
        if ($idx === $entry_index) continue; // Skip ang entry na ine-edit
        if (!isset($entry['time'])) continue;

        $ex_from = DateTime::createFromFormat('H:i', $entry['time']['from']);
        $ex_to   = DateTime::createFromFormat('H:i', $entry['time']['to']);
        if (!$ex_from || !$ex_to) continue;

        if ($from_dt < $ex_to && $to_dt > $ex_from) {
            $conflict_subject = $entry['subject'] ?? 'another subject';
            return [
                'success' => false,
                'message' => "Time conflict with your own schedule: '{$conflict_subject}' is already at {$entry['time']['from']} - {$entry['time']['to']} on {$day}."
            ];
        }
    }

    // ✅ Cross-schedule conflict — same subject lang ang iche-check
    $subject = $data['schedule'][$day][$entry_index]['subject'] ?? null;
    $conflicts = $this->check_schedule_conflict($sch_id, $day, $new_from, $new_to, $subject);
    if (!empty($conflicts)) {
        return ['success' => false, 'message' => 'Time conflict with same subject in: ' . implode(', ', $conflicts)];
    }

    // ✅ Room conflict check — same room, same day, overlapping time across ALL schedules
    $check_room = ($new_room !== null) ? $new_room : ($data['schedule'][$day][$entry_index]['room'] ?? '');
    if (trim($check_room) !== '') {
        $room_conflicts = $this->check_room_conflict($check_room, $day, $new_from, $new_to, $sch_id, $entry_index);
        if (!empty($room_conflicts)) {
            $msg_parts = [];
            foreach ($room_conflicts as $rc) {
                $msg_parts[] = "{$rc['faculty']} ({$rc['subject']} {$rc['time']})";
            }
            return ['success' => false, 'message' => "Room conflict: '{$check_room}' is already occupied on {$day} by " . implode(', ', $msg_parts)];
        }
    }

    $data['schedule'][$day][$entry_index]['time']['from'] = $new_from;
    $data['schedule'][$day][$entry_index]['time']['to']   = $new_to;
    if ($new_room !== null) {
        $data['schedule'][$day][$entry_index]['room'] = $new_room;
    }
    $new_json = json_encode($data);
    $upd = $this->conn->prepare("UPDATE schedule SET sch_schedule = ? WHERE sch_id = ?");
    if (!$upd) return ['success' => false, 'message' => 'Prepare failed'];
    $upd->bind_param("si", $new_json, $sch_id);
    if ($upd->execute()) { $upd->close(); return ['success' => true, 'message' => 'Schedule time updated successfully.']; }
    $upd->close();
    return ['success' => false, 'message' => 'Update failed'];
}



// =============================================================
// FACULTY META (availability + specializations)
// =============================================================
// Idempotent migration: ensure faculty_meta has availability_admin column.
// Dean / Program-Chair-set availability lives there; the faculty's self-set
// availability stays in `availability`. For Gen Ed subjects, the auto-gen
// uses availability_admin (mandatory, overrides faculty-self).
private function ensure_faculty_meta_columns() {
    static $done = false;
    if ($done) return;
    $done = true;
    $r = @$this->conn->query(
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'faculty_meta'
           AND COLUMN_NAME = 'availability_admin' LIMIT 1"
    );
    if ($r && $r->num_rows === 0) {
        @$this->conn->query(
            "ALTER TABLE faculty_meta ADD COLUMN availability_admin LONGTEXT NULL AFTER availability"
        );
    }
}

public function get_faculty_meta($user_id) {
    $this->ensure_faculty_meta_columns();
    $user_id = intval($user_id);
    $stmt = $this->conn->prepare(
        "SELECT availability, availability_admin, specializations
         FROM faculty_meta WHERE user_id = ?"
    );
    if (!$stmt) return ['availability' => [], 'availability_admin' => [], 'specializations' => []];
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return [
        'availability'        => $res ? (json_decode($res['availability'] ?? '[]', true) ?: []) : [],
        'availability_admin'  => $res ? (json_decode($res['availability_admin'] ?? '[]', true) ?: []) : [],
        'specializations'     => $res ? (json_decode($res['specializations'] ?? '[]', true) ?: []) : []
    ];
}

// Called by Dean / Program Chair — writes to availability_admin (Gen Ed source of truth).
public function save_faculty_meta($user_id, $availability_json, $specializations_json) {
    $this->ensure_faculty_meta_columns();
    $user_id = intval($user_id);

    // Validate JSON
    if (!is_string($availability_json))    $availability_json = json_encode($availability_json ?: new stdClass());
    if (!is_string($specializations_json)) $specializations_json = json_encode($specializations_json ?: []);
    if (json_decode($availability_json, true) === null && trim($availability_json) !== '{}' && trim($availability_json) !== '[]') {
        return ['success' => false, 'message' => 'Invalid availability format.'];
    }
    if (json_decode($specializations_json, true) === null && trim($specializations_json) !== '[]') {
        return ['success' => false, 'message' => 'Invalid specializations format.'];
    }

    $stmt = $this->conn->prepare(
        "INSERT INTO faculty_meta (user_id, availability_admin, specializations)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE availability_admin = VALUES(availability_admin),
                                 specializations    = VALUES(specializations)"
    );
    if (!$stmt) return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
    $stmt->bind_param("iss", $user_id, $availability_json, $specializations_json);
    if ($stmt->execute()) { $stmt->close(); return ['success' => true, 'message' => 'Faculty profile saved.']; }
    $stmt->close();
    return ['success' => false, 'message' => 'Failed to save faculty profile.'];
}

// Faculty self-service: update own availability and specializations.
// Caller must enforce that $user_id comes from the authenticated session.
public function save_my_profile($user_id, $availability_json, $specializations_json) {
    $this->ensure_faculty_meta_columns();
    $user_id = intval($user_id);
    if ($user_id <= 0) return ['success' => false, 'message' => 'Invalid user.'];

    if (!is_string($availability_json))    $availability_json = json_encode($availability_json ?: new stdClass());
    if (!is_string($specializations_json)) $specializations_json = json_encode($specializations_json ?: []);

    if (json_decode($availability_json, true) === null && trim($availability_json) !== '{}' && trim($availability_json) !== '[]') {
        return ['success' => false, 'message' => 'Invalid availability format.'];
    }
    if (json_decode($specializations_json, true) === null && trim($specializations_json) !== '[]') {
        return ['success' => false, 'message' => 'Invalid specializations format.'];
    }

    $stmt = $this->conn->prepare(
        "INSERT INTO faculty_meta (user_id, availability, specializations)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE availability    = VALUES(availability),
                                 specializations = VALUES(specializations)"
    );
    if (!$stmt) return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
    $stmt->bind_param("iss", $user_id, $availability_json, $specializations_json);
    if ($stmt->execute()) { $stmt->close(); return ['success' => true, 'message' => 'Profile saved.']; }
    $stmt->close();
    return ['success' => false, 'message' => 'Failed to save profile.'];
}

public function get_all_faculty_with_meta() {
    $this->ensure_faculty_meta_columns();
    $sql = "SELECT u.user_id, u.user_fname, u.user_lname, u.user_type,
                   fm.availability, fm.availability_admin, fm.specializations
            FROM users u
            LEFT JOIN faculty_meta fm ON fm.user_id = u.user_id
            WHERE u.user_type IN ('faculty','gec') AND u.user_status = 1";
    $res = $this->conn->query($sql);
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['availability']       = json_decode($r['availability'] ?? '[]', true) ?: [];
        $r['availability_admin'] = json_decode($r['availability_admin'] ?? '[]', true) ?: [];
        $r['specializations']    = json_decode($r['specializations'] ?? '[]', true) ?: [];
        $rows[] = $r;
    }
    return $rows;
}

// =============================================================
// SUBJECTS for a given program / year / semester
// =============================================================
public function get_subjects_by_program_year($program, $year_level, $semester, $tier = '', $curriculum_year = '') {
    // Frontend uses codes like BSCOE; curriculum uses BSCoE — match loosely (case-insensitive prefix).
    $sql = "SELECT * FROM curriculum
            WHERE LOWER(program) = LOWER(?)
              AND year_level = ?
              AND semester   = ?
              AND (lec_hours + lab_hours) > 0";
    $params = [$program, $year_level, $semester];
    $types  = "sss";

    if ($tier !== '' && in_array($tier, ['gen_ed','gen_eng','major'], true)) {
        $sql .= " AND course_tier = ?";
        $params[] = $tier;
        $types   .= "s";
    }
    if ($curriculum_year !== '') {
        // Match all visual variants ("2013-2014" / "2013–2014" / "2013 - 2014" / etc.)
        $variants = $this->curriculum_year_variants($curriculum_year);
        $placeholders = implode(',', array_fill(0, count($variants), '?'));
        $sql   .= " AND curriculum_year IN ($placeholders)";
        foreach ($variants as $v) {
            $params[] = $v;
            $types   .= "s";
        }
    }

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// Canonicalize a curriculum_year string: strip spaces, fold en/em/minus dashes to hyphen.
private function normalize_curriculum_year($y) {
    $y = str_replace(
        ["\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x88\x92", ' '],
        ['-', '-', '-', ''],
        (string)$y
    );
    return trim($y);
}

// All visual variants of a curriculum_year value that might be stored in the DB.
private function curriculum_year_variants($y) {
    $base = $this->normalize_curriculum_year($y); // e.g. "2013-2014"
    if ($base === '' || strpos($base, '-') === false) {
        return [$base];
    }
    $en  = "\xE2\x80\x93"; // –
    $em  = "\xE2\x80\x94"; // —
    $variants = [
        $base,
        str_replace('-', $en, $base),
        str_replace('-', $em, $base),
        str_replace('-', ' - ', $base),
        str_replace('-', " $en ", $base),
        str_replace('-', " $em ", $base),
    ];
    return array_values(array_unique($variants));
}

// Distinct curriculum years available for a given program (for the UI selector)
public function get_curriculum_years($program = '') {
    if ($program !== '') {
        $stmt = $this->conn->prepare(
            "SELECT DISTINCT curriculum_year FROM curriculum
             WHERE LOWER(program) = LOWER(?) AND curriculum_year <> ''"
        );
        $stmt->bind_param("s", $program);
    } else {
        $stmt = $this->conn->prepare(
            "SELECT DISTINCT curriculum_year FROM curriculum
             WHERE curriculum_year <> ''"
        );
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Normalize so visual duplicates collapse:
    //   "2013-2014", "2013–2014" (en-dash), "2013 - 2014" -> "2013-2014"
    $seen = [];
    foreach ($rows as $r) {
        $y = $r['curriculum_year'];
        // Replace en-dash, em-dash, minus-sign with regular hyphen, then strip spaces
        $y = str_replace(
            ["\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x88\x92", ' '],
            ['-', '-', '-', ''],
            $y
        );
        $y = trim($y);
        if ($y !== '') $seen[$y] = true;
    }
    $years = array_keys($seen);
    rsort($years); // newest first (string sort works for "YYYY-YYYY")
    return $years;
}

// =============================================================
// ROOMS — managed list of room numbers
// =============================================================
public function get_rooms($only_active = false) {
    $sql = "SELECT * FROM rooms"
         . ($only_active ? " WHERE is_active = 1" : "")
         . " ORDER BY room_name";
    $res = $this->conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

public function add_room($room_name, $room_type = 'lecture', $capacity = 0) {
    $room_name = trim($room_name);
    if ($room_name === '') return ['success'=>false,'message'=>'Room name is required.'];
    try {
        $stmt = $this->conn->prepare(
            "INSERT INTO rooms (room_name, room_type, capacity) VALUES (?, ?, ?)"
        );
        $cap = intval($capacity);
        $stmt->bind_param("ssi", $room_name, $room_type, $cap);
        $stmt->execute();
        $stmt->close();
        return ['success'=>true,'message'=>'Room added.'];
    } catch (mysqli_sql_exception $e) {
        return ['success'=>false,'message'=>(stripos($e->getMessage(),'duplicate')!==false ? 'Room already exists.' : 'Failed to add room.')];
    }
}

public function update_room($room_id, $room_name, $room_type, $capacity) {
    try {
        $stmt = $this->conn->prepare(
            "UPDATE rooms SET room_name = ?, room_type = ?, capacity = ? WHERE room_id = ?"
        );
        $cap = intval($capacity);
        $rid = intval($room_id);
        $stmt->bind_param("ssii", $room_name, $room_type, $cap, $rid);
        $stmt->execute();
        $stmt->close();
        return ['success'=>true,'message'=>'Room updated.'];
    } catch (mysqli_sql_exception $e) {
        return ['success'=>false,'message'=>(stripos($e->getMessage(),'duplicate')!==false ? 'Room name already exists.' : 'Failed to update room.')];
    }
}

public function toggle_room_status($room_id) {
    $stmt = $this->conn->prepare("UPDATE rooms SET is_active = IF(is_active=1,0,1) WHERE room_id = ?");
    $rid = intval($room_id);
    $stmt->bind_param("i", $rid);
    if ($stmt->execute()) { $stmt->close(); return ['success'=>true,'message'=>'Room status updated.']; }
    $stmt->close();
    return ['success'=>false,'message'=>'Failed to update room status.'];
}

public function delete_room($room_id) {
    $stmt = $this->conn->prepare("DELETE FROM rooms WHERE room_id = ?");
    $rid = intval($room_id);
    $stmt->bind_param("i", $rid);
    if ($stmt->execute()) { $stmt->close(); return ['success'=>true,'message'=>'Room deleted.']; }
    $stmt->close();
    return ['success'=>false,'message'=>'Failed to delete room.'];
}

// =============================================================
// COURSE TIER + PAIRING (revision1.txt)
// =============================================================
public function set_curriculum_meta($curriculum_id, $course_tier, $pairing) {
    if (!in_array($course_tier, ['gen_ed','gen_eng','major'], true)) {
        return ['success'=>false,'message'=>'Invalid course tier.'];
    }
    if (!in_array($pairing, ['NONE','MWF','TTH','WS'], true)) {
        return ['success'=>false,'message'=>'Invalid pairing.'];
    }
    $stmt = $this->conn->prepare(
        "UPDATE curriculum SET course_tier = ?, pairing = ? WHERE curriculum_id = ?"
    );
    $cid = intval($curriculum_id);
    $stmt->bind_param("ssi", $course_tier, $pairing, $cid);
    if ($stmt->execute()) { $stmt->close(); return ['success'=>true,'message'=>'Curriculum metadata updated.']; }
    $stmt->close();
    return ['success'=>false,'message'=>'Failed to update curriculum.'];
}

// =============================================================
// AUTO-GENERATE SCHEDULE
//   Inputs: program, year_level, semester, list of available rooms
//   Output: ['saved' => [...], 'unassigned' => [...], 'message' => ...]
// =============================================================
public function auto_generate_schedule($program, $year_level, $semester, $rooms = [], $tier = 'major', $curriculum_year = '', $merge_across_programs = false) {
    $year_level = (string) $year_level;

    $subjects = $this->get_subjects_by_program_year($program, $year_level, $semester, $tier, $curriculum_year);
    if (empty($subjects)) {
        $detail = $tier ? (' (' . str_replace('_',' ', $tier) . ' tier)') : '';
        if ($curriculum_year !== '') $detail .= ' [curriculum ' . $curriculum_year . ']';
        return ['success' => false, 'message' => 'No subjects found for ' . $program . ' year ' . $year_level . ' / ' . $semester . $detail . '.'];
    }
    $faculty_list = $this->get_all_faculty_with_meta();
    if (empty($faculty_list)) {
        return ['success' => false, 'message' => 'No active faculty found.'];
    }
    if (empty($rooms)) {
        // Fall back to active rooms from the DB (managed via /dean/rooms.php)
        $db_rooms = $this->get_rooms(true);
        $rooms = array_map(function($r){ return $r['room_name']; }, $db_rooms);
        if (empty($rooms)) {
            $rooms = ['301','302','303','304','305'];
        }
    }

    // Cohort key helper (lowercased program for case-insensitive matching).
    $make_cohort_key = function($prog, $yr, $sem) {
        return strtolower($prog) . '|' . $yr . '|' . $sem;
    };
    $current_cohort_key = $make_cohort_key($program, $year_level, $semester);

    // ---------- Cross-program merge map ----------
    // For each subject code, find sibling programs that offer the SAME subject_code
    // at the same year_level / semester (and curriculum_year, if specified).
    // sibling_cohorts_by_code[code] = ['cohort_keys'=>[...], 'cohort_labels'=>['BSEE','BSECE',...]]
    $sibling_cohorts_by_code = [];
    $cohort_meta = [];
    $cohort_meta[$current_cohort_key] = ['program' => $program, 'year_level' => $year_level, 'semester' => $semester];

    if ($merge_across_programs) {
        $codes = array_values(array_unique(array_map(function($s){ return $s['subject_code']; }, $subjects)));
        foreach ($codes as $code) {
            $sql = "SELECT DISTINCT program FROM curriculum
                    WHERE subject_code = ? AND year_level = ? AND semester = ?
                      AND LOWER(program) <> LOWER(?)";
            $params = [$code, $year_level, $semester, $program];
            $types  = "ssss";
            if ($curriculum_year !== '') {
                $sql .= " AND curriculum_year = ?";
                $params[] = $curriculum_year;
                $types   .= "s";
            }
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) continue;
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            if (empty($rows)) continue; // no sibling — no merge for this code

            $cohort_keys   = [$current_cohort_key];
            $cohort_labels = [$program];
            foreach ($rows as $r) {
                $other = $r['program'];
                $key = $make_cohort_key($other, $year_level, $semester);
                $cohort_keys[]   = $key;
                $cohort_labels[] = $other;
                $cohort_meta[$key] = ['program' => $other, 'year_level' => $year_level, 'semester' => $semester];
            }
            $sibling_cohorts_by_code[$code] = [
                'cohort_keys'   => $cohort_keys,
                'cohort_labels' => $cohort_labels,
            ];
        }
    }

    // Returns cohort_keys for a subject (just current cohort if not merged).
    $cohort_keys_for = function($code) use (&$sibling_cohorts_by_code, $current_cohort_key) {
        return $sibling_cohorts_by_code[$code]['cohort_keys'] ?? [$current_cohort_key];
    };
    $cohort_labels_for = function($code) use (&$sibling_cohorts_by_code, $program) {
        return $sibling_cohorts_by_code[$code]['cohort_labels'] ?? [$program];
    };

    // ---------- Build busy maps from existing schedules ----------
    // faculty_busy[user_id][day] = [{from,to}]; cohort_busy[program|yr|sem][day]; room_busy[room][day]
    $faculty_busy = [];
    $cohort_busy  = [];
    $room_busy    = [];

    $existing = $this->conn->query("SELECT sch_user_id, sch_schedule FROM schedule");
    while ($row = $existing->fetch_assoc()) {
        $uid = intval($row['sch_user_id']);
        $data = json_decode($row['sch_schedule'], true);
        if (!isset($data['schedule']) || !is_array($data['schedule'])) continue;

        $cohort_key = strtolower($data['program'] ?? '') . '|' . ($data['year_level'] ?? '') . '|' . ($data['semester'] ?? '');

        foreach ($data['schedule'] as $day => $entries) {
            foreach ($entries as $entry) {
                if (!isset($entry['time']['from'], $entry['time']['to'])) continue;
                $from = $entry['time']['from'];
                $to   = $entry['time']['to'];
                if ($from === '00:00' && $to === '00:00') continue;

                $faculty_busy[$uid][$day][] = ['from' => $from, 'to' => $to];
                if (!empty($data['year_level'])) {
                    $cohort_busy[$cohort_key][$day][] = ['from' => $from, 'to' => $to];
                }
                if (!empty($entry['room'])) {
                    $room_busy[trim($entry['room'])][$day][] = ['from' => $from, 'to' => $to];
                }
            }
        }
    }

    // Returns the union of cohort_busy across every cohort the given subject is shared with.
    // For a non-merged subject this is just the current cohort's busy map.
    $cohort_busy_for_code = function($code) use (&$cohort_busy, $cohort_keys_for) {
        $keys = $cohort_keys_for($code);
        if (count($keys) === 1) {
            return $cohort_busy[$keys[0]] ?? [];
        }
        $merged = [];
        foreach ($keys as $k) {
            if (!isset($cohort_busy[$k])) continue;
            foreach ($cohort_busy[$k] as $day => $ranges) {
                foreach ($ranges as $r) {
                    $merged[$day][] = $r;
                }
            }
        }
        return $merged;
    };

    // ---------- Schedule subjects ----------
    // Two-pass strategy:
    //   PASS 1 (faculty-first): each faculty grabs every cohort subject that
    //     matches their declared specialization, so a specialist gets all
    //     their subjects assigned before any load-balancing kicks in.
    //   PASS 2 (subject-first fallback): leftover subjects (no specialist,
    //     or specialist had no free slot) go to whoever fits, least-loaded.
    usort($subjects, function($a, $b) {
        return ($b['lec_hours'] + $b['lab_hours']) <=> ($a['lec_hours'] + $a['lab_hours']);
    });

    $faculty_load = [];                  // user_id => total hours assigned in this run
    $by_cohort_faculty_schedule = [];    // [cohort_key][user_id]['Monday' => [entries...]]
    $merged_summary = [];                // [['subject_code'=>..,'cohorts'=>['BSEE','BSECE',...]]]
    $unassigned = [];
    $assigned_codes = [];

    $commit = function($uid, $code, $hours, $attempt)
        use (&$faculty_load, &$faculty_busy, &$cohort_busy, &$room_busy,
             &$by_cohort_faculty_schedule, &$merged_summary,
             $cohort_keys_for, $cohort_labels_for) {
        $faculty_load[$uid] = ($faculty_load[$uid] ?? 0) + $hours;
        $cohort_keys   = $cohort_keys_for($code);
        $cohort_labels = $cohort_labels_for($code);
        $is_merged     = count($cohort_keys) > 1;

        if ($is_merged) {
            $merged_summary[] = [
                'subject_code' => $code,
                'cohorts'      => array_values($cohort_labels),
            ];
        }

        foreach ($attempt as $a) {
            $faculty_busy[$uid][$a['day']][] = ['from' => $a['from'], 'to' => $a['to']];
            foreach ($cohort_keys as $ck) {
                $cohort_busy[$ck][$a['day']][] = ['from' => $a['from'], 'to' => $a['to']];
            }
            $room_busy[$a['room']][$a['day']][] = ['from' => $a['from'], 'to' => $a['to']];

            $entry = [
                'subject' => $code,
                'hours'   => floatval($a['session_hours']),
                'time'    => ['from' => $a['from'], 'to' => $a['to']],
                'room'    => $a['room']
            ];
            if ($is_merged) {
                $entry['cohorts'] = array_values($cohort_labels);
            }
            // Persist a copy under EVERY cohort that shares this class so each
            // program's view (and future auto-gen runs) sees the busy slot.
            foreach ($cohort_keys as $ck) {
                $by_cohort_faculty_schedule[$ck][$uid][$a['day']][] = $entry;
            }
        }
    };

    // For a given subject + faculty, decide which availability is mandatory:
    // Gen Ed -> admin-set availability (with fallback to faculty-self if blank).
    // Others -> faculty-self availability (with fallback to admin if blank).
    $resolve_availability = function ($faculty, $subject) {
        $tier  = $subject['course_tier'] ?? '';
        $admin = $faculty['availability_admin'] ?? [];
        $self  = $faculty['availability'] ?? [];
        if ($tier === 'gen_ed') {
            return !empty($admin) ? $admin : $self;
        }
        return !empty($self) ? $self : $admin;
    };

    // PASS 1: faculty-first — least-loaded specialists go first, each one
    // sweeps every subject in this cohort that matches their specialization.
    $faculty_pool = array_values(array_filter($faculty_list, function($f) {
        $hasAvail = !empty($f['availability']) || !empty($f['availability_admin']);
        return $hasAvail && !empty($f['specializations']);
    }));
    usort($faculty_pool, function($a, $b) use ($faculty_load) {
        return ($faculty_load[$a['user_id']] ?? 0) <=> ($faculty_load[$b['user_id']] ?? 0);
    });

    foreach ($faculty_pool as $faculty) {
        $uid = intval($faculty['user_id']);
        $specs = $faculty['specializations'];

        foreach ($subjects as $subject) {
            $code = $subject['subject_code'];
            if (in_array($code, $assigned_codes, true)) continue;
            if (!in_array($code, $specs, true)) continue;

            $availability = $resolve_availability($faculty, $subject);
            if (empty($availability)) continue;

            $hours = floatval($subject['lec_hours']) + floatval($subject['lab_hours']);
            if ($hours <= 0) continue;
            $sessions = $this->split_into_sessions($hours);
            $pairing  = isset($subject['pairing']) ? $subject['pairing'] : 'NONE';

            $attempt = $this->try_assign_sessions(
                $availability, $sessions,
                $faculty_busy[$uid] ?? [],
                $cohort_busy_for_code($code),
                $room_busy, $rooms, $pairing
            );
            if ($attempt !== null) {
                $commit($uid, $code, $hours, $attempt);
                $assigned_codes[] = $code;
            }
        }
    }

    // PASS 2: subject-first fallback for whatever PASS 1 missed.
    foreach ($subjects as $subject) {
        $code = $subject['subject_code'];
        if (in_array($code, $assigned_codes, true)) continue;

        $hours = floatval($subject['lec_hours']) + floatval($subject['lab_hours']);
        if ($hours <= 0) continue;
        $sessions = $this->split_into_sessions($hours);
        $pairing  = isset($subject['pairing']) ? $subject['pairing'] : 'NONE';

        $matched = array_values(array_filter($faculty_list, function($f) use ($code) {
            return in_array($code, $f['specializations'], true);
        }));
        if (empty($matched)) {
            $matched = $faculty_list;
        }
        usort($matched, function($a, $b) use ($faculty_load) {
            return ($faculty_load[$a['user_id']] ?? 0) <=> ($faculty_load[$b['user_id']] ?? 0);
        });

        $assigned = false;
        foreach ($matched as $faculty) {
            $uid = intval($faculty['user_id']);
            $availability = $resolve_availability($faculty, $subject);
            if (empty($availability)) continue;

            $attempt = $this->try_assign_sessions(
                $availability, $sessions,
                $faculty_busy[$uid] ?? [],
                $cohort_busy_for_code($code),
                $room_busy, $rooms, $pairing
            );
            if ($attempt !== null) {
                $commit($uid, $code, $hours, $attempt);
                $assigned_codes[] = $code;
                $assigned = true;
                break;
            }
        }

        if (!$assigned) {
            $tier   = $subject['course_tier'] ?? '';
            $reason = ($tier === 'gen_ed')
                ? 'No matching faculty has a free slot in the Dean/PC-set availability (Gen Ed source).'
                : 'No matching faculty has a free slot in their availability for this cohort.';
            $unassigned[] = [
                'subject_code' => $code,
                'subject_name' => $subject['subject_name'],
                'hours' => $hours,
                'reason' => $reason
            ];
        }
    }

    // ---------- Persist generated schedules ----------
    // We persist per (cohort, faculty). When a class is shared across programs
    // (cross-program merge), each cohort gets its own row containing the same
    // entry — so each program's view shows the slot and future auto-runs see it.
    $saved = [];
    foreach ($by_cohort_faculty_schedule as $cohort_key => $uid_schedules) {
        $meta = $cohort_meta[$cohort_key] ?? null;
        if (!$meta) continue;
        $ck_program    = $meta['program'];
        $ck_year_level = $meta['year_level'];
        $ck_semester   = $meta['semester'];

        foreach ($uid_schedules as $uid => $sched_by_day) {
            $payload = [
                'program'    => $ck_program,
                'year_level' => $ck_year_level,
                'semester'   => $ck_semester,
                'schedule'   => $sched_by_day
            ];
            $payload_json = json_encode($payload);

            // If this user already has a row for the same semester + same cohort, merge into it.
            $existing_id = $this->find_schedule_id_for_cohort($uid, $ck_program, $ck_year_level, $ck_semester);
            if ($existing_id) {
                $get = $this->conn->prepare("SELECT sch_schedule FROM schedule WHERE sch_id = ?");
                $get->bind_param("i", $existing_id);
                $get->execute();
                $row = $get->get_result()->fetch_assoc();
                $get->close();
                $existing_data = json_decode($row['sch_schedule'] ?? '{}', true) ?: [];
                $existing_sched = $existing_data['schedule'] ?? [];
                foreach ($sched_by_day as $day => $entries) {
                    foreach ($entries as $e) {
                        $existing_sched[$day][] = $e;
                    }
                }
                $existing_data['program']    = $ck_program;
                $existing_data['year_level'] = $ck_year_level;
                $existing_data['semester']   = $ck_semester;
                $existing_data['schedule']   = $existing_sched;
                $merged = json_encode($existing_data);
                $upd = $this->conn->prepare("UPDATE schedule SET sch_schedule = ? WHERE sch_id = ?");
                $upd->bind_param("si", $merged, $existing_id);
                $upd->execute();
                $upd->close();
                $saved[] = ['sch_id' => $existing_id, 'user_id' => $uid, 'program' => $ck_program, 'merged' => true];
            } else {
                $ins = $this->conn->prepare("INSERT INTO schedule (sch_user_id, sch_schedule) VALUES (?, ?)");
                $ins->bind_param("is", $uid, $payload_json);
                $ins->execute();
                $new_id = $ins->insert_id;
                $ins->close();
                $saved[] = ['sch_id' => $new_id, 'user_id' => $uid, 'program' => $ck_program, 'merged' => false];
            }
        }
    }

    $message = 'Auto-generated ' . count($saved) . ' faculty schedule(s).';
    if (!empty($unassigned)) $message .= ' ' . count($unassigned) . ' subject(s) could not be auto-assigned.';
    if (!empty($merged_summary)) $message .= ' ' . count($merged_summary) . ' subject(s) merged across programs.';

    return [
        'success'    => true,
        'message'    => $message,
        'saved'      => $saved,
        'unassigned' => $unassigned,
        'merged'     => $merged_summary
    ];
}

// Split a weekly-hours number into session lengths.
// Rule (per To-do.txt): if > 1.5 hours, split into 2 equal sessions; otherwise 1 session.
private function split_into_sessions($hours) {
    if ($hours <= 1.5) return [$hours];
    $half = round(($hours / 2) * 2) / 2; // round to nearest 0.5
    return [$half, $hours - $half];
}

// Find existing schedule row for (faculty, program, year_level, semester)
private function find_schedule_id_for_cohort($user_id, $program, $year_level, $semester) {
    $stmt = $this->conn->prepare(
        "SELECT sch_id FROM schedule
         WHERE sch_user_id = ?
           AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(sch_schedule, '$.program')))    = LOWER(?)
           AND JSON_UNQUOTE(JSON_EXTRACT(sch_schedule, '$.year_level')) = ?
           AND JSON_UNQUOTE(JSON_EXTRACT(sch_schedule, '$.semester'))   = ?
         LIMIT 1"
    );
    if (!$stmt) return null;
    $stmt->bind_param("isss", $user_id, $program, $year_level, $semester);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? intval($row['sch_id']) : null;
}

// Pairing definitions per revision1.txt.
// MWF = Monday/Wednesday/Friday  TTH = Tuesday/Thursday  WS = Wednesday/Saturday
private function pairing_days($pairing) {
    switch ($pairing) {
        case 'TTH': return ['Tuesday', 'Thursday'];
        case 'MWF': return ['Monday', 'Wednesday', 'Friday'];
        case 'WS':  return ['Wednesday', 'Saturday'];
    }
    return [];
}

// Greedy session assignment within a faculty's availability window.
// $pairing = 'NONE' | 'MWF' | 'TTH' | 'WS' — preferred meeting-day group.
private function try_assign_sessions($availability, $sessions, $fac_busy, $cohort_busy, &$room_busy, $rooms, $pairing = 'NONE') {
    // Build a day priority list: pairing days first, then the rest.
    $pair_days = $this->pairing_days($pairing);
    $all_days  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    $day_priority = array_values(array_unique(array_merge($pair_days, $all_days)));

    // Two-pass strategy: try assigning every session inside the pairing days
    // first; if that fails (slot full), fall back to the full week.
    if (!empty($pair_days)) {
        $strict = $this->try_assign_with_day_filter(
            $availability, $sessions, $fac_busy, $cohort_busy, $room_busy, $rooms,
            $pair_days, $day_priority
        );
        if ($strict !== null) return $strict;
    }
    return $this->try_assign_with_day_filter(
        $availability, $sessions, $fac_busy, $cohort_busy, $room_busy, $rooms,
        $all_days, $day_priority
    );
}

private function try_assign_with_day_filter($availability, $sessions, $fac_busy, $cohort_busy, &$room_busy, $rooms, $allowed_days, $day_priority) {
    $lunch = ['from' => '12:00', 'to' => '13:00'];

    $days_used = [];
    $assignments = [];

    foreach ($sessions as $session_hours) {
        $found = null;

        // Iterate days in priority order, but only those allowed in this pass.
        foreach ($day_priority as $day) {
            if (!in_array($day, $allowed_days, true)) continue;
            if (in_array($day, $days_used, true)) continue;
            if (!isset($availability[$day]) || !is_array($availability[$day])) continue;

            $windows = $availability[$day];
            foreach ($windows as $win) {
                $win_from = $this->t($win['from'] ?? '');
                $win_to   = $this->t($win['to']   ?? '');
                if (!$win_from || !$win_to) continue;

                $session_minutes = (int) round($session_hours * 60);

                // sweep start times in 30-min steps within the window
                for ($start = clone $win_from; ; $start->modify('+30 minutes')) {
                    $end = clone $start;
                    $end->modify("+{$session_minutes} minutes");
                    if ($end > $win_to) break;

                    $sf = $start->format('H:i');
                    $et = $end->format('H:i');

                    if ($this->overlaps($sf, $et, $lunch['from'], $lunch['to'])) continue;
                    if ($this->any_overlap($sf, $et, $fac_busy[$day] ?? [])) continue;
                    if ($this->any_overlap($sf, $et, $cohort_busy[$day] ?? [])) continue;

                    // pick first room that is free
                    $room_pick = null;
                    foreach ($rooms as $room) {
                        $room = trim($room);
                        if ($room === '') continue;
                        if (!$this->any_overlap($sf, $et, $room_busy[$room][$day] ?? [])) {
                            $room_pick = $room;
                            break;
                        }
                    }
                    if (!$room_pick) continue;

                    $found = [
                        'day' => $day, 'from' => $sf, 'to' => $et,
                        'room' => $room_pick, 'session_hours' => $session_hours
                    ];
                    break;
                }
                if ($found) break;
            }
            if ($found) break;
        }

        if (!$found) return null;
        $days_used[] = $found['day'];
        $assignments[] = $found;
    }
    return $assignments;
}

// helpers
private function t($hhmm) {
    if (!is_string($hhmm) || $hhmm === '') return null;
    $dt = DateTime::createFromFormat('H:i', $hhmm);
    return $dt ?: null;
}
private function overlaps($a_from, $a_to, $b_from, $b_to) {
    $af = new DateTime($a_from); $at = new DateTime($a_to);
    $bf = new DateTime($b_from); $bt = new DateTime($b_to);
    return $af < $bt && $at > $bf;
}
private function any_overlap($from, $to, $ranges) {
    foreach ($ranges as $r) {
        if ($this->overlaps($from, $to, $r['from'], $r['to'])) return true;
    }
    return false;
}

// ---------------- GET ROOM SCHEDULES (aggregate all entries grouped by room) ----------------
public function get_room_schedules() {
    $stmt = $this->conn->prepare("
        SELECT s.sch_id, s.sch_schedule, u.user_fname, u.user_lname
        FROM schedule s
        JOIN users u ON s.sch_user_id = u.user_id
    ");
    if (!$stmt) return [];
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $rooms = []; // room => [ {day, from, to, subject, faculty, semester, program} ]

    while ($row = $result->fetch_assoc()) {
        $data = json_decode($row['sch_schedule'], true);
        if (!isset($data['schedule'])) continue;

        $faculty = trim($row['user_fname'] . ' ' . $row['user_lname']);
        $program  = $data['program']  ?? '';
        $semester = $data['semester'] ?? '';

        foreach ($data['schedule'] as $day => $entries) {
            foreach ($entries as $entry) {
                $room = isset($entry['room']) ? trim($entry['room']) : '';
                if ($room === '') continue;

                if (!isset($rooms[$room])) $rooms[$room] = [];
                $rooms[$room][] = [
                    'day'      => $day,
                    'from'     => $entry['time']['from'] ?? '00:00',
                    'to'       => $entry['time']['to']   ?? '00:00',
                    'subject'  => $entry['subject'] ?? '',
                    'faculty'  => $faculty,
                    'program'  => $program,
                    'semester' => $semester,
                    'sch_id'   => $row['sch_id'],
                ];
            }
        }
    }

    ksort($rooms); // sort by room name
    return $rooms;
}


}