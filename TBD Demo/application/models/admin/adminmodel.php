<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:25-08-2015
 * Dependency: None
 */

class Adminmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 25-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    /**
     * Login a valid user
     *
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function login($email, $password) {

        $this -> db -> select(array(
            'u.Id',
            'u.FirstName',
            'u.Password',
            'u.LastName',
            'u.Email',
            'u.UserRole',
            'u.ProfileImage',
            'r.Id as retailerID',
            'r.LogoImage',
            'r.CompanyName',
            'roles.Type',
            'roles.Level'
        ));
        $this -> db -> from('users u');
        $this -> db -> join('retailers r', 'r.RetailerAdminId = u.Id', 'left');
        $this -> db -> join('userroles roles', 'roles.Id = u.UserRole', 'left');
        $this -> db -> where('u.Email', $email);
        $this -> db -> where('u.Password', MD5($password));
        $this -> db -> where('u.IsActive', 1);
        $this -> db -> where('u.IsRemoved', 0);
        $this -> db -> where('u.UserRole !=', 4);
        $this -> db -> limit(1);
        $query = $this -> db -> get();


        if ($query -> num_rows() == 1) {
            return $query -> result();
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
            return $query -> result();
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

    public function reset_password($token, $new_password) {
        $this -> db -> select(array(
            'Id',
            'FirstName',
            'Email'
        ));
        $this -> db -> from('users');
        $this -> db -> where('PasswordReset', $token);
        $this -> db -> where('IsActive', 1);
        $this -> db -> where('IsRemoved', 0);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {

            $data = array('Password' => MD5($new_password),
                'PasswordReset' => '');
            $this -> db -> where('PasswordReset', $token);
            $this -> db -> update('users', $data);


            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /**
     * Return dashboard Menus
     *
     * @return type
     */
    public function load_menu() {
        $this -> db -> select(array('options.Id', 'OptionName', 'Pagename', 'SequenceNo', 'Icon', 'ParentId'));
        $this -> db -> from('options');
        $this -> db -> join('access', 'access.OptionId = options.Id');
        $this -> db -> join('users', 'users.UserRole = access.RoleId');
        $this -> db -> where('options.IsActive', 1);
        $this -> db -> where('options.IsRemoved', 0);
        $this -> db -> where('users.Id', $this -> session -> userdata('user_id'));
        $this -> db -> order_by('SequenceNo', 'asc');
        $query = $this -> db -> get();
        $arr = $menu_arr = array();
        foreach ($query -> result_array() as $row) {
            $arr[$row['OptionName']] = array($row['Pagename'], $row['SequenceNo'], $row['Icon']);
            array_push($menu_arr, $row['Pagename']);
        }
        $this -> session -> set_userdata('menu', $arr);
        $this -> session -> set_userdata('menu_page_arr', $menu_arr);
        
        return $query -> result_array();
    }

    public function generate_token() {
        $token = openssl_random_pseudo_bytes(8, $cstrong);
        if (!$cstrong) {
            exit('OpenSSL not supported on this server.');
        }

        return bin2hex($token);
    }

    public function category_data() {

        //Get categories count
        $this -> db -> select('COUNT(Id) as category_count');
        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));
        $query = $this -> db -> get('categories');

        return $query -> row_array();
    }

    public function product_data() {

        //Get product count
        $this -> db -> select('COUNT(a.Id) as product_count', FALSE)
            -> from('products as a')
            -> join('categories as b', 'a.MainCategoryId = b.Id')
            -> join('categories as c', 'a.ParentCategoryId = c.Id')
            -> join('categories as d', 'a.CategoryId = d.Id and d.IsActive = 1 and d.IsRemoved = 0', 'left');
        $this -> db -> where(
            array(
                'a.IsRemoved' => 0,
                'a.IsActive' => 1,
                'b.IsRemoved' => 0,
                'b.IsActive' => 1,
                'c.IsRemoved' => 0,
                'c.IsActive' => 1
        ));
        $query = $this -> db -> get();

        return $query -> row_array();
    }

    public function retailers_data() {
        //Get retailer count
        $this -> db -> select('COUNT(Id) as retailer_count');
        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));
        $query = $this -> db -> get('retailers');

        return $query -> row_array();
    }

    public function user_data($type) {

        $this -> db -> select('COUNT(users.Id) as users');
        $this -> db -> join('userroles', 'users.UserRole = userroles.Id', 'left');

        $this -> db -> where(
            array(
                'users.IsRemoved' => 0,
                'users.IsActive' => 1
        ));

        $this -> db -> where('userroles.Type = \'' . $type . '\' or users.UserRole = 0', NULL, FALSE);
        $query = $this -> db -> get('users');
        return $query -> row_array();
    }
    /*
     * Get Store Format Count
     */

    public function store_format_data() {

        $this -> db -> select('COUNT(Id) as store_format_count');
        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1,
                'RetailerId' => $this -> session -> userdata('user_retailer_id')
        ));
        $query = $this -> db -> get('storestypes');        
        return $query -> row_array();
    }
	
	/*
     * Get Store Format Count
     */

    public function store_formats_count($userType) {

        $this -> db -> select('COUNT(Id) as store_format_count');
        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));
		if($userType==3 || $userType==5){
			 $this -> db -> where(
            array(               
                'RetailerId' => $this -> session -> userdata('user_retailer_id')
			));
		}
			
        $query = $this -> db -> get('storestypes'); 

        return $query -> row_array();
    }

    public function store_product_data() {
        
        $this -> db -> select('COUNT(distinct ProductId) as store_products_count');
        $this -> db -> where(
            array(
                'RetailerId' => $this -> session -> userdata('user_retailer_id'),
                'IsRemoved' => 0,
                'IsActive' => 1
        ));
        if ($this -> session -> userdata('user_type') == 5) {
            $this -> db -> where('StoreTypeId', $this -> session -> userdata('user_store_format_id'));
        }
        $query = $this -> db -> get('storeproducts');
        return $query -> row_array();
    }

    public function stores_data() {
        $this -> db -> select('COUNT(Id) as store_count');
        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));

        if ($this -> session -> userdata('user_retailer_id')) {
            $this -> db -> where('RetailerId', $this -> session -> userdata('user_retailer_id'));
        }

        if ($this -> session -> userdata('user_type') == 5) {
            $this -> db -> where('StoreTypeId', $this -> session -> userdata('user_store_format_id'));
        }
        $query = $this -> db -> get('stores');
        
        return $query -> row_array();
    }
	
	  public function getStoresCountByFormatId($storeFormatId) {
        $this -> db -> select('COUNT(Id) as store_count');
        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1
        ));
       
        $this -> db -> where('StoreTypeId', $storeFormatId);
      
        $query = $this -> db -> get('stores');
        
        return $query -> row_array();
    }

    public function special_product_data() {

        $this -> db -> select('COUNT(distinct ProductId) as special_product_count');
        $this -> db -> where(
            array(
                'IsActive' => 1,
                'IsApproved' => 1,
                'RetailerId' => $this -> session -> userdata('user_retailer_id')
        ));

        if ($this -> session -> userdata('user_type') == 5) {
            $this -> db -> where('StoreTypeId', $this -> session -> userdata('user_store_format_id'));
        }
        $query = $this -> db -> get('productspecials');        
        return $query -> row_array();
    }
    /*
     * Get Store user details
     */

    public function get_store_user_details($user_id) {
        $this -> db -> select('storeadmin.StoreId,stores.RetailerId,stores.RetailerId,stores.StoreTypeId,storestypes.Logo');
        $this -> db -> join('stores', 'storeadmin.StoreId = stores.Id', 'left');
        $this -> db -> join('storestypes', 'stores.StoreTypeId = storestypes.Id', 'left');
        $this -> db -> where('UserId', $user_id);

        $query = $this -> db -> get('storeadmin');

        return $query -> row_array();
    }
    /*
     * Get Store user details
     */

    public function get_store_format_user_details($user_id) {
        $this -> db -> select('StoreTypeId,storestypes.RetailerId,storestypes.Logo');
        $this -> db -> join('storestypes', 'storeadmin.StoreTypeId = storestypes.Id', 'left');
        $this -> db -> where('UserId', $user_id);

        $query = $this -> db -> get('storeadmin');

        return $query -> row_array();
    }
    /*
     * Get user count state wise
     */

    public function get_state_users() {
        $this -> db -> _protect_identifiers = FALSE;
        $this -> db -> select('COUNT(users.Id) AS User_Count,StateCode,state.Name,state.Id');

        $this -> db -> join('users', 'users.IsRemoved=0 AND (users.State = state.Name OR users.State = state.Id) AND (users.UserRole = 4 OR users.UserRole = 0 )', 'left');

        $this -> db -> group_by('state.StateCode,state.Name');
        $query = $this -> db -> get('state');

        return $query -> result_array();
    }
    /*
     * Get store count state wise
     */

    public function get_state_stores_count($retailer_id = 0, $storeformat_id = 0) {
        $this -> db -> select('COUNT(stores.Id) AS Store_Count,StateCode,state.Name,state.Id');
        $this -> db -> group_by('state.StateCode,state.Name');
        $select = "";
        if ($this -> session -> userdata('user_type') == 3) {
            $retailer_id = $this -> session -> userdata('user_retailer_id');
        }
        if ($this -> session -> userdata('user_type') == 5) {
            $this -> db -> where('StoreTypeId', $this -> session -> userdata('user_store_format_id'));
        }
        if ($retailer_id) {
            $select .= ' AND stores.RetailerId =' . $retailer_id;
        }
        if ($storeformat_id) {
            $select .= ' AND stores.StoreTypeId =' . $storeformat_id;
        }
        $this -> db -> join('stores', 'stores.StateId = state.Id AND stores.IsRemoved=0' . $select, 'left');
        $query = $this -> db -> get('state');
        
        return $query -> result_array();
    }
    /*
     * Get store count state wise
     */

    public function get_state_stores($state_code, $retailer_id = 0, $store_format_id = 0) {
        $this -> db -> select('a.StoreId,a.StoreName,a.Latitude,a.Longitude,StateCode,COUNT(c.Id) AS Product_Count,b.Name,d.CompanyName,e.StoreType')
            -> from('stores as a')
            -> join('state as b', 'a.StateId = b.Id AND a.IsRemoved=0', 'left')
            -> join('storeproducts as c', 'a.Id = c.StoreId')
            -> join('retailers as d', 'd.Id = a.RetailerId')
            -> join('storestypes as e', 'e.Id = a.StoreTypeId');

        if ($this -> session -> userdata('user_type') == 3) {
            $retailer_id = $this -> session -> userdata('user_retailer_id');
        }
        if ($this -> session -> userdata('user_type') == 5) {
            $retailer_id = $this -> session -> userdata('user_retailer_id');
            $store_format_id = $this -> session -> userdata('user_store_format_id');
        }

        if ($retailer_id) {
            $this -> db -> where('a.RetailerId ', $retailer_id);
        }
        if ($store_format_id) {
            $this -> db -> where('a.StoreTypeId ', $store_format_id);
        }
        if ($state_code) {
            $this -> db -> where('b.StateCode ', $state_code);
        }

        $this -> db -> group_by('a.Id');

        $this -> db -> order_by('a.StoreName');

        $query = $this -> db -> get();
		
        return $query -> result_array();
    }
    /*
     * Number of users using retailer.
     */

    public function get_users_retailers() {

        $this -> db -> select('sum(case when c.Id is null then 0 else 1 end ) AS User_Count,a.Id,a.CompanyName', FALSE)
            -> from('retailers as a')
            -> join('userpreferredbrands as b', 'a.Id = b.RetailerId', 'left')
            -> join('users as c', 'c.Id = b.UserId AND c.IsRemoved=0 AND c.IsActive=1 AND (c.UserRole=4 or c.UserRole=0)', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id');

        $query = $this -> db -> get();

        return $query -> result_array();
    }
    /*
     * Number of users using retailer.
     */

    public function get_product_views_retailers() {

        $this -> db -> select('COUNT(b.Id) AS Products_view_Count,a.Id,a.CompanyName')
            -> from('retailers as a')
            -> join('productviews as b', 'a.Id = b.RetailerId', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                )
            )
            -> group_by('a.Id');

        $query = $this -> db -> get();

        return $query -> result_array();
    }
    /*
     * Get number of products viewed of a particular retailer
     */

    public function get_products_viewed($retailer_name) {
        $this -> db -> select('COUNT(productviews.Id) AS Products_view_Count,products.ProductName');

        $this -> db -> join('retailers', 'retailers.Id = productviews.RetailerId', 'left');

        $this -> db -> join('products', 'products.Id = productviews.ProductId', 'left');

        if ($retailer_name) {
            $this -> db -> where('retailers.CompanyName', urldecode($retailer_name));
        }

        $this -> db -> group_by('productviews.ProductId');

        $this -> db -> order_by('Products_view_Count');

        $query = $this -> db -> get('productviews');

        $this -> db -> last_query();

        return $query -> result_array();
    }

    public function get_state_user_list($state_id, $state_name) {
        $this -> db -> select("concat(FirstName,' ',LastName) as UserName, Email, case when DateOfBirth = '' or DateOfBirth = '0000-00-000:00:00' or DateOfBirth is null then '' else date_format(DateOfBirth,'%d/%m/%Y') end as DOB, Gender, 
concat_ws(',',if(length(HouseNumber), HouseNumber, NULL),if(length(StreetAddress), StreetAddress, NULL),if(length(City), City, NULL),if(length(b.Name), b.Name, NULL),if(length(c.Name), c.Name, NULL),if(length(a.PinCode), a.PinCode, NULL)) as address, TelephoneFixed, Mobile", FALSE)
            -> from('users as a')
            -> join('state as b', 'a.State = b.Id or a.State=b.Name')
            -> join('countries as c', 'a.Country = c.Id')
            -> where('(a.UserRole = 4 OR a.UserRole = 0)', NULL, FALSE)
            -> where('(a.State = ' . $state_id . ' OR a.State = \'' . $state_name . '\')', NULL, FALSE)
            -> where(array(
                'a.IsActive' => 1,
                'a.IsRemoved' => 0
            ));
        $query = $this -> db -> get();
//        echo $this -> db -> last_query();die;
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retailer_user_list($retailer_id) {

        $this -> db -> select("concat(c.FirstName,' ',c.LastName) as UserName, c.Email, case when c.DateOfBirth = '' or c.DateOfBirth = '0000-00-000:00:00' or c.DateOfBirth is null then '' else date_format(c.DateOfBirth,'%d/%m/%Y') end as DOB, c.Gender, 
concat_ws(',',if(length(c.HouseNumber), c.HouseNumber, NULL),if(length(c.StreetAddress), c.StreetAddress, NULL),if(length(c.City), c.City, NULL),if(length(d.Name), d.Name, NULL),if(length(e.Name), e.Name, NULL),if(length(c.PinCode), c.PinCode, NULL)) as address, c.TelephoneFixed, c.Mobile", FALSE)
            -> from('retailers as a')
            -> join('userpreferredbrands as b', 'a.Id = b.RetailerId')
            -> join('users as c', 'c.Id = b.UserId AND c.IsRemoved=0 AND c.IsActive=1 AND (c.UserRole=4 or c.UserRole=0)')
            -> join('state as d', 'd.Id=c.State or d.Name=c.State', 'left')
            -> join('countries as e', 'e.Id=c.Country', 'left')
            -> where(
                array(
                    'a.Id' => $retailer_id,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0
                )
            )
            -> group_by('c.Id');

        $query = $this -> db -> get();

//        echo $this -> db -> last_query();die;
        return $query -> result_array();
    }

    public function get_retailers() {
        $this -> db -> select('Id,CompanyName,CompanyDescription')
            -> from('retailers')
            -> where(
                array(
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
	public function get_users($parameter,$userType) {
      /*  $this -> db -> select('Id,FirstName,LastName,Email,DateOfBirth,CreatedOn');
           $this -> db -> from('users');
           $this -> db -> where(
                array(
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
				if($parameter=='newUsers'){
					$this->db->where('CreatedOn >=', date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s') . ' -30 days')));			
					$this->db->where('CreatedOn <=', date('Y-m-d H:i:s') );
				}
        $query = $this -> db -> get();
		
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
				
				*/
				$this -> db -> select('users.Id,users.FirstName,users.LastName,users.Email,users.DateOfBirth,users.CreatedOn');
        $this -> db -> join('userroles', 'users.UserRole = userroles.Id', 'left');

        $this -> db -> where(
            array(
                'users.IsRemoved' => 0,
                'users.IsActive' => 1
        ));

        $this -> db -> where('userroles.Type = \'Users\' or users.UserRole = 0', NULL, FALSE);
				if($parameter=='newUsers'){
					$this->db->where('users.CreatedOn >=', date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s') . ' -30 days')));			
					$this->db->where('users.CreatedOn <=', date('Y-m-d H:i:s') );
				}
				
        $query = $this -> db -> get('users');
				return $query -> result_array();				
    }
	public function getTotalLiveSpecial() {
        $this -> db -> select('count(Id) as count');
           $this -> db -> from('specials');
           $this -> db -> where(
                array(
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
			);
	
			$this->db->where('SpecialTo >=', date("Y-m-d"));			
			$this->db->where('SpecialFrom <=', date('Y-m-d') );
		
        $query = $this -> db -> get();
		
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }
	
    public function get_storetype($retailer_id) {
        $this -> db -> select('Id, StoreType')
            -> from('storestypes')
            -> where(array(
                'IsActive' => 1,
                'IsRemoved' => 0
            ));
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retailers_count() {
        $this -> db -> select('count(a.Id) as count, b.Name as state_name', FALSE)
            -> from('retailers as a')
            -> join('state as b', 'a.StateId = b.Id')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('b.Id');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retailer_store_count() {
        $this -> db -> select('count(b.Id) as count, a.CompanyName,a.Id', FALSE)
            -> from('retailers as a')
            -> join('storestypes as b', 'b.RetailerId=a.Id and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> join('stores as c', 'c.RetailerId=a.Id and c.StoreTypeId = b.Id and c.IsActive = 1 and c.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retailer_consumer_count() {
        $this -> db -> select('a.CompanyName, count(c.Id) as count')
            -> from('retailers as a')
            -> join('userpreferredbrands as b', 'a.Id = b.RetailerId', 'left')
            -> join('users as c', 'c.Id = b.UserId and c.IsActive  = 1 and c.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retailer_admin_count() {
        $this -> db -> select('count(b.Id) as count, a.CompanyName', FALSE)
            -> from('retailers as a')
            -> join('users as b', 'b.Id = a.RetailerAdminId and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retailer_product_count() {
        $this -> db -> select('count(distinct b.ProductId) as count, a.CompanyName', FALSE)
            -> from('retailers as a')
            -> join('storeproducts as b', 'b.RetailerId = a.Id and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> join('products as c', 'c.Id = b.ProductId and c.IsActive = 1 and c.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                )
            )
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_store_count_by_region() {
        $this -> db -> select('count(b.Id) as count, a.Name as state_name', FALSE)
            -> from('state as a')
            -> join('stores as b', 'a.Id = b.StateId and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_format_count_by_region() {
        $this -> db -> select('count(c.Id) as count, a.Name as state_name', FALSE)
            -> from('state as a')
            -> join('retailers as b', 'b.StateId = a.Id and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> join('storestypes as c', 'c.RetailerId = b.Id and c.IsActive = 1 and c.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_storetype_store_count($retailer_id) {
        $this -> db -> select('count(b.Id) as count, a.StoreType, a.Id', FALSE)
            -> from('storestypes as a')
            -> join('stores as b', 'b.StoreTypeId = a.Id and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'a.RetailerId' => $retailer_id
                )
            )
            -> group_by('a.Id');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_all_retailer_store_format_store_count() {
        $this -> db -> select('count(c.Id) as count, a.Id as retailer_id,a.CompanyName, b.Id as store_type_id,b.StoreType,a.LogoImage', FALSE)
            -> from('retailers as a')
            -> join('storestypes as b', 'b.RetailerId = a.Id and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> join('stores as c', 'c.StoreTypeId = b.Id and c.RetailerId = a.Id and c.IsActive = 1 and c.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id, b.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_store_special_count($retailer_id, $store_type_id) {
        $this -> db -> select('count(b.Id) as count, c.StoreName, c.Id', FALSE)
            -> from('storeproducts as a')
            -> join('productspecials as b', 'a.ProductId = b.ProductId and a.RetailerId = b.RetailerId and a.StoreTypeId = b.StoreTypeId and a.StoreId = b.StoreId and now() between b.PriceAppliedFrom and b.PriceAppliedTo')
            -> join('stores as c', 'c.Id = a.StoreId')
            -> where(
                array(
                    'a.RetailerId' => $retailer_id,
                    'a.StoreTypeId' => $store_type_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsApproved' => 1
                )
            )
            -> group_by('a.StoreId')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_back_user_total_count() {
        $retailer_count_array = [];
        $this -> db -> select('count(*) as count, a.Id, a.CompanyName', FALSE)
            -> from('retailers as a')
            -> where('a.RetailerAdminId is not null', NULL, FALSE)
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $res_arr = $query -> result_array();
            foreach ($res_arr as $result) {
                $retailer_count_array[$result['Id']] = array(
                    'label' => $result['CompanyName'],
                    'y' => $result['count']
                );
                $store_type_count_details = $this -> store_type_user_count($result['Id']);
                if ($store_type_count_details) {
                    $retailer_count_array[$result['Id']]['y'] += $store_type_count_details['count'];
                }
                $store_count_details = $this -> store_user_count($result['Id']);
                if ($store_count_details) {
                    $retailer_count_array[$result['Id']]['y'] += $store_count_details['count'];
                }
            }
            echo '<pre>';
            print_r($retailer_count_array);
            die;
        }
    }

    public function store_type_user_count($retailer_id) {
        $this -> db -> select('count(b.Id) as count, a.Id', FALSE)
            -> from('storestypes as a')
            -> join('storeadmin as b', 'a.Id = b.StoreTypeId')
            -> where(
                array(
                    'a.RetailerId' => $retailer_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function store_user_count($retailer_id) {
        $this -> db -> select('count(d.Id) as count', FALSE)
            -> from('retailers as a')
            -> join('storestypes as b', 'a.Id = b.RetailerId')
            -> join('stores as c', 'c.RetailerId = a.Id and c.StoreTypeId = b.Id')
            -> join('storeadmin as d', 'd.StoreId = c.Id')
            -> where(
                array(
                    'c.RetailerId' => $retailer_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_consumer_total_count() {
        $this -> db -> select('count(a.Id) as count, c.CompanyName, c.Id as retailer_id', FALSE)
            -> from('users as a')
            -> join('userpreferredbrands as b', 'b.UserId = a.Id')
            -> join('retailers as c', 'c.Id = b.RetailerId')
            -> where('(a.UserRole = 0 or a.UserRole = 4)', NULL, FALSE)
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0,
                )
            )
            -> group_by('c.Id');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_category_product_count() {
        $this -> db -> select('count(b.Id) as count, a.CategoryName, a.Id', FALSE)
            -> from('categories as a')
            -> join('products as b', 'b.MainCategoryId = a.Id and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_store_special_count_expansion($retailer, $store_type, $store) {
        $this -> db -> select('d.ProductName, e.CategoryName as main_cat, f.CategoryName as parent_cat, g.CategoryName as cat, b.SpecialPrice, b.SpecialQty, h.SpecialName, date_format(h.SpecialFrom,\'%d/%m/%Y\') as SpecialFrom, date_format(h.SpecialTo,\'%d/%m/%Y\') as SpecialTo', FALSE)
            -> from('storeproducts as a')
            -> join('productspecials as b', 'a.ProductId = b.ProductId and a.RetailerId = b.RetailerId and a.StoreTypeId = b.StoreTypeId and a.StoreId = b.StoreId and now() between b.PriceAppliedFrom and b.PriceAppliedTo')
            -> join('stores as c', 'c.Id = a.StoreId')
            -> join('products as d', 'd.Id = b.ProductId')
            -> join('categories as e', 'e.Id = d.MainCategoryId')
            -> join('categories as f', 'f.Id = d.ParentCategoryId')
            -> join('categories as g', 'g.Id = d.CategoryId', 'left')
            -> join('specials as h', 'h.Id = b.SpecialId')
            -> where(
                array(
                    'a.RetailerId' => $retailer,
                    'a.StoreTypeId' => $store_type,
                    'a.StoreId' => $store,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsApproved' => 1
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_region_consumer_count() {
        $this -> db -> select('count(b.Id) as count, a.Name as state_name, a.Id', FALSE)
            -> from('state as a')
            -> join('users as b', 'b.State = a.Id and b.IsActive = 1 and b.IsRemoved = 0')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> where('(b.UserRole = 0 or b.UserRole = 4)', NULL, FALSE)
            -> group_by('a.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_gender_consumer_count() {
        $this -> db -> select("count(Id) as count, case when Gender = 'M' then 'Male' when Gender = 'F' then 'Female' else 'Not Mentioned' end as gender_exp, Gender", FALSE)
            -> from('users')
            -> where(
                array(
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
            )
            -> where('(UserRole = 0 or UserRole = 4)', NULL, FALSE)
            -> group_by('Gender')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_user_device_count() {
        $this -> db -> select("sum(case when DeviceType = 'A' then 1 else 0 end) as android_count,sum(case when DeviceType = 'I' then 1 else 0 end) as ios_count,sum(case when b.Id is null then 1 else 0 end) as web_count", FALSE)
            -> from('users as a')
            -> join('userdevices as b', 'a.Id = b.UserId', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
            )
            -> where('(a.UserRole = 0 or a.UserRole = 4)', NULL, FALSE)
            -> group_by('b.DeviceType');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $result_array = $query -> result_array();
            $android_count = 0;
            $ios_count = 0;
            $web_count = 0;
            foreach ($result_array as $res) {
                $android_count += $res['android_count'];
                $ios_count += $res['ios_count'];
                $web_count += $res['web_count'];
            }
            return array(
                array(
                    'label' => 'Android',
                    'y' => $android_count,
                    'id' => 'A'
                ),
                array(
                    'label' => 'iOS',
                    'y' => $ios_count,
                    'id' => 'I'
                ),
                array(
                    'label' => 'Web',
                    'y' => $web_count,
                    'id' => 'W'
                ),
            );
        }
        return FALSE;
    }

    public function get_consumer_retailer_expansion($retailer) {
        $this -> db -> select("concat(b.FirstName,' ',b.LastName) as UserName, b.Email, case when b.DateOfBirth = '' or b.DateOfBirth = '0000-00-000:00:00' or b.DateOfBirth is null then '' else date_format(b.DateOfBirth,'%d/%m/%Y') end as DOB, b.Gender, 
concat_ws(',',if(length(b.HouseNumber), b.HouseNumber, NULL),if(length(b.StreetAddress), b.StreetAddress, NULL),if(length(b.City), b.City, NULL),if(length(c.Name), c.Name, NULL),if(length(d.Name), d.Name, NULL),if(length(b.PinCode), b.PinCode, NULL)) as address, b.TelephoneFixed, b.Mobile", FALSE)
            -> from('userpreferredbrands as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> join('state as c', 'b.State = c.Id', 'left')
            -> join('countries as d', 'd.Id = b.Country', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'a.RetailerId' => $retailer
                )
            )
            -> where('(b.UserRole = 0 or b.UserRole = 4)', NULL, FALSE);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_consumer_region_expansion($state) {
        $this -> db -> select("concat(b.FirstName,' ',b.LastName) as UserName, b.Email, case when b.DateOfBirth = '' or b.DateOfBirth = '0000-00-000:00:00' or b.DateOfBirth is null then '' else date_format(b.DateOfBirth,'%d/%m/%Y') end as DOB, b.Gender,concat_ws(',',if(length(b.HouseNumber), b.HouseNumber, NULL),if(length(b.StreetAddress), b.StreetAddress, NULL),if(length(b.City), b.City, NULL),if(length(a.Name), a.Name, NULL),if(length(c.Name), c.Name, NULL),if(length(b.PinCode), b.PinCode, NULL)) as address, b.TelephoneFixed, b.Mobile", FALSE)
            -> from('state as a')
            -> join('users as b', 'b.State = a.Id')
            -> join('countries as c', 'c.Id = b.Country', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'a.Id' => $state
                )
            )
            -> where('(b.UserRole = 0 or b.UserRole = 4)', NULL, FALSE);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_consumer_gender_expansion($gender) {
        $this -> db -> select("concat(b.FirstName,' ',b.LastName) as UserName, b.Email, case when b.DateOfBirth = '' or b.DateOfBirth = '0000-00-000:00:00' or b.DateOfBirth is null then '' else date_format(b.DateOfBirth,'%d/%m/%Y') end as DOB, b.Gender, 
concat_ws(',',if(length(b.HouseNumber), b.HouseNumber, NULL),if(length(b.StreetAddress), b.StreetAddress, NULL),if(length(b.City), b.City, NULL),if(length(c.Name), c.Name, NULL),if(length(d.Name), d.Name, NULL),if(length(b.PinCode), b.PinCode, NULL)) as address, b.TelephoneFixed, b.Mobile ", FALSE)
            -> from('users as b')
            -> join('state as c', 'b.State = c.Id', 'left')
            -> join('countries as d', 'd.Id = b.Country', 'left')
            -> where(
                array(
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0
                )
            )
            -> where('(b.UserRole = 0 or b.UserRole = 4)', NULL, FALSE)
            -> where("b.Gender = '" . trim($gender) . "'");
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_consumer_device_expansion($device) {
        $this -> db -> select("concat(b.FirstName,' ',b.LastName) as UserName, b.Email, case when b.DateOfBirth = '' or b.DateOfBirth = '0000-00-000:00:00' or b.DateOfBirth is null then '' else date_format(b.DateOfBirth,'%d/%m/%Y') end as DOB, b.Gender, 
concat_ws(',',if(length(b.HouseNumber), b.HouseNumber, NULL),if(length(b.StreetAddress), b.StreetAddress, NULL),if(length(b.City), b.City, NULL),if(length(c.Name), c.Name, NULL),if(length(d.Name), d.Name, NULL),if(length(b.PinCode), b.PinCode, NULL)) as address, b.TelephoneFixed, b.Mobile", FALSE)
            -> from('userdevices as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> join('state as c', 'c.Id = b.State', 'left')
            -> join('countries as d', 'd.Id = b.Country', 'left')
            -> where(
                array(
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'a.DeviceType' => $device
                )
            )
            -> where('(b.UserRole = 0 or b.UserRole = 4)', NULL, FALSE);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_consumer_web_expansion() {
        $query = $this -> db -> query("select concat(a.FirstName,' ',a.LastName) as UserName, a.Email, case when a.DateOfBirth = '' or a.DateOfBirth = '0000-00-000:00:00' or a.DateOfBirth is null then '' else date_format(a.DateOfBirth,'%d/%m/%Y') end as DOB, a.Gender, concat_ws(',',if(length(a.HouseNumber), a.HouseNumber, NULL),if(length(a.StreetAddress), a.StreetAddress, NULL),if(length(a.City), a.City, NULL),if(length(b.Name), b.Name, NULL),if(length(c.Name), c.Name, NULL),if(length(a.PinCode), a.PinCode, NULL)) as address, a.TelephoneFixed, a.Mobile from users as a left join state as b on b.Id = a.State left join countries as c on c.Id = a.Country where a.Id not in(select b.Id from userdevices as a join users as b on b.Id = a.UserId left join state as c on c.Id = b.State left join countries as d on d.Id = b.Country where b.IsActive = 1 and b.IsRemoved = 0) and (a.UserRole = 0 or a.UserRole = 4) and a.IsActive = 1 and a.IsRemoved = 0");
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_category_sub_total() {
        $this -> db -> select('count(b.Id) as count, a.Id as main_cat_id,a.CategoryName as main_cat, c.Id as parent_cat_id,c.CategoryName as parent_cat', FALSE)
            -> from('categories as a')
            -> join('products as b', 'b.MainCategoryId = a.Id')
            -> join('categories as c', 'c.Id = b.ParentCategoryId')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                )
            )
            -> group_by('c.Id')
            -> order_by('count', 'desc');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_product_expansion_details($main_cat, $parent_cat) {
        $this -> db -> select('a.ProductName, a.Brand, b.CategoryName, a.RRP')
            -> from('products as a')
            -> join('categories as b', 'b.Id = a.CategoryId and b.IsActive = 1 and b.IsRemoved = 0', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'a.MainCategoryId' => $main_cat,
                    'a.ParentCategoryId' => $parent_cat
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retaielr_storetypes($retailer_id) {
        $this -> db -> select('count(c.Id) as count, b.StoreType, a.Id as retailer_id, b.Id as storetype_id', FALSE)
            -> from('retailers as a')
            -> join('storestypes as b', 'b.RetailerId = a.Id')
            -> join('stores as c', 'c.RetailerId = a.Id and c.StoreTypeId = b.Id')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0,
                    'a.Id' => $retailer_id
                )
            )
            -> group_by('b.Id');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_retailer_id_from_storetype($storetype) {
        $this -> db -> select('RetailerId')
            -> from('storestypes')
            -> where(
                array(
                    'IsActive' => 1,
                    'IsRemoved' => 0,
                    'Id' => $storetype
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $result_array = $query -> row_array();
            return $result_array['RetailerId'];
        }
        return FALSE;
    }

    public function get_retailer_storetype_stores($retailer_id, $storetype_id) {
        $this -> db -> select("a.Id,a.StoreName, a.StoreId, concat_ws(':',if(length(a.ContactPerson), a.ContactPerson, NULL),if(length(a.ContactPersonNumber), a.ContactPersonNumber, NULL)) as Contact, concat_ws(',',if(length(a.Building), a.Building, NULL),if(length(a.StreetAddress), a.StreetAddress, NULL),if(length(a.City), a.City, NULL),if(length(d.Name), d.Name, NULL),if(length(e.Name), e.Name, NULL)) as Address", FALSE)
            -> from('stores as a')
            -> join('retailers as b', 'b.Id = a.RetailerId')
            -> join('storestypes as c', 'c.Id = a.StoreTypeId')
            -> join('state as d', 'd.Id = a.StateId', 'left')
            -> join('countries as e', 'e.Id = a.CountryId', 'left')
            -> where(
                array(
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0,
                    'b.Id' => $retailer_id,
                    'c.Id' => $storetype_id
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }

    public function get_store_consumer_count($store_id) {
        $this -> db -> select('count(b.Id) as count, c.StoreName, c.Id')
            -> from('userpreferredbrands as a')
            -> join('users as b', 'b.Id = a.UserId')
            -> join('stores as c', 'c.Id = a.StoreId')
            -> where(
                array(
                    'a.StoreId' => $store_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_store_user_count($store_id) {
        $this -> db -> select('count(a.Id) as count, c.StoreName, c.Id')
            -> from('storeadmin as a')
            -> join('users as b', 'a.UserId = b.Id')
            -> join('stores as c', 'a.StoreId = c.Id')
            -> where(
                array(
                    'a.StoreId' => $store_id,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_store_product_count($store_id) {
        $this -> db -> select('count(c.Id) as count, a.StoreName, a.Id', FALSE)
            -> from('stores as a')
            -> join('storeproducts as b', 'b.StoreId = a.Id')
            -> join('products as c', 'c.Id = b.ProductId')
            -> where(
                array(
                    'b.StoreId' => $store_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }

    public function get_store_one_special_count($store_id) {
        $this -> db -> select('count(d.Id) as count, a.StoreName, a.Id', FALSE)
            -> from('stores as a')
            -> join('storeproducts as b', 'b.StoreId = a.Id')
            -> join('products as c', 'c.Id = b.ProductId')
            -> join('productspecials as d', 'd.ProductId = b.ProductId and d.RetailerId =b.RetailerId and d.StoreTypeId = b.StoreTypeId and d.StoreId = b.StoreId and now() between d.PriceAppliedFrom and d.PriceAppliedTo')
            -> where(
                array(
                    'b.StoreId' => $store_id,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,
                    'c.IsActive' => 1,
                    'c.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }
	
	public function get_promo_list_by_storeId($storeId) {
        $this -> db -> select('sp.Id, sp.SpecialName, sp.SpecialFrom, sp.SpecialTo', FALSE)
            -> from('specials as sp')
			-> join('special_stores as ss', 'sp.Id = ss.SpecialId')
			-> join('stores as st', 'st.Id = ss.StoreId')
            -> where(
                array(
                    'st.StoreId' => $storeId,
				//	'sp.IsActive'=>'1',
					'sp.IsRemoved'=>'0',
                )
            );
        $query = $this -> db -> get();
        return $query -> result_array();
    }
	public function getTotalLiveSpecialListing() {
        $this -> db -> select('sp.Id, sp.SpecialName, sp.SpecialFrom, sp.SpecialTo');
           $this -> db -> from('specials sp');
           $this -> db -> where(
                array(
                    'sp.IsActive' => 1,
                    'sp.IsRemoved' => 0
                )
			);
	
			$this->db->where('sp.SpecialTo >=', date("Y-m-d"));			
			$this->db->where('sp.SpecialFrom <=', date('Y-m-d') );
		
        $query = $this -> db -> get();
		
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return FALSE;
    }
	public function getTotalPromosByStore($storeId) {
        $this -> db -> select('count(sp.Id) totalSpecial', FALSE)
            -> from('specials as sp')
			-> join('special_stores as ss', 'sp.Id = ss.SpecialId')
			-> join('stores as st', 'st.Id = ss.StoreId')
            -> where(
                array(
                    'st.StoreId' => $storeId,
					'sp.IsRemoved'=>'0',
                )
            );
        $query = $this -> db -> get();
        return $query -> row_array();
    }
	public function getStoreNameById($store_id) {
        $this -> db -> select('StoreName');
        $this -> db -> from('stores');
        $this -> db -> where('StoreId', $store_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
		//echo $this->db->last_query();
        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }
	
	public function getStoreFormatsOfUserType($userType) {
        $this -> db -> select('storestypes.Id,storestypes.StoreType,retailers.CompanyName');
        $this -> db -> from('storestypes');
		$this -> db -> join('retailers', 'retailers.Id = storestypes.RetailerId');
		$this -> db -> where(
                array(
					'storestypes.IsActive'=>'1',
					'storestypes.IsRemoved'=>'0',
					'retailers.IsActive'=>'1',
					'retailers.IsRemoved'=>'0',
                )
            );
		if($userType==3 || $userType==5){
			$this -> db -> where(
                array(
                    'RetailerId' => $this -> session -> userdata('user_retailer_id'),
					
                )
            );
		}
        $query = $this -> db -> get();
		return $query -> result_array();
    }
	public function getStoresByFormatAndRetailer($store_format_id,$userType) {
		
        $this -> db -> select('stores.StoreName,stores.Id,stores.StoreId,retailers.CompanyName,storestypes.StoreType,state.Name,retailers.Id as RetailerId', FALSE);
        $this -> db -> from('stores');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
		$this -> db -> join('retailers', 'retailers.Id = stores.RetailerId');
		$this -> db -> join('state', 'state.Id = stores.StateId');
        $this -> db -> where(array('stores.IsRemoved' => 0, 'stores.IsActive' => 1));
		if($userType==3 || $userType==5){
			$this -> db -> where(array('stores.RetailerId'=>$this -> session -> userdata('user_retailer_id')));
		}
        $this -> db -> where_in('stores.StoreTypeId', $store_format_id);
        $this -> db -> order_by("stores.StoreName");
        $query = $this -> db -> get();

        return $query -> result_array();
    }
	public function getStoresByprovienceId($provienceId) {
		
        $this -> db -> select('stores.StoreName,stores.Id,stores.StoreId,retailers.CompanyName,storestypes.StoreType,state.Name,retailers.Id as RetailerId', FALSE);
        $this -> db -> from('stores');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
		$this -> db -> join('state', 'state.Id = stores.StateId');
		$this -> db -> join('retailers', 'retailers.Id = stores.RetailerId');
        $this -> db -> where(array('retailers.IsRemoved' => 0, 'retailers.IsActive' => 1,'stores.IsRemoved' => 0, 'stores.IsActive' => 1,'stores.StateId'=>$provienceId));		
        
        $this -> db -> order_by("stores.StoreName");
        $query = $this -> db -> get();

        return $query -> result_array();
    }
	public function getAllActiveStores() {
		
        $this -> db -> select('stores.StoreName,stores.Id,stores.StoreId,retailers.CompanyName,storestypes.StoreType,state.Name,retailers.Id as RetailerId', FALSE);
        $this -> db -> from('stores');
        $this -> db -> join('storestypes', 'storestypes.Id = stores.StoreTypeId');
		$this -> db -> join('state', 'state.Id = stores.StateId');
		$this -> db -> join('retailers', 'retailers.Id = stores.RetailerId');
        $this -> db -> where(array('retailers.IsRemoved' => 0, 'retailers.IsActive' => 1,'stores.IsRemoved' => 0, 'stores.IsActive' => 1));		
        
        $this -> db -> order_by("stores.StoreName");
        $query = $this -> db -> get();

        return $query -> result_array();
    }
	public function getProductsByStoreId($retailer_id,$store_id) {
        $this -> db -> select('count(a.ProductId) as totalProducts')		
            -> from('storeproducts as a')
			 -> join('products p', 'p.Id = a.ProductId')
            -> join('stores as b', 'b.Id = a.StoreId')
            -> where(
                array(
                    'a.RetailerId' => $retailer_id,
                    'a.StoreId' => $store_id,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0,   
					'a.IsActive' => 1,
                    'a.IsRemoved' => 0,      	
					'p.IsActive' => 1,
                    'p.IsRemoved' => 0,      					
                )
        );
		$this -> db -> group_by('a.ProductId');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
		
    }
	public function getProductsByRetailerAndCategory($userType,$categoryId=0){
		$this -> db -> select('p.Id,p.ProductName,p.Brand,p.ModifiedOn,c.CategoryName', FALSE);
        $this -> db -> from('products p');
        $this -> db -> join('storeproducts sp', 'p.Id = sp.ProductId');
		$this -> db -> join('retailers r', 'r.Id = sp.RetailerId');
		//if($categoryId!=0){
			$this -> db -> join('categories c', 'c.Id = p.MainCategoryId');
		//}
		
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1,'sp.IsRemoved' => 0, 'sp.IsActive' => 1,'c.IsRemoved' => 0, 'c.IsActive' => 1));
		if($userType==3 || $userType==5){
			$this -> db -> where(array('sp.RetailerId'=>$this -> session -> userdata('user_retailer_id')));
		}
		//if($categoryId!=0)
        //$this -> db -> where_in('c.Id', $categoryId);
        $this -> db -> group_by('p.Id');
	    $this -> db -> order_by('p.Id', 'desc');
        $query = $this -> db -> get();
		
        return $query -> result_array();
	}
	public function totalProductsCount($userType) {
        
        $this -> db -> select('COUNT(distinct sp.ProductId) as store_products_count');
		$this -> db -> from('products p');
		$this -> db -> join('storeproducts sp', 'p.Id = sp.ProductId');
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1,'sp.IsRemoved' => 0, 'sp.IsActive' => 1));
		if($userType==3 || $userType==5){
				$this -> db -> where(array('sp.RetailerId'=> $this -> session -> userdata('user_retailer_id')));
		}
		
        
        $query = $this -> db -> get();
        return $query -> row_array();
    }
	
	public function getTotalSharesOfProductId($productId) {
		
        $this -> db -> select('COUNT(Id) as totalShares');
        $this -> db -> where(
            array(
                'IsRemoved' => 0,
                'IsActive' => 1,
				'ProductId'=>$productId
        ));
        $query = $this -> db -> get('product_shares');
        return $query -> row_array();
    }
	public function getTotalViewsOfProductId($productId) {
		
        $this -> db -> select('COUNT(Id) as totalViews');
        $this -> db -> where(
            array(
               
				'ProductId'=>$productId
        ));
        $query = $this -> db -> get('productviews');
        return $query -> row_array();
    }
	public function getTotalReviewsOfProductId($productId) {
		
        $this -> db -> select('COUNT(Id) as totalReviews');
        $this -> db -> where(
            array(
                
				'ProductId'=>$productId
        ));
        $query = $this -> db -> get('productsreviews');
        return $query -> row_array();
    }
	public function getAllCategoriesOfRetailer($userType)
	{
		$this -> db -> select('c.Id,c.CategoryName', FALSE);
        $this -> db -> from('categories c');
        $this -> db -> join('retailercategories rc', 'c.Id = rc.CategoryID');
		$this -> db -> join('retailers r', 'r.Id = rc.RetailerId');
		$this -> db -> join('products p', 'c.Id = p.MainCategoryId');		
        $this -> db -> where(array('c.IsRemoved' => 0, 'c.IsActive' => 1));		
		if($userType==3 || $userType==5){
			$this -> db -> where(array('c.IsRemoved' => 0, 'c.IsActive' => 1,'rc.RetailerId'=>$this -> session -> userdata('user_retailer_id')));		
		}
        $this -> db -> group_by('c.Id');
	    $this -> db -> order_by('c.Id', 'desc');
        $query = $this -> db -> get();
	
        return $query -> result_array();
	}
	public function getSpecialProductsOfRetailer($userType){
		$this -> db -> select('p.Id,p.ProductName,p.Brand,p.ModifiedOn,c.CategoryName', FALSE);
        $this -> db -> from('products p');
		$this -> db -> join('productspecials ps', 'p.Id = ps.ProductId');
		$this -> db -> join('specials s', 's.Id = ps.SpecialId');
		$this -> db -> join('retailers r', 'r.Id = ps.RetailerId');		
		$this -> db -> join('categories c', 'c.Id = p.MainCategoryId');		
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1, 'ps.IsActive' => 1,
                'ps.IsApproved' => 1));
		if($userType==3 || $userType==5){
			$this -> db -> where(array('ps.RetailerId' => $this -> session -> userdata('user_retailer_id')));
		}
        $this -> db -> group_by('p.Id');
	    $this -> db -> order_by('p.Id', 'desc');
        $query = $this -> db -> get();
		
        return $query -> result_array();
	}
	
	public function totalSpecialProduct($userType) {
        
        $this -> db -> select('COUNT(distinct ps.ProductId) as special_product_count');
		$this -> db -> from('products p');
		$this -> db -> join('productspecials ps', 'p.Id = ps.ProductId');
		$this -> db -> join('specials s', 's.Id = ps.SpecialId');
		$this -> db -> join('retailers r', 'r.Id = ps.RetailerId');	
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1, 'ps.IsActive' => 1,
                'ps.IsApproved' => 1));		
		if($userType==3 || $userType==5)		{
			$this -> db -> where(array('ps.RetailerId'=>$this -> session -> userdata('user_retailer_id')));	
		}
		
				
        $query = $this -> db -> get();
        return $query -> row_array();
    }
	
	public function getTrendingProductsSpecialFromDB($userType,$monthYr) {
        
        $this -> db -> select('ps.ProductId,COUNT(ps.ProductId) as productCount,p.ProductName,p.Brand');
		$this -> db -> from('products p');
		$this -> db -> join('productspecials ps', 'p.Id = ps.ProductId');
		$this -> db -> join('specials s', 's.Id = ps.SpecialId');
		$this -> db -> join('retailers r', 'r.Id = ps.RetailerId');
		$this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1, 'ps.IsActive' => 1,'ps.IsApproved' => 1));	
       
		if($userType==3 || $userType==5){
			 $this -> db -> where(array('ps.RetailerId'=>$this -> session -> userdata('user_retailer_id')));		
		}
		$this->db->like('ps.PriceAppliedFrom',$monthYr.'-');
		$this->db->like('ps.PriceAppliedTo',$monthYr.'-');
		$this -> db -> group_by('p.Id');
		$this -> db -> order_by('productCount', 'desc');
		$this -> db -> limit(20,0);
        $query = $this -> db -> get();		
		//echo $this->db->last_query();
        return $query -> result_array();
    }
	public function getTrendingProductsViewsFromDB($userType,$monthYr) {
        
        $this -> db -> select('ps.ProductId,COUNT(ps.ProductId) as productCount,p.ProductName,p.Brand');
		$this -> db -> from('products p');
		$this -> db -> join('productviews ps', 'p.Id = ps.ProductId');
		$this -> db -> join('retailers r', 'r.Id = ps.RetailerId');
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1));
		if($userType==3 || $userType==5){
			 $this -> db -> where(array('ps.RetailerId'=>$this -> session -> userdata('user_retailer_id')));		
		}		
		$this->db->like('ps.ViewDate',$monthYr.'-');
		$this -> db -> group_by('p.Id');
		$this -> db -> order_by('productCount', 'desc');
		$this -> db -> limit(20,0);
        $query = $this -> db -> get();	
		//	echo $this->db->last_query();
        return $query -> result_array();
    }
	
	public function getTrendingProductsReviewsFromDB($userType,$monthYr) {
        //$this -> db -> where_in('stores.StoreTypeId', $store_format_id);
        $this -> db -> select('ps.ProductId,COUNT(ps.ProductId) as productCount');
		$this -> db -> from('products p');
		$this -> db -> join('productsreviews ps', 'p.Id = ps.ProductId');		
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1));	
		if($userType==3 || $userType==5){
			 $this -> db -> where(array('ps.RetailerId'=>$this -> session -> userdata('user_retailer_id')));		
		}			
		$this->db->like('ps.CreatedOn',$monthYr.'-');
		$this -> db -> group_by('p.Id');
		$this -> db -> order_by('productCount', 'desc');		
        $query = $this -> db -> get();	
		
        return $query -> result_array();
		
    }
	public function getTrendingProductsActualReviewsFromDB($userType,$productsArray) {
        //$this -> db -> where_in('stores.StoreTypeId', $store_format_id);
        $this -> db -> select('ps.ProductId,p.ProductName,p.Brand');
		$this -> db -> from('products p');
		$this -> db -> join('storeproducts ps', 'p.Id = ps.ProductId');	
		$this -> db -> join('retailers r', 'r.Id = ps.RetailerId');		
		$this -> db -> join('stores s', 's.Id = ps.StoreId');
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1,'ps.IsRemoved' => 0, 'ps.IsActive' => 1));
		
		if($userType==3 || $userType==5){
			 $this -> db -> where(array('ps.RetailerId'=>$this -> session -> userdata('user_retailer_id')));		
		}		
		
		$this -> db -> where_in('ps.ProductId', $productsArray);
		$this -> db -> limit(20,0);
        $query = $this -> db -> get();
        return $query -> result_array();
		
    }
	public function getTrendingProductsSharesFromDB($userType,$monthYr) {
        
        $this -> db -> select('ps.ProductId,COUNT(ps.ProductId) as productCount,p.ProductName,p.Brand');
		$this -> db -> from('products p');
		$this -> db -> join('product_shares ps', 'p.Id = ps.ProductId');
		$this -> db -> join('retailers r', 'r.Id = ps.RetailerId');
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1,'ps.IsRemoved' => 0, 'ps.IsActive' => 1,));		
		if($userType==3 || $userType==5){
			 $this -> db -> where(array('ps.RetailerId'=>$this -> session -> userdata('user_retailer_id')));		
		}	
		$this->db->like('ps.ShareDate',$monthYr.'-');
		$this -> db -> group_by('p.Id');
		$this -> db -> order_by('productCount', 'desc');
		$this -> db -> limit(20,0);
        $query = $this -> db -> get();	
		//	echo $this->db->last_query();
        return $query -> result_array();
    }
	
	
    public function getStoreAdminCount($retailerId,$storeId) {
        $this -> db -> select('COUNT(sa.Id) as totalStoreAdmin');
		$this -> db -> from('storeadmin sa');
		$this -> db -> join('stores s', 's.Id = sa.StoreId');
		$this -> db -> join('retailers r', 'r.Id = s.RetailerId');
		$this -> db -> join('users u', 'u.Id = sa.UserId');
        $this -> db -> where(array('u.IsRemoved' => 0, 'u.IsActive' => 1,'s.StoreId'=>$storeId,'sa.IsRemoved' => 0, 'sa.IsActive' => 1,'s.IsRemoved' => 0, 's.IsActive' => 1));
		
			 $this -> db -> where(array('s.RetailerId'=>$retailerId));
		
		$this -> db -> group_by('s.Id');
        $query = $this -> db -> get();
        return $query -> row_array();
		
    }
	
	
	 public function getStoreUsers($retailerId,$storeId) {

       $this -> db -> select('sum(case when c.Id is null then 0 else 1 end ) AS User_Count', FALSE);
	   $this -> db  -> from('retailers as a');
	   $this -> db  -> join('userpreferredbrands as b', 'a.Id = b.RetailerId', 'left');
	   $this -> db   -> join('users as c', 'c.Id = b.UserId AND c.IsRemoved=0 AND c.IsActive=1 AND (c.UserRole=4 or c.UserRole=0)', 'left');
	   $this -> db  -> where(
			array(
				'a.IsActive' => 1,
				'a.IsRemoved' => 0,
				'b.StoreId' => $storeId								
			)
		);
		
		$this -> db -> where(array('b.RetailerId'=>$retailerId));
		
	   $this -> db  -> group_by('a.Id');
		
        $query = $this -> db -> get();
	//echo $this->db->last_query();
        return $query -> row_array();
    }
	public function getCheckedinUsersByStoreOfRetailer($retailerId,$monthYr='') {
        
        $this -> db -> select('u.FirstName,u.LastName,s.StoreName,s.Building,s.StreetAddress,s.Zip,s.City,us.CheckinTime');
		$this -> db -> from('users u');
		$this -> db -> join('userstorecheckin us', 'u.Id = us.UserId');
		$this -> db -> join('stores s', 's.Id = us.StoreId');
        $this -> db -> where(array('u.IsRemoved' => 0, 'u.IsActive' => 1,'s.RetailerId'=>$retailerId,'s.IsRemoved' => 0, 's.IsActive' => 1));//
		$this->db->like('us.CheckinTime',$monthYr.'-');
        $query = $this -> db -> get();
        return $query -> result_array();
    }
	public function getCheckedinUsersCountByStoreOfRetailer($retailerId) {
        
        $this -> db -> select('count(u.Id) as checkedinusers');
		$this -> db -> from('users u');
		$this -> db -> join('userstorecheckin us', 'u.Id = us.UserId');
		$this -> db -> join('stores s', 's.Id = us.StoreId');
        $this -> db -> where(array('u.IsRemoved' => 0, 'u.IsActive' => 1,'s.RetailerId'=>$retailerId,'s.IsRemoved' => 0, 's.IsActive' => 1));//		
        $query = $this -> db -> get();
        return $query -> row_array();
    }
	public function getSignedUsersCount() {
        
        $this -> db -> select('count(u.Id) as signedusers');
		$this -> db -> from('users u');		
        $this -> db -> where(array('u.IsRemoved' => 0, 'u.IsActive' => 1));		
        $query = $this -> db -> get();
        return $query -> row_array();
    }
	public function getSignupUsers($monthYr='') {
        
        $this -> db -> select('u.FirstName,u.LastName,u.CreatedOn,u.Email');
		$this -> db -> from('users u');		
        $this -> db -> where(array('u.IsRemoved' => 0, 'u.IsActive' => 1));
		$this->db->like('u.CreatedOn',$monthYr.'-');		
        $query = $this -> db -> get();
	//	echo $this->db->last_query();
        return $query -> result_array();
    }
	
	/*
     * Get user count state wise
     */

    public function getUserByRetailer($retailerId) {
        $this -> db -> _protect_identifiers = FALSE;
        $this -> db -> select('COUNT(users.Id) AS User_Count,StateCode,state.Name,state.Id');
        $this -> db -> join('users', 'users.IsRemoved=0 AND (users.State = state.Name OR users.State = state.Id) AND (users.UserRole = 4 OR users.UserRole = 0 )', 'left');		
		$this -> db -> join('userpreferredbrands', 'users.Id = userpreferredbrands.UserId');
		$this -> db ->where(
                array(
                    'users.IsActive' => 1,
                    'users.IsRemoved' => 0, 'userpreferredbrands.RetailerId' => $retailerId
                )
            );
        $this -> db -> group_by('state.StateCode,state.Name');
        $query = $this -> db -> get('state');		
        return $query -> result_array();
    }
	
	public function getProvienceListing() {
        
        $this -> db -> select('Name');
		$this -> db -> from('state');	
        $query = $this -> db -> get();
        return $query -> result_array();
    }
	public function getTotalSignupUsersByMonth($monthYr='') {
        
		$this -> db -> select('count(u.Id) as count');
		$this -> db -> from('users u');		
        $this -> db -> where(array('u.IsRemoved' => 0, 'u.IsActive' => 1));
		$this->db->like('u.CreatedOn',$monthYr);		
       
		
		$query = $this -> db -> get();	
		return $query -> row_array();
    }
	
	public function getTotalSignupUsersByMonthForRetailer($monthYr='') {
        
			$this -> db -> select("count(c.Id) as count", FALSE)
			-> from('retailers as a')
			-> join('userpreferredbrands as b', 'a.Id = b.RetailerId')
			-> join('users as c', 'c.Id = b.UserId AND c.IsRemoved=0 AND c.IsActive=1 AND (c.UserRole=4 or c.UserRole=0)')
			-> where(
				array(
					'a.Id' => $this -> session -> userdata('user_retailer_id')								
				)
			)
			->like('c.CreatedOn',$monthYr)	
			-> group_by('c.Id');
			//echo $this->db->last_query().'------------------------------';
		$query = $this -> db -> get();	
		return $query -> row_array();
    }
	public function getSpecialsBrowseOfRetailer($userType) {
        
        $this -> db -> select('s.Id,s.SpecialName,sb.totalVisit');
		$this -> db -> from('specials s');
		$this -> db -> join('specialBrowse sb', 's.Id = sb.specialId');
		$this -> db -> join('retailers r', 'r.Id = sb.RetailerId');
        $this -> db -> where(array('s.IsRemoved' => 0, 's.IsActive' => 1));
		if($userType==3 || $userType==5){
			$this -> db -> where(array('sb.RetailerId'=>$this -> session -> userdata('user_retailer_id')));
		}
		$this -> db -> group_by('s.Id');
		$this -> db -> order_by('totalVisit', 'desc');
        $query = $this -> db -> get();	
		
        return $query -> result_array();
    }
	
	 public function getTotalProductsByRetailer($retailerId) {
      		
		$this -> db -> select('p.Id as count');
		$this -> db -> from('products p');
		$this -> db -> join('storeproducts sp', 'p.Id = sp.ProductId');
		$this -> db -> join('retailers r', 'r.Id = sp.RetailerId');
        $this -> db -> where(array('sp.RetailerId'=>$retailerId,'sp.IsRemoved' => 0, 'sp.IsActive' => 1,'p.IsRemoved' => 0, 'p.IsActive' => 1));
				
		$this -> db -> group_by('sp.ProductId');
		$query = $this -> db -> get();		
       
        return $query -> num_rows();
        
        return FALSE;
    }
	public function getTotalSpecialsByRetailer($retailerId) {
       	
		$this -> db -> select('s.Id as count');
		$this -> db -> from('specials s');
		$this -> db -> join('special_stores ss', 's.Id = ss.SpecialId');
		$this -> db -> join('retailers r', 'r.Id = ss.RetailerId');
        $this -> db -> where(array('ss.RetailerId'=>$retailerId,'ss.IsRemoved' => 0, 'ss.IsActive' => 1,'s.IsRemoved' => 0, 's.IsActive' => 1));
				
		$this -> db -> group_by('ss.SpecialId');
		$query = $this -> db -> get();		
        
        return $query -> num_rows();
      
        return FALSE;
    }
	public function getTotalStoreFormatsByRetailer($retailerId) {
        $this -> db -> select('count(b.Id) as count', FALSE)
            -> from('retailers as a')
            -> join('storestypes as b', 'b.RetailerId= a.Id')
            -> where(
                array(
                    'b.RetailerId' => $retailerId,
                    'a.IsActive' => 1,
                    'a.IsRemoved' => 0,
                    'b.IsActive' => 1,
                    'b.IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> row_array();
        }
        return FALSE;
    }
	public function getMostVisitedProducts($userType,$monthYr) {
        
        $this -> db -> select('ps.ProductId,COUNT(ps.ProductId) as productCount,p.ProductName,p.Brand');
		$this -> db -> from('products p');
		$this -> db -> join('productviews ps', 'p.Id = ps.ProductId');
		$this -> db -> join('retailers r', 'r.Id = ps.RetailerId');
        $this -> db -> where(array('p.IsRemoved' => 0, 'p.IsActive' => 1));
		if($userType==3 || $userType==5){
			 $this -> db -> where(array('ps.RetailerId'=>$this -> session -> userdata('user_retailer_id')));		
		}		
		$this->db->like('ps.ViewDate',$monthYr.'-');
		$this -> db -> group_by('p.Id');
		$this -> db -> order_by('productCount', 'desc');		
        $query = $this -> db -> get();
        return $query -> result_array();
    }
	public function getUsersByGender($userType) {
        if($userType==3 || $userType==5){
					$this -> db -> select('users.count(ID) as maleusers');
					$this -> db -> from('users');
					$this -> db -> join('userpreferredbrands', 'users.Id = userpreferredbrands.UserId');
					$this -> db ->where(
								array(
										'users.IsActive' => 1,
										'users.IsRemoved' => 0, 'userpreferredbrands.RetailerId' =>$this -> session -> userdata('user_retailer_id'),'users.Gender'=>'M'
								)
					);
					$this -> db -> group_by('users.Id');
					$query = $this -> db -> get();
					$users['maleuser']= $query -> result_array();
					
					$this -> db -> select('users.count(ID) as maleusers');
					$this -> db -> from('users');
					$this -> db -> join('userpreferredbrands', 'users.Id = userpreferredbrands.UserId');
					$this -> db ->where(
								array(
										'users.IsActive' => 1,
										'users.IsRemoved' => 0, 'userpreferredbrands.RetailerId' =>$this -> session -> userdata('user_retailer_id'),'users.Gender'=>'F'
								)
					);
					$this -> db -> group_by('users.Id');

					$query = $this -> db -> get();
					$users['femaleuser']= $query -> result_array();
				}
				else{
					$this -> db -> select('count(ID) as maleusers');
					$this -> db -> from('users');		
					$this -> db -> where(array('IsRemoved' => 0, 'IsActive' => 1,'Gender'=>'M'));		
					$query = $this -> db -> get();
					$users['maleuser']= $query -> result_array();
			
					$this -> db -> select('count(ID) as femaleusers');
					$this -> db -> from('users');		
					$this -> db -> where(array('IsRemoved' => 0, 'IsActive' => 1,'Gender'=>'F'));		
					$query = $this -> db -> get();
					$users['femaleuser']= $query -> result_array();
					return $users;
				}        
    } 
		public function getTotalUsers($userType) {
		
		if($userType==3 || $userType==5){
			$this -> db -> select('users.Id,users.DateOfBirth');
      $this -> db -> from('users');
			$this -> db -> join('userpreferredbrands', 'users.Id = userpreferredbrands.UserId');
			$this -> db ->where(
						array(
								'users.IsActive' => 1,
								'users.IsRemoved' => 0, 'userpreferredbrands.RetailerId' => $this -> session -> userdata('user_retailer_id')
						)
			);
			$this -> db -> group_by('users.Id');
		}
		else{
			$this -> db -> select('u.Id,u.DateOfBirth');
			$this -> db -> from('users u');		
			$this -> db -> where(array('u.IsRemoved' => 0, 'u.IsActive' => 1));
		}
		$query = $this -> db -> get();
		return $query -> result_array();
    }
}
