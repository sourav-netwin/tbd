<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:AS
 * Purpose: Checkin functionality
 * Date:08-02-2017
 * Dependency: checkinmodel.php
 */

class Checkin extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this -> post('api_key');

        validateApiKey($api_key);

        $this -> load -> model('webservices/checkinmodel', '', TRUE);
    }

    /**
     * Add checkin details to the DB
     */
    public function do_checkin_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $latitude = $this -> post('latitude') ? $this -> post('latitude') : "";
        $longitude = $this -> post('longitude') ? $this -> post('longitude') : "";

        $checkin_details = $this -> _get_checkin_details($user_id, $store_id);
        $location_difference = $this -> checkinmodel -> get_location_difference($store_id, $latitude, $longitude);
        $insert_data = array(
            'UserId' => $user_id,
            'StoreId' => $store_id,
            'CheckinTime' => date('Y-m-d H:i:s')
        );
        $can_insert = FALSE;
        $store_details = $this -> checkinmodel -> get_store_details($store_id);
        if ($checkin_details) {
            $last_checkin_time_diff = $this -> checkinmodel -> get_last_checkin_time_difference($user_id, $store_id);
            $last_checkin_time_diff = $last_checkin_time_diff ? $last_checkin_time_diff : 0;
            if ($last_checkin_time_diff <= 1) {
                $retArr['status'] = FAIL;
                $retArr['message'] = "You can check in only once in an hour.";
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                if ($location_difference <= 1) {
                    $can_insert = TRUE;
                }
                else {
                    $retArr['status'] = FAIL;
                    $retArr['message'] = "You are not seems near to " . $store_details['StoreName'];
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
            }
        }
        else {
            if ($location_difference <= 1) {
                $can_insert = TRUE;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = "You are not seems near to " . $store_details['StoreName'];
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        if ($can_insert) {
            $is_insert = $this -> checkinmodel -> add_checkin($insert_data);
            if ($is_insert) {
				gainLoyaltyPointsMailOfUser($user_id,'Checkin');
                $retArr['status'] = SUCCESS;
                $retArr['message'] = "You have been successfully checked in to " . $store_details['StoreName'];
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = "Failed checkin to " . $store_details['StoreName'];
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Failed checkin to " . $store_details['StoreName'];
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Get the checkin details of the user or store
     * @param int $user_id
     * @param int $store_id
     * @return boolean array
     */
    public function _get_checkin_details($user_id = '', $store_id = '') {
        $checkin_details = $this -> checkinmodel -> get_checkin_details($user_id, $store_id);
        if ($checkin_details) {
            return $checkin_details;
        }
        return FALSE;
    }
    
    
    /**
     * Get the stores within 100 meters from the user's current location
     */
    public function get_checkin_stores_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";        
        $latitude = $this -> post('latitude') ? $this -> post('latitude') : "";
        $longitude = $this -> post('longitude') ? $this -> post('longitude') : "";
        
        # Specify distance (In Meter) to get stores within that area from the user's current location
        $allowDistance = 0.1; // 100 Meter
        
        //$allowDistance = 10000000;
        $stores = $this -> checkinmodel -> get_checkin_stores($latitude, $longitude);
        
        $allStores = array();
        $index =0;
        foreach ($stores as $store)
        {
           if($store['distance']!="" && $store['distance']!=NULL && $store['distance'] < $allowDistance )
           {
               $allStores[$index]= $store;
               $isHavingAnySpecials = $this -> checkinmodel -> check_stores_specials($store['storeId']);
               $allStores[$index]['distance'] = $store['distance'] * 1000; // Show distance in metre
               $allStores[$index]['specialsCount'] = $isHavingAnySpecials['special_counts'] > 0 ? $isHavingAnySpecials['special_counts'] : "0";
               $index++;
           }
        }
            
        if ($allStores) {
            $retArr['status'] = SUCCESS;
            $retArr['stores_details'] = $allStores;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['stores_details'] = $allStores;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // End get_checkin_stores_post
}