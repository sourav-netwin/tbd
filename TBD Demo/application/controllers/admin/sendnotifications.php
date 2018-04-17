<?php

/*
 * Author:AS
 * Purpose:Send Notification controller
 * Date:06-12-2016
 * Dependency: sendnotifications.php
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Sendnotifications extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
        $this -> load -> model('admin/notificationmodel', '', TRUE);
        $this -> load -> model('admin/statemodel', '', TRUE);
        $this -> load -> model('admin/usermodel', '', TRUE);

        $this -> page_title = "Send Notifications";
        $this -> breadcrumbs[] = array('label' => 'Send Notifications', 'url' => '/sendnotifications');
    }

    /**
     * Send notification landing page
     */
    public function index() {
        $data['title'] = $this -> page_title;
        $this -> breadcrumbs[0] = array('label' => 'Send Notifications', 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['states'] = $this -> statemodel -> get_states();
        $data['users'] = $this -> usermodel -> get_app_users();
        /*
        echo "<pre>";
        print_r($data['users']);
        exit;
        */
        $this -> template -> view('admin/send_notification/index', $data);
    }

    public function get_state_storetype() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $state_id = $this -> input -> post('state_id');
            $storetype_list = $this -> notificationmodel -> get_state_storetype($state_id);
            $html = '<option value="">Select Store Format</option>';
            if ($storetype_list) {
                foreach ($storetype_list as $storetype) {
                    $html .= '<option value="' . $storetype['Id'] . '">' . $storetype['StoreType'] . '</option>';
                }
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No records found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }
    
    
    

    public function send_notification() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> form_validation -> set_rules('notif_subject', 'Subject', 'trim|required|max_length[50]|xss_clean');
            $this -> form_validation -> set_rules('notif_content', 'Content', 'trim|required|max_length[200]|xss_clean');
            if (!$this -> form_validation -> run() == FALSE) {
                $subject = $this -> input -> post('notif_subject');
                $content = $this -> input -> post('notif_content');
                $all = '';
                $notif_android = $this -> input -> post('notif_android');
                $notif_iphone = $this -> input -> post('notif_iphone');
                $notif_male = $this -> input -> post('notif_male');
                $notif_female = $this -> input -> post('notif_female');
                $state = $this -> input -> post('sel_region');
                $storetype = $this -> input -> post('sel_storetype');
                $retailer = $this -> input -> post('sel_retailer');
                $user = $this -> input -> post('sel_user');

                if (!$notif_android && !$notif_iphone && !$notif_male && !$notif_female && !$state && !$storetype) {
                    $all = 1;
                }
                
                /*
                $check_array = array(
                    'all' => $all,
                    'android' => $notif_android,
                    'iphone' => $notif_iphone,
                    'male' => $notif_male,
                    'female' => $notif_female,
                    'state' => $state,
                    'storetype' => $storetype
                );                 
                 */
                
                
                $check_array = array(
                    'all' => $all,
                    'android' => $notif_android,
                    'iphone' => $notif_iphone,
                    'male' => $notif_male,
                    'female' => $notif_female,
                    'state' => $state,
                    'storetype' => $storetype,
                    'retailer' => $retailer,
                    'user' => $user
                );

                $user_list = $this -> notificationmodel -> get_notification_users($check_array);
                
                if ($user_list) {
                    # Insert notification data
                    $insertBatch= array();                    
                    foreach($user_list as $userData)
                    {
                      # Get user information mapped to user.  
                      $deviceUserDetails = $this -> notificationmodel -> get_user_by_device_id($userData['DeviceId']);
                      
                      if( $deviceUserDetails)
                      {
                          $UserId = $deviceUserDetails->UserId;
                      
                            $insertBatch[] =  array(
                                "Title" => $subject,
                                "Message" => $content,
                                "UserId" => $deviceUserDetails->UserId,
                                "IsActive" => 1,
                                "IsRead" => 0,
                                "IsRemoved" => 0,
                                "CreatedOn" => date('Y-m-d H:i:s')
                            );
                          
                      }// if( $deviceUserDetails)                      
                    }
                    if( $insertBatch)
                    {
                        $this->db->insert_batch('usernotification', $insertBatch); 
                    }
                    send_custom_push_notification($subject, $content, $user_list);
                    $this -> result = 1;
                    $this -> message = 'Notifications sent successfully';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'No users available to send notification';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Invalid data';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }
    
    /* Function to get retailer for the selected store */    
    public function get_state_retailers() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $state_id = $this -> input -> post('state_id');
            $state_id = $state_id == 'all' ? 0 :  $state_id;
            
            # If want to show all retailers 
            $state_id = 0;
            
            $retailers_list = $this -> notificationmodel -> get_state_retailers($state_id);
            
            $html = '<option value="">Select Retailer</option>';
            if ($retailers_list) {
                foreach ($retailers_list as $retailer) {
                    $html .= '<option value="' . $retailer['Id'] . '">' . $retailer['RetailerName'] . '</option>';
                }
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No records found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }
    
    
    public function get_retailer_storetype() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_id = $this -> input -> post('retailer_id');
            $storetype_list = $this -> notificationmodel -> get_retailer_storetype($retailer_id);
            $html = '<option value="">Select Store Format</option>';
            if ($storetype_list) {
                foreach ($storetype_list as $storetype) {
                    $html .= '<option value="' . $storetype['Id'] . '">' . $storetype['StoreType'] . '</option>';
                }
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No records found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }
    
    /* Get Users based */
    public function get_users() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $region_id      = $this -> input -> post('region_id');
            $retailer_id    = $this -> input -> post('retailer_id');
            $storetype_id   = $this -> input -> post('storetype_id');
            $isMale         = $this -> input -> post('isMale');
            $isFemale       = $this -> input -> post('isFemale');
            $isAndroid      = $this -> input -> post('isAndroid');
            $isIphone       = $this -> input -> post('isIphone');
            
            
            $user_list = $this -> notificationmodel -> get_users($region_id,$retailer_id,$storetype_id, $isMale, $isFemale, $isAndroid, $isIphone);
            
            $html = '<option value="">Select User</option>';
            if ($user_list) {
                foreach ($user_list as $user) {
                    $showUserInfo = $user['FullName'];
                    $showUserInfo = $user['Email']!="" ? $showUserInfo." - ".$user['Email'] : $showUserInfo;
                    $showUserInfo = $user['Mobile']!="" ? $showUserInfo." - ".$user['Mobile'] : $showUserInfo;
                                            
                    $html .= '<option value="' . $user['Id'] . '">' . $showUserInfo . '</option>';
                }
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No records found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(
            array(
                'result' => $this -> result,
                'message' => $this -> message
            )
        );
    }
    
}