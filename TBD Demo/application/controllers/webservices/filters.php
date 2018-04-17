<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/*
 * Author:PHN
 * Purpose: Filter Webservices
 * Date:02-09-2015
 * Dependency: productmodel.php
 */

class Filters extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this -> post('api_key');

        validateApiKey($api_key);

        $retArr = array();

        $this -> load -> model('webservices/productmodel', '', TRUE);

        $this -> load -> model('webservices/quickshoppingmodel', '', TRUE);
        $this -> load -> model('webservices/basketmodel', '', TRUE);

        //Set the latitude
        $this -> productmodel -> latitude = $this -> post('latitude') ? $this -> post('latitude') : "";

        $this -> productmodel -> longitude = $this -> post('longitude') ? $this -> post('longitude') : "";

        //Set the default store
        $this -> productmodel -> store_id = $this -> post('store_id') ? $this -> post('store_id') : "";

        //Set the page & the limit
        $this -> productmodel -> page_no = $this -> post('page') ? $this -> post('page') : "1";

        $this -> productmodel -> page_limit = API_PAGE_LIMIT;
    }

    public function get_shopping_list_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";

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
                $item_array['count'] = $item_array[5];
                $item_array['bought'] = isset($item_array[6]) ? $item_array[6] : '0';
                
                
                $store_id = $item_array[4];
                $storeRetailerDetails = $this -> basketmodel ->get_store_retailer_details($store_id);
                
                
                $item_array['retailer_name'] = $storeRetailerDetails['retailerName'];
                $item_array['store_name'] = $storeRetailerDetails['StoreName'];
                
                
                unset($item_array[0], $item_array[1], $item_array[2], $item_array[3], $item_array[4], $item_array[5], $item_array[6]);
                $item_array['is_special'] = "0";
                $user_pref_retailer = $this -> basketmodel -> get_user_preferred_retailer($user_id);
                $product = $this -> productmodel -> product_details($item_array['product_id'], $user_pref_retailer -> Id, $user_id);
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
            
            # Sort array by productname
            $this -> array_sort_by_column($item_details, 'name');
            
            $retArr['status'] = SUCCESS;
            $retArr['shopping_list'] = $item_details;
            $retArr['store_details'] = $this -> _get_product_store($user_id, $store_id, $retailer_id);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['shopping_list'] = [];
            $retArr['store_details'] = $this -> _get_product_store($user_id, $store_id, $retailer_id);
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    /* Function to sort multidimentional array by column */
    public   function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    public function save_shopping_list_post() {

        $shopping_list = $this -> post('shopping_list') ? $this -> post('shopping_list') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        //$shopping_list_string = implode(",", $shopping_list);
        $shopping_list_string = '';
        if (!empty($shopping_list)) {
            $i = 0;
            foreach ($shopping_list as $product) { 
                $product['name'] ? $product['name'] = str_replace(',', '|||', $product['name']) : $product['name'] = '0';
                $product['product_id'] ? $product['product_id'] = $product['product_id'] : $product['product_id'] = '0';
                $product['retailer_id'] ? $product['retailer_id'] = $product['retailer_id'] : $product['retailer_id'] = '0';
                $product['store_type_id'] ? $product['store_type_id'] = $product['store_type_id'] : $product['store_type_id'] = '0';
                $product['store_id'] ? $product['store_id'] = $product['store_id'] : $product['store_id'] = '0';
                $product['count'] ? $product['count'] = $product['count'] : $product['count'] = '0';
                $product['bought'] ? $product['bought'] = $product['bought'] : $product['bought'] = '0';
                if ($i == 0) {
                    $shopping_list_string .= $product['name'] . ':::' . $product['product_id'] . ':::' . $product['retailer_id'] . ':::' . $product['store_type_id'] . ':::' . $product['store_id'] . ':::' . $product['count'] . ':::' . $product['bought'];
                }
                else {
                    $shopping_list_string .= ',' . $product['name'] . ':::' . $product['product_id'] . ':::' . $product['retailer_id'] . ':::' . $product['store_type_id'] . ':::' . $product['store_id'] . ':::' . $product['count'] . ':::' . $product['bought'];
                }
                $i++;
            }
            $data = array(
                'UserId' => $user_id,
                'ShoppingList' => $shopping_list_string,
                'CreatedOn' => date('Y-m-d H:i:s'),
            );
            //Save the user shopping list
            $result = $this -> quickshoppingmodel -> save_list($user_id, $data);
            if ($result) {
                $retArr['status'] = SUCCESS;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
            else {
                $retArr['status'] = FAIL;
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function quickshopping_list_post() {

        $shopping_list = $this -> post('shopping_list') ? $this -> post('shopping_list') : "";
        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $shopping_list_string = implode(",", $shopping_list);
        $data = array(
            'UserId' => $user_id,
            'ShoppingList' => $shopping_list_string,
            'CreatedOn' => date('Y-m-d H:i:s'),
        );

        //Save the user shopping list
        $this -> quickshoppingmodel -> save_list($user_id, $data);

        // Search the products for the shopping list
        $products_to_search = $this -> productmodel -> get_products_by_shopping_list($retailer_id, $user_id, $shopping_list);

        foreach ($products_to_search as $key => $products) {
            $product_string = array();



            foreach ($products as $products_list) {
                $product_string[] = $products_list['Id'];
            }
            $products = array();


            // Get the product details
            if ($product_string)
                $products = $this -> productmodel -> get_products("", $retailer_id, $user_id, $product_string);

            $i = 0; //Encode image of products
            foreach ($products as $product) {
                if ($product['ProductImage'])
                    $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
                else
                    $products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . DEFAULT_PRODUCT_IMAGE_PATH);

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
                $i++;
            }

            $products_to_search[$key] = $products;
        }

        if ($products_to_search) {
            $retArr['status'] = SUCCESS;
            $retArr['search_result'] = $products_to_search;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
        else {
            $retArr['status'] = FAIL;
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    public function delete_quick_list_post() {
        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";
        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";

        $shopping_list = $this -> quickshoppingmodel -> get_list($user_id);

        $shopping_list_array = array();
        if (!empty($shopping_list['ShoppingList'])) {
            $shopping_list_array = explode(",", $shopping_list['ShoppingList']);
            if ($shopping_list_array) {
                $new_list = [];
                foreach ($shopping_list_array as $index => $item) {
                    $item_array = explode(':::', $item);
                    $item_array[1] = str_replace('|||', ',', $item_array[1]);
                    if ($item_array[1] == $product_id) {
                        unset($shopping_list_array[$index]);
                    }
                    else{
                        $item_array[1] = str_replace(',', '|||', $item_array[1]);
                    }
                }
                $new_list = implode(',', $shopping_list_array);
                $data = array(
                    'UserId' => $user_id,
                    'ShoppingList' => $new_list,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                );
                //Save the user shopping list
                $result = $this -> quickshoppingmodel -> save_list($user_id, $data);
                if ($result) {
                    $retArr['status'] = SUCCESS;
                    $retArr['message'] = 'Item removed successfully';
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
                else {
                    $retArr['status'] = FAIL;
                    $retArr['message'] = 'Failed to remove item';
                    $this -> response($retArr, 200); // 200 being the HTTP response code
                    die;
                }
            }
            else {
                $retArr['status'] = FAIL;
                $retArr['message'] = 'Failed to remove item';
                $this -> response($retArr, 200); // 200 being the HTTP response code
                die;
            }
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = 'Failed to remove item';
            $this -> response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    public function _get_product_store($user_id = '', $store_id = '', $retailer_id = '') {

//        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
//        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
//        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

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
}

?>