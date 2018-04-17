<?php

/*
 * Author:  PM
 * Purpose: Registration Controller
 * Date:    07-10-2015
 */

class Registration extends My_Front_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
        $this -> load -> model('front/usermodel', '', TRUE);
    }

    //Function to load the registration page
    public function index() {
        $this -> load -> model('front/contentmodel', '', TRUE);

        $this -> load -> library('instagram_api');

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this -> all_categories;

        // Get terms and conditions text
        $data['terms_and_conditions'] = $this -> contentmodel -> get_content(TERMS_CONDITIONS);

        $this -> template -> front_view('front/registration', $data, 1);
    }

    // Function to register a user
    public function register() {
        $data = array();
        $this -> load -> library('instagram_api');
        // Fb registration
//        $this->load->library('email');
        if ($this -> input -> post('fb_response')) {
            $called_from = $this -> input -> post('called_from');
            $fb_data = $this -> input -> post('fb_response');

            $user_id = $this -> usermodel -> fb_login($fb_data);

            $this -> _login($user_id, $called_from, '1');
        }
        // Normal registration
        else if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            // Servere side validations for register form
            $this -> form_validation -> set_rules('first_name', 'first name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('last_name', 'last name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('telephone', 'telephone', 'trim|required|callback_validate_phone|xss_clean');
            $this -> form_validation -> set_rules('mobile_number', 'mobile number', 'trim|required|callback_validate_phone|callback_check_uniqueness_mobile|xss_clean');
            $this -> form_validation -> set_rules('email', 'email', 'trim|required|valid_email|callback_check_uniqueness_email');
            $this -> form_validation -> set_rules('password', 'password', 'trim|required|matches[confirm_password]');
            $this -> form_validation -> set_rules('confirm_password', 'confirm password', 'trim|required');
            $this -> form_validation -> set_rules('house_number', 'house No.', 'trim|required|max_length[50]|callback_no_specials|xss_clean');
            $this -> form_validation -> set_rules('street_name', 'street name', 'trim|required|max_length[50]|callback_no_specials|xss_clean');
            $this -> form_validation -> set_rules('suburb', 'suburb', 'trim|required|max_length[50]|callback_no_specials|xss_clean');
            $this -> form_validation -> set_rules('city', 'city', 'trim|required|max_length[50]|callback_letters_and_spaces|xss_clean');
            $this -> form_validation -> set_rules('province', 'province', 'trim|required|max_length[50]|callback_letters_and_spaces|xss_clean');
            $this -> form_validation -> set_rules('pin_code', 'pin code', 'trim|required|max_length[50]|numeric|xss_clean');
            $this -> form_validation -> set_rules('terms_conditions', 'terms conditions', 'trim|required|max_length[50]|xss_clean');

            if (!$this -> form_validation -> run() == FALSE) {
                $address = $this -> input -> post('house_number') . ' ' . $this -> input -> post('street_name') . ' ' . $this -> input -> post('city') . ' ' . $this -> input -> post('suburb') . ' ' . $this -> input -> post('province') . ' South Africa';
                // Get lat and long by address
                $prepAddr = str_replace(' ', '+', $address);
                $geocode = $this -> file_get_contents_curl('http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false');
                $output = json_decode($geocode);
                if ($output -> status == 'ZERO_RESULTS') {
                    $latitude = $longitude = 0;
                }
                else {
                    $latitude = $output -> results[0] -> geometry -> location -> lat;
                    $longitude = $output -> results[0] -> geometry -> location -> lng;
                }

                $full_name = $this -> input -> post('first_name') . ' ' . $this -> input -> post('last_name');
                $email_id = $this -> input -> post('email');

                $insert_data = array('FirstName' => $this -> input -> post('first_name'),
                    'LastName' => $this -> input -> post('last_name'),
                    'Email' => $this -> input -> post('email'),
                    'Password' => MD5($this -> input -> post('password')),
                    'UserRole' => USER_ROLE,
                    'HouseNumber' => $this -> input -> post('house_number'),
                    'StreetAddress' => $this -> input -> post('street_name'),
                    'City' => $this -> input -> post('city'),
                    'Suburb' => $this -> input -> post('suburb'),
                    'State' => $this -> input -> post('province'),
                    'Country' => COUNTRY,
                    'PinCode' => $this -> input -> post('pin_code'),
                    'TelephoneFixed' => $this -> input -> post('telephone'),
                    'Mobile' => $this -> input -> post('mobile_number'),
                    'Latitude' => $latitude,
                    'Longitude' => $longitude,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                    'IsActive' => 1
                );

                $user_id = $this -> usermodel -> register($insert_data);

                $this -> _login($user_id, 'registration');
            }
        }
        $this -> template -> front_view('front/registration', $data, 1);
    }

    function set_preference() {

        if (!$this -> usermodel -> check_email_entered($this -> session -> userdata('userid'))) {
            redirect(front_url() . 'registration/set_details');
            exit(0);
        }

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $data['latitude'] = $this -> input -> post('us_latitude');
            $data['longitude'] = $this -> input -> post('us_longitude');
            $data['distance'] = $this -> input -> post('us_radius_hd');
            $distance = $data['distance'];
            if ($distance > 0) {
                $distance = $distance / 1000;
            }
            $this -> usermodel -> set_location_preference($data['latitude'], $data['longitude'], $distance, $this -> session -> userdata('userid'));
        }

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this -> all_categories;

        $this -> load -> model('front/retailermodel', '', TRUE);
        //Get active retailers to be displayed that contain active not deleted store products
        $data['retailers'] = $this -> retailermodel -> get_retailers_having_store_products();

        $this -> message = $this -> load -> view('front/set_preference', $data, true);
        echo json_encode(array(
            'message' => $this -> message
        ));
        die();
    }
    /* Set session data of user
     * Called from - Login Or register for displaying appropriate message
     * fb_login - 1 : Pass Json data, 0 : Redirect
     */

    function _login($user_id, $called_from, $fb_login = 0) {
        $success = $error = '';
        if ($user_id > 0) {
            $user_details = $this -> usermodel -> get_user_details($user_id);

            if (!empty($user_details)) {
                if ($called_from == 'email') {
                    $this -> session -> set_userdata('success_message', 'Email Id  updated successfully');
                }
                else if ($called_from == 'login') {
                    $this -> session -> set_userdata('success_message', 'You are logged in successfully');
                }
                else {
                    $this -> session -> set_userdata('success_message', 'You have registered successfully');
                }

                $this -> session -> set_userdata('userid', $user_details['Id']);
                $this -> session -> set_userdata('name', $user_details['FirstName'] . " " . $user_details['LastName']);
                // $this->session->set_userdata('email',$user_details['Email']);
                // $this->session->set_userdata('image',$user_details['ProfileImage']);

                if (!$this -> usermodel -> check_email_entered($user_id)) {
                    $success = front_url() . 'registration/set_details';
                }
                else if ($this -> usermodel -> check_user_preference($user_id)) {
                    $success = front_url() . 'topoffers';
                }
                else {
                    $success = front_url() . 'registration/set_details';
                }
            }
            else {
                $error = ( $called_from == 'login' ) ? 'Sorry, error while logging in' : 'Sorry, error while registering';
            }
        }
        else {
            $error = ( $called_from == 'login' ) ? 'Sorry, error while logging in' : 'Sorry, error while registering';
        }

        if ($fb_login == 0) {
            $this -> session -> set_userdata('error_message', $error);
            redirect($success);
            die();
        }
        else {
            echo json_encode(array('success' => $success, 'error' => $error));
            die();
        }
    }

    // Custom validation check for name
    function validate_name($name) {
        if ($name) {
            $this -> form_validation -> set_message('validate_name', 'Name must contain contain only letters, apostrophe, spaces or dashes.');
            if (preg_match("/^[a-zA-Z'\-\s]+$/", $name)) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return true;
        }
    }

    // Custom validation check for a field to have only letters and spaces
    function letters_and_spaces($house_number) {
        $this -> form_validation -> set_message('letters_and_spaces', 'This field can contain only letters and spaces.');
        if (preg_match('/^[a-zA-Z\s]+$/', $house_number)) {
            return true;
        }
        else {
            return false;
        }
    }

    // Custom validation check for a field to have no special characters
    function no_specials($street_name) {
        $this -> form_validation -> set_message('no_specials', 'This field can not contain special characters.');
        if (preg_match('/^[a-zA-Z0-9\'\"\,\/\-\s]+$/', $street_name)) {
            return true;
        }
        else {
            return false;
        }
    }

    // Custom validation check for phone number
    function validate_phone($phone_number) {
        $this -> form_validation -> set_message('validate_phone', 'Please enter a valid phone number.');
        if (preg_match('/^[0-9-+()\s]+$/', $phone_number)) {
            return true;
        }
        else {
            return false;
        }
    }

    // Custom validation check for unique email
    function check_uniqueness_email($email) {
        $this -> form_validation -> set_message('check_uniqueness_email', 'Email already registered.');
        return $this -> usermodel -> check_unique_email($email);
    }

    function check_uniqueness_email_edit($email, $id) {
        $this -> form_validation -> set_message('check_uniqueness_email_edit', 'Email already registered.');
        return $this -> usermodel -> check_unique_email_edit($email, $id);
    }

    function check_uniqueness_mobile($mobile) {
        $this -> form_validation -> set_message('check_uniqueness_mobile', 'Mobile No already registered.');
        return $this -> usermodel -> check_unique_mobile($mobile, $id);
    }

    function check_uniqueness_mobile_edit($mobile, $id) {
        $this -> form_validation -> set_message('check_uniqueness_mobile_edit', 'Mobile No already registered.');
        return $this -> usermodel -> check_unique_mobile_edit($mobile, $id);
    }

    function file_get_contents_curl($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    // https://github.com/epoberezkin/twitter-codeigniter
    /* redirect to Twitter for authentication */
    public function twitter_redirect($called_from) {
        $this -> session -> set_userdata('called_from', $called_from);
        $this -> load -> library('twconnect');

        /* twredirect() parameter - callback point in your application */
        /* by default the path from config file will be used */
        $ok = $this -> twconnect -> twredirect();

        if (!$ok) {
            echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
    }
    /* return point from Twitter */
    /* you have to call $this->twconnect->twprocess_callback() here! */

    public function twitter_callback() {
        $this -> load -> library('twconnect');

        $ok = $this -> twconnect -> twprocess_callback();

        if ($ok) {
            // saves Twitter user information to $this->twconnect->tw_user_info
            // twaccount_verify_credentials returns the same information
            $this -> twconnect -> twaccount_verify_credentials();

            $twitter_user_info = $this -> twconnect -> tw_user_info;
//            echo '<pre>';
//            print_r($twitter_user_info);die;

            $user_id = $this -> usermodel -> twitter_login($twitter_user_info -> screen_name, $twitter_user_info -> name,$twitter_user_info -> id);

            // Destroy the session as it creates problem ( gives failure ) when next login with twitter
            // $this->session->sess_destroy();
            $this -> _login($user_id, $this -> session -> userdata('called_from'));
            $this -> session -> unset_userdata('called_from');
        }
        else {
            // $this->session->sess_destroy();
            $this -> session -> set_userdata('error_message', 'Twitter connect failed');
            redirect(front_url() . '');
        }
    }

    public function instagram_callback() {
        $this -> load -> library('instagram_api');
        $auth_response = $this -> instagram_api -> authorize($_GET['code']);

        if ($auth_response) {
            $user_id = $this -> usermodel -> instagram_login($auth_response);
            $this -> _login($user_id, $this -> session -> userdata('called_from'));
            $this -> session -> unset_userdata('called_from');
        }
        else {
            // $this->session->sess_destroy();
            $this -> session -> set_userdata('error_message', 'Instagram connect failed');
            redirect(front_url() . '');
        }

//        echo $auth_response->access_token;
//        echo '<br />';
//        echo $auth_response->user->username;
//        echo '<br />';
//        echo $auth_response->user->profile_picture;
//        echo '<br />';
//        echo $auth_response->user->full_name;
//        echo '<br />';
//        echo $auth_response->user->id;
//        echo '<br />';
    }
    
    public function google_callback() {
//        $this -> load -> library('instagram_api');
//        $auth_response = $this -> instagram_api -> authorize($_GET['code']);
//
//        if ($auth_response) {
//            $user_id = $this -> usermodel -> instagram_login($auth_response);
//            $this -> _login($user_id, $this -> session -> userdata('called_from'));
//            $this -> session -> unset_userdata('called_from');
//        }
//        else {
//            // $this->session->sess_destroy();
//            $this -> session -> set_userdata('error_message', 'Instagram connect failed');
//            redirect(front_url() . '');
//        }

    }

    // Function to display nearest store
    public function get_stores($retailer_id) {

        $this -> load -> model('front/retailermodel', '', TRUE);

        //Get active retailers to be displayed that contain active not deleted store products
        $stores = $this -> retailermodel -> get_nearest_store($retailer_id, $limit = 5);

        $html = '';
        if (!empty($stores)) {
            foreach ($stores as $store) {

                $html.='<li><a href="javascript:void(0);" data-store-id="' . $store['Id'] . '">
                                                   ' . $store['StoreName'] .'('.round($store['distance'],2).' KM)'. '
                                                </a>
                                            </li>';
            }
        }

        echo $html;
    }

    //Save user preference
    public function save_user_preference() {

        $retailer_id = $this -> input -> post('pref_retailers');

        $store_id = $this -> input -> post('pref_stores');

        $user_id = $this -> session -> userdata('userid');

        $this -> load -> model('front/retailermodel', '', TRUE);

        $this -> retailermodel -> insert_user_preference($user_id, $retailer_id, $store_id);

        redirect(front_url() . 'topoffers');
    }

    public function edit_profile() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
//            echo '<pre>';
//            print_r($_POST);die;
            // Servere side validations for register form
            $this -> form_validation -> set_rules('first_name', 'first name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('last_name', 'last name', 'trim|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('telephone', 'telephone', 'trim|required|callback_validate_phone|xss_clean');
            $this -> form_validation -> set_rules('mobile_number', 'mobile number', 'trim|required|callback_validate_phone|callback_check_uniqueness_mobile_edit[' . $this -> session -> userdata('userid') . ']|xss_clean');
            $this -> form_validation -> set_rules('email', 'Email', 'trim|required|valid_email|callback_check_uniqueness_email_edit[' . $this -> session -> userdata('userid') . ']');
            //$this -> form_validation -> set_rules('house_number', 'house No.', 'trim|required|max_length[50]|callback_no_specials|xss_clean');
            $this -> form_validation -> set_rules('us_address', 'Address', 'trim|required|max_length[150]|callback_no_specials|xss_clean');
            //$this -> form_validation -> set_rules('suburb', 'suburb', 'trim|required|max_length[50]|callback_no_specials|xss_clean');
            $this -> form_validation -> set_rules('city', 'city', 'trim|required|max_length[50]|callback_letters_and_spaces|xss_clean');
            $this -> form_validation -> set_rules('province', 'province', 'trim|required|numeric|xss_clean');
            $this -> form_validation -> set_rules('pin_code', 'pin code', 'trim|required|max_length[50]|numeric|xss_clean');
            $this -> form_validation -> set_rules('us_latitude', 'Latitude', 'trim|xss_clean|required');
            $this -> form_validation -> set_rules('us_longitude', 'Longitude', 'trim|xss_clean|required');
            $this -> form_validation -> set_rules('us_radius_hd', 'radius', 'trim|numeric|xss_clean');

            if (!$this -> form_validation -> run() == FALSE) {
                $province = $this -> usermodel -> get_states($this -> input -> post('province'));
                $address = str_replace(', South Africa', $this -> input -> post('us_address')) . ' ' . $this -> input -> post('city') . ' ' . $province[0]['Name'] . ' South Africa';
                // Get lat and long by address
                /* $prepAddr = str_replace(' ', '+', $address);
                  $geocode = $this -> file_get_contents_curl('http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false');
                  $output = json_decode($geocode);
                  if ($output -> status == 'ZERO_RESULTS') {
                  $latitude = $longitude = 0;
                  }
                  else { */
                $latitude = $this -> input -> post('us_latitude');
                $longitude = $this -> input -> post('us_longitude');
                //}

                $radius = $this -> input -> post('us_radius_hd');
                if ($radius > 0) {
                    $radius = $radius / 1000;
                }

                $update_data = array('FirstName' => $this -> input -> post('first_name'),
                    'LastName' => $this -> input -> post('last_name'),
                    'Email' => $this -> input -> post('email'),
                    'HouseNumber' => str_replace(', South Africa', '', $this -> input -> post('us_address')),
                    //'StreetAddress' => $this -> input -> post('street_name'),
                    'City' => $this -> input -> post('city'),
                    //'Suburb' => $this -> input -> post('suburb'),
                    'State' => $this -> input -> post('province'),
                    'Country' => COUNTRY,
                    'PinCode' => $this -> input -> post('pin_code'),
                    'TelephoneFixed' => $this -> input -> post('telephone'),
                    'Mobile' => $this -> input -> post('mobile_number'),
                    'Latitude' => $latitude,
                    'Longitude' => $longitude,
                    'ModifiedOn' => date('Y-m-d H:i:s'),
                    'ModifiedBy' => $this -> session -> userdata('userid'),
                    'PrefLatitude' => $this -> input -> post('us_latitude'),
                    'PrefLongitude' => $this -> input -> post('us_longitude'),
                    'PrefDistance' => $radius
                );
                if (!empty($_FILES['profile_image']['name'])) {
                    $result = $this -> do_upload('profile_image', 'users', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    if (!isset($result['error'])) {
                        $update_data['ProfileImage'] = $result['upload_data']['file_name'];
                    }
                }
                $result = $this -> usermodel -> edit_profile($update_data);

                if ($result) {
                    $this -> session -> set_userdata('success_message', 'Profile updated successfully');
                    $this -> session -> set_userdata('name', $this -> input -> post('first_name') . " " . $this -> input -> post('last_name'));
                }
                else {
                    $this -> session -> set_userdata('error_message', 'Error while updating profile');
                }
                redirect(front_url() . 'my_profile');
            }
            else {
                $this -> session -> set_userdata('error_message', 'Error in form. Fields with * are mandatory');
                redirect(front_url() . 'my_profile');
            }
        }
    }
    /* Start: functions by Arunsankar */

    /**
     * Function to check whether the user is alreasy registered
     * @author Arunsankar
     * @date 14-Sept-2016
     */
    function check_is_logged_in() {
        if ($this -> session -> userdata('userid')) {
            echo json_encode(array('success' => '1', 'error' => ''));
        }
        else {
            echo json_encode(array('success' => '', 'error' => '1'));
        }
    }

    function set_details($id = '') {
        $data['userid'] = $this -> session -> userdata('userid');
        $this -> template -> front_view('front/set_details', $data, 1);
    }

    function set_email() {
        $this -> load -> model('front/retailermodel', '', TRUE);
        $id = $this -> session -> userdata('userid');
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> form_validation -> set_rules('email', 'Email', 'trim|required|valid_email|callback_check_uniqueness_email');
            if (!$this -> form_validation -> run() == FALSE) {
                $email = $this -> input -> post('email');
                if ($this -> usermodel -> set_email($id, $email)) {
                    $this -> result = 1;
                    $this -> message = 'Email added successfully';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to add email';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Email already registered';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Failed to add email';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    function check_details() {
        $is_email_set = FALSE;
        $is_preference_set = FALSE;
        $is_loc_preference_set = FALSE;
        $mes = '';
        $page = '';
        $data['userid'] = $this -> session -> userdata('userid');
        if ($this -> usermodel -> check_email_entered($data['userid'])) {
            $is_email_set = TRUE;
            if ($this -> usermodel -> check_location_preference($data['userid'])) {
                $is_loc_preference_set = TRUE;
                if ($this -> usermodel -> get_user_preferred_retailer($data['userid'])) {
                    $is_preference_set = TRUE;
                }
            }
        }

        if (!$is_email_set) {
            $mes = 'email';
        }
        elseif (!$is_loc_preference_set) {
            $mes = 'location';
        }
        elseif (!$is_preference_set) {
            $mes = 'preference';
        }
        if ($mes == 'email') {
            $page = $this -> load -> view('front/set_email', $data, TRUE);
        }
        if ($mes == 'location') {
            $page = $this -> load -> view('front/set_location', $data, TRUE);
        }
        if ($mes == 'preference') {
            //Get active parent categories to be displayed that contain active not deleted products
            $data['categories'] = $this -> categories;

            //Get all active categories to be displayed that contain active not deleted products
            $data['all_categories'] = $this -> all_categories;

            $this -> load -> model('front/retailermodel', '', TRUE);
            //Get active retailers to be displayed that contain active not deleted store products
            $data['retailers'] = $this -> retailermodel -> get_retailers_having_store_products();
            $page = $this -> load -> view('front/set_preference', $data, TRUE);
        }
        if ($mes == '') {
            $page = 'success';
        }
        echo json_encode(array(
            'message' => $page,
            'page' => $mes
        ));
    }
    /* End: functions by Arunsankar */
}
