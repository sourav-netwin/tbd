<?php

/*
 * Author:MK
 * Purpose:Loyalty Points Controller
 * Date:01-03-2017
 * Dependency: loyaltyproductmodel.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Promotions extends My_Controller {

    private $message;
    private $result;

    function __construct() {
        parent::__construct();
        
        # Load required models        
        //$this -> load -> model('admin/promotionsmodel', '', TRUE);  
        $this -> load -> model('admin/usermodel', '', TRUE);  

        # Set default values
        $this -> page_title = "Loyalty Points";
        $this -> breadcrumbs[] = array('label' => 'Promotions', 'url' => '/promotions');

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
        $this -> page_title = "Promotions";
        $this -> breadcrumbs[0] = array('label' => 'Promotions Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Active Promotions', 'url' => '/promotions/index');
        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/promotions/index', $data);
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
        /*
        $this -> datatables -> select("users.Id as u_id, CONCAT_WS( ' ', users.FirstName, users.LastName ) as Name,users.Email,users.Mobile")
            -> unset_column('u_id')
            -> unset_column('active')
            -> unset_column('RoleId')
            -> from('users')
            -> join('retailers', 'retailers.RetailerAdminId = users.Id', 'left')
            -> where(array('users.IsRemoved' => 0, 'users.UserRole' => 0))
            //-> where(array('users.IsRemoved' => 0, 'users.UserRole' => 4))                                    
            -> add_column('Actions', loyaltypoints_get_buttons('$1'), 'u_id');
            
        echo $this -> datatables -> generate();
         
         */
        
        $currentDate = date('Y-m-d');
        
        $this -> datatables -> select("specials.Id,specials.SpecialName, specials.SpecialFrom, specials.SpecialTo, retailers.CompanyName as RetailerName")
            -> unset_column('Id')
            -> unset_column('active')            
            -> from('specials')                
            -> join('special_stores', 'special_stores.SpecialId = specials.Id', 'left')
            -> join('retailers', 'retailers.Id = special_stores.RetailerId')
            -> where(array('specials.IsRemoved' => 0, 'specials.IsActive' => 1))
            -> where('specials.SpecialFrom <=',$currentDate)
            -> where('specials.SpecialTo >=',$currentDate)    
            -> group_by('specials.Id');
            //-> add_column('Actions', loyaltypoints_get_buttons('$1'), 'Id');
        
        echo $this -> datatables -> generate();
        
    }
}
