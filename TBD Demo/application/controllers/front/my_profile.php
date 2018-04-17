<?php

/*
 * Author:  PN
 * Purpose: Account Controller
 * Date:    30-10-2015
 */

class My_Profile extends My_Front_Controller {

    function __construct() {
        parent::__construct();
        $this -> load -> model('front/usermodel', '', TRUE);
        if (!$this -> usermodel -> check_email_entered($this -> session -> userdata('userid'))) {
            redirect(front_url() . 'registration/set_email');
            exit(0);
        }
    }

    //Function to load default landing page for website
    public function index() {
        $this->load->model('front/contentmodel', '', TRUE);

        //Get active parent categories to be displayed that contain active not deleted products
        $data['categories'] = $this->categories;

        //Get all active categories to be displayed that contain active not deleted products
        $data['all_categories'] = $this->all_categories;

        // Get my basket data
        $data['user_basket'] = $this->usermodel->get_user_basket();

        // Get my basket count
        $data['user_basket_products_count'] = $this->usermodel->get_user_basket_products_count();

        // Get other retailers price
        $user_basket_other_retailer = $this->usermodel->get_user_basket_other_retailers();

        $array_retailer = $other_retailer = array();
        foreach ($user_basket_other_retailer as $value) {
            $array_retailer[$value['LogoImage']][] = $value['Price'];
        }
        foreach ($array_retailer as $key => $value) {
            $other_retailer[$key][] = array_sum($value);
            $other_retailer[$key][] = ( count($value) == $data['user_basket_products_count'] ? 1 : 0 );
        }

        $data['user_basket_other_retailer'] = $other_retailer;

        // get user details
        $data['user_details'] = $this->usermodel->get_user_details($this->session->userdata('userid'));
        
        $data['provinces'] = $this->usermodel->get_states();

        $this->template->front_view('front/profile', $data, 1);
    }

    public function get_favorites() {

        $this->load->model('front/productmodel', '', TRUE);
        $data['top_offers'] = $this->productmodel->get_favorite_products();
        echo json_encode(array('view' => $this->load->view('front/top_offer_products', $data, TRUE)));
    }

    public function get_pricealerts() {
        $this->load->model('front/productmodel', '', TRUE);

        $data['top_offers'] = $this->productmodel->get_alert_products();

        echo json_encode(array('view' => $this->load->view('front/top_offer_products', $data, TRUE)));
    }

    public function get_wishlists() {
        $this->load->model('front/userwishlistmodel', '', TRUE);

        $data['wishlists'] = $this->userwishlistmodel->get_user_wishlist_details();

        echo json_encode(array('view' => $this->load->view('front/wishlist', $data, TRUE)));
    }

     public function create_list() {
        $this->load->model('front/userwishlistmodel', '', TRUE);

        $new_list = $this->input->post('new_list');

        $insert_id = $this->userwishlistmodel->create_wishlist($new_list);

        redirect(front_url() . 'my_profile#wishlists');
    }

    public function delete_list($wishlist) {

        $this->load->model('front/userwishlistmodel', '', TRUE);

        $this->userwishlistmodel->delete_wishlist($wishlist);

        $data['wishlists'] = $this->userwishlistmodel->get_user_wishlist_details();

        echo json_encode(array('view' => $this->load->view('front/wishlist', $data, TRUE)));
    }

    public function get_wishlist_detail($wishlist_id) {

        $this->load->model('front/userwishlistmodel', '', TRUE);

        $data['wishlists'] = $this->userwishlistmodel->get_user_wishlist_details($wishlist_id);

        $data['wishlists_products'] = $this->userwishlistmodel->get_user_wishlist_products($wishlist_id);

        echo json_encode(array('view' => $this->load->view('front/wishlist_products', $data, TRUE)));
    }

    public function delete_wishlist_product($wishlist_id, $wishlist_product_id) {

        $this->load->model('front/userwishlistmodel', '', TRUE);

        $this->userwishlistmodel->delete_product_wishlist($wishlist_product_id);

        $data['wishlists'] = $this->userwishlistmodel->get_user_wishlist_details($wishlist_id);

        $data['wishlists_products'] = $this->userwishlistmodel->get_user_wishlist_products($wishlist_id);

        echo json_encode(array('view' => $this->load->view('front/wishlist_products', $data, TRUE)));
    }

    public function delete_favorites() {

        $this->load->model('front/productmodel', '', TRUE);

        $data['top_offers'] = $this->productmodel->delete_favorite_products();

        echo json_encode(array('view' => ""));
    }

    public function delete_pricealerts() {

        $this->load->model('front/productmodel', '', TRUE);

        $data['top_offers'] = $this->productmodel->delete_pricealerts_products();

        echo json_encode(array('view' => ""));
    }



    public function get_notification() {

        $this->load->model('front/notificationmodel', '', TRUE);

        $data['notifications'] = $this->notificationmodel->get_notifications();

        echo json_encode(array('view' => $this->load->view('front/notifications', $data, TRUE)));
    }

    public function delete_all_notifications() {
        $this->load->model('front/notificationmodel', '', TRUE);

        $this->notificationmodel->delete_all_notifications();

        $data['notifications'] = $this->notificationmodel->get_notifications();

        echo json_encode(array('view' => $this->load->view('front/notifications', $data, TRUE)));
    }

    public function delete_notification($notification_id) {
        $this->load->model('front/notificationmodel', '', TRUE);

        $this->notificationmodel->delete_notifications($notification_id);

        $data['notifications'] = $this->notificationmodel->get_notifications();

        echo json_encode(array('view' => $this->load->view('front/notifications', $data, TRUE)));
    }

}

?>
