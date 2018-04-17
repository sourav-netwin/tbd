<?php

/*
 * Author:  PM
 * Purpose: Base Controller
 * Date:    08-10-2015
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
class My_Front_Controller extends CI_Controller {

    public $categories = array();
    public $all_categories = array();

    function __construct() {
        parent::__construct();

        // Loaded in common controller as it is needed almost everywhere
        $this -> load -> model('front/usermodel');
        $this -> load -> helper('text');

        $this -> config -> load('custom');

        //Get shopping list of user
        if (!$this -> session -> userdata('shopping_list')) {
            $this -> session -> set_userdata('shopping_list', $this -> usermodel -> get_quick_shopping_list());
        }

        // Code to check if user is logged in when he is browsing pages other than login and registration
        if (!in_array($this -> router -> fetch_class(), $this -> config -> item('guest_pages'))) {
            if (!$this -> session -> userdata('userid')) {
                $this -> session -> set_userdata('error_message', 'Please login in');
                redirect(front_url());
                die();
            }
            else {
                $retailer = $this -> usermodel -> get_user_preferred_retailer();
                if (!$retailer) {
                    redirect(front_url() . 'registration/set_details');
                }
            }
        }

        $this -> load -> model('front/categorymodel', '', TRUE);
        $this -> load -> model('front/contentmodel', '', TRUE);

        //Get active parent categories to be displayed that contain active not deleted products
        $categories = $this -> categorymodel -> get_category_having_products();

        $this -> site_data = array('terms_and_conditions_glb' => $this -> contentmodel -> get_content(1));

        foreach ($categories as $category) {
            if (!in_array($category['CategoryName'], $this -> categories))
                $this -> categories[$category['main_category_id']] = $category['CategoryName'];

            $this -> all_categories[$category['CategoryName']][$category['parent_category_id'] . '::' . $category['parent_category']][$category['sub_category_id']] = $category['sub_category'];
        }
        
        /*
        if(ismobile()){
           redirect(PLAY_STORE_URL);
           exit(0);
        }
        */
        
        
        $isMobile = ismobile();
        
        if($isMobile){
            if($isMobile == 1)
            {
                redirect(PLAY_STORE_URL);
            }else if($isMobile == 2){
                redirect(APP_STORE_URL);
            }
            exit(0);
        }
        
    }
    /* Function to get common data for product container view display ( top offer and search by category pages )
     * Return - array of data
     */

    function get_product_container_data() {
        $this -> load -> model('front/retailermodel');

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this -> all_categories;

        // Get count of active retailers that contain active not deleted store products
        $data['all_retailers_count'] = $this -> retailermodel -> get_retailers_count();

        //Get active retailers to be displayed that contain active not deleted store products
//        $data['retailers'] = $this -> retailermodel -> get_retailers_having_store_products($this -> config -> item('top_offer_retailer_limit'));
        $data['retailers'] = $this -> retailermodel -> get_retailers_having_store_products(0);

        // Fetch user prefered retailer and store
        $data['user_preferred_retailer'] = $product_data['user_preferred_retailer'] = $this -> usermodel -> get_user_preferred_retailer();
        
        $data['user_basket_total'] = $this -> usermodel -> get_user_basket_total();

        // Get 5 nearest stores for displaying in drop down
        $data['nearest_stores'] = $this -> retailermodel -> get_nearest_store($data['user_preferred_retailer'] -> Id, 5);

        // Get my basket data
        $data['user_basket'] = $this -> usermodel -> get_user_basket();
        $data['quick_shopping_list'] = '';
        if ($this -> session -> userdata('userid')) {
            $user_id = $this -> session -> userdata('userid');
            $shopping_list = $this -> usermodel -> get_quick_shopping_list();
            $shopping_list_array = array();
            if (!empty($shopping_list)) {
                $shopping_list_array = explode(",", $shopping_list);
            }
            $item_details = array();
            if (!empty($shopping_list_array)) {
                foreach ($shopping_list_array as $item) {
                    $item_array = explode(':::', $item);
                    $item_array['name'] = str_replace('|||',',',$item_array[0]);
                    $item_array['product_id'] = $item_array[1];
                    $item_array['retailer_id'] = $data['user_preferred_retailer']->Id;
                    $item_array['store_type_id'] = $data['user_preferred_retailer'] -> StoreTypeId;
                    $item_array['store_id'] = $data['user_preferred_retailer'] -> StoreId;
                    $item_array['count'] = $item_array[5];
                    unset($item_array[0], $item_array[1], $item_array[2], $item_array[3], $item_array[4], $item_array[5]);
                    $item_array['is_special'] = "0";
                    $product = $this -> usermodel -> product_details($item_array['product_id'], $item_array['retailer_id'], $user_id,$item_array['store_id']);
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
                    $product_price = round($product_price,2);
                    if($product_price){
                        $product_price_array = explode('.',$product_price);
                        if(!isset($product_price_array[1])){
                            $product_price = $product_price_array[0].'.00';
                        }
                        elseif(strlen($product_price_array[1]) == 1){
                            $product_price = $product_price_array[0].'.'.$product_price_array[1].'0';
                        }
                    }
                    $item_array['price'] = !$product_price ? 'NA' : $product_price;
                    $item_details[] = $item_array;
                }
                $data['quick_shopping_list'] = $item_details;
            }
        }


        // Get my basket count
        $data['user_basket_products_count'] = $this -> usermodel -> get_user_basket_products_count();

        // Get other retailers price
        $user_basket_other_retailer = $this -> usermodel -> get_user_basket_other_retailers();

        $array_retailer = $other_retailer = array();
        foreach ($user_basket_other_retailer as $value) {
            $array_retailer[$value['LogoImage']][] = $value['Price'];
        }
        foreach ($array_retailer as $key => $value) {
            $other_retailer[$key][] = $value[0];
            $other_retailer[$key][] = ( count($value) == $data['user_basket_products_count'] ? 1 : 0 );
        }

        $data['user_basket_other_retailer'] = $other_retailer;
        return $data;
    }

    function get_product_container_data_by_location($distance) {
        $this -> load -> model('front/retailermodel');
        $this -> load -> model('front/productmodel');

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this -> categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this -> all_categories;

        // Get count of active retailers that contain active not deleted store products
        $data['all_retailers_count'] = $this -> retailermodel -> get_retailers_count();

        //Get the user location preferrences
        $data['location_preference'] = $this -> productmodel -> get_user_location_preferences($this -> session -> userdata('userid'));

        //Get active retailers to be displayed that contain active not deleted store products
        $data['retailers'] = $this -> retailermodel -> get_retailers_having_store_products_location($this -> config -> item('top_offer_retailer_limit'), $data['location_preference'], $distance);

        // Fetch user prefered retailer and store
        $data['user_preferred_retailer'] = $product_data['user_preferred_retailer'] = $this -> usermodel -> get_user_preferred_retailer();

        // Get 5 nearest stores for displaying in drop down
        $data['nearest_stores'] = $this -> retailermodel -> get_nearest_store($data['user_preferred_retailer'] -> Id, 5);

        // Get my basket data
        $data['user_basket'] = $this -> usermodel -> get_user_basket();

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
        return $data;
    }
    /*
     * Purpose: Upload images
     * Param1 : field name
     * Param2 : folder_name
     */

    function do_upload($field_name, $folder_name, $x, $y, $w, $h) {

        $large_path = './assets/images/' . $folder_name . '/large/';
        $medium_path = './assets/images/' . $folder_name . '/medium';
        $small_path = './assets/images/' . $folder_name . '/small';

        $medium_size = 500;
        $small_size = 200;

        if ($folder_name == 'retailers') {
            $medium_size = 300;
            $small_size = 200;
        }

        $this -> load -> library('image_lib');

        if ($folder_name == 'sliders') {
            $config['upload_path'] = './assets/images/' . $folder_name;
        }
        else {
            $config['upload_path'] = './assets/images/' . $folder_name . '/original';
        }

        $config['allowed_types'] = 'gif|jpg|png|jpeg';

        $this -> load -> library('upload', $config);
        $result = $this -> upload -> do_upload($field_name);

        $data = $this -> upload -> data();
        if ($folder_name == 'sliders') {
            return $data;
        }

        if (!$result) {
            $error = array('error' => $this -> upload -> display_errors());

            return $error;
        }
        else {
            $data = array('upload_data' => $this -> upload -> data());


            //Crop Large Image

            $config = array(
                'source_image' => $data['upload_data']['full_path'], //path to the uploaded image
                'new_image' => $large_path, //path to new medium image
                'maintain_ratio' => FALSE,
                'width' => $w,
                'height' => $h
            );

            $config['x_axis'] = $x;
            $config['y_axis'] = $y;

            $this -> image_lib -> initialize($config);

            $result = $this -> image_lib -> crop();

            //Medium
            $config = array(
                'source_image' => $large_path . $data['upload_data']['file_name'], //path to the uploaded image
                'new_image' => $medium_path, //path to new medium image
                'maintain_ratio' => true,
                'width' => $medium_size,
                'height' => $medium_size
            );

            $this -> image_lib -> initialize($config);
            $this -> image_lib -> resize();

            //Small
            $config = array(
                'source_image' => $large_path . $data['upload_data']['file_name'], //path to the uploaded image
                'new_image' => $small_path, //path to new thumb image
                'maintain_ratio' => true,
                'width' => $small_size,
                'height' => $small_size
            );

            $this -> image_lib -> initialize($config);
            $this -> image_lib -> resize();

            return $data;
        }
    }
}
