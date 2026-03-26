
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
                $newSchedule[$day][] = [
                    'subject' => $subject,
                    'hours'   => $hours,
                    'time'    => ['from' => '00:00', 'to' => '00:00'],
                    'conflict_warning' => true
                ];
                continue;
            }

            // Mark these newly assigned slots as occupied for subsequent entries in this schedule
            foreach ($assigned as $a) {
                $day_occupied[] = ['from' => $a['from'], 'to' => $a['to']];
            }

            $newSchedule[$day][] = [
                'subject' => $subject,
                'hours'   => $hours,
                'time'    => [
                    'from' => $assigned[0]['from']->format('H:i'),
                    'to'   => end($assigned)['to']->format('H:i')
                ]
            ];
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

// ---- MANUALLY EDIT A SPECIFIC ENTRY TIME ----
public function edit_entry_time($sch_id, $day, $entry_index, $new_from, $new_to) {
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

    $data['schedule'][$day][$entry_index]['time']['from'] = $new_from;
    $data['schedule'][$day][$entry_index]['time']['to']   = $new_to;
    $new_json = json_encode($data);
    $upd = $this->conn->prepare("UPDATE schedule SET sch_schedule = ? WHERE sch_id = ?");
    if (!$upd) return ['success' => false, 'message' => 'Prepare failed'];
    $upd->bind_param("si", $new_json, $sch_id);
    if ($upd->execute()) { $upd->close(); return ['success' => true, 'message' => 'Schedule time updated successfully.']; }
    $upd->close();
    return ['success' => false, 'message' => 'Update failed'];
}

}