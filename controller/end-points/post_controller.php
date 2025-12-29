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
            $subject_id = $_POST['subject_id'];
            $result = $db->delete_subject($subject_id);

            echo json_encode($result['success'] 
                ? ['status'=>'success','message'=>$result['message']] 
                : ['status'=>'error','message'=>$result['message']]
            );

        // ---------- SCHEDULE ----------
        } else if (isset($_POST['requestType']) && in_array($_POST['requestType'], ['create_schedule', 'update_schedule'])) {
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
                        $scheduleData['schedule'][$day][$key] = [
                            'subject' => $subject,
                            'hours'   => $hours
                        ];
                    } else {
                        $scheduleData['schedule'][$day][$key] = [
                            'subject' => $value,
                            'hours'   => 0.5
                        ];
                    }
                }
            }

            // Assign random time slots based on entry hours
            if (!empty($scheduleData['schedule'])) {
                $scheduleData['schedule'] = $db->assign_random_slots($scheduleData['schedule']);
            }

            // Re-encode JSON
            $sch_schedule_clean = json_encode($scheduleData);

            // Validation: prevent duplicate schedules for the same user
            if ($_POST['requestType'] === 'create_schedule') {
                if ($db->schedule_exists($sch_user_id)) {
                    echo json_encode(['status' => 'error', 'message' => 'Schedule already exists for this user.']);
                    exit;
                }
                $result = $db->create_schedule($sch_user_id, $sch_schedule_clean);
            } else {
                $sch_id = intval($sch_id);
                $result = $db->update_schedule($sch_id, $sch_user_id, $sch_schedule_clean);
            }

            // Return JSON response
            echo json_encode($result['success'] 
                ? ['status' => 'success', 'message' => $result['message']] 
                : ['status' => 'error',   'message' => $result['message']]
            );
        } else if ($_POST['requestType'] === 'delete_schedule') {
            $sch_id = $_POST['sch_id'];
            $result = $db->delete_schedule($sch_id);
            echo json_encode($result['success'] 
                ? ['status' => 'success', 'message' => $result['message']] 
                : ['status' => 'error', 'message' => $result['message']]
            );
        }else {
            http_response_code(404);
            echo json_encode(['status'=>404,'message'=>'Request Type Not Found']);
        }

    } else {
        echo json_encode(['status'=>400,'message'=>'No POST requestType']);
    }
}
?>
