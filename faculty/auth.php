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
        $query = "SELECT * FROM `users` WHERE user_id = $id AND user_status=1 AND user_type='faculty'";

        $result = $this->conn->query($query);

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        return $items; 
    }



    public function get_user_schedule($user_id) {
        $user_id = intval($user_id);
        $query = "SELECT sch_id, sch_schedule 
                FROM schedule 
                WHERE sch_user_id = $user_id 
                ORDER BY sch_id DESC";

        $result = $this->conn->query($query);

        $schedules = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['sch_schedule'] = json_decode($row['sch_schedule'], true);
                $schedules[] = $row;
            }
        }
        return $schedules; 
    }




    


}