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
        } else if($_POST['requestType'] == 'update_subject') {

            echo "<pre>";
            print_r($_POST);
            echo "</pre>";

            $subject_id   = $_POST['subject_id'];
            $subject_code = $_POST['subject_code'];
            $subject_name = $_POST['subject_name'];
            $units        = $_POST['units'];
            $subject_type = $_POST['subject_type']; // ✅ Add type

            $result = $db->update_subject($subject_id, $subject_code, $subject_name, $units, $subject_type);
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

        } else if ($_POST['requestType'] == 'add_subject') {
            $subject_code = $_POST['subject_code'];
            $subject_name = $_POST['subject_name'];
            $units        = $_POST['units'];
            $subject_type = $_POST['subject_type']; // ✅ Add type

            $result = $db->add_subject($subject_code, $subject_name, $units, $subject_type);
            echo json_encode($result['success'] 
                ? ['status'=>'success','message'=>$result['message']] 
                : ['status'=>'error','message'=>$result['message']]
            );

        // ---------- CURRICULUM ----------
        } else if ($_POST['requestType'] == 'add_curriculum') {
           $year_semester = $_POST['year_semester'];
            $subject_ids = $_POST['subject_ids']; // array of subject_ids

            $success_count = 0;
            foreach ($subject_ids as $subject_id) {
                $result = $db->add_curriculum($year_semester, $subject_id);
                if ($result['success']) $success_count++;
            }

            if ($success_count === count($subject_ids)) {
                echo json_encode(['status'=>'success','message'=>'Curriculum added successfully']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Some subjects could not be added']);
            }

        } else if ($_POST['requestType'] == 'update_curriculum') {
            $id = $_POST['id'];
            $year_semester = $_POST['year_semester'];
            $subject_id = $_POST['subject_id'];

            $result = $db->update_curriculum($id, $year_semester, $subject_id);
            echo json_encode($result['success'] ? ['status'=>'success','message'=>$result['message']] : ['status'=>'error','message'=>$result['message']]);

        } else if ($_POST['requestType'] == 'delete_curriculum') {
            $id = $_POST['id'];
            $result = $db->delete_curriculum($id);
            echo json_encode($result['success'] ? ['status'=>'success','message'=>$result['message']] : ['status'=>'error','message'=>$result['message']]);

        }else  // ---------------- SCHEDULE ----------------
        if (isset($_POST['requestType']) && in_array($_POST['requestType'], ['create_schedule', 'update_schedule'])) {
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
                        $hours   = isset($value['hours']) ? floatval($value['hours']) : 0.5; // default 0.5 hr
                        $scheduleData['schedule'][$day][$key] = [
                            'subject' => $subject,
                            'hours'   => $hours
                        ];
                    } else {
                        // If value is just a string, assume 0.5 hr
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

            // Call appropriate DB method
            if ($_POST['requestType'] === 'create_schedule') {
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
