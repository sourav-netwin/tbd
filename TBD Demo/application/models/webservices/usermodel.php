<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:25-08-2015
 * Dependency: None
 */

class Usermodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 25-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_user_details_by_id($user_id) {

        /*
        $this -> db -> select('users.Id,FirstName,LastName,users.TelephoneFixed,users.Mobile,users.Email,users.StreetAddress,users.PrefLatitude,users.PrefLongitude,users.PrefDistance,Email,ProfileImage,HouseNumber,StreetAddress,City,Suburb,state.Id as state_id,state.name as state,PinCode,TelephoneFixed,Mobile,Gender,RefererUserId');
        $this -> db -> select("DATE_FORMAT(DateOfBirth, '%d-%m-%Y') AS DateOfBirth", FALSE);
        $this -> db -> from('users');
        $this -> db -> join('state', 'users.state=state.Id', 'left');
        $this -> db -> where('users.Id', $user_id);
        $this -> db -> where('users.IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
        */
        
        $this -> db -> select("users.Id,users.FirstName,users.LastName,users.TelephoneFixed,users.Mobile,users.Email,users.StreetAddress,users.PrefLatitude,users.PrefLongitude,users.PrefDistance,users.ProfileImage,users.HouseNumber,users.StreetAddress,users.City,users.Suburb,state.Id as state_id,state.name as state,users.PinCode,users.TelephoneFixed,users.Mobile,users.Gender,users.RefererUserId, CONCAT_WS( ' ', referrer.FirstName, referrer.LastName ) as Referrer_Name",FALSE);
        $this -> db -> select("DATE_FORMAT(users.DateOfBirth, '%d-%m-%Y') AS DateOfBirth", FALSE);
        $this -> db -> from('users');
        $this -> db -> join('state', 'users.state=state.Id', 'left');
        $this -> db -> join('users as referrer', 'users.RefererUserId=referrer.Id', 'left');
        $this -> db -> where('users.Id', $user_id);
        $this -> db -> where('users.IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_user_details_by_email($email) {
        $this -> db -> from('users');
        $this -> db -> where('Email', $email);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    /**
     * Login a valid user
     *
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function login($email, $password) {

        $this -> db -> select('users.Id,users.FirstName,users.LastName,users.TelephoneFixed,users.Mobile,users.Email,users.StreetAddress,users.PrefLatitude,users.PrefLongitude,users.PrefDistance,users.ProfileImage,userroles.Type,users.City,users.State');
        $this -> db -> join('userroles', 'users.userrole = userroles.Id  or users.userrole = 0', 'left');

        $this -> db -> where('users.Email', $email);
        $this -> db -> where('users.Password', MD5($password));
        //$this -> db -> where('userroles.Type', 'users');
        $this -> db -> where('users.IsActive', 1);
        $this -> db -> where('users.IsRemoved', 0);
        $this -> db -> limit(1);

        $query = $this -> db -> get('users');
        
//        echo $this -> db -> last_query();die;

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }
    /*
     * Purpose: To check if user with same user exists.
     * Date: 11 Oct 2014
     * Input Parameter:
     * 		FB_UID - Facebook Id
     *  Output Parameter:
     * 		Array : if the user already exists.
     * 		FALSE : if user does not exists.
     */

    public function loginByFacebook($fb_uid) {

        $this -> db -> select('users.Id,users.FirstName,users.LastName,users.Email,users.ProfileImage,userroles.Type,users.City,users.State');
        $this -> db -> join('userroles', 'users.userrole = userroles.Id', 'left');

        $this -> db -> where('users.FacebookId', $fb_uid);
        $this -> db -> where('users.IsActive', 1);
        $this -> db -> where('users.IsRemoved', 0);
        $this -> db -> limit(1);

        $query = $this -> db -> get('users');

        if ($query -> num_rows() == 1) {
            $userArr = $query -> row_array();
            return $userArr;
        }
        else {
            $userArr = array();
            return $userArr;
        }
    }
    /*
     * Purpose: To check if user with same user exists.
     * Date: 11 Oct 2014
     * Input Parameter:
     * 		FB_UID - Facebook Id
     *  Output Parameter:
     * 		Array : if the user already exists.
     * 		FALSE : if user does not exists.
     */

    public function loginByTwitter($twitter_id) {

        $this -> db -> select('users.Id,users.FirstName,users.LastName,users.Email,users.ProfileImage,userroles.Type,users.City,users.State');
        $this -> db -> join('userroles', 'users.userrole = userroles.Id', 'left');

        $this -> db -> where('users.TwitterId', $twitter_id);
        $this -> db -> where('users.IsActive', 1);
        $this -> db -> where('users.IsRemoved', 0);
        $this -> db -> limit(1);

        $query = $this -> db -> get('users');

        if ($query -> num_rows() == 1) {
            $userArr = $query -> row_array();
            return $userArr;
        }
        else {
            $userArr = array();
            return $userArr;
        }
    }
    
    public function loginByGoogle($gp_uid) {

        $this -> db -> select('users.Id,users.FirstName,users.LastName,users.Email,users.ProfileImage,userroles.Type,users.City,users.State');
        $this -> db -> join('userroles', 'users.userrole = userroles.Id', 'left');

        $this -> db -> where('users.GoogleId', $gp_uid);
        $this -> db -> where('users.IsActive', 1);
        $this -> db -> where('users.IsRemoved', 0);
        $this -> db -> limit(1);

        $query = $this -> db -> get('users');

        if ($query -> num_rows() == 1) {
            $userArr = $query -> row_array();
            return $userArr;
        }
        else {
            $userArr = array();
            return $userArr;
        }
    }

    public function add_user($data) {

        //get the id for role
        //$user_role = $this -> get_user_role("Users");

        $data['UserRole'] = 0;

        $this -> db -> insert('users', $data);

        $user_id = $this -> db -> insert_id();

        $this -> db -> insert('usernotificationsetting', array('UserId' => $user_id));

        return $user_id;
    }

    public function update_user($user_id, $data) {

        $this -> db -> where('Id', $user_id);
        $this -> db -> update('users', $data);
        return true;
    }

    public function update_fb_email($fb_uid, $data) {
        $where = array(
            'FacebookId' => $fb_uid,
            'IsActive' => 1,
            'IsRemoved' => 0
        );
        $this -> db -> where($where);
        if ($this -> db -> update('users', $data)) {
            return TRUE;
        }
        return FALSE;
    }
    public function update_tw_email($tw_uid, $data) {
        $where = array(
            'TwitterId' => $tw_uid,
            'IsActive' => 1,
            'IsRemoved' => 0
        );
        $this -> db -> where($where);
        if ($this -> db -> update('users', $data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function check_old_password($user_id, $password) {
        $this -> db -> from('users');
        $this -> db -> where('Id', $user_id);
        $this -> db -> where('Password', MD5($password));
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function change_password($user_id, $new_password) {

        $data = array('Password' => MD5($new_password));
        $this -> db -> where('Id', $user_id);
        $this -> db -> update('users', $data);
        return TRUE;
    }

    /**
     * Check if valid email address exist in database
     *
     * @param type $email
     * @return boolean
     */
    public function check_email($email) {
        $this -> db -> select(array(
            'Id',
            'FirstName',
            'Email'
        ));
        $this -> db -> from('users');
        $this -> db -> where('Email', $email);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_user_role($type) {
        $this -> db -> select(array(
            'Id'
        ));
        $this -> db -> from('userroles');
        $this -> db -> where('Type', $type);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    /**
     * set password reset link for an email address
     *
     * @param type $user_id
     * @return type array
     */
    public function set_password_reset_token($user_id) {

        $token = $this -> generate_token();

        $data = array('PasswordReset' => $token);

        $this -> db -> where('Id', $user_id);
        $this -> db -> update('users', $data);

        return $token;
    }

    public function generate_token() {

        $token = openssl_random_pseudo_bytes(8, $cstrong);
        if (!$cstrong) {
            exit('OpenSSL not supported on this server.');
        }
        return bin2hex($token);
    }

    public function save_notification_setting($user_id, $data) {
        $this -> db -> where('UserId', $user_id);
        $this -> db -> update('usernotificationsetting', $data);
        return true;
    }

    public function get_notification_setting($user_id) {

        $this -> db -> from('usernotificationsetting');
        $this -> db -> where('UserId', $user_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function check_fb_email_exists($fb_uid) {
        $this -> db -> select('email');
        $this -> db -> from('users');
        $this -> db -> where('FacebookId', $fb_uid);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            $dataArray = $query -> row_array();
            if ($dataArray['email']) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
    }
    public function check_tw_email_exists($tw_uid) {
        $this -> db -> select('email');
        $this -> db -> from('users');
        $this -> db -> where('TwitterId', $tw_uid);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            $dataArray = $query -> row_array();
            if ($dataArray['email']) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
    }

    public function check_fb_account_exists($fb_uid) {
        $this -> db -> select('FacebookId');
        $this -> db -> from('users');
        $this -> db -> where('FacebookId', $fb_uid);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            $dataArray = $query -> row_array();
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    public function check_gp_account_exists($gp_uid) {
        $this -> db -> select('GoogleId');
        $this -> db -> from('users');
        $this -> db -> where('GoogleId', $gp_uid);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            $dataArray = $query -> row_array();
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    public function check_tw_account_exists($tw_uid) {
        $this -> db -> select('TwitterId');
        $this -> db -> from('users');
        $this -> db -> where('TwitterId', $tw_uid);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            $dataArray = $query -> row_array();
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function check_preffered_location($user_id) {
        $this -> db -> select('PrefLatitude, PrefLongitude');
        $this -> db -> from('users');
        $this -> db -> where('Id', $user_id);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> where('PrefLatitude != \'\'');
        $this -> db -> where('PrefLatitude != 0');
        $this -> db -> where('PrefLongitude != \'\'');
        $this -> db -> where('PrefLongitude != 0');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function save_preffered_location($user_id, $update_data) {

        $this -> db -> where('Id', $user_id);
        if ($this -> db -> update('users', $update_data)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function remove_device_token($user_id, $device_token) {
        $this -> db -> where(
            array(
                'UserId' => $user_id,
                'DeviceId' => $device_token
            )
        );
        if ($this -> db -> delete('userdevices')) {
            return TRUE;
        }
        return FALSE;
    }
    
    public function check_email_edit($email,$user_id) {
        $this -> db -> select(array(
            'Id',
            'FirstName',
            'Email'
        ));
        $this -> db -> from('users');
        $this -> db -> where('Email', $email);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> where('Id != '.$user_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    
    /*
     * Method Name: insert_app_share_details
     * Purpose: Save App share details
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *             
     */
    public function insert_app_share_details($insert_data) {
        if ($this -> db -> insert('app_shares', $insert_data)) {
            return TRUE;
        }
        return FALSE;
    }
    
     /*
     * Method Name: get_app_shares
     * Purpose: Add App share count 
     * params:
     *      input: $insert_data
     *      output: status - FAIL / SUCCESS
     *             
     */
    public function get_app_shares($user_id) {
        $this -> db -> select('count(Id) as count')
            -> from('app_shares')
            -> where('UserId', $user_id)
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        $query = $this -> db -> get();
        return $query -> row_array();
    }
    
    
    /*
     * Method Name: referer_users
     * Purpose: Get the referer user listing  
     * params:
     *      input: $search = "", $user_id
     *      output: status - FAIL / SUCCESS
     *             
     */
    
    public function referer_users($search = "", $user_id) {
        $this -> db -> select("users.Id as RefererUserId, CONCAT_WS( ' ', users.FirstName, users.LastName ) as FullName, users.Mobile",FALSE);        
        $this -> db -> from('users');        
        $this -> db -> where('users.Id <>', $user_id);
        $this -> db -> where_in('users.UserRole', array(0,4));
         //Keyword Search
        if (!empty($search)) {
            $where="(users.FirstName like '%".$search."%' OR users.LastName like '%".$search."%')";
            $this->db->where($where, NULL, FALSE);
        }
        $this -> db -> where('users.Mobile IS NOT NULL');
        $this -> db -> where('users.IsActive', 1);
        $this -> db -> where('users.IsRemoved', 0);        
        $query = $this -> db -> get();
        
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }
    }
    
    /* Function to get user preferred brand */
    public function get_user_preference($user_id) {
        $this -> db -> select('u.RetailerId,u.StoreId,r.CompanyName as RetailerName ,s.StoreName,s.StoreTypeId');
        $this -> db -> from('userpreferredbrands as u');        
        $this -> db -> join('retailers as r', 'r.Id = u.RetailerId', 'left');
        $this -> db -> join('stores as s', 's.Id = u.StoreId', 'left');
        $this -> db -> where(array(
            'u.IsActive' => 1,
            'u.IsRemoved' => 0,
            'u.UserId' => $user_id
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        return $query -> row_array();
    }
    
    
    /* Function to save "Talk to Us" Request */
    public function save_contactus_request($insert_data) {
        $this -> db -> insert('contactus', $insert_data);
        return $this -> db -> insert_id();
    }
	 /* Function to get user details
     * Param: user_id - Id of user to get data of
     * Return: Array - array containing user details
     */

    public function get_user_details($user_id) {
        $this -> db -> select('*');
        $this -> db -> from('users');
        $this -> db -> where(array('Id' => $user_id));
        $this -> db -> limit(1);

        $query = $this -> db -> get();

        return $query -> row_array();
    }
    
}

?>