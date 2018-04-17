<?php

/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class Loyaltybrandmodel extends CI_Model {
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
     *  Get Loyalty brands
     */
    public function get_loyalty_brands() {
        $this->db->select('Id,BrandName');

        $this->db->where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));

        $this->db->order_by("BrandName");
        $query = $this->db->get('loyalty_product_brands');

        return $query->result_array();
    }
    
     /*
     *  Get Loyalty brand details
     */
    public function get_loyalty_brand_details($brand_id) {
        $this->db->select('Id,BrandName');
        $this->db->from('loyalty_product_brands');
        $this->db->where('Id', $brand_id);
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