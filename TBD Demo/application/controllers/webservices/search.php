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

class Search extends REST_Controller {

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
     * Get search details
     */
    public function search_product_post() {

        $user_id = $this -> post('user_id');
        $search_text = $this -> post('search_text');
        $start = $this -> post('start') ? $this -> post('start') : 0;
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
        
        $retailer_id = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;
        
        //$product_list = $this -> searchmodel -> get_products("", "", "", "", "", "", $search_text, $user_id, $start);
        $product_list = $this -> searchmodel -> get_products_WIP("", "", "", "", "", "", $search_text, $user_id, $start);
        
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
                
                switch ($device_type) {
                    case "A":
                          $pageUrl = PLAY_STORE_URL;
                          break;
                    case "I":
                          $pageUrl = APP_STORE_URL;
                          break;
                    case "W":
                          $pageUrl = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                          break;
                    default:
                          $pageUrl = PLAY_STORE_URL;
                }

                //$product_list[$i]['PageUrl'] = $pageUrl;
                
                $product_list[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                $product_list[$i]['AppStoreUrl']  = APP_STORE_URL;
                $product_list[$i]['PlayStoreUrl'] = PLAY_STORE_URL;
                
                $i++;
            }
            
            $retArr['status'] = SUCCESS;
            $retArr['product_details'] = ($product_list);
            $retArr['total_pages'] = 1;
            $retArr['products_range'] = array();
            $retArr['next'] = $start + 20;
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = SUCCESS;
            $retArr['total_pages'] = 0;
            $retArr['product_details'] = array();
            $retArr['products_range'] = 0;
            $retArr['next'] = 0;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    /**
     * Get search details
     */
    public function shopping_list_search_product_post() {

        $user_id = $this -> post('user_id');
        $search_text = $this -> post('search_text');
        $start = $this -> post('start') ? $this -> post('start') : 0;
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
        $retailer_id = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;
        
        $product_list = $this -> searchmodel -> get_shopping_list_search_products("", "", "", "", "", "", $search_text, $user_id, $start);
        
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

                
                switch ($device_type) {
                    case "A":
                        $pageUrl = PLAY_STORE_URL;
                        break;
                    case "I":
                        $pageUrl = APP_STORE_URL;
                        break;
                    case "W":
                        $pageUrl = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                        break;
                    default:
                        $pageUrl = PLAY_STORE_URL;
                }

                //$product_list[$i]['PageUrl'] = $pageUrl;
                
                $product_list[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                $product_list[$i]['AppStoreUrl']  = APP_STORE_URL;
                $product_list[$i]['PlayStoreUrl'] = PLAY_STORE_URL;

                $i++;
            }
            $retArr['status'] = SUCCESS;
            $retArr['product_details'] = ($product_list);
            $retArr['total_pages'] = 1;
            $retArr['products_range'] = array();
            $retArr['next'] = $start + 20;            
            
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = SUCCESS;
            $retArr['total_pages'] = 0;
            $retArr['product_details'] = array();
            $retArr['products_range'] = 0;
            $retArr['next'] = 0;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /**
     * Search referer users
     */
    public function search_referer_users_post() {

        $user_id = $this -> post('user_id');
        $search_text = $this -> post('search_text');        
        $referer_users = $this -> usermodel -> referer_users($search_text, $user_id);
        
        if ($referer_users) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Referer Users found.";
            $retArr['referer_users'] = $referer_users;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Referer Users not found.";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /**
     * Get products for chatbot search
     */
    public function chatbot_search_product_post() {

        $user_id = $this -> post('user_id');
        $search_text = $this -> post('search_text');
        $start = $this -> post('start') ? $this -> post('start') : 0;
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
        $retailer_id = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;

        //Get user preference
        $user_preference = $this -> searchmodel ->get_user_preference($user_id);
        
        //$product_list = $this -> searchmodel -> get_products("", "", "", "", "", "", $search_text, $user_id, $start);
        $product_list = $this -> searchmodel -> get_products_WIP("", "", "", "", "", "", $search_text, $user_id, $start);
        
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
                
                switch ($device_type) {
                    case "A":
                        $pageUrl = PLAY_STORE_URL;
                        break;
                    case "I":
                        $pageUrl = APP_STORE_URL;
                        break;
                    case "W":
                        $pageUrl = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                        break;
                    default:
                        $pageUrl = PLAY_STORE_URL;
                }

                //$product_list[$i]['PageUrl'] = $pageUrl;
                
                $product_list[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                $product_list[$i]['AppStoreUrl']  = APP_STORE_URL;
                $product_list[$i]['PlayStoreUrl'] = PLAY_STORE_URL;

                $i++;
            }
            $retArr['status'] = SUCCESS;
            $retArr['user_preferred_brands'] = $user_preference ;
            $retArr['product_details'] = ($product_list);
            $retArr['total_pages'] = 1;
            $retArr['products_range'] = array();
            $retArr['next'] = $start + 20;                        
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = SUCCESS;
            $retArr['total_pages'] = 0;
            $retArr['user_preferred_brands'] = $user_preference;
            $retArr['product_details'] = array();
            $retArr['products_range'] = 0;
            $retArr['next'] = 0;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    /**
     * Get products for chatbot search
     */
    public function chatbot_get_product_specials_stores_post() {

        $user_id    = $this -> post('user_id');
        $product_id = $this -> post('product_id');
        $lat        = $this->post('latitude') ? $this->post('latitude') : 0;
        $long       = $this->post('longitude')? $this->post('longitude') : 0;
        $prefDistance   = 0;
        
        
        # Get user preference
        $user_preference = $this -> searchmodel -> get_user_preference_details($user_id);
        
        if($user_preference){
            //$lat            = $user_preference['PrefLatitude'];
            //$long           = $user_preference['PrefLongitude'];
            $prefDistance   = $user_preference['PrefDistance'];
        }
        
        if($lat== 0 && $long==0)
        {
           $prefDistance   = 0; 
        }
        
        $stores_list = $this -> searchmodel -> chatbot_get_product_specials_stores($user_id, $product_id, $lat,$long);
        
        # Show only those stores which are within the user geofense ( preferred diatance)
        if($prefDistance > 0 )
        {
            $index = 0;
            $final_stores =array();
            foreach ($stores_list as $singleStore) {
                
                if($singleStore['distance'] <= $prefDistance && $singleStore['store_price'] > 0 )
                //if($singleStore['distance'] <= $prefDistance )
                {
                    //$final_stores[$index]['distance']            = $singleStore['distance'];
                    $final_stores[$index]['productSpecialId']    = $singleStore['productSpecialId'];
                    $final_stores[$index]['ProductId']           = $singleStore['ProductId'];
                    $final_stores[$index]['RetailerId']          = $singleStore['RetailerId'];
                    $final_stores[$index]['StoreTypeId']         = $singleStore['StoreTypeId'];
                    $final_stores[$index]['StoreId']             = $singleStore['StoreId'];
                    $final_stores[$index]['ProductName']         = $singleStore['ProductName'];
                    $final_stores[$index]['RetailerName']        = $singleStore['RetailerName'];                    
                    $final_stores[$index]['StoreName']           = $singleStore['StoreName'];
                    $final_stores[$index]['SpecialQty']          = $singleStore['SpecialQty'];
                    $final_stores[$index]['SpecialPrice']        = $singleStore['SpecialPrice'];
                    $final_stores[$index]['store_price']         = $singleStore['store_price'];
                    $index++;
                } //if($singleStore['distance'] <= $prefDistance)
            }//foreach ($stores_list as $singleStore)
        }else{
            $final_stores = $stores_list;
        }
        
        if ($final_stores) {
            $retArr['status'] = SUCCESS;           
            //$retArr['stores_list'] = $stores_list;
            $retArr['stores_list'] = $final_stores;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else{
            $retArr['status'] = SUCCESS;           
            $retArr['stores_list'] = array();
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /**
     * Get products for chatbot search
     */
    public function chatbot_get_product_stores_post() {        
        $user_id    = $this -> post('user_id');
        $product_id = $this -> post('product_id');
        $lat        = $this->post('latitude') ? $this->post('latitude') : 0;
        $long       = $this->post('longitude')? $this->post('longitude') : 0;
        $prefDistance   = 0;
        
        # Get user preference
        $user_preference = $this -> searchmodel -> get_user_preference_details($user_id);
        
        if($user_preference){
            //$lat            = $user_preference['PrefLatitude'];
            //$long           = $user_preference['PrefLongitude'];
            $prefDistance   = $user_preference['PrefDistance'];
        }
        
        if($lat== 0 && $long==0)
        {
           $prefDistance   = 0; 
        }
        
        # Get user preference
        $productDetails = $this -> searchmodel -> get_product_details($product_id);
        
        # Get user preference
        $allStores = $this -> searchmodel -> get_all_stores($lat,$long,$prefDistance);
        
        //$storeIds = $this -> searchmodel -> get_all_stores($lat,$long,$prefDistance);
        
        $index = 0;
        $final_stores =array();
        $storeIds = array();
        $stores_list = array();
        
        foreach ($allStores as $sglStore) {
            $storeIds[] = $sglStore['Id'];
           
            //    $final_stores[$index]['StoreId']    = $sglStore['Id'];
            //    $final_stores[$index]['StoreName']  = $sglStore['StoreName'];
            //    $final_stores[$index]['distance']   = $sglStore['distance'];
            //    $index++;
        } 
        
        if($storeIds)
        {
            $stores_list = $this -> searchmodel -> chatbot_get_product_stores($user_id, $product_id, $lat,$long,$prefDistance,$storeIds);
        }
        
        # Show only those stores which are within the user geofense ( preferred diatance)
        if($prefDistance > 0 )
        {
            $index = 0;
            //$final_stores =array();
            foreach ($stores_list as $singleStore) {
                
                if($singleStore['distance'] <= $prefDistance && $singleStore['store_price'] > 0 )
                {
                    if($singleStore['Special_Qty_Price'] != NULL || $singleStore['Special_Qty_Price'] != "")
                    {
                        $splInfo = explode("-",$singleStore['Special_Qty_Price']);
                        $productSpecialId = $splInfo[0];
                        $specialQty = $splInfo[1];
                        $specialPrice = $splInfo[2];
                    }else{
                        $productSpecialId = "0";
                        $specialQty = "0";
                        $specialPrice = "0";
                    }
                                   
                    $final_stores[$index]['productSpecialId']    = $productSpecialId;                    
                    $final_stores[$index]['ProductId']           = $productDetails['Id'];
                    $final_stores[$index]['RetailerId']          = $singleStore['RetailerId'];
                    $final_stores[$index]['StoreTypeId']         = $singleStore['StoreTypeId'];
                    $final_stores[$index]['StoreId']             = $singleStore['StoreId'];                    
                    $final_stores[$index]['ProductName']         = $productDetails['ProductName'];
                    $final_stores[$index]['RetailerName']        = $singleStore['RetailerName'];                    
                    $final_stores[$index]['StoreName']           = $singleStore['StoreName'];                    
                    $final_stores[$index]['SpecialQty']          = $specialQty;
                    $final_stores[$index]['SpecialPrice']        = $specialPrice;
                    $final_stores[$index]['store_price']         = $singleStore['store_price'];
                    
                    $index++;
                } //if($singleStore['distance'] <= $prefDistance)
            }//foreach ($stores_list as $singleStore)
        }else{
            $final_stores = $stores_list;
        }
        
        if ($final_stores) {
            $retArr['status'] = SUCCESS;           
            //$retArr['stores_list'] = $stores_list;
            $retArr['stores_list'] = $final_stores;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }else{
            $retArr['status'] = SUCCESS;           
            $retArr['stores_list'] = array();
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /**
     * Get product categories with product count 
     */
    public function search_product_categories_post() {
        $user_id        = $this -> post('user_id');
        $search_text    = trim($this -> post('search_text'));
        $retailer_id = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;

        $category_list  = $categories = $product_list = array();
        
        if($user_id > 0 && $search_text !="")
        {
            $product_list = $this -> searchmodel -> get_categories_with_products($search_text, $user_id);
        }
        
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
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
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
     * Get product for the category
     */
    public function search_product_by_category_post() {
        $user_id        = $this -> post('user_id');
        $search_text    = trim($this -> post('search_text'));
        $category_id    = $this -> post('category_id') ? $this -> post('category_id') : 0; 
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
        $retailer_id = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;

        $product_list   = array();
        
        if($user_id > 0 && $search_text !="" && $category_id > 0)
        {
            $product_list = $this -> searchmodel -> get_products_by_category($search_text, $user_id,$category_id);
        }
        
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
                
                switch ($device_type) {
                    case "A":
                        $pageUrl = PLAY_STORE_URL;
                        break;
                    case "I":
                        $pageUrl = APP_STORE_URL;
                        break;
                    case "W":
                        $pageUrl = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                        break;
                    default:
                        $pageUrl = PLAY_STORE_URL;
                }

                //$product_list[$i]['PageUrl'] = $pageUrl;
                
                $product_list[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                $product_list[$i]['AppStoreUrl']  = APP_STORE_URL;
                $product_list[$i]['PlayStoreUrl'] = PLAY_STORE_URL;             

                $i++;
            }
            $retArr['status'] = SUCCESS;
            $retArr['product_details'] = ($product_list);
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
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
    
    
    
    /**
     * Get product categories with product count 
     */
    public function chatbot_search_product_categories_post() {
        $user_id        = $this -> post('user_id');
        $search_text    = trim($this -> post('search_text'));
        $special_only   = strtolower(trim($this -> post('special_only'))) == 'yes' ? 'Yes' : 'No';
        $retailer_id    = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id       = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;
        
        $category_list  = $categories = $product_list = $brand_list = $allBrands = array();
        
        if($user_id > 0 && $search_text !="")
        {
            $product_list = $this -> searchmodel -> chatbot_get_categories_with_products($search_text, $user_id,$special_only);
        }
        
        if ($product_list) {
            $category_list['ALL CATEGORIES']['ProductCategoryId'] = 0;            
                   
            # Collect products information based on category
            foreach ($product_list as $product) {
                $categoryName   = trim(strtoupper($product['ProductCategoryName']));
                $brandName      = trim(strtoupper($product['Brand']));
                $categoryBrandName = $categoryName." - ".$brandName;
                
                if(array_key_exists($categoryName, $category_list))
                {
                   $category_list[$categoryName]['ProductCategoryId'] = $product['ProductCategoryId'];
                   $category_list[$categoryName]['ProductCount'] = $category_list[$categoryName]['ProductCount']+1;                   
                   
                   if(array_key_exists($categoryBrandName, $brand_list))
                   {
                       $brand_list[$categoryBrandName]['BrandProductCategoryId'] = $product['ProductCategoryId'];
                       $brand_list[$categoryBrandName]['BrandName'] = $brandName;
                       $brand_list[$categoryBrandName]['BrandProductCount'] = $brand_list[$categoryBrandName]['BrandProductCount'] + 1;                       
                   }else{
                      $brand_list[$categoryBrandName]['BrandProductCategoryId'] = $product['ProductCategoryId']; 
                      $brand_list[$categoryBrandName]['BrandName'] = $brandName;
                      $brand_list[$categoryBrandName]['BrandProductCount'] = 1; 
                   }
                   
                }else{
                   $category_list[$categoryName]['ProductCategoryId'] = $product['ProductCategoryId'];
                   $category_list[$categoryName]['ProductCount'] = 1;
                   
                   if(array_key_exists($categoryBrandName, $brand_list))
                   {
                       $brand_list[$categoryBrandName]['BrandProductCategoryId'] = $product['ProductCategoryId'];
                       $brand_list[$categoryBrandName]['BrandName'] = $brandName;
                       $brand_list[$categoryBrandName]['BrandProductCount'] = $brand_list[$categoryBrandName]['BrandProductCount'] + 1;                       
                   }else{
                      $brand_list[$categoryBrandName]['BrandProductCategoryId'] = $product['ProductCategoryId']; 
                      $brand_list[$categoryBrandName]['BrandName'] = $brandName;
                      $brand_list[$categoryBrandName]['BrandProductCount'] = 1; 
                   }
                }
            }
            
            $allBrands[0]['BrandProductCategoryId'] = 0;
            $allBrands[0]['BrandName'] = 'ALL BRANDS';
            $allBrands[0]['BrandProductCount'] = 0;
            
            $allBrandindex =1;
            foreach ($brand_list as $brandKey => $brandValue )
            {  
               $allBrands[$allBrandindex]['BrandProductCategoryId'] = $brandValue['BrandProductCategoryId'];   
               $allBrands[$allBrandindex]['BrandName'] = $brandKey;
               $allBrands[$allBrandindex]['BrandProductCount'] = $brandValue['BrandProductCount'];  
               $allBrandindex++;
            }  
            
            # Make an array for all categories with product information
            $i = 0;
            $totalProducts = 0;
            foreach ($category_list as $key => $value) {
               $brands = array(); 
               $index =0;
               foreach ($brand_list as $brandKey => $brandValue )
               {   
                   $brandKeyArr = explode('-',$brandKey);
                   
                   if($key == trim($brandKeyArr[0]))
                   {
                      $brands[$index]['BrandProductCategoryId'] = $brandValue['BrandProductCategoryId'];   
                      $brands[$index]['BrandName'] = $brandValue['BrandName'];
                      $brands[$index]['BrandProductCount'] = $brandValue['BrandProductCount'];  
                      $index++;
                   }
               }               
                
               $categories[$i]['ProductCategoryName']   = $key;  
               $categories[$i]['ProductCategoryId']     = $value['ProductCategoryId']; 
               $categories[$i]['ProductCount']          = $value['ProductCount'];
               $categories[$i]['brands']                = $brands;
               $totalProducts = $totalProducts + (int) $value['ProductCount'];
               $i++; 
            }
            
            $categories[0]['ProductCount'] = $totalProducts;
            $allBrands[0]['BrandProductCount'] = $totalProducts;
            $categories[0]['brands'] = $allBrands;
            
            # Send response            
            $retArr['status'] = SUCCESS;
            $retArr['categories'] = ($categories);
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
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
     * Get product for the category
     */
    public function chatbot_search_product_by_category_post() {
        $user_id                = $this -> post('user_id');
        $search_text            = trim($this -> post('search_text'));
        $category_id            = $this -> post('category_id') ? $this -> post('category_id') : 0;         
        $brandName              = trim($this -> post('brand')) ? trim($this -> post('brand')) : ""; 
        $brandName              = $brandName == "ALL BRANDS" ? "" : $brandName;
        $product_category_id    = $this -> post('product_category_id') ? $this -> post('product_category_id') : 0;        
        $special_only           = strtolower(trim($this -> post('special_only'))) == 'yes' ? 'Yes' : 'No'; 
        $retailer_id            = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id               = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;
        $device_type            = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type            = $device_type != '' ? $device_type : "A";
	    
        $product_list           = array();
        
        if($product_category_id)
        {
            $category_id = $product_category_id;
        }
        
        //Get user preference
        $user_preference = $this -> searchmodel ->get_user_preference($user_id);
        
        $brand = "";
        $brandNameArr   = explode("-",trim($brandName));
        $arrCount       = count($brandNameArr);
        if($arrCount == 2)
        {
            $categoryName  = trim($brandNameArr[0]);
            $brand         = trim($brandNameArr[1]);
        }else{
            $categoryName  = "";
            $brand         = trim($brandNameArr[0]);
        }        
        
        if($user_id > 0 && $search_text !="")
        {
            $product_list = $this -> searchmodel -> get_products_by_category_and_brand($search_text, $user_id,$category_id, $brand,$special_only);
        }
        
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
                
                switch ($device_type) {
                    case "A":
                        $pageUrl = PLAY_STORE_URL;
                        break;
                    case "I":
                        $pageUrl = APP_STORE_URL;
                        break;
                    case "W":
                        $pageUrl = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                        break;
                    default:
                        $pageUrl = PLAY_STORE_URL;
                }

                //$product_list[$i]['PageUrl'] = $pageUrl;
                
                $product_list[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                $product_list[$i]['AppStoreUrl']  = APP_STORE_URL;
                $product_list[$i]['PlayStoreUrl'] = PLAY_STORE_URL;
             

                $i++;
            }
            $retArr['status'] = SUCCESS;
            $retArr['user_preferred_brands'] = $user_preference ;
            $retArr['product_details'] = ($product_list);
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = SUCCESS;
            $retArr['user_preferred_brands'] = $user_preference ;
            $retArr['product_details'] = array();            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
} 