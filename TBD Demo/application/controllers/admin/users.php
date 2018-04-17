<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


/*
 * Author:PHN
 * Purpose:User Controller
 * Date:26-08-2015
 * Dependency: usermodel.php
 */
class Users extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
        $this -> load -> model('admin/usermodel', '', TRUE);
        $this -> load -> model('admin/statemodel', '', TRUE);

        $this -> page_title = "Users";
        $this -> breadcrumbs[] = array('label' => 'Users', 'url' => '/users');
    }

    public function index() {



        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'User Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'BackOffice Users', 'url' => '/users');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['user_roles'] = $this -> usermodel -> get_user_roles();

        if ($this -> session -> userdata('user_level') < 3) {
            $this -> template -> view('admin/users/index', $data);
        }
        else {
            $this -> template -> view('admin/users/store_users', $data);
        }
    }

    public function datatable($user_role) {
        $this -> datatables -> select("users.Id as u_id, users.IsActive as active, CONCAT_WS( ' ', FirstName, LastName ) as Name,Email, retailers.CompanyName as CompanyName, r.Type as Type, r.Id as RoleId")
            -> unset_column('u_id')
            -> unset_column('active')
            -> unset_column('RoleId')
            -> from('users')
            -> join('userroles r', 'r.Id = users.UserRole')
            -> join('retailers', 'retailers.RetailerAdminId = users.Id', 'left')
            -> where(array('users.IsRemoved' => 0, 'users.Id !=' => $this -> session -> userdata('user_id')))
            -> add_column('Actions', user_get_buttons('$1'), 'u_id');
        if ($user_role > 0) {
            $this -> datatables -> filter('users.UserRole', $user_role);
        }

        echo $this -> datatables -> generate();
    }

    public function web_datatable() {
        
        $this -> datatables -> select("users.Id as u_id, users.SocialMedia, users.UserScreenName, users.IsActive as active, CONCAT_WS( ' ', users.FirstName, users.LastName ) as Name,users.Email,case when users.CreatedOn!= '0000-00-00 00:00:00' then DATE_FORMAT(users.CreatedOn,'%d/%m/%Y') else '' end as RegistrationDate",FALSE)
            -> unset_column('u_id')
            -> unset_column('active')
            -> unset_column('RoleId')
            -> from('users')
            -> join('retailers', 'retailers.RetailerAdminId = users.Id', 'left')
            -> where(array('users.IsRemoved' => 0, 'users.UserRole' => 0))
            //-> where(array('users.IsRemoved' => 0, 'users.UserRole' => 4))                        
            -> add_column('Actions', social_user_get_buttons('$1'), 'u_id');
            //get__loyalty_points
        echo $this -> datatables -> generate();
    }

    public function store_user_datatable($user_role) {

        $this -> datatables -> select("users.Id as u_id, users.IsActive as active, CONCAT_WS( ' ', FirstName, LastName ) as Name,Email, retailers.CompanyName as CompanyName, r.Type as Type, r.Id as RoleId,stores.StoreName,storestypes.StoreType")
            -> unset_column('u_id')
            -> unset_column('active')
            -> unset_column('RoleId')
            -> from('users')
            -> join('userroles r', 'r.Id = users.UserRole')
            -> join('retailers', 'retailers.RetailerAdminId = users.Id', 'left')
            -> join('storeadmin', 'storeadmin.UserId = users.Id')
            -> join('stores', 'stores.Id = storeadmin.StoreId', 'left')
            -> join('storestypes', 'storeadmin.StoreTypeId = storestypes.Id', 'left')
            -> where(array('users.IsRemoved' => 0, 'users.Id !=' => $this -> session -> userdata('user_id')))
            -> where("r.Level >", $this -> session -> userdata('user_level'))
            -> add_column('Actions', user_get_buttons('$1'), 'u_id');

        if ($user_role > 0) {
            $this -> datatables -> filter('users.UserRole', $user_role);
        }

        //Retailers Users
        if ($this -> session -> userdata('user_level') == 3) {
            $this -> datatables -> where('(stores.RetailerId = ' . $this -> session -> userdata('user_retailer_id') . ' or storestypes.RetailerId=' . $this -> session -> userdata('user_retailer_id') . ')');
//            $this->datatables->or_where('storestypes.Id',$this->session->userdata('user_store_format_id'));
        }

        if ($this -> session -> userdata('user_level') == 4) {
            $this -> datatables -> where('stores.RetailerId', $this -> session -> userdata('user_retailer_id'));
        }
//

        echo $this -> datatables -> generate();
    }

    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Add User
            $this -> form_validation -> set_rules('first_name', 'first name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('last_name', 'last name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('password', 'password', 'trim|required|matches[confirm_password]');
            $this -> form_validation -> set_rules('confirm_password', 'confirm password', 'trim|required');
            //$this->form_validation->set_rules('profile_image', 'Document', 'callback_file_selected_check');
            $this -> form_validation -> set_rules('email', 'email', 'trim|required|valid_email|callback_check_uniqueness_email');
            $this -> form_validation -> set_rules('telephone', 'Telephone(Home)', 'trim|required|numeric|xss_clean');
            $this -> form_validation -> set_rules('mobile', 'Mobile No', 'trim|required|numeric|callback_validate_phone|callback_check_uniqueness_mobile|xss_clean');
//            if ($this->input->post('city') != '')
//                $this->form_validation->set_rules('city', 'city', 'callback_validate_city');

            if (!$this -> form_validation -> run() == FALSE) {

                $file_uploaded = TRUE;
                $result = '';
                $image_name = '';
                if (!$_FILES['profile_image']['size'] == 0) {
                    $result = $this -> do_upload('profile_image', 'users', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    if (isset($result['error'])) {
                        $file_uploaded = FALSE;
                    }
                    else {
                        $image_name = $result['upload_data']['file_name'];
                    }
                }

                if ($file_uploaded) {

                    $data = array(
                        'FirstName' => $this -> input -> post('first_name'),
                        'LastName' => $this -> input -> post('last_name'),
                        'Email' => $this -> input -> post('email'),
                        'TelephoneFixed' => $this -> input -> post('telephone'),
                        'Mobile' => $this -> input -> post('mobile'),
                        'Password' => MD5($this -> input -> post('password')),
                        'UserRole' => $this -> input -> post('user_role'),
                        'StreetAddress' => $this -> input -> post('street_address'),
                        'City' => $this -> input -> post('city'),
                        'State' => $this -> input -> post('state'),
                        'PinCode' => $this -> input -> post('pin_code'),
                        'ProfileImage' => $image_name
                    );

                    $result = $this -> usermodel -> add_user($data);
                    $this -> session -> set_userdata('success_message', "User added successfully");

                    redirect('users', 'refresh');
                }
                else {
                    // code to display error while image upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                }
            }
        }

        $this -> breadcrumbs[0] = array('label' => 'User Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'BackOffice Users', 'url' => '/users');
        $this -> breadcrumbs[2] = array('label' => 'Add User', 'url' => 'users/add');

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['user_roles'] = $this -> usermodel -> get_user_roles('Retailers,');

        $data['states'] = $this -> statemodel -> get_states();

        $this -> template -> view('admin/users/add', $data);
    }

    public function edit($id) {

        $data = $this -> usermodel -> get_user_details($id);
        $this -> breadcrumbs[] = array('label' => 'Edit User', 'url' => 'users/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $exclude = "";
        if ($data['UserRole'] != '3')
            $exclude = 'Retailers';

        $data['user_roles'] = $this -> usermodel -> get_user_roles($exclude);

        $data['states'] = $this -> statemodel -> get_states();

        $html = $this -> load -> view('admin/users/edit', $data, true);
        $name = $data['FirstName'].' '.$data['LastName'];
        echo json_encode(array(
            'html' => $html,
            'name' => $name
        ));
    }

    public function delete($id) {

        $this -> usermodel -> delete_user($id);


        $this -> session -> set_userdata('success_message', "User deleted successfully");
        redirect('users', 'refresh');
    }

    public function web_delete($id) {

        $this -> usermodel -> delete_user($id);


        $this -> session -> set_userdata('success_message', "User deleted successfully");
        
        
        redirect('users/web_users', 'refresh');
        //redirect('users/social', 'refresh');
    }

    public function change_status($id, $status) {

        $this -> usermodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "User status updated successfully");
        redirect('users', 'refresh');
    }
    public function web_change_status($id, $status) {

        $this -> usermodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "User status updated successfully");
        redirect('users/social', 'refresh');
    }

    public function change_password($id) {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> form_validation -> set_rules('password', 'password', 'trim|required|matches[confirm_password]');
            $this -> form_validation -> set_rules('confirm_password', 'confirm password', 'trim|required');

            if ($this -> form_validation -> run() == TRUE) {

                $new_password = $this -> input -> post('password');
                $id = $this -> input -> post('id');

                $result = $this -> usermodel -> change_password($id, $new_password);

                //Get User Name
                $user_data = $this -> usermodel -> get_user_details($id);

                // Send email notification to user about .
                $this -> load -> model('admin/emailtemplatemodel');

                $email_template_details = $this -> emailtemplatemodel -> get_email_template_details(6);

                $emailBody = $email_template_details['Content'];
                $emailBody = str_replace("{USERNAME}", $user_data['FirstName'], $emailBody);

                //---- LOAD EMAIL LIBRARY ----//
                $this -> load -> library('email');
                $config['mailtype'] = 'html';

                $this -> email -> initialize($config);
                $this -> email -> from($email_template_details['FromEmail']);
                $this -> email -> to($email_template_details['ToEmail']);
                $this -> email -> subject("The Best Deals: Password Changed");
                $this -> email -> message($emailBody);
                $this -> email -> send();

                if ($result) {
                    $this -> session -> set_userdata('success_message', "Password changed successfully");
                    redirect('users', 'refresh');
                }
            }
        }
        $this -> breadcrumbs[] = array('label' => 'Change Password', 'url' => 'users/change_password');
        $data['id'] = $id;
        $data['title'] = $this -> page_title;
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['user_details'] = $this -> usermodel -> get_user_details($id);
        $this -> template -> view('admin/users/change_password', $data);
    }

    function file_selected_check() {

        $this -> form_validation -> set_message('file_selected_check', 'Please upload user image.');
        if (empty($_FILES['profile_image']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }


    function validate_name($name) {
        $this -> form_validation -> set_message('validate_name', 'Name must contain contain only letters, apostrophe, spaces or dashes.');
        if (preg_match("/^[a-zA-Z'\-\s]+$/", $name)) {
            return true;
        }
        else {
            return false;
        }
    }

    function validate_city($city) {
        $this -> form_validation -> set_message('validate_city', 'City must contain only letters and spaces.');
        if (preg_match('/^[a-zA-Z\s]+$/', $city)) {
            return true;
        }
        else {
            return false;
        }
    }

    function validate_streetaddress($street_address) {
        $this -> form_validation -> set_message('validate_streetaddress', 'Street Address must not contain special characters.');
        if (preg_match('/^[a-zA-Z0-9\'",\\\/\-\s]+$/', $street_address)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function admins($admin_type, $admin_id) {

        $data['title'] = ucfirst($admin_type) . " " . $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['user_roles'] = $this -> usermodel -> get_user_roles();

        $data['admin_type'] = $admin_type;

        $data['admin_id'] = $admin_id;

        $this -> template -> view('admin/users/store_users', $data);
    }

//    public function store_user_datatable($admin_type, $admin_id) {
//
//        $this->datatables->select("users.Id as u_id, users.IsActive as active, CONCAT_WS( ' ', FirstName, LastName ) as Name,Email, r.Type as Type, r.Id as RoleId")
//                ->unset_column('u_id')
//                ->unset_column('active')
//                ->unset_column('RoleId')
//                ->from('users')
//                ->join('userroles r', 'r.Id = users.UserRole')
//                ->join('storeadmin', 'storeadmin.UserId = users.Id')
//                ->where(array('users.IsRemoved' => 0, 'users.Id !=' => $this->session->userdata('user_id')))
//                ->add_column('Actions', user_get_buttons('$1'), 'u_id');
//
//        if ($admin_type == 'storeformat') {
//            $this->datatables->where('storeadmin.StoreTypeId', $admin_id);
//        } else {
//            $this->datatables->where('storeadmin.StoreId', $admin_id);
//        }
//
//        echo $this->datatables->generate();
//    }

    public function add_store_user() {

        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/storeformatmodel', '', TRUE);

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Add Retailer
            $this -> form_validation -> set_rules('email', 'email', 'trim|required|callback_check_uniqueness_email');
            $this -> form_validation -> set_rules('first_name', 'first_name', 'trim|required|max_length[50]|xss_clean');
            $this -> form_validation -> set_rules('last_name', 'last_name', 'trim|required|max_length[50]|xss_clean');
            $this -> form_validation -> set_rules('password', 'password', 'trim|required|matches[confirm_password]');

            if (!$this -> form_validation -> run() == FALSE) {

                $store_format_user = 0;
                if ($this -> input -> post('user_type') == '1') {
                    $store_format_user = 1;
                }
                //If Store Format User
                if ($this -> session -> userdata('user_type') == 5) {
                    $store_format_user = 0;
                }

                if ($store_format_user) {
                    $role = $this -> usermodel -> get_user_role('StoreFormat User');
                }
                else {
                    $role = $this -> usermodel -> get_user_role('Store User');
                }

                $user_data = array(
                    'FirstName' => $this -> input -> post('first_name'),
                    'LastName' => $this -> input -> post('last_name'),
                    'Email' => $this -> input -> post('email'),
                    'Password' => MD5($this -> input -> post('password')),
                    'UserRole' => $role['Id'],
                    'TelephoneFixed' => $this -> input -> post('contact_tel'),
                );

                $user_id = $this -> usermodel -> add_user($user_data);

                if ($store_format_user) {

                    //Save the user to the store admin.
                    $store_data = array(
                        'UserId' => $user_id,
                        'StoreTypeId' => $this -> input -> post('store_format'),
                    );

                    $this -> load -> model('admin/storeadminmodel', '', TRUE);
                    $result = $this -> storeadminmodel -> add_admin($store_data);

                    $this -> storeformatmodel -> update_user_count($this -> input -> post('store_format'));
                }
                else {

                    //Save the user to the store admin.
                    $store_data = array(
                        'UserId' => $user_id,
                        'StoreId' => $this -> input -> post('store'),
                    );

                    $this -> load -> model('admin/storeadminmodel', '', TRUE);
                    $result = $this -> storeadminmodel -> add_admin($store_data);

                    $this -> storemodel -> update_user_count($this -> input -> post('store'));

                    //Add default wizard status
                    $this -> storemodel -> add_store_wizard($store_data);
                }

                if ($result > 0)
                    $this -> session -> set_userdata('success_message', "Store Format User added successfully");
                else
                    $this -> session -> set_userdata('success_message', "Error while adding Store Format User");

                redirect('users', 'refresh');
            }
        }


        $this -> breadcrumbs[] = array('label' => 'Add User', 'url' => 'users/add_store_user');

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        //Retailers Users
        $retailer_id = ( $this -> session -> userdata('user_type') < 3 ) ? 0 : $this -> session -> userdata('user_retailer_id');

        //StoreFormat Users
        $store_format_id = ( $this -> session -> userdata('user_type') < 5 ) ? 0 : $this -> session -> userdata('user_store_format_id');

        $data['stores'] = $this -> storemodel -> get_stores($retailer_id, $store_format_id);

        $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($retailer_id);

        $this -> template -> view('admin/users/add_store_user', $data);
    }

    public function store_user_delete($id) {

        // Reduce the count of store user/ store format user

        $data = $this -> usermodel -> get_user_details($id);
        $this -> usermodel -> delete_user($id);
        //StoreFormat Users
        if ($data["UserRole"] == 5) {
            $this -> load -> model('admin/storeformatmodel', '', TRUE);
            $this -> storeformatmodel -> delete_storeformat_user($id);
        }

        if ($data["UserRole"] == 6) {
            $this -> load -> model('admin/storemodel', '', TRUE);
            $this -> storemodel -> delete_store_user($id);
        }

        $this -> session -> set_userdata('success_message', "User deleted successfully");
        redirect('users', 'refresh');
    }

    public function check_mail() {

        $user_id = "2";

        //Send Confirmation Mail to User & Admin
        UserRegistrationConfirmation($user_id);

        die();
    }
    /* Start: functions by Arunsankar */

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit User
            $this->form_validation->set_rules('first_name', 'first name', 'trim|required|callback_validate_name|xss_clean');
            $this->form_validation->set_rules('last_name', 'last name', 'trim|required|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('telephone', 'Telephone(Home)', 'trim|required|numeric|xss_clean');
            $this -> form_validation -> set_rules('mobile', 'Mobile No', 'trim|required|numeric|callback_validate_phone|callback_check_uniqueness_mobile_edit['.$id.']|xss_clean');
            $this -> form_validation -> set_rules('email', 'email', 'trim|required|valid_email|callback_check_uniqueness_email_edit['.$id.']');
//            $this->form_validation->set_rules('city', 'city', 'callback_validate_city');
            //if ($this->input->post('old_photo') == '' && empty($_FILES['profile_image']['name']))
            //$this->form_validation->set_rules('profile_image', 'Document', 'callback_file_selected_check');

            if (!$this -> form_validation -> run() == FALSE) {
                if (!empty($_FILES['profile_image']['name'])) {
                    $result = $this -> do_upload('profile_image', 'users', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    if (!isset($result['error'])) {
                        $edit_data = array(
                            'FirstName' => $this->input->post('first_name'),
                            'LastName' => $this->input->post('last_name'),
                            'Email' => $this -> input -> post('email'),
                            'TelephoneFixed' => $this -> input -> post('telephone'),
                            'Mobile' => $this -> input -> post('mobile'),
                            'UserRole' => $this -> input -> post('user_role'),
                            'StreetAddress' => $this -> input -> post('street_address'),
                            'City' => $this -> input -> post('city'),
                            'State' => $this -> input -> post('state'),
                            'PinCode' => $this -> input -> post('pin_code'),
                            'ProfileImage' => $result['upload_data']['file_name']
                        );
                        $this -> usermodel -> update_user_profile($id, $edit_data);
                        $this -> session -> set_userdata('success_message', "User updated successfully");

                        $this -> result = 1;
                    }
                    else {
                        // code to display error while image upload
                        //$this->session->set_userdata('error_message', $result['error']);
                        $this -> result = 0;
                        $this -> message = $result['error'];
                    }
                }
                else {
                    $edit_data = array(
                        'FirstName' => $this->input->post('first_name'),
                        'LastName' => $this->input->post('last_name'),
                        'Email' => $this -> input -> post('email'),
                        'TelephoneFixed' => $this -> input -> post('telephone'),
                        'Mobile' => $this -> input -> post('mobile'),
                        'UserRole' => $this -> input -> post('user_role'),
                        'StreetAddress' => $this -> input -> post('street_address'),
                        'City' => $this -> input -> post('city'),
                        'State' => $this -> input -> post('state'),
                        'PinCode' => $this -> input -> post('pin_code')
                    );

                    $this -> usermodel -> update_user_profile($id, $edit_data);
                    $this -> session -> set_userdata('success_message', "User updated successfully");
                    $this -> result = 1;
                }
            }
            else {
                //$this->session->set_userdata('error_message', 'Form validation failed');
                $this -> result = 0;
                $this -> message = $this -> form_validation -> error_array();
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

    public function web_users() {
        
        $this -> page_title = "Web/Mobile Users";
        $this -> breadcrumbs[0] = array('label' => 'User Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Web/Mobile Users', 'url' => '/users/web_users');
        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/users/web', $data);
    }
    function validate_phone($phone_number) {
        $this -> form_validation -> set_message('validate_phone', 'Please enter a valid phone number.');
        if (preg_match('/^[0-9-+()\s]+$/', $phone_number)) {
            return true;
        }
        else {
            return false;
        }
    }
    function check_uniqueness_email($email) {
        $this -> form_validation -> set_message('check_uniqueness_email', 'Email already registered.');
        return $this -> usermodel -> check_unique_email($email);
    }
    function check_uniqueness_email_edit($email,$id) {
        $this -> form_validation -> set_message('check_uniqueness_email_edit', 'Email already registered.');
        return $this -> usermodel -> check_unique_email_edit($email,$id);
    }
    function check_uniqueness_mobile($mobile) {
        $this -> form_validation -> set_message('check_uniqueness_mobile', 'Mobile No already registered.');
        return $this -> usermodel -> check_unique_mobile($mobile,$id);
    }
    function check_uniqueness_mobile_edit($mobile,$id) {
        $this -> form_validation -> set_message('check_uniqueness_mobile_edit', 'Mobile No already registered.');
        return $this -> usermodel -> check_unique_mobile_edit($mobile,$id);
    }
    /* End: functions by Arunsankar */
    
    
    public function showLoyalty($userId) {

        //$data = $this -> usermodel -> get_user_details($id);
        
        # Set defaults        
        $loyaltyDetails = array();
        $loyaltyDetails['current_date'] = date('d M Y');
        $loyaltyDetails['points_earned_from_reviews'] = 0;
        $loyaltyDetails['points_earned_from_product_shares'] = 0;
        $loyaltyDetails['points_earned_from_app_shares'] = 0;
        $loyaltyDetails['total_points_earned_this_month'] = 0;
        $loyaltyDetails['total_points_earned_to_date'] = 0;
        $loyaltyDetails['total_points_redeemed_this_month'] = 0;
        $loyaltyDetails['total_points_redeemed_to_date'] = 0;
        $loyaltyDetails['current_point_balance'] = 0;        
        
       
        
        # Get loyalty earned details till date
        $loyaltyEarnedDetails = $this -> usermodel -> get_loyalty_earned_details($userId); 
        
        if($loyaltyEarnedDetails)
        {
            $total_points_earned_to_date = $loyaltyEarnedDetails['userProductReviews'] + $loyaltyEarnedDetails['userProductShares'] + $loyaltyEarnedDetails['userAppShares'];
            $loyaltyDetails['points_earned_from_reviews']        = $loyaltyEarnedDetails['userProductReviews'];
            $loyaltyDetails['points_earned_from_product_shares'] = $loyaltyEarnedDetails['userProductShares'];
            $loyaltyDetails['points_earned_from_app_shares']     = $loyaltyEarnedDetails['userAppShares'];
            $loyaltyDetails['total_points_earned_to_date']       = $total_points_earned_to_date;
        }
        
        # Get loyalty earned details for month
        $dt         = new DateTime( date("Y-m-d") ); 
        $startDate  = date('Y-m-01')." 00:00:00";
        $endDate    = $dt->format( 'Y-m-t' )." 23:59:59";
        
        $loyaltyEarnedDetailsForMoth = $this -> usermodel -> get_loyalty_earned_details($userId, $startDate, $endDate);
        
        if($loyaltyEarnedDetailsForMoth )
        {
            $total_points_earned_this_month = $loyaltyEarnedDetailsForMoth['userProductReviews'] + $loyaltyEarnedDetailsForMoth['userProductShares'] + $loyaltyEarnedDetailsForMoth['userAppShares'];
            $loyaltyDetails['total_points_earned_this_month'] = $total_points_earned_this_month;
        }
        
        # Get loyalty redeemed details
        $redeemedDetails = $this -> usermodel -> get_loyalty_redeemed_details($userId);
        $redeemedDetailsForMonth = $this -> usermodel -> get_loyalty_redeemed_details($userId, $startDate, $endDate);
        //$loyaltyBalanceDetails = $this -> loyaltymodel -> get_loyalty_balance($userId);
                
        if($redeemedDetails )
        {
            $loyaltyDetails['total_points_redeemed_to_date'] = $redeemedDetails['loyalty_consumption'];
        }
        
         if($redeemedDetailsForMonth )
        {
            $loyaltyDetails['total_points_redeemed_this_month'] = $redeemedDetailsForMonth['loyalty_consumption'];
        }        
       
        # Get User's balance loyalty points 
        $loyaltyDetails['current_point_balance'] = $this -> usermodel -> get_loyalty_balance($userId);
        
        $data = $loyaltyDetails;
        
        $this -> breadcrumbs[] = array('label' => 'Show Loyalty', 'url' => 'users/showLoyalty/' . $userId);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $html = $this -> load -> view('admin/users/show_loyalty', $data, true);
        
        $userDetails = $this -> usermodel -> get_user_details($userId);
        
        $name = $userDetails['FirstName'].' '.$userDetails['LastName'];
        echo json_encode(array(
            'html' => $html,
            'name' => $name
        ));
    }
}
?>