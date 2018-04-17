<?php

/*
 * Author: Name:AS
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:08-02-2017
 * Dependency: None
 */

class Checkinmodel extends CI_Model {

    public function get_checkin_details($user_id = '', $store_id = '') {
        $this -> db -> select('*')
            -> from('userstorecheckin');
        if ($user_id) {
            $this -> db -> where('UserId', $user_id);
        }
        if ($store_id) {
            $this -> db -> where('StoreId', $store_id);
        }
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function add_checkin($insert_data) {
        if ($this -> db -> insert('userstorecheckin', $insert_data)) {
			//gainLoyaltyPointsMailOfUser()
            return TRUE;
        }
        return FALSE;
    }

    public function get_last_checkin_time_difference($user_id, $store_id) {
        $this -> db -> select("(UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')-UNIX_TIMESTAMP(CheckinTime))/3600 as hour_diff", FALSE)
            -> from('userstorecheckin')
            -> where(
                array(
                    'UserId' => $user_id,
                    'StoreId' => $store_id
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            return $res_arr['hour_diff'];
        }
        return FALSE;
    }

    public function get_location_difference($store_id, $latitude, $longitude) {
        $this -> db -> select("(6371 * acos( cos( radians($latitude) ) * cos( radians( Latitude ) ) * cos( radians( Longitude ) - 
radians($longitude) ) + sin( radians($latitude) ) * sin( radians( Latitude ) ) ) ) AS distance", FALSE)
            -> from('stores')
            -> where(
                array(
                    'Id' => $store_id,
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> row_array();
            return $res_arr['distance'];
        }
        return FALSE;
    }

    public function get_store_details($store_id) {
        $this -> db -> select('*')
            -> from('stores')
            -> where(
                array(
                    'Id' => $store_id,
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }
    
    
    /* Get Stores listing from the user's current location within 100 meter */
    public function get_checkin_stores($lat="", $long="") {
        if ($lat != "" && $long != "") {
            $this -> db -> select('MIN((6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ))  AS distance');
        }
        $this -> db -> select('stores.Id as storeId,stores.StoreName,retailers.Id as retailerId,retailers.CompanyName as retailerName'); //,retailers.LogoImage
        $this -> db -> join('retailers', 'stores.RetailerId = retailers.Id AND retailers.IsActive =1 AND  retailers.IsRemoved =0');
        $this -> db -> where('stores.IsActive', 1);
        $this -> db -> where('stores.IsRemoved', 0);
        $this -> db -> group_by('stores.Id');
        if ($lat != "" && $long != "") {
            $this -> db -> order_by('distance', 'ASC');
        }
        $query = $this -> db -> get('stores');
        return $query -> result_array();
    }
    
    /* Check if store has specials */
    public function check_stores_specials($storeId) {        
        $this -> db -> select('specials.Id,productspecials.StoreId,PriceAppliedFrom,PriceAppliedTo, count( DISTINCT(specials.Id)) as special_counts');       
        $this -> db -> join('specials', 'specials.Id = productspecials.SpecialId AND specials.IsActive =1 AND  specials.IsRemoved =0');
        $this -> db -> where('productspecials.StoreId', $storeId);
        $this -> db -> where('productspecials.PriceAppliedFrom <= ', date('Y-m-d'));
        $this -> db -> where('productspecials.PriceAppliedTo >= ', date('Y-m-d'));
        $this -> db -> where('productspecials.IsActive', 1);
        $this -> db -> where('productspecials.IsApproved', 1);
        $this -> db -> group_by('productspecials.specialId');
        $query = $this -> db -> get('productspecials');
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }else{
           return FALSE; 
        }
    }
}