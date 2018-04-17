<?php

/*
 * Author:  PM
 * Purpose: Top Offers Controller
 * Date:    14-10-2015
 */

class Topoffers extends My_Front_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
        $this -> load -> model('front/retailermodel');
        $this -> load -> model('front/productmodel');
        $this -> config -> load('topoffers');
        $this -> load -> model('front/usermodel', '', TRUE);
        if (!$this -> usermodel -> check_email_entered($this -> session -> userdata('userid'))) {
            redirect(front_url() . 'registration/set_email');
            exit(0);
        }
    }

    //Function to load home page after login
    public function index() {
        // Get product container data from common controller
        $data = $this -> get_product_container_data();

        // Get price range to be displayed for searching
        $data['price_range'] = $this -> productmodel -> get_price_range();

        $top_offer_data['location_preference'] = $this -> productmodel -> get_user_location_preferences($this -> session -> userdata('userid'));
        // Count of top offers to be displayed for that prefered retailer and store
        $top_offers_totals = $this -> productmodel -> get_top_offers_count();
        $top_offer_data['count_top_offers'] = count($top_offers_totals);

        // Top offers to be displayed for that prefered retailer and store
        $top_offer_products_data['top_offers'] = $this -> productmodel -> get_top_offers();
        
        $top_offer_data['top_offers'] = $this -> load -> view('front/top_offer_products', $top_offer_products_data, TRUE);

        $top_offer_data['user_preferred_retailer'] = $data['user_preferred_retailer'];
        $top_offer_data['retailers'] = $data['retailers'];
        $top_offer_data['all_retailers_count'] = $data['all_retailers_count'];
        $top_offer_data['last_special_product_id'] = ( (count($top_offer_products_data['top_offers']) - 1) > 0 ) ? $top_offer_products_data['top_offers'][(count($top_offer_products_data['top_offers']) - 1)]['SpecialId'] : 0;

        $data['product_list'] = $this -> load -> view('front/topoffers', $top_offer_data, TRUE);

        $data['is_top_offer'] = 1;
        
        $data['breadcrumbs'] = array(
            array(
                'name' => 'Top Offers',
                'url' => ''
            )
        );

        $this -> template -> front_view('front/products_container', $data, 1);
    }

    // Function to display retailers on top offers page
    public function get_retailers() {
        $limit = ( $this -> input -> post('display') == 'all' ) ? 0 : $this -> config -> item('top_offer_retailer_limit');

        //Get active retailers to be displayed that contain active not deleted store products
        $retailers = $this -> retailermodel -> get_retailers_having_store_products($limit);

        $html = '';
        if (!empty($retailers)) {
            foreach ($retailers as $retailer) {
                $html .= '<li>
                            <a href="javascript:void(0);" class="thumbnail small-thumb" data-retailer-id="'.$retailer['Id'].'">
                                <img src="' . front_url() . RETAILER_IMAGE_PATH .'small/'. $retailer['LogoImage'] . '" class="img-responsive">
                            </a>
                        </li>';
            }
        }

        echo $html;
    }
    /* Update user preference of retailer and store */

    public function update_user_preference() {
        $res = '';
        $retailer_id = $this -> input -> post('retailer_id');

        if ($retailer_id > 0) {
            $store_id = ( $this -> input -> post('store_id') ) ? $this -> input -> post('store_id') : $this -> retailermodel -> get_nearest_store($retailer_id);
            $data = array('RetailerId' => $retailer_id, 'StoreId' => $store_id);

            $res = $this -> usermodel -> update_user_preference($data);
        }

        echo $res;
    }

    // Search top offers based on price range provided
    public function search_products() {
        $price_range = $this -> input -> post('price_range');

        $data['top_offers'] = $this -> productmodel -> get_top_offers($price_range);

        $last_special_product_id = ( (count($data['top_offers']) - 1) > 0 ) ? $data['top_offers'][(count($data['top_offers']) - 1)]['SpecialId'] : 0;

        echo json_encode(array('view' => $this -> load -> view('front/top_offer_products', $data, TRUE), 'last_product' => $last_special_product_id));
    }

    // Mark/Unmark a product as favourite
    public function toggle_favourite() {
        $product_id = $this -> input -> post('product_id');
        $special_id = $this -> input -> post('special_id');
        $is_fav = $this -> input -> post('is_fav');

        $result = $this -> productmodel -> toggle_favourite($product_id, $special_id, $is_fav);

        echo $result;
    }

    // Show more products
    public function show_more_products() {
        $last_offer_product = $this -> input -> post('last_offer_product');

        // Count of top offers to be displayed for that prefered retailer and store
        $top_offers_totals = $this -> productmodel -> get_top_offers_count();
        $count_top_offers = count($top_offers_totals);

        $data['top_offers'] = $this -> productmodel -> get_top_offers(array(), $last_offer_product);

        $last_special_product_id = ( (count($data['top_offers']) - 1) > 0 ) ? $data['top_offers'][(count($data['top_offers']) - 1)]['SpecialId'] : 0;

        echo json_encode(array('view' => $this -> load -> view('front/top_offer_products', $data, TRUE), 'count' => $count_top_offers, 'last_product' => $last_special_product_id));
    }

    public function get_top_offer_by_distance() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            $distance = $this -> input -> post('dist_range');

            // Get product container data from common controller
            $data = $this -> get_product_container_data_by_location($distance);

            // Get price range to be displayed for searching
            $data['price_range'] = $this -> productmodel -> get_price_range();

            // Count of top offers to be displayed for that prefered retailer and store
            $top_offers_totals = $this -> productmodel -> get_top_offers_count();
            $top_offer_data['count_top_offers'] = count($top_offers_totals);

            // Top offers to be displayed for that prefered retailer and store
            $top_offer_products_data['top_offers'] = $this -> productmodel -> get_top_offers();
            $top_offer_data['top_offers'] = $this -> load -> view('front/top_offer_products', $top_offer_products_data, TRUE);

            $top_offer_data['user_preferred_retailer'] = $data['user_preferred_retailer'];
            $top_offer_data['retailers'] = $data['retailers'];
            $top_offer_data['all_retailers_count'] = $data['all_retailers_count'];
            $top_offer_data['last_special_product_id'] = ( (count($top_offer_products_data['top_offers']) - 1) > 0 ) ? $top_offer_products_data['top_offers'][(count($top_offer_products_data['top_offers']) - 1)]['SpecialId'] : 0;

            $html = '<div class="manage_content">
        <h2 class="grey_heading">Top Offers <small>at ' . $top_offer_data['user_preferred_retailer'] -> CompanyName . ' (' . $top_offer_data['count_top_offers'] . ')</small></h2>
        <div class="panel panel-offer">
            <div class="panel-heading">Todays</div>
            <div class="panel-body">
                <div class="offer_head">
                    <span>TOP</span>  OFFERS
                </div>
                <input type="hidden" id="init_dist" value="' . $data['location_preference'][0]['PrefDistance'] . '" />';
            if (!empty($top_offer_data['retailers'])) {
                $html .= '<div class="subtitle">from across ' . $top_offer_data['all_retailers_count'] . ' Supermarkets</div>
                    <ul class="store" id="top_offer_retailers">';
                foreach ($top_offer_data['retailers'] as $retailer) {
                    $html .= '<li>
                                    <a href="javascript:void(0);" class="thumbnail" data-retailer-id="' . $retailer['Id'] . '>">
                                       <img src="' . front_url() . RETAILER_IMAGE_PATH . 'small/' . $retailer['LogoImage'] . '" class="img-responsive">
                                    </a>
                                </li>';
                }
                $html .= '</ul>';
                if (count($top_offer_data['retailers']) > $this -> config -> item('top_offer_retailer_limit')) {
                    $html .= '<div class="subtitle">
                                <a href="javascript:void(0);" id="all_supermarkets" data-display="all">See all supermarkets</a>
                            </div>';
                }
            }
            $html .= '</div>
        </div>
        <div class="prd_list_wrap special_offer">
            <div class="row">';
            if (!empty($top_offer_data['top_offers'])) {
                $html .= $top_offer_data['top_offers'];
            }
            else {
                $html .= '<small>No special offer available for now at  ' . $top_offer_data['user_preferred_retailer'] -> CompanyName . '</small>';
            }

            $html .= '</div>
        </div>';
            if ($count_top_offers > $this -> config -> item('top_offer_product_limit')) {
                $html .= '<div class="text-center">
                <h4>
                    <a href="javascript:void(0);" id="show_more">Show more</a>
                </h4>
            </div>';
            }
            $html .= '<input type="hidden" name="last_offer_product" id="last_offer_product" value="' . $top_offer_data['last_special_product_id'] . '">
    </div>';
            
            $this -> result = 1;
            $this -> message = $html;
            //$data['product_list'] = $this -> load -> view('front/topoffers', $top_offer_data, TRUE);
            //$data['is_top_offer'] = 1;
            //$this -> template -> front_view('front/products_container', $data, 1);
        }
        else{
            $this -> result = 0;
            $this -> message = 'Invalid Data';
        }
        
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
}
