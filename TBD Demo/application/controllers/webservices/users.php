<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:PHN
 * Purpose: User Webservices
 * Date:02-09-2015
 * Dependency: usermodel.php
 */

class Users extends REST_Controller {

    function __construct() {
        parent::__construct();
        $api_key = $this -> post('api_key');

        validateApiKey($api_key);

        $retArr = array();

        $this -> load -> model('webservices/usermodel', '', TRUE);
        $this -> load -> model('webservices/statemodel', '', TRUE);
        $this -> load -> model('webservices/devicemodel', '', TRUE);
    }
    /*
     * Check user login
     */

    /*
     * Method Name: Login
     * Purpose: To verify login credentials.
     * params:
     *      input: Username, password
     *      output: status - FAIL / SUCCESS
     *              message - The reason if login fails / Success message
     *              userDetails - Array containing all the details for logged in user, if login is successful.
     */

    public function login_post() {
        //Parameters
        $email = $this -> post("email") ? $this -> post("email") : "";
        $password = $this -> post("password") ? $this -> post("password") : "";
        $fb_uid = $this -> post("fb_uid") ? $this -> post("fb_uid") : "";
        $sign_from = $this -> post("sign_from") ? $this -> post("sign_from") : "";
        $screen_name = $this -> post("screen_name") ? $this -> post("screen_name") : "";
        $twitter_id = $this -> post("twitter_id") ? $this -> post("twitter_id") : "";
        $twitter_img = $this -> post("twitter_img") ? $this -> post("twitter_img") : "";
        $dev_device_id = $this -> post("device_token") ? $this -> post("device_token") : "";
        $dev_device_type = $this -> post("device_type") ? $this -> post("device_type") : "";
        $gp_id = $this -> post("gp_id") ? $this -> post("gp_id") : "";
        $gp_img = $this -> post("gp_img") ? $this -> post("gp_img") : "";

        //Login Using Facebook
        if ($sign_from == 'F') {

            $userDetails = $this -> usermodel -> loginByFacebook($fb_uid);
            if ($userDetails) {
                $this -> devicemodel -> add_device($userDetails['Id'], $dev_device_id, $dev_device_type);
                //If data is found, return the data with success message...
                $retArr['status'] = SUCCESS;
                $retArr['message'] = VALID_USER_CREDENTIALS;
                if ($userDetails['ProfileImage'] != '')
                    $userDetails['ProfileImage'] = (front_url() . USER_IMAGE_PATH . "medium/" . $userDetails['ProfileImage']);
                else
                    $userDetails['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);
                $retArr['userDetails'] = array($userDetails);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                //If no data found, then register the user with the facebook id and username and blank password
                $time = date("Y-m-d H:i:s");

                $first_name = $this -> post('first_name') ? $this -> post('first_name') : "";
                $last_name = $this -> post('last_name') ? $this -> post('last_name') : "";
                $email = $this -> post('email') ? $this -> post('email') : "";

                // Check if email address already exists

                if (!$this -> usermodel -> check_email($email)) {

                    $ImageName = generateRandomString(20) . '.jpg';
                    $large = $this -> DownloadImage('https://graph.facebook.com/' . $fb_uid . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/large/' . $ImageName);
                    $medium = $this -> DownloadImage('https://graph.facebook.com/' . $fb_uid . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/medium/' . $ImageName);
                    $small = $this -> DownloadImage('https://graph.facebook.com/' . $fb_uid . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/small/' . $ImageName);
                    $original = $this -> DownloadImage('https://graph.facebook.com/' . $fb_uid . '/picture?type=large&access_token=176514089453791|8a5718f25f7e959d5e22024cf76f264d', './assets/images/users/original/' . $ImageName);
                    if (!$large || !$medium || !$small || !$original) {
                        $ImageName = '';
                    }

                    $insert_data = array(
                        'FirstName' => $first_name,
                        'LastName' => $last_name,
                        'ProfileImage' => $ImageName,
                        'Email' => $email,
                        'FacebookId' => $fb_uid,
                        'UserRole' => 'Users',
                        'CreatedOn' => $time
                    );

                    $result = $this -> usermodel -> add_user($insert_data);

                    if ($result) {
                        // Register a device for user
                        $this -> devicemodel -> add_device($result, $dev_device_id, $dev_device_type);

                        $insert_data['ProfileImage'] = "";
                        unset($insert_data['created_on']);

                        $insert_data['id'] = $result;
                        $retArr['status'] = SUCCESS;
                        $retArr['message'] = VALID_USER_CREDENTIALS;
                        $retArr['userDetails'] = array($insert_data);
                        $this -> response($retArr, 200); // 200 being the HTTP response code
                        die;
                    }
                    else {
                        $insert_data['ProfileImage'] = "";
                        $retArr['status'] = FAIL;
                        $retArr['message'] = REGISTRATION_FAILED;
                        $retArr['userDetails'] = array($insert_data);
                        $this -> response($retArr, 200); // 200 being the HTTP response code
                        die;
                    }
                }
                else {
                    $retArr['status'] = FAIL;
                    $retArr['message'] = EMAIL_EXISTS;
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
            }
        }
        elseif ($sign_from == 'T') {

            //If no data found, then register the user with the twitter screen name
            $time = date("Y-m-d H:i:s");

            $first_name = $this -> post('first_name') ? $this -> post('first_name') : "";
            $last_name = $this -> post('last_name') ? $this -> post('last_name') : "";
            $email = $this -> post('email') ? $this -> post('email') : "";

            $userDetails = $this -> usermodel -> loginByTwitter($twitter_id);

            if ($userDetails) {

                $this -> devicemodel -> add_device($userDetails['Id'], $dev_device_id, $dev_device_type);
                //If data is found, return the data with success message...
                $retArr['status'] = SUCCESS;
                $retArr['message'] = VALID_USER_CREDENTIALS;
                if ($userDetails['ProfileImage'] != '')
                    $userDetails['ProfileImage'] = (front_url() . USER_IMAGE_PATH . "medium/" . $userDetails['ProfileImage']);
                else
                    $userDetails['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);
                $retArr['userDetails'] = array($userDetails);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            } else {
                $ImageName = generateRandomString(20) . '.jpg';
                if ($twitter_img) {
                    $twitter_img = str_replace('_normal', '', $twitter_img);
                    $large = $this -> DownloadImage($twitter_img, './assets/images/users/large/' . $ImageName);
                    $medium = $this -> DownloadImage($twitter_img, './assets/images/users/medium/' . $ImageName);
                    $small = $this -> DownloadImage($twitter_img, './assets/images/users/small/' . $ImageName);
                    $original = $this -> DownloadImage($twitter_img, './assets/images/users/original/' . $ImageName);
                    if (!$large || !$medium || !$small || !$original) {
                        $ImageName = '';
                    }
                }
                else {
                    $ImageName = '';
                }


                $insert_data = array(
                    'FirstName' => $first_name,
                    'LastName' => $last_name,
                    'ProfileImage' => $ImageName,
                    'Email' => $email,
                    'UserScreenName' => $screen_name,
                    'TwitterId' => $twitter_id,
                    'UserRole' => 'Users',
                    'CreatedOn' => $time
                );

                $result = $this -> usermodel -> add_user($insert_data);

                if ($result) {

                    // Register a device for user
                    $this -> devicemodel -> add_device($result, $dev_device_id, $dev_device_type);

                    $insert_data['ProfileImage'] = "";
                    unset($insert_data['created_on']);

                    $insert_data['id'] = $result;
                    $retArr['status'] = SUCCESS;
                    $retArr['message'] = VALID_USER_CREDENTIALS;
                    $retArr['userDetails'] = array($insert_data);
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
                else {
                    $insert_data['ProfileImage'] = "";
                    $retArr['status'] = FAIL;
                    $retArr['message'] = REGISTRATION_FAILED;
                    $retArr['userDetails'] = array($insert_data);
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
            }
        }
        elseif ($sign_from == 'G') {
            $userDetails = $this -> usermodel -> loginByGoogle($gp_id);
            if ($userDetails) {
                $this -> devicemodel -> add_device($userDetails['Id'], $dev_device_id, $dev_device_type);
                //If data is found, return the data with success message...
                $retArr['status'] = SUCCESS;
                $retArr['message'] = VALID_USER_CREDENTIALS;
                if ($userDetails['ProfileImage'] != '')
                    $userDetails['ProfileImage'] = (front_url() . USER_IMAGE_PATH . "medium/" . $userDetails['ProfileImage']);
                else
                    $userDetails['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);
                $retArr['userDetails'] = array($userDetails);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                //If no data found, then register the user with the google id and username and blank password
                $time = date("Y-m-d H:i:s");

                $first_name = $this -> post('first_name') ? $this -> post('first_name') : "";
                $last_name = $this -> post('last_name') ? $this -> post('last_name') : "";
                $email = $this -> post('email') ? $this -> post('email') : "";

                // Check if email address already exists

                if (!$this -> usermodel -> check_email($email)) {

                    $ImageName = generateRandomString(20) . '.jpg';

                    if ($gp_img) {
                        $large = $this -> DownloadImage($gp_img, './assets/images/users/large/' . $ImageName);
                        $medium = $this -> DownloadImage($gp_img, './assets/images/users/medium/' . $ImageName);
                        $small = $this -> DownloadImage($gp_img, './assets/images/users/small/' . $ImageName);
                        $original = $this -> DownloadImage($gp_img, './assets/images/users/original/' . $ImageName);
                        if (!$large || !$medium || !$small || !$original) {
                            $ImageName = '';
                        }
                    }
                    else {
                        $ImageName = '';
                    }

                    $insert_data = array(
                        'FirstName' => $first_name,
                        'LastName' => $last_name,
                        'ProfileImage' => $ImageName,
                        'Email' => $email,
                        'GoogleId' => $gp_id,
                        'UserRole' => 'Users',
                        'CreatedOn' => $time
                    );

                    $result = $this -> usermodel -> add_user($insert_data);

                    if ($result) {
                        // Register a device for user
                        $this -> devicemodel -> add_device($result, $dev_device_id, $dev_device_type);

                        $insert_data['ProfileImage'] = "";
                        unset($insert_data['created_on']);

                        $insert_data['id'] = $result;
                        $retArr['status'] = SUCCESS;
                        $retArr['message'] = VALID_USER_CREDENTIALS;
                        $retArr['userDetails'] = array($insert_data);
                        $this -> response($retArr, 200); // 200 being the HTTP response code
                        die;
                    }
                    else {
                        $insert_data['ProfileImage'] = "";
                        $retArr['status'] = FAIL;
                        $retArr['message'] = REGISTRATION_FAILED;
                        $retArr['userDetails'] = array($insert_data);
                        $this -> response($retArr, 200); // 200 being the HTTP response code
                        die;
                    }
                }
                else {
                    $retArr['status'] = FAIL;
                    $retArr['message'] = EMAIL_EXISTS;
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
            }
        }
        else {

            if ($email && $password) {

                $userDetails = $this -> usermodel -> login($email, $password);

                if ($userDetails) {
                    // Register a device for user
                    $this -> devicemodel -> add_device($userDetails['Id'], $dev_device_id, $dev_device_type);

                    if ($userDetails['ProfileImage'])
                        $userDetails['ProfileImage'] = (front_url() . USER_IMAGE_PATH . "medium/" . $userDetails['ProfileImage']);
                    else
                        $userDetails['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);
                    $retArr['status'] = SUCCESS;
                    $retArr['message'] = VALID_USER_CREDENTIALS;
                    $retArr['userDetails'] = array($userDetails);
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
                else {
                    $retArr['status'] = FAIL;
                    $retArr['message'] = INVALID_USER_CREDENTIALS;
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = INVALID_USER_CREDENTIALS;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
    }

    /**
     * Purpose: User Registration
     * Output Parameter:
     *          if successful - Array containing newly registered user
     *          else - Fail status and reason for failure.
     * */
    public function registration_post() {

        $time = date("Y-m-d H:i:s");
        $email = $this -> post('email') ? $this -> post('email') : "";
        $first_name = $this -> post('first_name') ? $this -> post('first_name') : "";
        $last_name = $this -> post('last_name') ? $this -> post('last_name') : "";
        $telephone_fixed = $this -> post('telephone_fixed') ? $this -> post('telephone_fixed') : "";
        $telephone_mob = $this -> post('telephone_mob') ? $this -> post('telephone_mob') : "";
        $password = $this -> post('password') ? $this -> post('password') : "";
        //$houseNo = $this->post('house_No') ? $this->post('house_No') : "";
        $streetName = $this -> post('street_name') ? $this -> post('street_name') : "";
        //$suburb = $this->post('suburb') ? $this->post('suburb') : "";
        $city = $this -> post('city') ? $this -> post('city') : "";
        $state = $this -> post('province') ? $this -> post('province') : "";
        //$pincode = $this->post('pincode') ? $this->post('pincode') : "";
        $latitude = $this -> post('latitude') ? $this -> post('latitude') : "";
        $longitude = $this -> post('longitude') ? $this -> post('longitude') : "";
        $dob = $this -> post('dob') ? $this -> post('dob') : "";
        $gender = $this -> post('gender') ? $this -> post('gender') : "";

        $dev_device_id = $this -> post("device_token") ? $this -> post("device_token") : "";
        $dev_device_type = $this -> post("device_type") ? $this -> post("device_type") : "";
        $referer_user_id = $this -> post('referer_user_id') ? $this -> post('referer_user_id') : "";

        if (!preg_match("/^([\w\-\.]+)@((\[([0-9]{1,3}\.){3}[0-9]{1,3}\])|(([\w\-]+\.)+)([a-zA-Z]{2,4}))$/", $email)) {
            $retArr['status'] = FAIL;
            $retArr['message'] = INVALID_EMAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }

        // Check if email address already exists

        if (!$this -> usermodel -> check_email($email)) {

            $insert_data = array(
                'FirstName' => $first_name,
                'LastName' => $last_name,
                'Email' => $email,
                'Password' => md5($password),
                //'HouseNumber' => $houseNo,
                'StreetAddress' => $streetName,
                //'Suburb' => $suburb,
                'city' => $city,
                'State' => $state,
                'Country' => '1',
                //'PinCode' => $pincode,
                'Latitude' => $latitude,
                'UserRole' => USER_ROLE,
                'Longitude' => $longitude,
                'TelephoneFixed' => $telephone_fixed,
                'Mobile' => $telephone_mob,
                'CreatedOn' => $time,
                'DateOfBirth' => date("Y-m-d", strtotime($dob)),
                'Gender' => $gender,
                'RefererUserId'=> $referer_user_id
            );

            $result = $this -> usermodel -> add_user($insert_data);
            //$result = FALSE;
            if ($result) {

                // Register a device for user
                $this -> devicemodel -> add_device($result, $dev_device_id, $dev_device_type);

                $retArr['UserId'] = $result;
                $retArr['status'] = SUCCESS;
                $retArr['message'] = REGISTRATION_SUCCESS;
                $retArr['userDetails'] = array($insert_data);
                $this -> response($retArr, 200); // 200 being the HTTP response code
            }
            else {

                $retArr['status'] = FAIL;
                $retArr['message'] = REGISTRATION_FAILED;
                $retArr['userDetails'] = array($insert_data);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {

            $retArr['status'] = FAIL;
            $retArr['message'] = EMAIL_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    /**
     * Get user details
     */
    public function get_user_details_post() {
        $id = $this -> post('user_id');

        $user_details = $this -> usermodel -> get_user_details_by_id($id);

        if ($user_details) {
            //replace null values
            foreach ($user_details as $key => $value) {
                if (is_null($value)) {
                    $user_details[$key] = "";
                }
            }

            //Set path for image
            if ($user_details['ProfileImage'])
                $user_details['ProfileImage'] = (front_url() . USER_IMAGE_PATH . "medium/" . $user_details['ProfileImage']);
            else
                $user_details['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);

            $retArr['status'] = SUCCESS;
            $retArr['user_details'] = ($user_details);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'No details found';
            $retArr['user_details'] = [];
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Purpose: Edit Registration
     * Output Parameter:
     * Update user details
     * */
    public function edit_post() {
        $time = date("Y-m-d H:i:s");
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $first_name = $this -> post('first_name') ? $this -> post('first_name') : "";
        $last_name = $this -> post('last_name') ? $this -> post('last_name') : "";
        //$email = $this -> post('email') ? $this -> post('email') : "";
        $telephone_fixed = $this -> post('telephone_fixed') ? $this -> post('telephone_fixed') : "";
        $telephone_mob = $this -> post('telephone_mob') ? $this -> post('telephone_mob') : "";
        $houseNo = $this -> post('house_No') ? $this -> post('house_No') : "";
        $streetName = $this -> post('street_name') ? $this -> post('street_name') : "";
        $suburb = $this -> post('suburb') ? $this -> post('suburb') : "";
        $city = $this -> post('city') ? $this -> post('city') : "";
        $state = $this -> post('province') ? $this -> post('province') : "";
        $pincode = $this -> post('pincode') ? $this -> post('pincode') : "";
        $latitude = $this -> post('latitude') ? $this -> post('latitude') : "";
        $longitude = $this -> post('longitude') ? $this -> post('longitude') : "";
        $dob = $this -> post('dob') ? $this -> post('dob') : "";
        $gender = $this -> post('gender') ? $this -> post('gender') : "M";
        $distance = $this -> post('distance') ? $this -> post('distance') : "0";
        $referer_user_id = $this -> post('referer_user_id') ? $this -> post('referer_user_id') : "";
        
        //$email_allowed = $this -> usermodel -> check_email_edit($email, $user_id);
        $email_allowed = TRUE;
        if ($email_allowed) {
            $update_data = array(
                'FirstName' => $first_name,
                'LastName' => $last_name,
                //'Email' => $email,
                'HouseNumber' => $houseNo,
                'StreetAddress' => $streetName,
                'Suburb' => $suburb,
                'city' => $city,
                'State' => $state,
                'Country' => '1',
                'PinCode' => $pincode,
                'Latitude' => $latitude,
                'Longitude' => $longitude,
                'PrefLatitude' => $latitude,
                'PrefLongitude' => $longitude,
                'PrefDistance' => $distance,
                'TelephoneFixed' => $telephone_fixed,
                'Mobile' => $telephone_mob,
                'ModifiedOn' => $time,
                'DateOfBirth' => date("Y-m-d", strtotime($dob)),
                'Gender' => $gender,
                'RefererUserId'=> $referer_user_id
            );



            $result = $this -> usermodel -> update_user($user_id, $update_data);

            if ($result) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = PROFILE_UPDATE_SUCCESS;
                $retArr['userDetails'] = $update_data;
                $this -> response($retArr, 200); // 200 being the HTTP response code
            }
            else {

                $retArr['status'] = FAIL;
                $retArr['message'] = PROFILE_UPDATE_FAILED;
                $retArr['userDetails'] = $update_data;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {

            $retArr['status'] = FAIL;
            $retArr['message'] = EMAIL_EXISTS;
            $retArr['userDetails'] = $update_data;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Purpose: To change the password
     */
    public function change_password_post() {
        $old_password = $this -> post("old_password");
        $new_password = $this -> post("new_password");
        $id = $this -> post('user_id');
        $retArr = array();

        //-----VERIFY IF THE OLD PASSWORD MATCHES THE NEW PASSWORD-----
        $passwordVerify = $this -> usermodel -> check_old_password($id, $old_password);
        if ($passwordVerify) {
            //-----UPDATE THE PASSWORD---

            $result = $this -> usermodel -> change_password($id, $new_password);

            //-----RETURN SUCCESS MESSAGE ON SUCCESSFUL PASSWORD CHANGE---
            if ($result) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = PASSWORD_UPDATE_SUCCESS;
                $this -> response($retArr, 200); // 200 being the HTTP response code
            }
            else {
                //-----RETURN FAILURE MESSAGE ON UNSUCCESSFUL ATTEMPT
                $retArr['status'] = FAIL;
                $retArr['message'] = PASSWORD_UPDATE_FAILED;
                $this -> response($retArr, 200); // 200 being the HTTP response code
            }
        }
        else {
            //----RETURN MESSAGE IF NEWLY TYPED PASSWORD AND NEW PASSWORD DO NOT MATCH
            $retArr['status'] = FAIL;
            $retArr['message'] = PASSWORD_MISMATCH;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    /**
     * Purpose: To retrieve password using mail...
     */
    public function forget_password_post() {
        $email = $this -> post("email");
        $retArr = array();
        if ($email) {
            $userDetails = $this -> usermodel -> get_user_details_by_email($email);

            if ($userDetails) {
                if ($userDetails['IsActive'] == 1) {

                    $token = $this -> usermodel -> set_password_reset_token($userDetails['Id']);

                    //$reset_password_link = front_url() . 'reset_password?tkn=' . $token;
                    $reset_password_link = front_url() . 'reset_password/' . $token;

                    //Set message
                    $message = "Hi " . $userDetails['FirstName'] . "<br />";

                    $message .= "<p>Your password reset link for TBD is as " . $reset_password_link . "</p>";

                    $message .= "<p>Thanks</p>";

                    //Set email sending parameters
                    $this -> load -> library('email');
                    $this -> email -> from($this -> config -> item('system_email_address'), 'TBD');
                    $this -> email -> to($email);
                    $this -> email -> subject('TBD - Password Reset');
                    $this -> email -> message($message);
                    $isSent = $this -> email -> send();

                    //$result = TRUE;
                    if ($isSent) {
                        $retArr['status'] = SUCCESS;
                        $retArr['message'] = EMAIL_SENT;
                        $this -> response($retArr, 200); // 200 being the HTTP response code
                    }
                    else {
                        $retArr['status'] = FAIL;
                        $retArr['message'] = EMAIL_SEND_FAILED;
                        $this -> response($retArr, 200); // 200 being the HTTP response code
                    }
                }
                else {
                    //-----RETURN FAILURE MESSAGE ON UNSUCCESSFUL ATTEMPT
                    $retArr['status'] = FAIL;
                    $retArr['message'] = ACCOUNT_INACTIVE;
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                }
            }
            else {
                //-----RETURN FAILURE MESSAGE ON UNSUCCESSFUL ATTEMPT
                $retArr['status'] = FAIL;
                $retArr['message'] = EMAIL_NOT_FOUND;
                $this -> response($retArr, 200); // 200 being the HTTP response code
            }
        }
        else {
            //-----RETURN FAILURE MESSAGE ON UNSUCCESSFUL ATTEMPT
            $retArr['status'] = FAIL;
            $retArr['message'] = INVALID_EMAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }
    /*
     *  Save the user settings for notification
     */

    public function save_user_setting_post() {

        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $update_data = array();
        if (null !== ($this -> post('wishlist_notification'))) {
            $update_data['WishlistNotification'] = $this -> post('wishlist_notification');
        }

        if (null !== ($this -> post('favorite_notification'))) {
            $update_data['FavoriteNotification'] = $this -> post('favorite_notification');
        }

        if (null !== ($this -> post('other_notification'))) {
            $update_data['OtherRetailer'] = $this -> post('other_notification');
        }


        $result = $this -> usermodel -> save_notification_setting($user_id, $update_data);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Settings saved successfully";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {

            $retArr['status'] = FAIL;
            $retArr['message'] = "Error in saving your settings";
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    /**
     * Get user setting details
     */
    public function get_user_setting_post() {
        $id = $this -> post('user_id');

        $user_details = $this -> usermodel -> get_notification_setting($id);

        $retArr['status'] = SUCCESS;
        $retArr['user_details'] = $user_details ? $user_details : '';
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    public function states_post() {
        $states = $this -> statemodel -> get_states();
        $retArr['status'] = SUCCESS;
        $retArr['states'] = $states;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    public function check_fb_email_post() {
        $fb_uid = $this -> post('fb_uid');
        $isExists = $this -> usermodel -> check_fb_email_exists($fb_uid);
        if ($isExists) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = 'Email id is available';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Email id not available";
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function check_tw_email_post() {
        $tw_uid = $this -> post('tw_uid');
        $isExists = $this -> usermodel -> check_tw_email_exists($tw_uid);
        if ($isExists) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = 'Email id is available';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Email id not available";
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function update_fb_email_post() {
        $fb_uid = $this -> post('fb_uid');
        $email = $this -> post('email');
        if (!$this -> usermodel -> check_email($email)) {
            $update_data = array(
                'email' => $email
            );
            $result = $this -> usermodel -> update_fb_email($fb_uid, $update_data);
            if ($result) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = FB_EMAIL_UPDATE_SUCCESS;
                $retArr['userDetails'] = array($update_data);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {

                $retArr['status'] = FAIL;
                $retArr['message'] = FB_EMAIL_UPDATE_FAILED;
                $retArr['userDetails'] = array($update_data);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = EMAIL_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function update_tw_email_post() {
        $tw_uid = $this -> post('tw_uid');
        $email = $this -> post('email');
        if (!$this -> usermodel -> check_email($email)) {
            $update_data = array(
                'email' => $email
            );
            $result = $this -> usermodel -> update_tw_email($tw_uid, $update_data);
            if ($result) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = TW_EMAIL_UPDATE_SUCCESS;
                $retArr['userDetails'] = array($update_data);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {

                $retArr['status'] = FAIL;
                $retArr['message'] = TW_EMAIL_UPDATE_FAILED;
                $retArr['userDetails'] = array($update_data);
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = EMAIL_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function check_fb_account_post() {
        $fb_uid = $this -> post('fb_uid');
        $isExists = $this -> usermodel -> check_fb_account_exists($fb_uid);
        if ($isExists) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = FB_ACCOUNT_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = FB_ACCOUNT_NOT_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function check_gp_account_post() {
        $gp_uid = $this -> post('gp_uid');
        $isExists = $this -> usermodel -> check_gp_account_exists($gp_uid);
        if ($isExists) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = GP_ACCOUNT_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = GP_ACCOUNT_NOT_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function check_tw_account_post() {
        $tw_uid = $this -> post('tw_uid');
        $isExists = $this -> usermodel -> check_tw_account_exists($tw_uid);
        if ($isExists) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = TW_ACCOUNT_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = TW_ACCOUNT_NOT_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function check_email_post() {
        $email = $this -> post('email');
        $isExists = $this -> usermodel -> check_email($email);
        if ($isExists) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = EMAIL_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = EMAIL_NOT_FOUND;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function check_preffered_location_post() {
        $user_id = $this -> post('user_id');
        $location = $this -> usermodel -> check_preffered_location($user_id);
        if ($location) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = PREF_LOCATION_EXISTS;
            $retArr['latlong'] = $location;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = PREF_LOCATION_NOT_EXISTS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function save_preffered_location_post() {
        $user_id = $this -> post('user_id');
        $latitude = $this -> post('latitude');
        $longitude = $this -> post('longitude');
        $distance = $this -> post('distance');
        $streetName = $this -> post('street_name') ? $this -> post('street_name') : "";
        $update_data = array(
            'PrefLatitude' => $latitude,
            'PrefLongitude' => $longitude,
            'PrefDistance' => $distance,
            'Latitude' => $latitude,
            'Longitude' => $longitude,
            'StreetAddress' => $streetName,
        );
        $isUpdate = $this -> usermodel -> save_preffered_location($user_id, $update_data);
        if ($isUpdate) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = PREF_LOCATION_SUCCESS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = PREF_LOCATION_FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }
    }

    public function logout_post() {
        $user_id = $this -> post('user_id');
        $device_token = $this -> post('device_token');
        $is_removed = $this -> usermodel -> remove_device_token($user_id, $device_token);
        if ($is_removed) {
            $retArr['status'] = SUCCESS;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
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
    
    /*
     * Method Name: add_app_share_count
     * Purpose: Add App share count 
     * params:
     *      input: user_id, $share_from
     *      output: status - FAIL / SUCCESS
     *              message - Get the count of app Share
     */
    public function add_app_share_count_post() {       
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $share_from = $this -> post('share_from') ? $this -> post('share_from') : ""; //W - Web, A - Android, I - I phone 
        //F-Facebook, T-Twitter, E-Email, G-Google and W-WhatsApp
        $social_media = $this -> post('social_media') ? $this -> post('social_media') : ""; 
        
        $insert_data = array(            
            'UserId' => $user_id,
            'ShareFrom' => $share_from,
            'SocialMedia' => $social_media
        );
        $isInsert = $this -> usermodel -> insert_app_share_details($insert_data);
        if ($isInsert) {
			
			gainLoyaltyPointsMailOfUser($user_id,'APP Share From '.$share_from);
            $share_count = $this -> usermodel -> get_app_shares($user_id);
            $share_count = $share_count['count'] ? $share_count['count'] : 0;
            $retArr['status'] = SUCCESS;
            $retArr['count'] = $share_count;
            $retArr['message'] = 'Share count updated successfully';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'Failed to update share count';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    /**
     * Purpose: Save Talk to Us/Contactus Request
     * Output Parameter:
     * Save Talk to Us/Contactus Request
     * */
    public function contactus_post() {        
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $email = $this -> post('email') ? $this -> post('email') : "";
        $mobile_number = $this -> post('mobile_number') ? $this -> post('mobile_number') : "";
        $request_type = $this -> post('request_type') ? $this -> post('request_type') : "";
        $request_type = $request_type == 'Suggestion' ? 2 : 1; // 1: Help , 2 : Suggestion
        $usercomment = $this -> post('user_comment') ? $this -> post('user_comment') : "";
	
        $insert_data = array(
            'UserId' => $user_id,
            'Email' => $email,
            'MobileNumber' => $mobile_number,
            'RequestType' => $request_type,
            'UserComment' => $usercomment,
            'IsActive'=>1,
            'CreatedBy' => $user_id,
            'CreatedOn' => date("Y-m-d H:i:s")
        );

        $result = $this -> usermodel -> save_contactus_request($insert_data);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Request sent successfully.";                
            $this -> response($retArr, 200); // 200 being the HTTP response code
        }else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Unable to send request";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    } // contactus_post
    
}

?>
