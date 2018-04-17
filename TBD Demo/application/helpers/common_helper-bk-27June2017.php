<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/array_helper.html
 */
// ------------------------------------------------------------------------

/**
 * Element
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty it returns FALSE (or whatever you specify as the default value.)
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
if (!function_exists('validateApiKey')) {

    function validateApiKey($api_key = '') {
        if ($api_key) {
            if ($api_key != md5(API_TOKEN)) {
                echo json_encode(array("status" => FAIL, "message" => INVALID_TOKEN_MESSAGE));
                die;
            }
        }
        else {
            echo json_encode(array("status" => FAIL, "message" => NO_TOKEN_MESSAGE));
            die;
        }
    }
}
/**
 * Element
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty it returns FALSE (or whatever you specify as the default value.)
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
if (!function_exists('stripJunk')) {

    function stripJunk($string) {
        $string = str_replace(" ", "-", trim($string));
        $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
        $string = strtolower($string);
        return $string;
    }
}


/**
 * Purpose: Merging content from email view and userdata
 * Input Parameter:
 *          strParam = the parameters to be replaced
 *          strContent = the content from which the parameters are to be replaced
 * Output Parameter:
 *          template as text
 */
if (!function_exists('mergeContent')) {

    function mergeContent($strParam = array()) {

        $strContent = "";
        $CI = &get_instance();
        //ob_start(); // start output buffer
        $strContent = $CI -> load -> view('email/template', '', true);
        //$strContent = ob_get_contents(); // get contents of buffer
        //ob_end_clean();

        if ($strParam) {
            foreach ($strParam as $key => $value) {
                $strContent = str_replace($key, $value, $strContent);
            }
        }

        return $strContent;
    }
}

/**
 * Purpose: Get the front url directly
 * Input Parameter:
 *
 * Output Parameter:
 *          returns front url
 */
if (!function_exists('front_url')) {

    function front_url() {
        $CI = &get_instance();
        return $CI -> config -> item('front_url');
    }
}
/* End of file common_helper.php */
/* Location: ./appplication/helpers/array_helper.php */

/**
 * Element
 *
 * Get the time in the human trailing format
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
if (!function_exists('humanTiming')) {

    function humanTiming($time) {

        $time = time() - $time; // to get the time since that moment

        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < 1)
                return "1 second";
            else if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
    }
    /* End of file common_helper.php */
    /* Location: ./appplication/helpers/array_helper.php */

    /**
     * Element
     *
     * Get the time in the human trailing format
     *
     * @access	public
     * @param	string
     * @param	array
     * @param	mixed
     * @return	mixed	depends on what the array contains
     */
    if (!function_exists('ProductPriceRange')) {

        function ProductPriceRange($max, $min) {
            $x = 10;

            //Get the partition range
            $partition_range = $max / 5;

            $result = array();

            $partition_range = round(($partition_range + $x / 2) / $x) * $x;
            $i = 0;
            if ($partition_range <= $max) {
                if ($max == $min) {
                    $result[$i]['min'] = 0;
                    $result[$i]['max'] = number_format($max, 2);
                }
                else if (( $max - $min ) < $partition_range) {
                    $result[$i]['min'] = number_format($min, 2);
                    $result[$i]['max'] = number_format($max, 2);
                }
                else {
                    $number = range($min, $max, $partition_range);

                    foreach ($number as $number) {

                        if ($i == 0) {
                            $result[$i]['min'] = 0;
                            $result[$i]['max'] = number_format($number, 2);
                        }
                        else {
                            $result[$i]['min'] = $result[$i - 1]['max'];
                            $result[$i]['max'] = number_format($number, 2);
                        }
                        $i++;
                    }

                    $result[$i]['min'] = $result[$i - 1]['max'] . " and above";
                    $result[$i]['max'] = 0;
                }
            }
            return $result;
        }
    }

    if (!function_exists('send_notification')) {

        /* End of file common_helper.php */
        /* Location: ./appplication/helpers/array_helper.php */

        /**
         * Element
         *
         * Send push notifications to android & iphone devices
         * Get the users device Id with the device type
         */
        function send_notification($msg, $users) {

            $android_user = array();
            $ios_user = array();

            foreach ($users as $user) {

                if ($user['DeviceType'] == 'A') {
                    $android_user[] = $user['DeviceId'];
                }

                if ($user['DeviceType'] == 'I') {
                    $ios_user[] = $user['DeviceId'];
                }
            }

            if (!empty($android_user)) {

                //Android connection
                $url = 'https://android.googleapis.com/gcm/send';
                $headers = array(
                    'Authorization:key=' . GOOGLE_FCM_KEY,                    
                    'Content-Type: application/json'
                );
                //End

                $message = array("title" => $msg);
                // Set POST variables
                $fields = array(
                    'registration_ids' => $android_user,
                    'data' => $message,
                );

                // Open connection
                $ch = curl_init();
                // Set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Disabling SSL Certificate support temporarly
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                // Execute post
                curl_exec($ch);
                // Close connection
                curl_close($ch);
                //echo $result;
            }

//           Push Notification for IOS
            if (!empty($ios_user)) {

                foreach ($ios_user as $deviceToken) {

                    ////////////////////////////////////////////////////////////////////////////////

                    $ctx = stream_context_create();

                    stream_context_set_option($ctx, 'ssl', 'local_cert', PEM_CERTIFICATE);
                    stream_context_set_option($ctx, 'ssl', 'passphrase', PASSPHRASE);

                    // Open a connection to the APNS server
                    $fp = stream_socket_client(
                        'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

                    if (!$fp)
                        exit("Failed to connect: $err $errstr" . PHP_EOL);


                    // Create the payload body
                    $body['aps'] = array(
                        'alert' => $msg,
                        'sound' => 'default'
                    );

                    // Encode the payload as JSON
                    $payload = json_encode($body);

                    // Build the binary notification
                    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                    // Send it to the server
                    $result = fwrite($fp, $msg, strlen($msg));

//                    if (!$result)
//                        echo 'Message not delivered' . PHP_EOL;
//                    else
//                        echo 'Message successfully delivered' . PHP_EOL;
                    // Close the connection to the server
                    fclose($fp);
                }
            }
        }
    }

    if (!function_exists('resize_crop_image')) {

        function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80, $x, $y) {
            $imgsize = getimagesize($source_file);
            $width = $imgsize[0];
            $height = $imgsize[1];
            $mime = $imgsize['mime'];

            switch ($mime) {
                case 'image/gif':
                    $image_create = "imagecreatefromgif";
                    $image = "imagegif";
                    break;

                case 'image/png':
                    $image_create = "imagecreatefrompng";
                    $image = "imagepng";
                    $quality = 7;
                    break;

                case 'image/jpeg':
                    $image_create = "imagecreatefromjpeg";
                    $image = "imagejpeg";
                    $quality = 80;
                    break;

                default:
                    return false;
                    break;
            }

            $dst_img = imagecreatetruecolor($max_width, $max_height);
            $src_img = $image_create($source_file);

            $width_new = $height * $max_width / $max_height;
            $height_new = $width * $max_height / $max_width;
            //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
            if ($width_new > $width) {
                //cut point by height
                $h_point = (($height - $height_new) / 2);
                //copy image
                imagecopyresampled($dst_img, $src_img, 0, 0, $x, $y, $max_width, $max_height, $width, $height_new);
            }
            else {
                //cut point by width
                $w_point = (($width - $width_new) / 2);
                imagecopyresampled($dst_img, $src_img, 0, 0, $x, $y, $max_width, $max_height, $width_new, $height);
            }

            $image($dst_img, $dst_dir, $quality);

            if ($dst_img)
                imagedestroy($dst_img);
//            if ($src_img)
//                imagedestroy($src_img);
        }
    }

    /**
     * Element
     *
     * Send Confirmation mail to user and admin
     */
    if (!function_exists('UserRegistrationConfirmation')) {

        function UserRegistrationConfirmation($user_id) {

            $CI = &get_instance();
            $CI -> load -> model('front/usermodel');
            $user_details = $CI -> usermodel -> get_user_details($user_id);

            $CI -> load -> model('front/emailtemplatemodel');
            $email_template_details = $CI -> emailtemplatemodel -> get_email_template_details(3);

            $emailBody = $email_template_details['Content'];
            $emailBody = str_replace("{USERNAME}", $user_details['FirstName'], $emailBody);


            //---- LOAD EMAIL LIBRARY ----//
            $CI -> load -> library('email');
            $config['mailtype'] = 'html';
            $CI -> email -> initialize($config);

            $CI -> email -> from($email_template_details['FromEmail']);
            $CI -> email -> to($user_details['Email']);
            $CI -> email -> subject("The Best Deals Confirmation");

            $CI -> email -> message($emailBody);
            $CI -> email -> send();


            // Send Admin details regarding the user registration.
            $email_template_details = $CI -> emailtemplatemodel -> get_email_template_details(4);
            $emailBody = "";
            $emailBody = $email_template_details['Content'];
            $emailBody = str_replace("{USERNAME}", $user_details['FirstName'] . " " . $user_details['LastName'], $emailBody);
            $emailBody = str_replace("{USERID}", $user_details['Id'], $emailBody);
            $emailBody = str_replace("{EMAIL}", $user_details['Email'], $emailBody);

            //---- LOAD EMAIL LIBRARY ----//
            $CI -> load -> library('email');
            $config['mailtype'] = 'html';
            $CI -> email -> initialize($config);

            $CI -> email -> from($email_template_details['FromEmail']);
            $CI -> email -> to($email_template_details['ToEmail']);
            $CI -> email -> subject("The Best Deals Confirmation");

            $CI -> email -> message($emailBody);
            $CI -> email -> send();
        }
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

function replace_slash($string) {
    return str_replace("/", "%2F", $string);
}

//function number_shorten($number, $precision = 3, $divisors = null) {
//    // Setup default $divisors if not provided
//    if (!isset($divisors)) {
//        $divisors = array(
//            pow(1000, 0) => '', // 1000^0 == 1
//            pow(1000, 1) => 'K', // Thousand
//            pow(1000, 2) => 'M', // Million
//            pow(1000, 3) => 'B', // Billion
//            pow(1000, 4) => 'T', // Trillion
//            pow(1000, 5) => 'Qa', // Quadrillion
//            pow(1000, 6) => 'Qi', // Quintillion
//        );
//    }
//
//    // Loop through each $divisor and find the
//    // lowest amount that matches
//    foreach ($divisors as $divisor => $shorthand) {
//        if (abs($number) < ($divisor * 1000)) {
//            // We found a match!
//            break;
//        }
//    }
//
//    // We found our match, or there were no matches.
//    // Either way, use the last defined value for $divisor.
//    $result = bcdiv($number , $divisor, $precision);
//    $result_array = explode('.', $result);
//    $actual_val = '';
//    if ($result_array[1] <= 0) {
//        $actual_val = $result_array[0];
//    }
//    else {
//        $actual_val = $result;
//    }
//    return $actual_val . $shorthand;
//}

function number_shorten($number, $precision = 3, $divisors = null) {
    // Setup default $divisors if not provided
    if (!isset($divisors)) {
        $divisors = array(
            pow(10, 5) => 'L', //lakh
            pow(1000, 0) => '', // 1000^0 == 1
            pow(1000, 1) => 'K', // Thousand
            pow(1000, 2) => 'M', // Million
            pow(1000, 3) => 'B', // Billion
            pow(1000, 4) => 'T', // Trillion
            pow(1000, 5) => 'Qa', // Quadrillion
            pow(1000, 6) => 'Qi', // Quintillion
        );
    }

    // Loop through each $divisor and find the
    // lowest amount that matches

    foreach ($divisors as $divisor => $shorthand) {
        if ($divisor == 100000 && (abs($number) > 100000)) {
            if (abs($number) < ($divisor * 10)) {
                // We found a match!
                break;
            }
        }
        elseif ($divisor != 100000) {
            if (abs($number) < ($divisor * 1000)) {
                // We found a match!
                break;
            }
        }
    }

    // We found our match, or there were no matches.
    // Either way, use the last defined value for $divisor.
    $result = bcdiv($number, $divisor, $precision);
    $result_array = explode('.', $result);
    $actual_val = '';
    if ($result_array[1] <= 0) {
        $actual_val = $result_array[0];
    }
    else {
        $actual_val = $result;
    }
    return $actual_val . $shorthand;
}

function sanitize($input) {
    $output = '';
    if (is_array($input)) {
        foreach ($input as $var => $val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input = cleanInput($input);
        $output = mysql_real_escape_string($input);
        //$output = $input;
    }
    return $output;
}

function cleanInput($input) {

    $search = array(
        '@<script[^>]*?>.*?</script>@si', // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );

    $output = preg_replace($search, '', $input);
    return str_replace('[removed]', ' ', $output);
}

function send_push_notification($notification_array, $users, $insert_data = array()) {
    
    
    $CI = &get_instance();
    $CI -> load -> model('admin/notificationmodel');

    //$user_ids = $CI -> notificationmodel -> get_device_user_ids();
    $device_tokens = $CI -> notificationmodel -> get_device_tokens($users);
    $device_token_array = [];
    if ($device_tokens) {
        $cnt = 1;
        $group = 0;
        foreach ($device_tokens as $device_token) {
            $device_token_array[$group][] = $device_token['DeviceId'];
            if (FCM_MAX_LIMIT === $cnt) {
                $cnt = 1;
                $group++;
            }
            else {
                $cnt++;
            }
        }
    }
    if (isset($device_token_array[0])) {
        foreach ($device_token_array as $token_set) {
            $tokens = $token_set;
            $url = FCM_ANDROID_URL;
            $priority = "high";
            $fields = array(
                'registration_ids' => $tokens,
                'data' => $notification_array
            );
            $headers = array(
                'Authorization:key=' . GOOGLE_FCM_KEY,
                'Content-Type: application/json'
            );            
          
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            //echo curl_error($ch);
            if ($result === FALSE) {
                //die('Curl failed: ' . curl_error($ch));
            }
            else {
                remove_nonregistered_device_tokens($result, $tokens);
            }
            curl_close($ch);             
            
            $CI -> notificationmodel -> save_notification_history(json_encode($fields), $result, implode(',', $users));
        }
    }
    if (!empty($insert_data)) {
        $CI -> notificationmodel -> add_multiple_notification($insert_data);
    }
}

/**
 * Function to create the message and send push notification to android
 * 
 * @param int $product_id
 * @param int $retailer_id
 * @param int $store_type_id
 * @param int $store_id
 * @param string $product_name
 * @param string $retailer_name
 * @param string $store_name
 * @param string $store_type_name
 * @param int $special_count
 * @param float $special_price
 * @param string $special_name
 * @param int $success_count
 * @param array $store_array
 * @param array $product_array
 * @param array $store_detail_array
 */
function create_push_message($product_id = '', $retailer_id = '', $store_type_id = '', $store_id = '', $product_name = '', $retailer_name = '', $store_name = '', $store_type_name = '', $special_count = 0, $special_price = 0, $special_name = '', $success_count = 0, $store_array = array(), $product_array = array(), $store_detail_array = array(),  $specialFrom  ="", $specialTo ="") {
    
    $multiple_insert = [];
    $CI = &get_instance();
    $CI -> load -> model('admin/specialproductmodel');

    //Create a general message
    $message = $special_name . ', ';
    if ($success_count > 1) { //if the product count is more than one
        $product_id = '';
        $retailer_id = '';
        $store_type_id = '';
        $store_id = '';
        $message .= $success_count . ' products. From ' . $retailer_name;
    }
    else { //if only one product is addded
        $message .= $product_name . ' from: ' . $retailer_name . ', ' . $store_name . ', ';
        if ($special_count > 1) {
            $message .= $special_price . '(' . $special_count . ')';
        }
        else {
            $message .= $special_price . '.';
        }
    }
    $message .= ' Don\'t miss it!';

    $user_ids = $CI -> specialproductmodel -> get_device_user_ids(); //Get the registered device IDs now available in DB
     
    $users_have_store = [];
    $user_have_price_alert = [];
    $price_change_array = [];
    $price_change_count = [];
    $retailer_special_message = [];
    $retailer_count_arr = [];
    $user_add = [];
    $comp_available_stores = [];
    $product_names = []; // Array to store product names
    $productNameMessage = "";
    $store_names = []; // Array to store name of the stores
    $storeNameMessage = "";
    $usersStoreIds = array();
    
    $price_watch_count = array();
    $price_watch_array = array();
    $pricewatch_product_names = array();
    
    if (!empty($user_ids)) {//If users available to send the notifications
        foreach ($user_ids as $user_id) {
          //if( $user_id['UserId'] == 201 || $user_id['UserId'] == 217){
          // if( $user_id['UserId'] == 77 || $user_id['UserId'] == 341){ 
            $comp_available_stores = [];
            if ($user_id['PrefLatitude'] && $user_id['PrefLongitude'] && $user_id['PrefDistance']) {
                # get Default stores of the user
                $store_det = $CI -> specialproductmodel -> get_device_user_stores($user_id['PrefLatitude'], $user_id['PrefLongitude'], $user_id['PrefDistance'], $store_array, $user_id['state']);
                             
                
                $store_name = isset($store_det[0]['StoreName']) ? $store_det[0]['StoreName'] : '';
                //$store_get_id = isset($store_det[0]['StoreId']) ? $store_det[0]['StoreId'] : '';
                
                # Modify By MK
                $store_get_id = isset($store_det[0]['Id']) ? $store_det[0]['Id'] : '';
                
                $special_notifications = $CI -> specialproductmodel -> get_special_enabled_user_store();
                
                
                $special_user_list = [];
                if ($special_notifications) {
                    foreach ($special_notifications as $special_users) {
                        $special_user_list[$special_users['UserId']] = array(
                            'StoreId' => $special_users['StoreId'],
                            'Specials' => $special_users['Specials'],
                            'PreferredStoreOnly' => $special_users['PreferredStoreOnly']
                        );
                    }
                }
                
                # Prepare array for price Watch notification 
                $user_notification_settings = $CI -> specialproductmodel -> get_user_notification_settings($user_id['UserId']);
                
                if ($user_notification_settings) {
                    if ($user_notification_settings['PriceWatch'] == 1) {
                        foreach ($product_array as $index => $product) {
                            
                        $is_pricewatch_alert_available = $CI -> specialproductmodel -> check_pricewatch_alert($product['id'], $user_id['UserId']);
                        
                        if ($is_pricewatch_alert_available) {                            
                            $price_watch_array[$user_id['UserId']][] = array(
                                    'title' => $product['name'].' is on Special',
                                    'message' => $product['name'] . ' is on Special at: ' . $store_detail_array[$index]['retailerName'] . ' - ' . $store_detail_array[$index]['name'] .' from '.$specialFrom.' - '.$specialTo.'.',
                                    'product_id' => $product['id'],
                                    'retailer_id' => $store_detail_array[$index]['retailer'],
                                    'store_type_id' => $store_detail_array[$index]['storeType'],
                                    'store_id' => $store_detail_array[$index]['id'],
                                    'is_special' => '0',
                                    'is_location_message' => '0',
                                    'is_location_near_message' => '0'
                                );
                        } 
                      }
                    }//if ($user_notification_settings['PriceWatch'] == 1) {
                } // if ($user_notification_settings) {
                
                
                
                if (!empty($store_det)) {
                    foreach ($store_det as $store_de) {
                        if (in_array($store_de['Id'], $store_array)) {
                            $comp_available_stores[] = $store_de['Id'];
                        }
                        
                        $usersStoreIds[$user_id['UserId']][]= $store_de['Id']; // Stores Ids within the user's preferred distance
                    }
                }
               
                
                
                if ($store_name && $store_get_id) {
                    if (!in_array($user_id['UserId'], $users_have_store)) {                        
                        //Adding the user devices, in which they have the stores added in DB
                        if (!empty($special_user_list)) {
                            if (isset($special_user_list[$user_id['UserId']])) {
                                $special_user_list[$user_id['UserId']]['StoreId'];
                                
                                if ($special_user_list[$user_id['UserId']]['StoreId'] == $store_get_id && ($special_user_list[$user_id['UserId']]['Specials'] == 1 || $special_user_list[$user_id['UserId']]['PreferredStoreOnly'] == 1)) {
                                    $users_have_store[$user_id['UserId']] = array(
                                        'UserId' => $user_id['UserId'],
                                        'StoreName' => $store_name,
                                        'StoreId' => $store_get_id
                                    );
                                    if (!in_array($user_id['UserId'], $user_add)) {
                                        $user_add[] = $user_id['UserId'];
                                    }
                                }
                                else {
                                   
                                    
                                }
                            }
                        }
                        else {
                            if (!in_array($user_id['UserId'], $user_add)) {
                                $user_add[] = $user_id['UserId'];
                            }
                            $users_have_store[$user_id['UserId']] = array(
                                'UserId' => $user_id['UserId'],
                                'StoreName' => $store_name,
                                'StoreId' => $store_get_id
                            );
                        }
                    }
                }
            }
            
        
        
            if (!isset($users_have_store[$user_id['UserId']])) {
                if (!empty($comp_available_stores)) {                    
                    $user_notification_settings = $CI -> specialproductmodel -> get_user_notification_settings($user_id['UserId']);
                    $user_preferred_brands = $CI -> specialproductmodel -> get_user_preferred_brands($user_id['UserId']);
                    
                    if ($user_notification_settings) {
                        if ($user_notification_settings['Specials'] == 1) {
                            if ($user_notification_settings['PreferredStoreOnly'] == 1) {
                                if (in_array($user_preferred_brands['StoreId'], $comp_available_stores)) {
                                    $users_have_store[$user_id['UserId']] = array(
                                        'UserId' => $user_id['UserId'],
                                        'StoreName' => '',
                                        'StoreId' => $user_preferred_brands['StoreId']
                                    );
                                }
                            }
                            else {
                                $users_have_store[$user_id['UserId']] = array(
                                    'UserId' => $user_id['UserId'],
                                    'StoreName' => '',
                                    'StoreId' => $comp_available_stores[0]
                                );
                            }
                        }
                    }
                }
            }
            
          //}//if( $user_id['UserId'] == 201 || $user_id['UserId'] == 217){  
        }
                 
        
        if (!empty($users_have_store)) { //if there are users with stores, then send push notification to them
            $notification_array = array(
                'title' => 'Hurry! Specials Added',
                'message' => $message,
                'product_id' => $product_id,
                'retailer_id' => $retailer_id,
                'store_type_id' => $store_type_id,
                'store_id' => $store_id,
                'is_special' => '1',
                'is_location_message' => '0',
                'is_location_near_message' => '0'
            );
            if (!empty($product_array)) {
                //Checking for price alert enabled
                //If for a single product for single user, then will send the message with all the product details
                //If user have multiple alerts, then a general message will send

                foreach ($product_array as $index => $product) {
                    foreach ($users_have_store as $user_get) {
                        $multiple_single_insert = [];
                        $is_alert_available = $CI -> specialproductmodel -> check_price_alert($product['id'], $user_get['UserId']);
                        if ($is_alert_available) {                            
                            if (!array_key_exists($user_get['UserId'], $price_change_array)) {
                                # set product names according to users
                                $product_names[$user_get['UserId']][] = $product['name']; 
                                
                                $price_change_count[$user_get['UserId']] = 1;
                                $price_change_array[$user_get['UserId']] = array(
                                    'title' => 'Price Change Alert',
                                    'message' => $product['name'] . ' from: ' . $store_detail_array[$index]['retailerName'] . ', ' . $store_detail_array[$index]['name'],
                                    'product_id' => $product['id'],
                                    'retailer_id' => $store_detail_array[$index]['retailer'],
                                    'store_type_id' => $store_detail_array[$index]['storeType'],
                                    'store_id' => $store_detail_array[$index]['id'],
                                    'is_special' => '0',
                                    'is_location_message' => '0',
                                    'is_location_near_message' => '0'
                                );
                            }
                            else {
                                # set product names according to users
                                $product_names[$user_get['UserId']][] = $product['name']; 
                                
                                $price_change_count[$user_get['UserId']] += 1;
                                $price_change_array[$user_get['UserId']] = array(
                                    'title' => 'Price Change Alert',
                                    'message' => $price_change_count[$user_get['UserId']] . ' products from: ' . $store_detail_array[$index]['retailerName'] . ', ' . $store_detail_array[$index]['name'],
                                    'product_id' => '',
                                    'retailer_id' => $store_detail_array[$index]['retailer'],
                                    'store_type_id' => '',
                                    'store_id' => '',
                                    'is_special' => '0',
                                    'is_location_message' => '0',
                                    'is_location_near_message' => '0'
                                );
                            }
//                            $notification_array1 = array(
//                                'title' => 'Price Change Alert',
//                                'message' => $product['name'] . ' from: ' . $store_detail_array[$index]['retailerName'] . ', ' . $store_detail_array[$index]['name'],
//                                'product_id' => $product['id'],
//                                'retailer_id' => $store_detail_array[$index]['retailer'],
//                                'store_type_id' => $store_detail_array[$index]['storeType'],
//                                'store_id' => $store_detail_array[$index]['id'],
//                                'is_special' => '1'
//                            );
//                            $multiple_single_insert[] = array(
//                                'Title' => $notification_array1['title'],
//                                'Message' => $notification_array1['message'],
//                                'UserId' => $user_get['UserId'],
//                                'CreatedOn' => date('Y-m-d H:i:s')
//                            );
//                            send_push_notification($notification_array1, array($user_get['UserId']), $multiple_single_insert);
                        }
                        else {
                            if (!array_key_exists($store_detail_array[$index]['id'] . ':' . $user_get['UserId'], $retailer_special_message)) {
                                if (!isset($retailer_count_arr[$user_get['UserId']])) {
                                    if( in_array($store_detail_array[$index]['id'],$usersStoreIds[$user_get['UserId']]))
                                    {   
                                        $retailer_count_arr[$user_get['UserId']] = 1;
                                        # Get stores names
                                        $store_names[$user_get['UserId']][]=$store_detail_array[$index]['name'];
                                    }
                                    
                                }
                                else {
                                    if( in_array($store_detail_array[$index]['id'],$usersStoreIds[$user_get['UserId']]))
                                    {                                    
                                        $retailer_count_arr[$user_get['UserId']] += 1;
                                    
                                        # Get stores names
                                        $store_names[$user_get['UserId']][]=$store_detail_array[$index]['name'];
                                    }
                                    
                                }
                                $retailer_special_message[$store_detail_array[$index]['id'] . ':' . $user_get['UserId']] = array(
//                                    'title' => 'Special Added',
                                    'title' => $store_detail_array[$index]['retailerName'].' - '.$special_name,                                    
                                    'message' => 'Specials Added from: ' . $store_detail_array[$index]['retailerName'] . ', ' . $store_detail_array[$index]['name'],
                                    'product_id' => $product['id'],
                                    'retailer_id' => $store_detail_array[$index]['retailer'],
                                    'store_type_id' => $store_detail_array[$index]['storeType'],
                                    'store_id' => $store_detail_array[$index]['id'],
                                    'is_special' => '0',
                                    'is_location_message' => '0',
                                    'is_location_near_message' => '0'
                                );
                            }
                        }
                    }
                }
                
                $retailer_final_array = [];
                if (!empty($retailer_special_message) && !empty($retailer_count_arr)) {
                    foreach ($retailer_special_message as $store_user_id => $val) {                        
                        $store_user_arr = explode(':', $store_user_id);
                         // $store_user_arr[0] : Store Id
                         // $store_user_arr[1] : User Id
                        
                        if (isset($retailer_count_arr[$store_user_arr[1]])) {
                            if ($retailer_count_arr[$store_user_arr[1]] > 1) {
                                
                                $retailer_final_array[$store_user_arr[1]] = array(
                                    'title' => $store_detail_array[$index]['retailerName'].' - '.$special_name,                                    
                                    'message' => 'Specials Added: ' . $retailer_count_arr[$store_user_arr[1]] . ' Stores from ' . $store_detail_array[$index]['retailerName'],
                                    'product_id' => '0',
                                    'retailer_id' => $val['retailer_id'],
                                    'store_type_id' => '0',
                                    'store_id' => '0',
                                    'is_special' => '0',
                                    'is_location_message' => '0',
                                    'is_location_near_message' => '0'
                                );
                            }
                        }
                    }
                }
                 
                //checking the price alert array and send the notifications
                if (!empty($price_change_array)) {
                    foreach ($price_change_array as $price_user_id => $price_array) {
                        
                        //Get product Names
                        if(isset($product_names[$price_user_id])){
                            $productNameMessage = implode("," ,$product_names[$price_user_id]);
                        }
                        
                        $finalMessage = "";
                        $msg = explode("products from:",$price_array['message']);
                        $finalMessage = $productNameMessage." products from: ".$msg[1];
                        
                        $multiple_special_insert = array();
                        $multiple_single_insert[] = array(
                            'Title' => $price_array['title'],
                            'Message' => $finalMessage,
                            'UserId' => $price_user_id,
                            'CreatedOn' => date('Y-m-d H:i:s')
                        );
                        
                        send_push_notification($price_array, array($price_user_id), $multiple_single_insert);
                    }
                }
                
                
                        
                if (!empty($retailer_final_array)) {
                    foreach ($retailer_final_array as $special_user_id => $special_array) {
                        if(isset($store_names[$special_user_id])){                            
                            $userStores = $store_names[$special_user_id];
                            foreach($userStores as $userStore)
                            {   
                                $finalStoreMessage = "";
                                $storeMsg = explode("Stores from",$special_array['message']);
                      
                                $msgTitleArr = explode("-",$special_array['title']);
                                $title = trim($msgTitleArr[0])." - ".$userStore;
                                $finalStoreMessage = $special_name ." Special from ".date("d M", strtotime($specialFrom))." to ".date("d M", strtotime($specialTo));
                                
                                $special_array['title'] = $title;
                                $special_array['message'] = $finalStoreMessage;
                                
                                $multiple_special_insert = array();
                                $multiple_special_insert[] = array(
                                    'Title' => $special_array['title'],
                                    'Message' => $finalStoreMessage,
                                    'UserId' => $special_user_id,
                                    'CreatedOn' => date('Y-m-d H:i:s')
                                );
                                
                                send_push_notification($special_array, array($special_user_id), $multiple_special_insert);
                            } //  foreach($userStores as $userStore)
                        }//  if(isset($store_names[$special_user_id]))
                    }// Foreach
                }//if (!empty($retailer_final_array)) {
            }
            
            foreach ($users_have_store as $us_st) {
                $multiple_insert[] = array(
                    'Title' => $notification_array['title'],
                    'Message' => $notification_array['message'],
                    'UserId' => $us_st['UserId'],
                    'CreatedOn' => date('Y-m-d H:i:s')
                );
            }
            send_push_notification($notification_array, $user_add, $multiple_insert);
        }
        
        # Send Price Watch notification 
        if (!empty($price_watch_array)) {
            foreach ($price_watch_array as $pricewatch_user_id => $priceWatchArray) {                        
                if(isset($price_watch_array[$pricewatch_user_id])){                            
                    foreach($priceWatchArray as $singlePriceWatchInfo)
                    { 
                        $insert_info = array();
                        $insert_info[] = array(
                            'Title' => $singlePriceWatchInfo['title'],
                            'Message' => $singlePriceWatchInfo['message'],
                            'UserId' => $pricewatch_user_id,
                            'CreatedOn' => date('Y-m-d H:i:s')
                        );
                        # Send notification
                        send_push_notification($singlePriceWatchInfo, array($pricewatch_user_id), $insert_info);                        
                    }// end foreach 
                } //  end if
            } // end foreach
        }// if
                
    }
}

/**
 * Function to send push notification when change price of the store
 * 
 * @param int $product_id
 * @param int $retailer_id
 * @param int $store_type_id
 * @param int $store_id
 * @param string $product_name
 * @param string $retailer_name
 * @param string $store_name
 * @param string $store_type_name
 * @param float $price
 * @param int $success_count
 * @param array $store_array
 * @param array $product_array
 * @param array $store_detail_array
 */
function create_change_push_message($product_id = '', $retailer_id = '', $store_type_id = '', $store_id = '', $product_name = '', $retailer_name = '', $store_name = '', $store_type_name = '', $price = 0, $success_count = 0, $store_array = array(), $product_array = array(), $store_detail_array = array()) {
    /* $multiple_insert = [];
      $CI = &get_instance();
      $CI -> load -> model('admin/specialproductmodel');
      //$message = 'Price change on ';
      $message = '';
      $count_str = '';
      if ($success_count > 1) {
      $product_id = '';
      $retailer_id = '';
      $store_type_id = '';
      $store_id = '';
      $message .= $success_count . ' products. From ' . $retailer_name;
      //$count_str = $success_count;
      }
      else {
      $message .= $product_name . ' from : ' . $retailer_name . ', ' . $store_name . ', ';

      $message .= $price . '.';
      }
      $message .= ' Don\'t miss it!';


      $user_ids = $CI -> specialproductmodel -> get_device_user_ids();
      $users_have_store = [];
      $user_have_price_alert = [];
      if ($user_ids) {
      foreach ($user_ids as $user_id) {
      if ($user_id['PrefLatitude'] && $user_id['PrefLongitude'] && $user_id['PrefDistance']) {
      $store_list = $CI -> specialproductmodel -> get_device_user_stores($user_id['PrefLatitude'], $user_id['PrefLongitude'], $user_id['PrefDistance'], $store_array);
      if ($store_list) {
      if (!in_array($user_id['UserId'], $users_have_store)) {
      $users_have_store[] = array(
      'UserId' => $user_id['UserId'],
      'StoreName' => $store_name
      );
      }
      }
      }
      }
      if (!empty($users_have_store)) {
      $notification_array = array(
      'title' => 'Price Change Alert',
      'message' => $message,
      'product_id' => $product_id,
      'retailer_id' => $retailer_id,
      'store_type_id' => $store_type_id,
      'store_id' => $store_id,
      'is_special' => '0',
      'is_location_message' => '0',
      'is_location_near_message' => '0'
      );
      //            if (!empty($product_array)) {
      //                foreach ($product_array as $index => $product) {
      //                    foreach ($users_have_store as $user_get) {
      //                        $multiple_single_insert = [];
      //                        $is_alert_available = $CI -> specialproductmodel -> check_price_alert($product['id'], $user_get);
      //                        if ($is_alert_available) {
      //                            $notification_array1 = array(
      //                                'title' => 'Price change alert',
      //                                'message' => 'A price change is made for ' . $product['name'] . ' by store ' . $store_detail_array[$index]['name'],
      //                                'product_id' => $product['id'],
      //                                'retailer_id' => $store_detail_array[$index]['retailer'],
      //                                'store_type_id' => $store_detail_array[$index]['storeType'],
      //                                'store_id' => $store_detail_array[$index]['id']
      //                            );
      //                            $multiple_single_insert[] = array(
      //                                'Title' => $notification_array1['title'],
      //                                'Message' => $notification_array1['message'],
      //                                'UserId' => $user_get,
      //                                'CreatedOn' => date('Y-m-d H:i:s')
      //                            );
      //                            send_push_notification($notification_array1, array($user_get), $multiple_single_insert);
      //                        }
      //                    }
      //                }
      //            }
      foreach ($users_have_store as $us_st) {
      $multiple_insert[] = array(
      'Title' => $notification_array['title'],
      'Message' => $notification_array['message'],
      'UserId' => $us_st['UserId'],
      'CreatedOn' => date('Y-m-d H:i:s')
      );
      }
      send_push_notification($notification_array, $users_have_store, $multiple_insert);
      }
      } */
}

/**
 * Create location change notification message
 * 
 * @param int $user_id
 * @param string $device_token
 * @param float $latitude
 * @param float $longitude
 * @param int $distance
 */
function create_location_change_message($user_id, $device_token, $latitude, $longitude, $distance) {

    $notification_array = array(
        'title' => 'Location Change Alert',
        'message' => 'You are out of the preferred location. Please change the location to get more alerts.',
        'latitude' => $latitude,
        'longitude' => $longitude,
        'distance' => $distance,
        'is_special' => '0',
        'is_location_message' => '1',
        'is_location_near_message' => '0',
        'store_id' => '',
        'product_id' => '',
        'retailer_id' => '',
        'store_type_id' => ''
    );
    send_push_notification_location($notification_array, $user_id, $device_token);
}

/**
 * Function to send the notification if the user is near to the store
 * 
 * @param int $user_id
 * @param string $device_token
 * @param array $nearby_store_data
 */
function create_location_nearby_message($user_id, $device_token, $nearby_store_data) {

    $notification_array = array(
        'title' => 'Nearby Store Alert',
        'message' => 'You are near to store: ' . $nearby_store_data['StoreName'] . '. ' . $nearby_store_data['SpecialCount'] . ' specials are available here.',
        'latitude' => $nearby_store_data['Latitude'],
        'longitude' => $nearby_store_data['Longitude'],
        'distance' => $nearby_store_data['CurrentDistance'],
        'is_special' => '0',
        'is_location_message' => '0',
        'is_location_near_message' => '1',
        'store_id' => '',
        'product_id' => '',
        'retailer_id' => '',
        'store_type_id' => ''
    );
    send_push_notification_location($notification_array, $user_id, $device_token, 1); //final attr is to identify that the request is for near by store
}

/**
 * Send notification to the user if gone out of the preferred location
 * 
 * @param array $notification_array
 * @param int $user_id
 * @param string $device_token
 */
function send_push_notification_location($notification_array, $user_id, $device_token, $store_near = 0) {
    $CI = &get_instance();
    $can_send = TRUE;
//    if($store_near === 0){ 
//        if ($this -> session -> userdata('location_notification_send_time')) {
//            $start = date_create($this -> session -> userdata('location_notification_send_time'));
//            $end = date_create(date('Y-m-d H:i:s'));
//            $diff = date_diff($end, $start);
//            if ($diff['h'] >= LOCATION_NOTIFICATION_DELAY) {
//                $can_send = TRUE;
//            }
//        }
//        else {
//            $can_send = TRUE;
//        }
//    }
//    else{
//        if ($this -> session -> userdata('nearby_notification_send_time')) {
//            $start = date_create($this -> session -> userdata('nearby_notification_send_time'));
//            $end = date_create(date('Y-m-d H:i:s'));
//            $diff = date_diff($end, $start);
//            if ($diff['i'] >= NEARBY_NOTIFICATION_DELAY) {
//                $can_send = TRUE;
//            }
//        }
//        else {
//            $can_send = TRUE;
//        }
//    }


    if ($can_send) {
        if ($store_near === 0) {
            $CI -> session -> set_userdata('location_notification_send_time', date('Y-m-d H:i:s')); //setting the time when the latest notification sent
        }
        else {
            $CI -> session -> set_userdata('nearby_notification_send_time', date('Y-m-d H:i:s')); //setting the time when the latest nearby notification sent
        }

        $url = FCM_ANDROID_URL;
        $priority = "high";
        $fields = array(
            'registration_ids' => array($device_token),
            'data' => $notification_array
        );
        $headers = array(
            'Authorization:key=' . GOOGLE_FCM_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //echo curl_error($ch);
        if ($result === FALSE) {
            //die('Curl failed: ' . curl_error($ch));
        }
        else {
            remove_nonregistered_device_tokens($result, array($device_token));
        }
        curl_close($ch);
        //echo $result;
        $CI -> notificationmodel -> save_notification_history(json_encode($fields), $result, $user_id);
    }
}

function encode_per($string) {
    $error_elms = array(
        '[per_rep]' => '%',
        '[per_hip]' => '/'
    );
    $act_string = '';
    foreach ($error_elms as $key => $elm) {
        $string = str_replace($elm, $key, $string);
    }
    return $string;
}

function decode_per($string) {
    $error_elms = array(
        '[per_rep]' => '%',
        '[per_hip]' => '/'
    );
    $act_string = '';
    foreach ($error_elms as $key => $elm) {
        $string = str_replace($key, $elm, $string);
    }
    return $string;
}

function send_custom_push_notification($subject, $content, $device_tokens) {
    $device_token_array = [];
    if ($device_tokens) {
        $cnt = 1;
        $group = 0;
        foreach ($device_tokens as $device_token) {
            $device_token_array[$group][] = $device_token['DeviceId'];
            if (FCM_MAX_LIMIT === $cnt) {
                $cnt = 1;
                $group++;
            }
            else {
                $cnt++;
            }
        }
    }
    if (isset($device_token_array[0])) {
        $notification_array = array(
            'title' => $subject,
            'message' => $content,
            'latitude' => '',
            'longitude' => '',
            'distance' => '',
            'is_special' => '0',
            'is_location_message' => '0',
            'is_location_near_message' => '1',
            'store_id' => '',
            'product_id' => '',
            'retailer_id' => '',
            'store_type_id' => ''
        );
        foreach ($device_token_array as $token_set) {
            $tokens = $token_set;
            $url = FCM_ANDROID_URL;
            $priority = "high";
            $fields = array(
                'registration_ids' => $tokens,
                'data' => $notification_array
            );
            $headers = array(
                'Authorization:key=' . GOOGLE_FCM_KEY,
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            //echo curl_error($ch);
            if ($result === FALSE) {
                //die('Curl failed: ' . curl_error($ch));
            }
            else {
                remove_nonregistered_device_tokens($result, $tokens);
            }
            curl_close($ch);
        }
    }
}

function format_decimal($number) {
    $num_arr = explode('.', round($number, 2));
    $new_num = '';
    if (!isset($num_arr[1])) {
        $new_num = $num_arr[0] . '.00';
    }
    elseif (strlen($num_arr[1] < 2)) {
        $new_num = $num_arr[0] . '.' . $num_arr[1] . '0';
    }
    else {
        $new_num = $num_arr[0] . '.' . $num_arr[1];
    }
    return $new_num;
}

function remove_nonregistered_device_tokens($result, $token_array) {
    $CI = &get_instance();
    $result_arr = json_decode($result);
    $non_registered_array = [];
    if (isset($result_arr -> results)) {
        foreach ($result_arr -> results as $key => $val) {
            if (isset($val -> error)) {
                if ($val -> error == 'NotRegistered') {
                    $non_registered_array[] = $token_array[$key];
                }
            }
        }
    }
    if (!empty($non_registered_array)) {
        $CI -> notificationmodel -> delete_nonregistered_devices($non_registered_array);
    }
}

function ismobile() {
    $is_mobile = '0';

    if (preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $is_mobile = 1;
    }

    if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
        $is_mobile = 1;
    }

    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array('w3c ', 'acs-', 'alav', 'alca', 'amoi', 'andr', 'audi', 'avan', 'benq', 'bird', 'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-');

    if (in_array($mobile_ua, $mobile_agents)) {
        $is_mobile = 1;
    }

    if (isset($_SERVER['ALL_HTTP'])) {
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
            $is_mobile = 1;
        }
    }

    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
        $is_mobile = 0;
    }

    return $is_mobile;
}    