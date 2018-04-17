<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:26-08-2015
 * Dependency: None
 */

class Usermodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_users() {
        $this -> db -> select('users.Id,users.FirstName,users.LastName,users.Email,users.IsActive,UserRoles.Type');
        $this -> db -> join('userroles', 'users.UserRole = userroles.Id', 'left');

        $this -> db -> where(
            array(
                'users.IsRemoved' => 0
        ));
        $query = $this -> db -> get('users');

        return $query -> result_array();
    }

    public function get_user_details($user_id) {
        $this -> db -> from('users');
        $this -> db -> where('Id', $user_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function update_user_profile($user_id, $data) {

        $this -> db -> where('Id', $user_id);
        $this -> db -> update('users', $data);
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

    public function add_user($data) {

        $this -> db -> insert('users', $data);

        $user_id = $this -> db -> insert_id();

        $this -> db -> insert('usernotificationsetting', array('UserId' => $user_id));

        return $user_id;
    }

    public function delete_user($user_id) {

        $data = array('IsRemoved' => "1");
        $this -> db -> where('Id', $user_id);
        $this -> db -> update('users', $data);

        return TRUE;
    }

    public function change_status($user_id, $status) {

        $data = array('IsActive' => $status);
        $this -> db -> where('Id', $user_id);
        $this -> db -> update('users', $data);

        return TRUE;
    }

    public function get_user_roles($exclude_role = '') {
        $this -> db -> from('userroles');
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);

        if ($exclude_role != '')
        //$this->db->where('Level <', '3');
            $this -> db -> order_by("Type");
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return $query -> result_array();
        }
        else {
            return FALSE;
        }
    }

    public function get_users_by_role($role) {
        $this -> db -> select('users.Id,users.FirstName,users.LastName');
        $this -> db -> join('userroles', 'userroles.Id = users.UserRole');

        $this -> db -> where(
            array(
                'users.IsRemoved' => 0,
                'userroles.Type' => $role
        ));
        $query = $this -> db -> get('users');

        return $query -> result_array();
    }

    public function check_unique_email($email) {

        $this -> db -> from('users');
        $this -> db -> where('Email', ($email));
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return FALSE;
        }
        else {
            return TRUE;
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

    public function check_unique_email_edit($email, $user_id) {
        $this -> db -> from('users');
        $this -> db -> where('Email', ($email));
        $this -> db -> where('Id != ' . $user_id);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function check_unique_mobile($mobile) {
        $this -> db -> from('users');
        $this -> db -> where('Mobile', ($mobile));
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() >= 1) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function check_unique_mobile_edit($mobile, $user_id) {
        $this -> db -> from('users');
        $this -> db -> where('Mobile', ($mobile));
        $this -> db -> where('Id != ' . $user_id);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();


        if ($query -> num_rows() >= 1) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    
    
    /*
     * Method Name: get_loyalty_consumptions
     * Purpose: Get loyalty_consumptions for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     * 
     *  Additional informatiion 
     *  1 points : Reviews
     *  2 points : Product shares
     *  5 points : App share 
     *  20 points : App install by defaults   
     */
    
    
    public function get_loyalty_earned_details($userId, $startDate="", $endDate="") {
        
        # Points calculation 
        $reviews_points = 1;
        $product_shares_points = 2;
        //$app_shares_points = 5;
        $app_shares_points = 2;
        if( $userId == 201)
        {
            $app_shares_points = 5000;
        }
        
        if( $userId == 77)
        {
            $app_shares_points = 10000;
        }
        $app_install_points = 20;
        
        $loyaltyEarnedDetails = array();
        $loyaltyEarnedDetails['userProductReviews'] = 0;
        $loyaltyEarnedDetails['userProductShares'] = 0;        
        $loyaltyEarnedDetails['userAppShares'] = 0;        
        
        # Get users Product Reviews
        $this -> db -> select('count(r.Id) as userProductReviews');        
        $this -> db -> from('users as u');
        $this -> db -> join('productsreviews r', 'r.UserId=u.Id', 'left');
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('r.CreatedOn >=', $startDate);
            $this -> db -> where('r.CreatedOn <=', $endDate);
        }
        
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
        $query = $this -> db -> get();
        
        $productReviews = $query -> row_array();
       
        if($productReviews)
        {
           $loyaltyEarnedDetails['userProductReviews'] = $productReviews['userProductReviews']; 
        }
        
        # Get user products shares
        $this -> db -> select('count(s.Id) as userProductShares');        
        $this -> db -> from('users as u');
        $this -> db -> join('product_shares s', 's.UserId=u.Id', 'left');        
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
         $this -> db -> where('s.IsActive', 1);
        $this -> db -> where('s.IsRemoved', 0);
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('s.ShareDate >=', $startDate);
            $this -> db -> where('s.ShareDate <=', $endDate);
        }
        $query = $this -> db -> get();
        
        
        $productShares = $query -> row_array();
        
        if($productShares)
        {
           $loyaltyEarnedDetails['userProductShares'] = $product_shares_points * $productShares['userProductShares']; 
        }
        
        # Get user App shares
        $this -> db -> select('count(a.Id) as userAppShares');        
        $this -> db -> from('users as u');
        $this -> db -> join('app_shares a', 'a.UserId=u.Id', 'left');        
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
        $this -> db -> where('a.IsActive', 1);
        $this -> db -> where('a.IsRemoved', 0);
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('a.ShareDate >=', $startDate);
            $this -> db -> where('a.ShareDate <=', $endDate);
        }
        $query = $this -> db -> get();        
        
        $appShares = $query -> row_array();
        
        if($appShares)
        {
           $loyaltyEarnedDetails['userAppShares'] = $app_shares_points * $appShares['userAppShares']; 
        }
        
        return $loyaltyEarnedDetails;        
    }
    
    
    /*
     * Method Name: get_loyalty_redeemed_details
     * Purpose: Get get_loyalty_redeemed_details for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     * 
     *  Note : Consumption of the loyalty point is done when user add product in cart and order is finalise.
            
     */
    
    
    public function get_loyalty_redeemed_details($userId, $startDate="", $endDate="") {
              
        # Get layalty redeemed details 
        $this -> db -> select("c.Id,case when sum(c.PointsUsed) is null then 0 else sum(c.PointsUsed) end as loyalty_consumption, case when c.BalancePoints is null then 0 else c.BalancePoints end as BalancePoints",FALSE);
        
        $this -> db -> from('users as u');
        $this -> db -> join('loyalty_consumption c', 'c.UserId=u.Id', 'left');  
        if( $startDate != "" && $endDate != "" )
        {
            $this -> db -> where('c.ConsumptionDate >=', $startDate);
            $this -> db -> where('c.ConsumptionDate <=', $endDate);
        }
        
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
        $this -> db -> where('u.IsRemoved', 0);
        $this-> db -> group_by("c.UserId");
        $this-> db -> order_by("c.Id","desc");
        $query = $this -> db -> get();
        
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }      
    }
    
    
    /*
     * Method Name: get_loyalty_redeemed_details
     * Purpose: Get get_loyalty_redeemed_details for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
    
    public function get_loyalty_balance_old($userId) {              
        # Get layalty redeemed details 
        $this -> db -> select("c.Id , case when c.BalancePoints is null then 0 else c.BalancePoints end as BalancePoints",FALSE);        
        $this -> db -> from('users as u');
        $this -> db -> join('loyalty_consumption c', 'c.UserId=u.Id', 'left');  
        $this -> db -> where('u.Id', $userId);
        $this -> db -> where('u.IsRemoved', 0);
        $this -> db -> where('u.IsRemoved', 0);        
        $this-> db -> order_by("c.Id","desc");
        $this -> db -> limit(1);    
        $query = $this -> db -> get();
        //echo $this->db->last_query();exit;
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }      
    }
    
    /*
     * Method Name: get_loyalty_redeemed_details
     * Purpose: Get get_loyalty_redeemed_details for user
     * params:
     *      input: user_id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if user not found fails / Success message
     *              consumptionDetails - Array containing all the details for the user consumption.
     */
    
   
    public function get_loyalty_balance($userId) {        
         $loyalty_consumption = 0;
         $total_points_earned_to_date = 0;
         
         $loyaltyBalanceDetails = array();
         $loyaltyBalanceDetails['BalancePoints']= 0;
         
         # Get loyalty earned details till date
         $loyaltyEarnedDetails = $this -> get_loyalty_earned_details($userId); 
         
         if($loyaltyEarnedDetails)
         {
            $total_points_earned_to_date = $loyaltyEarnedDetails['userProductReviews'] + $loyaltyEarnedDetails['userProductShares'] + $loyaltyEarnedDetails['userAppShares'];            
         }
        
        # Get loyalty redeemed details
        $redeemedDetails = $this -> get_loyalty_redeemed_details($userId);
        
        
        if($redeemedDetails)
        {
           $loyalty_consumption = $redeemedDetails['loyalty_consumption'];
        }
        
        return $loyaltyBalanceDetails['BalancePoints'] = $total_points_earned_to_date - $loyalty_consumption;
    }
    
    /* Function to Get App users */
    public function get_app_users() {
        $this -> db -> select("users.Id,users.FirstName,users.LastName,CONCAT_WS( ' ', users.FirstName, users.LastName ) as FullName,users.Email,users.Mobile");
        $this -> db -> where(
            array(
                'users.IsActive'=>1,
                'users.IsRemoved' => 0
        ));
        $whereCond = '( users.UserRole = 0 or  users.UserRole = 4 )';
        $this -> db -> where($whereCond);
        
        $this-> db -> order_by("FullName","ASC");
        
        $query = $this -> db -> get('users');
        //echo $this->db->last_query();exit;
        return $query -> result_array();
    }
    
}

