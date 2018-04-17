<?php

/*
 * Author:PM
 * Purpose:Special Products Controller
 * Date:14-09-2015
 * Dependency: specialproductmodel.php
 */

class SpecialProducts extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/specialproductmodel', '', TRUE);
        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);
        $this -> load -> model('admin/productmodel', '', TRUE);
        $this -> load -> model('admin/storeproductmodel', '', TRUE);
        $this -> load -> model('admin/storeformatmodel', '', TRUE);
        $this -> load -> model('admin/categorymodel', '', TRUE);

        $this -> page_title = "Approve A Special";
        $this -> breadcrumbs[0] = array('label' => 'Manage Your Specials', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Approve A Special', 'url' => '/specialproducts');

        //StoreFormat Users
        //Check if user has completed store wizard steps.
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    public function index() {

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['specials'] = $this -> specialproductmodel -> get_specials();

        $this -> template -> view('admin/special_products/index', $data);
    }

    public function datatable($price_from = 0, $price_to = 0) {

        $this -> datatables -> select("products.ProductName as ProductName, specials.SpecialName, stores.StoreName as Address, IF((productspecials.SpecialQty != 1), CONCAT_WS(' For ', productspecials.SpecialQty ,productspecials.SpecialPrice), productspecials.SpecialPrice) AS Price, DATE(productspecials.PriceAppliedFrom) as PriceAppliedFrom, DATE(productspecials.PriceAppliedTo) as PriceAppliedTo, productspecials.IsActive AS active, productspecials.Id AS Id, productspecials.IsApproved as approved", FALSE)
            -> unset_column('active')
            -> unset_column('Id')
            -> from('productspecials')
            -> join('products', 'products.Id = productspecials.ProductId')
            -> join('stores', 'stores.Id = productspecials.StoreId', 'left')
            -> join('specials', 'productspecials.SpecialId = specials.Id', 'left')
            -> add_column('Actions', get_action_buttons('$1', 'specialproducts'), 'Id');


        $array_where = '';
        if ($this -> session -> userdata('user_type') == 3) {
            $array_where['productspecials.RetailerId'] = $this -> session -> userdata('user_retailer_id');
        }

        //StoreFormat Users
        if ($this -> session -> userdata('user_type') == 5) {
            $array_where['productspecials.StoreTypeId'] = $this -> session -> userdata('user_store_format_id');
        }

        //Store Users
        if ($this -> session -> userdata('user_type') == 6) {
            $array_where['productspecials.StoreId'] = $this -> session -> userdata('user_store_id');
        }

        $array_where != '' ? $this -> datatables -> where($array_where) : '';


        //$this -> datatables -> where($array_where);
        $cond = '';
        if ($price_from != 0 || $price_to != 0) {
            if ($price_from != 0 && $price_to == 0)
                $cond = array("productspecials.PriceAppliedFrom >=" => $price_from);
            if ($price_from == 0 && $price_to != 0)
                $cond = array("productspecials.PriceAppliedTo" => $price_to);
            if ($price_from != 0 && $price_to != 0)
                $cond = "( DATE(PriceAppliedFrom) BETWEEN '$price_from' AND '$price_to' OR DATE(PriceAppliedTo) BETWEEN '$price_from' AND '$price_to' )";

            $this -> datatables -> where($cond);
        }


        echo $this -> datatables -> generate();
    }

    public function add() {
        $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

        //Non Admin Users
        if ($this -> session -> userdata('user_level') >= 3) {
            $retailer_id = $this -> session -> userdata('user_retailer_id');
        }
        else {
            $retailer_id = $this -> input -> post('retailers');
        }

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Add Special Product
            $this -> form_validation -> set_rules('products', 'products', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('special_quantity', 'special quantity', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('special_price', 'special price', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('price_from', 'price from', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('price_to', 'price to', 'trim|required|xss_clean');

            if (!$this -> form_validation -> run() == FALSE) {

                //Storeformat User
                if ($this -> session -> userdata('user_type') == 5) {
                    $stores = $this -> input -> post('stores_list');
                }
                //Store User
                elseif ($this -> session -> userdata('user_type') != 6) {
                    //Stores List Array
                    $stores = $this -> input -> post('store_format_list');
                }
                else {
                    $stores = array($this -> session -> userdata('user_store_id'));
                }

                $stores_list = implode(',', $stores);

                //Validate if same offer exists for same product
                $validate_data = array('ProductId' => $this -> input -> post('products'),
                    'RetailerId' => $retailer_id,
                    'SpecialQty' => $this -> input -> post('special_quantity'),
                    'SpecialPrice' => $this -> input -> post('special_price'),
                    'PriceAppliedFrom' => $this -> input -> post('price_from'),
                    'PriceAppliedTo' => $this -> input -> post('price_to'),
                    'IsActive' => 1,
                    'IsApproved' => 0,
                );

                $validate_offer = $this -> specialproductmodel -> validate_offer($validate_data, $stores_list);

                if ($validate_offer) {
                    foreach ($stores as $store):
                        if ($store) {
                            if ($this -> session -> userdata('user_level') > 3) {
                                //StoreFormat Users & Store Users
                                $store_format_id = $this -> session -> userdata('user_store_format_id');
                                if ($this -> session -> userdata('user_level') == 5) {
                                    $store_format_id = $this -> storemodel -> get_store_format($store);
                                }
                            }
                            else {
                                $store_format_id = $this -> storemodel -> get_store_format($store);
                            }

                            //Check if product available in the store if not available product is added.

                            $this -> specialproductmodel -> validate_store_product($this -> input -> post('products'), $retailer_id, $store);

                            $insert_data = array('ProductId' => $this -> input -> post('products'),
                                'RetailerId' => $retailer_id,
                                'StoreId' => $store,
                                'StoreTypeId' => $store_format_id,
                                'PriceForAllStores' => $this -> input -> post('price_store'),
                                'ActualPrice' => $this -> input -> post('actual_price'),
                                'SpecialQty' => $this -> input -> post('special_quantity'),
                                'SpecialPrice' => $this -> input -> post('special_price'),
                                'PriceAppliedFrom' => $this -> input -> post('price_from'),
                                'PriceAppliedTo' => $this -> input -> post('price_to'),
                                'CreatedBy' => $this -> session -> userdata('user_id'),
                                'CreatedOn' => date('Y-m-d H:i:s'),
                                'IsActive' => 1,
                                'IsApproved' => 0,
                                'ApprovedBy' => 0);


                            $result = $this -> specialproductmodel -> add_special_product($insert_data);
                        }
                    endforeach;

                    if ($result > 0) {

                        //Send Email To Retailer Admin
                        $this -> load -> model('admin/emailtemplatemodel');

                        $email_template_details = $this -> emailtemplatemodel -> get_email_template_details(2);
                        $productdetails = $this -> productmodel -> get_product_details($this -> input -> post('products'));

                        $emailBody = $email_template_details['Content'];
                        $emailBody = str_replace("{user_name}", $this -> session -> userdata('user_full_name'), $emailBody);

                        $emailBody = str_replace("{product_name}", $productdetails['ProductName'], $emailBody);
                        $emailBody = str_replace("{quantity}", $this -> input -> post('special_quantity'), $emailBody);
                        $emailBody = str_replace("{price}", $this -> input -> post('special_price'), $emailBody);

                        //---- LOAD EMAIL LIBRARY ----//
                        $this -> load -> library('email');
                        $config['mailtype'] = 'html';

                        $this -> email -> initialize($config);
                        $this -> email -> from($email_template_details['FromEmail']);
                        $this -> email -> to($email_template_details['ToEmail']);
                        $this -> email -> subject("The Best Deals: " . $email_template_details['ToEmail']);
                        $this -> email -> message($emailBody);
                        // $this -> email -> send();
                        //Get user having price alert enabled
                        $product_id = $this -> input -> post('products');
                        //$this -> send_user_notification($product_id);

                        $this -> session -> set_userdata('success_message', "Special product added successfully");
                    }
                    else
                        $this -> session -> set_userdata('error_message', "Error while adding special product");
                    redirect('specialproducts', 'refresh');
                }
                else
                    $this -> session -> set_userdata('error_message', "The same offer already exists for one of the store selcted");
                redirect('specialproducts', 'refresh');
            }
        }

        $this -> breadcrumbs[] = array('label' => 'Add Special Product', 'url' => 'specialproducts/add');

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['main_categories'] = $this -> categorymodel -> get_retailer_categories($retailer_id);

//        $data['products'] = $this->storeproductmodel->get_store_products($retailer_id);


        $retailer_id = $this -> session -> userdata('user_retailer_id');

        $data['main_categories'] = $this -> categorymodel -> get_retailer_categories($retailer_id);

        //Retailer Users
        if ($this -> session -> userdata('user_type') == 3) {
            $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($this -> session -> userdata('user_retailer_id'));
        }
        //Store Format Users
        if ($this -> session -> userdata('user_type') == 5) {
            $store_format_id = $this -> session -> userdata('user_store_format_id');
            $data['stores'] = $this -> storemodel -> get_stores_by_store_format($store_format_id);
        }


        $this -> template -> view('admin/special_products/add', $data);
    }

    public function edit($id) {
        $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

        $this -> breadcrumbs[] = array('label' => 'Edit Special Product', 'url' => 'specialproducts/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['special_product_details'] = $this -> specialproductmodel -> get_special_product_details($id);

        $data['products'] = $this -> storeproductmodel -> get_store_products($retailer_id);

        $data['stores'] = $this -> storemodel -> get_stores();

        $this -> load -> view('admin/special_products/edit', $data);
    }

    public function edit_post($id) {
        $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');


        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Update Special Product

            $this -> form_validation -> set_rules('special_quantity', 'special quantity', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('special_price', 'special price', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('from_price', 'price from', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('to_price', 'price to', 'trim|required|xss_clean');

            if (!$this -> form_validation -> run() == FALSE) {

                $edit_data = array(
                    'SpecialQty' => $this -> input -> post('special_quantity'),
                    'SpecialPrice' => $this -> input -> post('special_price'),
                    'PriceAppliedFrom' => $this -> input -> post('from_price'),
                    'PriceAppliedTo' => $this -> input -> post('to_price'),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s'));

                $result = $this -> specialproductmodel -> update_special_product($id, $edit_data);

                //Get user having price alert enabled
                $product_id = $this -> input -> post('products');
                $this -> send_user_notification($product_id);
                $this -> result = 1;

//                $this->session->set_userdata('success_message', "Special Product updated successfully");
//
//                redirect('specialproducts', 'refresh');
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

        $this -> specialproductmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Special product status updated successfully");
        redirect('specialproducts', 'refresh');
    }

    public function delete($id) {

        $this -> specialproductmodel -> delete_product($id);
        $this -> session -> set_userdata('success_message', "Special product deleted successfully");
        redirect('specialproducts', 'refresh');
    }

    public function approve_product($id) {

        $isApprove = $this -> specialproductmodel -> approve_product($id);
        $special_product_data = $this -> specialproductmodel -> get_special_pending_details_single($id);
        $product_name = $special_product_data['ProductName'];
        $product_id = $special_product_data['ProductId'];
        $retailer_name = $special_product_data['CompanyName'];
        $retailer_id = $special_product_data['RetailerId'];
        $store_name = $special_product_data['StoreName'];
        $store_id = $special_product_data['StoreId'];
        $store_type_name = $special_product_data['StoreType'];
        $store_type_id = $special_product_data['StoreTypeId'];
        $special_count = $special_product_data['SpecialQty'];
        $special_price = $special_product_data['SpecialPrice'];
        $special_name = $special_product_data['SpecialName'];
        $store_array[] = $special_product_data['StoreId'];
        $store_detail_array[] = array(
            'id' => $special_product_data['StoreId'],
            'name' => $special_product_data['StoreName'],
            'retailer' => $special_product_data['RetailerId'],
            'storeType' => $special_product_data['StoreTypeId']
        );
        $product_array[] = array(
            'id' => $special_product_data['ProductId'],
            'name' => $special_product_data['ProductName']
        );
        create_push_message($product_id, $retailer_id, $store_type_id, $store_id, $product_name, $retailer_name, $store_name, $store_type_name, $special_count, $special_price, $special_name, '1', $store_array, $product_array, $store_detail_array);
        //$this -> session -> set_userdata('success_message', "Special product approved successfully");
        //redirect('specialproducts', 'refresh');
        //$isApprove = TRUE;
        if ($isApprove) {
            $this -> result = 1;
            $this -> message = 'Special product approved successfully';
        }
        else {
            $this -> result = 0;
            $this -> message = 'Failed to approve special product';
        }

        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }

    public function get_products_by_category($main_parent_category_id = 0) {

        $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

//        $categories_products = $this->storeproductmodel->get_store_products($retailer_id, $main_parent_category_id);

        $product_ids = array();
        $categories_products = $this -> productmodel -> get_products_by_category($main_parent_category_id, $product_ids);

        $categories_products_results = "";

        $categories_products_results = "<option value=''>Select Product</option>";

        foreach ($categories_products as $categories_products) {
            $categories_products_results .= '<option value="' . $categories_products['Id'] . '">' . $categories_products['ProductName'] . '</option>';
        }

        echo json_encode(array('categories_products' => $categories_products_results));
    }

    public function welcome() {

        $data['title'] = $this -> page_title;

        //Update step one completed for a user.
        $step_data = array('Step3' => '1');
        $this -> storemodel -> update_wizard_step($step_data);

        $this -> template -> view('admin/store_wizard/special_products_welcome', $data);
    }

    public function get_default_price() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $category = $this -> input -> post('category');
            $product = $this -> input -> post('product');
            $price = $this -> specialproductmodel -> get_default_price($product);
            if ($price) {
                echo $price['RRP'];
            }
            else {
                echo '';
            }
        }
    }
}

?>