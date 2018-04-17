<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:PHN
 * Purpose: User Webservices
 * Date:02-09-2015
 * Dependency: usermodel.php
 */

class Dummy extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this -> post('api_key');

        validateApiKey($api_key);

        $retArr = array();

        $this -> load -> model('webservices/usermodel', '', TRUE);
        $this -> load -> model('webservices/statemodel', '', TRUE);
        $this -> load -> model('webservices/devicemodel', '', TRUE);
        $this -> load -> model('webservices/searchmodel', '', TRUE);
        $this -> config -> load('topoffers');
        $this -> load -> model('webservices/basketmodel', '', TRUE);
        $this -> load -> model('webservices/productmodel', '', TRUE);
        
    }

    
    /**
     * Get product categories with product count 
     */
    public function search_product_categories_post() {
        $user_id        = $this -> post('user_id');
        $search_text    = $this -> post('search_text');
        $category_list  = array();
        $categories     = array();
        
        $product_list = $this -> searchmodel -> get_categories_with_products($search_text, $user_id);
        
        if ($product_list) {
            
            # Collect products information based on category
            foreach ($product_list as $product) {
                $categoryName = trim($product['ProductCategoryName']);
                
                if(array_key_exists($categoryName, $category_list))
                {
                   $category_list[$categoryName]['ProductCategoryId'] = $product['ProductCategoryId'];
                   $category_list[$categoryName]['ProductCount'] = $category_list[$categoryName]['ProductCount']+1;
                   //$category_list[$categoryName]['ProductIds'] = $category_list[$categoryName]['ProductIds'].",".$product['Id']; 
                }else{
                   $category_list[$categoryName]['ProductCategoryId'] = $product['ProductCategoryId'];
                   $category_list[$categoryName]['ProductCount'] = 1;
                   //$category_list[$categoryName]['ProductIds'] = $product['Id']; 
                }
            }
            
            # Make an array for all categories with product information
            $i = 0;
            foreach ($category_list as $key => $value) {
               $categories[$i]['ProductCategoryName']   = $key;  
               $categories[$i]['ProductCategoryId']     = $value['ProductCategoryId']; 
               $categories[$i]['ProductCount']          = $value['ProductCount']; 
               //$categories[$i]['ProductIds']            = $value['ProductIds']; 
               $i++; 
            }
                        
            $retArr['status'] = SUCCESS;
            $retArr['categories'] = ($categories);
            $retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = SUCCESS;
            $retArr['categories'] = array();
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    
    
    /**
     * Get product categories with product count 
     */
    public function search_product_by_category_post() {
        $user_id        = $this -> post('user_id');
        $search_text    = $this -> post('search_text');
        $category_id    = $this -> post('category_id') ? $this -> post('category_id') : 0; 
        
        $product_list = $this -> searchmodel -> get_products_by_category($search_text, $user_id,$category_id);
        
        if ($product_list) {
            $i = 0;
            foreach ($product_list as $product) {
                if ($product['ProductImage']) {
                    $product_list[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
                }
                else {
                    $product_list[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);
                }
                
                # Check if product is favourite for the user
                $is_favorite = $this -> productmodel ->is_product_favorite($user_id, $product['Id'], $product['special_id']);            
                $product_list[$i]['is_favorite'] = $is_favorite;
                

                if ($product['avg_rating'] == NULL) {
                    $product_list[$i]['avg_rating'] = "0";
                }

                if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                    $product_list[$i]['SpecialQty'] = "0";
                    $product_list[$i]['SpecialPrice'] = "0";
                }
//                $product_list[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                $product_list[$i]['PageUrl'] = 'https://play.google.com/store/apps/details?id=com.thebestdeals';

                $i++;
            }
            $retArr['status'] = SUCCESS;
            $retArr['product_details'] = ($product_list);
            $retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = SUCCESS;
            $retArr['product_details'] = array();            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    
}