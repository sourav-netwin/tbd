<?php
/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:01-03-2017
 * Dependency: None
 */

class Advertisementsmodel extends CI_Model {
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
    
     public function get_advertisement_details($advertisement_id){         
        $this->db->select('Id,MainCategoryId,AdvertisementTitle,AdvertisementImage,AdvertisementDescription,AdvertisementUrl,StartDate, EndDate, home_page, ClientType, CompanyName, ClientEmail, ContactNumber, ContactPerson, RetailerId, StoreTypeId, StoreId, CreatedBy,ModifiedBy,CreatedOn,ModifiedOn,IsActive,IsRemoved');
        $this->db->from('advertisements');                
        $this->db->where('Id', $advertisement_id);
        $this->db->where('IsRemoved', 0);        
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
    
    public function add_advertisement($data) {
        $this->db->insert('advertisements', $data);
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
    public function update_advertisement($advertisement_id, $data) {
        $this->db->where('Id', $advertisement_id);
        $this->db->update('advertisements', $data);
    }

    /*
     * Method Name: delete_advertisement
     * Purpose: Delete advertisement product
     * params:
     *      input: $advertisement_id
     *      output: status - FAIL / SUCCESS     *              
     */
    public function delete_advertisement($advertisement_id) {
        $data = array('IsRemoved' => "1");
        $this->db->where('Id', $advertisement_id);
        $this->db->update('advertisements', $data);

        return TRUE;
    }

     /*
     * Method Name: change_status
     * Purpose: Update advertisement status
     * params:
     *      input: $advertisement_id, $status
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    public function change_status($advertisement_id, $status) {
        $data = array('IsActive' => $status);
        $this->db->where('Id', $advertisement_id);
        $this->db->update('advertisements', $data);

        return TRUE;
    }
}