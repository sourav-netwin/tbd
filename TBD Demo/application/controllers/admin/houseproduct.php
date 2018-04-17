<?php

/*
 * Author:AS
 * Purpose:House product controller - Retailer user login
 * Date:11-11-206
 * Dependency: houseproduct.php
 */

class Houseproduct extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/categorymodel', '', TRUE);
        $this -> load -> model('admin/productmodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);
        $this -> load -> model('admin/houseproductmodel', '', TRUE);
        $this -> load -> model('admin/storemodel', '', TRUE);

        $this -> page_title = "Add House Brands";
    }

    public function index($id = '') {
        $data['title'] = $this -> page_title;
        $this -> breadcrumbs[0] = array('label' => $this -> page_title, 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['main_categories'] = $data['parent_category'] = $data['sub_category'] = array();
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();
        $this -> template -> view('admin/house_product/index', $data);
    }

    public function datatable($main_parent_category_id = 0, $parent_category_id = 0, $sub_category_id = 0) {
        $retailer_id = $this -> session -> userdata('user_retailer_id');
        $this -> datatables -> select("products.ProductName as ProductName, products.ProductImage as ProductImage , products.Id as Id, products.IsActive AS active, categories.CategoryName as sub_category, parent_category.CategoryName AS parent_cat,main_parent_category.CategoryName AS main_parent_cat", FALSE)
            -> unset_column('ProductImage')
            -> unset_column('Id')
            -> unset_column('active')
            -> from('products')
            -> join('categories', 'categories.Id = products.CategoryId', 'left')
            -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId', 'left')
            -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId', 'left')
            -> where('HouseId', $retailer_id)
            -> add_column('Actions', get_house_action_buttons('$1', 'houseproduct'), 'Id');

        $cond = array('products.IsRemoved' => '0');
        if ($main_parent_category_id != 0 || $parent_category_id != 0 || $sub_category_id != 0) {
            if ($main_parent_category_id != 0)
                $cond = $cond + array("main_parent_category.Id" => $main_parent_category_id);
            if ($parent_category_id != 0)
                $cond = $cond + array("parent_category.Id" => $parent_category_id);
            if ($sub_category_id != 0)
                $cond = $cond + array("categories.Id" => $sub_category_id);
        }
        $this -> datatables -> where($cond);


        echo $this -> datatables -> generate();
    }

    public function get_category_listing() {
        $category_id = $this -> input -> post("id");
        $category_type = $this -> input -> post("type");

        $parent_category = $sub_category = '';
        if ($category_type == 'product_main_category')
            $parent_category .= '<option value="">Select Category</option>';
        else
            $parent_category .= '<option value="">Select Sub Category</option>';

        if ($category_id != 0) {
            $categories = $this -> categorymodel -> get_parent_categories($category_id);
            foreach ($categories as $category) {
                $parent_category .= '<option value="' . $category['Id'] . '">' . $category['CategoryName'] . '</option>';
            }
        }

        if ($category_type == 'product_main_category') {
            $sub_category .= '<option value="">Select Sub Category</option>';
        }

        echo json_encode(array('parent_category' => $parent_category, 'sub_category' => $sub_category));
    }

    public function add() {

        $data['retailer_details'] = $this -> retailermodel -> get_retailer_details($this -> session -> userdata('user_retailer_id'));
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Add Product
            $this -> form_validation -> set_rules('product_main_category', 'main category', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_parent_category', 'category', 'trim|required|xss_clean');


            $this -> form_validation -> set_rules('product_name', 'Product name', 'trim|required|xss_clean|callback_check_uniqueness_by_product_name');
            $this -> form_validation -> set_rules('brand', 'brand', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_description', 'product description', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_rate', 'product rate', 'trim|required|numeric|xss_clean');


            if (!$this -> form_validation -> run() == FALSE) {

                $result = array();
                $image_path = "";

                //If image uploaded
                if (!empty($_FILES['product_image']['name'])) {
                    $result = $this -> do_upload('product_image', 'products', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));

                    $image_path = $result['upload_data']['file_name'];
                }

                if (!isset($result['error'])) {
                    $data = array(
                        'MainCategoryId' => $this -> input -> post('product_main_category'),
                        'ParentCategoryId' => $this -> input -> post('product_parent_category'),
                        'CategoryId' => $this -> input -> post('product_sub_category'),
                        'SKU' => $this -> input -> post('sku'),
                        'ProductName' => $this -> input -> post('product_name'),
                        'Brand' => $data['retailer_details']['CompanyName'],
                        'ProductDescription' => $this -> input -> post('product_description'),
                        'RRP' => number_format($this -> input -> post('product_rate'), 2),
                        'ProductImage' => $image_path,
                        'HouseId' => $this -> session -> userdata('user_retailer_id'), 
                        'CreatedBy' => $this -> session -> userdata('user_id'),
                        'CreatedOn' => date('Y-m-d H:i:s'),
                        'IsActive' => 1
                    );
                    $result = $this -> productmodel -> add_product($data);
                    if($result){
                        $store_details = $this -> storemodel -> get_stores($this -> session -> userdata('user_retailer_id'));
                        if($store_details){
                            $store_product_array = [];
                            foreach($store_details as $store){
                                $store_product_array[] = array(
                                    'ProductId' => $result,
                                    'RetailerId' => $store['RetailerId'],
                                    'StoreId' => $store['Id'],
                                    'StoreTypeId' => $store['StoreTypeId'],
                                    'PriceForAllStores' => '0',
                                    'Price' => number_format($this -> input -> post('product_rate'), 2),
                                    'CreatedBy' => $this -> session -> userdata('user_id'),
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'IsActive' => 1
                                );
                            }
                            if(!empty($store_product_array)){
                                $this -> storemodel -> add_product_batch($store_product_array);
                            }
                        }
                    }
                    $this -> session -> set_userdata('success_message', "Product added successfully");
                    redirect('houseproduct', 'refresh');
                }
                else {
                    // code to display error while image upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                }
            }
        }

        $this -> breadcrumbs[0] = array('label' => $this -> page_title, 'url' => 'houseproduct');
        $this -> breadcrumbs[1] = array('label' => 'Add Product', 'url' => '');

        $data['title'] = 'Add Product';

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['main_categories'] = $data['parent_category'] = $data['sub_category'] = array();
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();

        $this -> template -> view('admin/house_product/add', $data);
    }

    public function edit($id) {

        $data = $this -> productmodel -> get_product_details($id);

        $this -> breadcrumbs[] = array('label' => 'Edit Product', 'url' => 'houseproduct/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['main_categories'] = $data['parent_category'] = $data['sub_category'] = array();
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();

        if (!empty($data['main_categories'])) {
            $data['parent_category'] = $this -> categorymodel -> get_parent_categories($data['main_parent_cat_id']);

            $data['sub_category'] = $this -> categorymodel -> get_parent_categories($data['parent_cat_id']);
        }

        $this -> load -> view('admin/house_product/edit', $data);
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit Product

            $this -> form_validation -> set_rules('product_main_category', 'main category', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_parent_category', 'category', 'trim|required|xss_clean');


            $this -> form_validation -> set_rules('product_name', 'Product name', 'trim|required|xss_clean|callback_check_uniqueness_by_product_name_edit[' . $id . ']');
            $this -> form_validation -> set_rules('brand', 'brand', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_description', 'product description', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_rate', 'product rate', 'trim|required|numeric|xss_clean');

//            if (empty($_FILES['prod_image']['name']) && $this->input->post('old_product_image') == '') {
//               $this->form_validation->set_rules('prod_image', 'Document', 'callback_file_selected_check');
//            }

            if (!$this -> form_validation -> run() == FALSE) {
                if (!empty($_FILES['product_image']['name'])) {
                    $result = $this -> do_upload('product_image', 'products', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    if (!isset($result['error'])) {
                        $edit_data = array(
                            'MainCategoryId' => $this -> input -> post('product_main_category'),
                            'ParentCategoryId' => $this -> input -> post('product_parent_category'),
                            'CategoryId' => $this -> input -> post('product_sub_category'),
                            'SKU' => $this -> input -> post('sku'),
                            'ProductName' => $this -> input -> post('product_name'),
                            'ProductDescription' => $this -> input -> post('product_description'),
                            'RRP' => number_format($this -> input -> post('product_rate'), 2),
                            'ProductImage' => $result['upload_data']['file_name'],
                            'ModifiedBy' => $this -> session -> userdata('user_id'),
                            'ModifiedOn' => date('Y-m-d H:i:s')
                        );

                        $result = $this -> productmodel -> update_product($id, $edit_data);
                        $this -> session -> set_userdata('success_message', "Product updated successfully");
                        $this -> result = 1;
                    }
                    else {
                        // code to display error while image upload
                        //$this->session->set_userdata('error_message', $result['error']);
                        $this -> result = 0;
                        $this -> message = $result['error'];
                    }
                }
                else {
                    $edit_data = array(
                        'MainCategoryId' => $this -> input -> post('product_main_category'),
                        'ParentCategoryId' => $this -> input -> post('product_parent_category'),
                        'CategoryId' => $this -> input -> post('product_sub_category'),
                        'SKU' => $this -> input -> post('sku'),
                        'ProductName' => $this -> input -> post('product_name'),
                        'Brand' => $this -> input -> post('brand'),
                        'ProductDescription' => $this -> input -> post('product_description'),
                        'RRP' => number_format($this -> input -> post('product_rate'), 2),
                        'ModifiedBy' => $this -> session -> userdata('user_id'),
                        'ModifiedOn' => date('Y-m-d H:i:s')
                    );

                    $result = $this -> productmodel -> update_product($id, $edit_data);
                    $this -> session -> set_userdata('success_message', "Product updated successfully");
                    $this -> result = 1;
                }
            }
            else {
                $this -> result = 0;
                $this -> message = $this -> form_validation -> error_array();
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

    public function change_status($id, $status) {

        $this -> productmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Product status updated successfully");
        redirect('houseproduct', 'refresh');
    }

    public function delete($id) {

        $this -> productmodel -> delete_product($id);
        $this -> session -> set_userdata('success_message', "Product deleted successfully");
        redirect('houseproduct', 'refresh');
    }
}