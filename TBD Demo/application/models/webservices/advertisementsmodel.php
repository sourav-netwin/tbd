<?php

/*
 * Author: Name:MK
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class advertisementsmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public $latitude;
    public $longitude;
    public $store_id;
    public $page_no;
    public $page_limit;

    public function __construct() {

        parent::__construct();
    }

   /* Get advertisements */
    public function get_all_advertisements($category_id = 0 ) {
        //$this->db->select('Id,MainCategoryId,AdvertisementTitle,AdvertisementImage,AdvertisementDescription,AdvertisementUrl,StartDate, EndDate, CreatedBy,ModifiedBy,CreatedOn,ModifiedOn,IsActive,IsRemoved');
        
        $this->db->select('Id,MainCategoryId,AdvertisementTitle,AdvertisementImage,AdvertisementDescription,AdvertisementUrl');
        $this->db->where(
                array(
                    'IsRemoved' => 0,
                    'IsActive' => 1
            ));
        $this->db->where( 'StartDate <= ', date('Y-m-d'));
        $this->db->where( 'EndDate >= ', date('Y-m-d'));
        
        if($category_id > 0)
        {
            $this->db->where( 'MainCategoryId', $category_id);
        }
        
        $this->db->where( 'home_page', 1);
        
        $this->db->order_by('Id');        
        $query = $this->db->get('advertisements');
        //echo $this->db->last_query();exit;
        return $query->result_array();
    }
    
}
