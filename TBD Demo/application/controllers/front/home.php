<?php

/*
 * Author:  PM
 * Purpose: Home Controller
 * Date:    07-10-2015
 */

class Home extends My_Front_Controller {

    private $result;
    private $message;
    private $clientId;
    private $clientSecret;
    private $redirectUrl;

    function __construct() {
        parent::__construct();
        $this -> load -> library('instagram_api');
        include_once APPPATH."libraries/google-api-php-client/Google_Client.php";
		include_once APPPATH."libraries/google-api-php-client/contrib/Google_Oauth2Service.php";
        
        $this -> clientId = '873827329375-3b0k7u206fpm53doqgon4eh9boml4rj9.apps.googleusercontent.com';
        $this -> clientSecret = 'qNSa1SG9x5unJd2ZV1xLc03I';
        $this -> redirectUrl = 'http://thebestdeal.co.za/registration/google_callback';
    }

    //Function to load default landing page for website
    public function index() {
       
        $this -> load -> model('front/slidermodel', '', TRUE);
        $this -> load -> model('front/retailermodel', '', TRUE);
        
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to thebestdeal.co.za');
        $gClient->setClientId($this -> clientId);
        $gClient->setClientSecret($this -> clientSecret);
        $gClient->setRedirectUri($this -> redirectUrl);
        $google_oauthV2 = new Google_Oauth2Service($gClient);

        //Get active sliders to be displayed
        $data['sliders'] = $this -> slidermodel -> get_sliders();

        //Get active retailers to be displayed that contain active not deleted store products
        $data['retailers'] = $this -> retailermodel -> get_retailers_having_store_products();

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        $this -> template -> front_view('front/home', $data);
    }

    // Display content on faq page when clicked "Need Help" link
    public function faq() {
        $this -> load -> model('front/contentmodel', '', TRUE);

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this -> all_categories;

        // Get FAQ text
        $data['faq_text'] = $this -> contentmodel -> get_content(FAQ);

        $this -> template -> front_view('front/faq', $data, 1);
    }

    // Display user details on click of profile link to edit them
    public function edit_profile() {

        $this -> load -> model('front/contentmodel', '', TRUE);

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this -> all_categories;

        // Get my basket data
        $data['user_basket'] = $this -> usermodel -> get_user_basket();

        // Get my basket count
        $data['user_basket_products_count'] = $this -> usermodel -> get_user_basket_products_count();

        // Get other retailers price
        $user_basket_other_retailer = $this -> usermodel -> get_user_basket_other_retailers();

        $array_retailer = $other_retailer = array();
        foreach ($user_basket_other_retailer as $value) {
            $array_retailer[$value['LogoImage']][] = $value['Price'];
        }
        foreach ($array_retailer as $key => $value) {
            $other_retailer[$key][] = array_sum($value);
            $other_retailer[$key][] = ( count($value) == $data['user_basket_products_count'] ? 1 : 0 );
        }

        $data['user_basket_other_retailer'] = $other_retailer;

        // get user details
        $data['user_details'] = $this -> usermodel -> get_user_details($this -> session -> userdata('userid'));

        $this -> template -> front_view('front/profile', $data, 1);
    }

    public function check_login() {
        if ($this -> session -> userdata('userid')) {
            echo '1';
        }
        else {
            echo '0';
        }
    }

    public function send_help_email() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> form_validation -> set_rules('user_name', 'User Name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('user_email', 'Email', 'trim|required|valid_email|xss_clean');
            $this -> form_validation -> set_rules('email_subject', 'Subjest', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('email_body', 'Email body', 'trim|required|xss_clean');
            if (!$this -> form_validation -> run() == FALSE) {
                $user_name = sanitize($this -> input -> post('user_name'));
                $user_email = sanitize($this -> input -> post('user_email'));
                $email_subject = sanitize($this -> input -> post('email_subject'));
                $email_body = sanitize($this -> input -> post('email_body'));
                $this -> load -> library('email');
                $config['mailtype'] = 'html';

                $mymessage = "<!DOCTYPE HTML PUBLIC =22-//W3C//DTD HTML 4.01 Transitional//EN=22 =22http://www.w3.org/TR/html4/loose.dtd=22>";
                $mymessage .= "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\" /></head><body>";
                $mymessage .= "<p>" . $email_body . "</p>";
                $mymessage .= "</body></html>";


                $this -> email -> initialize($config);
                $this -> email -> from($user_email,$user_name);
                $this -> email -> to($this -> session -> userdata('user_email'));
                //$this -> email -> to('genknooztester1@gmail.com');
                $this -> email -> reply_to($user_email);
                $this -> email -> subject('From TBD: '.$email_subject);
                $this -> email -> message($mymessage);
                if ($this -> email -> send()) {
                //if (1==1) {
                    $this -> result = 1;
                    $this -> message = 'Email has been sent successfully';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to send email. Please try again';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Form has errors. Please fix and try again';
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

    function validate_name($name) {
        $this -> form_validation -> set_message('validate_name', 'Name must contain contain only letters, apostrophe, spaces or dashes.');
        if (preg_match("/^[a-zA-Z'\-\s]+$/", $name)) {
            return true;
        }
        else {
            return false;
        }
    }
}

?>