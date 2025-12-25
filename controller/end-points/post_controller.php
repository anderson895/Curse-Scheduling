<?php
include('../class.php');

$db = new global_class();

session_start();

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['requestType'])) {
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
    
        } else {
                echo "404";
        }

    }else {
        echo 'No POST REQUEST';
    }

}
?>