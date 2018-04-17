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

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
}
?>