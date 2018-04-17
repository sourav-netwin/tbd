<?php

/*
 * Author:PM
 * Purpose:Store Products Controller
 * Date:10-09-2015
 * Dependency: storeproductmodel.php
 */

class StoreProducts extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/storeproductmodel', '', TRUE);
        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);
        $this -> load -> model('admin/productmodel', '', TRUE);
        $this -> load -> model('admin/categorymodel', '', TRUE);
        $this -> load -> model('admin/storeformatmodel', '', TRUE);

        $this -> page_title = "Store Products";
        $this -> breadcrumbs[] = array('label' => 'Stores Products', 'url' => '/storeproducts');

        //StoreFormat Users
        //Check if user has completed store wizard steps.
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    public function index() {

        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'Stores Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Store Products', 'url' => '/storeproducts');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        //Admin Users
        if ($this -> session -> userdata('user_level') < 3) {

            $data['retailers'] = $this -> retailermodel -> get_retailers();
        }
        else {
            $retailer_id = $this -> session -> userdata('user_retailer_id');

            $data['main_categories'] = $this -> categorymodel -> get_retailer_categories($retailer_id);

            //Store Format Users
            if ($this -> session -> userdata('user_type') == 5) {
                $store_format_id = $this -> session -> userdata('user_store_format_id');
                $data['stores'] = $this -> storemodel -> get_stores_by_store_format($store_format_id);
            }
            else {
                $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($this -> session -> userdata('user_retailer_id'));
            }
        }

        $this -> template -> view('admin/store_products/index', $data);
    }

    public function datatable($retailer_id = 0) {

        $this -> datatables -> select("products.ProductName as ProductName,case when storeproducts.Price <= 0 then concat('<input class=\"change_store_pr\" id=\"store_price_',storeproducts.Id,'\" style=\"width:70px;text-align:right\" type=\"text\" value=\"',0.00,'\" />') else concat('<input class=\"change_store_pr\" id=\"store_price_',storeproducts.Id,'\" style=\"width:70px;text-align:right\" type=\"text\" value=\"',storeproducts.Price,'\" />') end as Price, retailers.CompanyName as CompanyName, CONCAT_WS( ', ', stores.StoreName,stores.City ) as Address, storeproducts.IsActive as active, storeproducts.Id as Id,storeproducts.IsNew as IsNew ", FALSE)
            -> unset_column('Id')
            -> unset_column('active')
            -> from('storeproducts')
            -> join('products', 'products.Id = storeproducts.ProductId')
            -> join('retailers', 'retailers.Id = storeproducts.RetailerId')
            -> join('stores', 'stores.Id = storeproducts.StoreId', 'left')
            -> add_column('Actions', get_edit_button('$1', 'storeproducts'), 'Id');

        $array_where = array('products.IsRemoved' => 0);
        if ($this -> session -> userdata('user_type') == 3) {
            $array_where['retailers.Id'] = $this -> session -> userdata('user_retailer_id');
        }

        //StoreFormat Users
        if ($this -> session -> userdata('user_type') == 5) {
            $array_where['storeproducts.StoreTypeId'] = $this -> session -> userdata('user_store_format_id');
        }

        //Store Users
        if ($this -> session -> userdata('user_type') == 6) {
            $array_where['storeproducts.StoreId'] = $this -> session -> userdata('user_store_id');
        }


        if ($retailer_id > 0) {
            $array_where['storeproducts.RetailerId'] = $retailer_id;
        }

        $this -> datatables -> where($array_where);
        
         # If login as retailer then show only product only once.
        if ( $this -> session -> userdata('user_type') == 3 || $this -> session -> userdata('user_type') == 6) {
            $this -> datatables -> group_by('storeproducts.ProductId');
        }
        
        echo $this -> datatables -> generate();
    }

    public function add($step = "") {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Products Array
            $store_products = $this -> input -> post('store_products');

            if (empty($store_products)) {
                $this -> session -> set_userdata('error_message', "Please select a product");

                if ($step != 'new') {
                    redirect('storeproducts/add', 'refresh');
                }
                else {
                    redirect('storeproducts/add/new', 'refresh');
                }
            }

            //Retailer Users
            if ($this -> session -> userdata('user_level') >= 3) {
                $retailer_id = $this -> session -> userdata('user_retailer_id');
            }
            else {
                $retailer_id = $this -> input -> post('retailers');
            }


            $validate = TRUE;
            //No retailer validation for retailer users
            if ($this -> session -> userdata('user_level') < 3) {
                $this -> form_validation -> set_rules('retailers', 'retailers', 'trim|required|xss_clean');
                $validate = $this -> form_validation -> run();
            }


            if ($validate) {

                //Stores List Array
                if ($this -> session -> userdata('user_type') != 6) {
                    //Stores List Array
                    $stores = $this -> input -> post('stores_list');
                }
                else {
                    $stores = array($this -> session -> userdata('user_store_id'));
                }

                foreach ($store_products as $store_product):
                    foreach ($stores as $store):

                        if ($store != 0) {
                            if ($this -> session -> userdata('user_level') > 3) {
                                //StoreFormat Users & Store Users
                                $store_format_id = $this -> session -> userdata('user_store_format_id');
                            }
                            else {
                                $store_format_id = $this -> storemodel -> get_store_format($store);
                            }
                            $is_already_added = $this -> storemodel -> check_product_added($store_product, $retailer_id, $store_format_id, $store);
                            if (!$is_already_added) {
                                $insert_data = array('ProductId' => $store_product,
                                    'RetailerId' => $retailer_id,
                                    'StoreId' => $store,
                                    'StoreTypeId' => $store_format_id,
                                    'Price' => number_format($this -> input -> post('product_price_' . $store_product)),
                                    'CreatedBy' => $this -> session -> userdata('user_id'),
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'IsActive' => 1);

                                $result = $this -> storeproductmodel -> add_store_product($insert_data);
                            }
                            else {
                                $result = 0;
                            }
                        }

                    endforeach;
                endforeach;

                if ($result > 0)
                    $this -> session -> set_userdata('success_message', "Product added to store successfully");
                else
                    $this -> session -> set_userdata('error_message', "Error while adding product to store");

                if ($step != 'new') {
                    redirect('storeproducts/add', 'refresh');
                }
                else {

                    //Update step one completed for a user.
                    $step_data = array('Step3' => '1');
                    $this -> storemodel -> update_wizard_step($step_data);
                    redirect('specialproducts/welcome', 'refresh');
                }
            }
        }
        if ($step != 'new') {
            $this -> breadcrumbs[] = array('label' => 'Add Product to Store', 'url' => 'storeproducts/add');
            $data['breadcrumbs'] = $this -> breadcrumbs;
        }
        $data['title'] = $this -> page_title;

        $data['step'] = $step;

        //Admin Users
        if ($this -> session -> userdata('user_level') < 3) {
            $data['retailers'] = $this -> retailermodel -> get_retailers();
        }
        else {

            $retailer_id = $this -> session -> userdata('user_retailer_id');
            $data['retailer_id'] = $retailer_id;
            $data['main_categories'] = $this -> categorymodel -> get_retailer_categories($retailer_id);

            //Store Format Users
            if ($this -> session -> userdata('user_type') == 5) {
                $store_format_id = $this -> session -> userdata('user_store_format_id');
                $data['stores'] = $this -> storemodel -> get_stores_by_store_format($store_format_id);
                $data['store_format_id'] = $store_format_id;
            }
            elseif ($this -> session -> userdata('user_type') == 6) {
                $store_id = $this -> session -> userdata('user_store_id');
                $data['store_id'] = $store_id;
                $store_details = $this -> storeproductmodel -> get_store_detais_from_id($store_id);
                if ($store_details) {
                    $retailer_id = $store_details['RetailerId'];
                    $store_format_id = $store_details['StoreTypeId'];
                }
                $data['retailer_id'] = $retailer_id;
                $data['store_format_id'] = $store_format_id;
            }
            else {
                $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($this -> session -> userdata('user_retailer_id'));
            }
        }

        $this -> template -> view('admin/store_products/add', $data);
    }

    public function add_custom($step = "") {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_id = $this -> input -> post('retailers_store_search');
            $store_formats = $this -> input -> post('store_format_list');
            $stores = $this -> input -> post('stores_list');
            $products = json_decode($this -> input -> post('selectedProd'));

//            echo '<pre>';
//            print_r($products);die;    

            if (empty($products)) {
                $this -> session -> set_userdata('error_message', "Please select a product");

                if ($step != 'new') {
                    redirect('storeproducts/add', 'refresh');
                }
                else {
                    redirect('storeproducts/add/new', 'refresh');
                }
            }

            //Retailer Users
            if ($this -> session -> userdata('user_level') >= 3) {
                $retailer_id = $this -> session -> userdata('user_retailer_id');
            }
            else {
                $retailer_id = $this -> input -> post('retailers_store_search');
            }


            $validate = TRUE;
            //No retailer validation for retailer users
            if ($this -> session -> userdata('user_level') < 3) {
                $this -> form_validation -> set_rules('retailers_store_search', 'retailers', 'trim|required|xss_clean');
                $validate = $this -> form_validation -> run();
            }


            if ($validate) {

                //Stores List Array
                if ($this -> session -> userdata('user_type') != 6) {
                    //Stores List Array
                    $stores = $this -> input -> post('stores_list');
                }
                else {
                    $stores = array($this -> session -> userdata('user_store_id'));
                }

                foreach ($products as $key => $store_product):
                    foreach ($stores as $store):

                        if ($store != 0) {
                            if ($this -> session -> userdata('user_level') > 3) {
                                //StoreFormat Users & Store Users
                                $store_format_id = $this -> session -> userdata('user_store_format_id');
                            }
                            else {
                                $store_format_id = $this -> storemodel -> get_store_format($store);
                            }
                            $is_already_added = $this -> storemodel -> check_product_added($key, $retailer_id, $store_format_id, $store);
                            if (!$is_already_added) {
                                $insert_data = array(
                                    'ProductId' => $key,
                                    'RetailerId' => $retailer_id,
                                    'StoreId' => $store,
                                    'StoreTypeId' => $store_format_id,
                                    'Price' => number_format($store_product -> price),
                                    'CreatedBy' => $this -> session -> userdata('user_id'),
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'IsActive' => 1);

                                $result = $this -> storeproductmodel -> add_store_product($insert_data);
                            }
                        }

                    endforeach;
                endforeach;

                if ($result > 0)
                    $this -> session -> set_userdata('success_message', "Product added to store successfully");
                else
                    $this -> session -> set_userdata('error_message', "Error while adding product to store");

                if ($step != 'new') {
                    //redirect('storeproducts/add', 'refresh');
                }
                else {
                    //Update step one completed for a user.
                    $step_data = array('Step3' => '1');
                    $this -> storemodel -> update_wizard_step($step_data);
                    //redirect('specialproducts/welcome', 'refresh');
                }
            }
        }
        echo '1';
    }

    public function edit($id) {

        $data['store_product_details'] = $this -> storeproductmodel -> get_store_product_details($id);
        $this -> breadcrumbs[] = array('label' => 'Edit Store', 'url' => 'stores/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> load -> view('admin/store_products/edit', $data);
    }

    public function edit_post($id) {
        $data['store_product_details'] = $this -> storeproductmodel -> get_store_product_details($id);

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            $retailer_id = ( $this -> session -> userdata('user_type') != 3 ) ? $this -> input -> post('retailers') : $this -> session -> userdata('user_retailer_id');

            $this -> form_validation -> set_rules('price', 'price', 'trim|required|xss_clean');

            if (!$this -> form_validation -> run() == FALSE) {

                $edit_data = array('Price' => $this -> input -> post('price'),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s'));

                $this -> storeproductmodel -> update_store_product($id, $edit_data);
                $this -> session -> set_userdata('success_message', "Product updated for store successfully");

                //Send Alert To users using push notifications if change in price
                if ($data['store_product_details']['Price'] != $this -> input -> post('price')) {

                    //Get user having price alert enabled
                    $product_id = $this -> input -> post('products');

                    $product = $this -> productmodel -> get_product_details(1);

                    $product_name = $product['ProductName'];

                    //Get product name
                    $message = "The price of your favorite product " . $product_name . " has been changed.";

                    $users = $this -> productmodel -> get_price_alert_users($product_id);

                    //Send Notification.
                    send_notification($message, $users);

                    //Add value to user notification table
                    $this -> load -> model('admin/notificationmodel', '', TRUE);
                    foreach ($users as $user) {

                        $data = array(
                            'UserId' => $user['UserId'],
                            'Message' => $message,
                            'CreatedOn' => Date('Y-m-d')
                        );

                        $this -> notificationmodel -> add_notification($data);
                    }
                }

                $this -> result = 1;
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

        $this -> storeproductmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Store product status updated successfully");
        redirect('storeproducts', 'refresh');
    }

    public function export() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Export Product
            //No retailer validation for retailer users
            if ($this -> session -> userdata('user_level') < 3) {
                $this -> form_validation -> set_rules('retailers', 'retailers', 'trim|required|xss_clean');
            }
            $this -> form_validation -> set_rules('product_main_category', 'product_main_category', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('stores_list', 'stores', 'required|xss_clean');
            // $this->form_validation->set_rules('store_format_list', 'store format', 'required|xss_clean');
            // $this->form_validation->set_rules('products', 'products', 'required|xss_clean');

            if (!$this -> form_validation -> run() == FALSE) {
                //Retailer Users
                if ($this -> session -> userdata('user_level') >= 3) {
                    $retailer_id = $this -> session -> userdata('user_retailer_id');
                }
                else {
                    $retailer_id = $this -> input -> post('retailers');
                }


                //Stores List Array
                if ($this -> session -> userdata('user_type') != 6) {
                    //Stores List Array
                    $stores = $this -> input -> post('stores_list');
                }
                else {
                    $stores = array($this -> session -> userdata('user_store_id'));
                }


                $main_parent_category_id = $this -> input -> post('product_main_category');

                $retailer_details = $this -> retailermodel -> get_retailer_details($retailer_id);

                $product_ids = array();

                if ($retailer_id) {

                    //Get the products already added to the retailer
                    $retailer_products = $this -> storeproductmodel -> get_products_by_retailer($retailer_id, $stores);

                    foreach ($retailer_products as $retailer_product):
                        $product_ids[] = $retailer_product['ProductId'];
                    endforeach;
                }

                $products = $this -> productmodel -> get_products_by_category($main_parent_category_id, $product_ids);

                //load the excel library
                $this -> load -> library('excel');

                $objPHPExcel = new PHPExcel();

                $objPHPExcel -> setActiveSheetIndex(0);
                $objPHPExcel -> getActiveSheet() -> setTitle("Store Products");

                $objPHPExcel -> createSheet();

                // Initialise the Excel row number
                $rowCount = 1;

                //start of printing column names as names of MySQL fields
                $column = 'A';
                $n = 0;
                $columnName = array('', 'Product ID', 'Product Name', 'Retailer Name', 'Store', 'Store Format', 'Price', 'Store Price');
                for ($i = 1; $i < count($columnName); $i++) {
                    $objPHPExcel -> getActiveSheet() -> setCellValue($column . $rowCount, $columnName[$i]);
                    $objPHPExcel -> getActiveSheet() -> getColumnDimension($column) -> setWidth(20);
                    $objPHPExcel -> getActiveSheet() -> getStyle($column . $rowCount) -> applyFromArray(
                        array('font' => array('bold' => true, 'color' => array('rgb' => '000000'), 'size' => 12, 'name' => 'Calibri'),
                            'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')))
                        )
                    );
                    $n = $i;
                    $column++;
                }

                $rowCount = 2;
                foreach ($products as $product) {
                    foreach ($stores as $store) {

                        $store_details = $this -> storemodel -> get_store_details($store);
                        $column = 'A';
                        $objPHPExcel -> getActiveSheet() -> setCellValue($column . $rowCount, $product['Id']);
                        $column++;
                        $objPHPExcel -> getActiveSheet() -> setCellValue($column . $rowCount, $product['ProductName']);
                        $column++;
                        $objPHPExcel -> getActiveSheet() -> setCellValue($column . $rowCount, $retailer_details['CompanyName']);
                        $column++;
                        $objPHPExcel -> getActiveSheet() -> setCellValue($column . $rowCount, $store_details['StoreName']);
                        $column++;
                        $objPHPExcel -> getActiveSheet() -> setCellValue($column . $rowCount, $store_details['StoreType']);
                        $objPHPExcel -> getActiveSheet() -> getColumnDimension($column) -> setWidth(40);
                        $column++;
                        $objPHPExcel -> getActiveSheet() -> setCellValue($column . $rowCount, $product['RRP']);
                        $objPHPExcel -> getActiveSheet() -> getStyle($column . $rowCount) -> getNumberFormat() -> setFormatCode('#,##0.00');
                        $column++;
                        $rowCount++;
                    }
                }

                // Make retailer name and store column read only
                $rowCount = $rowCount - 1;
                $objPHPExcel -> getActiveSheet() -> getProtection() -> setSheet(true);
                $objPHPExcel -> getActiveSheet() -> getStyle("A2:B$rowCount") -> getProtection() -> setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
                $objPHPExcel -> getActiveSheet() -> getStyle("E2:G$rowCount") -> getProtection() -> setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

                //Redirect output to a client’s web browser (Excel5)
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="Products.xls"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter -> save('php://output');
                die();
            }
        }
    }

    public function import() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            if (!empty($_FILES['import_file']['name'])) {
                $result = $this -> do_upload_file('import_file');
                if (!isset($result['error'])) {
                    //load the excel library
                    $this -> load -> library('excel');

                    $file_path = IMPORT_FILE_PATH . $result['upload_data']['file_name'];
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file_path);

                    foreach ($objPHPExcel -> getWorksheetIterator() as $worksheet) {
                        $worksheetTitle = $worksheet -> getTitle();
                        $highestRow = $worksheet -> getHighestRow(); // e.g. 10
                        $highestColumn = $worksheet -> getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                        $nrColumns = ord($highestColumn) - 64;

                        if ($nrColumns > 1 && $highestRow > 1) {
                            $array_store_prices = array();
                            for ($row = 2; $row <= $highestRow; ++$row) {
                                if ($worksheet -> getCell("G$row") -> getValue() != '')
                                    array_push($array_store_prices, $worksheet -> getCell("G$row") -> getValue());
                            }

                            if (count($array_store_prices) != ( $highestRow - 1 )) {
                                $this -> session -> set_userdata("error_message", "The store prices for some products are not available");
                                redirect('storeproducts', 'refresh');
                            }
                            else {
                                $res_arr = array();
                                for ($row = 2; $row <= $highestRow; ++$row) {
                                    $retailer_data = $this -> retailermodel -> get_retailer_by_name($worksheet -> getCell("C$row") -> getValue());

                                    $store_data = $this -> storemodel -> get_store_by_name($worksheet -> getCell("D$row") -> getValue());
                                    $store_id = $store_data['Id'];

                                    $insert_data = array('ProductId' => $worksheet -> getCell("A$row") -> getValue(),
                                        'RetailerId' => $retailer_data['Id'],
                                        'StoreId' => $store_id,
                                        'StoreTypeId' => $store_data['StoreTypeId'],
                                        'Price' => $worksheet -> getCell("G$row") -> getValue(),
                                        'CreatedBy' => $this -> session -> userdata('user_id'),
                                        'CreatedOn' => date('Y-m-d H:i:s'),
                                        'IsActive' => 1);

                                    $result = $this -> storeproductmodel -> add_store_product($insert_data);
                                    array_push($res_arr, $result);
                                }
                                if (!in_array(0, $res_arr))
                                    $this -> session -> set_userdata('success_message', "Stores products imported successfully");
                                else
                                    $this -> session -> set_userdata('error_message', "Error while importing store products");
                                redirect('storeproducts', 'refresh');
                            }
                        }
                    }
                }
                else {
                    // code to display error while image upload
                    $this -> session -> set_userdata('error_message', $result['error']);
                    redirect('stores', 'refresh');
                }
            }
        }
    }

    public function get_retailer_categories($id) {

        $retailer_categories = $this -> categorymodel -> get_retailer_categories($id);
        $retailer_image_arr = $this -> categorymodel -> get_retailer_image($id);
        $retailer_image = '';
        if ($retailer_image_arr[0]['LogoImage']) {
            $retailer_image = front_url() . RETAILER_IMAGE_PATH . 'small/' . $retailer_image_arr[0]['LogoImage'];
        }
        $retailer_categories_results = "<option value=''>Select Category</option>";

        foreach ($retailer_categories as $retailer_categories) {
            $retailer_categories_results .= '<option value="' . $retailer_categories['Id'] . '">' . $retailer_categories['CategoryName'] . '</option>';
        }

        echo json_encode(array('retailer_categories' => $retailer_categories_results, 'retailer_image' => $retailer_image));
    }

    public function get_retailer_store_formats($retailer_id) {

        $this -> load -> model('admin/storeformatmodel');
        $store_formats = $this -> storeformatmodel -> get_store_formats($retailer_id);

        $retailer_store_format_results = '';

        $store_format_chunks = array_chunk($store_formats, 2);

        foreach ($store_format_chunks as $key => $store_formats_chunk) {

            $retailer_store_format_results .= ' <div class="col-md-6">';

            if ($key == 0) {
                $retailer_store_format_results .= '<div class="col-md-12"><div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="store_format_list[]" value="0" id="all_store_formats"><label>All Store Formats
                                                    </label>
                                                 </div>
                                            </div>
                                           </div>';
            }

            foreach ($store_formats_chunk as $store_format) {
                $retailer_store_format_results .= '  <div class="col-md-12">
                                                <div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="store_format_list[]" value="' . $store_format['Id'] . '"><label>' . $store_format['StoreType'] . '
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>';
            }

            $retailer_store_format_results .= '</div>';
        }

        echo json_encode(array('retailer_store_format' => $retailer_store_format_results));
    }

    public function get_storeformat_stores() {
        $store_format_id = isset($_POST['store_format']) ? $_POST['store_format'] : '';
        $store_results = "";

        if ($store_format_id != '') {
            $stores = $this -> storemodel -> get_stores_by_store_format($store_format_id);

            if (!empty($stores)) {
                $store_results .= '';
                $stores_chunks = array_chunk($stores, 2);

                foreach ($stores_chunks as $key => $stores_chunk) {

                    $store_results .= ' <div class="col-md-6">';

                    if ($key == 0) {
                        $store_results .= '<div class="col-md-12"><div class="row">
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" name="stores_list[]" value="0" id="all_stores"><label>All Stores
                                                    </label>
                                                 </div>
                                            </div>
                                           </div>';
                    }

                    foreach ($stores_chunk as $store) {
                        $store_results .= '  <div class="col-md-12">
                                                <div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="stores_list[]" value="' . $store['Id'] . '"><label>' . $store['StoreName'] . '
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>';
                    }

                    $store_results .= '</div>';
                }
            }
        }
//        $store_results .= '</div>';

        echo json_encode(array('retailer_store' => $store_results));
    }

    public function get_retailer_stores($retailer_id) {

        $retailer_stores = $this -> storemodel -> get_stores($retailer_id);

        $retailer_stores_results = "<option value=''>Select Store</option>";

        foreach ($retailer_stores as $retailer_stores) {
            $retailer_stores_results .= '<option value="' . $retailer_stores['Id'] . '">' . $retailer_stores['StoreName'] . '</option>';
        }

        echo json_encode(array('retailer_store' => $retailer_stores_results));
    }

    public function get_products_by_category($main_parent_category_id = 0, $retailer_id = 0) {
        //Non Admin Users
        if ($this -> session -> userdata('user_level') >= 3) {

            $retailer_id = $this -> session -> userdata('user_retailer_id');
        }

        $product_ids = array();
        //If not store user
        if ($this -> session -> userdata('user_type') != 6) {
            $store_id = $_POST['store_ids'];
        }
        else {
            $store_id = array($this -> session -> userdata('user_store_id'));
        }


        if ($retailer_id) {

            //Get the products already added to the retailer
            $retailer_products = $this -> storeproductmodel -> get_products_by_retailer($retailer_id, $store_id);

            foreach ($retailer_products as $retailer_product):
                $product_ids[] = $retailer_product['ProductId'];
            endforeach;
        }

        $categories_products = $this -> productmodel -> get_products_by_category($main_parent_category_id, $product_ids, $retailer_id);

        $categories_products_results = "";

        if ($categories_products) {
            foreach ($categories_products as $categories_products) {
                $categories_products_results .= "<tr id='det_prod_" . $categories_products['Id'] . "'>";
                $categories_products_results .= "<td>
                                                    <div class='checkbox' style='width: 15px !important;'>
                                                        <label>
                                                            <input type='checkbox' name='store_products[]' value='" . $categories_products['Id'] . "' checked>
                                                        </label>
                                                    </div>
                                                </td>";
                $categories_products_results .= "<td>" . $categories_products['ProductName'] . "</td>";
                $categories_products_results .= "<td>" . $categories_products['Brand'] . "</td>";
                //$categories_products_results .="<td>" . $categories_products['SKU'] . "</td>";
                $categories_products_results .="<td>" . ($categories_products['CategoryName'] != '' ? $categories_products['CategoryName'] . ' - ' : '') . $categories_products["parent_cat"] . "</td>";
                $categories_products_results .="<td>" . $categories_products['RRP'] . "</td>";
                $categories_products_results .="<td><input style='width: 110px;' type='text' class='form-control' value='" . $categories_products['RRP'] . "' name='product_price_" . $categories_products['Id'] . "'/></td>";
                $categories_products_results .="</tr>";
            }
        }
        else {
            $categories_products_results .= "<tr><td colspan='7' align='center'> No products avaliable </td></tr>";
        }

        echo json_encode(array('categories_products' => $categories_products_results));
    }

    public function get_products_custom() {
        //Non Admin Users
        $retailer_id = $this -> input -> post('retailer_id');
        $store_sel_id = $this -> input -> post('store_id');
        $store_type_id = $this -> input -> post('store_type_id');
        $search_product = $this -> input -> post('search_pro');
        if ($this -> session -> userdata('user_level') >= 3) {

            $retailer_id = $this -> session -> userdata('user_retailer_id');
        }

        $product_ids = array();
        //If not store user
        if ($this -> session -> userdata('user_type') != 6) {
            $store_id = $_POST['store_ids'];
        }
        else {
            $store_id = array($this -> session -> userdata('user_store_id'));
            $store_sel_id = $this -> session -> userdata('user_store_id');
        }

        if ($retailer_id && ($this -> session -> userdata('user_type') != 6)) {

            //Get the products already added to the retailer
            $retailer_products = $this -> storeproductmodel -> get_products_by_retailer($retailer_id, $store_id);

            foreach ($retailer_products as $retailer_product):
                $product_ids[] = $retailer_product['ProductId'];
            endforeach;
        }
        if ($this -> session -> userdata('user_type') == 6) {
            //Get the products already added to the store
            $store_products = $this -> storeproductmodel -> get_added_products_by_store($retailer_id, $store_type_id, $store_sel_id);
            foreach ($store_products as $store_product):
                $product_ids[] = $store_product['ProductId'];
            endforeach;
        }
        $categories_products = $this -> productmodel -> get_products_by_name($search_product, $product_ids, $retailer_id);

        $categories_products_results = "";

        if ($categories_products) {
            foreach ($categories_products as $categories_products) {
                $categories_products_results .= "<tr id='det_prod_src_" . $categories_products['Id'] . "'>";
                $categories_products_results .= "<td>
                                                    <div class='checkbox' style='width: 15px !important;'>
                                                        <label>
                                                            <input type='checkbox' name='store_products_search[]' value='" . $categories_products['Id'] . "'>
                                                        </label>
                                                    </div>
                                                </td>";
                $categories_products_results .= "<td>" . $categories_products['ProductName'] . "</td>";
                $categories_products_results .= "<td>" . $categories_products['Brand'] . "</td>";
                //$categories_products_results .="<td>" . $categories_products['SKU'] . "</td>";
                $categories_products_results .="<td>" . ($categories_products['CategoryName'] != '' ? $categories_products['CategoryName'] . ' - ' : '') . $categories_products["parent_cat"] . "</td>";
                $categories_products_results .="<td>" . $categories_products['RRP'] . "</td>";
                $categories_products_results .="<td><input type='text' class='form-control prod_prc' value='" . $categories_products['RRP'] . "' name='product_price_" . $categories_products['Id'] . "' data-main='" . $categories_products['MainCategoryId'] . "'/></td>";
                $categories_products_results .="</tr>";
            }
        }
        else {
            $categories_products_results .= "<tr><td colspan='7' align='center'> No products avaliable </td></tr>";
        }

        echo json_encode(array('categories_products' => $categories_products_results));
    }

    public function product_catalogue() {

        $data['title'] = 'Product Catalogue';

        //Update step one completed for a user.
        $step_data = array('Step2' => '1');
        $this -> storemodel -> update_wizard_step($step_data);

        $store_id = $this -> session -> userdata('user_store_id');
        $store_details = $this -> storemodel -> get_store_details($store_id);
        $data['retailer'] = $store_details['CompanyName'];
        $data['store_name'] = $store_details['StoreName'];
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/store_wizard/product_catalogue', $data);
    }

    public function product_catalogue_inherit() {
        $retailer_id = $this -> session -> userdata('user_retailer_id');

        $product_ids = array();

        $store_id = array($this -> session -> userdata('user_store_id'));


        if ($retailer_id) {

            //Get the products already added to the retailer
            $retailer_products = $this -> storeproductmodel -> get_products_by_retailer($retailer_id, $store_id);

            foreach ($retailer_products as $retailer_product):
                $product_ids[] = $retailer_product['ProductId'];
            endforeach;
        }

        $store_products = $this -> productmodel -> get_products_by_category($main_parent_category_id = 0, $product_ids);


        //Stores List Array
        if ($this -> session -> userdata('user_type') != 6) {
            //Stores List Array
            $stores = $this -> input -> post('stores_list');
        }
        else {
            $stores = array($this -> session -> userdata('user_store_id'));
        }

        foreach ($store_products as $store_product):
            foreach ($stores as $store):

                if ($store != 0) {

                    $store_format_id = $this -> session -> userdata('user_store_format_id');

                    $insert_data = array('ProductId' => $store_product['Id'],
                        'RetailerId' => $retailer_id,
                        'StoreId' => $store,
                        'StoreTypeId' => $store_format_id,
                        'Price' => number_format($store_product['RRP']),
                        'CreatedBy' => $this -> session -> userdata('user_id'),
                        'CreatedOn' => date('Y-m-d H:i:s'),
                        'IsActive' => 1);

                    $this -> storeproductmodel -> add_store_product($insert_data);
                }

            endforeach;
        endforeach;


        //Update step one completed for a user.
        $step_data = array('Step3' => '1');
        $this -> storemodel -> update_wizard_step($step_data);

        $this -> session -> set_userdata('success_message', "Product added to store successfully");
        redirect('specialproducts/welcome', 'refresh');
    }

    public function add_auto() {
        $retailer_id = $this -> session -> userdata('user_retailer_id');
        $store_id = array($this -> session -> userdata('user_store_id'));

        $data['retailer_id'] = $retailer_id;
        $data['store_id'] = $store_id;
        $data['title'] = 'Choose Categories';

        $data['categories'] = $this -> categorymodel -> get_main_categories();

        $this -> template -> view('admin/store_wizard/add_auto', $data);
    }

    public function add_auto_catalogue() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $added_count = 0;
        $this -> load -> library('excel_180');
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_id = $this -> session -> userdata('user_retailer_id');
            $store_format_id = $this -> session -> userdata('user_store_format_id');
            $store_id = $this -> session -> userdata('user_store_id');

            $added_products = $this -> storeproductmodel -> get_added_products_by_store($retailer_id, $store_format_id, $store_id);
            $product_ids = [];
            if ($added_products) {
                foreach ($added_products as $store_product):
                    $product_ids[] = $store_product['ProductId'];
                endforeach;
            }

            $categories = $this -> input -> post('select_auto_category');
            $categories_string = implode(',', $categories);

            if (!empty($categories)) {
                $products_to_add = $this -> storeproductmodel -> get_products_catalogue_add($categories, $product_ids, $retailer_id);
                if ($products_to_add) {
                    $added_count = sizeof($products_to_add);
                    $this -> session -> set_userdata('added_cat_count', $added_count);
                    $excel_data[] = array(
                        'Product Name',
                        'Main category',
                        'Parent Category',
                        'Sub category',
                        //'Default Price',
                        'Store Price',
                        'Product Id',
                        'Retailer Id',
                        'Store Id',
                        'Store Type Id',
                        'User Id'
                    );
                    foreach ($products_to_add as $product) {
                        $insert_data[] = array(
                            'ProductId' => $product['Id'],
                            'RetailerId' => $retailer_id,
                            'StoreId' => $store_id,
                            'StoreTypeId' => $store_format_id,
                            'PriceForAllStores' => 0,
                            'Price' => $product['RRP'],
                            'CreatedBy' => $this -> session -> userdata('user_id'),
                            'CreatedOn' => date('Y-m-d H:i:s'),
                            'IsActive' => 1
                        );
                        $product_details = $this -> storeproductmodel -> get_excel_details($product['Id']);
                        $excel_data[] = array(
                            'Product Name' => $product_details['ProductName'],
                            'Main category' => $product_details['main_cat'],
                            'Parent Category' => $product_details['parent_cat'],
                            'Sub category' => $product_details['category'],
                            //'default' => $product['RRP'],
                            'store_price' => $product['RRP'],
                            'ProductId' => $product['Id'],
                            'RetailerId' => $retailer_id,
                            'StoreId' => $store_id,
                            'StoreTypeId' => $store_format_id,
                            'CreatedBy' => $this -> session -> userdata('user_id'),
                        );
                    }
                    $activesheet = $this -> excel_180 -> getActiveSheet();
                    $activesheet -> fromArray($excel_data, NULL, 'A1');

                    $activesheet -> getStyle('A1:E1') -> getFont() -> setBold(true);

                    $activesheet -> getColumnDimension('F') -> setVisible(FALSE);
                    $activesheet -> getColumnDimension('G') -> setVisible(FALSE);
                    $activesheet -> getColumnDimension('H') -> setVisible(FALSE);
                    $activesheet -> getColumnDimension('I') -> setVisible(FALSE);
                    $activesheet -> getColumnDimension('J') -> setVisible(FALSE);

                    $activesheet -> getColumnDimension('A') -> setAutoSize(TRUE);
                    $activesheet -> getColumnDimension('B') -> setAutoSize(TRUE);
                    $activesheet -> getColumnDimension('C') -> setAutoSize(TRUE);
                    $activesheet -> getColumnDimension('D') -> setAutoSize(TRUE);
                    $activesheet -> getColumnDimension('E') -> setAutoSize(TRUE);
                    //$activesheet -> getColumnDimension('F') -> setAutoSize(TRUE);
                    $activesheet -> freezePane('A2');
                    $activesheet -> getStyle('A1:E1')
                        -> applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => '454545')
                                ),
                                'font' => array(
                                    'bold' => true,
                                    'color' => array('rgb' => 'FFFFFF')
                                )
                            )
                    );
                    $activesheet -> getProtection() -> setSheet(TRUE);
                    $activesheet -> getProtection() -> setInsertRows(TRUE);
                    $activesheet -> getProtection() -> setFormatCells(TRUE);
                    $activesheet -> getProtection() -> setPassword('G8#!H#t@2ZTVEW@');
                    $activesheet -> getStyle('E2:E' . $activesheet -> getHighestDataRow()) -> getProtection() -> setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
                    $filename = $this -> session -> userdata('user_id') . '.xls';
                    $writeObj = PHPExcel_IOFactory::createWriter($this -> excel_180, 'Excel5');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Cache-Control: max-age=0');
                    unlink('assets/admin/temp_files/' . $filename);
                    $writeObj -> save('assets/admin/temp_files/' . $filename);

//                    $this -> load -> library('email');
//                    $config['mailtype'] = 'html';
//
//                    $mymessage = "<!DOCTYPE HTML PUBLIC =22-//W3C//DTD HTML 4.01 Transitional//EN=22 =22http://www.w3.org/TR/html4/loose.dtd=22>";
//                    $mymessage .= "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\" />";
//                    $mymessage .= "<strong>  Dear Store User </strong><br/><br/>";
//                    $mymessage .= "You have successfully added all the selected products. <br /><br />You can find the attached excel file with all the added product details.<br /><br />You can edit the \"Store Price\" column to edit the default price. <br /><br />To import the updated file, go <a href=\"" . base_url() . "storeproducts\">Here</a>, click on \"Import Price to Store\" button, select your file and click on import button<br /></br />Thats it. You are all done<br /><br />Regards<br /><br />";
//                    $mymessage .= "<strong>The Best Deals Team</strong>" . "<br/><br/>";
//                    $mymessage .= "</body></html>";
//
//
//                    $this -> email -> initialize($config);
//                    $this -> email -> from('bella@thebestdeals.co.za');
//                    $this -> email -> to($this -> session -> userdata('user_email'));
//                    //$this -> email -> to('genknooztester1@gmail.com');
//                    $this -> email -> subject("Congratulations. You are all done");
//                    $this -> email -> message($mymessage);
//                    $this -> email -> attach('assets/admin/temp_files/' . $filename); //Attaching the file with list of all added products
//                    $this -> email -> send();




                    $this -> load -> model('admin/emailtemplatemodel');

                    $email_template_details = $this -> emailtemplatemodel -> get_email_template_details(5);

                    $emailBody = $email_template_details['Content'];
                    //$emailBody = str_replace("{LINK}", $reset_password_link, $emailBody);

                    //---- LOAD EMAIL LIBRARY ----//
                    $this -> load -> library('email');
                    $config['mailtype'] = 'html';
                    $this -> email -> initialize($config);

                    $this -> email -> from($email_template_details['FromEmail']);
                    $this -> email -> to($this -> session -> userdata('user_email'));
                    $this -> email -> subject("Congratulations. You are all done");

                    $this -> email -> message($emailBody);
                    $this -> email -> attach('assets/admin/temp_files/' . $filename); //Attaching the file with list of all added products
                    $this -> email -> send();







                    $isInsert = $this -> storeproductmodel -> add_storeproduct_batch($insert_data);
                    //$isInsert = TRUE; //uncomment abobe line and comment/delete this line to insert data. This line is only for a demonstration purpose
                    if ($isInsert) {
                        $step_data = array('Step3' => '1');
                        $this -> storemodel -> update_wizard_step($step_data);
                        $this -> result = 1;
                        $this -> message = 'Products added successfully';
                    }
                    else {
                        $this -> result = 0;
                        $this -> message = 'Failed to add products. Please try again';
                    }
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Products already added to the catalogue';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Categories should not be empty';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
            'count' => $added_count
        ));
    }

    public function get_add_count() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $retailer_id = $this -> session -> userdata('user_retailer_id');
            $store_format_id = $this -> session -> userdata('user_store_format_id');
            $store_id = $this -> session -> userdata('user_store_id');

            $added_products = $this -> storeproductmodel -> get_added_products_by_store($retailer_id, $store_format_id, $store_id);
            $product_ids = [];
            if ($added_products) {
                foreach ($added_products as $store_product):
                    $product_ids[] = $store_product['ProductId'];
                endforeach;
            }


            $categories = $this -> input -> post('select_auto_category');
            $categories_string = implode(',', $categories);

            if (!empty($categories)) {
                $products_to_add = $this -> storeproductmodel -> get_products_catalogue_add_count($categories, $product_ids, $retailer_id);
                if ($products_to_add['count']) {
                    $this -> result = 1;
                    $this -> message = $products_to_add['count'];
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Products already added to the catalogue';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Categories should not be empty';
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

    public function finish_auto() {
        $retailer_id = $this -> session -> userdata('user_retailer_id');
        $store_id = $this -> session -> userdata('user_store_id');
        $added_count = $this -> session -> userdata('added_cat_count');

        $data['retailer_id'] = $retailer_id;
        $data['store_id'] = $store_id;
        $data['added_cat_count'] = $added_count;

        $data['title'] = "Completed";

        $data['categories'] = $this -> categorymodel -> get_main_categories();

        $this -> template -> view('admin/store_wizard/finish_auto', $data);
    }

    public function importprice() {
        set_time_limit(0);
        $this -> load -> library('excel_180');
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            if (!empty($_FILES['import_price_file']['name'])) {
                $mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                if (in_array($_FILES['import_price_file']['type'], $mimes)) {
                    $fileName = $_FILES['import_price_file']['tmp_name'];
                    try {
                        $fileType = PHPExcel_IOFactory::identify($fileName);
                        $objReader = PHPExcel_IOFactory::createReader($fileType);
                        $objPHPExcel = $objReader -> load($fileName);
                        $hash = $objPHPExcel -> getActiveSheet() -> getProtection() -> getPassword(); // returns a hash
                        $valid = ($hash === PHPExcel_Shared_PasswordHasher::hashPassword('G8#!H#t@2ZTVEW@'));

                        if ($valid) {
                            $document = PHPExcel_IOFactory::load($fileName);
                            $activeSheetData = $document -> getActiveSheet() -> toArray(null, true, true, true);
                            if ($activeSheetData[1]) {
                                unset($activeSheetData[1]);
                            }
                            $insertNum = 0;
                            if (!empty($activeSheetData)) {
                                if ($activeSheetData[2]['J'] == $this -> session -> userdata('user_id')) {
                                    foreach ($activeSheetData as $row) {
                                        if ($row['F'] > 0) {
                                            $where = array(
                                                'ProductId' => $row['F'],
                                                'RetailerId' => $row['G'],
                                                'StoreId' => $row['H'],
                                                'StoreTypeId' => $row['I']
                                            );
                                            $update_data = array(
                                                'Price' => $row['E']
                                            );
                                            $isUpdate = $this -> storeproductmodel -> update_store_price($update_data, $where);
                                            if ($isUpdate) {
                                                $insertNum++;
                                            }
                                        }
                                    }
                                    if ($insertNum > 0) {
                                        $this -> session -> set_userdata('success_message', 'Prices updated successfully');
                                        redirect('storeproducts', 'refresh');
                                    }
                                    else {
                                        $this -> session -> set_userdata('error_message', 'No prices where updated');
                                        redirect('storeproducts', 'refresh');
                                    }
                                }
                                else {
                                    $this -> session -> set_userdata('error_message', 'Invalid file found');
                                    redirect('storeproducts', 'refresh');
                                }
                            }
                            else {
                                $this -> session -> set_userdata('error_message', 'No records found to import');
                                redirect('storeproducts', 'refresh');
                            }
                        }
                        else {
                            $this -> session -> set_userdata('error_message', 'Invalid file found');
                            redirect('storeproducts', 'refresh');
                        }
                    }
                    catch (Exception $e) {
                        $this -> session -> set_userdata('error_message', 'Import failed');
                        redirect('storeproducts', 'refresh');
                    }
                }
                else {
                    $this -> session -> set_userdata('error_message', 'Invalid file format');
                    redirect('storeproducts', 'refresh');
                }
            }
        }
        else {
            redirect('storeproducts', 'refresh');
        }
        $this -> load -> library('excel_180');
    }

    public function update_store_price() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $price_list = json_decode($this -> input -> post('price_list'));
            $price_error = 0;
            $update_count = 0;
            $product_name = '';
            $product_id = '';
            $retailer_name = '';
            $retailer_id = '';
            $store_name = '';
            $store_id = '';
            $store_type_name = '';
            $store_type_id = '';
            $price = '';
            $store_array = [];
            $store_detail_array = [];
            $product_array = [];
            if (!empty($price_list)) {
                foreach ($price_list as $id => $price) {
                    if ($price > 0 && is_numeric($price)) {
                        $where = array(
                            'Id' => $id
                        );
                        $update_data = array(
                            'Price' => $price,
                            'IsNew' => '0'
                        );
                        $isUpdate = $this -> storeproductmodel -> update_store_price($update_data, $where);
                        $store_product_data = $this -> storeproductmodel -> get_store_data_single($id);
                        if ($isUpdate) {
                            $product_name = $store_product_data['ProductName'];
                            $product_id = $store_product_data['ProductId'];
                            $retailer_name = $store_product_data['CompanyName'];
                            $retailer_id = $store_product_data['RetailerId'];
                            $store_name = $store_product_data['StoreName'];
                            $store_id = $store_product_data['StoreId'];
                            $store_type_name = $store_product_data['StoreType'];
                            $store_type_id = $store_product_data['StoreTypeId'];
                            $price = $store_product_data['Price'];
                            $store_array[] = $store_product_data['StoreId'];
                            $store_detail_array[] = array(
                                'id' => $store_product_data['StoreId'],
                                'name' => $store_product_data['StoreName'],
                                'retailer' => $store_product_data['RetailerId'],
                                'storeType' => $store_product_data['StoreTypeId']
                            );
                            $product_array[] = array(
                                'id' => $store_product_data['ProductId'],
                                'name' => $store_product_data['ProductName']
                            );
                            $update_count++;
                        }
                    }
                    else {
                        $price_error++;
                    }
                }
                //create_change_push_message($product_id, $retailer_id, $store_type_id, $store_id, $product_name, $retailer_name, $store_name, $store_type_name, $price, $update_count, $store_array, $product_array, $store_detail_array);
                if ($price_error > 0) {
                    $this -> result = 0;
                    $this -> message = 'Some/all details failed to update. Invalid price found';
                }
                elseif ($update_count > 0) {
                    $this -> session -> set_userdata('success_message', 'Prices updated successfully');
                    $this -> result = 1;
                    $this -> message = 'Prices updated successfully';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'No prices found to update';
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
    
    /*  Function to update product price in all stores of the retailer */
    public function change_price_in_all_stores() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $storeProductId = isset($_POST['id']) ? $_POST['id'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $retailer_id = $this -> session -> userdata('user_retailer_id');
        
        $this -> storeproductmodel -> change_price_in_all_stores($storeProductId, $price, $retailer_id);
        $this -> session -> set_userdata('success_message', "Product price in all store updated successfully");
        
        $this -> result = 1;
        $this -> message = 'Product price in all store updated successfully';
        
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
    
}
