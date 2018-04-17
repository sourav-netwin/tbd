<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:25-08-2015
 * Dependency: None
 */

class Devicemodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 25-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function add_device($user_id, $device_id, $device_type) {

        //Delete the previous record for the user
        $this -> db -> select(array(
            'Id'
        ));
        $this -> db -> from('userdevices');
        $this -> db -> where('UserId', $user_id);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {

            $user_device = $query -> row_array();

            $update_data = array('IsActive' => 0);

            $this -> db -> where('Id', $user_device['Id']);
            $this -> db -> update('userdevices', $update_data);
        }
        else {
            //Insert a new device for the user
            $insert_data = array(
                'DeviceId' => $device_id,
                'DeviceType' => $device_type,
                'UserId' => $user_id,
                'CreatedOn' => date("Y-m-d H:i:s")
            );
            $this -> db -> insert('userdevices', $insert_data);

            return $this -> db -> insert_id();
        }
        return '1';
    }
}

?>