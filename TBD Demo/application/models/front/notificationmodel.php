<?php

/*
 * Author:  PN
 * Purpose: User Notification functions
 * Date:    21-10-2015
 */

class Notificationmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-09-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_notifications() {
        $this->db->select('Id,Message,UserId,');
        $this->db->select("DATE_FORMAT(CreatedOn, '%d/%m/%Y') AS CreatedOn", FALSE);
        $this->db->from('usernotification');
        $this->db->where('UserId', $this->session->userdata('userid'));
        $this->db->where('IsActive', 1);
        $this->db->where('IsRemoved', 0);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function delete_all_notifications() {
//      $this->db->where_in('Id', $ids);
        $this->db->where('UserId', $this->session->userdata('userid'));

        //Delete the notifications
        $this->db->delete('usernotification');
    }

    public function delete_notifications($id) {
//      $this->db->where_in('Id', $ids);
        $this->db->where('Id', $id);

        //Delete the notifications
        $this->db->delete('usernotification');
    }

}
?>

