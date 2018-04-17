<?php

/*
 * Author:  PM
 * Purpose: Slider related functions
 * Date:    08-10-2015
 */

class Slidermodel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }

    // Get all active sliders that are not deleted
    public function get_sliders(){
        $this->db->select('Image');
        $this->db->from('slider');
        $this->db->where( array('IsRemoved' => '0', 'IsActive' => 1) );
        $this->db->order_by('Sequence');
        $query = $this->db->get();

        return $query->result_array();
    }
}

?>