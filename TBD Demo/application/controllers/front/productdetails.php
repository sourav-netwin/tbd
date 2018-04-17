<?php

/*
 * Author:  PM
 * Purpose: Products Details Controller
 * Date:    20-10-2015
 */

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
class Productdetails extends My_Front_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('front/productmodel');
        $this -> load -> model('front/userwishlistmodel');
        $this -> load -> model('front/usermodel', '', TRUE);
        if (!$this -> usermodel -> check_email_entered($this -> session -> userdata('userid'))) {
            redirect(front_url() . 'registration/set_email');
            exit(0);
        }
    }

    //Function to load product details
    public function index($product_name, $product_id,$special_get = '') {
        if ($product_name != '' && $product_id != '') {
            $product_id = $this -> encrypt -> decode($product_id);
            $product_id = $product_id ? $product_id : 0;
            $product_name = decode_per(urldecode($product_name));
            $this -> config -> load('productdetails');
            $special_id = '';
            if(trim($special_get) != ''){
                $special_id = $this -> encrypt -> decode($special_get);
            }
            
            // Get product container data from common controller
            $data = $this -> get_product_container_data();

            $user_preference = $this -> productmodel -> get_user_preference($this -> session -> userdata('userid'));
            $store_id = $user_preference['StoreId'];
            $retailer_id = $user_preference['RetailerId'];
            $store_type_id = $user_preference['StoreTypeId'];

            // Get product details
            $data['product_details'] = $this -> productmodel -> get_product_details($product_name, $product_id, $special_id);
            
            $store_deatails = $this -> productmodel -> get_store_details($data['product_details']['StoreId']);
            $data['user_wishlist'] = $this -> userwishlistmodel -> get_user_wish_list();
            $data['preferances'] = $this -> productmodel -> get_user_location_preferences($this -> session -> userdata('userid'));
            $data['product_comparison'] = $this -> productmodel -> compare_product($product_id);
            $data['product_reviews'] = $this -> productmodel -> get_product_reviews($product_id);
            $data['product_name'] = $product_name;
            $data['product_views'] = $this -> productmodel -> get_product_views($product_id);
            $data['product_views'] = $data['product_views']['count'] ? $data['product_views']['count'] : 0;
            $data['product_shares'] = $this -> productmodel -> get_product_shares($product_id);
            $data['product_shares'] = $data['product_shares']['count'] ? $data['product_shares']['count'] : 0;
            $data['store_details'] = [];

            $data['related_products'] = $this -> _related_products($product_id, $retailer_id, $store_type_id, $store_id, $this -> session -> userdata('userid'));

//            echo '<pre>';
//            print_r($data['related_products']);die;

            $dayNames = array(
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday',
            );

            if ($store_deatails) {
                $cnt = 0;
                $time_cnt = 0;
                foreach ($store_deatails as $product) {
                    if ($cnt == 0) {
                        $data['store_details']['StoreName'] = $product['StoreName'];
                        $data['store_details']['Latitude'] = $product['Latitude'];
                        $data['store_details']['Longitude'] = $product['Longitude'];
                        $data['store_details']['StreetAddress'] = $product['StreetAddress'];
                        $data['store_details']['Zip'] = $product['Zip'];
                        $data['store_details']['ContactPersonNumber'] = $product['ContactPersonNumber'];
                        $data['store_details']['StateName'] = $product['StateName'];
                        $data['store_details']['LogoImage'] = $product['LogoImage'] == '' ? '' : front_url() . RETAILER_IMAGE_PATH . 'small/' . $product['LogoImage'];
                        $data['store_details']['map_url'] = 'javascript:void(0)';
                        if ($product['Latitude'] && $product['Longitude']) {
                            $data['store_details']['map_url'] = 'http://maps.google.com?q=' . $product['Latitude'] . ',' . $product['Longitude'];
                        }
                        if (date('N', strtotime(date("Y/m/d"))) == $product['OpenCloseDay']) {
                            $time_arr = explode('-', $product['OpenCloseTimeFrom']);
                            $data['store_details']['StoreTime'] = array(
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
                            $data['store_details']['StoreTime'] = array(
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
                    $data['store_details']['StoreTime'] = array(
                        'Day' => '',
                        'OpenTime' => '',
                        'CloseTime' => ''
                    );
                }
            }


            $data['breadcrumbs'] = array(
                array(
                    'name' => $data['product_details']['main_cat_name'],
                    'url' => ''
                ),
                array(
                    'name' => $data['product_details']['parent_cat_name'],
                    'url' => front_url() . 'productslist/index/parent/' . $this -> encrypt -> encode($data['product_details']['parent_cat_id'])
                ),
                array(
                    'name' => $data['product_details']['cat_name'],
                    'url' => front_url() . 'productslist/index/sub/' . $this -> encrypt -> encode($data['product_details']['cat_id'])
                ),
                array(
                    'name' => $product_name,
                    'url' => ''
                )
            );
            $data['product_details']['special_id_get'] = $special_id;            
            //Increase the product view counter
            $this -> productmodel -> add_product_view($product_id, $this -> session -> userdata('userid'));

            $this -> template -> front_view('front/product_details', $data, 1);
        }
        else {
            redirect('productslist');
        }
    }

    // Function to add product to user wishlist if exists, else create a new and add to it
    public function add_to_list() { 
        
        $existing_list = $this -> input -> post('existing_list');
        $new_list = $this -> input -> post('new_list');        
        $product_id = $this -> input -> post('product');
        $product_name = $this -> input -> post('product_name');
        $product_price = $this -> input -> post('product_price');
        $product_special_price = $this -> input -> post('product_special_price');
        $special_id = $this -> input -> post('special_id');
        
        $user_preference = $this -> productmodel -> get_user_preference($this -> session -> userdata('userid'));
        $store_id = $user_preference['StoreId'];
        $retailer_id = $user_preference['RetailerId'];

        $ins_data = array('UserId' => $this -> session -> userdata('userid'), 'SpecialId' => $special_id,  'ProductId' => $product_id, 'RetailerId' => $retailer_id, 'StoreId' => $store_id, 'Price' => $product_price, 'OfferPrice' => $product_special_price, 'CreatedOn' => date('Y-m-d H:i:s'));

        // Add to existing list
        if ($existing_list > 0) {
            $ins_data['UserWishlistId'] = $existing_list;
        }
        else if ($new_list != '') { // Create new and then add to it
            $insert_id = $this -> userwishlistmodel -> create_wishlist($new_list);

            $ins_data['UserWishlistId'] = $insert_id;
        }
        $result = $this -> userwishlistmodel -> add_to_wishlist($ins_data);

        if ($result == 'duplicate') {
            $this -> session -> set_userdata('success_message', 'Product already added to this wishlist');
        }
        else if ($result > 0) {
            $this -> session -> set_userdata('success_message', 'Product added to wishlist successfully');
        }
        else {
            $this -> session -> set_userdata('success_message', 'Error while adding product to wishlist');
        }
        //redirect(front_url() . 'productdetails/' . urlencode($product_name) . '/' . $this -> encrypt -> encode($product_id));
        redirect(front_url() . 'productdetails/' . urlencode($product_name) . '/' . $this -> encrypt -> encode($product_id) . '/' . $this -> encrypt -> encode($special_id));
    }

    public function toggle_price_alert() {
        $price_alert = $this -> input -> post('price_alert');
        $product_id = $this -> input -> post('product_id');

        $data = array('UserId' => $this -> session -> userdata('userid'), 'ProductId' => $product_id);

        // Add price alert
        if ($price_alert == 0) {
            $data['CreatedOn'] = date("Y-m-d H:i:s");
            $result = $this -> productmodel -> add_price_alert($data);
            if ($result > 0) {
                echo "success";
            }
        }
        else { // Remove price alert
            $this -> productmodel -> remove_price_alert($data);
            echo "success";
        }
    }

    // Add user review and rating
    public function add_review() {
        $review = $this -> input -> post('review');
        $rating = $this -> input -> post('rating');
        $product_id = $this -> input -> post('product_id');

        $result = $this -> productmodel -> add_review_rating($product_id, $review, $rating);
        $html = '';
        if ($result) {
            $user_details = $this -> usermodel -> get_user_details($this -> session -> userdata('userid'));
            $image = front_url() . USER_IMAGE_PATH . $user_details['ProfileImage'];
            if (!file_exists($image))
                $image = front_url() . USER_IMAGE_PATH . 'small/default.gif';

            $mtime = date("c", time()); // Converts to date formate 2013-06-19T03:30:13+00:00

            $html = '<div>
                        <div class="media">
                            <div class="media-left">
                                <a href="#">
                                    <img class="media-object" src="' . $image . '" alt="User Image">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    ' . $user_details['FirstName'] . " " . $user_details['LastName'] . '
                                    <div class="ratings">
                                        <span data-score="' . $rating . '" class="review_rating"></span>
                                    </div>
                                </h4>
                                ' . $review . '
                                <div class="time_sec timeago" title="' . $mtime . '"></div>
                            </div>
                        </div>
                    </div>';
        }
        echo $html;
    }

    // Add product to basket
    public function add_to_basket() {
        
        $success = $error = '';
        $special_id = $this -> input -> post('special_id');
        $product_id = $this -> input -> post('product');
        $count = $this -> input -> post('count');

        if (!is_numeric($count)) {
            $count = 1;
        }
        elseif ($count > 10) {
            $count = 10;
        }
        elseif ($count <= 0) {
            $count = 1;
        }
        $count = floor($count);

        $result = $this -> productmodel -> add_to_basket($special_id, $product_id, $count);
        if ($result == 'duplicate') {
            $error = "Product already added to basket";
        }
        else if ($result > 0) {
            $success = "Product added to basket successfully";
        }
        else {
            $error = "Error while adding product to basket";
        }

        echo json_encode(array("success" => $success, "error" => $error));
    }

    function compare_product_user() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> config -> load('productdetails');
            $distance = $this -> input -> post('dist_range');
            $product_id = $this -> input -> post('prodId');
            $data = array(
                'dist' => $distance,
                'prodId' => $product_id
            );
            $result = $this -> productmodel -> compare_product_user($data);
            if (isset($result[0])) {
                $html = '<div class="comp_wrap">';
                $i = 0;
                foreach ($result as $product) {
                    $html .= '<div class="product_comparison_container row';
                    ( ($i % 2) != 0 ) ? $html .= 'grey-bg' : $html .= '';
                    $html .= '"';
                    if (( $i + 1 ) > $this -> config -> item('product_detail_comparison_limit')) {
                        $html .= 'style=display:none;';
                    }
                    $html .= '><div class="col-sm-6">
                            <img src="';
                    $html .= front_url() . RETAILER_IMAGE_PATH . 'small/' . $product['LogoImage'];
                    $html .='" class="img-responsive">
                        </div>';
                    if ($product['SpecialQty'] != '' && $product['SpecialPrice'] != '') {
                        $html .= '<div class="col-sm-6 prd_price discount">
                                    <span class="label label-danger special_label">Specials</span>&nbsp;
                                    <span class="strikout">R' . $product['Price'] . '</span> <span class="new_price">' . $product['SpecialQty'] . ' for R' . $product['SpecialPrice'] . '</span>
                                </div>';
                    }
                    else {
                        $html .= '<div class="col-sm-6">
                                        <span class="">R' . $product['Price'] . '</span>
                                    </div>';
                    }
                    $html .= '</div>';
                    $i++;
                }
                $html .= '</div>';
                if (count($product_comparison) > $this -> config -> item('product_detail_comparison_limit')) {
                    $html .= '<a href="javascript:void(0);" class="btn btn-block btn-blue mt-10" id="compare_all">Compare all Stores(' . count($product_comparison) . ')</a>';
                }
                $this -> result = 1;
                $this -> message = $html;
            }
            else {
                $this -> result = 0;
                $this -> message = 'No shops found in this region';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid Data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_chart_details() {
        $day = '';
        $price = '';
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $product_id = $this -> input -> post('prodId');
            $chart_array = $this -> productmodel -> get_chart_details($product_id);
            $day_arr = array();
            $price_arr = array();
            $store_arr = array();
            if ($chart_array) {
                $cnt = 1;
                foreach ($chart_array as $chart) {
                    if ($cnt == 1) {
                        $day_arr[] = date('d-M', strtotime($chart['CreatedOn']));
                        $price_arr[] = format_decimal($chart['RRP']);
                        $store_arr[] = array(
                            'retailer' => $chart['CompanyName'],
                            'store' => ''
                        );
                    }
                    $day_arr[] = $chart['day_month'];
                    $price_arr[] = format_decimal($chart['SpecialPrice']);
                    $store_arr[] = array(
                        'retailer' => $chart['CompanyName'],
                        'store' => $chart['StoreName']
                    );
                    $cnt++;
                }
                $this -> result = 1;
                $day = $day_arr;
                $price = $price_arr;
            }
            else {
                $this -> result = 1;
                $this -> message = 'No records found';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid Data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
            'day' => $day,
            'price' => $price,
            'store' => $store_arr
        ));
    }

    public function add_share_count() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $user_preference = $this -> productmodel -> get_user_preference($this -> session -> userdata('userid'));
            $store_id = $user_preference['StoreId'];
            $retailer_id = $user_preference['RetailerId'];
            $product_id = $this -> input -> post('prod_id');
            if ($product_id) {
                $insert_data = array(
                    'ProductId' => $product_id,
                    'RetailerId' => $retailer_id,
                    'StoreId' => $store_id,
                    'UserId' => $this -> session -> userdata('userid'),
                    'ShareFrom' => 'W'
                );
                $this -> productmodel -> insert_share_details($insert_data);
            }
            $share_count = $this -> productmodel -> get_product_shares($product_id);
            $share_count = $share_count['count'] ? $share_count['count'] : 0;
            $this -> result = 1;
            $this -> message = $share_count;
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid request';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function _related_products($product_id, $retailer_id, $store_type_id, $store_id, $user_id) {
//        $product_id = $this -> post('product_id') ? $this -> post('product_id') : "";
//        $retailer_id = $this -> post('retailer_id') ? $this -> post('retailer_id') : "";
//        $store_type_id = $this -> post('store_type_id') ? $this -> post('store_type_id') : "";
//        $store_id = $this -> post('store_id') ? $this -> post('store_id') : "";
//        $user_id = $this -> post('user_id') ? $this -> post('user_id') : "";

        $category_details = $this -> productmodel -> get_categories($product_id);
        if ($category_details) {
            $related_products = $this -> productmodel -> get_related_products($retailer_id, $store_type_id, $store_id, $product_id, $category_details['MainCategoryId'], $category_details['ParentCategoryId'], $category_details['CategoryId'], $user_id);
            if ($related_products[0]) {
                $i = 0;
                $total_price = 0;
                foreach ($related_products as $product) {
                    if ($product['ProductImage'])
                        $related_products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . $product['ProductImage']);
                    else
                        $related_products[$i]['ProductImage'] = (front_url() . PRODUCT_IMAGE_PATH . "medium/" . DEFAULT_PRODUCT_IMAGE_NAME);

                    if ($product['SpecialQty'] == NULL && $product['SpecialPrice'] == NULL) {
                        $related_products[$i]['SpecialQty'] = "0";
                        $related_products[$i]['SpecialPrice'] = "0";
                    }

                    $total_price = $total_price + $product['store_price'];
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
}
