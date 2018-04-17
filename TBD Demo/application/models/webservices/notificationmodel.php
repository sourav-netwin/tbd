<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:26-08-2015
 * Dependency: None
 */

class Notificationmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-09-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_notifications($user_id) {
        $this -> db -> select('Id,Title,Message,UserId,specialId,notificationType');
        $this -> db -> select("DATE_FORMAT(CreatedOn, '%d/%m/%Y') AS CreatedOn", FALSE);
        $this -> db -> from('usernotification');
        $this -> db -> where('UserId', $user_id);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> order_by('Id', 'DESC');
        $query = $this -> db -> get();

        return $query -> result_array();
    }

    public function delete_notifications($ids) {
        $this -> db -> where_in('Id', $ids);
        //Delete the notifications
        $this -> db -> delete('usernotification');
    }

    public function get_user_location_details($user_id, $current_latitude, $current_longitude) {
        $this -> db -> select('(6371 * acos( cos( radians(' . $current_latitude . ') ) * cos( radians( a.PrefLatitude ) ) * cos( radians( a.PrefLongitude ) - radians(' . $current_longitude . ') ) + sin( radians(' . $current_latitude . ') ) * sin( radians( a.PrefLatitude ) ) ) ) AS CurrentDistance,a.PrefDistance', FALSE)
            -> from('users as a')
            -> where('a.Id', $user_id)
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_nearby_store_details($user_id, $current_latitude, $current_longitude) {
        $this -> db -> select('(6371 * acos( cos( radians(' . $current_latitude . ') ) * cos( radians( a.Latitude ) ) * cos( radians( a.Longitude ) - radians(' . $current_longitude . ') ) + sin( radians(' . $current_latitude . ') ) * sin( radians( a.Latitude ) ) ) ) AS CurrentDistance,
a.Id as StoreId,a.StoreName,a.RetailerId,a.StoreTypeId,count(b.Id) as SpecialCount,a.Latitude,a.Longitude', FALSE)
            -> from('stores as a')
            -> join('productspecials as b', 'b.StoreId = a.Id', 'left')
            -> group_by('a.Id')
            -> having('CurrentDistance <= ' . NEARBY_NOTIFICATION_DISTANCE)//getting shops with in the specified distance
            -> order_by('CurrentDistance')//getting the lowest distance first
            -> limit(1);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            $res_array = $query -> row_array();
            if($res_array['SpecialCount'] > 0){
                return $res_array;
            }
            return FALSE;
        }
        return FALSE;
    }
    
    public function add_multiple_notification($data) {
        $this -> db -> insert_batch('usernotification', $data);
    }
    public function save_notification_history($message, $result, $users) {
        $insert_data = array(
            'MessageString' => $message,
            'ResponseString' => $result,
            'UserList' => $users
        );
        $this -> db -> insert('push_notification_history', $insert_data);
    }
    
    public function check_notification_setting_exists($user_id){
        $this -> db -> select('count(*) as count')
            -> from('usernotificationsetting')
            -> where('UserId', $user_id);
        $query = $this -> db -> get();
        $result_array = $query -> row_array();
        if($result_array['count'] > 0){
            return TRUE;
        }
        return FALSE;
    }
    
    public function insert_special_setting($insert_data){
        if($this -> db -> insert('usernotificationsetting', $insert_data)){
            return TRUE;
        }
        return FALSE;
    }
    public function update_special_setting($update_data, $where){
        $this -> db -> where($where);
        if($this -> db -> update('usernotificationsetting', $update_data)){
            return TRUE;
        }
        return FALSE;
    }
    
    public function get_notification_settings($user_id){
        $this -> db -> select('WishlistNotification, FavoriteNotification, OtherRetailer, PriceChange, LocationChange, NearStore, Specials, PreferredStoreOnly, PriceWatch')
            -> from('usernotificationsetting')
            -> where('UserId', $user_id);
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return $query -> row_array();
        }
        return FALSE;
    }
    
    public function delete_nonregistered_devices($non_registered_array){
        $this -> db -> where_in('DeviceId', $non_registered_array);
        if($this -> db -> delete('userdevices')){
            return TRUE;
        }
        return FALSE;
    }
	
	public function getNotificationById($notificationId) {
        $this -> db -> select('Id,Title,UserId,storesIds,generaLMessage,Message');
        $this -> db -> select("DATE_FORMAT(CreatedOn, '%d/%m/%Y') AS CreatedOn", FALSE);
        $this -> db -> from('usernotification');
        $this -> db -> where('Id', $notificationId);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
		$query = $this -> db -> get();
        return $query -> row_array();
    }
	public function getStoreNameById($storeId) {
        $this -> db -> select('StoreName');
        $this -> db -> from('stores');
        $this -> db -> where('Id', $storeId);
        $query = $this -> db -> get();
        return $query -> row_array();
    }
	
}
?>

