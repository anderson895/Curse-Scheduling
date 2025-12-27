<?php


include ('../controller/config.php');

date_default_timezone_set('Asia/Manila');

class auth_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }

    public function check_account($id) {
        $id = intval($id);
        $query = "SELECT * FROM `users` WHERE user_id = $id AND user_status=1 AND user_type='program chair'";

        $result = $this->conn->query($query);

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        return $items; 
    }



    


}