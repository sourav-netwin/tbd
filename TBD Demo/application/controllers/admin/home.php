<?php

/*
 * Author:PHN
 * Purpose:Admin Controller
 * Date:25-08-2015
 * Dependency: adminmodel.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
 */
class Home extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 25-08-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
        $this -> page_title = "Dashboard";
        $this -> breadcrumbs[] = array();
        $this -> load -> model('admin/adminmodel', '', TRUE);
        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/notificationmodel', '', TRUE);
        $this -> load -> model('admin/loyaltypointmodel', '', TRUE);  
		$this -> load -> model('admin/loyaltyordermodel', '', TRUE);
		$this -> load -> model('admin/specialproductmodel', '', TRUE);
        $this -> check_user_premium();
    }

    /**
     * Load Sign-in View, if user has not logged In
     */
    public function index() {

        if (!$this -> session -> userdata('user_id')) {
            $this -> load -> view('admin/login');
        }
        else {
            redirect('home/dashboard', 'refresh');
        }
    }
    /*
     * Check user login
     */

    public function verifylogin() {
        $this -> form_validation -> set_rules('email', 'email', 'trim|required|xss_clean');
        $this -> form_validation -> set_rules('password', 'password', 'trim|required|xss_clean|callback_check_database');

        if ($this -> form_validation -> run() == FALSE) {
            $this -> load -> view('admin/login');
        }
        else {
            # Delete all user notifications before last month             
            $deletePrevious = $this -> notificationmodel -> delete_previous_month_notifications();
            
            # calculate loyalty points 
            $calResult = $this -> loyaltypointmodel -> calculate_loyalty_for_all_users();
            
            redirect('home', 'refresh');
        }
    }
    /*
     * Check user login in database
     */

    public function check_database($password) {
        $email = $this -> input -> post('email');
        $result = $this -> adminmodel -> login($email, $password);

        if ($result) {
            foreach ($result as $row) {
                $this -> session -> set_userdata('user_id', $row -> Id);
                $this -> session -> set_userdata('user_first_name', $row -> FirstName);
                $this -> session -> set_userdata('user_last_name', $row -> LastName);
                $this -> session -> set_userdata('user_full_name', $row -> FirstName . " " . $row -> LastName);
                $this -> session -> set_userdata('user_email', $row -> Email);
                $this -> session -> set_userdata('user_type', $row -> UserRole);
                $this -> session -> set_userdata('user_image', $row -> ProfileImage);
                $this -> session -> set_userdata('user_role', $row -> Type);
                $this -> session -> set_userdata('user_level', $row -> Level);

                //For retailer user
                if ($row -> UserRole == 3) {
                    $this -> session -> set_userdata('user_retailer_id', $row -> retailerID);
                    $this -> session -> set_userdata('user_company_name', $row -> CompanyName);

                    if ($row -> LogoImage) {
                        $logo = front_url() . RETAILER_IMAGE_PATH . 'small/' . $row -> LogoImage;
                        $this -> session -> set_userdata('user_logo', $logo);
                    }
                }

                //For store format user
                if ($row -> UserRole == 5) {
                    $user_details = $this -> adminmodel -> get_store_format_user_details($row -> Id);

                    $this -> session -> set_userdata('user_retailer_id', $user_details['RetailerId']);
                    $this -> session -> set_userdata('user_store_format_id', $user_details['StoreTypeId']);

                    if ($user_details['Logo']) {
                        $logo = front_url() . STORE_FORMAT_IMAGE_PATH . 'small/' . $user_details['Logo'];
                        $this -> session -> set_userdata('user_logo', $logo);
                    }
                }

                //For store user
                if ($row -> UserRole == 6) {
                    $user_details = $this -> adminmodel -> get_store_user_details($row -> Id);

                    $this -> session -> set_userdata('user_store_id', $user_details['StoreId']);
                    $this -> session -> set_userdata('user_retailer_id', $user_details['RetailerId']);
                    $this -> session -> set_userdata('user_store_format_id', $user_details['StoreTypeId']);


                    if ($user_details['Logo']) {
                        $logo = front_url() . STORE_FORMAT_IMAGE_PATH . 'small/' . $user_details['Logo'];
                        $this -> session -> set_userdata('user_logo', $logo);
                    }
                }
            }

            return TRUE;
        }
        else {
            $this -> form_validation -> set_message('check_database', 'Invalid email or password');
            return FALSE;
        }
    }

    public function dashboard() {

        if ($this -> session -> userdata('user_id')) {
			
			$unReadOrders = $this -> loyaltyordermodel -> getunReadOrders();
			 $this -> session -> set_userdata('unReadOrders', 0);
			 $this -> session -> set_userdata('unReadOrders', $unReadOrders);
			
            $this -> load -> model('front/contentmodel', '', TRUE);
            $data = array();
			
			
			$users=array();	
			
            $data['nav_menus'] = $this -> adminmodel -> load_menu();
            
            $data['title'] = $this -> page_title;
            $data['breadcrumbs'] = $this -> breadcrumbs;
            
			$data['store_formats_count'] = $this -> adminmodel -> store_formats_count($this -> session -> userdata('user_type'));
			$data['special_product'] = $this -> adminmodel -> totalSpecialProduct($this -> session -> userdata('user_type'));
			$mostvisited = $this -> adminmodel -> getMostVisitedProducts($this -> session -> userdata('user_type'),date('Y-m'));
			$data['mostvisited']=$countmostvisited=0;
			if(!empty($mostvisited)){
				foreach($mostvisited as $visited){
					$countmostvisited+=$visited['productCount'];
				}
			}
			$data['mostvisited']=$countmostvisited;
            //If ADMIN
            if ($this -> session -> userdata('user_type') == 1 || $this -> session -> userdata('user_type') == 2) {

                //get the counts for the category
                $data['category'] = $this -> adminmodel -> category_data();

                //get the counts for the product
                $data['product'] = $this -> adminmodel -> product_data();

                //get the counts for the Retailers
                $data['retailers'] = $this -> adminmodel -> retailers_data();

                //get the counts for the stores
                $data['stores'] = $this -> adminmodel -> stores_data();

                //get the counts for the Retailers
                $data['users'] = $this -> adminmodel -> user_data('Users');
				
								// get new users count
								$data['newUsers'] = $this -> adminmodel -> get_users('newUsers',$this -> session -> userdata('user_type'));

                //Get count of retailer-stores
                $data['state_stores_count'] = $this -> adminmodel -> get_state_stores_count();

//                $data['state_stores'] = $this->adminmodel->get_state_stores();

                $this -> load -> model('admin/retailermodel', '', TRUE);
                $data['all_retailers'] = $this -> retailermodel -> get_retailers();

                //Get count of users per state
                $data['state_users'] = $this -> adminmodel -> get_state_users();
				
				//get all active stores
				$data['activeStores'] = $this -> adminmodel -> getAllActiveStores();

                //Get count of retailer users
                $data['retailer_users'] = $this -> adminmodel -> get_users_retailers();

                //get retailer count with state
                $data['retailer_counts'] = $this -> adminmodel -> get_retailers_count();

				 //get special count
                $data['liveSpecials'] = $this -> adminmodel -> getTotalLiveSpecial();
				
                //Get product view count
                $data['retailer_product_view'] = $this -> adminmodel -> get_product_views_retailers();

                $this -> template -> view('admin/dashboard', $data);
            }
			
            //If RETAILER ADMIN
            if ($this -> session -> userdata('user_type') == 3 || $this -> session -> userdata('user_type') == 5) {

                //get the counts for the store formats
                

                //get the counts for the store product
                //$data['store_products'] = $this -> adminmodel -> store_product_data();
                
                $tempResponse['store_products_count']=0;
                $data['store_products'] = $tempResponse;
               
                //get the counts for the stores
                $data['stores'] = $this -> adminmodel -> stores_data();

                if ($this -> session -> userdata('user_type') == 3) {
                    $this -> load -> model('admin/storeformatmodel', '', TRUE);
                    $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($this -> session -> userdata('user_retailer_id'));
                }

                //get the counts for the special products
                
				$data['checkedinusers'] = $this -> adminmodel -> getCheckedinUsersCountByStoreOfRetailer($this -> session -> userdata('user_retailer_id'));
				$data['signedusers'] = $this -> adminmodel -> getSignedUsersCount();
               // $data['state_stores_count'] = $this -> adminmodel -> get_state_stores_count($this -> session -> userdata('user_retailer_id'));
			   
			   $data['state_users'] = $this -> adminmodel -> getUserByRetailer($this -> session -> userdata('user_retailer_id'));
				
                $this -> template -> view('admin/retailer_dashboard', $data);
            }


            //If STORE USER
            if ($this -> session -> userdata('user_type') == 6) {

                //Redirect Store User according to steps
                $this -> load -> model('admin/storemodel', '', TRUE);
                $navigation_details = $this -> storemodel -> get_wizard_steps();

                if ($navigation_details['Step1'] == 0) {
                    $this -> template -> view('admin/welcome_screen', $data);
                }
                elseif ($navigation_details['Step2'] == 0) {
                    redirect('home/store/new', 'refresh');
                }
                elseif ($navigation_details['Step3'] == 0) {
                    redirect('storeproducts/product_catalogue', 'refresh');
                }
                else {
                    $this -> template -> view('admin/welcome', $data);
                }
            }
        }
        else {
            redirect(base_url() . 'admin', 'refresh');
        }
    }
    /*
     * Logout the login user with deleting all its session.
     */

    public function logout() {
        $this -> session -> unset_userdata('user_id');
        $this -> session -> unset_userdata('user_first_name');
        $this -> session -> unset_userdata('user_last_name');
        $this -> session -> unset_userdata('user_email');
        $this -> session -> sess_destroy();

        redirect('home', 'refresh');
    }
    /*
     * Forgot Password
     */

    public function forgot_password() {

        if (!$this -> session -> userdata('user_id')) {
            $this -> load -> view('admin/forgot_password');
        }
        else {
            redirect('home/dashboard', 'refresh');
        }
    }
    /*
     * Send reset password link to user email address
     */

    public function send_password() {
        $this -> form_validation -> set_rules('email', 'email', 'trim|required|xss_clean|callback_check_email');
        $res = $this -> form_validation -> run();

        if ($res == FALSE) {

            $this -> load -> view('admin/forgot_password');
        }
        else {
            $email_get = $this -> input -> post('email');

            //Set token for the user
            $token = $this -> adminmodel -> set_password_reset_token($this -> session -> userdata('reset_user_id'));

            $reset_password_link = base_url() . 'admin/reset_password?tkn=' . $token;

            $this -> load -> model('admin/emailtemplatemodel');

            $email_template_details = $this -> emailtemplatemodel -> get_email_template_details(1);

            $emailBody = $email_template_details['Content'];
            $emailBody = str_replace("{LINK}", $reset_password_link, $emailBody);

            //---- LOAD EMAIL LIBRARY ----//
            $this -> load -> library('email');
            $config['mailtype'] = 'html';
            $this -> email -> initialize($config);

            $this -> email -> from($email_template_details['FromEmail']);
            $this -> email -> to($email_get);
            $this -> email -> subject("The Best Deals: Password Reset");

            $this -> email -> message($emailBody);
            $this -> email -> send();

            $data['message'] = "The password reset link send to your email address";

            $this -> load -> view('admin/forgot_password', $data);
        }
    }
    /*
     * Check if user address exists
     */

    public function check_email() {
        $email = $this -> input -> post('email');
        $result = $this -> adminmodel -> check_email($email);

        if ($result) {
            foreach ($result as $row) {
                $this -> session -> set_userdata('reset_user_id', $row -> Id);
                $this -> session -> set_userdata('reset_user_first_name', $row -> FirstName);
                $this -> session -> set_userdata('reset_user_email', $row -> Email);
            }
            return TRUE;
        }
        else {
            $this -> form_validation -> set_message('check_email', 'Email Address does not exists');
            return FALSE;
        }
    }

    public function reset_password() {
        $data = array();
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            $this -> form_validation -> set_rules('password', 'password', 'trim|required|matches[confirm_password]');
            $this -> form_validation -> set_rules('confirm_password', 'confirm password', 'trim|required');
            $res = $this -> form_validation -> run();
            $token = $this -> input -> post('tkn');

            if ($res == TRUE && $token != "") {

                $new_password = $this -> input -> post('password');

                $result = $this -> adminmodel -> reset_password($token, $new_password);

                if ($result) {
                    $data['message'] = "Password changed successfully";
                }
                else {
                    $data['message'] = "The password link is expired";
                }
            }
        }
        else {
            if (!$this -> input -> get('tkn')) {
                redirect('home/dashboard', 'refresh');
            }
        }


        $this -> load -> view('admin/reset_password', $data);
    }

    public function store($step = "") {

        $id = $this -> session -> userdata('user_store_id');

        $this -> load -> model('admin/storemodel', '', TRUE);

        $this -> load -> model('admin/statemodel', '', TRUE);

        //Update step one completed for a user.
        $step_data = array('Step1' => '1');

        $this -> storemodel -> update_wizard_step($step_data);

        if ($step == 'next') {
            //Update step one completed for a user.
            $step_data = array('Step2' => '1');

            $this -> storemodel -> update_wizard_step($step_data);

            redirect('storeproducts/product_catalogue', 'refresh');
        }

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Edit Retailer

            $this -> form_validation -> set_rules('street_address', 'street address', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('zip', 'zip code', 'trim|required|numeric|xss_clean');
            $this -> form_validation -> set_rules('city', 'city', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('state', 'state', 'trim|required|xss_clean');


            if (!$this -> form_validation -> run() == FALSE) {
                $edit_data = array(
                    'StoreId' => $this -> input -> post('store_id'),
                    'StoreName' => $this -> input -> post('store_name'),
                    'StreetAddress' => $this -> input -> post('street_address'),
                    'Zip' => $this -> input -> post('zip'),
                    'City' => $this -> input -> post('city'),
                    'StateId' => $this -> input -> post('state'),
                    'CountryId' => 1,
                    'Latitude' => $this -> input -> post('latitude'),
                    'Longitude' => $this -> input -> post('longitude'),
                    'ContactPerson' => $this -> input -> post('store_contact_person'),
                    'ContactPersonNumber' => $this -> input -> post('store_contact_tel'),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s'),
                    'IsActive' => 1);

                $result = $this -> storemodel -> update_store($id, $edit_data);

                $insert_data = array('Opendays' => $this -> input -> post('open_days'),
                    'OpenHours' => $this -> input -> post('open_hours'));

                //Save Store Timimg
                $this -> storemodel -> save_store_timing($id, $insert_data);

                $this -> session -> set_userdata('success_message', "Store updated successfully");
                //Update step one completed for a user.
                $step_data = array('Step2' => '1');

                $this -> storemodel -> update_wizard_step($step_data);
                if ($step == 'new') {
                    redirect('storeproducts/product_catalogue', 'refresh');
                }
                else {
                    redirect('home/store', 'refresh');
                }
            }
        }



        $data = $this -> storemodel -> get_store_details($id);

        $data['store_timings'] = $this -> storemodel -> get_store_timing($id);

        $data['title'] = 'Store details';

        $data['step'] = $step;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['states'] = $this -> statemodel -> get_states();
        $data['store_email'] = $this -> session -> userdata('user_email');

        $this -> template -> view('admin/store_wizard/validate_store', $data);
    }
    /*
     * Get Stores as per states for the selected retailer.
     */

    public function stores_states($retailer_id = 0, $storeformat_id = 0) {
        $stores = $this -> adminmodel -> get_state_stores_count($retailer_id, $storeformat_id);

        $result_stores = array();
        $result_stores_table = "";
        $i = 0;
        foreach ($stores as $user):
            $result_stores[$i]["key"] = $user['StateCode'];
            $result_stores[$i]["value"] = $user['Store_Count'];


            $result_stores_table .='<a href="javascript:void(0)" class="state_count" name="state_count" data-code="' . $user['StateCode'] . '" data-name="' . $user['Name'] . '">';
            $result_stores_table .='<div class="map_div">';
			$result_stores_table .='<span class="text"> ' . $user['Name'] . '</span>';
            $result_stores_table .='<span class="number"> ' . $user['Store_Count'] . '</span><br/>';
            
            $result_stores_table .='</div></a>';

            $i++;
        endforeach;

        echo json_encode(array('result_stores' => $result_stores, 'result_stores_table' => $result_stores_table));
    }
	
	
    public function get_retailer_stores($state_code, $retailer_id = 0, $store_format_id = 0) {

        $stores = $this -> adminmodel -> get_state_stores($state_code, $retailer_id, $store_format_id);

        $result_stores = "";
	//echo $this -> session -> userdata('user_type');
        if (!empty($stores)) {
            foreach ($stores as $store):
				$promos=$this -> adminmodel -> getTotalPromosByStore($store['StoreId']);
                $result_stores .= "<tr><td>" . $store['StoreName'] . "</td>";
                $result_stores .= "<td><span class=''>" . $store['Product_Count'] . "</span></td>";			
				$result_stores .= "<td>" . $store['CompanyName'] . "</td>"; 
				$result_stores .= "<td>" . $store['StoreType'] . "</td>";
				$result_stores .= "<td><a href='javascript:void(0);' data-href='".base_url()."home/getSpecialsByStoreId/".$store['StoreId']."' StoreId='".$store['StoreId']."' class='showpromodetails '>" . $promos['totalSpecial']. "</a></td></tr>";
		
            endforeach;
        } else {
            $result_stores .="";
        }
        $retailers = $this -> adminmodel -> get_retailers();
        $retailer_html = '';
        if ($retailers) {
            foreach ($retailers as $retailer) {
                $retailer_html .= '<option value="' . $retailer['CompanyName'] . '">' . $retailer['CompanyName'] . '</option>';
            }
        }
        else {
            $retailer_html .= '<option value="">Select Retailer</option>';
        }
        echo json_encode(array(
            'html' => $result_stores,
            'retailers' => $retailer_html,
			'userType'=>$this -> session -> userdata('user_type')
        ));
    }

    public function get_products_view($retailer_name) {
        $products = $this -> adminmodel -> get_products_viewed($retailer_name);

        $result_products = "";

        if (!empty($products)) {
            foreach ($products as $product):
                $result_products .= "<tr><td>" . $product['ProductName'] . "</td>";
                $result_products .= "<td><span class=''>" . $product['Products_view_Count'] . "</span></td></tr>";
            endforeach;
        } else {
            $result_products .="";
        }
        echo json_encode($result_products);
    }

    public function get_retailer_users($state_id = '', $state_name = '') {
        if ($state_id && urldecode($state_name)) {
            $user_details = $this -> adminmodel -> get_state_user_list($state_id, urldecode(str_replace('-', ' ', $state_name)));
            if ($user_details) {
                $html = '';
                foreach ($user_details as $user) {
                    $html .= '<tr>
                        <td>' . $user['UserName'] . '</td>
                        <td>' . $user['Email'] . '</td>
                        <td>' . $user['Mobile'] . '</td>
                        <td>' . $user['TelephoneFixed'] . '</td>
                        <td>' . $user['DOB'] . '</td>
                        <td>' . $user['Gender'] . '</td>
                        <td>' . $user['address'] . '</td>
                        </tr>';
                }
                echo json_encode($html);
            }
            else {
                echo json_encode('');
            }
        }
        else {
            echo json_encode('');
        }
    }

    public function get_retailer_users_have($retailer_id = '') {
        if ($retailer_id) {
            $user_details = $this -> adminmodel -> get_retailer_user_list($retailer_id);
            if ($user_details) {
                $html = '';
                foreach ($user_details as $user) {
                    $html .= '<tr>
                        <td>' . $user['UserName'] . '</td>
                        <td>' . $user['Email'] . '</td>
                        <td>' . $user['Mobile'] . '</td>
                        <td>' . $user['TelephoneFixed'] . '</td>
                        <td>' . $user['DOB'] . '</td>
                        <td>' . $user['Gender'] . '</td>
                        <td>' . $user['address'] . '</td>
                        </tr>';
                }
                echo json_encode($html);
            }
            else {
                echo json_encode('');
            }
        }
        else {
            echo json_encode('');
        }
    }

    public function get_store_types() {
        $retailer_id = sanitize($this -> input -> post('retailer'));
        if ($retailer_id) {
            $store_types = $this -> adminmodel -> get_storetype($retailer_id);
            if ($store_types) {
                $html = '<option value="">Select Store Type</option>';
                foreach ($store_types as $store_type) {
                    $html .= '<option value="' . $store_type['StoreType'] . '">' . $store_type['StoreType'] . '</option>';
                }
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 1;
                $this -> message = '<option value="">Select Store Type</option>';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'No records found';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_user_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_store_count = $this -> adminmodel -> get_retailer_store_count();
            $retailer_consumer_count = $this -> adminmodel -> get_retailer_consumer_count();
            $retailer_admin_count = $this -> adminmodel -> get_retailer_admin_count();
            $retailer_product_count = $this -> adminmodel -> get_retailer_product_count();

            $retailer_store_count_array = [];
            $retailer_consumer_count_array = [];
            $retailer_admin_count_array = [];
            $retailer_product_count_array = [];

            if ($retailer_store_count) {
                foreach ($retailer_store_count as $retailer_store) {
                    $retailer_store_count_array[] = array(
                        'name' => $retailer_store['CompanyName'],
                        'y' => $retailer_store['count'],
                        'id' => $retailer_store['Id']
                    );
                }
            }
            else {
                $retailer_store_count_array = [];
            }

            if ($retailer_consumer_count) {
                foreach ($retailer_consumer_count as $retailer_consumer) {
                    $retailer_consumer_count_array[] = array(
                        'name' => $retailer_consumer['CompanyName'],
                        'y' => $retailer_consumer['count']
                    );
                }
            }
            else {
                $retailer_consumer_count_array = [];
            }
            if ($retailer_admin_count) {
                foreach ($retailer_admin_count as $retailer_admin) {
                    $retailer_admin_count_array[] = array(
                        'name' => $retailer_admin['CompanyName'],
                        'y' => $retailer_admin['count']
                    );
                }
            }
            else {
                $retailer_admin_count_array = [];
            }
            if ($retailer_product_count) {
                foreach ($retailer_product_count as $retailer_product) {
                    $retailer_product_count_array[] = array(
                        'name' => $retailer_product['CompanyName'],
                        'y' => $retailer_product['count']
                    );
                }
            }
            else {
                $retailer_product_count_array = [];
            }
            $this -> message['retailer_store_count'] = $retailer_store_count_array;
            $this -> message['retailer_consumer_count'] = $retailer_consumer_count_array;
            $this -> message['retailer_admin_count'] = $retailer_admin_count_array;
            $this -> message['retailer_product_count'] = $retailer_product_count_array;
            $this -> result = 1;
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_store_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $store_count_by_region = $this -> adminmodel -> get_store_count_by_region();
            $store_count_by_format = $this -> adminmodel -> get_format_count_by_region();

            $store_state_count_array = [];
            $store_format_count_array = [];

            if ($store_count_by_region) {
                foreach ($store_count_by_region as $store_state) {
                    $store_state_count_array[] = array(
                        'label' => $store_state['state_name'],
                        'y' => $store_state['count']
                    );
                }
            }
            else {
                $store_state_count_array = [];
            }
            if ($store_count_by_format) {
                foreach ($store_count_by_format as $store_format) {
                    $store_format_count_array[] = array(
                        'label' => $store_format['state_name'],
                        'y' => $store_format['count']
                    );
                }
            }
            else {
                $store_format_count_array = [];
            }
            $this -> message['store_state_count'] = $store_state_count_array;
            $this -> message['store_format_count'] = $store_format_count_array;
            $this -> result = 1;
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_storetype_store_count() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_id = $this -> input -> post('retailer_id');
            $store_details = $this -> adminmodel -> get_storetype_store_count($retailer_id);

            $store_format_count_array = [];

            if ($store_details) {
                foreach ($store_details as $store) {
                    $store_format_count_array[] = array(
                        'name' => $store['StoreType'],
                        'y' => $store['count'],
                        'id' => $store['Id']
                    );
                }
            }
            else {
                $store_format_count_array = [];
            }
            $this -> message['store_format_count'] = $store_format_count_array;
            $this -> result = 1;
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_retailer_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_full_details = $this -> adminmodel -> get_all_retailer_store_format_store_count();
            $retailer_full_array = [];
            if ($retailer_full_details) {
                foreach ($retailer_full_details as $retailer) {
                    
                    if($retailer['LogoImage'])
                    {
                        $retailerLogoImage = (front_url() . RETAILER_IMAGE_PATH."medium/" . $retailer['LogoImage']);
                    }else{
                        $retailerLogoImage = "";
                    }
                    
            
                    //$retailerLogoImage = $retailer['LogoImage'];
                    
                    $retailer_full_array[$retailer['retailer_id'] . '::' . $retailer['CompanyName']][] = array(
                        'label' => $retailer['StoreType'],
                        'y' => $retailer['count'],
                        'id' => $retailer['store_type_id'],
                        'retailer' => $retailer['retailer_id'],
                        'retailer_name' => $retailer['CompanyName'],
                        'retailer_logo_image' => $retailerLogoImage
                    );
                }
                $this -> result = 1;
                $this -> message = $retailer_full_array;
            }
            else {
                $this -> result = 1;
                $this -> message = [];
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_store_special_count() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_id = $this -> input -> post('retailer');
            $store_type_id = $this -> input -> post('store_type');
            $special_counts = $this -> adminmodel -> get_store_special_count($retailer_id, $store_type_id);
            $special_count_array = [];
            if ($special_counts) {
                foreach ($special_counts as $special) {
                    $special_count_array[] = array(
                        'label' => $special['StoreName'],
                        'y' => $special['count'],
                        'id' => $special['Id'],
                        'retailer' => $retailer_id,
                        'store_type' => $store_type_id
                    );
                }
                $this -> result = 1;
                $this -> message['specials_count'] = $special_count_array;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Specials Found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_users_count_expansion() {
        $back_user_count_details = $this -> adminmodel -> get_back_user_total_count();
        if ($back_user_count_details) {
            print_r($back_user_count_details);
            die;
        }
        die;
    }

    public function get_consumers_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $back_user_count_details = $this -> adminmodel -> get_consumer_total_count();
            $region_user_count_details = $this -> adminmodel -> get_region_consumer_count();
            $gender_user_count_details = $this -> adminmodel -> get_gender_consumer_count();
            $device_count_array = $this -> adminmodel -> get_user_device_count();
            $special_count_array = [];
            $region_count_array = [];
            $gender_count_array = [];
            if ($back_user_count_details) {
                foreach ($back_user_count_details as $user) {
                    $special_count_array[] = array(
                        'label' => $user['CompanyName'],
                        'y' => $user['count'],
                        'id' => $user['retailer_id']
                    );
                }
            }
            if ($region_user_count_details) {
                foreach ($region_user_count_details as $region) {
                    $region_count_array[] = array(
                        'label' => $region['state_name'],
                        'y' => $region['count'],
                        'id' => $region['Id']
                    );
                }
            }
            if ($gender_user_count_details) {
                foreach ($gender_user_count_details as $gender) {
                    $gender_count_array[] = array(
                        'label' => $gender['gender_exp'],
                        'y' => $gender['count'],
                        'id' => $gender['Gender']
                    );
                }
            }
            $this -> result = 1;
            $this -> message['users_count'] = $special_count_array;
            $this -> message['region_users_count'] = $region_count_array;
            $this -> message['gender_users_count'] = $gender_count_array;
            $this -> message['device_users_count'] = $device_count_array;
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }

        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
    /* public function get_products_count_expansion() {
      if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
      $product_count_details = $this -> adminmodel -> get_category_product_count();
      $product_count_array = [];
      if ($product_count_details) {
      foreach ($product_count_details as $product) {
      $product_count_array[] = array(
      'label' => $product['CategoryName'],
      'y' => $product['count'],
      'id' => $product['Id']
      );
      }
      $this -> result = 1;
      $this -> message['prduct_count'] = $product_count_array;
      }
      else {
      $this -> result = 1;
      $this -> message = [];
      }
      }
      else {
      $this -> result = 0;
      $this -> message = 'Invalid data';
      }

      echo json_encode(array(
      'result' => $this -> result,
      'message' => $this -> message
      ));
      } */

    public function get_category_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $product_count_details = $this -> adminmodel -> get_category_product_count();
            $product_count_array = [];
            if ($product_count_details) {
                foreach ($product_count_details as $product) {
                    $product_count_array[] = array(
                        'label' => $product['CategoryName'],
                        'y' => $product['count'],
                        'id' => $product['Id']
                    );
                }
                $this -> result = 1;
                $this -> message['prduct_count'] = $product_count_array;
            }
            else {
                $this -> result = 1;
                $this -> message = [];
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }

        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_special_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer = sanitize($this -> input -> post('retailer'));
            $store_type = sanitize($this -> input -> post('store_type'));
            $store = sanitize($this -> input -> post('store'));

            $special_expansion_details = $this -> adminmodel -> get_store_special_count_expansion($retailer, $store_type, $store);
            if ($special_expansion_details) {
                $html = '<table class="table table-bordered" id="product_special_expansion_table">
                    <thead>
                    <tr>
                        <td>Product</td>
                        <td>Main Category</td>
                        <td>Parent Category</td>
                        <td>Category</td>
                        <td>Special Price</td>
                        <td>Special Name</td>
                        <td>From Date</td>
                        <td>To Date</td>
                    </tr>
                    </thead><tbody>';
                foreach ($special_expansion_details as $special) {
                    $html .= '<tr>
                        <td>' . $special['ProductName'] . '</td>
                        <td>' . $special['main_cat'] . '</td>
                        <td>' . $special['parent_cat'] . '</td>
                        <td>' . $special['cat'] . '</td>
                        <td>' . $special['SpecialPrice'] . '(' . $special['SpecialQty'] . ')</td>
                        <td>' . $special['SpecialName'] . '</td>
                        <td>' . $special['SpecialFrom'] . '</td>
                        <td>' . $special['SpecialTo'] . '</td>
                        </tr>';
                }

                $html .= '</tbody></table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Specials Found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_consumer_retailer_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer = sanitize($this -> input -> post('retailer'));
            $consumer_retailer_details = $this -> adminmodel -> get_consumer_retailer_expansion($retailer);
            if ($consumer_retailer_details) {
                $html = '<table class="table table-bordered" id="consumer_retailer_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Telephone</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                        </tr>
                    </thead><tbody>';
                foreach ($consumer_retailer_details as $retailer) {
                    $html .= '<tr>
                                <td>' . $retailer['UserName'] . '</td>
                                <td>' . $retailer['Email'] . '</td>
                                <td>' . $retailer['Mobile'] . '</td>
                                <td>' . $retailer['TelephoneFixed'] . '</td>
                                <td>' . $retailer['DOB'] . '</td>
                                <td>' . $retailer['Gender'] . '</td>
                              </tr>';
                }
                $html .= '</tbody></table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No users found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_consumer_region_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $state = sanitize($this -> input -> post('state'));
            $consumer_retailer_details = $this -> adminmodel -> get_consumer_region_expansion($state);
            if ($consumer_retailer_details) {
                $html = '<table class="table table-bordered" id="consumer_region_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Telephone</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                        </tr>
                    </thead><tbody>';
                foreach ($consumer_retailer_details as $retailer) {
                    $html .= '<tr>
                                <td>' . $retailer['UserName'] . '</td>
                                <td>' . $retailer['Email'] . '</td>
                                <td>' . $retailer['Mobile'] . '</td>
                                <td>' . $retailer['TelephoneFixed'] . '</td>
                                <td>' . $retailer['DOB'] . '</td>
                                <td>' . $retailer['Gender'] . '</td>
                              </tr>';
                }
                $html .= '</tbody></table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No users found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_consumer_gender_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $gender = sanitize($this -> input -> post('gender'));
            $consumer_retailer_details = $this -> adminmodel -> get_consumer_gender_expansion($gender);
            if ($consumer_retailer_details) {
                $html = '<table class="table table-bordered" id="consumer_gender_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Telephone</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                        </tr>
                    </thead><tbody>';
                foreach ($consumer_retailer_details as $retailer) {
                    $html .= '<tr>
                                <td>' . $retailer['UserName'] . '</td>
                                <td>' . $retailer['Email'] . '</td>
                                <td>' . $retailer['Mobile'] . '</td>
                                <td>' . $retailer['TelephoneFixed'] . '</td>
                                <td>' . $retailer['DOB'] . '</td>
                                <td>' . $retailer['Gender'] . '</td>
                              </tr>';
                }
                $html .= '</tbody></table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No users found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_consumer_device_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $device = sanitize($this -> input -> post('device'));
            if ($device == 'A' || $device == 'I') {
                $consumer_retailer_details = $this -> adminmodel -> get_consumer_device_expansion($device);
            }
            else {
                $consumer_retailer_details = $this -> adminmodel -> get_consumer_web_expansion();
            }

            if ($consumer_retailer_details) {
                $html = '<table class="table table-bordered" id="consumer_device_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Telephone</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                        </tr>
                    </thead><tbody>';
                foreach ($consumer_retailer_details as $retailer) {
                    $html .= '<tr>
                                <td>' . $retailer['UserName'] . '</td>
                                <td>' . $retailer['Email'] . '</td>
                                <td>' . $retailer['Mobile'] . '</td>
                                <td>' . $retailer['TelephoneFixed'] . '</td>
                                <td>' . $retailer['DOB'] . '</td>
                                <td>' . $retailer['Gender'] . '</td>
                              </tr>';
                }
                $html .= '</tbody></table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No users found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_products_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $category_sub_total_list = $this -> adminmodel -> get_category_sub_total();
            if ($category_sub_total_list) {
                $category_array = [];
                foreach ($category_sub_total_list as $category) {
                    $category_array[$category['main_cat_id'] . '::' . $category['main_cat']][] = array(
                        'label' => $category['parent_cat'],
                        'y' => $category['count'],
                        'id' => $category['parent_cat_id'],
                        'main_cat' => $category['main_cat_id']
                    );
                }
                $this -> result = 1;
                $this -> message = $category_array;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Details Found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_category_sub_count_expansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $main_cat = sanitize($this -> input -> post('main_cat'));
            $parent_cat = sanitize($this -> input -> post('parent_cat'));
            $product_details = $this -> adminmodel -> get_product_expansion_details($main_cat, $parent_cat);
            if ($product_details) {
                $html = '<table class="table table-bordered" id="cat_sub_table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Price</th>
                        </tr>
                    </thead><tbody>';
                foreach ($product_details as $product) {
                    $html .= '<tr>
                        <td>' . $product['ProductName'] . '</td>
                        <td>' . $product['CategoryName'] . '</td>
                        <td>' . $product['Brand'] . '</td>
                        <td>' . $product['RRP'] . '</td>
                        </tr>';
                }
                $html .= '</tbody></table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Products Found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_retailer_each_exapansion() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_id = $this -> input -> post('retailer');
            $retailer_storetypes = $this -> adminmodel -> get_retaielr_storetypes($retailer_id);

            $retailer_storetype_array = [];

            if ($retailer_storetypes) {
                foreach ($retailer_storetypes as $storetype) {
                    $retailer_storetype_array[$storetype['storetype_id'] . '::' . $storetype['StoreType']][] = array(
                        'name' => $storetype['StoreType'],
                        'y' => $storetype['count'],
                        'retailer' => $storetype['retailer_id'],
                        'storetype' => $storetype['storetype_id'],
                    );
                }
                $this -> result = 1;
                $this -> message['storetypes'] = $retailer_storetype_array;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Details Found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function show_retailer_storetype_count_table() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $storetype_id = sanitize($this -> input -> post('store_type'));
            $retailer_id = $this -> adminmodel -> get_retailer_id_from_storetype($storetype_id);
            $stores_list = $this -> adminmodel -> get_retailer_storetype_stores($retailer_id, $storetype_id);
            if ($stores_list) {
                $html = '<table class="table table-bordered" id="retailer_stst_table">
                    <thead>
                        <tr>
                            <th>Store Name</th>
                            <th>Store ID</th>
                            <th>Contact</th>
                            <th>Address</th>
                        </tr>
                    </thead><tbody>';
                foreach ($stores_list as $store) {
                    $html .= '<tr>
                        <td><a href="javascript:void(0)" class="store_exp_modal" data-id="' . $store['Id'] . '">' . $store['StoreName'] . '</a></td>
                        <td>' . $store['StoreId'] . '</td>
                        <td>' . $store['Contact'] . '</td>
                        <td>' . $store['Address'] . '</td>
                        </tr>';
                }
                $html .= '</tbody><table>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No Data Found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_store_modal_graphs() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $store_id = sanitize($this -> input -> post('store'));

            $consumer_count = [];
            $user_count = [];
            $product_count = [];
            $special_count = [];

            $store_consumer_count = $this -> adminmodel -> get_store_consumer_count($store_id);
            $store_user_count = $this -> adminmodel -> get_store_user_count($store_id);
            $store_product_count = $this -> adminmodel -> get_store_product_count($store_id);
            $store_special_count = $this -> adminmodel -> get_store_one_special_count($store_id);
            $consumer_count[] = array(
                'name' => $store_consumer_count['StoreName'],
                'y' => $store_consumer_count['count'],
                'id' => $store_consumer_count['Id']
            );
            $user_count[] = array(
                'name' => $store_user_count['StoreName'],
                'y' => $store_user_count['count'],
                'id' => $store_user_count['Id']
            );
            $product_count[] = array(
                'name' => $store_product_count['StoreName'],
                'y' => $store_product_count['count'],
                'id' => $store_product_count['Id']
            );
            $special_count[] = array(
                'name' => $store_special_count['StoreName'],
                'y' => $store_special_count['count'],
                'id' => $store_special_count['Id']
            );
            $this -> result = 1;
            $this -> message['consumer_count'] = $consumer_count;
            $this -> message['user_count'] = $user_count;
            $this -> message['product_count'] = $product_count;
            $this -> message['special_count'] = $special_count;
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
    
    public function check_user_premium(){
        if($this -> session -> userdata('user_type') == 6){
            $store_promotion_details = $this -> storemodel -> get_store_promos($this -> session -> userdata('user_store_id'));
            if($store_promotion_details['Premium'] == 1){
                $this -> session -> set_userdata('store_promotion_active', 'true');
            }
        }
    }
    
    // Function to get the Store Products Counts
    public function get_store_products_counts(){
        # Get store products data
        $data = $this -> adminmodel -> totalProductsCount($this -> session -> userdata('user_type') );
        
		 
        $this -> result = 1;
        $this -> message['store_products_counts'] = $data['store_products_count'];
       
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
			
        ));
    }
	
	public function getStoresListingByFormat($store_type) {    

		$storesListing = $this -> adminmodel -> getStoresByFormatAndRetailer($store_type,$this -> session -> userdata('user_type') );
		$retun=$this->getStoreList($storesListing,'storeFormat');
		echo json_encode($retun);
    }
	public function getStoresListingByProvience($provienceId) {    

		$storesListing = $this -> adminmodel -> getStoresByprovienceId($provienceId);
		$retun=$this->getStoreList($storesListing,'provience' );
		echo json_encode($retun);
    }
	public function getAllActiveStores() {    

		$storesListing = $this -> adminmodel -> getAllActiveStores();
		$retun=$this->getStoreList($storesListing,'allActiveStores' );
		echo json_encode($retun);
    }
	public function getStoreList($storesListing,$parameter){
		$result = 0;
		$html = 'No Stores Found';$StoreType='';$data=array();
		
		$provienceListing = $this -> adminmodel -> getProvienceListing();
		$provinceHtml='';
		if(!empty($provienceListing)){
			foreach($provienceListing as $p)
			$provinceHtml.='<option value="'.$p['Name'].'">'.$p['Name'].'</option>';
		}
		
		$all_retailers = $this ->adminmodel -> get_retailers();
		
		$RetailerHtml='';
		if(!empty($all_retailers)){
			foreach($all_retailers as $retailer)
			$RetailerHtml.='<option value="'.$retailer['CompanyName'].'">'.$retailer['CompanyName'].'</option>';
		}

		if ($storesListing) {
			$html = '';
			foreach ($storesListing as $store) {
			   $promos=$this -> adminmodel -> getTotalPromosByStore($store['StoreId']);
			   $getStoreAdminCount=$this -> adminmodel -> getStoreAdminCount($store['RetailerId'],$store['StoreId']);			  

			   if($getStoreAdminCount)
				 $totalStoreAdmin=  $getStoreAdminCount['totalStoreAdmin'];
			   else $totalStoreAdmin=0;

			   $getStoreUsers=$this -> adminmodel -> getStoreUsers($store['RetailerId'],$store['Id']);
			   if($getStoreUsers)
				 $User_Count=  $getStoreUsers['User_Count'];
			   else $User_Count=0;


				$html .= "<tr class='storelistrow'>";
				$html .= "<td>" . $store['StoreName'] . "</td>";
				if($parameter=='storeFormat')
					$html .= "<td width='20%'> " . $store['Name']. "</td>";
				else
					$html .= "<td width='20%'> " . $store['CompanyName']. "</td>";
				$html .= "<td width='10%' style='text-align:center;'><span class=''>" . $this -> adminmodel -> getProductsByStoreId($store['RetailerId'],$store['Id'])['totalProducts'] . "</span></td>";	
				
				$html .= "<td width='10%' style='text-align:center;'><a href='javascript:void(0);' data-href='".base_url()."home/getSpecialsByStoreId/".$store['StoreId']."' StoreId='".$store['StoreId']."' class='showpromodetails '>" . $promos['totalSpecial']. "</a></td>";
				$html .= "<td width='10%' style='text-align:center;'>  <span class=''>" . $totalStoreAdmin. "</span></td>";
				$html .= "<td width='10%' style='text-align:center;'> <span class=''>" . $User_Count. "</span></td>";
				
				$html .= "</tr>";
				$StoreType=$store['StoreType'];
			}		   
			$result= 1;			
		}
		if($this -> session -> userdata('user_type') ==1 || $this -> session -> userdata('user_type') ==2){
			$data['htmll']=$html;
			$html = $this -> load -> view('admin/storesbyformat', $data, true);
		}
		return array(
            'result' => $result,
            'html' => $html,
			'StoreType'=>$StoreType,
			'provinceHtml'=>$provinceHtml,
			'RetailerHtml'=>$RetailerHtml
        );
       
	}
	
	public function getSpecialsByStoreId($storeId)
	{ 
		if (strpos($storeId, '%20') !== false)
			$storeId=str_replace('%20',' ',$storeId);
		$storeDetails=$this -> adminmodel -> getStoreNameById($storeId);
		$specialListing=$this -> adminmodel -> get_promo_list_by_storeId($storeId);
		$result_specials = "";
		if (!empty($specialListing)) {
			$i=1;
            foreach ($specialListing as $special):	
				if($i%2==0) $rowClass="even";else $rowClass='odd';
				$userType = $this -> session -> userdata('user_type');
				$user_retailer_id = $this -> session -> userdata('user_retailer_id');
				$user_store_format_id = $this -> session -> userdata('user_store_format_id');
				$user_store_id = $this -> session -> userdata('user_store_id');
				
				$store_count = $this->specialproductmodel->get_special_store_count($special['Id'],$userType,$user_retailer_id,$user_store_format_id,$user_store_id);
		
                $result_specials .= "<tr class='".$rowClass."'><td><a target='_blank' href='".base_url()."specialmanagement/managespecials/".$special['Id']."'>" . $special['SpecialName'] . "</a></td>";
                $result_specials .= "<td><span>" . date('d/m/Y',strtotime($special['SpecialFrom'] )). "</span></td>";				
				$result_specials .= "<td><span>" . date('d/m/Y',strtotime($special['SpecialTo'] )). "</span></td>";			
                $result_specials .= "<td class='specialproducts'>".$store_count."</td></tr>";
				$i++;
            endforeach;
        } else {
            $result_specials .="";
        }
		$data['result_specials']=$result_specials;
		$result_specials_data = $this -> load -> view('admin/specialsbystoreid', $data, true);
		if(!empty($storeDetails)){
			$StoreName=$storeDetails['StoreName'];
		}
		else $StoreName='';
		echo json_encode (array('storeName' => $StoreName, 'result_specials_data' => $result_specials_data));
	}
	public function getAllLiveSpecials()
	{ 		
		$specialListing=$this -> adminmodel -> getTotalLiveSpecialListing();
		$result_specials = "";
		if (!empty($specialListing)) {
			$i=1;
            foreach ($specialListing as $special):	
				if($i%2==0) $rowClass="even";else $rowClass='odd';
				$userType = $this -> session -> userdata('user_type');
				$user_retailer_id = $this -> session -> userdata('user_retailer_id');
				$user_store_format_id = $this -> session -> userdata('user_store_format_id');
				$user_store_id = $this -> session -> userdata('user_store_id');
				
				$store_count = $this->specialproductmodel->get_special_store_count($special['Id'],$userType,$user_retailer_id,$user_store_format_id,$user_store_id);
		
                $result_specials .= "<tr class='".$rowClass."'><td><a target='_blank' href='".base_url()."specialmanagement/managespecials/".$special['Id']."'>" . $special['SpecialName'] . "</a></td>";
                $result_specials .= "<td><span>" . date('d/m/Y',strtotime($special['SpecialFrom'] )). "</span></td>";				
				$result_specials .= "<td><span>" . date('d/m/Y',strtotime($special['SpecialTo'] )). "</span></td>";			
                $result_specials .= "<td class='specialproducts'>".$store_count."</td></tr>";
				$i++;
            endforeach;
        } else {
            $result_specials .="";
        }		
		echo json_encode (array( 'html' => $result_specials));
	}
	
	public function getStoreFormatsOfUserType() {        
		$result = 0;
		$html = 'No Stores Formats Found';
		$retailerHtml='';
		$storeFormats= $this -> adminmodel -> getStoreFormatsOfUserType($this -> session -> userdata('user_type'));
		$allRetailers= $this -> adminmodel -> get_retailers();
		if(!empty($allRetailers)){
			foreach($allRetailers as $retailer){
				$retailerHtml.='<option value="'.$retailer['CompanyName'].'">'.$retailer['CompanyName'].'</option>';
			}
		}
		if($this -> session -> userdata('user_type')==3 || $this -> session -> userdata('user_type')==5){
			if(!empty($storeFormats)){
			$html='<div class="demo-wrapper">
                <div id="stores_state_report" class="reports stores_state_report_table input-box-content">
                  ';
			foreach($storeFormats as $storeFormat){
			
				$Store_Count=$this -> adminmodel -> getStoresByFormatAndRetailer($storeFormat['Id'],$this -> session -> userdata('user_retailer_id'));
				$storeCount=count($Store_Count); $devideBy=100;
				
				if(count($storeCount) <100)
					$storeCount=(count($Store_Count)*100);
				if(count($storeCount)>=10000)
					$devideBy=1000;
				if (count($storeCount)<=10000 && count($storeCount)>=1000)
					$devideBy=100;
				if(count($storeCount) <=100) $devideBy=10;
				$divWidthPercent=($storeCount/$devideBy);
				if($divWidthPercent > 20) $divWidthPercent=($divWidthPercent/10);
					$divWidthPercent=($divWidthPercent/2);
				$html.='<a data-href="'.base_url().'home/getStoresListingByFormat/'.$storeFormat['Id'].'" href="javascript:void(0)" class="storeFormat storeFormatName" name="state_count" data-code="'.$storeFormat['Id'].'" data-name="'.$storeFormat['StoreType'].'">
				<b class="">'.$storeFormat['StoreType'].' </b></a>
				<div class="storecountwithbar">
				<span class="storecount">'.count($Store_Count).'</span>
					<div class="progress" style="width:'.$divWidthPercent.'%">
				  
					<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:100%">
				
				</div> </div></div>';
				
				}
						$html.='		
							
								  
							</div>
						</div>
						<!-- <div id="store_details" class="reports_details hide">
							<h1 class="report_main_header">Stores in <span id="storeFormat"></span></h1>
							<div class="table-report tableOfStoreListing">						
							</div>
						</div> -->';
			}
		}
		else{
			if(!empty($storeFormats)){
			$html='';
				foreach($storeFormats as $storeFormat){
				
					$totalStores= $this -> adminmodel -> getStoresCountByFormatId($storeFormat['Id']);
					
					$html .= "<tr>";
					$html .= "<td>" .$storeFormat['StoreType']. "</td>";
					$html .= "<td>" . $storeFormat['CompanyName']. "</td>";
					$html .= "<td style='text-align:center;' width='10%'><span class=''><a data-href='".base_url()."home/getStoresListingByFormat/".$storeFormat['Id']."' class='getStores' storeFormatId='".$storeFormat['Id']."' href='javascript:void(0);'>" .$totalStores['store_count'] . "</a></span></td>";
					$html .= "</tr>";
					
				}
						
			}
		}
		
        echo json_encode(array(
            'result' => $result,
            'html' => $html,
			'retailerHtml'=>$retailerHtml
        ));
    }
	public function getProducts($parameter) {

		$result = 0;
		$html='';
		$categoriesHtml='';
		$categoryId=0;
		$products=array();
		if($parameter=='generalproducts')
			$products= $this -> adminmodel -> getProductsByRetailerAndCategory($this -> session -> userdata('user_type'),$categoryId);
		else if($parameter=='specialproducts')
			$products= $this -> adminmodel -> getSpecialProductsOfRetailer($this -> session -> userdata('user_type') );
		if(!empty($products)){
			
			foreach ($products as $product) {
			   
				$totalShares= $this -> adminmodel -> getTotalSharesOfProductId($product['Id'])['totalShares'];
				$totalViews= $this -> adminmodel -> getTotalViewsOfProductId($product['Id'])['totalViews'];
				$totalReviews= $this -> adminmodel -> getTotalReviewsOfProductId($product['Id'])['totalReviews'];
				$html .= "<tr>";
				$html .= "<td>" . $product['Brand'].' '. $product['ProductName']. "</td>";
				$html .= "<td width='10%'class='hide_column'>" . $product['CategoryName']. "</td>";
				$html .= "<td  style='text-align:center;'  width='10%'><span class=''>" .$totalShares . "</span></td>";	
				$html .= "<td  style='text-align:center;'  width='10%'><span class=''>" .$totalViews . "</span></td>";	
				$html .= "<td  style='text-align:center;'  width='10%'><span class=''>" .$totalReviews . "</span></td>";	
				$html .= "</tr>";
				
			}
		}
		
		$categories= $this -> adminmodel -> getAllCategoriesOfRetailer($this -> session -> userdata('user_type'));
		
		if(!empty($categories)){
			
			foreach ($categories as $category) {			   
				$categoriesHtml .= "<option value='".$category['CategoryName']."'>" .$category['CategoryName']. "</option>";				
			}
		}		
		
        echo json_encode(array(
            'result' => $result,
            'html' => $html,
			'categoriesHtml'=>$categoriesHtml
        )); 
    }
	public function getAllRetailers() {

		$result = 0;
		$html='';
		$categoriesHtml='';
		$categoryId=0;
		$products=array();
		$all_retailers = $this ->adminmodel -> get_retailers();
		if(!empty($all_retailers)){
			
			foreach ($all_retailers as $retailer) {			   
				$totalProducts = $this ->adminmodel -> getTotalProductsByRetailer($retailer['Id']);
				$totalSpecials = $this ->adminmodel -> getTotalSpecialsByRetailer($retailer['Id']);
				$totalStoreFormat = $this ->adminmodel -> getTotalStoreFormatsByRetailer($retailer['Id']);
				$html .= "<tr>";
				$html .= "<td>" . $retailer['CompanyName']. "</td>";
				$html .= "<td>" . $retailer['CompanyDescription']. "</td>";
				$html .= "<td style='text-align:center;' width='10%'><span class=''>" .$totalProducts. "</span></td>";
				$html .= "<td style='text-align:center;' width='15%'><span class=''>" .$totalSpecials. "</span></td>";
				$html .= "<td style='text-align:center;' width='15%'><span class=''>" .$totalStoreFormat['count'] . "</span></td>";
				$html .= "</tr>";
				
			}
		}			
		
        echo json_encode(array(
            'result' => $result,
            'html' => $html
        )); 
    }
	public function getAllUsers($parameter) {

		$result = 0;
		$html='';
		$categoriesHtml='';
		$categoryId=0;
		$products=array();
		$users = $this ->adminmodel -> get_users($parameter,$this -> session -> userdata('user_type'));
		if(!empty($users)){
			
			foreach ($users as $user) {			   
				
				$html .= "<tr>";
				$html .= "<td>" . $user['FirstName']. ' '.$user['LastName']. "</td>";
				$html .= "<td>" . $user['Email']. "</td>";
				$html .= "<td>" . $user['DateOfBirth']. "</td>";
				$html .= "<td>" .  date('Y-m-d',strtotime($user['CreatedOn'])). "</td>";
				$html .= "</tr>";
				
			}
		}			
		
        echo json_encode(array(
            'result' => $result,
            'html' => $html
        )); 
    }
	public function getMostVisitedProducts($monthYr='') {

		$result = 0;
		$html='<b>Products are Not Found</b>';
		$categoriesHtml='';
		$categoryId=0;
		$products=array();
		if($monthYr=='')
			$monthYr=date('Y-m');
		
		$products= $this -> adminmodel -> getMostVisitedProducts($this -> session -> userdata('user_type'),$monthYr);
		
		if(!empty($products)){
			$html='';
			foreach ($products as $product) {		
				$productCount=$product['productCount'];
				if($product['productCount'] <100)
					$productCount=($product['productCount']*100);
				if($product['productCount']>=10000)
					$devideBy=1000;
				else if ($product['productCount']<=10000 && $product['productCount']>=1000)
					$devideBy=100;
				else $devideBy=10;
				$divWidthPercent=($productCount/$devideBy);
		
				$html .= "<tr>";
				$html .= "<td>" . $product['Brand'].' '. $product['ProductName']. "</td>";
				$html .= "<td style='text-align:center;' width='20%'><span class=''>" .$product['productCount'] . "</span></td>";
				$html .= "</tr>";			
				
			}
		}			
		
        echo json_encode(array(
            'result' => $result,
            'html' => $html
        )); 
    }
	public function gettrendingproducts($parameter='specialproducts',$monthYr='') {

		$result = 0;
		$html='<b>Products are Not Found</b>';
		$categoriesHtml='';
		$categoryId=0;
		$products=array();
		if($monthYr=='')
			$monthYr=date('Y-m');
		//$monthYr=date('Y-m',strtotime($monthYr));
		
		if($parameter=='productspecial')
			$products= $this -> adminmodel -> getTrendingProductsSpecialFromDB($this -> session -> userdata('user_type') ,$monthYr);
		else if($parameter=='productviews')
			$products= $this -> adminmodel -> getTrendingProductsViewsFromDB($this -> session -> userdata('user_type'),$monthYr);
		else if($parameter=='productshares')
			$products= $this -> adminmodel -> getTrendingProductsSharesFromDB($this -> session -> userdata('user_type'),$monthYr);
		else if($parameter=='productreviews'){
			$results= $this -> adminmodel -> getTrendingProductsReviewsFromDB($this -> session -> userdata('user_type'),$monthYr);
			$productsArray=array();$productAndReview=array();
			if(!empty($results)){			
				foreach($results as $product){
					$productsArray[]=$product['ProductId'];
					$productAndReview[$product['ProductId']]=$product['productCount'];
				}
				$productss= $this -> adminmodel -> getTrendingProductsActualReviewsFromDB($this -> session -> userdata('user_type'),$productsArray);
				if(!empty($productss)){
					foreach($productss as $product){
						$productData=array();
						$productData['ProductName']=$product['ProductName'];
						$productData['Brand']=$product['Brand'];
						$productData['productCount']=$productAndReview[$product['ProductId']];
						$products[]=$productData;
					}
				}
			}
		}
			
		if(!empty($products)){
			$html='';
			foreach ($products as $product) {		
				$productCount=$product['productCount'];
				if($product['productCount'] <100)
					$productCount=($product['productCount']*100);
				if($product['productCount']>=10000)
					$devideBy=1000;
				else if ($product['productCount']<=10000 && $product['productCount']>=1000)
					$devideBy=100;
				else $devideBy=10;
				$divWidthPercent=($productCount/$devideBy);
		
				$html .= "<tr>";
				$html .= "<td>" . $product['Brand'].' '. $product['ProductName']. "</td>";
				$html .= "<td style='text-align:center;' width='20%'><span class=''>" .$product['productCount'] . "</span></td>";
				$html .= "</tr>";
				
				
			}
		}
		
		$categories= $this -> adminmodel -> getAllCategoriesOfRetailer($this -> session -> userdata('user_retailer_id'));
		
		if(!empty($categories)){
			
			foreach ($categories as $category) {			   
				$categoriesHtml .= "<option value='".$category['CategoryName']."'>" .$category['CategoryName']. "</option>";				
			}
		}		
		
        echo json_encode(array(
            'result' => $result,
            'html' => $html,
			'categoriesHtml'=>$categoriesHtml
        )); 
    }
	public function getUsersInfo($parameter,$monthYr='') {

		$result = 0;
		$html='';
		$users=array();
		if($monthYr=='')
			$monthYr=date('Y').'-'.date('m');
		
		if($parameter=='countOfUsersByRetailer'){
			$users= $this -> adminmodel -> getCheckedinUsersByStoreOfRetailer($this -> session -> userdata('user_retailer_id'),$monthYr);		
			if(!empty($users)){		
				$result=1;
				$html='';
				foreach ($users as $user) {			  
					$html .= "<tr>";
					$html .= "<td>" . $user['FirstName'].' '. $user['LastName']. "</td>";
					$html .= "<td>" . $user['StoreName']. "</td>";
					$html .= "<td>" . $user['Building'].' '. $user['StreetAddress'].' '. $user['Zip'].' '. $user['City']. "</td>";
					$html .= "<td>" . date('d/m/y H:i',strtotime($user['CheckinTime'])). "</td>";
					$html .= "</tr>";
					
				}
			}	
		}
		else {
			$users= $this -> adminmodel -> getSignupUsers($monthYr);		
			if(!empty($users)){		
				$result=1;
				$html='';
				foreach ($users as $user) {			  
					$html .= "<tr>";
					$html .= "<td>" . $user['FirstName'].' '. $user['LastName']. "</td>";
					$html .= "<td>" . $user['Email']. "</td>";
					$html .= "<td>" . date('d/m/y H:i',strtotime($user['CreatedOn'])). "</td>";
					$html .= "</tr>";
					
				}
			}	
		}		
        echo json_encode(array(
            'result' => $result,
            'html' => $html
        )); 
    }
	
	public function getsignupusers($year='') {

		$result = 0;
		$html='';
		$users=array();	$atotalusers='';array();
		if($year==date('Y')) {
			$tillMonth=date("m",strtotime(date('ymd')));
			
		}
		else {
			$tillMonth='12';
		}
			for($i=1;$i<= $tillMonth;$i++){
				
				if($i<10)
					$month=$year.'-0'.$i.'-';
				else $month=$year.'-'.$i.'-';
				if($this -> session -> userdata('user_type')==3 || $this -> session -> userdata('user_type')==5){
						$users= $this -> adminmodel -> getTotalSignupUsersByMonthForRetailer($month);		
				}
				else $users= $this -> adminmodel -> getTotalSignupUsersByMonth($month);		
				
				if(!empty($users)){						
					$atotalusers[strtoupper(date('M', mktime(0, 0, 0, $i, 10)))]=$users['count'];
				}
			}
					
		
        echo json_encode($atotalusers); 
    }
	public function getsignupusersforlastthreemonth() {

		$result = 0;
		$html='';
		$users=array();	$totalusers='';array();		
			$tillMonth=date("m",strtotime(date('ymd')));		
		$year=date('Y');
			for($i=($tillMonth-2);$i<= $tillMonth;$i++){
				
				if($i<10)
					$month=$year.'-0'.$i.'-';
				else $month=$year.'-'.$i.'-';
				$users= $this -> adminmodel -> getTotalSignupUsersByMonth($month);		
				if(!empty($users)){						
					$totalusers[date('Y, '.$i.' 1', mktime(0, 0, 0, $i, 10))]=$users['count'];
					//$totalusers[date('M', mktime(0, 0, 0, $i, 10))]=$users['count'];
				}
			}
					
		echo json_encode(array(
            'totalusers' => $totalusers,
        )); 
       
	
    }
	public function getspecialsbrowseofretailer() {

		$result = 0;
		$html='';
		$specials=array();
		$browseCount=0;
		$specials= $this -> adminmodel -> getSpecialsBrowseOfRetailer($this -> session -> userdata('user_type') );		
		if(!empty($specials)){		
			$result=1;
			$html='';
			foreach ($specials as $special) {
				$browseCount+=$special['totalVisit'] ;
				$html .= "<tr>";
				$html .= "<td>" . $special['SpecialName']. "</td>";
				$html .= "<td  style='text-align:center;' width='20%'><span class=''>" .$special['totalVisit'] . "</span></td>";				
				$html .= "</tr>";				
			}
		}	
		
        echo json_encode(array(
            'result' => $result,
            'html' => $html,
			'browseCount'=>$browseCount
        )); 
    }
	
	public function outhcallback() {
		
		/* $fields = array(
		'client_id' => urlencode('900366999434-inaa31mk6bnvmg0tai2ouebi91boucse.apps.googleusercontent.com'),
		'client_secret' => urlencode('qh0HpHpIwNQmyPF7sWG-GDH7'),
		#'refresh_token' => '1/mvU1D5Q8baOEkkdFXGao1Et6-HAv_z8koe2CtnhDseA',
		'refresh_token' => '1/5FuHyii2-Z2gkvDahTttSHcc9IB86fsHw217g0TAqFw',
		'grant_type' => 'refresh_token'
		); 
		
		$redirect_uri = 'http://www.thebestdeal.co.za/admin/home/outhcallback';
		$fields = array(
		'code' => urlencode('4/jvSSiGeLooncnvGNgUgGiAop3xrEBEGKBT-SgB5SyiY'),
		'client_id' => urlencode('913945624588-1pks41ghl7hr0jgmgkdo232gq4ho15jl.apps.googleusercontent.com'),
		'client_secret' => urlencode('SvHrBwLEnrotFV9hoNckRbdM'),
		'redirect_uri' => urlencode($redirect_uri),
		'grant_type' => urlencode('authorization_code')
		); 		
		$post = '';
		foreach ($fields as $key => $value) 
		{
			$post .= $key . '=' . $value . '&';
		}
		$post = rtrim($post, '&');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
		curl_setopt($curl, CURLOPT_POST, 5);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($result); 
		
		echo '<pre>';print_r($response); echo '</pre>'; 
		
		//1/CE_TcqjrGS7YETg4EX3S5J-sqP7nhy8QGycuv8bW6bA
		
		//4/jvSSiGeLooncnvGNgUgGiAop3xrEBEGKBT-SgB5SyiY*/
	}
	
	public function getusageanalytics($timeduration='',$matrics='',$dimension='',$month='notselected',$dailydate='notselected',$startdate='notselected',$enddates='notselected')
	{
		require_once 'application/libraries/google-analytics-api_zip_1/google-analytics-api/vendor/autoload.php';
		$OAUTH2_CLIENT_ID = '913945624588-1pks41ghl7hr0jgmgkdo232gq4ho15jl.apps.googleusercontent.com';
		$OAUTH2_CLIENT_SECRET = 'SvHrBwLEnrotFV9hoNckRbdM';

		$client = new Google_Client();
		$client->setClientId($OAUTH2_CLIENT_ID);
		$client->setClientSecret($OAUTH2_CLIENT_SECRET);
	
		$client->setScopes('https://www.googleapis.com/auth/analytics.readonly');
		$refreshToken = '1/CE_TcqjrGS7YETg4EX3S5J-sqP7nhy8QGycuv8bW6bA';

		$client->refreshToken($refreshToken);
		$tokens = $client->getAccessToken($refreshToken);
		$client->setAccessToken($tokens);
		// Create an authorized analytics service object.
		  $analytics = new Google_Service_AnalyticsReporting($client);
		$startMonth=date('Y-01');$endMonth=date('Y-m');
		if($month=='notselected'){
			$monthnum=date('Y-01');
		}
		else $monthnum=$month; 
		if($timeduration=='timeofday')
		  {
			  $stDate='today';			  
		  }
		  $endDate='today';$now = time();$returnLastDate=date('Y-m-d');
		  if($timeduration!='timeofday'){
			  
			  if($timeduration=='year')
			  {
				  $date = strtotime($startMonth."-01");
			}
			  else {
				  $date = strtotime($monthnum."-01");
			  }
			  $datediff = $now - $date;			   
			  $findStartDateDays=floor($datediff / (60 * 60 * 24));
			  $stDate=$findStartDateDays.'daysAgo';			  
			  
			  if($month!='notselected'){
				  $returnLastDate=$monthnum."-".date('t',strtotime($monthnum."-01"));
				  $date = strtotime($monthnum."-".date('t',strtotime($monthnum."-01")));
				  $datediff = $now - $date;			   
				  $findEndDateDays=floor($datediff / (60 * 60 * 24));
				  $endDate=$findEndDateDays.'daysAgo';
				  
			  }				  
				  
		  }	
		   if($month==date('Y-m'))
		   { $endDate='today';		$returnLastDate=date('Y-m-d');}
		  if($startdate!='notselected'){
			  $date = strtotime($startdate);
			  $datediff = $now - $date;			   
			  $findStartDateDays=floor($datediff / (60 * 60 * 24));
			  $stDate=$findStartDateDays.'daysAgo';	
		  }
		  if($enddates!='notselected'){
			  $date = strtotime($enddates);
			  $datediff = $now - $date;			   
			  $findEndDateDays=floor($datediff / (60 * 60 * 24));
			  $endDate=$findEndDateDays.'daysAgo';	
		  }
			if($dailydate!='notselected'){
				$date = strtotime($dailydate);
			   $datediff = $now - $date;			   
			   $findStartDateDays=floor($datediff / (60 * 60 * 24));
			   $endDate=$stDate=$findStartDateDays.'daysAgo';	
			   
			}
		    $VIEW_ID = "139585736"; 
		   
		    if($timeduration!='timeofday')
			  $tochecklastdate=31;
		   else $tochecklastdate=date('d');		  
		  
			  // Call the Analytics Reporting API V4.
			  $reportsOfActivity = $this->getReportsOfActivity($analytics,$stDate,$endDate,$VIEW_ID,$matrics,$dimension);
				$reportsOfActivity['arrayForDimension']=$reportsOfActivity['dimension']=array();
			  $reportsOfActivity=$this->printResults($reportsOfActivity,$matrics,$dimension,$startdate);

			  $reportsOfActivityForDate['arrayForDimension']=$reportsOfActivityForDate['dimension']=array();
			  if($dimension=='week'){
				  $reportsOfActivityForDate = $this->getReportsOfActivity($analytics,$stDate,$endDate,$VIEW_ID,$matrics,'date');				
				  $reportsOfActivityForDate=$this->printResults($reportsOfActivityForDate,$matrics,'date',$startdate);
			  }

			 if($dimension=='hour' && $dailydate!='notselected'){
				  for($i=01;$i<=24;$i++){

					 if(!array_key_exists($i,$reportsOfActivity['arrayForDimension'])){
						 $reportsOfActivity['arrayForDimension'][$i]=0;
					 }
				  }
				  ksort($reportsOfActivity['arrayForDimension']);
				  $convertoToHoursArray=array();
				  foreach($reportsOfActivity['arrayForDimension'] as $key=>$report){
					  $convertoToHoursArray[date("g:i a", strtotime($key.':00'))]=$report;
				  }
				 
				  $reportsOfActivity['arrayForDimension']=$convertoToHoursArray;
			 }

			$returntxt=1;$maxVisitsLabelForDate=$maxVisitsLabel=array(0=>0);
			if(!empty($reportsOfActivity['arrayForDimension']))
				$maxVisitsLabel=array_keys($reportsOfActivity['arrayForDimension'], max($reportsOfActivity['arrayForDimension']));
			 $maxVisitsLabel = array_fill_keys($maxVisitsLabel, 'maxvalue');
			if(!empty($reportsOfActivityForDate['arrayForDimension'])){
				
				for($i=1;$i<=31;$i++){
					if($i<10)$key='0'.$i; else $key=$i;
					if (!array_key_exists($key,$reportsOfActivityForDate['arrayForDimension']))
						$reportsOfActivityForDate['arrayForDimension'][$key]=0;
				}
			
				ksort($reportsOfActivityForDate['arrayForDimension']);
				foreach ($reportsOfActivityForDate['arrayForDimension'] as $key => $val) {
					$reportsOfActivityForDate['arrayForDimension'][$key.'.'] = $val;
					unset($reportsOfActivityForDate['arrayForDimension'][$key]);
				}
				$maxVisitsLabelForDate=array_keys($reportsOfActivityForDate['arrayForDimension'], max($reportsOfActivityForDate['arrayForDimension']));
			}
			$maxVisitsLabelForDate = array_fill_keys($maxVisitsLabelForDate, 'maxvalue');
			
			
		    echo json_encode(array(          
						'arrayForDimension' => $reportsOfActivity['arrayForDimension'],		
						'arrayForDimensionForDate' => $reportsOfActivityForDate['arrayForDimension'],		
						'totalSumOfValues'=>array_sum($reportsOfActivity['arrayForDimension']),
						'returntxt'=>$returntxt,
						'returnLastDate'=>$returnLastDate,
						'maxVisitsLabel'=>$maxVisitsLabel,
						'maxVisitsLabelForDate'=>$maxVisitsLabelForDate
        )); 
		}
		public function getWeeks($date, $rollover)
    {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $weeks = 1;

        for ($i = 1; $i <= $elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }
		PUBLIC function getReportsOfActivity($analytics,$stDate,$endDate,$VIEW_ID,$matrics,$dimension) {

			  // Create the DateRange object.
			  $dateRange = new Google_Service_AnalyticsReporting_DateRange(); 
			
			  $dateRange->setStartDate($stDate);
			  $dateRange->setEndDate($endDate);

			  // Create the Metrics object.
			  $Metricss = new Google_Service_AnalyticsReporting_Metric();
			  $Metricss->setExpression("ga:".$matrics);
			  //$Metricss->setExpression("ga:visits");
			 //Create the Dimensions object.
			  $Dimensionss = new Google_Service_AnalyticsReporting_Dimension();
			  $Dimensionss->setName("ga:".$dimension);
			  //$Dimensionss->setName("ga:date");

			  // Create the ReportRequest object.
			  $request = new Google_Service_AnalyticsReporting_ReportRequest();
			  $request->setViewId($VIEW_ID);
			  $request->setDateRanges($dateRange);
			  $request->setDimensions(array($Dimensionss));
			  $request->setMetrics(array($Metricss));

			  $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
			  $body->setReportRequests( array( $request) );
			  return $analytics->reports->batchGet( $body );
		 
		}
		/**
		 * Parses and prints the Analytics Reporting API V4 response.
		 *
		 * @param An Analytics Reporting API V4 response.
		 */
		PUBLIC function printResults($reports,$matrics,$dimensionn,$startDate='notselected') {
			
			$visits=$dimension=$overAllArray=$arrayForDimension=array();
			  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
				$report = $reports[ $reportIndex ];
				$header = $report->getColumnHeader();
				$dimensionHeaders = $header->getDimensions();
				$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
				$rows = $report->getData()->getRows();

				for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				  $row = $rows[ $rowIndex ];
				  $dimensions = $row->getDimensions();
				  $metrics = $row->getMetrics();
				  for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
					//print($dimensionHeaders[$i] . ":---> " . $dimensions[$i] . "\n");
					if($matrics=='dates')
						$dimension[]=date('d y',strtotime($dimensions[$i]));
					else $dimension[]=$dimensions[$i];
				  }

				  for ($j = 0; $j < count($metrics); $j++) {
					$values = $metrics[$j]->getValues();
					for ($k = 0; $k < count($values); $k++) {
					  $entry = $metricHeaders[$k];
					//	print($entry->getName() . ":>> " . $values[$k] . "\n");
					$visits[]=$values[$k];
					}
				  }
				}
			  }
			  
			  if(!empty($dimension)){
				  if($matrics=='sessions'){
					foreach($dimension as $k => $d){
					  $sessionArrayForDates[$d]=floor($visits[$k] % 3600 / 60) ;
					}  
				  }
				  else{
				  if($startDate!='notselected')					  
					$i=$this->getWeeks($startDate, "sunday");
				  else
					$i=1;
					  foreach($dimension as $k => $d){
						  if($dimensionn=='month'){
							$monthNum  = $d;
							$dateObj   = DateTime::createFromFormat('!m', $monthNum);
							$monthName = $dateObj->format('M'); 
							$arrayForDimension[$monthName]=$visits[$k]; 
						  }
						  else if($dimensionn=='week'){
							  $arrayForDimension['Week '.$i]=$visits[$k]; 
							  $i++;
						  }
						  else if($dimensionn=='date'){
							$arrayForDimension[date('d',strtotime($d))]=$visits[$k]; 							 
						  }
						  else if($dimensionn=='hour'){
							$arrayForDimension[ltrim($d, '0')]=$visits[$k]; 							 
						  }
				  }
				  }
			  }
			  
			//  if($dimensionn=='date') ksort($arrayForDimension);
			 // print_r($arrayForDimension);
			  $overAllArray['dimension']=$dimension;
			  $overAllArray['arrayForDimension']=$arrayForDimension;
			  return $overAllArray;
		}
		public function getsignupuserforcurryear() {

		$result = 0;
		$html='';
		$users=array();	$atotalusers='';array();
		
		$tillMonth=date("m",strtotime(date('ymd')));
			
		$year=date('Y');
			for($i=1;$i<= $tillMonth;$i++){
				
				if($i<10)
					$month=$year.'-0'.$i.'-';
				else $month=$year.'-'.$i.'-';
				if($this -> session -> userdata('user_type')==3 || $this -> session -> userdata('user_type')==5){
						$users= $this -> adminmodel -> getTotalSignupUsersByMonthForRetailer($month);		
				}
				else $users= $this -> adminmodel -> getTotalSignupUsersByMonth($month);		
				
				if(!empty($users)){						
					$atotalusers[strtoupper(date('M', mktime(0, 0, 0, $i, 10)))]=$users['count'];
				}
			}
					
		
        echo json_encode($atotalusers); 
    }
	public function getUsersByGender() {

		$result = 0;
		$html='';
		$users=array();	$atotalusers='';array();		
		$users= $this -> adminmodel -> getUsersByGender($this -> session -> userdata('user_type'));		
		
		if(!empty($users)){						
			$atotalusers['Male']=$users['maleuser'][0]['maleusers'];
			$atotalusers['Female']=$users['femaleuser'][0]['femaleusers'];
			$totalusers=($users['maleuser'][0]['maleusers']+$users['femaleuser'][0]['femaleusers']);
		}
         echo json_encode(array(          
			'usersbygender' => $atotalusers,		
			'totalusers' => $totalusers,
			
        )); 
    }
		public function getUsersByAge() {

		$result = 0;
		$html='';
		$users=array();	$usersGroupByAge='';array();		
		$users= $this -> adminmodel -> getTotalUsers($this -> session -> userdata('user_type'));		
		$usersUnder18=$usersBetween18To25=$usersBetween26To29=$usersBetween30To35=$usersBetween36To45=$usersBetween46To55=$usersBetween56To65=$usersOver65=$totalUsers=0;
		if(!empty($users)){	
				foreach($users as $user){
					if($user['DateOfBirth']!='0000-00-00'){
						 $birthDate = date('m/d/Y',strtotime($user['DateOfBirth']));
						//explode the date to get month, day and year
						$birthDate = explode("/", $birthDate);
						//get age from date or birthdate
						$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
							? ((date("Y") - $birthDate[2]) - 1)
							: (date("Y") - $birthDate[2]));
					
						if($age < 18)	$usersUnder18++;
						else if($age >= 18 && $age <= 25)	$usersBetween18To25++;
						else if($age >= 26 && $age <= 29)	$usersBetween26To29++;
						else if($age >= 30 && $age <= 35)	$usersBetween30To35++;
						else if($age >= 36 && $age <= 45)	$usersBetween36To45++;
						else if($age >= 46 && $age <= 55)	$usersBetween46To55++;
						else if($age >= 56 && $age <= 65)	$usersBetween56To65++;
						else if($age < 65)	$usersOver65++;
						$totalUsers++;
					}					
				}			
		}
		$usersGroupByAge['Under 18']=$usersUnder18;
		$usersGroupByAge['18 - 25']=$usersBetween18To25;
		$usersGroupByAge['26 - 29']=$usersBetween26To29;
		$usersGroupByAge['30 - 35']=$usersBetween30To35;
		$usersGroupByAge['36 - 45']=$usersBetween36To45;
		$usersGroupByAge['46 - 55']=$usersBetween46To55;
		$usersGroupByAge['56 - 65']=$usersBetween56To65;
		$usersGroupByAge['Over 65']=$usersOver65;
		
         echo json_encode(array(
			'usersGroupByAge' => $usersGroupByAge,
			'totalSumOfValues'=>$totalUsers
        )); 
    }
}