<?php
include('../class.php');

$db = new global_class();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['requestType'])) {

        // ---------- LOGIN ----------
        if ($_POST['requestType'] == 'Login') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $result = $db->Login($username, $password);

            if ($result['success']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $result['message'],
                    'user_type' => $result['data']['user_type']
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $result['message']
                ]);
            }

        // ---------- ACCOUNT ----------
        } else if ($_POST['requestType'] == 'CreateAccount') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'];
            $last_name = $_POST['last_name'];
            $password = $_POST['password'];
            $type = $_POST['type'];
            $user_status = $_POST['user_status'];

            $result = $db->CreateAccount($username, $email, $first_name, $middle_name, $last_name, $password, $type, $user_status);

            echo json_encode($result['success'] ? ['status'=>'success','message'=>$result['message']] : ['status'=>'error','message'=>$result['message']]);

        } else if ($_POST['requestType'] == 'update_account') {
            $user_id = $_POST['user_id'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'];
            $last_name = $_POST['last_name'];

            $result = $db->update_account($user_id, $username, $email, $first_name, $middle_name, $last_name);
            echo json_encode($result['success'] ? ['status'=>'success','message'=>$result['message']] : ['status'=>'error','message'=>$result['message']]);

        } else if ($_POST['requestType'] == 'toggle_account_status') {
            $user_id = $_POST['user_id'];
            $status = $_POST['status'];
            $result = $db->toggle_account_status($user_id, $status);
            echo json_encode($result['success'] ? ['status'=>'success','message'=>$result['message']] : ['status'=>'error','message'=>$result['message']]);

        // ---------- SUBJECT ----------
        } else if ($_POST['requestType'] == 'add_subject') {
            $program         = $_POST['program'];
            $curriculum_year = $_POST['curriculum_year'];
            $year_level      = $_POST['year_level'];
            $semester        = $_POST['semester'];
            $subject_code    = $_POST['subject_code'];
            $subject_name    = $_POST['subject_name'];
            $lec_hours       = $_POST['lec_hours'];
            $lab_hours       = $_POST['lab_hours'];
            $lec_units       = $_POST['lec_units'];
            $lab_units       = $_POST['lab_units'];
            $prerequisite    = $_POST['prerequisite'] ?? null;

            $result = $db->add_subject($program, $curriculum_year, $year_level, $semester, $subject_code, $subject_name, $lec_hours, $lab_hours, $lec_units, $lab_units, $prerequisite);

            echo json_encode($result['success'] 
                ? ['status'=>'success','message'=>$result['message']] 
                : ['status'=>'error','message'=>$result['message']]
            );

        } else if ($_POST['requestType'] == 'update_subject') {
            $subject_id      = $_POST['subject_id'];
            $program         = $_POST['program'];
            $curriculum_year = $_POST['curriculum_year'];
            $year_level      = $_POST['year_level'];
            $semester        = $_POST['semester'];
            $subject_code    = $_POST['subject_code'];
            $subject_name    = $_POST['subject_name'];
            $lec_hours       = $_POST['lec_hours'];
            $lab_hours       = $_POST['lab_hours'];
            $lec_units       = $_POST['lec_units'];
            $lab_units       = $_POST['lab_units'];
            $prerequisite    = $_POST['prerequisite'] ?? null;

            $result = $db->update_subject($subject_id, $program, $curriculum_year, $year_level, $semester, $subject_code, $subject_name, $lec_hours, $lab_hours, $lec_units, $lab_units, $prerequisite);

            echo json_encode($result['success'] 
                ? ['status'=>'success','message'=>$result['message']] 
                : ['status'=>'error','message'=>$result['message']]
            );

        } else if ($_POST['requestType'] == 'delete_subject') {
            $curriculum_id = $_POST['curriculum_id'];
            $result = $db->delete_subject($curriculum_id);

            echo json_encode($result['success'] 
                ? ['status'=>'success','message'=>$result['message']] 
                : ['status'=>'error','message'=>$result['message']]
            );

        // ---------- SCHEDULE ----------
        } else if (isset($_POST['requestType']) && in_array($_POST['requestType'], ['create_schedule', 'update_schedule'])) {


            // echo "<pre>";
            // print_r($_POST);
            // echo "</pre>";

            $sch_id = $_POST['sch_id'] ?? null;
            $sch_user_id = intval($_POST['sch_user_id'] ?? 0);
            $sch_schedule = $_POST['sch_schedule'] ?? '{}'; // JSON string from frontend

            // Decode JSON
            $scheduleData = json_decode($sch_schedule, true);
            if (!isset($scheduleData['schedule']) || !is_array($scheduleData['schedule'])) {
                $scheduleData['schedule'] = [];
            }

            // Normalize entries: ensure each entry has 'subject' and 'hours'
            foreach ($scheduleData['schedule'] as $day => $entries) {
                foreach ($entries as $key => $value) {
                    if (is_array($value)) {
                        $subject = $value['subject'] ?? '';
                        $hours   = isset($value['hours']) ? floatval($value['hours']) : 0.5;
                        $room    = isset($value['room']) ? trim($value['room']) : '';
                        $entry_normalized = [
                            'subject' => $subject,
                            'hours'   => $hours
                        ];
                        if ($room !== '') $entry_normalized['room'] = $room;
                        $scheduleData['schedule'][$day][$key] = $entry_normalized;
                    } else {
                        $scheduleData['schedule'][$day][$key] = [
                            'subject' => $value,
                            'hours'   => 0.5
                        ];
                    }
                }
            }

            // Re-encode JSON (time assignment happens inside create_schedule/update_schedule with conflict awareness)
            $sch_schedule_clean = json_encode($scheduleData);

            // Validation: prevent duplicate schedules for the same user + same semester
            if ($_POST['requestType'] === 'create_schedule') {
                $semester_check = $scheduleData['semester'] ?? '';
                if ($db->schedule_exists($sch_user_id, $semester_check)) {
                    echo json_encode(['status' => 'error', 'message' => 'Schedule already exists for this user on the selected semester.']);
                    exit;
                }
                $result = $db->create_schedule($sch_user_id, $sch_schedule_clean);
            } else {
                $sch_id = intval($sch_id);
                $result = $db->update_schedule($sch_id, $sch_user_id, $sch_schedule_clean);
            }

            // Return JSON response
            if ($result['success']) {
                // Check if any entries had no available slot
                $saved_data = json_decode($result['saved_json'] ?? '{}', true);
                $warnings = [];
                foreach (($saved_data['schedule'] ?? []) as $day => $entries) {
                    foreach ($entries as $entry) {
                        if (!empty($entry['conflict_warning'])) {
                            $warnings[] = "{$entry['subject']} on {$day}";
                        }
                    }
                }
                $msg = $result['message'];
                if (!empty($warnings)) {
                    $msg .= ' Warning: No available time slot for: ' . implode(', ', $warnings) . '. Please edit their time manually.';
                }
                echo json_encode(['status' => 'success', 'message' => $msg]);
            } else {
                echo json_encode(['status' => 'error', 'message' => $result['message']]);
            }
        } else if ($_POST['requestType'] === 'delete_schedule') {
            $sch_id = $_POST['sch_id'];
            $result = $db->delete_schedule($sch_id);
            echo json_encode($result['success'] 
                ? ['status' => 'success', 'message' => $result['message']] 
                : ['status' => 'error', 'message' => $result['message']]
            );
        } else if ($_POST['requestType'] === 'edit_entry_time') {
            $sch_id      = intval($_POST['sch_id']);
            $day         = $_POST['day'];
            $entry_index = intval($_POST['entry_index']);
            $new_from    = $_POST['new_from'];
            $new_to      = $_POST['new_to'];
            $new_room    = isset($_POST['new_room']) ? trim($_POST['new_room']) : null;
            $result = $db->edit_entry_time($sch_id, $day, $entry_index, $new_from, $new_to, $new_room);
            echo json_encode($result['success']
                ? ['status' => 'success', 'message' => $result['message']]
                : ['status' => 'error',   'message' => $result['message']]
            );

        } else if ($_POST['requestType'] === 'check_conflict') {
            $exclude_sch_id = intval($_POST['exclude_sch_id'] ?? 0);
            $day            = $_POST['day'];
            $new_from       = $_POST['new_from'];
            $new_to         = $_POST['new_to'];
            $room           = isset($_POST['room']) ? trim($_POST['room']) : '';
            $entry_index    = isset($_POST['entry_index']) ? intval($_POST['entry_index']) : -1;

            $time_conflicts = $db->check_schedule_conflict($exclude_sch_id, $day, $new_from, $new_to);

            $room_conflicts = [];
            if ($room !== '') {
                $room_conflicts_raw = $db->check_room_conflict($room, $day, $new_from, $new_to, $exclude_sch_id, $entry_index);
                foreach ($room_conflicts_raw as $rc) {
                    $room_conflicts[] = "Room '{$rc['room']}' — {$rc['faculty']} ({$rc['subject']} {$rc['time']})";
                }
            }

            echo json_encode([
                'status' => 200,
                'conflicts' => $time_conflicts,
                'room_conflicts' => $room_conflicts
            ]);

        } else if ($_POST['requestType'] === 'save_faculty_meta') {
            $user_id              = intval($_POST['user_id'] ?? 0);
            $availability_json    = $_POST['availability']    ?? '{}';
            $specializations_json = $_POST['specializations'] ?? '[]';
            $result = $db->save_faculty_meta($user_id, $availability_json, $specializations_json);
            echo json_encode($result['success']
                ? ['status' => 'success', 'message' => $result['message']]
                : ['status' => 'error',   'message' => $result['message']]
            );

        } else if ($_POST['requestType'] === 'save_my_availability') {
            $session_user_id = intval($_SESSION['user_id'] ?? 0);
            if ($session_user_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
                exit;
            }
            $availability_json = $_POST['availability'] ?? '{}';
            $result = $db->save_my_availability($session_user_id, $availability_json);
            echo json_encode($result['success']
                ? ['status' => 'success', 'message' => $result['message']]
                : ['status' => 'error',   'message' => $result['message']]
            );

        } else if ($_POST['requestType'] === 'auto_generate_schedule') {
            $program         = $_POST['program']         ?? '';
            $year_level      = $_POST['year_level']      ?? '';
            $semester        = $_POST['semester']        ?? '';
            $rooms_raw       = $_POST['rooms']           ?? '';
            $tier            = $_POST['tier']            ?? 'major';
            $curriculum_year = $_POST['curriculum_year'] ?? '';
            $rooms = array_values(array_filter(array_map('trim', explode(',', $rooms_raw)), function($v) { return $v !== ''; }));
            $result = $db->auto_generate_schedule($program, $year_level, $semester, $rooms, $tier, $curriculum_year);
            echo json_encode($result);

        // ---------- ROOMS ----------
        } else if ($_POST['requestType'] === 'add_room') {
            $name = $_POST['room_name'] ?? '';
            $type = $_POST['room_type'] ?? 'lecture';
            $cap  = $_POST['capacity']  ?? 0;
            $result = $db->add_room($name, $type, $cap);
            echo json_encode(['status'=>$result['success']?'success':'error','message'=>$result['message']]);

        } else if ($_POST['requestType'] === 'update_room') {
            $rid  = $_POST['room_id']   ?? 0;
            $name = $_POST['room_name'] ?? '';
            $type = $_POST['room_type'] ?? 'lecture';
            $cap  = $_POST['capacity']  ?? 0;
            $result = $db->update_room($rid, $name, $type, $cap);
            echo json_encode(['status'=>$result['success']?'success':'error','message'=>$result['message']]);

        } else if ($_POST['requestType'] === 'toggle_room_status') {
            $result = $db->toggle_room_status($_POST['room_id'] ?? 0);
            echo json_encode(['status'=>$result['success']?'success':'error','message'=>$result['message']]);

        } else if ($_POST['requestType'] === 'delete_room') {
            $result = $db->delete_room($_POST['room_id'] ?? 0);
            echo json_encode(['status'=>$result['success']?'success':'error','message'=>$result['message']]);

        // ---------- CURRICULUM META (tier + pairing) ----------
        } else if ($_POST['requestType'] === 'set_curriculum_meta') {
            $cid     = $_POST['curriculum_id'] ?? 0;
            $tier    = $_POST['course_tier']   ?? 'major';
            $pairing = $_POST['pairing']       ?? 'NONE';
            $result = $db->set_curriculum_meta($cid, $tier, $pairing);
            echo json_encode(['status'=>$result['success']?'success':'error','message'=>$result['message']]);

        } else {
            http_response_code(404);
            echo json_encode(['status'=>404,'message'=>'Request Type Not Found']);
        }

    } else {
        echo json_encode(['status'=>400,'message'=>'No POST requestType']);
    }
}
?>
