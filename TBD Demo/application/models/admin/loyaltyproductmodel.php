<?php
/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:01-03-2017
 * Dependency: None
 */

class Loyaltyproductmodel extends CI_Model {
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
     * Method Name: get_product_details
     * Purpose: Edit loyalty product information
     * params:
     *      input: $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty product not found fails / Success message
     *              loyaltyProductDetails - Array containing loyalty product details.
     */
    
     public function get_product_details($product_id){         
        $this->db->select('p.Id as Id, p.BrandName, p.CategoryId, p.LoyaltyTitle, p.ProductImage, p.LoyaltyDescription, p.StartDate, p.EndDate,p.LoyaltyPoints,  p.IsActive AS active, c.CategoryName');
        $this->db->from('loyalty_products as p');        
        $this->db->join('loyalty_categories as c', 'c.Id = p.CategoryId and c.IsActive = 1 and c.IsRemoved = 0', 'left');        
        $this->db->where('p.Id', $product_id);
        $this->db->where('p.IsRemoved', 0);        
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        } 
     }
     
      /*
     * Method Name: add_product
     * Purpose: Update loyalty product information
     * params:
     *      input: $data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty product not found fails / Success message
     *              loyaltyProductDetails - Array containing loyalty product details.
     */
    
    public function add_product($data) {
        $this->db->insert('loyalty_products', $data);
        return $this->db->insert_id();
    }
    
     /*
     * Method Name: update_product
     * Purpose: Update loyalty product information
     * params:
     *      input: $product_id, $data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty product not found fails / Success message
     *              loyaltyProductDetails - Array containing loyalty product details.
     */
    public function update_product($product_id, $data) {
        $this->db->where('Id', $product_id);
        $this->db->update('loyalty_products', $data);
    }

    /*
     * Method Name: delete_product
     * Purpose: Delete loyalty product
     * params:
     *      input: $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty product not found fails / Success message
     *              loyaltyProductDetails - Array containing loyalty product details.
     */
    public function delete_product($product_id) {

        $data = array('IsRemoved' => "1");
        $this->db->where('Id', $product_id);
        $this->db->update('loyalty_products', $data);

        return TRUE;
    }

     /*
     * Method Name: change_status
     * Purpose: Update loyalty product status
     * params:
     *      input: $product_id, $status
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    public function change_status($product_id, $status) {
        $data = array('IsActive' => $status);
        $this->db->where('Id', $product_id);
        $this->db->update('loyalty_products', $data);

        return TRUE;
    }

    /*
     * Method Name: check_product_by_name
     * Purpose: Check product name exist
     * params:
     *      input: $name
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    public function check_product_by_name($name) {
        $this->db->from('loyalty_products');
        $this->db->where('LoyaltyTitle', ($name));
        $this->db->where('IsRemoved', 0);
        $this->db->limit(1);
        $query = $this->db->get();
       
        if ($query->num_rows() >= 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /*
     * Method Name: check_product_by_name_edit
     * Purpose: Check product name exist excluding current record
     * params:
     *      input: $name,$id
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    public function check_product_by_name_edit($name,$id) {
        $this->db->from('loyalty_products');
        $this->db->where('LoyaltyTitle', ($name));
        $this->db->where('Id != ', $id);
        $this->db->where('IsRemoved', 0);
        $this->db->limit(1);
        $query = $this->db->get();
       
        if ($query->num_rows() >= 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}