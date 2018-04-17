<?php

/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the store Groups.
 * Date:26-08-2015
 * Dependency: None
 */

class Storegroupmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 29-03-2017
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_store_groups() {
        $this -> db -> select('Id,GroupName');
        $this->db->from('store_groups');
        $this->db->where('IsActive', 1);
        $this->db->where('IsRemoved', 0);
        $this->db->order_by('GroupName','asc');
        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
    
    
    
    
}
?>