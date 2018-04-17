<?php

/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class Loyaltycategorymodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 01-03-2017
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    
    /*
     *  Get Loyalty categories
     */
    public function get_loyalty_categories() {
        $this->db->select('Id,CategoryName');

        $this->db->where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));

        $this->db->order_by("CategoryName");
        $query = $this->db->get('loyalty_categories');

        return $query->result_array();
    }
    
     /*
     *  Get Loyalty category details
     */
    public function get_loyalty_category_details($category_id) {
        $this->db->select('Id,CategoryName');
        $this->db->from('loyalty_categories');
        $this->db->where('Id', $category_id);
        $this->db->where('IsRemoved', 0);
        $this->db->where('IsActive', 1);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    

}
?>