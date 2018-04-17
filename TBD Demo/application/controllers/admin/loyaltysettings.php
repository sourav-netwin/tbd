<?php
/*
 * Author:MK
 * Purpose:Loyaltysettings controller
 * Date:06-12-2016
 * Dependency: loyaltysettings.php
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Loyaltysettings extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
        $this -> load -> model('admin/loyaltysettingsmodel', '', TRUE);

        $this -> page_title = "Loyalty Settings";
        $this -> breadcrumbs[] = array('label' => 'Loyalty Settings', 'url' => '/loyaltysettings');
    }

    /**
     * Send notification landing page
     */
    public function index() {
        
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {            
            //Update Settings
            $this -> form_validation -> set_rules('app_install', 'App install', 'trim|required|integer|xss_clean');            
            $this -> form_validation -> set_rules('product_reviews', 'Product reviews', 'trim|required|integer|xss_clean');
            $this -> form_validation -> set_rules('store_checkin', 'Store checkin', 'trim|required|integer|xss_clean');
            $this -> form_validation -> set_rules('referrer', 'referrer', 'trim|required|integer|xss_clean');
            
            $this -> form_validation -> set_rules('app_shares_facebook', 'App shares facebook', 'trim|required|integer|xss_clean');            
            $this -> form_validation -> set_rules('app_shares_twitter', 'App shares twitter', 'trim|required|integer|xss_clean');
            $this -> form_validation -> set_rules('app_shares_email', 'App shares email', 'trim|required|integer|xss_clean');            
            $this -> form_validation -> set_rules('app_shares_google', 'App shares google', 'trim|required|integer|xss_clean');
            $this -> form_validation -> set_rules('app_shares_whatsApp', 'App shares whatsApp', 'trim|required|numeric|xss_clean');            
            
            $this -> form_validation -> set_rules('product_shares_facebook', 'Product shares facebook', 'trim|required|integer|xss_clean');            
            $this -> form_validation -> set_rules('product_shares_twitter', 'Product shares twitter', 'trim|required|integer|xss_clean');
            $this -> form_validation -> set_rules('product_shares_email', 'Product shares email', 'trim|required|integer|xss_clean');            
            $this -> form_validation -> set_rules('product_shares_google', 'Product shares google', 'trim|required|integer|xss_clean');
            $this -> form_validation -> set_rules('product_shares_whatsApp', 'Product shares whatsApp', 'trim|required|numeric|xss_clean');            
            
            if (!$this -> form_validation -> run() == FALSE) {
                $id = $this -> input -> post('id');
                $edit_data = array(
                        'app_install' => $this -> input -> post('app_install'),
                        'product_reviews' => $this -> input -> post('product_reviews'),
                        'store_checkin' => $this -> input -> post('store_checkin'),
                        'referrer'=> $this -> input -> post('referrer'),                        
                        'app_shares_facebook' => $this -> input -> post('app_shares_facebook'),
                        'app_shares_twitter' => $this -> input -> post('app_shares_twitter'),
                        'app_shares_email' => $this -> input -> post('app_shares_email'),
                        'app_shares_google' => $this -> input -> post('app_shares_google'),
                        'app_shares_whatsApp' => $this -> input -> post('app_shares_whatsApp'),                        
                        'product_shares_facebook' => $this -> input -> post('product_shares_facebook'),
                        'product_shares_twitter' => $this -> input -> post('product_shares_twitter'),
                        'product_shares_email' => $this -> input -> post('product_shares_email'),
                        'product_shares_google' => $this -> input -> post('product_shares_google'),
                        'product_shares_whatsApp' => $this -> input -> post('product_shares_whatsApp'),                    
                        'ModifiedBy' => $this -> session -> userdata('user_id'),
                        'ModifiedOn' => date('Y-m-d H:i:s')
                   );

                   $result = $this -> loyaltysettingsmodel -> update_settings($id, $edit_data);
                   $this -> session -> set_userdata('success_message', "Loyalty settings updated successfully");                    
                   $this -> result = 1;
                   $this -> message = 'Loyalty settings updated successfully';
                    
            }else{
                $this -> result = 0;
                $this -> message = $this -> form_validation -> error_array();
            }
            
        }
        
        $data['title'] = $this -> page_title;
        $this -> breadcrumbs[0] = array('label' => 'Loyalty Settings', 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['loyaltySettings'] = $this -> loyaltysettingsmodel -> get_loyalty_setttings();
        
        $this -> template -> view('admin/loyaltysettings/index', $data);
    }
}