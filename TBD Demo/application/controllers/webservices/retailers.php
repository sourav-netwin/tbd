<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:PHN
 * Purpose: Retailers Webservices
 * Date:02-09-2015
 * Dependency: usermodel.php
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Retailers extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this->post('api_key');

        validateApiKey($api_key);

        $retArr = array();

        $this->load->model('webservices/retailermodel', '', TRUE);
    }

    /**
     * List all the retailers
     */
    public function list_post() {
        # Get request parameters 
        $group_id = $this->post('group_id') ? $this->post('group_id') : 1;
        
        $retailers = $this->retailermodel->get_retailers($group_id);
        $store_groups = $this -> retailermodel -> get_store_groups();
        
        $i = 0;
        //Encode image of category
        foreach ($retailers as $retailer) {
            if ($retailer['LogoImage'])
                $retailers[$i]['LogoImage'] = (front_url() . RETAILER_IMAGE_PATH."medium/" . $retailer['LogoImage']);
            $i++;
        }
        $retArr['status'] = SUCCESS;
        $retArr['retailers'] = ($retailers);
        $retArr['store_groups'] = ($store_groups);
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    /**
     *  Save preferred user for a retailer
     */
    public function save_user_preference_post() {

        $user_id = $this->post('user_id') ? $this->post('user_id') : "";
        $retailer_id = $this->post('retailer_id') ? $this->post('retailer_id') : "";
        $store_id = $this->post('store_id') ? $this->post('store_id') : "";
        $lat = $this->post('latitude') ? $this->post('latitude') : "";
        $long= $this->post('longitude') ? $this->post('longitude') : "";
        $group_id = $this->post('group_id') ? $this->post('group_id') : 1;
        

        // get store listing for selected retailers.
        //$stores = $this->retailermodel->get_stores($retailer_id,$lat,$long,$user_id);
        
        $stores = $this->retailermodel->get_stores($retailer_id,$lat,$long,$user_id,$group_id);
        
        //replace null values
        $i = 0;
        foreach ($stores as $store) {
           
            
            # Get the specials count for the store
            # Added on 21 March 2017
            /*
            $storeId = $store['Id'];
            $storesSpecials = $this->retailermodel->get_stores_specials($storeId);            
            $stores[$i]['HaveSpecial']= $storesSpecials > 0 ? 1 :0;
            */
            
            $stores[$i]['HaveSpecial']= $store['storesSpecialsCount'] > 0 ? 1 :0;
            
            if ($store['distance'] == NULL) {
                 $stores[$i]['distance']="";
            }
            $i++;
        }
       
        $result = $this->retailermodel->save_retailer_user($user_id, $retailer_id,$store_id);
        $result = TRUE;
        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['stores'] = $stores;
            $retArr['message'] = "Retailer added successfully";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function get_user_preference_post() {

        $user_id = $this->post('user_id') ? $this->post('user_id') : "";

        $result = $this->retailermodel->get_saved_retailer($user_id);

        //replace null values
        foreach ($result as $key => $value) {
            if (is_null($value)) {
                $result[$key] = "";
            }
        }

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['retailer'] = $result;
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function save_store_post() {

        $user_id = $this->post('user_id') ? $this->post('user_id') : "";

        $store_id = $this->post('store_id') ? $this->post('store_id') : "";

        $result = $this->retailermodel->save_store_user($user_id, $store_id);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Retailer store added successfully";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    /* Set Grocessary Group fro all stores  */
    public function map_all_retailers_with_grocessary_post() {
        
        // get retailer listing for selected retailers.
        $retailers = $this->retailermodel->get_all_retailers();
        foreach ($retailers as $retailer) {
            $data = array(                
                'RetailerId' => $retailer['Id'],                
                'StoreGroupId' => 1
            );
            
            $this -> db -> insert('retailers_storegroups', $data);
            $ids =  $this -> db -> insert_id();
        }
        
    }
    
    /* Set Grocessary Group fro all stores  */
    public function map_all_stores_with_grocessary_post() {
        
        // get store listing for selected retailers.
        $stores = $this->retailermodel->get_all_stores();
        foreach ($stores as $store) {
            $data = array(                
                'StoreId' => $store['Id'],                
                'StoreGroupId' => 1
            );
            
            $this -> db -> insert('stores_storegroups', $data);
            $ids =  $this -> db -> insert_id();
        }
        
    }
    /**/
    
    /* Set Grocessary Group fro all storeformats  */
    public function map_all_storesformat_with_grocessary_post() {
        $storeformats = $this->retailermodel->get_all_storeformats();
        foreach ($storeformats as $storeformat) {
            $data = array(                
                'StoreFormatId' => $storeformat['Id'],                
                'StoreGroupId' => 1
            );
            
            $this -> db -> insert('storeformats_storegroups', $data);
            $ids =  $this -> db -> insert_id();
        }
        
    }
    /**/
    
    
    /* Set Grocessary Group for parent categories  */
    public function map_all_categories_with_grocessary_post() {
        
        // Get store listing for parent categories.
        $categories = $this->retailermodel->get_parent_categories();
        foreach ($categories as $category) {
            $data = array(                
                'CategoryId' => $category['Id'],                
                'StoreGroupId' => 1
            );
            
            $this -> db -> insert('categories_storegroups', $data);
            $ids =  $this -> db -> insert_id();
        }
        
    }
    /**/    
    
    
    /**
     *  Save user's prefferred Retailer and stores 
     */
    public function save_preferences_post() {
        $user_id = $this->post('user_id') ? $this->post('user_id') : "";
        $retailer_id = $this->post('retailer_id') ? $this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;
        $lat = $this->post('latitude') ? $this->post('latitude') : "";
        $long= $this->post('longitude') ? $this->post('longitude') : "";
        $group_id = $this->post('group_id') ? $this->post('group_id') : 1;
        
        $limit = "1"; 
        
        //$stores = array();
        
        // Get store listing for selected retailers.
        $stores = $this->retailermodel->get_retailers_stores($retailer_id,$store_id,$lat,$long,$user_id,$group_id,$limit);

        //replace null values
        $i = 0;
        foreach ($stores as $store) {
            $store_id = $store_id == 0 ? $stores[0]['Id'] : $store_id;
            
            
            # Get the specials count for the store
            //$stores[$i]['HaveSpecial']= $store['storesSpecialsCount'] > 0 ? 1 :0;
            
            
         
            $storesSpecialsCount = 0;
            $storesSpecialsDetails = $this->retailermodel->get_stores_specials_count($store['Id']);
            if($storesSpecialsDetails)
            {
                $storesSpecialsCount = $storesSpecialsDetails['storesSpecialsCount'];
            }
            $stores[$i]['storesSpecialsCount']= $storesSpecialsCount;
            $stores[$i]['HaveSpecial']= $storesSpecialsCount > 0 ? 1 :0;
            
            
            if ($store['distance'] == NULL) {
                $stores[$i]['distance']="";
            }
            $i++;
        }
       
        
        $result = $this->retailermodel->save_preferences($user_id, $retailer_id,$store_id);
        $result = TRUE;
        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['stores'] = $stores;
            $retArr['message'] = "Retailer added successfully";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }    
    
     /**
     *  Get Stores for the retailer based on group
     */
    public function get_retailer_stores_post() {
        $user_id = $this->post('user_id') ? $this->post('user_id') : "";
        $retailer_id = $this->post('retailer_id') ? $this->post('retailer_id') : 0;       
        $lat = $this->post('latitude') ? $this->post('latitude') : "";
        $long= $this->post('longitude') ? $this->post('longitude') : "";
        $group_id = $this->post('group_id') ? $this->post('group_id') : 1;
        $limit = ""; 
        
        // Get store listing for selected retailers.
        $stores = $this->retailermodel->get_retailers_stores($retailer_id,0,$lat,$long,$user_id,$group_id,$limit);

        if($stores)
        {
            //replace null values
            $i = 0;
            foreach ($stores as $store) {
                # Get the specials count for the store
                //$stores[$i]['HaveSpecial']= $store['storesSpecialsCount'] > 0 ? 1 :0;
                                
                $storesSpecialsCount = 0;
                $storesSpecialsDetails = $this->retailermodel->get_stores_specials_count($store['Id']);
                if($storesSpecialsDetails)
                {
                    $storesSpecialsCount = $storesSpecialsDetails['storesSpecialsCount'];
                }
                $stores[$i]['storesSpecialsCount']= $storesSpecialsCount;
                $stores[$i]['HaveSpecial']= $storesSpecialsCount > 0 ? 1 :0;
                
                if ($store['distance'] == NULL) {
                    $stores[$i]['distance']="";
                }
                $i++;
            }

            $retArr['status'] = SUCCESS;
            $retArr['stores'] = $stores;
            $retArr['message'] = "Store(s) available.";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }else{
            $retArr['status'] = FAIL;
            $retArr['stores'] = $stores;
            $retArr['message'] = "Store(s) not available.";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
}