<?php
/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:01-03-2017
 * Dependency: None
 */

class Loyaltysettingsmodel extends CI_Model {
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
    
     public function get_loyalty_setttings(){
        $this->db->select('Id, product_reviews, product_shares, app_shares, app_install, referrer, app_shares_facebook, app_shares_twitter, app_shares_email, app_shares_google, app_shares_whatsApp, product_shares_facebook, product_shares_twitter, product_shares_email, product_shares_google, product_shares_whatsApp, store_checkin, CreatedOn, ModifiedBy, ModifiedOn');
        $this->db->from('loyalty_settings as s');                
        $this->db->order_by('Id','desc');        
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        } 
     }
    
     /*
     * Method Name: update_settings
     * Purpose: Update loyalty settings information
     * params:
     *      input: $settings_id, $data
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty settings not found fails / Success message
     *              
     */
    public function update_settings($settings_id, $data) {
        $this->db->where('Id', $settings_id);
        $this->db->update('loyalty_settings', $data);
    }
}