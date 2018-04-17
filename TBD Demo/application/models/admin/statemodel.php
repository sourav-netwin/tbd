<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:26-08-2015
 * Dependency: None
 */

class Statemodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_states() {
        $this->db->from('state');
        $this->db->where('IsActive', 1);
        $this->db->where('IsRemoved', 0);
        $this->db->order_by('Name','asc');
        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }

    public function get_state_details( $state_id ) {
        $this->db->from('state');
        $this->db->where('Id', $state_id);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }

    public function validate_state( $state )
    {
        $res_arr = array();

        $this->db->select('Id,Name');
        $this->db->from('state');
        $this->db->where_in('Name', $state);
        $query = $this->db->get();

        foreach ($query->result_array() as $res)
        {
            $res_arr[$res['Name']] = $res['Id'];
        }

        return $res_arr;
    }
}
?>