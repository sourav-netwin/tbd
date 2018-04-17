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
 * Purpose: Products Webservices
 * Date:02-09-2015
 * Dependency: usermodel.php
 */

class Products extends REST_Controller {

    function __construct() {
        parent::__construct();
        $api_key = $this -> post('api_key');
        validateApiKey($api_key);

        $this -> load -> model('webservices/searchmodel', '', TRUE);
        $this -> load -> model('webservices/productmodel', '', TRUE);
        $this -> load -> model('webservices/reviewmodel', '', TRUE);
        $this -> load -> model('webservices/basketmodel', '', TRUE);
        $this -> load -> model('webservices/advertisementsmodel', '', TRUE);

        //Set the latitude
        $this -> productmodel -> latitude = $this -> post('latitude') ? $this -> post('latitude') : "";
        $this -> productmodel -> longitude = $this -> post('longitude') ? $this -> post('longitude') : "";

        //Set the default store
        $this -> productmodel -> store_id = $this -> post('store_id') ? $this -> post('store_id') : "";

        //Set the page & the limit
        $this -> productmodel -> page_no = $this -> post('page') ? $this -> post('page') : "1";
        $this -> productmodel -> page_limit = API_PAGE_LIMIT;
    }

    /**
     * List all the products
     */
    public function list_post() {
        $category_id = $this -> post('category_id') ? $this -> post('category_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $storetype_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $keyword = $this -> post('keyword') ? $this -> post('keyword') : "";
        $price_range = $this -> post('price_range') ? $this -> post('price_range') : "";
        $page_number = (int) $this -> post('page_number') ? $this -> post('page_number') : "";
        $brands = $this -> post('brands') ? $this -> post('brands') : [];
        $page_get = $page_number;


        if (!$page_get || $page_get == 0 || $page_get < 1) {
            $page_get = 1;
        }
        if ($page_number === 1 || !$page_number || $page_number < 1) {
            $page_number = 0;
        }
        else {
            $page_number = $page_number - 1;
        }

        if ($page_number == 0 && $page_get == 1) {
            $limit_start = 0;
        }
        elseif ($page_number === 1) {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
        else {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
        $search_array = array();

        //Set search parameters
        if (isset($keyword))
            $search_array['keyword'] = trim($keyword);

        if (isset($price_range))
            $search_array['price_range'] = trim($price_range);

        //$products = $this -> productmodel -> get_products($category_id, $retailer_id, $user_id, '', $search_array, $get_total = 0, $limit_start, $brands, $store_id, $storetype_id);
        
        $products = $this -> productmodel -> get_products_WIP($category_id, $retailer_id, $user_id, '', $search_array, $get_total = 0, $limit_start, $brands, $store_id, $storetype_id);
        $totalRecordquery = $this->db->query('SELECT FOUND_ROWS() AS `count`');
        $products_total = $totalRecordquery->row()->count;
        
        $i = 0;

        //Encode image of products
        foreach ($products as $product) {
            if ($product['ProductImage'])
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);

            # Check if product is favourite for the user
            $is_favorite = $this -> productmodel ->is_product_favorite($user_id, $product['Id'], $product['special_id']);            
            $products[$i]['is_favorite'] = $is_favorite;

            if ($product['avg_rating'] == NULL) {
                $products[$i]['avg_rating'] = "0";
            }

            if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                $products[$i]['SpecialQty'] = "0";
                $products[$i]['SpecialPrice'] = "0";
            }
            $products[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);

            $i++;
        }

        //Get a price change for a particular category
        $range = array();
        if ($category_id) {
            //Get a price
            $products_range = $this -> productmodel -> get_products_price_range($category_id, $retailer_id);

            $range = ProductPriceRange($products_range['max_price'], $products_range['min_price']);
        }

        //Get Number of pages
        $total_pages = ceil($products_total / API_PAGE_LIMIT);

        $retArr['status'] = SUCCESS;
        $retArr['products'] = ($products);
        $retArr['total_pages'] = $total_pages;
        $retArr['current_page'] = $page_get;
        $retArr['products_range'] = ($range);
        //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
        $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    /**
     * Get the hot offers for a particular retailer
     */
    public function hot_deals_post() {
        $category_id = $this -> post('category_id') > 0 ? $this -> post('category_id') : "";        
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";        
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
    
        /* Pagination */
        $page_number = (int) $this -> post('page_number') ? $this -> post('page_number') : "";        
        $page_get = $page_number;
        
        if (!$page_get || $page_get == 0 || $page_get < 1) {
            $page_get = 1;
        }
        if ($page_number === 1 || !$page_number || $page_number < 1) {
            $page_number = 0;
        }
        else {
            $page_number = $page_number - 1;
        }       
        
        if ($page_number == 0 && $page_get == 1) {
            $limit_start = 0;
        }
        elseif ($page_number === 1) {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
        else {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
        
        $products = $this -> productmodel -> get_hot_deals($category_id, $retailer_id, $user_id,$get_total = 0, $limit_start);
        
        # Get total records 
        $totalRecordquery = $this->db->query('SELECT FOUND_ROWS() AS `count`');  
        $products_total = $totalRecordquery->row()->count; 
        
        $additional = $this -> productmodel -> get_additional_details($store_id);
        $retailerName = "";
        if($additional)
        {
            $retailerName = $additional['RetailerName']." ";
        }
        
        $i = 0;
        //Encode image of products
        foreach ($products as $product) {
            
            if ($product['ProductImage'])
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);

            if( $product['HouseId'] > 0  )
            {
                $products[$i]['ProductName'] = $retailerName.$product['ProductName'];
            }
            
            # Check if product is favourite for the user
            $is_favorite = $this -> productmodel ->is_product_favorite($user_id, $product['Id'], $product['special_id']);                        
            $products[$i]['is_favorite'] = $is_favorite;

            if ($product['avg_rating'] == NULL) {
                $products[$i]['avg_rating'] = "0";
            }

            if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                $products[$i]['SpecialQty'] = "0";
                $products[$i]['SpecialPrice'] = "0";
            }
            $front_url = front_url();
            if (strpos($front_url, 'http://www.') === FALSE) {
                $front_url = str_replace('http://', 'http://www.', $front_url);
            }
            
            switch ($device_type) {
                case "A":
                        $pageUrl = PLAY_STORE_URL;
                        break;
                case "I":
                        $pageUrl = APP_STORE_URL;
                        break;
                case "W":
                        $pageUrl = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                        break;
                default:
                        $pageUrl = PLAY_STORE_URL;
             }
                
             //$products[$i]['PageUrl']      = $pageUrl;
             
             $products[$i]['PageUrl']      = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
             $products[$i]['AppStoreUrl']  = APP_STORE_URL;
             $products[$i]['PlayStoreUrl'] = PLAY_STORE_URL;             
    
            $i++;
        }
        
        $total_pages = ceil($products_total / API_PAGE_LIMIT);
        
        $retArr['status'] = SUCCESS;
        $retArr['total_pages'] = $total_pages;
        $retArr['current_page'] = $page_get;
        $retArr['products'] = ($products);
        $retArr['additional'] = $additional;
        
        //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
        $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
        $retArr['t_and_c'] = $this -> basketmodel -> get_special_terms_and_conditions($user_id, $retailer_id, $category_id,$store_id);
        $retArr['store_details'] = $this -> _get_product_store($user_id, $store_id, $retailer_id);
        
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    

    /**
     * Get the details of a particular products
     */
    public function product_details_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $store_type_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $special_id = (int) $this -> post('special_id') ? (int) $this -> post('special_id') : 0;
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
        $lat = $this->post('latitude') ? $this->post('latitude') : "";
        $long= $this->post('longitude') ? $this->post('longitude') : "";
        
        $views = $this -> productmodel -> get_product_views($product_id);
        $views = $views['count'] ? number_shorten($views['count'], 2) : 0;
        $shares = $this -> productmodel -> get_product_shares($product_id);
        $shares = $shares['count'] ? number_shorten($shares['count'], 2) : 0;

        $product = $this -> productmodel -> product_details($product_id, $retailer_id, $user_id, $special_id);

        if ($product) {
            $front_url = front_url();
            if (strpos($front_url, 'http://www.') === FALSE) {
                $front_url = str_replace('http://', 'http://www.', $front_url);
            }
            
            switch ($device_type) {
                case "A":
                        $pageUrl = PLAY_STORE_URL;
                        break;
                case "I":
                        $pageUrl = APP_STORE_URL;
                        break;
                case "W":
                        $pageUrl = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                        break;
                default:
                       $pageUrl = PLAY_STORE_URL;
            }
            //$product['PageUrl'] = $pageUrl;            
            
            $product['PageUrl'] = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);           
            $product['AppStoreUrl']  = APP_STORE_URL;
            $product['PlayStoreUrl'] = PLAY_STORE_URL;
                
            if ($product['ProductImage'])
                $product['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $product['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);

            # Check if product is favourite for the user
            $is_favorite = $this -> productmodel ->is_product_favorite($user_id, $product_id, $special_id);
            $product['is_favorite'] = $is_favorite;
            
            # Combo Products fpr Special Product 
            if($product_id > 0 && $special_id > 0)
            {
                $comboProductName = '';
                $combo_products = $this -> productmodel ->get_combo_products($product_id, $special_id);
                if($combo_products)
                {  
                    foreach($combo_products as $comboProduct)
                    {
                       $comboProductName = $comboProductName ." + ".$comboProduct['Quantity']." x ".$comboProduct['ComboProductName'];  
                    }
                    
                    $productName =  $product['SpecialQty']." x ".$product['ProductName'].$comboProductName;
                    
                    $product['ProductName'] = $productName; 
                    $product['combo_deal'] = "1";
                }else{
                    $product['combo_deal'] = "0";
                }
            }else{
                $product['combo_deal'] = "0";
            }
            
            if ($product['wish_lists'] != NULL && $product['wish_lists'] != 0) {
                $product['wish_lists'] = "1";
            }
            else {
                $product['wish_lists'] = "0";
            }

            if ($product['avg_rating'] == NULL) {
                $product['avg_rating'] = "0";
            }

            if ($product['price_alert'] != NULL) {
                $product['price_alert'] = "1";
            }else {
                $product['price_alert'] = "0";
            }
            $price_value = $product['store_price'];
            $product['save_percent'] = "";
            $product['save_value'] = "";
            if (($product['SpecialQty'] == NULL || $product['SpecialQty'] == 0) && ($product['SpecialPrice'] == NULL || $product['SpecialPrice'] == 0)) {
                $product['SpecialQty'] = "0";
                $product['SpecialPrice'] = "0";
            }
            else {
                $price_value = $product['SpecialPrice'];
                $one_price = $product['SpecialPrice'];
                if ($product['SpecialQty'] > 1) {
                    $one_price = $product['SpecialPrice'] / $product['SpecialQty'];
                }
                $product['save_percent'] = round(100 - (($one_price / $product['store_price']) * 100)) . '%';
                $product['save_value'] = round(($product['store_price']*$product['SpecialQty']) - ($one_price*$product['SpecialQty']), 2);
                $save_arr = explode('.', $product['save_value']);
                if (!isset($save_arr[1])) {
                    $product['save_value'] = $product['save_value'] . '.00';
                }
                else{
                    if(strlen($save_arr[1]) < 2){
                        $product['save_value'] = $save_arr[0].'.'.$save_arr[1].'0';
                    }
                }
            }

            $product['shares'] = $shares;
            $product['views'] = $views;
            
            # Get Reviews for the product
            $allReviews = $this -> reviewmodel -> get_products_all_reviews($product_id);
                        
            # Get Reviews for the product
            $reviews = $this -> reviewmodel -> get_product_reviews($product_id);

            //Convert the time to time ago for reviews
            $i = 0;
            $is_review_added = 0;
            foreach ($reviews as $review) {
                if ($review['CreatedOn'])
                    $reviews[$i]['CreatedOn'] = humanTiming(strtotime($review['CreatedOn'])) . " ago";

                if ($review['ProfileImage'])
                    $reviews[$i]['ProfileImage'] = (front_url() . USER_IMAGE_PATH . 'medium/' . $review['ProfileImage']);
                else
                    $reviews[$i]['ProfileImage'] = (front_url() . DEFAULT_USER_IMAGE_PATH);

                //To check if review added by user.

                if ($review['UserId'] == $user_id)
                    $is_review_added = 1;

                $i++;
            }

            $lat  = "";
            $long = "";
            $prefDistance = 0;

            $user_details = $this -> productmodel ->get_user_details($user_id);
            if($user_details)
            {
                $lat = $user_details['PrefLatitude'];
                $long = $user_details['PrefLongitude'];
                $prefDistance = $user_details['PrefDistance'];
            }
            
            $counter_data = array(
                'ProductId' => $product_id,
                'UserId' => $user_id,
                'RetailerId' => $retailer_id,
                'StoreId' => $this -> post('store_id')
            );
            //Increase the product view counter
            $this -> productmodel -> add_product_view($counter_data);

            $retArr['status'] = SUCCESS;
            $retArr['product'] = array($product);
            $retArr['is_review_added'] = ($is_review_added);
            $retArr['reviews'] = ($reviews);
            $retArr['allReviews'] = ($allReviews);
            $retArr['related_products'] = $this -> _related_products($product_id, $retailer_id, $store_type_id, $store_id, $user_id, $device_type);
            $retArr['store_details'] = $this -> _get_product_store($user_id, $store_id, $retailer_id);
            //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
            $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "No product found";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /**
     * Get the details compare_product_post
     */
    public function compare_product_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";        
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $store_type_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";        
        $special_id = $this -> post('special_id') ? $this -> post('special_id') : "";
        $special_id > 0 ? $special_id = $special_id : $special_id = '';
        
        $lat = $this->post('latitude') ? $this->post('latitude') : "";
        $long= $this->post('longitude') ? $this->post('longitude') : "";
        
        $group_id = $this->post('group_id') ? $this->post('group_id') : 0;
        
        $prefDistance = 0;
        $final_store_products =array();
        
        $user_details = $this -> productmodel ->get_user_details($user_id);
        if($user_details)
        {
            $prefDistance = $user_details['PrefDistance'];
        }
        
        # Get Product details
        $product = $this -> productmodel -> product_details($product_id, $retailer_id, $user_id, $special_id);
        
        if (($product['SpecialQty'] == NULL || (int)$product['SpecialQty'] == 0) && ($product['SpecialPrice'] == NULL || (int)$product['SpecialPrice'] == 0)) {
            $productPrice = $product['store_price'];
        }else{
            $productPrice = $product['SpecialPrice'];
            if ($product['SpecialQty'] > 0) {
                $productPrice = $product['SpecialPrice'] / $product['SpecialQty'];
            }
        }
        
        $store_products = $this -> productmodel -> compare_product_optimize($product_id, $retailer_id, $store_id,$lat, $long,$group_id);
        
        $i = 0;
        foreach ($store_products as $store_product) {
            if ($store_product['LogoImage'])
                $store_products[$i]['LogoImage'] = (front_url() . RETAILER_IMAGE_PATH . 'medium/' . $store_product['LogoImage']);

            if ($store_product['Price'] == NULL) {
                $store_products[$i]['Price'] = "0";
            }

            if ((!isset($store_product['distance'])) || $store_product['distance'] == NULL) {
                $store_products[$i]['distance'] = "0";
            }
            
            if ( $store_product['Special_Qty_Price'] == NULL || $store_product['Special_Qty_Price'] == "") {
                $store_products[$i]['SpecialQty'] = "0";
                $store_products[$i]['SpecialPrice'] = "0";
            }else{
                $splQtyPrice = explode('-',$store_product['Special_Qty_Price']);                
                $store_products[$i]['SpecialQty'] = $splQtyPrice[0];
                $store_products[$i]['SpecialPrice'] = $splQtyPrice[0];
            }
            
            $i++;
        }
        
        # Show only those stores which are within the preferd distance 
        if($prefDistance > 0 )
        {
            $index = 0;
            $final_store_products =array();
            foreach ($store_products as $store_product) {
                if($store_product['distance'] <= $prefDistance)
                {   
                    //if ($store_product['SpecialQty'] == 0 && $store_product['SpecialPrice'] == 0) {
                    if ((int)$store_product['SpecialQty'] == 0 && (int)$store_product['SpecialPrice'] == 0) {
                        $storeProductPrice = $store_product['Price'];
                    }else{
                        $storeProductPrice = $store_product['SpecialPrice'];
                        if ($store_product['SpecialQty'] > 0) {
                            $storeProductPrice = $store_product['SpecialPrice'] / $store_product['SpecialQty'];
                        }
                    }                    
                    
                    if($productPrice <= $storeProductPrice)
                    {
                        
                        $final_store_products[$index]['distance'] = $store_product['distance'];
                        $final_store_products[$index]['Id'] = $store_product['Id'];
                        $final_store_products[$index]['CompanyName'] = $store_product['CompanyName'];
                        $final_store_products[$index]['LogoImage'] = $store_product['LogoImage'];
                        $final_store_products[$index]['Price'] = $store_product['Price'];
                        $final_store_products[$index]['SpecialQty'] = $store_product['SpecialQty'];
                        $final_store_products[$index]['SpecialPrice'] = $store_product['SpecialPrice'];
                        $final_store_products[$index]['StoreName'] = $store_product['StoreName'];
                        
                        $index++;
                    }
                } //if($store_product['distance'] <= $prefDistance)
                //$index++;
            }//foreach ($store_products as $store_product)

        }else{
            $final_store_products = $store_products;
        }
        
        if($final_store_products){    
            $retArr['status'] = SUCCESS;
            $retArr['store_products'] = ($final_store_products);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        
        }else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "No product found";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }        
    }
    
    
    /**
     * Add product to favorite list
     */
    public function make_product_favorite_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $special_id = (int)$this -> post('special_id') ? (int)$this -> post('special_id') : 0;
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $result = $this -> productmodel -> make_product_favorite($user_id, $product_id, $special_id);

        if ($result == 'ADD') {
            $message = "Product added to favourite list";
            $favorite_status = "1";
        }
        else {
            $message = "Product removed from favourite list";
            $favorite_status = "0";
        }

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['is_favorite'] = $favorite_status;
            $retArr['message'] = $message;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Add product to alert
     */
    public function add_product_alert_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $result = $this -> productmodel -> add_product_alert($user_id, $product_id);

        if ($result == 'ADD') {
            $message = "Product added to alert list";
            $alert_status = "1";
        }
        else {
            $message = "Product removed from alert list";
            $alert_status = "0";
        }

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = $message;
            $retArr['price_alert'] = $alert_status;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    public function delete_alerts_post() {
        $ids = $this -> post('id') ? $this -> post('id') : "";
        $ids = explode(',', $ids);
        $this -> productmodel -> delete_alerts($ids);

        $retArr['status'] = SUCCESS;

        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    /**
     *  Add reviews & rating for a product
     */
    public function add_review_rating_post() {

        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $review_comment = $this -> post('review_comment') ? $this -> post('review_comment') : "";
        $review_rating = $this -> post('review_rating') ? $this -> post('review_rating') : "";

        $result = $this -> reviewmodel -> add_review_rating($user_id, $product_id, $review_comment, $review_rating);
        
        // Get Reviews for the product
        $allReviews = $this -> reviewmodel -> get_products_all_reviews($product_id);	
        
        if ($result) {  //If reviews added
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Review added successfully";
            $retArr['allReviews'] = ($allReviews);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else { //If reviews exists already
            $retArr['status'] = FAIL;
            $retArr['message'] = "Review already exists";
            $retArr['allReviews'] = ($allReviews);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    /*
     * Update a review rating
     */

    public function update_review_rating_post() {

        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $review_comment = $this -> post('review_comment') ? $this -> post('review_comment') : "";
        $review_rating = $this -> post('review_rating') ? $this -> post('review_rating') : "";

        $result = $this -> reviewmodel -> update_review_rating($user_id, $product_id, $review_comment, $review_rating);
        // Get Reviews for the product
        $allReviews = $this -> reviewmodel -> get_products_all_reviews($product_id);
	
        
        if ($result) {  //If reviews added
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Review updated successfully";
            $retArr['allReviews'] = ($allReviews);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else { //If reviews exists already
            $retArr['status'] = FAIL;
            $retArr['message'] = "Review updating failed";
            $retArr['allReviews'] = ($allReviews);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Delete a review rating
     */
    public function delete_review_rating_post() {

        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $result = $this -> reviewmodel -> delete_review_rating($user_id, $product_id);
        // Get Reviews for the product
        $allReviews = $this -> reviewmodel -> get_products_all_reviews($product_id);
	
        
        if ($result) {  //If reviews added
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Review deleted successfully";
            $retArr['allReviews'] = ($allReviews);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else { //If reviews exists already
            $retArr['status'] = FAIL;
            $retArr['message'] = "Review deletion failed";
            $retArr['allReviews'] = ($allReviews);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    /*
     * List favorite products
     */

    public function list_favorite_products_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
    
        $products = $this -> productmodel -> get_favorite_products($user_id, $retailer_id);
        if ($products) {
            $i = 0;
            foreach ($products as $product) {
                
                if ($product['ProductImage'])
                    $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
                else
                    $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);
                
                if ($product['avg_rating'] == NULL) {
                    $products[$i]['avg_rating'] = "0";
                }


                if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                    $products[$i]['SpecialQty'] = "0";
                    $products[$i]['SpecialPrice'] = "0";
                }
                $products[$i]['is_favorite'] = "1";

                $front_url = front_url();
                if (strpos($front_url, 'http://www.') === FALSE) {
                    $front_url = str_replace('http://', 'http://www.', $front_url);
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
                
                //$products[$i]['PageUrl'] = $pageUrl;
                
                $products[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                
                $products[$i]['AppStoreUrl']  = APP_STORE_URL;
                $products[$i]['PlayStoreUrl'] = PLAY_STORE_URL;
    
                $i++;
            }

            $retArr['status'] = SUCCESS;
            $retArr['favorite_products'] = $products;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'No favourites found';
            $retArr['favorite_products'] = [];
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /*
     * List alert products
     */

    public function list_alert_products_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
    
        $products = $this -> productmodel -> get_alert_products($user_id, $retailer_id);

        if ($products) {
            $i = 0;
            foreach ($products as $product) {
                if ($product['ProductImage'])
                    $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
                else
                    $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);

                if ($product['is_favorite'] != NULL) {
                    $products[$i]['is_favorite'] = "1";
                }
                else {
                    $products[$i]['is_favorite'] = "0";
                }

                if ($product['avg_rating'] == NULL) {
                    $products[$i]['avg_rating'] = "0";
                }

                if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                    $products[$i]['SpecialQty'] = "0";
                    $products[$i]['SpecialPrice'] = "0";
                }
                $front_url = front_url();
                if (strpos($front_url, 'http://www.') === FALSE) {
                    $front_url = str_replace('http://', 'http://www.', $front_url);
                }
                
                switch ($device_type) {
                    case "A":
                            $pageUrl = PLAY_STORE_URL;
                            break;
                    case "I":
                            $pageUrl = APP_STORE_URL;
                            break;
                    case "W":
                            $pageUrl = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                            break;
                    default:
                            $pageUrl = PLAY_STORE_URL;
                }
                
                //$products[$i]['PageUrl'] = $pageUrl;
                
                $products[$i]['PageUrl'] = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                $products[$i]['AppStoreUrl']  = APP_STORE_URL;
                $products[$i]['PlayStoreUrl'] = PLAY_STORE_URL;            
    
                $i++;
            }

            $retArr['status'] = SUCCESS;
            $retArr['alert_products'] = $products;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'No price alerts found';
            $retArr['alert_products'] = [];

            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function report_abuse_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $message = $this -> post('message') ? $this -> post('message') : "";

        $result = $this -> productmodel -> add_report_abuse($user_id, $product_id, $message);

        if ($result) {  //If report added
            $retArr['status'] = SUCCESS;
            $retArr['message'] = "Abuse report added successfully";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else { //If report exists already
            $retArr['status'] = FAIL;
            $retArr['message'] = "Abuse report already exists";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function get_basket_post() {

        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $products = $this -> productmodel -> get_user_basket($user_id, $retailer_id, $store_id);

        // Get other retailers price
        $other_retailer_products = $this -> productmodel -> get_user_basket_other_retailers($user_id, $retailer_id);

        $i = 0;
        $total_price = 0;

        //Encode image of products
        foreach ($products as $product) {
            if ($product['ProductImage'])
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);

            if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                $products[$i]['SpecialQty'] = "0";
                $products[$i]['SpecialPrice'] = "0";
            }

            $total_price = $total_price + $product['Price'];
            $i++;
        }

        $i = 0;

        foreach ($other_retailer_products as $other_retailer_product) {
            if ($other_retailer_product['LogoImage'])
                $other_retailer_products[$i]['LogoImage'] = (front_url() . RETAILER_IMAGE_PATH . 'medium/' . $other_retailer_product['LogoImage']);

            if ($other_retailer_product['Price'] == NULL) {
                $other_retailer_products[$i]['Price'] = "0";
            }
            $i++;
        }

        $retArr['status'] = SUCCESS;
        $retArr['products'] = ($products);
        $retArr['basket_total_price'] = number_format($total_price, 2);
        $retArr['other_retailer_products'] = $other_retailer_products;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    public function get_product_store_post() {

        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $products = $this -> productmodel -> get_store_details($retailer_id, $store_id);
        $product_details = [];
        $dayNames = array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        );
        if ($products) {
            $cnt = 0;
            $time_cnt = 0;
            foreach ($products as $product) {
                if ($cnt == 0) {
                    $product_details['StoreName'] = $product['StoreName'];
                    $product_details['Latitude'] = $product['Latitude'];
                    $product_details['Longitude'] = $product['Longitude'];
                    $product_details['StreetAddress'] = $product['StreetAddress'];
                    $product_details['Zip'] = $product['Zip'];
                    $product_details['ContactPersonNumber'] = $product['ContactPersonNumber'];
                    $product_details['StateName'] = $product['StateName'];
                    $product_details['LogoImage'] = $product['LogoImage'] == '' ? '' : front_url() . RETAILER_IMAGE_PATH . 'small/' . $product['LogoImage'];
                    if (date('N', strtotime(date("Y/m/d"))) == $product['OpenCloseDay']) {
                        $time_arr = explode('-', $product['OpenCloseTimeFrom']);
                        $product_details['StoreTime'] = array(
                            'Day' => $dayNames[$product['OpenCloseDay']],
                            'OpenTime' => date("H:i", strtotime($time_arr[0])),
                            'CloseTime' => date("H:i", strtotime($time_arr[1]))
                        );
                        $time_cnt++;
                    }
                }
                else {
                    if (date('N', strtotime(date("Y/m/d"))) == $product['OpenCloseDay']) {
                        $time_arr = explode('-', $product['OpenCloseTimeFrom']);
                        $product_details['StoreTime'] = array(
                            'Day' => $dayNames[$product['OpenCloseDay']],
                            'OpenTime' => date("H:i", strtotime($time_arr[0])),
                            'CloseTime' => date("H:i", strtotime($time_arr[1]))
                        );
                        $time_cnt++;
                    }
                }
                $cnt++;
            }
            if ($time_cnt == 0) {
                $product_details['StoreTime'] = array(
                    'Day' => '',
                    'OpenTime' => '',
                    'CloseTime' => ''
                );
            }
            $retArr['status'] = SUCCESS;
            $retArr['store_details'] = $product_details;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['store_details'] = [];
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function _get_product_store($user_id = '', $store_id = '', $retailer_id = '') {

        $products = $this -> productmodel -> get_store_details($retailer_id, $store_id);
        $product_details = [];
        $dayNames = array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        );
        if ($products) {
            $cnt = 0;
            $time_cnt = 0;
            foreach ($products as $product) {
                if ($cnt == 0) {                    
                    $product_details['RetailerName'] = $product['RetailerName'];
                    $product_details['StoreName'] = $product['StoreName'];
                    $product_details['Latitude'] = $product['Latitude'];
                    $product_details['Longitude'] = $product['Longitude'];
                    $product_details['StreetAddress'] = $product['StreetAddress'];
                    $product_details['Zip'] = $product['Zip'];
                    $product_details['ContactPersonNumber'] = $product['ContactPersonNumber'];
                    $product_details['StateName'] = $product['StateName'];
                    $product_details['LogoImage'] = $product['LogoImage'] == '' ? '' : front_url() . RETAILER_IMAGE_PATH . 'small/' . $product['LogoImage'];
                    if (date('N', strtotime(date("Y/m/d"))) == $product['OpenCloseDay']) {
                        $time_arr = explode('-', $product['OpenCloseTimeFrom']);
                        $product_details['StoreTime'] = array(
                            'Day' => $dayNames[$product['OpenCloseDay']],
                            'OpenTime' => date("H:i", strtotime($time_arr[0])),
                            'CloseTime' => date("H:i", strtotime($time_arr[1]))
                        );
                        $time_cnt++;
                    }
                }
                else {
                    if (date('N', strtotime(date("Y/m/d"))) == $product['OpenCloseDay']) {
                        $time_arr = explode('-', $product['OpenCloseTimeFrom']);
                        $product_details['StoreTime'] = array(
                            'Day' => $dayNames[$product['OpenCloseDay']],
                            'OpenTime' => date("H:i", strtotime($time_arr[0])),
                            'CloseTime' => date("H:i", strtotime($time_arr[1]))
                        );
                        $time_cnt++;
                    }
                }
                $cnt++;
            }
            if ($time_cnt == 0) {
                $product_details['StoreTime'] = array(
                    'Day' => '',
                    'OpenTime' => '',
                    'CloseTime' => ''
                );
            }
            return $product_details;
        }
        else {
            return $product_details['StoreTime'] = array(
                'Day' => '',
                'OpenTime' => '',
                'CloseTime' => ''
            );
        }
    }

    public function add_share_count_post() {
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $share_from = $this -> post('share_from') ? $this -> post('share_from') : ""; //W - Web, A - Android, I - I phone 
         //F-Facebook, T-Twitter, E-Email, G-Google and W-WhatsApp
        $social_media = $this -> post('social_media') ? $this -> post('social_media') : ""; 
        
        $insert_data = array(
            'ProductId' => $product_id,
            'RetailerId' => $retailer_id,
            'StoreId' => $store_id,
            'UserId' => $user_id,
            'ShareFrom' => $share_from,
            'SocialMedia' => $social_media
        );
        $isInsert = $this -> productmodel -> insert_share_details($insert_data);
        if ($isInsert) {
			gainLoyaltyPointsMailOfUser($user_id,'Product Share From '.$share_from);
            $share_count = $this -> productmodel -> get_product_shares($product_id);
            $share_count = $share_count['count'] ? $share_count['count'] : 0;
            $retArr['status'] = SUCCESS;
            $retArr['count'] = $share_count;
            $retArr['message'] = 'Share count updated successfully';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'Failed to update share count';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function get_view_count_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $views = $this -> productmodel -> get_product_views($product_id);
        $views = $views['count'] ? $views['count'] : 0;
        $retArr['status'] = SUCCESS;
        $retArr['count'] = $views;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    public function get_share_count_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $views = $this -> productmodel -> get_product_shares($product_id);
        $views = $views['count'] ? $views['count'] : 0;
        $retArr['status'] = SUCCESS;
        $retArr['count'] = $views;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    public function _related_products($product_id, $retailer_id, $store_type_id, $store_id, $user_id,$device_type) {
        $category_details = $this -> productmodel -> get_categories($product_id);
        if ($category_details) {
            $related_products = $this -> productmodel -> get_related_products($retailer_id, $store_type_id, $store_id, $product_id, $category_details['MainCategoryId'], $category_details['ParentCategoryId'], $category_details['CategoryId'], $user_id);
            if ($related_products[0]) {
                $i = 0;
                foreach ($related_products as $product) {
                    if ($product['ProductImage'])
                        $related_products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
                    else
                        $related_products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);

                    if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                        $related_products[$i]['SpecialQty'] = "0";
                        $related_products[$i]['SpecialPrice'] = "0";
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
                    //$related_products[$i]['PageUrl'] = $pageUrl;
                    
                    $related_products[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                    $related_products[$i]['AppStoreUrl']  = APP_STORE_URL;
                    $related_products[$i]['PlayStoreUrl'] = PLAY_STORE_URL;            
    
                    $total_price = $total_price + $product['Price'];
                    $i++;
                }
                return $related_products;
            }
            else {
                return [];
            }
        }
        else {
            return [];
        }
    }

    public function get_chart_details_post() {
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $day = '';
        $price = '';
        $chart_array = $this -> productmodel -> get_chart_details($product_id);
        $day_arr = array();
        $price_arr = array();
        $store_arr = array();
        if ($chart_array) {
            $cnt = 1;
            foreach ($chart_array as $chart) {
                if ($cnt == 1) {
                    $day_arr[] = array('date' => date('d-M', strtotime($chart['CreatedOn'])));
                    $price_arr[] = array('price' => format_decimal($chart['RRP']));
                    $store_arr[] = array(
                        'retailer' => $chart['CompanyName'],
                        'store' => ''
                    );
                }
                $day_arr[] = array('date' => $chart['day_month']);
                $price_arr[] = array('price' => format_decimal($chart['SpecialPrice']));
                $store_arr[] = array(
                    'retailer' => $chart['CompanyName'],
                    'store' => $chart['StoreName']
                );
                $cnt++;
            }

            $retArr['status'] = SUCCESS;
            $retArr['day'] = $day_arr;
            $retArr['price'] = $price_arr;
            $retArr['store'] = $store_arr;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'No price change available';
            $retArr['day'] = [];
            $retArr['price'] = [];
            $retArr['store'] = [];
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /*  Function to get active promotions in user's preferred Retailer and store */
    public function get_promotions_post() {                
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $user_id     = $this -> post('user_id') ? $this -> post('user_id') : "";
        $store_id    = $this -> post('store_id') ? $this -> post('store_id') : "";
        
        # Get active promotions
        $promotions = $this -> productmodel -> get_promotions($retailer_id, $user_id, $store_id);
        
        if( $promotions )
        {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = 'Promotion(s) available';
            $retArr['promotions'] = $promotions;            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'No Promotion(s) available';
            $retArr['promotions'] = array();
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /*  Function to get products from the promotion */
    public function get_promotion_products_post() {                
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $special_id = $this -> post('special_id') ? $this -> post('special_id') : "";
        
        $page_number = (int) $this -> post('page_number') ? $this -> post('page_number') : "";        
        $page_get    = $page_number;   
        
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
    
        
        if (!$page_get || $page_get == 0 || $page_get < 1) {
            $page_get = 1;
        }
        if ($page_number === 1 || !$page_number || $page_number < 1) {
            $page_number = 0;
        }
        else {
            $page_number = $page_number - 1;
        }       
        
        if ($page_number == 0 && $page_get == 1) {
            $limit_start = 0;
        }
        elseif ($page_number === 1) {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
        else {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
		
        $this -> productmodel -> savespecialbrowse($retailer_id,$store_id, $special_id);
        $products = $this -> productmodel -> get_promotion_products($retailer_id,$store_id, $special_id, $user_id, $get_total = 0,$limit_start);
        
        # Get total records 
        $totalRecordquery = $this->db->query('SELECT FOUND_ROWS() AS `count`');  
        $products_total = $totalRecordquery->row()->count; 
        $total_pages = ceil($products_total / API_PAGE_LIMIT);
        
        $additional = $this -> productmodel -> get_additional_details($store_id);
        
        $i = 0;
        //Encode image of products
        foreach ($products as $product) {
            
            if ($product['ProductImage'])
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);
            
            # Check if product is favourite for the user
            $is_favorite = $this -> productmodel ->is_product_favorite($user_id, $product['Id'], $product['special_id']);                        
            $products[$i]['is_favorite'] = $is_favorite;

            if ($product['avg_rating'] == NULL) {
                $products[$i]['avg_rating'] = "0";
            }

            if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                $products[$i]['SpecialQty'] = "0";
                $products[$i]['SpecialPrice'] = "0";
            }
            $front_url = front_url();
            if (strpos($front_url, 'http://www.') === FALSE) {
                $front_url = str_replace('http://', 'http://www.', $front_url);
            }
            
            switch ($device_type) {
                case "A":
                        $pageUrl = PLAY_STORE_URL;
                        break;
                case "I":
                        $pageUrl = APP_STORE_URL;
                        break;
                case "W":
                        $pageUrl = $pageUrl = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
                        break;
                default:
                        $pageUrl = PLAY_STORE_URL;
            }
            //$products[$i]['PageUrl'] = $pageUrl;
            
            $products[$i]['PageUrl'] = $front_url . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
            $products[$i]['AppStoreUrl']  = APP_STORE_URL;
            $products[$i]['PlayStoreUrl'] = PLAY_STORE_URL;
            
            $i++;
        }
        
        $retArr['status']       = SUCCESS;
        $retArr['total_pages']  = $total_pages;
        $retArr['current_page'] = $page_get;
        $retArr['products']     = $products;
        $retArr['additional']   = $additional;
        //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
        $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
        $retArr['t_and_c']      = $this -> basketmodel -> get_special_tandc($special_id);
        $retArr['store_details']= $this -> _get_product_store($user_id, $store_id, $retailer_id);
        
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
     /**
     * List all the products
     */
    public function get_product_list_post() {
        $category_id = $this -> post('category_id') ? $this -> post('category_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $storetype_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $keyword = $this -> post('keyword') ? $this -> post('keyword') : "";
        $price_range = $this -> post('price_range') ? $this -> post('price_range') : "";
        $page_number = (int) $this -> post('page_number') ? $this -> post('page_number') : "";
        $brands = $this -> post('brands') ? $this -> post('brands') : [];
        $page_get = $page_number;

        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";

        if (!$page_get || $page_get == 0 || $page_get < 1) {
            $page_get = 1;
        }
        if ($page_number === 1 || !$page_number || $page_number < 1) {
            $page_number = 0;
        }
        else {
            $page_number = $page_number - 1;
        }

        if ($page_number == 0 && $page_get == 1) {
            $limit_start = 0;
        }
        elseif ($page_number === 1) {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
        else {
            $limit_start = $page_number * API_PAGE_LIMIT;
        }
        $search_array = array();

        //Set search parameters
        if (isset($keyword))
            $search_array['keyword'] = trim($keyword);

        if (isset($price_range))
            $search_array['price_range'] = trim($price_range);
        
        $products = $this -> productmodel -> get_product_list($category_id, $retailer_id, $user_id, '', $search_array, $get_total = 0, $limit_start, $brands, $store_id, $storetype_id);
      
        $totalRecordquery = $this->db->query('SELECT FOUND_ROWS() AS `count`');
        $products_total = $totalRecordquery->row()->count;
        
        $i = 0;
        //Encode image of products
        foreach ($products as $product) {
            if ($product['ProductImage'])
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);
            
            $prodSplDetails = $this -> productmodel ->product_specials_details($product['Id'],$retailer_id,$store_id);
            
            if($prodSplDetails)
            {
                $products[$i]['SpecialQty']         = $prodSplDetails['SpecialQty'] == NULL ? 0: $prodSplDetails['SpecialQty'];
                $products[$i]['SpecialPrice']       = $prodSplDetails['SpecialPrice'] == NULL ? 0: $prodSplDetails['SpecialPrice'];
                $products[$i]['PriceAppliedFrom']   = $prodSplDetails['PriceAppliedFrom'];
                $products[$i]['PriceAppliedTo']     = $prodSplDetails['PriceAppliedTo'];
                $products[$i]['IsStore']            = $prodSplDetails['IsStore'];
                $products[$i]['special_id']         = $prodSplDetails['special_id'];
                $special_id                         = $prodSplDetails['special_id'];
            }else{
                $products[$i]['SpecialQty'] = "0";
                $products[$i]['SpecialPrice'] = "0";
                $products[$i]['PriceAppliedFrom'] = "";
                $products[$i]['PriceAppliedTo'] = "";
                $products[$i]['IsStore'] = "0";
                $products[$i]['special_id'] = "0";
                $special_id                 = 0;
            }
            
            # Check if product is favourite for the user
            $is_favorite = $this -> productmodel ->is_product_favorite($user_id, $product['Id'], $special_id);
            $products[$i]['is_favorite'] = $is_favorite;
            
            if ($product['avg_rating'] == NULL) {
                $products[$i]['avg_rating'] = "0";
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
                
             //$products[$i]['PageUrl'] = $pageUrl;
             
             $products[$i]['PageUrl'] = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
             $products[$i]['AppStoreUrl']  = APP_STORE_URL;
             $products[$i]['PlayStoreUrl'] = PLAY_STORE_URL;
    
            $i++;
        }

        //Get a price change for a particular category
        $range = array();
        if ($category_id) {
            //Get a price
            $products_range = $this -> productmodel -> get_products_price_range($category_id, $retailer_id);

            $range = ProductPriceRange($products_range['max_price'], $products_range['min_price']);
        }

        # Get Number of pages
        $total_pages = ceil($products_total / API_PAGE_LIMIT);

        $retArr['status'] = SUCCESS;
        $retArr['products'] = ($products);
        $retArr['total_pages'] = $total_pages;
        $retArr['current_page'] = $page_get;
        $retArr['products_range'] = ($range);
        //$retArr['basket_count'] = $this -> basketmodel -> get_basket_count($user_id);
        $retArr['basket_count'] = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    /**
     * Get the hot offers for a particular retailer with ads 
     */
    public function hot_deals_with_ads_post() {
        
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";        
        $device_type = $this -> post('device_type') ? $this -> post('device_type') : ""; //W - Web, A - Android, I - I phone
        $device_type = $device_type != '' ? $device_type : "A";
    
        $products = $this -> productmodel -> get_all_hot_deals($category_id, $retailer_id, $user_id);
        $productCount = count($products);
                
        # Get adds 
        $advertisements = $this -> advertisementsmodel -> get_all_advertisements($category_id);        
        $adsCount = count($advertisements);
        $totalRecords = $productCount + $adsCount;
        
        $productIndex = 0;
        $adsIndex = 0;
        $productRange = 3;
        $allProducts = array();
        $ads = array(); 
        $i = 0;
        
        
        $count =0;
        //Encode image of products
        foreach ($products as $product) {
            if ($product['ProductImage'])
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);
            
            # Check if product is favourite for the user
            $is_favorite = $this -> productmodel ->is_product_favorite($user_id, $product['Id'], $product['special_id']);            
            $products[$i]['is_favorite'] = $is_favorite;

            if ($product['avg_rating'] == NULL) {
                $products[$i]['avg_rating'] = "0";
            }

            if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                $products[$i]['SpecialQty'] = "0";
                $products[$i]['SpecialPrice'] = "0";
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
                
             //$products[$i]['PageUrl'] = $pageUrl;
             
             $products[$i]['PageUrl'] = $pageUrl = front_url() . 'productdetails/' . urlencode(encode_per($product['ProductName'])) . '/' . $this -> encrypt -> encode($product['Id']);
             $products[$i]['AppStoreUrl']  = APP_STORE_URL;
             $products[$i]['PlayStoreUrl'] = PLAY_STORE_URL;
             
             $products[$i]['item_type'] = 'Product';
            
            if($count < ($productRange) )
            {
              $allProducts[] = $products[$i]; 
              $count++;
            }else{                
               $count = 1;               
               if($adsIndex < ($adsCount))
                {
                   //$ads[$adsIndex]['pid'] = $advertisements[$adsIndex]['Id'];
                   $ads[$adsIndex]['reviews_count'] = "";
                   $ads[$adsIndex]['avg_rating'] = "";
                   $ads[$adsIndex]['wish_lists'] = "";
                   $ads[$adsIndex]['Id'] = $advertisements[$adsIndex]['Id'];
                   
                   $ads[$adsIndex]['HouseId'] = "";
                   $ads[$adsIndex]['ProductName'] = $advertisements[$adsIndex]['AdvertisementTitle'];
                   //$ads[$adsIndex]['ProductImage'] = $advertisements[$adsIndex]['AdvertisementImage'];     
                   
                   if ( $advertisements[$adsIndex]['AdvertisementImage'] )
                    $ads[$adsIndex]['ProductImage'] = (front_url() . ADVERTISEMENT_IMAGE_PATH . "original/" . $advertisements[$adsIndex]['AdvertisementImage']);
                   else
                   $ads[$adsIndex]['ProductImage'] = (front_url() . DEFAULT_ADVERTISEMENT_IMAGE_PATH);
                
                   $ads[$adsIndex]['ProductDescription'] = $advertisements[$adsIndex]['AdvertisementDescription']; 
                   $ads[$adsIndex]['PageUrl'] = $advertisements[$adsIndex]['AdvertisementUrl']; 
                   
                   $ads[$adsIndex]['RRP'] = "";
                   $ads[$adsIndex]['Brand'] = "";
                   $ads[$adsIndex]['SKU'] = "";
                   $ads[$adsIndex]['store_price'] = "";
                   $ads[$adsIndex]['SpecialQty'] = "";
                   $ads[$adsIndex]['SpecialPrice'] = "";
                   $ads[$adsIndex]['BasketId'] = "";
                   $ads[$adsIndex]['price_alert'] = "";
                   $ads[$adsIndex]['PriceAppliedFrom'] = "";
                   $ads[$adsIndex]['PriceAppliedTo'] = "";
                   $ads[$adsIndex]['IsStore'] = "";
                   $ads[$adsIndex]['special_id'] = "";
                   $ads[$adsIndex]['is_favorite'] = "";
                   $ads[$adsIndex]['item_type'] = 'Advertisement';
                   
                   $allProducts[] = $ads[$adsIndex];
                   $adsIndex++;
                }                
                $allProducts[] = $products[$i]; 
            }
            
            $i++;
        }        
        
        $retArr['status'] = SUCCESS;        
        $retArr['products'] = ($allProducts);
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    
    /*  Function to get active promotions */
    public function get_active_promotions_post() {                        
        $user_id     = $this -> post('user_id') ? $this -> post('user_id') : "";
        $lat        = $this->post('latitude') ? $this->post('latitude') : 0;
        $long       = $this->post('longitude')? $this->post('longitude') : 0;
        
        $prefDistance = $index = 0;
        $promotions = array();
        
        # Get user preference
        $user_preference = $this -> searchmodel -> get_user_preference_details($user_id);
        
        if($user_preference){
            $prefDistance = $user_preference['PrefDistance'];
        }
        
        # Get active promotions        
        $activePromotions = $this -> productmodel -> get_active_promotions();
        foreach($activePromotions as $actPromo)
        {
           $storeDetails = array(); 
           $splId = $actPromo['special_id']; 
           $splName = $actPromo['SpecialName'];
           
           if( $prefDistance > 0 && $lat !="" && $long !="" && $splId > 0 )
           {
               # Get nearest special store from users geofense area
               $storeDetails = $this -> productmodel -> get_special_store_details($splId,$lat,$long,$prefDistance); 
           }
           
           if($storeDetails)
           {
              $promotions[$index]['special_id'] = $splId;
              $promotions[$index]['SpecialName'] = $splName;
              $promotions[$index]['retailer_id'] = $storeDetails['RetailerId'];
              $promotions[$index]['RetailerName'] = $storeDetails['RetailerName'];
              $promotions[$index]['store_id'] = $storeDetails['StoreId'];
              $promotions[$index]['StoreName'] = $storeDetails['StoreName'];
              $promotions[$index]['store_type_id'] = $storeDetails['StoreTypeId'];
              
              $index++;
           }
        }        
        
        if( $promotions )
        {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = 'Promotion(s) available';
            $retArr['promotions'] = $promotions;            
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'No Promotion(s) available';
            $retArr['promotions'] = array();
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
}