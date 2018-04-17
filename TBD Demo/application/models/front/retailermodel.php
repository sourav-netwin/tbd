<?php

/*
 * Author:  PM
 * Purpose: Slider related functions
 * Date:    08-10-2015
 */

class Retailermodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Get active & not deleted retailers that contain active not deleted store products
    public function get_retailers_having_store_products($limit = 0) {
        $this -> db -> select('retailers.LogoImage, retailers.Id, retailers.CompanyName');
        $this -> db -> from('retailers');
        $this -> db -> join('storeproducts', 'storeproducts.RetailerId = retailers.Id');
        $this -> db -> where(
            array(
                'retailers.IsActive' => 1,
                'retailers.IsRemoved' => 0,
                'storeproducts.IsActive' => 1
            )
        );
        $this -> db -> order_by('retailers.CompanyName');
        $this -> db -> group_by('retailers.Id');

        if ($limit > 0)
            $this -> db -> limit($limit);

        $query = $this -> db -> get();

        return $query -> result_array();
    }

    public function get_retailers_having_store_products_location($limit = 0, $location, $distance) {
        $this -> db -> select('MIN((6371 * acos( cos( radians(' . $location[0]['PrefLatitude'] . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $location[0]['PrefLongitude'] . ') ) + sin( radians(' . $location[0]['PrefLatitude'] . ') ) * sin( radians( stores.Latitude ) ) ) ))  AS distance,retailers.LogoImage, retailers.Id, retailers.CompanyName', false);
        $this -> db -> from('retailers');
        $this -> db -> join('storeproducts', 'storeproducts.RetailerId = retailers.Id');
        $this -> db -> join('stores', 'storeproducts.StoreId = stores.Id');
        $this -> db -> where(
            array(
                'retailers.IsActive' => 1,
                'retailers.IsRemoved' => 0,
                'storeproducts.IsActive' => 1
            )
        );
        $this -> db -> order_by('retailers.CompanyName');
        $this -> db -> group_by('retailers.Id');
        if (isset($distance[0])) {
            $cnt = 1;
            $apnd = '';
            $hav = 0;
            foreach ($distance as $val) {
                if($val['min'] == '' || $val['max'] == ''){
                    $hav++;
                }
                if ($cnt == 1) {
                    $apnd .= '(distance between ' . $val['min'] . ' and ' . $val['max'] . ')';
                }
                else {
                    $apnd .= ' or (distance between ' . $val['min'] . ' and ' . $val['max'] . ')';
                }
                $cnt++;
            }
            if($hav == 0){
                $this -> db -> having($apnd);
            }
            
        }

        if ($limit > 0)
            $this -> db -> limit($limit);

        $query = $this -> db -> get();

        return $query -> result_array();
    }

    // Get count of active retailers that contain active not deleted store products
    public function get_retailers_count() {
        $this -> db -> select('retailers.Id');
        $this -> db -> from('retailers');
        $this -> db -> join('storeproducts', 'storeproducts.RetailerId = retailers.Id');
        $this -> db -> where(
            array(
                'retailers.IsActive' => 1,
                'retailers.IsRemoved' => 0,
                'storeproducts.IsActive' => 1
            )
        );
        $this -> db -> group_by('retailers.Id');

        $query = $this -> db -> get();

        return $query -> num_rows();
    }
    /* Get nearest store for the retailer
     * param - retailer_id : Retailer_id
     */

    public function get_nearest_store($retailer_id, $limit = 1) {
        $CI = & get_instance();
        $CI -> load -> model('front/usermodel');

        // Code to get nearest store for that retailer
        $user_details = $CI -> usermodel -> get_user_details($this -> session -> userdata('userid'));
        $lat = $user_details['PrefLatitude'];
        $long = $user_details['PrefLongitude'];
        if (!$lat || !$long) {
            $lat = -29.85590820250414;
            $long = 31.0203857421875;
        }
        if ($lat != "" && $long != "") {
            $this -> db -> select('(6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ) AS distance');
            $this -> db -> order_by('distance', 'ASC');
            //$this -> db -> order_by('stores.StoreName');
            
        }

        $this -> db -> select('stores.Id, stores.StoreName');
        $this -> db -> where(array(
            'stores.IsActive' => 1,
            'stores.IsRemoved' => 0,
            'storestypes.IsActive' => 1,
            'storestypes.IsRemoved' => 0,
            'stores.RetailerId' => $retailer_id
        ));
        $this -> db -> join('storestypes', 'stores.StoreTypeId = storestypes.Id');
        $this -> db -> group_by('stores.Id');
        $this -> db -> having('distance between 0 and '.$user_details['PrefDistance']);
        //$this -> db -> limit(10);
        $query = $this -> db -> get('stores');
//        echo $this -> db -> last_query();die;

        if ($limit == 1)
            return $query -> row() -> Id;
        else
            return $query -> result_array();
    }
    /* Function to insert user preferences for retailer stores in user 'userpreferredbrands' table
     * Param: user_id - Id of user to set preference for
     */

    public function insert_user_preference($user_id, $retailer_id, $store_id) {
        // If user preference not set, only then insert
        // Will happen when user created from admin
        $this -> db -> select('Id');
        $this -> db -> from('userpreferredbrands');
        $this -> db -> where(array('UserId' => $user_id));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 0) {

            $data = array('UserId' => $user_id
                , 'RetailerId' => $retailer_id
                , 'StoreId' => $store_id
                , 'CreatedBy' => $user_id
                , 'CreatedOn' => date('Y-m-d H:i:s')
                , 'IsActive' => 1
            );
            $this -> db -> insert('userpreferredbrands', $data);
            $this -> db -> insert_id();
        }
        else {
            $data_where = array('UserId' => $user_id);

            $data = array(
                'RetailerId' => $retailer_id
                , 'StoreId' => $store_id
            );

            $this -> db -> update('userpreferredbrands', $data, $data_where);
        }
    }
}
