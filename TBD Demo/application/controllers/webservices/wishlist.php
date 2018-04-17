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
 * Purpose: WishList Webservices
 * Date:02-09-2015
 * Dependency: wishlistmodel.php
 */

class WishList extends REST_Controller {

    function __construct() {
        parent::__construct();

        $api_key = $this->post('api_key');

        validateApiKey($api_key);

        $retArr = array();

        $this->load->model('webservices/wishlistmodel', '', TRUE);
        $this->load->model('webservices/productmodel', '', TRUE);
        
        


    }

    /**
     * List the wishlist for a particular user
     */
    public function list_post() {

        $user_id = $this->post('user_id') ? $this->post('user_id') : "";
        $product_id = $this->post('product_id') ? $this->post('product_id') : "''";

        $wishlists = $this->wishlistmodel->get_user_wishlist($user_id, $product_id);

        $i = 0;
        foreach ($wishlists as $wishlist) {
            if ($wishlist['products_count'] == 'null')
                $wishlists[$i]['products_count'] = 0;
            $i++;
        }

        $retArr['status'] = SUCCESS;
        $retArr['wishlist'] = $wishlists;
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    /**
     * Create a new wishlist
     */
    public function create_post() {

        $user_id = $this->post('user_id') ? $this->post('user_id') : "";

        $wishlist_name = $this->post('wishlist_name') ? $this->post('wishlist_name') : "";

        $data = array(
            'UserId' => $user_id,
            'WishlistDescription' => $wishlist_name,
            'CreatedBy' => $user_id,
            'CreatedOn' => date("Y-m-d H:i:s")
        );

        $result = $this->wishlistmodel->add_wishlist($data);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['Id'] = $result;
            $retArr['WishlistDescription'] = $wishlist_name;
            $retArr['CreatedOn'] = date("d-m-Y");
            $retArr['products_count'] = 0;
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Error in creating wishlist";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Update a wishlist
     */
    public function update_post() {

        $user_id = $this->post('user_id') ? $this->post('user_id') : "";

        $wishlist_id = $this->post('wishlist_id') ? $this->post('wishlist_id') : "";

        $wishlist_name = $this->post('wishlist_name') ? $this->post('wishlist_name') : "";

        $data = array(
            'UserId' => $user_id,
            'WishlistDescription' => $wishlist_name,
            'ModifiedBy' => $user_id,
            'ModifiedOn' => date("Y-m-d H:i:s")
        );

        $result = $this->wishlistmodel->update_wishlist($wishlist_id, $data);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['wishlist'] = $result;
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Error in updating wishlist";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /*
     * Delete a wishlist
     */

    public function delete_post() {
        $wishlist_id = $this->post('wishlist_id') ? $this->post('wishlist_id') : "";

        $result = $this->wishlistmodel->delete_wishlist($wishlist_id);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['wishlist'] = $result;
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Error in deleting wishlist";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }
    
    public function delete_multiple_post() {
        $wishlist_ids = $this -> post('wishlist_ids') ? $this -> post('wishlist_ids') : "";
        $ids = explode(',', $wishlist_ids);
        $this -> wishlistmodel -> delete_wishlists($ids);

        $retArr['status'] = SUCCESS;

        $this -> response($retArr, 200); // 200 being the HTTP response code
        die;
    }

    /**
     * Add product to wishlist
     */
    public function add_product_wishlist_post() {

        $user_id = $this->post('user_id') ? $this->post('user_id') : "";
        $wishlist_id = $this->post('wishlist_id') ? $this->post('wishlist_id') : "";
        $product_id = $this->post('product_id') ? $this->post('product_id') : "";
        $retailer_id = $this->post('retailer_id') ? $this->post('retailer_id') : "";
        $store_id = $this->post('store_id') ? $this->post('store_id') : "";
        $special_id = $this->post('special_id') ? $this->post('special_id') : "";

        $data = array(
            'UserId' => $user_id,
            'UserWishlistId' => $wishlist_id,
            'ProductId' => $product_id,
            'RetailerId' => $retailer_id,
            'StoreId' => $store_id,
            'SpecialId' => $special_id
        );

        $wishlist = $this->wishlistmodel->add_product_wishlist($data);

        if ($wishlist) {
            $retArr['status'] = SUCCESS;
            $retArr['wishlist_product_id'] = $wishlist;
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Product already added to wishlist";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Delete product from wishlist
     */
    public function delete_product_wishlist_post() {
        $user_id = $this->post('user_id') ? $this->post('user_id') : "";
        $wishlist_id = $this->post('wishlist_id') ? $this->post('wishlist_id') : "";
        $product_id = $this->post('product_id') ? $this->post('product_id') : "";
        $retailer_id = $this->post('retailer_id') ? $this->post('retailer_id') : "";

        $data = array(
            'UserWishlistId' => $wishlist_id,
            'ProductId' => $product_id,
            'RetailerId' => $retailer_id,
            'UserId' => $user_id
        );

        $result = $this->wishlistmodel->delete_product_wishlist($data);

        if ($result) {
            $retArr['status'] = SUCCESS;
            $retArr['wishlist'] = $result;
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = "Error in deleting product from wishlist";
            $this->response($retArr, 200); // 200 being the HTTP response code
            die;
        }
    }

    /**
     * Show wishlist products
     */
    public function list_wishlist_product_post() {
        
        $wishlist_id = $this->post('wishlist_id') ? $this->post('wishlist_id') : "";        
        $user_id = $this->post('user_id') ? $this->post('user_id') : "";
        $retailer_id = $this->post('retailer_id') ? $this->post('retailer_id') : "";
        $store_id = $this->post('store_id') ? $this->post('store_id') : "";
        
        $wishlist_products = $this->wishlistmodel->get_user_wishlist_products($wishlist_id,$retailer_id);
        $preferred_details = $this->wishlistmodel->get_user_preferred_details($user_id);

        $i = 0;
        foreach ($wishlist_products as $product) {
            if ($product['ProductImage'])
                $wishlist_products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
            else
                $wishlist_products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . DEFAULT_PRODUCT_IMAGE_PATH);

            if ($product['LogoImage'])
                $wishlist_products[$i]['LogoImage'] = (front_url() . RETAILER_IMAGE_PATH . "medium/" .$product['LogoImage']);

            if ($product['avg_rating'] == NULL) {
                $wishlist_products[$i]['avg_rating'] = "0";
            }

            if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                $wishlist_products[$i]['SpecialQty'] = "0";
                $wishlist_products[$i]['SpecialPrice'] = "0";
            }

            if ($product['StoreName'])
                $wishlist_products[$i]['StoreName'] = "";

            if ($product['IsStore'] == NULL) {
                $wishlist_products[$i]['IsStore'] = "0";
            }
            
            if ($product['special_id'] == NULL) {
                $wishlist_products[$i]['special_id'] = "0";
            }
            $i++;
        }

        $retArr['status'] = SUCCESS;
        $retArr['wishlist_products'] = $wishlist_products;
        $retArr['preferred_details'] = $preferred_details;
        $retArr['store_details'] = $this -> _get_product_store($user_id, $store_id, $retailer_id);
        
        $this->response($retArr, 200); // 200 being the HTTP response code
        die;
    }
    
    /*
     *  Get Store details 
     */
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
}