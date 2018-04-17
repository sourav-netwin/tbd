<?php

/*
 * Author:  PM
 * Purpose: View Basket Controller
 * Date:    27-10-2015
 */

class Viewbasket extends My_Front_Controller {

    function __construct() {
        parent::__construct();
        $this -> load -> model('front/usermodel', '', TRUE);
        if (!$this -> usermodel -> check_email_entered($this -> session -> userdata('userid'))) {
            redirect(front_url() . 'registration/set_email');
            exit(0);
        }
    }

    //Function to load entire user basket
    public function index() {
        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this -> all_categories;

        // Get my basket data
        $data['user_basket'] = $this -> usermodel -> get_user_basket(0);

        // Get my basket count
        $data['user_basket_products_count'] = $this -> usermodel -> get_user_basket_products_count();

        // Get other retailers price
        $user_basket_other_retailer = $this -> usermodel -> get_user_basket_other_retailers();

        $array_retailer = $other_retailer = array();
        foreach ($user_basket_other_retailer as $value) {
            $array_retailer[$value['LogoImage']][] = $value['Price'];
        }
        foreach ($array_retailer as $key => $value) {
            $other_retailer[$key][] = array_sum($value);
            $other_retailer[$key][] = ( count($value) == $data['user_basket_products_count'] ? 1 : 0 );
        }

        $data['user_basket_other_retailer'] = $other_retailer;

        $this -> template -> front_view('front/view_basket', $data, 1);
    }
    /* Function to remove product from user basket
     */

    public function remove_from_basket() {
        $product_id = $this -> input -> post('product');
        $result = $this -> usermodel -> remove_from_basket($product_id);
        echo $result;
    }

    public function update_basket_count() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $count = $this -> input -> post('count');
            $sel_id = $this -> input -> post('sel_id');
            if(!is_numeric($count)){
                $count = 1;
            }
            elseif($count <= 0){
                $count = 1;
            }
            elseif($count > 999){
                $count = 999;
            }
            $update_data = array(
                'ProductCount' => $count
            );
            $where = array(
                'Id' => $sel_id
            );
            $is_updated = $this -> usermodel -> update_user_basket($update_data, $where);
            //$is_updated = true;
            if($is_updated){
                $basket_details = $this -> usermodel -> get_user_basket_from_id($sel_id);
                $this -> result = 1;
                $price_data = round($basket_details['Price'],2);
                $price_arr =  explode('.',$price_data);
                if(!isset($price_arr[1])){
                    $price_data = $price_data.'.00';
                }
                $this -> message = $price_data;
            }
            else{
                $this -> result = 0;
            $this -> message = 'Failed to update the count';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
}

?>