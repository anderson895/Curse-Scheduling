<?php
include('../class.php');

$db = new global_class();

session_start();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

   if (isset($_GET['requestType']))
    {
        if ($_GET['requestType'] == 'get_all_data') {
            
            $result = $db->get_all_data();
            echo json_encode([
            'status' => 200,
            'data' => $result
            ]);
            
            
        }



    }else {
        echo 'No GET REQUEST';
    }
}
?>