<?php
/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:15-03-2017
 * Dependency: None
 */

class Loyaltyordermodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 15-03-2017
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }
    
    
    /*
     * Method Name: get_order_details
     * Purpose: Edit loyalty order information
     * params:
     *      input: $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty product not found fails / Success message
     *              loyaltyProductDetails - Array containing loyalty product details.
     */
    
     public function get_order_details($order_id){         
        $this->db->select("o.LoyaltyOrderId as Id, o.UserId, o.OrderNumber, CONCAT_WS( ' ', u.FirstName, u.LastName ) as Name,u.Email,u.Mobile as userMobile,o.OrderTotal,  case when o.OrderStatus = 1 then 'Cancelled' when o.OrderStatus = 2 then 'Dispatched' else 'Received' end as OrderStatus, o.VoucherCode, o.CreatedOn,o.HouseNumber, o.StreetAddress, o.city, o.Suburb, o.State, o.Country, o.PinCode, o.Latitude, o.Longitude, o.TelephoneFixed, o.Mobile, o.OrderTotal, s.Name as stateName, c.Name as countryName", FALSE);
        $this->db->from('loyalty_orders as o');        
        $this->db->join('users as u', 'u.Id = o.UserId and u.IsActive = 1 and u.IsRemoved = 0', 'left');  
        $this->db->join('state as s', 's.Id = o.State and s.IsActive = 1 and s.IsRemoved = 0', 'left');  
        $this->db->join('countries as c', 'c.Id = o.Country and c.IsActive = 1 and c.IsRemoved = 0', 'left');  
        
                
        $this->db->where('o.LoyaltyOrderId', $order_id);               
        $this->db->limit(1);
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        } 
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
    public function update_order($order_id, $data) {
        $this->db->where('LoyaltyOrderId', $order_id);
        $this->db->update('loyalty_orders', $data);
        return "Updated";
    }
    
    
    /*
     * Method Name: get_order_details
     * Purpose: Edit loyalty order information
     * params:
     *      input: $product_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty product not found fails / Success message
     *              loyaltyProductDetails - Array containing loyalty product details.
     */
    
     public function get_order_products($order_id){         
        $this->db->select("op.Id, op.LoyaltyOrderId, op.UserId, op.LoyaltyProductId, op.PointsUsed, p.Id as Id, p.BrandName, p.CategoryId, p.LoyaltyTitle, p.ProductImage, p.LoyaltyDescription,c.CategoryName", FALSE);
        $this->db->from('loyalty_order_products as op');        
        //$this->db->join('loyalty_products as p', 'p.Id = op.LoyaltyProductId and p.IsActive = 1 and p.IsRemoved = 0', 'left');
        $this->db->join('loyalty_products as p', 'p.Id = op.LoyaltyProductId', 'left');
        $this->db->join('loyalty_categories as c', 'c.Id = p.CategoryId and c.IsActive = 1 and c.IsRemoved = 0', 'left');
        $this->db->where('op.LoyaltyOrderId', $order_id);                       
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        } 
     }
     
	public function getunReadOrders(){         
         $this->db->select("o.LoyaltyOrderId as Id", FALSE);
        $this->db->from('loyalty_orders as o');        
        $this->db->join('users as u', 'u.Id = o.UserId and u.IsActive = 1 and u.IsRemoved = 0', 'left');  
        $this->db->join('state as s', 's.Id = o.State and s.IsActive = 1 and s.IsRemoved = 0', 'left');  
        $this->db->join('countries as c', 'c.Id = o.Country and c.IsActive = 1 and c.IsRemoved = 0', 'left');  
        $this->db->where('o.isAdminReviewed', '0'); 
        $query = $this->db->get();
		
        return $query->num_rows();
     }
    
}