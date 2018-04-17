<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:PHN
 * Purpose: Category Webservices
 * Date:02-09-2015
 * Dependency: usermodel.php
 */

class Categories extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this->post('api_key');
        validateApiKey($api_key);
        $retArr = array();

        $this->load->model('webservices/categorymodel', '', TRUE);
        $this->load->model('webservices/basketmodel', '', TRUE);
        $this -> load -> model('webservices/advertisementsmodel', '', TRUE);
    }

    /**
     * List the categories
     */
    public function list_post() {
        $category_id = ($this->post('category_id')!="" && $this->post('category_id')!="null") ? $this->post('category_id') : 0;        
        $level       = $this->post('level') ? $this->post('level') : "1";
        $retailer_id = $this->post('retailer_id') ? $this->post('retailer_id') : "";
        $store_id    = $this->post('store_id') ? $this->post('store_id') : "";
        $user_id     = $this->post('user_id') ? $this->post('user_id') : "";        
        $group_id    = $this->post('group_id') ? $this->post('group_id') : 1;
       
       # Categories with Single query 
        $categories = $this->categorymodel->get_all_categories_WIP($category_id, $group_id, $retailer_id, $store_id, $level);
                
        $i = 0;    
        foreach ($categories as $category) {
            
           if ($category['CategoryIcon'])
                $categories[$i]['CategoryIcon'] = (front_url() . CATEGORY_IMAGE_PATH . 'large/' . $category['CategoryIcon']);
        
               
            //if ($category['product_count'])    
            //    $categories[$i]['product_count'] = (int)$category['product_count'];
            
            //$categories[$i]['child_cat_count'] = $category['child_cat_count'];
            
            //$categories[$i]['child_cat_count'] = $category['ChildCategoryCount'];
          
           
            if ($level == '1') {
                unset($categories[$i]['product_count']);
            } 
            
            if ($level == '3') {
                unset($categories[$i]['child_cat_count']);
            }
        
            unset($categories[$i]['ChildCategoryCount']);
            $i++;
        }
   
     
      
        /*
       $categories = $this->categorymodel->get_all_categories($category_id, $group_id,$level);
        $i = 0;
        //Encode image of category
        foreach ($categories as $category) {
            if ($category['CategoryIcon'])
                $categories[$i]['CategoryIcon'] = (front_url() . CATEGORY_IMAGE_PATH . 'large/' . $category['CategoryIcon']);

            if ($level == '2' || $level == '3') {
                //$product_count = $this->categorymodel->get_category_with_product_count($category['Id'], $retailer_id, $store_id, $level);
                //$product_count = $this->categorymodel->get_category_with_product_count_WIP($category['Id'], $retailer_id, $store_id, $level);
                
               $product_count = $this->categorymodel->get_category_and_product_count_WIP($category['Id'], $retailer_id, $store_id, $level);
                   
                //$product_count = 150;
                if (!empty($product_count)) {
                    $categories[$i]['product_count'] = $product_count;
                } else {
                    $categories[$i]['product_count'] = 0;
                }
            }
            if ($level == '2' || $level == '1') {   
               
                # Sub Category Count
                //$child_count = $this->categorymodel->get_child_category_count($category['Id']);
                //if (!empty($child_count)) {
                //    $categories[$i]['child_cat_count'] = $child_count;
                //} else {
                //    $categories[$i]['child_cat_count'] = 0;
               //}
               
                
                $categories[$i]['child_cat_count'] = $category['ChildCategoryCount'];
            }

            unset($categories[$i]['ChildCategoryCount']);
            
            $i++;
        }
        */
       
        $retArr['status'] = SUCCESS;
        $retArr['categories'] = array($categories);        
        //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
        $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    
    /**
     * Save subcategory count for each category
     */
    public function set_category_count_post() {        
        $this -> db -> select('c.Id,c.CategoryName');
        $query = $this -> db -> get('categories as c');           
        $allCategories = $query -> result_array();
        
        foreach($allCategories as $category)
        {
          $child_count = $this->categorymodel->get_child_category_count($category['Id']);  
          
          $data = array(
                'ChildCategoryCount' => $child_count                
            );
          
          $this -> db -> where('Id', $category['Id']);
          $this -> db -> update('categories', $data);
        }
        
        $retArr['status'] = SUCCESS;        
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    
    /**
     * Save subcategory count for each category
     */
    public function get_advertisements_post() {                
        $category_id = ($this->post('category_id')!="" && $this->post('category_id')!="null") ? $this->post('category_id') : 0;
        
        # Get adds 
        $advertisements = $this -> advertisementsmodel -> get_all_advertisements($category_id); 
        
        if($advertisements)
        {
            $adsIndex =0;
            foreach($advertisements as $advertisement)
            {
                if ( $advertisement['AdvertisementImage'] )
                    $advertisements[$adsIndex]['AdvertisementImage'] = (front_url() . ADVERTISEMENT_IMAGE_PATH . "original/" . $advertisement['AdvertisementImage']);
                else
                    $ads[$adsIndex]['AdvertisementImage'] = (front_url() . DEFAULT_ADVERTISEMENT_IMAGE_PATH);

                $adsIndex++;
            }

            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Advertisement(s) available";
            $retArr['advertisements'] = ($advertisements);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else{
            $retArr['status'] = FAIL;
            $retArr['message'] = "Advertisement(s) not available";
            $retArr['advertisements'] = ($advertisements);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
}
?>