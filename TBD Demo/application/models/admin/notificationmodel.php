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

    public function add_notification($data) {
        $this -> db -> insert('usernotification', $data);
        return $this -> db -> insert_id();
    }

    public function get_device_tokens($users) {
        if($users)
        {
            $this -> db -> select('UserId,DeviceId')
                -> from('userdevices')
                -> where_in('UserId', $users);
            $query = $this -> db -> get();
            if ($query -> num_rows() > 0) {
                return $query -> result_array();
            }
            return FALSE;
        }
        return FALSE;
    }

    public function save_notification_history($message, $result, $users) {
        $insert_data = array(
            'MessageString' => $message,
            'ResponseString' => $result,
            'UserList' => $users
        );
        $this -> db -> insert('push_notification_history', $insert_data);
    }

    public function get_device_user_ids() {
        $this -> db -> select('a.UserId,b.PrefLatitude,b.PrefLongitude,b.PrefDistance')
            -> from('userdevices as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> where('b.IsActive', 1)
            -> where('b.IsRemoved', 0)
            -> group_by('a.UserId');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_device_user_stores($latitude, $longitude, $distance, $store_array) {
        $this -> db -> select('MIN((6371 * acos( cos( radians(-29.8587) ) * cos( radians( a.Latitude ) ) * cos( radians( a.Longitude ) - 
radians(31.0218) ) + sin( radians(-29.8587) ) * sin( radians( a.Latitude ) ) ) ))  AS distance', FALSE)
            -> from('stores as a')
            -> join('retailers as b', 'b.Id = a.RetailerId')
            -> join('storestypes as c', 'c.Id = a.StoreTypeId')
            -> where_in('a.Id', implode(',', $store_array))
            -> group_by('a.Id')
            -> having('distance <= ' . $distance);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function add_multiple_notification($data) {
        $this -> db -> insert_batch('usernotification', $data);
		//echo $this->db->last_query().'<br/>';
    }

    //join stores as c on c.Id = b.StoreId
            //join storestypes as d on d.Id = c.StoreTypeId
            //join state as e on e.Id = c.StateId
    //and c.IsActive = 1 and c.IsRemoved = 0 and d.IsActive = 1 and d.IsRemoved = 0 and e.IsActive = 1 and e.IsRemoved = 0
    public function get_notification_users($check_array) {
        
        
        $query_string = 'SELECT * FROM ( select a.Id as UserId, a.FirstName, a.LastName, f.DeviceId from users as a
            join userpreferredbrands as b on b.UserId = a.Id
            join stores as s on b.StoreId = s.Id
            join userdevices as f on f.UserId = a.Id
            where a.IsActive = 1 and a.IsRemoved = 0 
             AND f.DeviceId != \'devicetoken\' AND f.DeviceId != \'NoToken\'';
        
        $device_filter = '';
        $gender_filter = '';
        $state_filter = '';
        $storetype_filter = '';
        
        $retailer_filter = '';        
        $user_filter = '';
        
        if (!$check_array['all']) {//if it is not for all devices, then try for the filters
            
            if ($check_array['android']) {
                $device_filter .= ' AND (f.DeviceType = \'A\' ';
            }
            if ($check_array['iphone']) {
                $device_filter == '' ? $device_filter .= ' AND (f.DeviceType = \'I\'' : $device_filter .= ' OR f.DeviceType = \'I\'';
            }
            $device_filter == '' ? $device_filter .= '' : $device_filter .= ')';
            
            if ($check_array['male']) {
                $gender_filter .= ' AND (a.Gender = \'M\' ';
            }
            if ($check_array['female']) {
                $gender_filter == '' ? $gender_filter .= ' AND (a.Gender = \'F\'' : $gender_filter .= ' OR a.Gender = \'F\'';
            }
            $gender_filter == '' ? $gender_filter .= '' : $gender_filter .= ')';
            
            /*
            if($check_array['state']){
                $state_filter = ' AND a.State = '.$check_array['state'];
            }
            */
            
            /*
            if($check_array['storetype']){
                $storetype_filter = ' AND d.Id = '.$check_array['storetype'];
            }             
            */
            
            if($check_array['storetype']){
                $storetype_filter = ' AND s.StoreTypeId = '.$check_array['storetype'];
            }
            
            if($check_array['retailer']){
                $retailer_filter = ' AND b.RetailerId = '.$check_array['retailer'];
            }
            
            if($check_array['user']){
                $user_filter = ' AND a.Id = '.$check_array['user'];
            } 
        }
        
        $query_string = $query_string . $device_filter.$gender_filter.$state_filter.$storetype_filter.$retailer_filter.$user_filter;
        
        $query_string = $query_string .' order by f.Id DESC ) AS tmp_table group by UserId';
        
        //echo $query_string; exit;
        
        
        $query = $this -> db -> query($query_string);
        //echo $this -> db -> last_query();die;
        if($query -> num_rows() > 0){
            return $query -> result_array();
        }
        return FALSE;
    }
    
    
    
    public function get_notification_users_old($check_array) {
        $query_string = 'select f.DeviceId from users as a
            join userpreferredbrands as b on b.UserId = a.Id
            join userdevices as f on f.UserId = a.Id
            where a.IsActive = 1 and a.IsRemoved = 0 
             AND f.DeviceId != \'devicetoken\' AND f.DeviceId != \'NoToken\'';
        
        $device_filter = '';
        $gender_filter = '';
        $state_filter = '';
        $storetype_filter = '';
        
        $retailer_filter = '';        
        $user_filter = '';
        
        if (!$check_array['all']) {//if it is not for all devices, then try for the filters
            
            if ($check_array['android']) {
                $device_filter .= ' AND (f.DeviceType = \'A\' ';
            }
            if ($check_array['iphone']) {
                $device_filter == '' ? $device_filter .= ' AND (f.DeviceType = \'I\'' : $device_filter .= ' OR f.DeviceType = \'I\'';
            }
            $device_filter == '' ? $device_filter .= '' : $device_filter .= ')';
            
            if ($check_array['male']) {
                $gender_filter .= ' AND (a.Gender = \'M\' ';
            }
            if ($check_array['female']) {
                $gender_filter == '' ? $gender_filter .= ' AND (a.Gender = \'F\'' : $gender_filter .= ' OR a.Gender = \'F\'';
            }
            $gender_filter == '' ? $gender_filter .= '' : $gender_filter .= ')';
            
            if($check_array['state']){
                $state_filter = ' AND a.State = '.$check_array['state'];
            }
            
            /*
            if($check_array['storetype']){
                $storetype_filter = ' AND d.Id = '.$check_array['storetype'];
            }             
            */
            
            if($check_array['user']){
                $user_filter = ' AND a.Id = '.$check_array['user'];
            } 
        }
        
        $query_string = $query_string . $device_filter.$gender_filter.$state_filter.$storetype_filter.$retailer_filter.$user_filter;
        
        $query = $this -> db -> query($query_string);
        echo $this -> db -> last_query();die;
        if($query -> num_rows() > 0){
            return $query -> result_array();
        }
        return FALSE;
    }
    
    

    public function get_state_storetype($state_id) {
        $this -> db -> select('b.Id,b.StoreType')
            -> from('stores as a')
            -> join('storestypes as b', 'a.StoreTypeId = b.Id')
            -> where(
                array(
                    'a.StateId' => $state_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0
                )
            )
            ->group_by('b.Id')
            -> order_by('b.StoreType');
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return $query -> result_array();
        }
        else{
            return TRUE;
        }
    }
    
    public function delete_nonregistered_devices($non_registered_array){
        $this -> db -> where_in('DeviceId', $non_registered_array);
        if($this -> db -> delete('userdevices')){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     *  Get the Users mapped with the device
     */
    public function get_user_by_device_id($deviceId) {
        $this -> db -> select('a.UserId,b.FirstName,b.LastName')
            -> from('userdevices as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> where('a.DeviceId',$deviceId)    
            -> where('a.IsActive', 1)
            -> where('a.IsRemoved', 0)
            -> where('b.IsActive', 1)    
            -> where('b.IsRemoved', 0);
                
        $query = $this -> db -> get(); 
        
        //echo $this->db->last_query();exit;
        
        if ($query -> num_rows() > 0) {
            return $query -> row();
        }
        return FALSE;
    }
    
    public function delete_previous_month_notifications() {
        
		/*
        $currentDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')." -1 month"));
        $this -> db -> where('CreatedOn < ', $currentDate);        
        $this -> db -> delete('usernotification');
       */
    }
    
    /* Function to get retailer based on seleted Region */
    public function get_state_retailers($state_id) {
        $this -> db -> select('r.Id,r.CompanyName as RetailerName')
            -> from('retailers as r')            
            -> where(
                array(                   
                    'r.IsActive' => 1,
                    'r.IsRemoved' => 0                    
                )
            )
            -> order_by('r.CompanyName');
        
        if($state_id > 0 )
        {
            $this -> db -> where('r.StateId', $state_id);   
        }
        
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return $query -> result_array();
        }
        else{
            return TRUE;
        }
    }
    
    
    /* Function to get retailer based on seleted Region */
    public function get_retailer_storetype($retailer_id) {
        $this -> db -> select('s.Id,s.StoreType')
            -> from('storestypes as s')            
            -> where(
                array(
                    's.RetailerId' => $retailer_id,
                    's.IsActive' => 1,
                    's.IsRemoved' => 0                    
                )
            )           
            -> order_by('s.StoreType');
        $query = $this -> db -> get();
        if($query -> num_rows() > 0){
            return $query -> result_array();
        }
        else{
            return TRUE;
        }
    }
    
    
    /* Function to get users  */
    public function get_users($region_id,$retailer_id,$storetype_id, $isMale, $isFemale, $isAndroid, $isIphone) {
        
        $finalResults = array();
        
        $this -> db -> select("u.Id as userId,u.FirstName,u.LastName,CONCAT_WS(' ', u.FirstName, u.LastName ) as FullName,u.Email,u.Mobile",FALSE);
        $this->db->join('userpreferredbrands as b', 'b.UserId = u.Id');
        $this->db->join('retailers as r', 'r.Id = b.RetailerId');
        $this->db->join('stores as c', 'c.Id = b.StoreId');
        $this -> db -> where(
            array(
                'u.IsActive'=>1,
                'u.IsRemoved' => 0
        ));
        $whereCond = '( u.UserRole = 0 or  u.UserRole = 4 )';
        $this -> db -> where($whereCond);
        
        /*
        if($region_id)
        {
            $this -> db -> where('u.state',$region_id); 
        }
        */
        
        if($region_id)
        {
            $this -> db -> where('r.StateId',$region_id); 
        }
        
        if($retailer_id)
        {
            $this -> db -> where('b.RetailerId',$retailer_id); 
        }
        
        if($storetype_id)
        {
            $this -> db -> where('c.StoreTypeId',$storetype_id); 
        }
                
        if($isMale > 0 && $isFemale > 0 )
        {
           $genderCond = '( u.Gender = "M" or u.Gender = "F" )';
           $this -> db -> where($genderCond); 
        }else if($isMale > 0 && $isFemale == 0 )
        {
           $this -> db -> where('u.Gender',"M");  
        }else if($isMale == 0 && $isFemale > 0 )
        {
           $this -> db -> where('u.Gender',"F");  
        }
        
        $this-> db -> order_by("FullName","ASC");
        
        $query = $this -> db -> get('users as u');
        //echo $this->db->last_query();exit;
        
        if($query -> num_rows() > 0){
            //return $query -> result_array();
            
            $results = $query -> result_array();
            
            if( $isAndroid > 0 || $isIphone > 0)
            {   
                foreach($results as $result)
                {
                    $userId     = $result['userId'];
                    $deviceInfo = $this->getUserDevice($userId, $isAndroid, $isIphone);
                    if($deviceInfo)
                    {
                       $finalResults[] =  $result;                        
                    }//if($deviceInfo)
                }// foreach($results as $result)
            }else{
                $finalResults = $results;
            }
            
            return $finalResults;
        }
        else{
            return TRUE;
        }
    }
    
    
    public function getUserDevice($userId,$isAndroid, $isIphone)
    {
        $this -> db -> select("Id as userDeviceId, UserId, DeviceId, DeviceType",FALSE);
        $this -> db -> where(
            array(
                'UserId'=>$userId,
                'IsActive'=>1,
                'IsRemoved' => 0
        ));
        
        if($isAndroid > 0 && $isIphone > 0 )
        {
           $mobileCond = '( DeviceType = "A" or DeviceType = "I" )';
           $this -> db -> where($mobileCond); 
        }else if($isAndroid > 0 && $isIphone == 0 )
        {
           $this -> db -> where('DeviceType',"A");  
        }else if($isAndroid == 0 && $isIphone > 0 )
        {
           $this -> db -> where('DeviceType',"I");  
        }
        
        $this-> db -> order_by("Id","DESC");        
        $query = $this -> db -> get('userdevices');
        //echo $this->db->last_query();exit;
        
        if($query -> num_rows() > 0){
            return $query -> result_array();
        }else{
            return FALSE;
        }
    }
    
    
}