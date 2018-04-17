<?php

/*
 * Author:MK
 * Purpose:Loyalty Points Controller
 * Date:01-03-2017
 * Dependency: loyaltyproductmodel.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Loyaltypoints extends My_Controller {

    private $message;
    private $result;

    function __construct() {
        parent::__construct();
        
        # Load required models        
        $this -> load -> model('admin/loyaltypointmodel', '', TRUE);  
        $this -> load -> model('admin/usermodel', '', TRUE);  

        # Set default values
        $this -> page_title = "Loyalty Points";
        $this -> breadcrumbs[] = array('label' => 'Loyalty Points', 'url' => '/loyaltypoints');

        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    /*
     * Method Name: index
     * Purpose: Shows all loyalty products 
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    
    public function index() {
        $this -> page_title = "Loyalty points";
        $this -> breadcrumbs[0] = array('label' => 'Loyalty Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Loyalty points', 'url' => '/loyaltypoints/index');
        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/loyaltypoints/index', $data);
    }

    /*
     * Method Name: datatable
     * Purpose: Get loyalty points fro the users
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    public function datatable() {        
        $this -> datatables -> select("users.Id as u_id, CONCAT_WS( ' ', users.FirstName, users.LastName ) as Name,users.Email,users.Mobile")
            -> unset_column('u_id')
            -> unset_column('active')
            -> unset_column('RoleId')
            -> from('users')
            -> join('retailers', 'retailers.RetailerAdminId = users.Id', 'left')
            -> where(array('users.IsRemoved' => 0, 'users.UserRole' => 0))
            //-> where(array('users.IsRemoved' => 0, 'users.UserRole' => 4))                                                  
            -> add_column('Actions', loyaltypoints_get_buttons('$1'), 'u_id');
            
        //$this->db->where("(s.SocialMedia = '$socialMedia' OR s.SocialMedia = '')");
        
        echo $this -> datatables -> generate();
    }
    
    
    public function showLoyalty($userId) {
        
        # Set defaults        
        $loyaltyDetails = array();
        $loyaltyDetails['current_date'] = date('d M Y');
        $loyaltyDetails['points_earned_from_reviews'] = 0;
        $loyaltyDetails['points_earned_from_product_shares'] = 0;
        $loyaltyDetails['points_earned_from_app_shares'] = 0;
        $loyaltyDetails['points_earned_from_checkin'] = 0;
        $loyaltyDetails['total_points_earned_this_month'] = 0;
        $loyaltyDetails['total_points_earned_to_date'] = 0;
        $loyaltyDetails['total_points_redeemed_this_month'] = 0;
        $loyaltyDetails['total_points_redeemed_to_date'] = 0;
        $loyaltyDetails['current_point_balance'] = 0;        
              
        
        # Get loyalty earned details till date
        $loyaltyEarnedDetails = $this -> loyaltypointmodel -> get_loyalty_earned_details($userId); 
        
        if($loyaltyEarnedDetails)
        {
            $total_points_earned_to_date = $loyaltyEarnedDetails['userProductReviews'] + $loyaltyEarnedDetails['userProductShares'] + $loyaltyEarnedDetails['userAppShares']+$loyaltyEarnedDetails['userCheckIns'];
            $loyaltyDetails['points_earned_from_reviews']        = $loyaltyEarnedDetails['userProductReviews'];
            $loyaltyDetails['points_earned_from_product_shares'] = $loyaltyEarnedDetails['userProductShares'];
            $loyaltyDetails['points_earned_from_app_shares']     = $loyaltyEarnedDetails['userAppShares'];
            $loyaltyDetails['points_earned_from_checkin']     = $loyaltyEarnedDetails['userCheckIns'];
            $loyaltyDetails['total_points_earned_to_date']       = $total_points_earned_to_date;
        }
        
        # Get loyalty earned details for month
        $dt         = new DateTime( date("Y-m-d") ); 
        $startDate  = date('Y-m-01')." 00:00:00";
        $endDate    = $dt->format( 'Y-m-t' )." 23:59:59";
        
        $loyaltyEarnedDetailsForMoth = $this -> loyaltypointmodel -> get_loyalty_earned_details($userId, $startDate, $endDate);
        
        if($loyaltyEarnedDetailsForMoth )
        {
            $total_points_earned_this_month = $loyaltyEarnedDetailsForMoth['userProductReviews'] + $loyaltyEarnedDetailsForMoth['userProductShares'] + $loyaltyEarnedDetailsForMoth['userAppShares']+$loyaltyEarnedDetails['userCheckIns'];
            $loyaltyDetails['total_points_earned_this_month'] = $total_points_earned_this_month;
        }
        
        # Get loyalty redeemed details
        $redeemedDetails = $this -> loyaltypointmodel -> get_loyalty_redeemed_details($userId);
        $redeemedDetailsForMonth = $this -> loyaltypointmodel -> get_loyalty_redeemed_details($userId, $startDate, $endDate);        
                
        if($redeemedDetails )
        {
            $loyaltyDetails['total_points_redeemed_to_date'] = $redeemedDetails['loyalty_consumption'];
        }
        
         if($redeemedDetailsForMonth )
        {
            $loyaltyDetails['total_points_redeemed_this_month'] = $redeemedDetailsForMonth['loyalty_consumption'];
        }        
       
        # Get User's balance loyalty points 
        $loyaltyDetails['current_point_balance'] = $this -> loyaltypointmodel -> get_loyalty_balance($userId);
        
        $data = $loyaltyDetails;
        
        $this -> breadcrumbs[] = array('label' => 'Show Loyalty', 'url' => 'users/showLoyalty/' . $userId);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $html = $this -> load -> view('admin/loyaltypoints/show_loyalty', $data, true);
        
        $userDetails = $this -> usermodel -> get_user_details($userId);
        
        $name = $userDetails['FirstName'].' '.$userDetails['LastName'];
        echo json_encode(array(
            'html' => $html,
            'name' => $name
        ));
    }
    
    
}
