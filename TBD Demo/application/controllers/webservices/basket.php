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
 * Purpose: Basket Webservices
 * Date:02-09-2015
 * Dependency: productmodel.php
 */

class Basket extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this -> post('api_key');

        validateApiKey($api_key);

        $this -> load -> model('webservices/basketmodel', '', TRUE);
        $this -> load -> model('webservices/productmodel', '', TRUE);
        $this -> load -> model('webservices/usermodel', '', TRUE);
        $this -> load -> model('webservices/quickshoppingmodel', '', TRUE);
    }

    // Add product to basket
    public function add_to_basket_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $special_id = $this -> post('special_id') ? $this -> post('special_id') : "";
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $product_count = (int) $this -> post('product_count') ? (int) $this -> post('product_count') : "1";
        
        $retailerId = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $storeId = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;
        
        
        $allToAdd = "No";
        
        if($storeId > 0 )
        {
            $storeDetails   = $this -> basketmodel -> get_store_retailer_details($storeId);
            $storeTypeId    = $storeDetails['StoreTypeId'];
        }else{
            # Get user preference
            $user_preference = $this -> usermodel ->get_user_preference($user_id);
            if($user_preference)
            {
                $retailerId     = $user_preference['RetailerId'];
                $storeTypeId    = $user_preference['StoreTypeId'];
                $storeId        = $user_preference['StoreId'];
            }
        }
       
        # Get products StorePrice        
        $productStorePrice = $this -> productmodel ->get_product_store_price($product_id,$retailerId,$storeTypeId,$storeId);
        
        if($productStorePrice > 0 )
        {
            $allToAdd = "Yes"; 
        }
        
        if($allToAdd == "Yes")
        {
            $result = $this -> basketmodel -> add_to_basket($special_id, $product_id, $user_id, $product_count);

            //$basket_count = $this -> basketmodel -> get_basket_count($user_id);
            $basket_count = $this -> basketmodel -> get_user_basket_count($user_id,$retailerId,$storeId);
            
            if ($result == 'duplicate') {
                $message = "Product already added to basket";
                $result = 0;
            }
            else {
                $message = "Product added to basket successfully";
            }

            if ($result) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = $message;
                $retArr['basket_count'] = $basket_count;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = $message;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        
        }else{
           $retArr['status'] = FAIL;
           $retArr['message'] = "Not allow to add this product.";
           $this -> response($retArr, 200); // 200 being the HTTP response code
           die; 
        }
    }
        
    // Add product to basket
    public function add_to_basket_new_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $special_id = (int) $this -> post('special_id') ? (int) $this -> post('special_id') : 0;
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $product_count = (int) $this -> post('product_count') ? (int) $this -> post('product_count') : "1";
        $addToShoppingList = trim($this -> post('add_to_shopping_list')) ? strtolower($this -> post('add_to_shopping_list')) : "no";
        $retailerId = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $storeId = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;
        
        $allToAdd       = "No";
        $isProductExist = "No";
        
        if($storeId > 0 )
        {
            $storeDetails   = $this -> basketmodel -> get_store_retailer_details($storeId);
            $storeTypeId    = $storeDetails['StoreTypeId'];
        }else{
            # Get user preference
            $user_preference = $this -> usermodel ->get_user_preference($user_id);
			
            if($user_preference)
            {
                $retailerId     = $user_preference['RetailerId'];
                $storeTypeId    = $user_preference['StoreTypeId'];
                $storeId        = $user_preference['StoreId'];
            }
        }
       
        # Get products StorePrice        
        $productStorePrice = $this -> productmodel ->get_product_store_price($product_id,$retailerId,$storeTypeId,$storeId);
        
        if($productStorePrice > 0 )
        {
            $allToAdd = "Yes"; 
        }
        
        if($allToAdd == "Yes")
        {
            $result = $this -> basketmodel -> add_to_basket($special_id, $product_id, $user_id, $product_count);
            
            
            # Add to shopping list starts
            if($addToShoppingList == 'yes'){ 
            
                # Get shopping list
                $shopping_list = $this -> quickshoppingmodel -> get_list($user_id);

                $shopping_list_array = array();
                if (!empty($shopping_list['ShoppingList'])) {
                    $shopping_list_array = explode(",", $shopping_list['ShoppingList']);
                }

                $item_details = array();
                if (!empty($shopping_list_array)) {
                    foreach ($shopping_list_array as $item) {
                        $item = str_replace('|||', ',', $item);
                        $item_array = explode(':::', $item);

                        $item_array['name'] = $item_array[0];
                        $item_array['product_id'] = $item_array[1];
                        $item_array['retailer_id'] = $item_array[2];
                        $item_array['store_type_id'] = $item_array[3];
                        $item_array['store_id'] = $item_array[4];
                        
                        if( $item_array['product_id'] == $product_id){
                            $item_array['count'] = $product_count;
                            $isProductExist = "Yes";
                        }else{
                            $item_array['count'] = $item_array[5];
                        }
                        
                        $item_array['bought'] = isset($item_array[6]) ? $item_array[6] : '0';

                        unset($item_array[0], $item_array[1], $item_array[2], $item_array[3], $item_array[4], $item_array[5], $item_array[6]);
                        $item_array['is_special'] = "0";

                        $user_pref_retailer = $this -> basketmodel -> get_user_preferred_retailer($user_id);
                        $product = $this -> productmodel -> product_details($item_array['product_id'], $user_pref_retailer -> Id, $user_id,$special_id);

                        $product_price = $product['store_price'];
                        if ($product['SpecialPrice'] > 0) {
                            $product_price = $product['SpecialPrice'];
                            $item_array['is_special'] = "1";
                        }
                        if ($product['SpecialPrice'] > 0 && $product['SpecialQty'] > 1) {
                            $product_price = $product['SpecialPrice'] / $product['SpecialQty'];
                        }
                        if ($item_array['count'] > 1) {
                            $product_price = $product_price * $item_array['count'];
                        }
                        $prod_price_arr = explode('.', $product_price);
                        if (!isset($prod_price_arr[1])) {

                            if ($product_price <= 0) {
                                $product_price = '0.00';
                            }
                            else {
                                $product_price = $product_price . '.00';
                            }
                            $item_array['price'] = $product_price;
                        }
                        else {
                            $product_price = round($product_price, 2);
                            $prod_price_arr = explode('.', $product_price);
                            if (!isset($prod_price_arr[1])) {
                                $product_price = $product_price . '.00';
                            }
                            elseif(strlen($prod_price_arr[1]) === 1){
                                $product_price = $prod_price_arr[0].'.'.$prod_price_arr[1].'0';
                            }
                            $item_array['price'] = $product_price.'';
                        }

                        $item_details[] = $item_array;
                    }
                }
               
                # Add new Item
                if( $isProductExist == "No" )
                {                
                    $userPreferrences = $this -> basketmodel -> get_user_preferred_retailer($user_id);
                    $productDetails = $this -> productmodel -> product_details($product_id, $userPreferrences -> Id, $user_id,$special_id);

                    $new_item_array = array ();
                    $new_item_array['name'] = $productDetails['ProductName'];
                    $new_item_array['product_id'] = $product_id;
                    $new_item_array['retailer_id'] = $userPreferrences -> Id;
                    $new_item_array['store_type_id'] = $userPreferrences -> StoreTypeId;
                    $new_item_array['store_id'] = $userPreferrences -> StoreId;
                    $new_item_array['count'] = $product_count;
                    $new_item_array['bought'] = '0';
                    $new_item_array['is_special'] = '0';

                    $product_price = $productDetails['store_price'];
                    if ($productDetails['SpecialPrice'] > 0) {
                        $product_price = $productDetails['SpecialPrice'];
                        $new_item_array['is_special'] = "1";
                    }
                    if ($productDetails['SpecialPrice'] > 0 && $productDetails['SpecialQty'] > 0) {
                        $product_price = $productDetails['SpecialPrice'] / $productDetails['SpecialQty'];
                    }
                    if ($new_item_array['count'] > 1) {
                        $product_price = $product_price * $new_item_array['count'];
                    }
                    $prod_price_arr = explode('.', $product_price);
                    if (!isset($prod_price_arr[1])) {
                        if ($product_price <= 0) {
                            $product_price = '0.00';
                        }
                        else {
                            $product_price = $product_price . '.00';
                        }
                        $new_item_array['price'] = $product_price;
                    }
                    else {
                        $product_price = round($product_price, 2);
                        $prod_price_arr = explode('.', $product_price);
                        if (!isset($prod_price_arr[1])) {
                            $product_price = $product_price . '.00';
                        }
                        elseif(strlen($prod_price_arr[1]) === 1){
                            $product_price = $prod_price_arr[0].'.'.$prod_price_arr[1].'0';
                        }
                        $new_item_array['price'] = $product_price.'';
                    }
                    $item_details[] = $new_item_array;
                }//if( $isProductExist = "No" )

                $shopping_list_string = '';
                $i = 0;
                foreach($item_details as $singleItem)
                {
                    if ($i == 0) {
                        $shopping_list_string .= $singleItem['name'] . ':::' . $singleItem['product_id'] . ':::' . $singleItem['retailer_id'] . ':::' . $singleItem['store_type_id'] . ':::' . $singleItem['store_id'] . ':::' . $singleItem['count'] . ':::' . $singleItem['bought'];
                    }
                    else {
                        $shopping_list_string .= ',' . $singleItem['name'] . ':::' . $singleItem['product_id'] . ':::' . $singleItem['retailer_id'] . ':::' . $singleItem['store_type_id'] . ':::' . $singleItem['store_id'] . ':::' . $singleItem['count'] . ':::' . $singleItem['bought'];
                    }
                    $i++;  
                }

                $shoppingListData = array(
                    'UserId' => $user_id,
                    'ShoppingList' => $shopping_list_string,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                );
                //Save the user shopping list
                $resultShoppingList = $this -> quickshoppingmodel -> save_list($user_id, $shoppingListData);
            
            } //if($addToShoppingList == 'yes')
            # Add product to shopping list ends
            
            //$basket_count = $this -> basketmodel -> get_basket_count($user_id);
            $basket_count = $this -> basketmodel -> get_user_basket_count($user_id,$retailerId,$storeId);
            
            if ($result == 'duplicate') {
                $message = "Product already added to basket";
                $result = 0;
            }
            else {
                $message = "Product added to basket successfully";
            }

            if ($result) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = $message;
                $retArr['basket_count'] = $basket_count;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = $message;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        
        }else{
           $retArr['status'] = FAIL;
           $retArr['message'] = "Not allow to add this product.";
           $this -> response($retArr, 200); // 200 being the HTTP response code
           die; 
        }
    }
    
    /*
     *  Function to remove product from user basket
     */

    public function remove_from_basket_post() {

        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $retailer_id = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;

        $result = $this -> basketmodel -> remove_from_basket($product_id, $user_id);
        
        //$basket_count = $this -> basketmodel -> get_basket_count($user_id);
        $basket_count = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['basket_count'] = $basket_count;
            $retArr['message'] = "Product removed successfully from basket";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Failed to remove product from basket";
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    /*
     * Get basket count
     */

    public function get_basket_count_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $retailer_id = (int) $this->post('retailer_id') ? (int)$this->post('retailer_id') : 0;
        $store_id = (int) $this->post('store_id') ? (int) $this->post('store_id') : 0;

        //$basket_count = $this -> basketmodel -> get_basket_count($user_id);
        $basket_count = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);

        $retArr['status'] = SUCCESS;
        $retArr['basket_count'] = $basket_count;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    /*
     * Get basket details
     */

    public function get_basket_details_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_type_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $basket_details = $this -> basketmodel -> get_user_basket(0, $user_id, $retailer_id, $store_type_id, $store_id);

        $retArr['status'] = SUCCESS;
        $retArr['basket_details'] = $basket_details;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    public function get_basket_min_details_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_type_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $basket_sum = $this -> basketmodel -> get_basket_min_details($user_id, $retailer_id, $store_type_id, $store_id);        
        $basket_details['total_count'] = $basket_sum['total_count'] ? $basket_sum['total_count'] : 0;
        
        $price = round($basket_sum['price_sum'], 2);
        $price_arr = explode('.', $price);
        if (!isset($price_arr[1])) {
            $price = $price_arr[0] . '.00';
        }
        else {
            if (strlen($price_arr[1]) == 1) {
                $price = $price_arr[0] . '.' . $price_arr[1] . '0';
            }
        }
        $basket_details['price_sum'] = $price;

        $price = '';

        $other_basket = $this -> basketmodel -> get_user_basket_other_retailers($user_id, $retailer_id, 0, '');
        if ($other_basket) {
            foreach ($other_basket as $basket) {
                $image_path = '';
                if ($basket['LogoImage']) {
                    $image_path = base_url() . '../assets/images/retailers/medium/' . $basket['LogoImage'];
                }
                else {
                    $image_path = "";
                }
                $price = round($basket['price_sum'], 2);
                $price_arr = explode('.', $price);
                if (!isset($price_arr[1])) {
                    $price = $price_arr[0] . '.00';
                }
                else {
                    if (strlen($price_arr[1]) == 1) {
                        $price = $price_arr[0] . '.' . $price_arr[1] . '0';
                    }
                }
                $basket_details['other_stores'][] = array(
                    'CompanyName' => $basket['CompanyName'],
                    'StoreName' => $basket['StoreName'],
                    'Price' => $price,
                    'LogoImage' => $image_path
                );
            }
        }
        else {
            $basket_details['other_stores'] = [];
        }
        
        $retArr['store_details'] = $this -> _get_product_store($user_id, $store_id, $retailer_id);
        $retArr['status'] = SUCCESS;
        $retArr['basket_details'] = $basket_details;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
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

    public function get_brand_list_post() {
        $user_id = $this -> post('user_id');
        $retailer_id = $this -> post('retailer_id');
        $storetype_id = $this -> post('store_type_id');
        $store_id = $this -> post('store_id');
        $category_id = $this -> post('category_id');
        $search_string = trim($this -> post('search_string'));

        $parent_category = $this -> basketmodel -> get_parent_cat_id($category_id);
        $brand_list = $this -> basketmodel -> get_brand_list($user_id, $retailer_id, $storetype_id, $store_id, $category_id, $search_string, $parent_category);

        if ($brand_list) {
            $retArr['status'] = SUCCESS;
            $retArr['brand_details'] = $brand_list;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['brand_details'] = [];
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function get_alternate_price_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $page_number = (int) $this -> post('page_number') ? $this -> post('page_number') : "";
        $price_order = $this -> post('price_order') ? trim($this -> post('price_order')) : "";
        $page_get = $page_number;


        if (!$page_get || $page_get == 0 || $page_get < 1) {
            $page_get = 1;
        }

        $basket_details = [];

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
            $limit_start = $page_number * ALTERNATE_PRICE_LIMIT;
        }
        else {
            $limit_start = $page_number * ALTERNATE_PRICE_LIMIT;
        }

        $other_basket = $this -> basketmodel -> get_user_basket_other_retailers($user_id, $retailer_id, $limit_start, $price_order);
        $other_basket_count = $this -> basketmodel -> get_user_basket_other_retailers_count($user_id, $retailer_id);

        $basket_details['total_pages'] = ceil($other_basket_count / ALTERNATE_PRICE_LIMIT);
        $basket_details['current_page'] = $page_get;

        if ($other_basket) {
            foreach ($other_basket as $basket) {
                $image_path = '';
                if ($basket['LogoImage']) {
                    $image_path = base_url() . '../assets/images/retailers/medium/' . $basket['LogoImage'];
                }
                else {
                    $image_path = "";
                }
                $price = round($basket['price_sum'], 2);
                $price_arr = explode('.', $price);
                if (!isset($price_arr[1])) {
                    $price = $price_arr[0] . '.00';
                }
                else {
                    if (strlen($price_arr[1]) == 1) {
                        $price = $price_arr[0] . '.' . $price_arr[1] . '0';
                    }
                }
                $basket_details['other_stores'][] = array(
                    'CompanyName' => $basket['CompanyName'],
                    'StoreName' => $basket['StoreName'],
                    'Price' => $price,
                    'LogoImage' => $image_path
                );
            }
        }
        else {
            $basket_details['other_stores'] = [];
        }

        $retArr['status'] = SUCCESS;
        $retArr['basket_details'] = $basket_details;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    public function edit_basket_count_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $basket_id = $this -> post('basket_id') ? $this -> post('basket_id') : "";
        $product_count = $this -> post('product_count') ? $this -> post('product_count') : "1";

        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_type_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";

        $result = $this -> basketmodel -> update_basket($user_id, $basket_id, $product_count);
        //$basket_count = $this -> basketmodel -> get_basket_count($user_id);
        $basket_count = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
        
        $single_basket_price = $this -> _get_basket_details_single($user_id, $retailer_id, $store_type_id, $store_id, $product_id);

        if (!$result) {
            $message = "Failed to update basket";
        }
        else {
            $message = "Basket updated successfully";
        }

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = $message;
            $retArr['basket_count'] = $basket_count;
            $retArr['price'] = $single_basket_price;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = $message;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function _get_basket_details_single($user_id, $retailer_id, $store_type_id, $store_id, $product_id) {
        $basket_details = $this -> basketmodel -> get_user_basket_single(0, $user_id, $retailer_id, $store_type_id, $store_id, $product_id);

        if ($basket_details) {
            $act_price = '';
            $price_arr = explode('.', round($basket_details[0]['Price'], 2));
            if (!isset($price_arr[1])) {
                $act_price = $price_arr[0] . '.00';
            }
            else {
                if (strlen($price_arr[1]) < 2) {
                    $act_price = $price_arr[0] . '.' . $price_arr[1] . '0';
                }
                else {
                    $act_price = $price_arr[0] . '.' . $price_arr[1];
                }
            }
            return $act_price;
        }
        return '';
    }

    public function get_basket_product_count_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $special_id = (int)$this -> post('special_id') ? (int)$this -> post('special_id') : 0;
        
        $product_count = $this -> basketmodel -> get_one_product_count($user_id, $product_id,$special_id);
        
        $retArr['status'] = SUCCESS;
        $retArr['count'] = $product_count;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    public function get_basket_product_count_new_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $special_id = (int)$this -> post('special_id') ? (int)$this -> post('special_id') : 0;
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        
        # Check user basket having products from another Retailer and Store
        $isPresent = $this -> basketmodel -> get_basket_product_count_from_another_retailer($user_id,$retailer_id,$store_id);
        $isPresent = $isPresent > 0 ? 1 : 0;
        
        $product_count = $this -> basketmodel -> get_basket_product_count($user_id, $product_id,$special_id,$retailer_id,$store_id);
        
        $retArr['status'] = SUCCESS;
        $retArr['count'] = $product_count;
        $retArr['isPresent'] = $isPresent;
        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    public function remove_user_basket_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $isRemoved = $this -> basketmodel -> remove_user_basket($user_id);
        if ($isRemoved) {
            $retArr['status'] = SUCCESS;
            $retArr['count'] = 'Items removed from basket';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['count'] = 'Failed to remove items from basket';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    
    
    // Add product to basket
    public function add_product_to_basket_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $special_id = (int) $this -> post('special_id') ? (int) $this -> post('special_id') : 0;
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
        $product_count = (int) $this -> post('product_count') ? (int) $this -> post('product_count') : "1";
        $addToShoppingList = trim($this -> post('add_to_shopping_list')) ? strtolower($this -> post('add_to_shopping_list')) : "no";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $remove_previous = trim($this -> post('remove_previous')) ? trim( strtolower($this -> post('remove_previous'))) : "no";
        
        $allToAdd       = "No";
        $isProductExist = "No";
        
        # Get Store details
        $storeDetails = $this -> basketmodel ->get_store_details($store_id);
        if($storeDetails)
        {
            $storeTypeId = $storeDetails['StoreTypeId'];
        
            # Get products StorePrice        
            $productStorePrice = $this -> productmodel ->get_product_store_price($product_id,$retailer_id,$storeTypeId,$store_id);
            
            if($productStorePrice > 0 )
            {
               $allToAdd = "Yes"; 
            }
        }
        
        if($allToAdd == "Yes")
        {
            # Remove all products from user's basket
            if( $remove_previous == 'yes')
            {
               $isRemoved = $this -> basketmodel -> remove_user_basket($user_id); 
            }
            
            $result = $this -> basketmodel -> add_product_to_basket($special_id, $product_id, $user_id, $product_count,$retailer_id,$store_id);
            
            # Add to shopping list starts
            if($addToShoppingList == 'yes'){ 
            
                # Get shopping list
                $shopping_list = $this -> quickshoppingmodel -> get_list($user_id);

                $shopping_list_array = array();
                if (!empty($shopping_list['ShoppingList'])) {
                    $shopping_list_array = explode(",", $shopping_list['ShoppingList']);
                }

                $item_details = array();
                if (!empty($shopping_list_array)) {
                    foreach ($shopping_list_array as $item) {
                        $item = str_replace('|||', ',', $item);
                        $item_array = explode(':::', $item);

                        $item_array['name'] = $item_array[0];
                        $item_array['product_id'] = $item_array[1];
                        $item_array['retailer_id'] = $item_array[2];
                        $item_array['store_type_id'] = $item_array[3];
                        $item_array['store_id'] = $item_array[4];
                        
                        if( $item_array['product_id'] == $product_id){
                            $item_array['count'] = $product_count;
                            $isProductExist = "Yes";
                        }else{
                            $item_array['count'] = $item_array[5];
                        }
                        
                        $item_array['bought'] = isset($item_array[6]) ? $item_array[6] : '0';

                        unset($item_array[0], $item_array[1], $item_array[2], $item_array[3], $item_array[4], $item_array[5], $item_array[6]);
                        $item_array['is_special'] = "0";

                        $user_pref_retailer = $this -> basketmodel -> get_user_preferred_retailer($user_id);
                        $product = $this -> productmodel -> product_details($item_array['product_id'], $user_pref_retailer -> Id, $user_id,$special_id);

                        $product_price = $product['store_price'];
                        if ($product['SpecialPrice'] > 0) {
                            $product_price = $product['SpecialPrice'];
                            $item_array['is_special'] = "1";
                        }
                        if ($product['SpecialPrice'] > 0 && $product['SpecialQty'] > 1) {
                            $product_price = $product['SpecialPrice'] / $product['SpecialQty'];
                        }
                        if ($item_array['count'] > 1) {
                            $product_price = $product_price * $item_array['count'];
                        }
                        $prod_price_arr = explode('.', $product_price);
                        if (!isset($prod_price_arr[1])) {

                            if ($product_price <= 0) {
                                $product_price = '0.00';
                            }
                            else {
                                $product_price = $product_price . '.00';
                            }
                            $item_array['price'] = $product_price;
                        }
                        else {
                            $product_price = round($product_price, 2);
                            $prod_price_arr = explode('.', $product_price);
                            if (!isset($prod_price_arr[1])) {
                                $product_price = $product_price . '.00';
                            }
                            elseif(strlen($prod_price_arr[1]) === 1){
                                $product_price = $prod_price_arr[0].'.'.$prod_price_arr[1].'0';
                            }
                            $item_array['price'] = $product_price.'';
                        }

                        $item_details[] = $item_array;
                    }
                }
               
                # Add new Item
                if( $isProductExist == "No" )
                {                
                    $userPreferrences = $this -> basketmodel -> get_user_preferred_retailer($user_id);
                    $productDetails = $this -> productmodel -> product_details($product_id, $userPreferrences -> Id, $user_id,$special_id);

                    $new_item_array = array ();
                    $new_item_array['name'] = $productDetails['ProductName'];
                    $new_item_array['product_id'] = $product_id;
                    $new_item_array['retailer_id'] = $userPreferrences -> Id;
                    $new_item_array['store_type_id'] = $userPreferrences -> StoreTypeId;
                    $new_item_array['store_id'] = $userPreferrences -> StoreId;
                    $new_item_array['count'] = $product_count;
                    $new_item_array['bought'] = '0';
                    $new_item_array['is_special'] = '0';

                    $product_price = $productDetails['store_price'];
                    if ($productDetails['SpecialPrice'] > 0) {
                        $product_price = $productDetails['SpecialPrice'];
                        $new_item_array['is_special'] = "1";
                    }
                    if ($productDetails['SpecialPrice'] > 0 && $productDetails['SpecialQty'] > 0) {
                        $product_price = $productDetails['SpecialPrice'] / $productDetails['SpecialQty'];
                    }
                    if ($new_item_array['count'] > 1) {
                        $product_price = $product_price * $new_item_array['count'];
                    }
                    $prod_price_arr = explode('.', $product_price);
                    if (!isset($prod_price_arr[1])) {
                        if ($product_price <= 0) {
                            $product_price = '0.00';
                        }
                        else {
                            $product_price = $product_price . '.00';
                        }
                        $new_item_array['price'] = $product_price;
                    }
                    else {
                        $product_price = round($product_price, 2);
                        $prod_price_arr = explode('.', $product_price);
                        if (!isset($prod_price_arr[1])) {
                            $product_price = $product_price . '.00';
                        }
                        elseif(strlen($prod_price_arr[1]) === 1){
                            $product_price = $prod_price_arr[0].'.'.$prod_price_arr[1].'0';
                        }
                        $new_item_array['price'] = $product_price.'';
                    }
                    $item_details[] = $new_item_array;
                }//if( $isProductExist = "No" )

                $shopping_list_string = '';
                $i = 0;
                foreach($item_details as $singleItem)
                {
                    if ($i == 0) {
                        $shopping_list_string .= $singleItem['name'] . ':::' . $singleItem['product_id'] . ':::' . $singleItem['retailer_id'] . ':::' . $singleItem['store_type_id'] . ':::' . $singleItem['store_id'] . ':::' . $singleItem['count'] . ':::' . $singleItem['bought'];
                    }
                    else {
                        $shopping_list_string .= ',' . $singleItem['name'] . ':::' . $singleItem['product_id'] . ':::' . $singleItem['retailer_id'] . ':::' . $singleItem['store_type_id'] . ':::' . $singleItem['store_id'] . ':::' . $singleItem['count'] . ':::' . $singleItem['bought'];
                    }
                    $i++;  
                }

                $shoppingListData = array(
                    'UserId' => $user_id,
                    'ShoppingList' => $shopping_list_string,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                );
                //Save the user shopping list
                $resultShoppingList = $this -> quickshoppingmodel -> save_list($user_id, $shoppingListData);
            
            } //if($addToShoppingList == 'yes')
            # Add product to shopping list ends
            
            //$basket_count = $this -> basketmodel -> get_basket_count($user_id);
            $basket_count = $this -> basketmodel -> get_user_basket_count($user_id,$retailer_id,$store_id);
            
            if ($result == 'duplicate') {
                $message = "Product already added to basket";
                $result = 0;
            }
            else {
                $message = "Product added to basket successfully";
            }

            if ($result) {
                $retArr['status'] = SUCCESS;
                $retArr['message'] = $message;
                $retArr['basket_count'] = $basket_count;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = $message;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        
        }else{
           $retArr['status'] = FAIL;
           $retArr['message'] = "Not allow to add this product.";
           $this -> response($retArr, 200); // 200 being the HTTP response code
           die; 
        }
    }
}