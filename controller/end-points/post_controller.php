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

       // ---------- SCHEDULE ----------
        } else if ($_POST['requestType'] == 'create_schedule') {

            $sch_user_id = $_POST['sch_user_id'];
            $sch_schedule = $_POST['sch_schedule']; // JSON string

            if (!json_decode($sch_schedule)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid schedule JSON'
                ]);
                exit;
            }

            if (method_exists($db, 'add_schedule')) {
                $result = $db->add_schedule($sch_user_id, $sch_schedule);
                echo json_encode($result['success'] 
                    ? ['status'=>'success','message'=>$result['message']] 
                    : ['status'=>'error','message'=>$result['message']]
                );
            } else {
                $query = "INSERT INTO schedule (sch_user_id, sch_schedule) VALUES (?, ?)";
                $stmt = $db->conn->prepare($query);
                $stmt->bind_param("is", $sch_user_id, $sch_schedule);

                echo $stmt->execute() 
                    ? json_encode(['status'=>'success','message'=>'Schedule created successfully'])
                    : json_encode(['status'=>'error','message'=>'Failed to create schedule']);
            }

        } else if ($_POST['requestType'] == 'update_schedule') {

            $sch_id = $_POST['sch_id'];
            $sch_user_id = $_POST['sch_user_id'];
            $sch_schedule = $_POST['sch_schedule']; // JSON string

            if (!json_decode($sch_schedule)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid schedule JSON'
                ]);
                exit;
            }

            // Use your global_class method if exists
            if (method_exists($db, 'update_schedule')) {
                $result = $db->update_schedule($sch_id, $sch_user_id, $sch_schedule);
                echo json_encode($result['success'] 
                    ? ['status'=>'success','message'=>$result['message']] 
                    : ['status'=>'error','message'=>$result['message']]
                );
            } else {
                // Fallback direct update query
                $query = "UPDATE schedule SET sch_user_id = ?, sch_schedule = ? WHERE sch_id = ?";
                $stmt = $db->conn->prepare($query);
                $stmt->bind_param("isi", $sch_user_id, $sch_schedule, $sch_id);

                echo $stmt->execute() 
                    ? json_encode(['status'=>'success','message'=>'Schedule updated successfully'])
                    : json_encode(['status'=>'error','message'=>'Failed to update schedule']);
            }

        } else if ($_POST['requestType'] == 'delete_schedule') {
            $sch_id = $_POST['sch_id'];
            $result = $db->delete_schedule($sch_id);
            echo json_encode($result['success'] 
                ? ['status'=>'success','message'=>$result['message']] 
                : ['status'=>'error','message'=>$result['message']]
            );

        } else {
            http_response_code(404);
            echo json_encode(['status'=>404,'message'=>'Request Type Not Found']);
        }

    } else {
        echo json_encode(['status'=>400,'message'=>'No POST requestType']);
    }
}
?>
