<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:PHN
 * Purpose: Notifications Webservices
 * Date:26-09-2015
 * Dependency: usermodel.php
 */

class Notifications extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this -> post('api_key');

        validateApiKey($api_key);

        $this -> load -> model('webservices/notificationmodel', '', TRUE);
    }

    /**
     * List all notification of user
     */
    public function list_post() {

        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $notifications = $this -> notificationmodel -> get_notifications($user_id);
        
        $index =0;
        foreach($notifications as $notification)
        {
            # Get StoreName from Special Notifications 
            $title = $notification['Title'];
            $tempArr = explode("-",$title);
            
            if( isset($tempArr[1]))
            {
                $storeName = trim( $tempArr[1]);
                $notifications[$index]['Message'] = $notification['Message']." at ".$storeName;
            }
            $index++;
        }

        $retArr['status'] = SUCCESS;
        $retArr['notifications'] = $notifications;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }	

    /**
     * Delete the selected post
     */
    public function delete_post() {
        $ids = $this -> post('id') ? $this -> post('id') : "";
        $ids = explode(',', $ids);
        $this -> notificationmodel -> delete_notifications($ids);

        $retArr['status'] = SUCCESS;

        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    /**
     * Function to send the notification to user when his location is outside the preferred location
     * The function will also send the notification if it founds the user location is near to some store
     */
    public function check_location_change_post() {
        $user_id = $this -> post('user_id');
        $device_token = $this -> post('device_token');
        $current_latitude = $this -> post('latitude');
        $current_longitude = $this -> post('longitude');
        $is_nearby_change = $this -> post('near_by_change');

        $notification_settings = $this -> _get_notification_settings($user_id);

        //Function to send the notification if the user is near by a store
        $nearby_store_data = $this -> notificationmodel -> get_nearby_store_details($user_id, $current_latitude, $current_longitude);

        if ($nearby_store_data && $notification_settings['NearStore'] == '1' ) {
            create_location_nearby_message($user_id, $device_token, $nearby_store_data);
        }

        if ($notification_settings['LocationChange'] == '1') {
            $location_data = $this -> notificationmodel -> get_user_location_details($user_id, $current_latitude, $current_longitude);
            if ($location_data) {
                $preferred_distance = $location_data['PrefDistance'];
                $current_distance = $location_data['CurrentDistance'];
                if ($current_distance > $preferred_distance) {
                    create_location_change_message($user_id, $device_token, $current_latitude, $current_longitude, $current_distance);
                    $retArr['status'] = SUCCESS;
                    $retArr['message'] = 'You are outside of the preferred location';
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
                else {
                    $retArr['status'] = FAIL;
                    $retArr['message'] = 'You are inside of the preferred location';
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = 'Location details are not available';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'Notification is disabled';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function change_notification_settings_post() {
        $user_id = $this -> post('user_id');
        $price_change = $this -> post('price_change');
        $location_change = $this -> post('location_change');
        $near_store = $this -> post('near_store');
        $specials = $this -> post('specials');
        $preferred_store_only = $this -> post('preferred_store_only');
        $price_watch = $this -> post('price_watch');

        $is_row_exists = $this -> notificationmodel -> check_notification_setting_exists($user_id);
        if ($is_row_exists) {
            $update_data = array(
                'PriceChange' => $price_change,
                'LocationChange' => $location_change,
                'NearStore' => $near_store,
                'Specials' => $specials,
                'PreferredStoreOnly' => $preferred_store_only,
                'PriceWatch' => $price_watch
            );
            $where = array(
                'UserId' => $user_id
            );
            $is_update = $this -> notificationmodel -> update_special_setting($update_data, $where);
            if ($is_update) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = 'Notification settings updated successfully';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = 'Failed to update notification settings';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {
            $insert_data = array(
                'WishlistNotification' => 1,
                'FavoriteNotification' => 1,
                'OtherRetailer' => 0,
                'UserId' => $user_id,
                'PriceChange' => $price_change,
                'LocationChange' => $location_change,
                'NearStore' => $near_store,
                'Specials' => $specials,
                'PreferredStoreOnly' => $preferred_store_only,
                'PriceWatch' => $price_watch
            );
            $is_insert = $this -> notificationmodel -> insert_special_setting($insert_data);
            if ($is_insert) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = 'Notification settings added successfully';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = 'Failed to add notification settings';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
    }

    public function get_notification_settings_post() {
        $user_id = $this -> post('user_id');
        $notification_settings = $this -> notificationmodel -> get_notification_settings($user_id);
        if ($notification_settings) {
            $retArr['status'] = SUCCESS;
            $retArr['settings'] = $notification_settings;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $notification_settings = array(
                'WishlistNotification' => 1,
                'FavoriteNotification' => 1,
                'OtherRetailer' => 0,
                'PriceChange' => 1,
                'LocationChange' => 1,
                'NearStore' => 0,
                'Specials' => 1,
                'PreferredStoreOnly' => 1,
                'PriceWatch' => 1,
            );
            $retArr['status'] = SUCCESS;
            $retArr['settings'] = $notification_settings;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function _get_notification_settings($user_id) {
        $notification_settings = $this -> notificationmodel -> get_notification_settings($user_id);
        if ($notification_settings) {
            return $notification_settings;
        }
        else {
            return $notification_settings = array(
                'WishlistNotification' => 1,
                'FavoriteNotification' => 1,
                'OtherRetailer' => 0,
                'PriceChange' => 1,
                'LocationChange' => 1,
                'NearStore' => 0,
                'Specials' => 1,
                'PreferredStoreOnly' => 1
            );
        }
    }
	/* Get all stores from special id */
	public function specialstores_post() {

        $notificationId = $this -> post('Id') ? $this -> post('Id') : "";

        $notification = $this -> notificationmodel -> getNotificationById($notificationId);
        
        $index =0;
        
            # Get StoreName from Special 
            $title = $notification['Title'];
            $allStoresArray=array();           
			$storeName = trim( $tempArr[1]);
			if(!empty($notification['generaLMessage'])){
				$storeIds=unserialize($notification['storesIds']);				
				if(!empty($storeIds)){
					$i=0;
					foreach($storeIds as $storeId=>$distance){
						if($storeId!=''){
							$store = $this -> notificationmodel -> getStoreNameById($storeId);
							$allStoresArray[$i]['StoreId']=$storeId;
							$allStoresArray[$i]['distance']=$distance;
							$allStoresArray[$i]['StoreName']=$store['StoreName'];
							$i++;
						}
					}
				}
			}
			$notification['StoresArray'] =$allStoresArray;
			unset($notification['storesIds']);
			$retArr['status'] = SUCCESS;
			$retArr['notifications'] = $notification;
			$this -> response($retArr, 200); // 200 being the HTTP response code
			die;
    }
}

?>
