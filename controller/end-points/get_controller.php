<?php
include('../class.php');

$db = new global_class();
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['requestType'])) {

        if ($_GET['requestType'] == 'get_all_data') {
            $result = $db->get_all_data();
            echo json_encode(['status' => 200, 'data' => $result]);

        } else if ($_GET['requestType'] == 'get_all_accounts') {
            $user_type = $_GET['user_type'] ?? 'all';
            $result = $db->get_all_accounts($user_type);
            echo json_encode(['status' => 200, 'data' => $result]);

        } else if ($_GET['requestType'] == 'get_all_subjects') {
            $result = $db->get_all_subjects();
            echo json_encode(['status' => 200, 'data' => $result]);

        } else if ($_GET['requestType'] == 'get_subject_by_id') {
            $subject_id = $_GET['subject_id'];
            $result = $db->get_subject_by_id($subject_id);
            echo json_encode(['status' => 200, 'data' => $result]);

        } else if ($_GET['requestType'] == 'get_curriculum') {
            $result = $db->get_curriculum();
            echo json_encode(['status' => 200, 'data' => $result]);

        } else if ($_GET['requestType'] == 'get_curriculum_by_id') {
            $curriculum_id = $_GET['id'];
            $result = $db->get_curriculum_by_id($curriculum_id);
            echo json_encode(['status' => 200, 'data' => $result]);

        // ===============================
        // FACULTY SCHEDULE ENDPOINTS
        // ===============================
        } else if ($_GET['requestType'] == 'get_schedule') {
            $faculty = $db->get_faculty_and_gec();
            echo json_encode(['status' => 200, 'data' => $faculty]);

        } else if ($_GET['requestType'] == 'get_schedules') {
            $schedules = $db->get_schedules(); // already includes faculty_name and decoded sch_schedule
            echo json_encode(['status' => 200, 'data' => $schedules]);

        } else if ($_GET['requestType'] == 'fetchAllSchedule') {

            $schId = isset($_GET['sch_id']) ? intval($_GET['sch_id']) : null;
            $schedules = $db->fetchAllSchedule($schId);
            echo json_encode(['status' => 200, 'data' => $schedules]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'message' => 'Request Type Not Found']);
        }

    } else {
        echo json_encode(['status' => 400, 'message' => 'No GET requestType']);
    }
}
?>
