<?php

/*
 * Author:PHN
 * Purpose: Base Controller
 * Date:26-08-2015
 */

class My_Controller extends CI_Controller {

    public $breadcrumbs = array();
    public $page_title;

    function __construct() {
        parent::__construct();

        $this -> load -> library('Datatables');
        $this -> load -> library('table');
        $this -> load -> helper('database');

        $this -> load -> model('admin/adminmodel');
        $this -> adminmodel -> load_menu();

        $this -> verify_login();

        $this -> verify_access();
        $this -> check_user_premium();
    }

    function verify_login() {
        if ($this -> session -> userdata('user_id')) {
            return true;
        }
        else {
            redirect('home', 'refresh');
        }
    }
    /*
     * Purpose: Set common data for pages
     * Param1 : title ( Page title )
     * Param2 : breadcrumbs ( Breadcrumbs on page )
     * Param3 : message ( Success message if any )
     */

    function set_site_data($title, $breadcrumbs = array(), $message = array()) {
        $data = array(
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'message' => $message
        );

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
        elseif ($folder_name == 'specials') {
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
    /*
     * Purpose: Upload images
     * Param1 : field name
     * Param2 : folder_name
     */

    function do_upload_slider($field_name, $folder_name, $resize_images = 0) {
        $this -> load -> library('image_lib');

        if ($folder_name == 'categories' || $folder_name == 'products') {
            $config['upload_path'] = './assets/images/' . $folder_name . '/large';
        }
        else {
            $config['upload_path'] = './assets/images/' . $folder_name;
        }
        $config['encrypt_name'] = TRUE;

        $config['allowed_types'] = 'gif|jpg|png|jpeg';

        $max_width = constant(strtoupper($folder_name) . '_WIDTH');

        $max_height = constant(strtoupper($folder_name) . '_HEIGHT');

        if ($max_width && $max_height) {

            $config['max_width'] = constant(strtoupper($folder_name) . '_WIDTH');


            $config['max_height'] = constant(strtoupper($folder_name) . '_HEIGHT');
        }

        $medium_path = './assets/images/' . $folder_name . '/medium';
        $thumb_path = './assets/images/' . $folder_name . '/thumb';

        $this -> load -> library('upload', $config);
        $result = $this -> upload -> do_upload($field_name);

        $data = $this -> upload -> data();
        if ($max_width && $max_height) {
            if ($data['image_width'] != $config['max_width'] && $data['image_height'] != $config['max_height']) {
                $error = array('error' => "Please upload a " . $config['max_width'] . "*" . $config['max_height'] . " image");

                return $error;
            }
        }

        if (!$result) {
            $error = array('error' => $this -> upload -> display_errors());

            return $error;
        }
        else {
            $data = array('upload_data' => $this -> upload -> data());

            if ($resize_images == 1) {
                //Medium
                $config = array(
                    'source_image' => $data['upload_data']['full_path'], //path to the uploaded image
                    'new_image' => $medium_path, //path to new medium image
                    'maintain_ratio' => true,
                    'width' => 300,
                    'height' => 300
                );

                $this -> image_lib -> initialize($config);
                $this -> image_lib -> resize();

                //Medium
                $config = array(
                    'source_image' => $data['upload_data']['full_path'], //path to the uploaded image
                    'new_image' => $thumb_path, //path to new thumb image
                    'maintain_ratio' => true,
                    'width' => 50,
                    'height' => 50
                );

                $this -> image_lib -> initialize($config);
                $this -> image_lib -> resize();
            }

            return $data;
        }
    }
    /*
     * Purpose: Upload files for importing data
     * Param1 : field name
     * Param2 : folder_name
     */

    function do_upload_file($field_name, $file_type = '') {
        $config['upload_path'] = ( $file_type == '' ) ? './assets/admin/import_files/' : './assets/images/products/large/';
        $config['allowed_types'] = ( $file_type == '' ) ? 'xls|xlsx' : 'zip|zipx';

        $this -> load -> library('upload');
        $this -> upload -> initialize($config);

        if (!$this -> upload -> do_upload($field_name)) {
            $error = array('error' => $this -> upload -> display_errors());

            return $error;
        }
        else {
            $data = array('upload_data' => $this -> upload -> data());
            return $data;
        }
    }

    function verify_access() {

        $menu = "/" . $this -> uri -> segment(2);
        $menu_page_arr = [];
        if(!empty($this->session->userdata('menu_page_arr'))){
            $menu_page_arr = $this->session->userdata('menu_page_arr');
        }
        if (in_array($menu, $menu_page_arr) || $menu == "/account" || $menu == "/mailbox" || $menu == "/storeformat" || $menu == '/insights') {
            return true;
        }
        else {
            redirect('home', 'refresh');
        }
    }

    function validateName($name) {
        $this -> form_validation -> set_message('validateName', 'Name must contain contain only letters, apostrophe, spaces or dashes.');
        if (preg_match('/^[a-z\'\-\s]+$/', $name)) {
            return true;
        }
        else {
            return false;
        }
    }

    function validateCity($city) {
        $this -> form_validation -> set_message('validateCity', 'City must contain only letters and spaces.');
        if (preg_match('/^[a-z\s]+$/', $city)) {
            return true;
        }
        else {
            return false;
        }
    }

    function validateStreetaddress($street_address) {
        $this -> form_validation -> set_message('validateStreetaddress', 'Street Address must not contain special characters.');
        if (preg_match('/^[a-z0-9\'",\\\/\-\s]+$/', $street_address)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function send_user_notification($product_id) {

        $this -> load -> model('admin/productmodel', '', TRUE);

        $product = $this -> productmodel -> get_product_details($product_id);

        $product_name = $product['ProductName'];

        //Get product name
        $message = "The price of your favorite product " . $product_name . " has been changed.";

        //------------------- Price Alert Users----------------------
        $alert_users = $this -> productmodel -> get_price_alert_users($product_id);

        //------------------- Get Favorite Products Users----------------------
        $fav_users = $this -> productmodel -> get_favorite_users($product_id);

        //------------------- Get Wishlist Products Users----------------------
        $wishlist_users = $this -> productmodel -> get_wishlist_users($product_id);


        $users = ( array_merge($alert_users, $fav_users) );
        $users = ( array_merge($wishlist_users, $users) );
        $users = array_unique($users, SORT_REGULAR);

        $valid_users = array();
        //Add value to user notification table
        $this -> load -> model('admin/notificationmodel', '', TRUE);
        foreach ($users as $user) {

            $data = array(
                'UserId' => $user['UserId'],
                'Message' => $message,
                'CreatedOn' => Date('Y-m-d')
            );

            $this -> notificationmodel -> add_notification($data);

            //Send Notification to mobile devices using push
            if ($user['DeviceId'] && $user['DeviceType']) {
                $valid_users[] = $user;
            }
        }

        if (!empty($valid_users)) {
            //Send Notification.
            send_notification($message, $valid_users);
        }

        $this -> session -> set_userdata('success_message', "Special product added successfully");
    }

    public function check_wizard_navigation() {
        $this -> load -> model('admin/storemodel', '', TRUE);
        $navigation_details = $this -> storemodel -> get_wizard_steps();


        if ($navigation_details['Step1'] == 0) {
            redirect('home/dashboard', 'refresh');
        }
        elseif ($navigation_details['Step2'] == 0) {
            redirect('home/store/new', 'refresh');
        }
        elseif ($navigation_details['Step3'] == 0) {
            if ($this -> uri -> segment(3) == 'product_catalogue_inherit' || $this -> uri -> segment(3) == 'add' || $this -> uri -> segment(3) == 'product_catalogue' || $this -> uri -> segment(3) == 'get_products_by_category' || $this -> uri -> segment(3) == 'add_auto' || $this -> uri -> segment(3) == 'add_auto_catalogue' || $this -> uri -> segment(3) == 'get_add_count') {
                return true;
            }
            redirect('storeproducts/product_catalogue', 'refresh');
        }
        else {
            return true;
        }
    }
    
    public function check_user_premium(){
        if($this -> session -> userdata('user_type') == 6){
            $this -> load -> model('admin/storemodel', '', TRUE);
            $store_promotion_details = $this -> storemodel -> get_store_promos($this -> session -> userdata('user_store_id'));
            if($store_promotion_details['Premium'] == 1){
                $this -> session -> set_userdata('store_promotion_active', 'true');
            }
        }
    }
}
