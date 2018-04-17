<?php

/*
 * Author:  PM
 * Purpose: User related functions
 * Date:    12-10-2015
 */

class Usermodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    /* Function to register a user
     * Param: data - Data to be inserted in users table
     * Return: id - Id of inserted record
     */

    public function register($data) {
        $this -> db -> insert('users', $data);
        $user_id = $this -> db -> insert_id();

        $this -> db -> insert('usernotificationsetting', array('UserId' => $user_id));

        //Send Confirmation Mail to User & Admin
        UserRegistrationConfirmation($user_id);

        return $user_id;
    }
    /* Function to check  current user preferences
     * Param: user_id - Id of user to set preference for
     */

    public function check_user_preference($user_id) {
        $this -> db -> select('Id');
        $this -> db -> from('userpreferredbrands');
        $this -> db -> where(array('UserId' => $user_id));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    public function check_location_preference($user_id) {
        $this -> db -> select('Id');
        $this -> db -> from('users');
        $this -> db -> where('Id', $user_id);
        $this -> db -> where('PrefLatitude != \'\'');
        $this -> db -> where('PrefLongitude != \'\'');
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    public function check_email_entered($user_id) {
        $this -> db -> select('Email');
        $this -> db -> from('users');
        $this -> db -> where(array('Id' => $user_id));
        $this -> db -> where('Email != \'\'');
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 0) {
            return false;
        }
        else {
            return true;
        }
    }
    /* Function to insert user preferences for retailer stores in user 'userpreferredbrands' table
     * Param: user_id - Id of user to set preference for
     */

    public function insert_user_preference($user_id) {
        // If user preference not set, only then insert
//        // Will happen when user created from admin
//        $this->db->select('Id');
//        $this->db->from('userpreferredbrands');
//        $this->db->where( array('UserId' => $user_id ) );
//        $this->db->limit(1);
//        $query = $this->db->get();
//
//        if( $query->num_rows() == 0 )
//        {
//            //Insert default preferred retailer and nearest store as per lat long while registering in 'userpreferredbrands' table in order to show top offers for that retailer and store
//            $this->db->select('Id');
//            $this->db->from('retailers');
//            $this->db->where( array('CompanyName' => $this->config->item('default_preferred_retailer') ) );
//            $this->db->limit(1);
//            $query = $this->db->get();
//
//            if( $query->num_rows() > 0 )
//            {
//                $retailer_id = $query->row()->Id;
//
//                // Code to get nearest store for that retailer
//                $user_details = $this->get_user_details( $user_id );
//                $lat = $user_details['Latitude'];
//                $long = $user_details['Longitude'];
//
//                if ($lat != "" && $long != "")
//                {
//                    $this->db->select('(6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( stores.Latitude ) ) * cos( radians( stores.Longitude ) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( stores.Latitude ) ) ) ) AS distance');
//                    $this->db->order_by('distance', 'ASC');
//                }
//
//                $this->db->select('stores.Id');
//                $this->db->where(array(
//                    'stores.IsActive' => 1,
//                    'stores.IsRemoved' => 0,
//                    'stores.RetailerId' => $retailer_id
//                ));
//                $this->db->limit(1);
//                $query = $this->db->get('stores');
//
//                $store_id = ( $query->num_rows() > 0) ? $query->row()->Id : 0;
//
//                $data = array( 'UserId' => $user_id
//                                , 'RetailerId' => $retailer_id
//                                , 'StoreId' => $store_id
//                                ,'CreatedBy' => $user_id
//                                , 'CreatedOn' => date('Y-m-d H:i:s')
//                                , 'IsActive' => 1
//                            );
//                $this->db->insert('userpreferredbrands', $data);
//                $insert_id = $this->db->insert_id();
//            }
//        }
    }
    /* Function to register/login a user using FB
     * Param: data - Data returned as response from FB
     * Return: Id of the user
     */

    public function fb_login($fb_data) {
        $first_name = $fb_data['first_name'];
        $last_name = $fb_data['last_name'];
        $fb_id = $fb_data['id'];
        // $fb_email = $fb_data['email'];

        $this -> db -> select('Id');
        $this -> db -> from('users');
        $this -> db -> where(array(
            'FacebookId' => $fb_id,
            'IsActive' => 1,
            'IsRemoved' => 0
        ));
        $this -> db -> limit(1);

        $query = $this -> db -> get();

        // If exists, login ( set session data ) else register and then login( set session data )
        if ($query -> num_rows() > 0) {
            $this -> insert_user_preference($query -> row() -> Id);
            return $query -> row() -> Id;
        }
        else {
            $ImageName = generateRandomString(20) . '.jpg';
            $large = $this -> DownloadImage('https://graph.facebook.com/' . $fb_id . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/large/' . $ImageName);
            $medium = $this -> DownloadImage('https://graph.facebook.com/' . $fb_id . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/medium/' . $ImageName);
            $small = $this -> DownloadImage('https://graph.facebook.com/' . $fb_id . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/small/' . $ImageName);
            $original = $this -> DownloadImage('https://graph.facebook.com/' . $fb_id . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/original/' . $ImageName);
            if (!$large || !$medium || !$small || !$original) {
                $ImageName = '';
            }
            $insert_data = array('FacebookId' => $fb_id
                , 'FirstName' => $first_name
                , 'LastName' => $last_name,
                'ProfileImage' => $ImageName,
                'SocialMedia' => 'Facebook'
            );

            return $this -> register($insert_data);
        }
    }

    public function DownloadImage($url, $dest) {
        $curl = curl_init($url);
        $fp = fopen($dest, 'wb');
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        fclose($fp);
        if ($respCode != 200) {
            unlink($dest);
            return FALSE;
        }
        else {
            return TRUE;
        }
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

    /**
     * Login a valid user
     *
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function login($email, $password) {
        $this -> db -> select('Id, Email, FirstName, LastName, ProfileImage');
        $this -> db -> from('users');
        $this -> db -> where(array('Email' => $email, 'Password' => MD5($password), 'UserRole' => USER_ROLE, 'IsActive' => 1, 'IsRemoved' => 0));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            $this -> insert_user_preference($query -> row() -> Id);
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    /**
     * Check if valid email address exist in database
     *
     * @param type $email
     * @return boolean
     */
    public function check_email_exists($email) {
        $this -> db -> select('Id');
        $this -> db -> from('users');
        $this -> db -> where(array('Email' => $email, 'UserRole' => USER_ROLE, 'IsActive' => 1, 'IsRemoved' => 0));
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

    /**
     * Generates a token
     *
     * @return string - token generated
     */
    public function generate_token() {
        $token = openssl_random_pseudo_bytes(8, $cstrong);
        if (!$cstrong) {
            exit('OpenSSL not supported on this server.');
        }

        return bin2hex($token);
    }

    public function reset_password($token, $new_password) {
        $this -> db -> select('Id');
        $this -> db -> from('users');
        $this -> db -> where(
            array(
                'PasswordReset' => $token,
                //'UserRole' => USER_ROLE,
                'IsActive' => 1,
                'IsRemoved' => 0
            )
        );
       // $this -> db -> where('(UserRole = ' . USER_ROLE . ' or UserRole = 0)', NULL, FALSE);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            $data = array(
                'Password' => MD5($new_password),
                'PasswordReset' => ''
            );
            $this -> db -> where('PasswordReset', $token);
            $this -> db -> update('users', $data);
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    /* Function to register/login a user using Twitter
     * Param: data - Data returned as response from Twitter
     * Return: Id of the user
     */

    public function twitter_login($screen_name, $full_name, $twitter_id) {
        $this -> db -> select('Id');
        $this -> db -> from('users');
        $this -> db -> where(array(
            'TwitterId' => $twitter_id,
            'IsActive' => 1,
            'IsRemoved' => 0
        ));
        $this -> db -> limit(1);

        $query = $this -> db -> get();

        // If exists, login ( set session data ) else register and then login( set session data )
        if ($query -> num_rows() > 0) {
            $this -> insert_user_preference($query -> row() -> Id);
            return $query -> row() -> Id;
        }
        else {
            $ImageName = generateRandomString(20) . '.jpg';
            $large = $this -> DownloadImage('https://twitter.com/' . $screen_name . '/profile_image?size=bigger', './assets/images/users/large/' . $ImageName);
            $medium = $this -> DownloadImage('https://twitter.com/' . $screen_name . '/profile_image?size=bigger', './assets/images/users/medium/' . $ImageName);
            $small = $this -> DownloadImage('https://twitter.com/' . $screen_name . '/profile_image?size=bigger', './assets/images/users/small/' . $ImageName);
            $original = $this -> DownloadImage('https://twitter.com/' . $screen_name . '/profile_image?size=bigger', './assets/images/users/original/' . $ImageName);
            if (!$large || !$medium || !$small || !$original) {
                $ImageName = '';
            }

            $insert_data = array(
                'FirstName' => $full_name,
                'TwitterId' => $twitter_id,
                'UserScreenName' => $screen_name,
                'ProfileImage' => $ImageName,
                'SocialMedia' => 'Twitter'
            );

            return $this -> register($insert_data);
        }
    }
    /* Function to register/login a user using Instagram
     * Param: data - Data returned as response from Instagram
     * Return: Id of the user
     */

    public function instagram_login($auth_response) {
        $this -> db -> select('Id');
        $this -> db -> from('users');
        $this -> db -> where(array(
            'InstagramId' => $auth_response -> user -> id,
            'IsActive' => 1,
            'IsRemoved' => 0
        ));
        $this -> db -> limit(1);

        $query = $this -> db -> get();

        // If exists, login ( set session data ) else register and then login( set session data )
        if ($query -> num_rows() > 0) {
            $this -> insert_user_preference($query -> row() -> Id);
            return $query -> row() -> Id;
        }
        else {
            $ImageName = generateRandomString(20) . '.jpg';
            $large = $this -> DownloadImage($auth_response -> user -> profile_picture, './assets/images/users/large/' . $ImageName);
            $medium = $this -> DownloadImage($auth_response -> user -> profile_picture, './assets/images/users/medium/' . $ImageName);
            $small = $this -> DownloadImage($auth_response -> user -> profile_picture, './assets/images/users/small/' . $ImageName);
            $original = $this -> DownloadImage($auth_response -> user -> profile_picture, './assets/images/users/original/' . $ImageName);
            if (!$large || !$medium || !$small || !$original) {
                $ImageName = '';
            }

            $insert_data = array(
                'FirstName' => $auth_response -> user -> full_name,
                'InstagramId' => $auth_response -> user -> id,
                'ProfileImage' => $ImageName,
                'SocialMedia' => 'Instagram'
            );

            return $this -> register($insert_data);
        }
    }
    /*  Function to get user preference retailer of logged in user
     *  return - string - retailer company name
     */

    public function get_user_preferred_retailer() {
        $this -> db -> select('retailers.CompanyName, retailers.Id, retailers.LogoImage, userpreferredbrands.StoreId, stores.StoreName,stores.StoreTypeId');
        $this -> db -> from('userpreferredbrands');
        $this -> db -> join('retailers', ' retailers.Id = userpreferredbrands.RetailerId');
        $this -> db -> join('stores', ' stores.Id = userpreferredbrands.StoreId');

        $this -> db -> where(array(
            'UserId' => $this -> session -> userdata('userid')
        ));
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        return $query -> row();
    }
    /*  Function to update user preference for logged in user
     *  param - array - data to be updated
     */

    public function update_user_preference($data) {
        $data_where = array('UserId' => $this -> session -> userdata('userid'));
        $status = $this -> db -> update('userpreferredbrands', $data, $data_where);

        return $status;
    }
    /* Function to get user basket data
     * return - array of user basket details
     */

    public function get_user_basket($limit = 1) {
        $user_preference = $this -> get_user_preferred_retailer();

        $this -> db -> select("a.Id as basket_id, b.Id, b.ProductName,a.ProductCount, b.ProductImage, 
            round(case when e.SpecialPrice > 0 and e.SpecialQty > 0 then ((e.SpecialPrice/e.SpecialQty)*a.ProductCount) when e.SpecialPrice > 0 then e.SpecialPrice*a.ProductCount else c.Price*a.ProductCount end,2) as Price ", false);
        $this -> db -> from('userbasket as a');
        $this -> db -> join('products as b', 'b.Id = a.ProductId');
        $this -> db -> join('storeproducts as c', 'b.Id = c.ProductId');
        $this -> db -> join('retailers as d', 'd.Id = c.RetailerId');
        $this -> db -> join('productspecials as e', 'e.ProductId = c.ProductId and e.SpecialId = a.SpecialId and e.RetailerId = d.Id and e.StoreTypeId = c.StoreTypeId and e.StoreId = c.StoreId AND DATE(e.PriceAppliedFrom) <= \'' . date('Y-m-d') . '\' AND DATE(e.PriceAppliedTo) >= \'' . date('Y-m-d') . '\' and e.IsActive=1 and e.IsApproved=1', 'left');
        $this -> db -> where(
            array(
                'a.UserId' => $this -> session -> userdata('userid'), 
                'd.Id' => $user_preference -> Id,
                'c.StoreId' => $user_preference -> StoreId
            )
            );
        $this -> db -> group_by("c.ProductId,a.SpecialId");

        if ($limit == 1)
            $this -> db -> limit($this -> config -> item('my_basket_limit'));

        $this -> db -> order_by('a.Id', 'Desc');

        $query = $this -> db -> get();
        //echo $this -> db -> last_query(); exit;   
        
        return $query -> result_array();
    }

    public function get_user_basket_total($limit = 1) {
        $user_preference = $this -> get_user_preferred_retailer();

        $query = $this -> db -> query("SELECT sum(sum_total) as sum_total 
                FROM 
                (SELECT 
                    round(case when productspecials.SpecialPrice > 0 and productspecials.SpecialQty > 0 
                    then ((productspecials.SpecialPrice/productspecials.SpecialQty)*userbasket.ProductCount) 
                    when productspecials.SpecialPrice > 0 then productspecials.SpecialPrice*userbasket.ProductCount else storeproducts.Price*userbasket.ProductCount end, 2) as sum_total 
                    FROM (`userbasket`) 
                    JOIN `products` ON `products`.`Id` = `userbasket`.`ProductId` 
                    JOIN `storeproducts` ON `products`.`Id` = `storeproducts`.`ProductId` 
                    JOIN `retailers` ON `retailers`.`Id` = `storeproducts`.`RetailerId` and `retailers`.`IsActive` = 1 and `retailers`.`IsRemoved` = 0
                    left join productspecials on productspecials.ProductId = storeproducts.ProductId and productspecials.RetailerId = retailers.Id 
                    and productspecials.StoreTypeId = storeproducts.StoreTypeId and productspecials.StoreId = storeproducts.StoreId 
                    AND DATE(productspecials.PriceAppliedFrom) <= '" . date('Y-m-d') . "' AND DATE(productspecials.PriceAppliedTo) >= '" . date('Y-m-d') . "' and productspecials.IsActive=1 and productspecials.IsApproved=1 
                    WHERE `userbasket`.`UserId` = " . $this -> session -> userdata('userid') . " AND `retailers`.`Id` = " . $user_preference -> Id . " and storeproducts.StoreId = ".$user_preference -> StoreId." GROUP BY `storeproducts`.`ProductId`) as price");
        
//        echo $this -> db -> last_query();die;

//        $this -> db -> join('products', 'products.Id = userbasket.ProductId');
//        $this -> db -> join('storeproducts', 'products.Id = storeproducts.ProductId');
//        $this -> db -> join('retailers', 'retailers.Id = storeproducts.RetailerId');
//        $this -> db -> where(array('userbasket.UserId' => $this -> session -> userdata('userid'), 'retailers.Id' => $user_preference -> Id));
        //$this -> db -> group_by("storeproducts.ProductId");
        // $query = $this -> db -> get();
        $res_arr = $query -> row_array();
        return $res_arr['sum_total'];
    }
    /* Function to get user basket products count
     * return - int
     */

    public function get_user_basket_products_count() {
        $user_preference = $this -> get_user_preferred_retailer();

        $this -> db -> select("products.Id");
        $this -> db -> from('userbasket');
        $this -> db -> join('products', 'products.Id = userbasket.ProductId');
        $this -> db -> join('storeproducts', 'products.Id = storeproducts.ProductId');
        $this -> db -> join('retailers', 'retailers.Id = storeproducts.RetailerId');
        $this -> db -> where(array('userbasket.UserId' => $this -> session -> userdata('userid'), 'retailers.Id' => $user_preference -> Id));
        $this -> db -> group_by("storeproducts.ProductId");
        $query = $this -> db -> get();

        return $query -> num_rows();
    }
    /* Function to get user basket data total for other retailers
     * return - array of user basket for other retailers details
     */

    public function get_user_basket_other_retailers() {
        $user_preference = $this -> get_user_preferred_retailer();

        $this -> db -> select("retailers.LogoImage, storeproducts.Price");
        $this -> db -> from('userbasket');
        $this -> db -> join('products', 'products.Id = userbasket.ProductId');
        $this -> db -> join('storeproducts', 'products.Id = storeproducts.ProductId');
        $this -> db -> join('retailers', 'retailers.Id = storeproducts.RetailerId');
        $this -> db -> where(array('userbasket.UserId' => $this -> session -> userdata('userid'), 'retailers.Id !=' => $user_preference -> Id));
        $this -> db -> group_by("storeproducts.RetailerId,storeproducts.ProductId");
        $query = $this -> db -> get();

        return $query -> result_array();
    }
    /* Function to remove product from basket
     *  Param - product_id of product to be removed
     *  return - success/failure
     */

    public function remove_from_basket($product_id) {
        $arr = array('ProductId' => $product_id, 'UserId' => $this -> session -> userdata('userid'));

        if ($this -> db -> delete('userbasket', $arr))
            return 'success';
        else
            return 'failure';
    }
    /* Function to update user details
     *  Param - array of data to be updated
     */

    public function edit_profile($data) {
        $data_where = array('Id' => $this -> session -> userdata('userid'));
        $status = $this -> db -> update('users', $data, $data_where);

        return $status;
    }
    /*
     * Function to get user previous quick list
     */

    public function get_quick_shopping_list() {
        $this -> db -> select('Id,ShoppingList');
        $this -> db -> from('userquickshoppinglist');
        $this -> db -> where(array(
            'UserId' => $this -> session -> userdata('userid')
        ));

        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $result = $query -> row_array();

            return $result['ShoppingList'];
        }
        else {
            return "";
        }
    }

    public function set_email($user_id, $email_id) {


        $data = array('Email' => $email_id);

        $this -> db -> where('Id', $user_id);
        if ($this -> db -> update('users', $data)) {
            UserRegistrationConfirmation($user_id);
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_max_min_distance($latitude, $longitude) {
        $this -> db -> select('MIN( 6371 * acos( cos( radians(' . $latitude . ') ) * cos( radians( Latitude ) ) * 
cos( radians( Longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * 
sin( radians( Latitude ) ) ) ) AS min_distance, MAX( 6371 * acos( cos( radians(' . $latitude . ') ) * cos( radians( Latitude ) ) * 
cos( radians( Longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * 
sin( radians( Latitude ) ) ) ) AS max_distance', false)
            -> from('stores');
        $query = $this -> db -> get();
        return $query -> result_array();
    }

    public function set_location_preference($latitude, $longitude, $distance, $user_id) {
        $data = array(
            'PrefLatitude' => $latitude,
            'PrefLongitude' => $longitude,
            'PrefDistance' => $distance
        );

        $this -> db -> where('Id', $user_id);
        if ($this -> db -> update('users', $data)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_states($id = '') {
        $this -> db -> select('Id, Name')
            -> from('state')
            -> where('IsActive', 1)
            -> where('IsRemoved', 0);
        if ($id != '') {
            $this -> db -> where('Id', $id);
        }
        $query = $this -> db -> get();
        return $query -> result_array();
    }

    public function product_details($product_id, $retailer_id, $user_id, $store_id) {

        $this -> db -> select('products.Id,
                           products.ProductName,
                           products.ProductImage,
                           products.ProductDescription,
                           products.RRP,
                           products.Brand,
                           products.SKU,
                           case when userbasket.Id is null then \'\' else userbasket.Id end as BasketId,
                           COUNT(productsreviews.ID) AS reviews_count,
                           AVG(productsreviews.rating) AS avg_rating,
                           usersfavorite.ID AS is_favorite,
                           storeproducts.Price AS store_price,
                           userspricealerts.ID AS price_alert,
                           productspecials.SpecialQty,
                           productspecials.SpecialPrice,
                           DATE_FORMAT(productspecials.PriceAppliedFrom,\'%Y-%c-%d\') as PriceAppliedFrom,
                           DATE_FORMAT(productspecials.PriceAppliedTo,\'%Y-%c-%d\') as PriceAppliedTo', false);

        $this -> db -> join('productsreviews', 'productsreviews.ProductId = products.Id', 'left');
        $this -> db -> join('usersfavorite', 'usersfavorite.ProductId = products.Id AND usersfavorite.UserId =' . $user_id, 'left');
        $this -> db -> join('userspricealerts', 'userspricealerts.ProductId = products.Id AND userspricealerts.UserId =' . $user_id, 'left');
        $this -> db -> join('storeproducts', 'storeproducts.ProductId = products.Id AND storeproducts.RetailerId =' . $retailer_id . " AND (storeproducts.StoreId=" . $store_id . " OR (storeproducts.StoreId=0 AND storeproducts.PriceForAllStores=1)) AND storeproducts.IsActive=1", 'left');
        $this -> db -> join('productspecials', 'productspecials.ProductId = products.Id AND DATE(productspecials.PriceAppliedFrom) <= "' . date('Y-m-d') . '" AND DATE(productspecials.PriceAppliedTo) >= "' . date('Y-m-d') . '" AND productspecials.RetailerId =' . $retailer_id . " AND (productspecials.StoreId=" . $store_id . " OR (productspecials.StoreId=0 AND productspecials.PriceForAllStores=1)) AND productspecials.IsActive=1 AND productspecials.IsApproved =1 ", 'left');

        $this -> db -> join('userbasket', 'userbasket.ProductId = products.Id AND userbasket.UserId =' . $user_id, 'left');

        $this -> db -> where(array(
            'products.IsActive' => 1,
            'products.IsRemoved' => 0
        ));

        $this -> db -> where('products.Id', $product_id);

        $query = $this -> db -> get('products');

//        echo $this->db->last_query();

        return $query -> row_array();
    }

    public function update_user_basket($data, $where) {
        $this -> db -> where($where);
        if ($this -> db -> update('userbasket', $data)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_user_basket_from_id($id) {
        $user_preference = $this -> get_user_preferred_retailer();

        $this -> db -> select("a.Id as basket_id, b.Id, b.ProductName, a.ProductCount, b.ProductImage, 
round(case when e.SpecialPrice > 0 and e.SpecialQty > 0 then ((e.SpecialPrice/e.SpecialQty)*a.ProductCount) when e.SpecialPrice > 0 then e. SpecialPrice*a.ProductCount else c.Price*a.ProductCount end,2) as Price", false);
        $this -> db -> from('userbasket as a');
        $this -> db -> join('products as b ', 'b.Id = a.ProductId');
        $this -> db -> join('storeproducts as c', 'b.Id = c.ProductId');
        $this -> db -> join('retailers as d', 'd.Id = c.RetailerId');
        $this -> db -> join('productspecials as e', 'e.ProductId = c.ProductId and e.RetailerId = d.Id and e.StoreTypeId = c.StoreTypeId and e.StoreId = c.StoreId AND DATE(e.PriceAppliedFrom) <= \'' . date('Y-m-d') . '\' AND DATE(e.PriceAppliedTo) >= \'' . date('Y-m-d') . '\' and e.IsActive=1 and e.IsApproved=1', 'left');
        $this -> db -> where(
            array(
                'a.UserId' => $this -> session -> userdata('userid'),
                'd.Id' => $user_preference -> Id,
                'a.Id' => $id
            )
        );
        $this -> db -> group_by("c.ProductId");
        $this -> db -> limit(1);

        $this -> db -> order_by('a.Id', 'Desc');

        $query = $this -> db -> get();
        return $query -> row_array();
    }
}
