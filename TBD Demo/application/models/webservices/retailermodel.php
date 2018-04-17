<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class retailermodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_retailers($group_id=0) {
        $this -> db -> select('r.Id,r.CompanyName,r.LogoImage');
        $this -> db -> join('retailers_storegroups AS rsg', 'r.Id = rsg.RetailerId');
        $this -> db -> where(array(
            'r.IsActive' => 1,
            'r.IsRemoved' => 0,
            'rsg.StoreGroupId' => $group_id
        ));
        $query = $this -> db -> get('retailers AS r');
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }

    public function get_retailers_old_29March2017($group_id=0) {
        $this -> db -> select('retailers.Id,retailers.CompanyName,retailers.LogoImage');

        $this -> db -> where(array(
            'retailers.IsActive' => 1,
            'retailers.IsRemoved' => 0
        ));
        
        $query = $this -> db -> get('retailers');

        return $query -> result_array();
    }
    
    public function save_retailer_user($user_id, $retailer_id, $store_id) {

        $this -> db -> select(array('Id'));
        $this -> db -> from('userpreferredbrands');

        $this -> db -> where(array(
            'UserId' => $user_id,
            'IsActive' => 1,
            'IsRemoved' => 0
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();

        $data = array(
            'UserId' => $user_id,
            'RetailerId' => $retailer_id,
            'StoreId' => $store_id,
            'CreatedOn' => date("Y-m-d H:i:s")
        );

        if ($query -> num_rows() == 0) {

            $this -> db -> insert('userpreferredbrands', $data);
            return $this -> db -> insert_id();
        }
        else {
            $this -> db -> where('UserId', $user_id);
            $this -> db -> update('userpreferredbrands', $data);
            return TRUE;
        }
    }

    public function get_saved_retailer($user_id) {

        $this -> db -> select('retailers.Id,retailers.CompanyName,retailers.LogoImage,stores.StoreName');
        $this -> db -> join('retailers', 'retailers.Id = userpreferredbrands.RetailerId', 'left');
        $this -> db -> join('stores', 'stores.Id = userpreferredbrands.StoreId', 'left');
        $this -> db -> where('userpreferredbrands.UserId', $user_id);
        $this -> db -> where('userpreferredbrands.IsActive', 1);
        $this -> db -> where('userpreferredbrands.IsRemoved', 0);

        $query = $this -> db -> get('userpreferredbrands');

        $this -> db -> limit(1);

        if ($query -> num_rows() >= 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_stores($retailer_id, $lat, $long, $user_id, $group_id) {
        $user_details = $this -> get_user_details($user_id);

        $subquery1 ='( 
                SELECT count(p.Id) as specialCount
                FROM specials as d
                LEFT JOIN productspecials as p ON `d`.`Id` = `p`.`SpecialId` AND p.IsActive =  1 AND p.IsApproved = 1  AND p.PriceAppliedFrom <= "'.date('Y-m-d').'" AND p.PriceAppliedTo >= "'.date('Y-m-d').'"
                WHERE p.StoreId =  `a`.`Id`
                and d.IsActive = 1 
                and d.IsRemoved = 0 
                and date(d.SpecialFrom) <= "'.date('Y-m-d').'" 
                and date(d.SpecialTo) >= "'.date('Y-m-d').'"
            ) as storesSpecialsCount';
       
        $this->db->select($subquery1);
        
        $this -> db -> select('sum(case when a.Id is not null then 1 else 0 end) as HaveSpecial,a.StoreName,a.Id,a.StoreTypeId', FALSE); 

        if ($lat == '0.0' && $long == '0.0') {
            $lat = $user_details['PrefLatitude'];
            $long = $user_details['PrefLongitude'];
        }

        if ($lat != "" && $long != "") {
            $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( a.Latitude ) ) * cos( radians( a.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( a.Latitude ) ) ) ),2) AS distance', FALSE);
            $this -> db -> group_by('a.Id');
            $this -> db -> order_by('distance', 'ASC');
        }
        $this -> db -> where(
            array(
                'a.IsRemoved' => 0,                
                'b.IsActive' => 1,
                'b.IsRemoved' => 0,                
                'e.StoreGroupId'=>$group_id
            )
        );
        
        $this -> db -> where('a.StoreTypeId = b.Id');
        $this -> db -> where('a.Id = e.StoreId');

        if ($retailer_id > 0) {
            $this -> db -> where(array('a.IsActive' => 1));
            $this -> db -> where(array('a.RetailerId' => $retailer_id));
        }
        $this -> db -> from('stores_storegroups as e, storestypes as b,stores as a');
        $query = $this -> db -> get();
        //echo $this -> db -> last_query();die;

        return $query -> result_array();
    }
    
   
    
    public function get_stores_working($retailer_id, $lat, $long, $user_id, $group_id) {
        $user_details = $this -> get_user_details($user_id);

        $this -> db -> select('sum(case when d.Id is not null then 1 else 0 end) as HaveSpecial,a.StoreName,a.Id,a.StoreTypeId', FALSE); 

        if ($lat == '0.0' && $long == '0.0') {
            $lat = $user_details['PrefLatitude'];
            $long = $user_details['PrefLongitude'];
        }

        if ($lat != "" && $long != "") {
            $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( a.Latitude ) ) * cos( radians( a.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( a.Latitude ) ) ) ),2) AS distance', FALSE);
            $this -> db -> group_by('a.Id');
            $this -> db -> order_by('distance', 'ASC');
        }
        $this -> db -> join('stores_storegroups as e', 'a.Id = e.StoreId');
        $this -> db -> join('storestypes as b', 'a.StoreTypeId = b.Id');
        $this -> db -> join('special_stores as c', 'c.StoreId = a.Id', 'left');
        $this -> db -> join('specials as d', 'd.Id = c.SpecialId and d.IsActive = 1 and d.IsRemoved = 0 and date(d.SpecialFrom) <= \'' . date('Y-m-d') . '\' and date(d.SpecialTo) >= \'' . date('Y-m-d') . '\'', 'left');
        $this -> db -> where(
            array(
                'a.IsRemoved' => 0,
                'b.IsActive' => 1,
                'b.IsRemoved' => 0,
                'e.StoreGroupId'=>$group_id
            )
        );

        if ($retailer_id > 0) {
            $this -> db -> where(array('a.IsActive' => 1));
            $this -> db -> where(array('a.RetailerId' => $retailer_id));
        }

        $this -> db -> from('stores as a');
        
        $query = $this -> db -> get();
        //echo $this -> db -> last_query();die;

        return $query -> result_array();
    }
    
    
    public function get_stores_old_29March2017($retailer_id, $lat, $long, $user_id) {
        $user_details = $this -> get_user_details($user_id);

        $this -> db -> select('sum(case when d.Id is not null then 1 else 0 end) as HaveSpecial,a.StoreName,a.Id,a.StoreTypeId', FALSE); 

        if ($lat == '0.0' && $long == '0.0') {
            $lat = $user_details['PrefLatitude'];
            $long = $user_details['PrefLongitude'];
//            $lat = -29.85590820250414;
//            $long = 31.0203857421875;
        }
//        $lat = $user_details['PrefLatitude'];
//        $long = $user_details['PrefLongitude'];

        if ($lat != "" && $long != "") {
            $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( a.Latitude ) ) * cos( radians( a.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( a.Latitude ) ) ) ),2) AS distance', FALSE);
            $this -> db -> group_by('a.Id');
            $this -> db -> order_by('distance', 'ASC');
//            $this -> db -> order_by('stores.StoreName');
        }
        $this -> db -> join('storestypes as b', 'a.StoreTypeId = b.Id');
        $this -> db -> join('special_stores as c', 'c.StoreId = a.Id', 'left');
        $this -> db -> join('specials as d', 'd.Id = c.SpecialId and d.IsActive = 1 and d.IsRemoved = 0 and date(d.SpecialFrom) <= \'' . date('Y-m-d') . '\' and date(d.SpecialTo) >= \'' . date('Y-m-d') . '\'', 'left');
        $this -> db -> where(
            array(
                'a.IsRemoved' => 0,
                'b.IsActive' => 1,
                'b.IsRemoved' => 0
            )
        );

        if ($retailer_id > 0) {
            $this -> db -> where(array('a.IsActive' => 1));
            $this -> db -> where(array('a.RetailerId' => $retailer_id));
        }

        $this -> db -> from('stores as a');
//        $this -> db -> limit(10);

        $query = $this -> db -> get();
       // echo $this -> db -> last_query();die;

        return $query -> result_array();
    }

    public function get_user_details($user_id) {
        $this -> db -> select('*');
        $this -> db -> from('users');
        $this -> db -> where(array('Id' => $user_id));
        $this -> db -> limit(1);

        $query = $this -> db -> get();

        return $query -> row_array();
    }
    
    /* Function used to get specials count for the store */
    public function get_stores_specials($storeId) {

        $this -> db -> select('count(a.Id) as specialCount', FALSE);   
        $this -> db -> where('a.StoreId', $storeId);
        $this -> db -> where('a.IsActive', 1);
        $this -> db -> where('a.IsApproved', 1);
        $this -> db -> where('a.PriceAppliedFrom <=', date('Y-m-d'));
        $this -> db -> where('a.PriceAppliedTo >=', date('Y-m-d'));   
        $query = $this -> db -> get('productspecials as a');
        
        //echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();
            return $result['specialCount'];
        }else {
            return 0;
        }
    }
    
    
    public function get_store_groups() {
        $this -> db -> select('Id,GroupName,viewType');
        $this->db->from('store_groups');
        $this->db->where('IsActive', 1);
        $this->db->where('IsRemoved', 0);
        $this->db->order_by('Id','asc');
        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
    
    public function get_all_stores() {
        $this -> db -> select('a.StoreName,a.Id', FALSE);
        $query = $this -> db -> get('stores as a');
        //echo $this -> db -> last_query();die;
        return $query -> result_array();
    }
    
    
    public function get_all_retailers() {
        $this -> db -> select('a.CompanyName,a.Id', FALSE);
        $query = $this -> db -> get('retailers as a');
        //echo $this -> db -> last_query();die;
        return $query -> result_array();
    }
    
    public function get_parent_categories() {
        $this -> db -> select('Id,CategoryName,ParentCategory,CategoryIcon,IsActive');
        $this -> db -> where('ParentCategory', 0);
        $query = $this -> db -> get('categories');
        return $query -> result_array();
    }
    
    
    public function get_all_storeformats() {
        $this -> db -> select('a.StoreType,a.Id', FALSE);
        $query = $this -> db -> get('storestypes as a');
        //echo $this -> db -> last_query();die;
        return $query -> result_array();
    }
    
    
    /* Functions added on 16 May 2017 */
    
    public function get_retailers_stores($retailer_id, $store_id,$lat, $long, $user_id, $group_id,$limit) {
        $user_details = $this -> get_user_details($user_id);

        $subquery1 ='( 
                SELECT count(p.Id) as specialCount
                FROM specials as d
                LEFT JOIN productspecials as p ON `d`.`Id` = `p`.`SpecialId` AND p.IsActive =  1 AND p.IsApproved = 1  AND p.PriceAppliedFrom <= "'.date('Y-m-d').'" AND p.PriceAppliedTo >= "'.date('Y-m-d').'"
                WHERE p.StoreId =  `a`.`Id`
                and d.IsActive = 1 
                and d.IsRemoved = 0 
                and date(d.SpecialFrom) <= "'.date('Y-m-d').'" 
                and date(d.SpecialTo) >= "'.date('Y-m-d').'"
            ) as storesSpecialsCount';
       
        //$this->db->select($subquery1);
        
        //$this -> db -> select('sum(case when a.Id is not null then 1 else 0 end) as HaveSpecial,a.StoreName,a.Id,a.StoreTypeId', FALSE); 
        
        $this -> db -> select('a.StoreName,a.Id,a.StoreTypeId', FALSE); 

        if ($lat == '0.0' && $long == '0.0') {
            $lat = $user_details['PrefLatitude'];
            $long = $user_details['PrefLongitude'];
        }

        if ($lat != "" && $long != "") {
            $this -> db -> select('ROUND((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( a.Latitude ) ) * cos( radians( a.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( a.Latitude ) ) ) ),2) AS distance', FALSE);
            $this -> db -> group_by('a.Id');
            $this -> db -> order_by('distance', 'ASC');
        }
        $this -> db -> where(
            array(
                'a.IsRemoved' => 0,                
                'b.IsActive' => 1,
                'b.IsRemoved' => 0,                
                'e.StoreGroupId'=>$group_id
            )
        );
        
        $this -> db -> where('a.StoreTypeId = b.Id');
        $this -> db -> where('a.Id = e.StoreId');
        
        if($store_id)
        {
             $this -> db -> where('a.Id',$store_id);
        }

        if ($retailer_id > 0) {
            $this -> db -> where(array('a.IsActive' => 1));
            $this -> db -> where(array('a.RetailerId' => $retailer_id));
        }
        $this -> db -> from('stores_storegroups as e, storestypes as b,stores as a');
        
        if($limit){
            $this->db->limit($limit);
        }
        
        $query = $this -> db -> get();
        //echo $this -> db -> last_query();die;
        return $query -> result_array();
    }
    
    
    
    public function save_preferences($user_id, $retailer_id, $store_id) {

        $this -> db -> select(array('Id'));
        $this -> db -> from('userpreferredbrands');

        $this -> db -> where(array(
            'UserId' => $user_id,
            'IsActive' => 1,
            'IsRemoved' => 0
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if($store_id > 0 )
        {   
            $data = array(
                'UserId' => $user_id,
                'RetailerId' => $retailer_id,
                'StoreId' => $store_id,
                'CreatedOn' => date("Y-m-d H:i:s")
            ); 
            
        }else{
           $data = array(
                'UserId' => $user_id,
                'RetailerId' => $retailer_id,                
                'CreatedOn' => date("Y-m-d H:i:s")
            );
        }       

        if ($query -> num_rows() == 0) {

            $this -> db -> insert('userpreferredbrands', $data);
            return $this -> db -> insert_id();
        }
        else {
            $this -> db -> where('UserId', $user_id);
            $this -> db -> update('userpreferredbrands', $data);
            return TRUE;
        }
    }
    
    
    public function get_stores_specials_count($store_id) {
        /*
        $subquery1 ='( 
                SELECT count(p.Id) as specialCount
                FROM specials as d
                LEFT JOIN productspecials as p ON `d`.`Id` = `p`.`SpecialId` AND p.IsActive =  1 AND p.IsApproved = 1  AND p.PriceAppliedFrom <= "'.date('Y-m-d').'" AND p.PriceAppliedTo >= "'.date('Y-m-d').'"
                WHERE p.StoreId =  '.$store_id.'
                and d.IsActive = 1 
                and d.IsRemoved = 0 
                and date(d.SpecialFrom) <= "'.date('Y-m-d').'" 
                and date(d.SpecialTo) >= "'.date('Y-m-d').'"
            ) as storesSpecialsCount';
        */
        
       
        $subquery1 ='( 
                SELECT count(*) as specialCount
                FROM specials as d
                LEFT JOIN productspecials as p ON `d`.`Id` = `p`.`SpecialId` AND p.IsActive =  1 AND p.IsApproved = 1  AND p.PriceAppliedFrom <= "'.date('Y-m-d').'" AND p.PriceAppliedTo >= "'.date('Y-m-d').'"
                WHERE p.StoreId =  '.$store_id.'
                and d.IsActive = 1 
                and d.IsRemoved = 0 
                and date(d.SpecialFrom) <= "'.date('Y-m-d').'" 
                and date(d.SpecialTo) >= "'.date('Y-m-d').'"
            ) as storesSpecialsCount';
            
        $this->db->select($subquery1);
        $query = $this -> db -> get();
        //echo $this -> db -> last_query();die;
        return $query -> row_array();
    }
}