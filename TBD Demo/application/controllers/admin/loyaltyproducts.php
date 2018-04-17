<?php

/*
 * Author:MK
 * Purpose:Loyalty Products Controller
 * Date:01-03-2017
 * Dependency: loyaltyproductmodel.php
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
class Loyaltyproducts extends My_Controller {

    private $message;
    private $result;

    function __construct() {
        parent::__construct();
        
        # Load required models
        $this -> load -> model('admin/loyaltybrandmodel', '', TRUE);
        $this -> load -> model('admin/loyaltycategorymodel', '', TRUE);        
        $this -> load -> model('admin/loyaltyproductmodel', '', TRUE);

        # Set default values
        $this -> page_title = "Loyalty Products";
        $this -> breadcrumbs[] = array('label' => 'Loyalty Products', 'url' => '/loyaltyproducts');

        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    /*
     * Method Name: index
     * Purpose: Shows all loyalty products 
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    
    public function index() {
        # Set default values
        $data['brands'] = array();
        
        # Set page title
        $data['title'] = $this -> page_title;

        # Set breadcrumbs
        $this -> breadcrumbs[0] = array('label' => 'Loyalty Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Loyalty Products', 'url' => '/loyaltyproducts');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        
        # Get loyalty products categories
        $data['categories'] = $this -> loyaltycategorymodel -> get_loyalty_categories();
       
        $this -> template -> view('admin/loyaltyproducts/index', $data);
    }

    /*
     * Method Name: datatable
     * Purpose: Get loyalty products data based on criteria
     * params:
     *      input: $brand_id
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    public function datatable($category_id =0) {
        $this -> datatables -> select("p.Id as Id,p.BrandName, p.LoyaltyTitle, p.ProductImage, p.LoyaltyDescription, p.StartDate, p.EndDate,p.LoyaltyPoints,  p.IsActive AS active, c.CategoryName", FALSE)
            -> unset_column('ProductImage')
            -> unset_column('Id')
            -> unset_column('active')
            -> from('loyalty_products as p')            
            -> join('loyalty_categories as c', 'c.Id = p.CategoryId and c.IsActive = 1 and c.IsRemoved = 0', 'left')    
            -> add_column('Actions', get_action_buttons('$1', 'loyaltyproducts'), 'Id');

        $cond = array(
           'p.IsRemoved' => '0'
        );
        
        if ($category_id != 0) {            
            $cond = $cond + array("p.CategoryId" => $category_id);
        }        
        
        $this -> datatables -> where($cond);

        echo $this -> datatables -> generate();
    }
    
    
    /*
     * Method Name: add
     * Purpose: Add loyalty products
     * params:
     *      input: PostArray
     *      output: status - FAIL / SUCCESS
     *              message - 
     */

    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Add Product            
            $this -> form_validation -> set_rules('LoyaltyTitle', 'Product name', 'trim|required|xss_clean|callback_check_uniqueness_by_product_name');            
            $this -> form_validation -> set_rules('LoyaltyDescription', 'Description', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('CategoryId', 'Category', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('BrandName', 'Brand name', 'trim|required|xss_clean');           
            $this -> form_validation -> set_rules('StartDate', 'Start date', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('EndDate', 'End Date', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('LoyaltyPoints', 'Loyalty points', 'trim|required|numeric|xss_clean');            

            if (!$this -> form_validation -> run() == FALSE) {

                $result = array();
                $image_path = "";

                //If image uploaded
                if (!empty($_FILES['ProductImage']['name'])) {
                    $result = $this -> do_upload('ProductImage', 'loyaltyproducts', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    $image_path = $result['upload_data']['file_name'];
                }
                if (!isset($result['error'])) {  
                   
                    $data = array(
                        'LoyaltyTitle' => $this -> input -> post('LoyaltyTitle'),
                        'LoyaltyDescription' => $this -> input -> post('LoyaltyDescription'),
                        'CategoryId' => $this -> input -> post('CategoryId'),
                        'BrandName' => $this -> input -> post('BrandName'),                        
                        'StartDate' => $this -> input -> post('StartDate'),
                        'EndDate' => $this -> input -> post('EndDate'),
                        'LoyaltyPoints' => $this -> input -> post('LoyaltyPoints'),                        
                        'ProductImage' => $image_path,
                        'CreatedBy' => $this -> session -> userdata('user_id'),
                        'CreatedOn' => date('Y-m-d H:i:s'),
                        'IsActive' => 1
                    );
                    $result = $this -> loyaltyproductmodel -> add_product($data);
                    if ($result) {
                        $this -> session -> set_userdata('success_message', "Product added successfully");
                        $this -> result = 1;
                        $this -> message = 'Product added successfully';
                    }
                    else {
                        $this -> session -> set_userdata('error_message', "Failed to add product");
                        $this -> result = 0;
                        $this -> message = 'Failed to add product';
                    }
                    redirect('/loyaltyproducts', 'refresh');
                    exit(0);
                }
                else {                   
                    // code to display error while image upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                    $this -> result = 0;
                    $this -> message = $result['error'];
                }
            }else{
               //echo validation_errors();
            }
        }

        $this -> breadcrumbs[0] = array('label' => 'Loyalty Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Loyalty Product', 'url' => '/loyaltyproducts');
        $this -> breadcrumbs[2] = array('label' => 'Add Product', 'url' => 'loyaltyproducts/add');

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;
        
        # Get loyalty products categories
        $data['categories'] = $this -> loyaltycategorymodel -> get_loyalty_categories();        
        
        $this -> template -> view('admin/loyaltyproducts/add', $data);
    }

    /*
     * Method Name: edit
     * Purpose: Get loyalty product information for edit
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProduct - Array containing all active loyalty products.
     */
    
    public function edit($id) {
        # get product deatils 
        $data = $this -> loyaltyproductmodel -> get_product_details($id);
           
        #Set values 
        $this -> breadcrumbs[] = array('label' => 'Edit Product', 'url' => 'loyaltyproducts/edit/' . $id);

        $data['title'] = $this -> page_title;
        $data['breadcrumbs'] = $this -> breadcrumbs;       
        
        # Get loyalty products categories
        $data['categories'] = $this -> loyaltycategorymodel -> get_loyalty_categories();
        
        $this -> load -> view('admin/loyaltyproducts/edit', $data);
    }

    /*
     * Method Name: edit_post
     * Purpose: Edit loyalty product information.
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProduct - Array containing all active loyalty products.
     */
    
    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit Product
            $this -> form_validation -> set_rules('LoyaltyTitle', 'Product name', 'trim|required|xss_clean|callback_check_uniqueness_by_product_name_edit[' . $id . ']');
            $this -> form_validation -> set_rules('LoyaltyDescription', 'Description', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('CategoryId', 'Category', 'trim|required|xss_clean');            
            $this -> form_validation -> set_rules('BrandName', 'Brand name', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('StartDate', 'Start date', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('EndDate', 'End Date', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('LoyaltyPoints', 'Loyalty points', 'trim|required|numeric|xss_clean');
            

//            if (empty($_FILES['prod_image']['name']) && $this->input->post('old_product_image') == '') {
//               $this->form_validation->set_rules('prod_image', 'Document', 'callback_file_selected_check');
//            }
                

            if (!$this -> form_validation -> run() == FALSE) {
                    
                if (!empty($_FILES['ProductImage']['name'])) {
                    $result = $this -> do_upload('ProductImage', 'loyaltyproducts', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    if (!isset($result['error'])) {
                        $edit_data = array(
                                'LoyaltyTitle' => $this -> input -> post('LoyaltyTitle'),
                                'LoyaltyDescription' => $this -> input -> post('LoyaltyDescription'),
                                'CategoryId' => $this -> input -> post('CategoryId'),                                
                                'BrandName'=> $this -> input -> post('BrandName'),
                                'StartDate' => $this -> input -> post('StartDate'),
                                'EndDate' => $this -> input -> post('EndDate'),
                                'LoyaltyPoints' => $this -> input -> post('LoyaltyPoints'),                        
                                'ProductImage' => $result['upload_data']['file_name'],
                                'ModifiedBy' => $this -> session -> userdata('user_id'),
                                'ModifiedOn' => date('Y-m-d H:i:s')
                         );
                        

                        $result = $this -> loyaltyproductmodel -> update_product($id, $edit_data);
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
                                'LoyaltyTitle' => $this -> input -> post('LoyaltyTitle'),
                                'LoyaltyDescription' => $this -> input -> post('LoyaltyDescription'),
                                'BrandId' => $this -> input -> post('BrandId'),
                                'CategoryId' => $this -> input -> post('CategoryId'),
                                'BrandName'=> $this -> input -> post('BrandName'),
                                'StartDate' => $this -> input -> post('StartDate'),
                                'EndDate' => $this -> input -> post('EndDate'),
                                'LoyaltyPoints' => $this -> input -> post('LoyaltyPoints'),                                                        
                                'ModifiedBy' => $this -> session -> userdata('user_id'),
                                'ModifiedOn' => date('Y-m-d H:i:s')
                         );

                    $result = $this -> loyaltyproductmodel -> update_product($id, $edit_data);
                    $this -> session -> set_userdata('success_message', "Product updated successfully");                    
                    $this -> result = 1;
                    $this -> message = 'Product updated successfully';
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

    /*
     * Method Name: delete
     * Purpose: Delete loyalty product
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if loyalty products not found fails / Success message
     *              loyaltyProduct - Array containing all active loyalty products.
     */
    
    public function delete($id) {        
        $this -> loyaltyproductmodel -> delete_product($id);
        $this -> session -> set_userdata('success_message', "Product deleted successfully");
        redirect('loyaltyproducts', 'refresh');
    }

    /*
     * Method Name: change_status
     * Purpose: Update loyalty product status
     * params:
     *      input: $product_id, $status
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    public function change_status($id, $status) {
        $this -> loyaltyproductmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Product status updated successfully");
        redirect('loyaltyproducts', 'refresh');
    }

    function file_selected_check() {
        $this -> form_validation -> set_message('file_selected_check', 'Please upload product image.');
        if (empty($_FILES['ProductImage']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }

    /*
     * Method Name: check_product_by_name
     * Purpose: Check product name exist
     * params:
     *      input: $name
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    function check_uniqueness_by_product_name($name) {
        $this -> form_validation -> set_message('check_uniqueness_by_product_name', 'Product already exists');
        return $this -> loyaltyproductmodel -> check_product_by_name($name);
    }

    /*
     * Method Name: check_product_by_name_edit
     * Purpose: Check product name exist excluding current record
     * params:
     *      input: $name,$id
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */    
    function check_uniqueness_by_product_name_edit($name, $id) {
        $this -> form_validation -> set_message('check_uniqueness_by_product_name_edit', 'Product already exists');

        return $this -> loyaltyproductmodel -> check_product_by_name_edit($name, $id);
    }
}
