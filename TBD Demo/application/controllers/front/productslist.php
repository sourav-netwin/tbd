<?php

/*
 * Author:  PM
 * Purpose: Products List Controller
 * Date:    19-10-2015
 */

class Productslist extends My_Front_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('front/retailermodel');
        $this -> load -> model('front/productmodel');
        $this -> load -> helper('text');
        $this -> config -> load('topoffers');
        $this -> load -> model('front/usermodel', '', TRUE);
        $this -> load -> model('front/quickshoppinglistmodel', '', TRUE);
        if (!$this -> usermodel -> check_email_entered($this -> session -> userdata('userid'))) {
            redirect(front_url() . 'registration/set_email');
            exit(0);
        }
    }

    //Function to load products on click of categories
    public function index($category_type = "", $category_id = "") {
        $category_id = $category_id != '' ? $this -> encrypt -> decode($category_id) : '';
        // Get product container data from common controller
        $data = $this -> get_product_container_data();

        // Get price range to be displayed for searching
        $data['price_range'] = $this -> productmodel -> get_price_range($category_type, $category_id);

        if ($category_type != '' && $category_id != '') {
            // Get category name
            if ($category_type == 'sub') {
                $category_details = $this -> categorymodel -> get_category_details($category_id, $category_type);
                $product_data['category_name'] = $category_details['sub_cat_name'];
                $breadcrumb_data['parent_cat'] = $category_details['parent_cat_name'];
                $breadcrumb_data['main_cat'] = $category_details['main_cat_name'];
                $breadcrumb_data['sub_cat'] = $category_details['sub_cat_name'];
                $breadcrumb_data['parent_cat_id'] = $category_details['parent_cat_id'];
                $breadcrumb_data['main_cat_id'] = $category_details['main_cat_id'];
                $breadcrumb_data['sub_cat_id'] = $category_details['sub_cat_id'];
            }
            else {
                $category_details = $this -> categorymodel -> get_category_details($category_id, $category_type);
                $product_data['category_name'] = $category_details['CategoryName'];
                $breadcrumb_data['parent_cat'] = $category_details['main_cat'];
                $breadcrumb_data['main_cat'] = $category_details['CategoryName'];
                $breadcrumb_data['parent_cat_id'] = $category_details['main_cat_id'];
                $breadcrumb_data['main_cat_id'] = $category_details['CategoryId'];
            }
        }


        // Count of products for that category to be displayed for that prefered retailer and store
        $product_data['count_products'] = $this -> productmodel -> get_products_count($category_type, $category_id);

        // Top offers to be displayed for that prefered retailer and store
        $product_data['top_offers'] = $this -> productmodel -> get_products($category_type, $category_id);
        $products_list_data['top_offers'] = $this -> load -> view('front/top_offer_products', $product_data, TRUE);

        $products_list_data['category_type'] = $category_type;
        $products_list_data['category_id'] = $category_id;
        $products_list_data['user_preferred_retailer'] = $data['user_preferred_retailer'];

        $products_list_data['last_product_id'] = ( (count($product_data['top_offers']) - 1) > 0 ) ? $product_data['top_offers'][(count($product_data['top_offers']) - 1)]['Id'] : 0;

        $data['product_list'] = $this -> load -> view('front/products_list', $products_list_data, TRUE);

        $data['is_top_offer'] = 0;

        if (isset($breadcrumb_data['parent_cat'])) {
            $data['breadcrumbs'][] = array(
                'name' => $breadcrumb_data['parent_cat'],
                'url' => ''
            );
        }
        if (isset($breadcrumb_data['main_cat']) && isset($breadcrumb_data['sub_cat'])) {
            $data['breadcrumbs'][] = array(
                'name' => $breadcrumb_data['main_cat'],
                'url' => front_url() . 'productslist/index/parent/' . $this -> encrypt -> encode($breadcrumb_data['main_cat_id'])
            );
        }
        else if (isset($breadcrumb_data['main_cat'])) {
            $data['breadcrumbs'][] = array(
                'name' => $breadcrumb_data['main_cat'],
                'url' => ''
            );
        }
        if (isset($breadcrumb_data['sub_cat'])) {
            $data['breadcrumbs'][] = array(
                'name' => $breadcrumb_data['sub_cat'],
                'url' => ''
            );
        }
//        $data['breadcrumbs'] = array(
//            array(
//                'name' => $product_data['category_name'],
//                'url' => ''
//            )
//        );

        $this -> template -> front_view('front/products_container', $data, 1);
    }

    public function search() {
        $search_text = $this -> input -> get('search_text');

        // Get product container data from common controller
        $data = $this -> get_product_container_data();

        // Get price range to be displayed for searching
//        $data['price_range'] = $this->productmodel->get_price_range( $category_type, $category_id );
        // Count of products for that category to be displayed for that prefered retailer and store
        $product_data['count_products'] = $this -> productmodel -> get_products_count("", "", 0, $search_text);

        // Top offers to be displayed for that prefered retailer and store
        $product_data['top_offers'] = $this -> productmodel -> get_products("", "", "", "", "", "", $search_text);
        $products_list_data['top_offers'] = $this -> load -> view('front/top_offer_products', $product_data, TRUE);

        $products_list_data['search_text'] = $search_text;

        $products_list_data['user_preferred_retailer'] = $data['user_preferred_retailer'];

        $products_list_data['last_product_id'] = ( (count($product_data['top_offers']) - 1) > 0 ) ? $product_data['top_offers'][(count($product_data['top_offers']) - 1)]['Id'] : 0;

        $data['product_list'] = $this -> load -> view('front/search_products', $products_list_data, TRUE);

        $data['is_top_offer'] = 0;

        $data['breadcrumbs'] = array(
            array(
                'name' => 'Search for: "' . $products_list_data['search_text'] . '"',
                'url' => ''
            )
        );

        $this -> template -> front_view('front/products_container', $data, 1);
    }

    public function search_quick() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $search_text = urldecode($this -> input -> post('search_text'));

            // Get product container data from common controller
            $data = $this -> get_product_container_data();

            // Get price range to be displayed for searching
//        $data['price_range'] = $this->productmodel->get_price_range( $category_type, $category_id );
            // Count of products for that category to be displayed for that prefered retailer and store
            $product_data['count_products'] = $this -> productmodel -> get_products_count("", "", 0, $search_text);

            // Top offers to be displayed for that prefered retailer and store
            $products = $this -> productmodel -> get_products("", "", "", "", "", "", $search_text);

            if ($products) {
                $html = '<ul>';
                foreach ($products as $product) {
                    $is_special = "0";
                    $product_price = $product['store_price'];
                    if ($product['SpecialPrice'] > 0) {
                        $product_price = $product['SpecialPrice'];
                        $is_special = "1";
                    }
                    if ($product['SpecialPrice'] > 0 && $product['SpecialQty'] > 1) {
                        $product_price = $product['SpecialPrice'] / $product['SpecialQty'];
                    }
                    $html .= '<li data-pi="' . $product['Id'] . '" data-ri="' . $product['RetailerId'] . '" data-sti="' . $product['StoreTypeId'] . '" data-si="' . $product['StoreId'] . '" data-sp="' . $is_special . '" data-pr="' . round($product_price,2) . '">' . $product['ProductName'] . '</li>';
                }
                $html .= '</ul>';
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No products found';
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

    public function remove_quick() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $full_text = urldecode($this -> input -> post('full_str'));

            $shopping_list = $this -> usermodel -> get_quick_shopping_list();
            $shopping_list = explode(',', $shopping_list);
            if ($shopping_list) {
                foreach ($shopping_list as $index => $value) {
                    if (strpos(str_replace('|||',',',$value), $full_text) === 0) {
                        unset($shopping_list[$index]);
                    }
                    else{
                        $shopping_list[$index] = str_replace(',','|||',$value);
                    }
                }
                $new_text = implode(',', $shopping_list);
                $data = array(
                    'UserId' => $this -> session -> userdata('userid'),
                    'ShoppingList' => $new_text,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                );
                $is_update = $this -> quickshoppinglistmodel -> save_list($this -> session -> userdata('userid'), $data);
                if ($is_update) {
                    $this -> result = 1;
                    $this -> message = 'Item removed from the list';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to remove item from the list';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'No records found to delete';
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

    // Search products based on price range provided
    public function search_products() {
        $price_range = $this -> input -> post('price_range');
        $category_id = $this -> input -> post('category_id');
        $category_type = $this -> input -> post('category_type');

        $search_text = $this -> input -> post('search_text');

        $data['top_offers'] = $this -> productmodel -> get_products($category_type, $category_id, $price_range, $last_product_id = 0, $show_offer = 0, $product_ids = array(), $search_text);

        $last_product_id = ( (count($data['top_offers']) - 1) > 0 ) ? $data['top_offers'][(count($data['top_offers']) - 1)]['Id'] : 0;

        echo json_encode(array('view' => $this -> load -> view('front/top_offer_products', $data, TRUE), 'last_product' => $last_product_id));
    }

    // Show more products
    public function show_more_products() {
        $last_product = $this -> input -> post('last_product');
        $show_offer = $this -> input -> post('show_offer');
        $category_id = $this -> input -> post('category_id');
        $category_type = $this -> input -> post('category_type');
        $price_range = $this -> input -> post('price_range');

        // Count of products for that category to be displayed for that prefered retailer and store
        $count_products = $this -> productmodel -> get_products_count($category_type, $category_id, $show_offer);

        $data['top_offers'] = $this -> productmodel -> get_products($category_type, $category_id, $price_range, $last_product, $show_offer);

        $last_product_id = ( (count($data['top_offers']) - 1) > 0 ) ? $data['top_offers'][(count($data['top_offers']) - 1)]['Id'] : 0;

        echo json_encode(array('view' => $this -> load -> view('front/top_offer_products', $data, TRUE), 'count' => $count_products, 'last_product' => $last_product_id));
    }

    // Show offer products
    public function show_offer_products() {
        $show_offer = $this -> input -> post('show_offer');
        $category_id = $this -> input -> post('category_id');
        $category_type = $this -> input -> post('category_type');
        $price_range = $this -> input -> post('price_range');

        $search_text = $this -> input -> post('search_text');

        // Count of products for that category to be displayed for that prefered retailer and store
        $count_products = $this -> productmodel -> get_products_count($category_type, $category_id, $show_offer, $search_text);

        $data['top_offers'] = $this -> productmodel -> get_products($category_type, $category_id, $price_range, 0, $show_offer, $product_ids = array(), $search_text);
        ;
        $last_product_id = ( (count($data['top_offers']) - 1) > 0 ) ? $data['top_offers'][(count($data['top_offers']) - 1)]['Id'] : 0;

        echo json_encode(array('view' => $this -> load -> view('front/top_offer_products', $data, TRUE), 'count' => $count_products, 'last_product' => $last_product_id));
    }

    public function add_single_quick() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $full_text = urldecode($this -> input -> post('item'));
            $shopping_list = $this -> usermodel -> get_quick_shopping_list();
            if ($shopping_list) {
                $shopping_list = explode(',', $shopping_list);
            }
            else {
                $shopping_list = '';
            }
            if ($shopping_list) {
                $prod_count = 0;
                $full_text_arr = explode(':::', $full_text);
                foreach ($shopping_list as $item) {
                    $item_arr = explode(':::', $item);
                    if ($item_arr[1] == $full_text_arr[1]) {
                        $prod_count++;
                    }
                }
                if ($prod_count == 0) {
                    if ($shopping_list) {
                        $new_text = implode(',', $shopping_list) . ',' . $full_text;
                    }
                    else {
                        $new_text = $full_text;
                    }
                    $data = array(
                        'UserId' => $this -> session -> userdata('userid'),
                        'ShoppingList' => $new_text,
                        'CreatedOn' => date('Y-m-d H:i:s'),
                    );
                    $is_save = $this -> quickshoppinglistmodel -> save_list($this -> session -> userdata('userid'), $data);
                    if ($is_save) {
                        $this -> result = 1;
                        $this -> message = 'Item added to the list';
                    }
                    else {
                        $this -> result = 0;
                        $this -> message = 'Failed to add item to the list';
                    }
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'The product already there in the list';
                }
            }
            else {
                $data = array(
                    'UserId' => $this -> session -> userdata('userid'),
                    'ShoppingList' => $full_text,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                );
                $is_save = $this -> quickshoppinglistmodel -> save_list($this -> session -> userdata('userid'), $data);
                if ($is_save) {
                    $this -> result = 1;
                    $this -> message = 'Item added to the list';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to add item to the list';
                }
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

    public function get_quick_price_one() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $product_id = $this -> input -> post('pro_id');
            $count = $this -> input -> post('count');

            $data = $this -> get_product_container_data();

            $is_special = "0";
            $product = $this -> usermodel -> product_details($product_id, $data['user_preferred_retailer'] -> Id, $this -> session -> userdata('userid'), $data['user_preferred_retailer'] -> StoreId);
            $product_price = $product['store_price'];
            if ($product['SpecialPrice'] > 0) {
                $product_price = $product['SpecialPrice'];
                $is_special = "1";
            }
            if ($product['SpecialPrice'] > 0 && $product['SpecialQty'] > 1) {
                $product_price = $product['SpecialPrice'] / $product['SpecialQty'];
            }
            if ($count > 1) {
                $product_price = $product_price * $count;
            }
            $product_price = round($product_price, 2);
            $product_price = $product_price.'';
            $price_arr = explode('.', $product_price);
            if (!isset($price_arr[1])) {
                $product_price = $price_arr[0] . '.00';
            }
            elseif(strlen($price_arr[1]) == 1){
                $product_price = $price_arr[0].'.'.$price_arr[1].'0';
            }
            $product_price = !$product_price ? 'NA' : $product_price;
            $shopping_list = $this -> usermodel -> get_quick_shopping_list();
            if ($shopping_list) {
                $shopping_list = explode(',', $shopping_list);
            }
            else {
                $shopping_list = '';
            }
            if ($shopping_list) {
                $prod_count = 0;
                $new_item_data = '';
                foreach ($shopping_list as $index => $item) {
                    $item_arr = explode(':::', $item);
                    if ($item_arr[1] == $product_id) {
                        $item_arr[5] = $count;
                    }
                    if ($index == 0) {
                        $new_item_data .= implode(':::', $item_arr);
                    }
                    else {
                        $new_item_data .= ',' . implode(':::', $item_arr);
                    }
                }
                $data = array(
                    'UserId' => $this -> session -> userdata('userid'),
                    'ShoppingList' => $new_item_data,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                );
                $is_save = $this -> quickshoppinglistmodel -> save_list($this -> session -> userdata('userid'), $data);
                if ($is_save) {
                    $this -> result = 1;
                    $this -> message = $product_price;
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to add item to the list';
                }
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