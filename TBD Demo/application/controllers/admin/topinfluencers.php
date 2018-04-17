<?php

/*
 * Author:MK
 * Purpose: Topinfluencers Controller
 * Date:07-04-2017
 * Dependency: topinfluencersmodel.php , loyaltypointmodel.php
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
class Topinfluencers extends My_Controller {

    private $message;
    private $result;

    function __construct() {
        parent::__construct();
        
        # Load required models        
        $this -> load -> model('admin/loyaltypointmodel', '', TRUE);  
        $this -> load -> model('admin/usermodel', '', TRUE);  

        # Set default values
        $this -> page_title = "Top Influencers";
        $this -> breadcrumbs[] = array('label' => 'Top Influencers', 'url' => '/topinfluencers');

        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    /*
     * Method Name: index
     * Purpose: Shows all mobile users with the loyalty points summary 
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    
    public function index() {
        $this -> page_title = "Top Influencers";
        $this -> breadcrumbs[0] = array('label' => 'Loyalty Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Top Influencers', 'url' => '/topinfluencers/index');
        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/topinfluencers/index', $data);
    }

    /*
     * Method Name: datatable
     * Purpose: Get loyalty points for the users
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    public function datatable() {         
        # calculate loyalty points 
        //$calResult = $this -> loyaltypointmodel -> calculate_loyalty_for_all_users(); 
        
        # Get Records 
        $this -> datatables -> select("users.Id as u_id, CONCAT_WS( ' ', users.FirstName, users.LastName ) as Name,users.Email,users.Mobile, EarnedLoyaltyPoints, ConsumedLoyaltyPoints, BalanceLoyaltyPoints")
            -> unset_column('u_id')
            -> unset_column('active')
            -> unset_column('RoleId')
            -> from('users')
            -> join('retailers', 'retailers.RetailerAdminId = users.Id', 'left')
            //-> where(array('users.IsRemoved' => 0, 'users.UserRole' => 0))
            -> where(array('users.IsRemoved' => 0, 'users.UserRole' => 4))                                                  
            -> add_column('Actions', loyaltypoints_get_buttons('$1'), 'u_id');
           // ->order_by('EarnedLoyaltyPoints','DESC');
        echo $this -> datatables -> generate();
    }
}