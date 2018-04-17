<?php

/*
 * Author:PM
 * Purpose:Products Controller
 * Date:05-09-2015
 * Dependency: productmodel.php
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
class Products extends My_Controller {

    private $message;
    private $result;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/productmodel', '', TRUE);
        $this -> load -> model('admin/categorymodel', '', TRUE);

        $this -> page_title = "Product Catalogue";
        $this -> breadcrumbs[] = array('label' => 'Product Catalogue', 'url' => '/products');

        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    public function index() {

        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'Product Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Product Catalogue', 'url' => '/products');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['main_categories'] = $data['parent_category'] = $data['sub_category'] = array();
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();

        $this -> template -> view('admin/products/index', $data);
    }

    public function datatable_old($main_parent_category_id = 0, $parent_category_id = 0, $sub_category_id = 0) {
        $this -> datatables -> select("case when products.HouseId is null then products.ProductName else concat(retailers.CompanyName,' ',products.ProductName) end as ProductName, products.ProductImage as ProductImage , products.Id as Id, products.IsActive AS active, categories.CategoryName as sub_category, parent_category.CategoryName AS parent_cat,main_parent_category.CategoryName AS main_parent_cat", FALSE)
            -> unset_column('ProductImage')
            -> unset_column('Id')
            -> unset_column('active')
            -> from('products')
            -> join('categories', 'categories.Id = products.CategoryId and categories.IsActive = 1 and categories.IsRemoved = 0', 'left')
            -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId and parent_category.IsActive = 1 and parent_category.IsRemoved = 0')
            -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId and main_parent_category.IsActive = 1 and main_parent_category.IsRemoved = 0')
            -> join('retailers', 'retailers.Id = products.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0', 'left')
            -> add_column('Actions', get_action_buttons('$1', 'products'), 'Id');

        $cond = array(
            'products.IsRemoved' => '0',
            'products.IsActive' => '1'
            );
        if ($main_parent_category_id != 0 || $parent_category_id != 0 || $sub_category_id != 0) {
            if ($main_parent_category_id != 0)
                $cond = $cond + array("main_parent_category.Id" => $main_parent_category_id);
            if ($parent_category_id != 0)
                $cond = $cond + array("parent_category.Id" => $parent_category_id);
            if ($sub_category_id != 0)
                $cond = $cond + array("categories.Id" => $sub_category_id);
        }
        if($this -> session -> userdata('user_type') >= 3){
            $this -> datatables -> where('(products.HouseId is null or products.HouseId = "" or products.HouseId = ' . $this -> session -> userdata('user_retailer_id') . ')');
        }
        $this -> datatables -> where($cond);


        echo $this -> datatables -> generate();
    }
    
    public function datatable($main_parent_category_id = 0, $parent_category_id = 0, $sub_category_id = 0) {
        
        # Set default values 
        $productNameCond = $categoryNameCond = $parentCategoryNameCond = $mainCategoryNameCond = "";
        
        # Get the text to be searched 
        $requestData        = $_REQUEST;
        $searchText         = $requestData['sSearch'];
        
        # Separate each word and exclude spaces 
        $tempSearchTextArr  = explode(' ',$searchText);
        $searchTextArr = array();
        foreach ($tempSearchTextArr as $row) {
            if (trim($row) != "")
            {
                $searchTextArr[] = $row;
            }
        }
        $searchWordsCounter = count($searchTextArr); 
        
        /*
        # Search by productName, categoryName, parentCategoryName and mainCategoryName        
        if($searchText)
        {
            $index=1;
            $searchCond ="(";
            foreach($searchTextArr as $single)
            {
                # Product Name search 
                $productNameCond = $productNameCond . " products.ProductName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $productNameCond = $productNameCond. " or";
                }

                # Category Name search 
                if($index  == 1 ){
                    $categoryNameCond = $categoryNameCond. " or";
                }
                $categoryNameCond = $categoryNameCond . " categories.CategoryName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $categoryNameCond = $categoryNameCond. " or";
                }

                # Parent Category Name search 
                if($index  == 1 ){
                    $parentCategoryNameCond = $parentCategoryNameCond. " or";
                }
                $parentCategoryNameCond = $parentCategoryNameCond . " parent_category.CategoryName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $parentCategoryNameCond = $parentCategoryNameCond. " or";
                }

                # Main Category Name search 
                if($index  == 1 ){
                    $mainCategoryNameCond = $mainCategoryNameCond. " or";
                }
                $mainCategoryNameCond = $mainCategoryNameCond . " main_parent_category.CategoryName LIKE '%".$single."%' ";
                if($index < $searchWordsCounter){
                    $mainCategoryNameCond = $mainCategoryNameCond. " or";
                }
                $index++;
            }
            $searchCond = $searchCond.$productNameCond.$categoryNameCond.$parentCategoryNameCond.$mainCategoryNameCond.")";        
        }         
         */
        
         # Search by productName, categoryName, parentCategoryName and mainCategoryName        
        if($searchText)
        {
            $searchCond ="(";
            $productNameCond = $productNameCond . " products.ProductName LIKE '%".$searchText."%' ";
            $productNameCond = $productNameCond . " or products.ProductDescription LIKE '%".$searchText."%' ";
            $categoryNameCond = $categoryNameCond . " or categories.CategoryName LIKE '%".$searchText."%' ";
            $parentCategoryNameCond = $parentCategoryNameCond . " or parent_category.CategoryName LIKE '%".$searchText."%' ";
            $mainCategoryNameCond = $mainCategoryNameCond . " or main_parent_category.CategoryName LIKE '%".$searchText."%' ";
            //$searchCond = $searchCond.$productNameCond.$categoryNameCond.$parentCategoryNameCond.$mainCategoryNameCond.")";  
            $searchCond = $searchCond.$productNameCond.")"; 
        }
                
        //$this -> datatables -> select("case when products.HouseId is null then products.ProductName else concat(retailers.CompanyName,' ',products.ProductName) end as ProductName, products.ProductImage as ProductImage , products.Id as Id, products.IsActive AS active, categories.CategoryName as sub_category, parent_category.CategoryName AS parent_cat,main_parent_category.CategoryName AS main_parent_cat,products.RRP", FALSE)
        $this -> datatables -> select("case when products.HouseId is null then products.ProductDescription else concat(retailers.CompanyName,' ',products.ProductName) end as ProductName, products.ProductImage as ProductImage , products.Id as Id, products.IsActive AS active, categories.CategoryName as sub_category, parent_category.CategoryName AS parent_cat,main_parent_category.CategoryName AS main_parent_cat,products.RRP", FALSE)
            -> unset_column('ProductImage')
            -> unset_column('Id')
            -> unset_column('active')
            -> from('products')
            -> join('categories', 'categories.Id = products.CategoryId and categories.IsActive = 1 and categories.IsRemoved = 0', 'left')
            -> join('categories parent_category', 'parent_category.Id = products.ParentCategoryId and parent_category.IsActive = 1 and parent_category.IsRemoved = 0')
            -> join('categories main_parent_category', 'main_parent_category.Id = products.MainCategoryId and main_parent_category.IsActive = 1 and main_parent_category.IsRemoved = 0')
            -> join('retailers', 'retailers.Id = products.HouseId and retailers.IsActive = 1 and retailers.IsRemoved = 0', 'left')
            -> add_column('Actions', get_action_buttons('$1', 'products'), 'Id');

        $cond = array(
            'products.IsRemoved' => '0',
            'products.IsActive' => '1'
            );
        if ($main_parent_category_id != 0 || $parent_category_id != 0 || $sub_category_id != 0) {
            if ($main_parent_category_id != 0)
                $cond = $cond + array("main_parent_category.Id" => $main_parent_category_id);
            if ($parent_category_id != 0)
                $cond = $cond + array("parent_category.Id" => $parent_category_id);
            if ($sub_category_id != 0)
                $cond = $cond + array("categories.Id" => $sub_category_id);
        }
        if($this -> session -> userdata('user_type') >= 3){
            $this -> datatables -> where('(products.HouseId is null or products.HouseId = "" or products.HouseId = ' . $this -> session -> userdata('user_retailer_id') . ')');
        }
        $this -> datatables -> where($cond);
        
        # Added adanced search condition
        if($searchText)
        {
            $this -> datatables -> where($searchCond);
        }

        echo $this -> datatables -> generate();
    }

    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Add Product
            $this -> form_validation -> set_rules('product_main_category', 'main category', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_parent_category', 'category', 'trim|required|xss_clean');


            //$this -> form_validation -> set_rules('product_name', 'Product name', 'trim|required|xss_clean|callback_check_uniqueness_by_product_name');
            
            $this -> form_validation -> set_rules('product_name', 'Product name', 'trim|required|xss_clean');
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
                        'Brand' => $this -> input -> post('brand'),
                        'ProductDescription' => $this -> input -> post('product_description'),
                        'RRP' => number_format($this -> input -> post('product_rate'), 2),
                        'ProductImage' => $image_path,
                        'CreatedBy' => $this -> session -> userdata('user_id'),
                        'CreatedOn' => date('Y-m-d H:i:s'),
                        'IsActive' => 1
                    );
                    $result = $this -> productmodel -> add_product($data);
                    if ($result) {
                        $active_stores = $this -> productmodel -> get_all_active_stores();
                        $product_id = $result;
                        if ($active_stores) {
                            $insert_data = [];
                            foreach ($active_stores as $store) {
                                $insert_data[] = array(
                                    'ProductId' => $product_id,
                                    'RetailerId' => $store['RetailerId'],
                                    'StoreId' => $store['Id'],
                                    'StoreTypeId' => $store['StoreTypeId'],
                                    'PriceForAllStores' => '0',
                                    'Price' => number_format($this -> input -> post('product_rate'), 2),
                                    'CreatedBy' => $this -> session -> userdata('user_id'),
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'IsNew' => '1',
                                    'IsActive' => 1,
                                    'IsRemoved' => 0
                                );
                            }
                            $is_added = $this -> productmodel -> insert_to_all_stores($insert_data);
                        }
                        $this -> session -> set_userdata('success_message', "Product added successfully");
                    }
                    else {
                        $this -> session -> set_userdata('error_message', "Failed to add product");
                    }
                    redirect('products', 'refresh');
                    exit(0);
                }
                else {
                    // code to display error while image upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                }
            }
        }

        $this -> breadcrumbs[0] = array('label' => 'Product Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Product Catalogue', 'url' => '/products');
        $this -> breadcrumbs[2] = array('label' => 'Add Product', 'url' => 'products/add');

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['main_categories'] = $data['parent_category'] = $data['sub_category'] = array();
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();

        $this -> template -> view('admin/products/add', $data);
    }

    public function edit($id) {

        //echo "I am in edit";exit;
        
        $data = $this -> productmodel -> get_product_details($id);

        $this -> breadcrumbs[] = array('label' => 'Edit Product', 'url' => 'products/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['main_categories'] = $data['parent_category'] = $data['sub_category'] = array();
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();

        if (!empty($data['main_categories'])) {
            $data['parent_category'] = $this -> categorymodel -> get_parent_categories($data['main_parent_cat_id']);

            $data['sub_category'] = $this -> categorymodel -> get_parent_categories($data['parent_cat_id']);
        }

        $this -> load -> view('admin/products/edit', $data);
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit Product

            $this -> form_validation -> set_rules('product_main_category', 'main category', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('product_parent_category', 'category', 'trim|required|xss_clean');


            //$this -> form_validation -> set_rules('product_name', 'Product name', 'trim|required|xss_clean|callback_check_uniqueness_by_product_name_edit[' . $id . ']');
            
            $this -> form_validation -> set_rules('product_name', 'Product name', 'trim|required|xss_clean');
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
                            'Brand' => $this -> input -> post('brand'),
                            'ProductDescription' => $this -> input -> post('product_description'),
                            'RRP' => number_format($this -> input -> post('product_rate'), 2),
                            'ProductImage' => $result['upload_data']['file_name'],
                            'ModifiedBy' => $this -> session -> userdata('user_id'),
                            'ModifiedOn' => date('Y-m-d H:i:s')
                        );

                        $result = $this -> productmodel -> update_product($id, $edit_data);
                        //$this -> session -> set_userdata('success_message', "Product updated successfully");
                        $this -> result = 1;
                        $this -> message = 'Product updated successfully';
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

    public function delete($id) {

        $this -> productmodel -> delete_product($id);
        $this -> session -> set_userdata('success_message', "Product deleted successfully");
        redirect('products', 'refresh');
    }

    public function change_status($id, $status) {

        $this -> productmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Product status updated successfully");
        redirect('products', 'refresh');
    }

    function file_selected_check() {
        $this -> form_validation -> set_message('file_selected_check', 'Please upload product image.');
        if (empty($_FILES['product_image']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }

    public function import() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            if (!empty($_FILES['import_file']['name'])) {
                $res_arr = array();
                $result = $this -> do_upload_file('import_file');
                if (!isset($result['error'])) {
                    //load the excel library
                    $this -> load -> library('excel');

                    $file_path = IMPORT_FILE_PATH . $result['upload_data']['file_name'];
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file_path);

                    $objWorksheet = $objPHPExcel -> getActiveSheet();

                    $highestRow = $objWorksheet -> getHighestRow(); // e.g. 10
                    $highestColumn = $objWorksheet -> getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $nrColumns = ord($highestColumn) - 64;

                    if ($nrColumns > 1 && $highestRow > 1) {
                        $array_categories = array();
                        for ($row = 2; $row <= $highestRow; ++$row) {
                            array_push($array_categories, $objWorksheet -> getCell("A$row") -> getValue());
                            array_push($array_categories, $objWorksheet -> getCell("B$row") -> getValue());
                            array_push($array_categories, $objWorksheet -> getCell("C$row") -> getValue());
                        }
                        $category_check_result = $this -> categorymodel -> validate_categories(array_unique($array_categories));

                        if (count(array_unique($array_categories)) != count($category_check_result)) {
                            $this -> session -> set_userdata("error_message", "Please enter a valid category in the import file");
                            redirect('products', 'refresh');
                        }
                        else {
                            for ($row = 2; $row <= $highestRow; ++$row) {

                                //Check if product exist
                                if ($this -> check_uniqueness_by_product_name($objWorksheet -> getCell("E$row") -> getValue())) {

                                    $insert_data = array(
                                        'MainCategoryId' => $category_check_result[$objWorksheet -> getCell("A$row") -> getValue()],
                                        'ParentCategoryId' => $category_check_result[$objWorksheet -> getCell("B$row") -> getValue()],
                                        'CategoryId' => $category_check_result[$objWorksheet -> getCell("C$row") -> getValue()],
                                        'SKU' => $objWorksheet -> getCell("D$row") -> getValue(),
                                        'ProductName' => $objWorksheet -> getCell("E$row") -> getValue(),
                                        'Brand' => $objWorksheet -> getCell("F$row") -> getValue(),
                                        'ProductDescription' => $objWorksheet -> getCell("G$row") -> getValue(),
                                        'RRP' => number_format($objWorksheet -> getCell("H$row") -> getValue(), 2),
                                        'ProductImage' => $objWorksheet -> getCell("I$row") -> getValue(),
                                        'CreatedBy' => $this -> session -> userdata('user_id'),
                                        'CreatedOn' => date('Y-m-d H:i:s'),
                                        'IsActive' => 1
                                    );
                                    $result = $this -> productmodel -> add_product($insert_data);
                                    if ($result) {
                                        array_push($res_arr, $result);
                                    }
                                }
                            }
                            if (in_array(0, $res_arr)) {
                                $this -> session -> set_userdata('error_message', "Error while importing products");
                                redirect('products', 'refresh');
                            }
                            else if (!empty($res_arr)) {
                                $insert_data = [];
                                foreach ($res_arr as $product_id) {
                                    $active_stores = $this -> productmodel -> get_all_active_stores();
                                    if ($active_stores) {
                                        $insert_data = [];
                                        foreach ($active_stores as $store) {
                                            $insert_data[] = array(
                                                'ProductId' => $product_id,
                                                'RetailerId' => $store['RetailerId'],
                                                'StoreId' => $store['Id'],
                                                'StoreTypeId' => $store['StoreTypeId'],
                                                'PriceForAllStores' => '0',
                                                'Price' => '0.00',
                                                'CreatedBy' => $this -> session -> userdata('user_id'),
                                                'CreatedOn' => date('Y-m-d H:i:s'),
                                                'IsNew' => '1',
                                                'IsActive' => 1,
                                                'IsRemoved' => 0
                                            );
                                        }
                                    }
                                }
                                $is_added = $this -> productmodel -> insert_to_all_stores($insert_data);
                            }
                        }
                    }
                    if (!empty($_FILES['import_zip_file']['name'])) {
                        $zip_upload_result = $this -> do_upload_file('import_zip_file', 'zip');
                        if (!isset($zip_upload_result['error'])) {
                            $zip = new ZipArchive;
                            $file = $zip_upload_result['upload_data']['full_path'];
                            chmod($file, 0777);

                            $file_name = explode(".", $zip_upload_result['upload_data']['client_name']);

                            if ($zip -> open($file) === TRUE) {
                                $zip -> extractTo('./assets/images/products/large/');
                                $zip -> close();

                                //Move all files from unzipped folder to large product images
                                $files = scandir("./assets/images/products/large/" . $file_name[0] . "/");
                                $oldfolder = "./assets/images/products/large/" . $file_name[0] . "/";
                                $newfolder = "./assets/images/products/large/";
                                foreach ($files as $fname) {
                                    if ($fname != '.' && $fname != '..') {
                                        rename($oldfolder . $fname, $newfolder . $fname);

                                        //Resize images to medium and thumb
                                        $this -> load -> library('image_lib');

                                        //Medium
                                        $config = array(
                                            'source_image' => $newfolder . $fname, //path to the uploaded image
                                            'new_image' => './assets/images/products/medium', //path to new medium image
                                            'maintain_ratio' => true,
                                            'width' => 500,
                                            'height' => 500
                                        );

                                        $this -> image_lib -> initialize($config);
                                        $this -> image_lib -> resize();

                                        //Thumb
                                        $config = array(
                                            'source_image' => $newfolder . $fname, //path to the uploaded image
                                            'new_image' => './assets/images/products/small', //path to new thumb image
                                            'maintain_ratio' => true,
                                            'width' => 200,
                                            'height' => 200
                                        );

                                        $this -> image_lib -> initialize($config);
                                        $this -> image_lib -> resize();
                                    }
                                }

                                //Remove the unzipped empty directory
                                $remove_result = rmdir($oldfolder);

                                //Remove the unzipped directory
                                unlink($file);
                                if (!in_array(0, $res_arr)) {
                                    $this -> session -> set_userdata('success_message', "Products and their images imported successfully");
                                    redirect('products', 'refresh');
                                }
                            }
                            else {
                                $this -> session -> set_userdata('error_message', "Error while importing products images");
                                redirect('products', 'refresh');
                            }
                        }
                        else {
                            // code to display error while file upload
                            $this -> session -> set_userdata('error_message', $zip_upload_result['error']);
                            redirect('products', 'refresh');
                        }
                    }
                    else {
                        $this -> session -> set_userdata('success_message', "Products imported successfully");
                        redirect('products', 'refresh');
                    }
                }
                else {
                    // code to display error while file upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                    redirect('products', 'refresh');
                }
            }
        }
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

    function check_uniqueness_by_product_name($name) {

        $this -> form_validation -> set_message('check_uniqueness_by_product_name', 'Product already exists');

        return $this -> productmodel -> check_product_by_name($name);
    }

    function check_uniqueness_by_product_name_edit($name, $id) {
        $this -> form_validation -> set_message('check_uniqueness_by_product_name_edit', 'Product already exists');

        return $this -> productmodel -> check_product_by_name_edit($name, $id);
    }
    
    /* Function to remove brand name from the product name : If Productname having brand name then brand name is removed from productname */
    function remove_brandname_from_productname() {  
        //echo "Success";exit;
        set_time_limit(0);
        ini_set('memory_limit', '512M');
		
        $this -> db -> select('products.Id,products.ProductName,products.Brand', FALSE);
        $query = $this -> db -> get('products');
        $results = $query -> result_array();

        $i=0;
        foreach($results as $result)
        {
            $brandName = $productName = '';

            $split = explode(' ', $result['ProductName'],2);
            if(isset($split[0]))
            {
                    $brandName = strtolower($split[0]); 
            }

            if(isset($split[1]))
            {
                    $productName = $split[1]; 
            }

            if( $brandName == strtolower($result['Brand']))
            {
                
                $data = array(
                        'ProductName' => $productName
                );

                $this->db->where('Id', $result['Id']);
                $this->db->update('products', $data);

                $i++;
            }
        }
        echo $i." products updated successfully.";
    }
    
    
    function remove_brandname_from_productname_old() {        
        set_time_limit(0);
        ini_set('memory_limit', '512M');
		
        $this -> db -> select('products.Id,products.ProductName,products.Brand', FALSE);
        $query = $this -> db -> get('products');
        $results = $query -> result_array();

        $i=0;
        foreach($results as $result)
        {
            $brandName = $productName = '';

            $split = explode(' ', $result['ProductName'],2);
            if(isset($split[0]))
            {
                    $brandName = $split[0]; 
            }

            if(isset($split[1]))
            {
                    $productName = $split[1]; 
            }

            if( $brandName == $result['Brand'])
            {
                
                    $data = array(
                            'ProductName' => $productName
                    );

                    $this->db->where('Id', $result['Id']);
                    $this->db->update('products', $data);
                
                    $i++;
            }
        }
        echo $i." products updated successfully.";
    }
    
    /* Function to remove brand name from the product name : If Productname having brand name then brand name is removed from productname */
    function remove_spaces_from_productname() {  
        //echo "Success";exit;
        set_time_limit(0);
        ini_set('memory_limit', '512M');
		
        $this -> db -> select('products.Id,products.ProductName,products.Brand', FALSE);
        $query = $this -> db -> get('products');
        $results = $query -> result_array();

        $i=0;
        foreach($results as $result)
        {
            $productName = trim($result['ProductName']);

            $data = array(
                    'ProductName' => $productName
            );

            $this->db->where('Id', $result['Id']);
            $this->db->update('products', $data);

            $i++;
            
        }
        echo $i." products updated successfully.";
    }
}
