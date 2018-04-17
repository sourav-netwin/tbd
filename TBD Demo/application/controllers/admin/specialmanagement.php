<?php

/*
 * Author:AS
 * Purpose:Store Catalogue Controller - Store format user login
 * Date:27-10-2015
 * Dependency: specialmanagement.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
class Specialmanagement extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();
		//$this->load->library('session');
        $this -> load -> model('admin/storemodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);
        $this -> load -> model('admin/statemodel', '', TRUE);

        $this -> load -> model('admin/storeformatmodel', '', TRUE);
        $this -> load -> model('admin/specialproductmodel', '', TRUE);
		
        $this -> page_title = "Manage Specials";
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    public function index() {
        $data['title'] = $this -> page_title;
        $this -> breadcrumbs[0] = array('label' => 'Manage Your Specials', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => $this -> page_title, 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['specials'] = $this -> specialproductmodel -> get_special_details();
        $data['years'] = $this -> specialproductmodel -> get_years(); 
        
        $data['yearId'] = date('Y');
        $data['monthId'] = date('m'); 
                
        $this -> template -> view('admin/special_management/index', $data);
    }

    public function add() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            
            $this -> form_validation -> set_rules('special_name', 'Special Name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('price_from', 'price from', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('price_to', 'price to', 'trim|required|xss_clean');
            if (!$this -> form_validation -> run() == FALSE) {
                $is_retailer = $this -> session -> userdata('user_type') == 3 ? '1' : '0' ;
                $is_storetype = $this -> session -> userdata('user_type') == 5 ? '1' : '0' ;
                $is_store = $this -> session -> userdata('user_type') == 6 ? '1' : '0' ;
                $is_regional_special = $this -> input -> post('regional_special') > 0 ? 1 : 0 ;
                
                if( $is_regional_special == 1 &&  $this -> session -> userdata('user_type') == 3 )
                {
                    $is_store = 1;
                }
                
                $special_name = $this -> input -> post('special_name');
                $special_from = $this -> input -> post('price_from');
                $special_to = $this -> input -> post('price_to');
                $special_terms = $this -> input -> post('sp_t_and_c');

                $all_states = $this -> input -> post('all_states');
                $all_stores = $this -> input -> post('all_stores');

                $states = $this -> input -> post('state_special_list');
                $stores = $this -> input -> post('store_special_list');
                $all_store_types = '';
                $store_types = [];
                if ($this -> session -> userdata('user_type') == 3) {
                    $all_store_types = $this -> input -> post('all_store_formats');
                    $store_types = $this -> input -> post('store_special_format_list');
                }
                if (($all_states == 1 || !empty($states)) && ($all_stores == 1 || !empty($stores))) {
                    if (!empty($special_terms)) {
                        $special_terms = implode(',', $special_terms);
                    }
                    $file_uploaded = TRUE;
                    $image_name = '';
                    if (!$_FILES['spbanner_image']['size'] == 0) {
                        $result = $this -> do_upload('spbanner_image', 'specials', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                        if (isset($result['error'])) {
                            $file_uploaded = FALSE;
                        }
                        else {
                            $image_name = $result['upload_data']['file_name'];
                        }
                    }
                    
                    # Check if special is already exists for the given date range for the particular stores.
                    if($is_regional_special == 0){
                        $isSpecialExists = $this -> specialproductmodel -> check_special_exists($special_from,$special_to, $stores,0,$is_regional_special);
                    }else{
                        $isSpecialExists = 0;
                    }
                    
                    $isSpecialExists = 0; ///this line added by priyanka on date 26th july 17
                    if ($isSpecialExists == 0 ) {                   
                    
                        if ($file_uploaded) {
                            $insert_data = array(
                                'SpecialName' => $special_name,
                                'SpecialBanner' => $image_name,
                                'SpecialFrom' => $special_from,
                                'SpecialTo' => $special_to,
                                'TermsAndConditions' => $special_terms,
                                'IsRetailer' => $is_retailer,
                                'IsStoreType' => $is_storetype,
                                'IsStore' => $is_store,
                                'IsRegional' => $is_regional_special
                            );
                            $isInsert = $this -> specialproductmodel -> insert_special_data($insert_data);
                            if ($isInsert) {

                                if ($all_states == 1) {
                                    $insert_data = array(
                                        'AllStates' => 1,
                                        'SpecialId' => $isInsert,
                                        'StateId' => 0
                                    );
                                    $this -> specialproductmodel -> insert_special_state($insert_data);
                                }
                                else {
                                    $insert_data = [];
                                    foreach ($states as $state) {
                                        $insert_data[] = array(
                                            'SpecialId' => $isInsert,
                                            'StateId' => $state,
                                            'AllStates' => 0
                                        );
                                    }
                                    $this -> specialproductmodel -> insert_special_state_batch($insert_data);
                                }
                                if ($all_stores == 1) {
                                    $insert_data = [];
                                    foreach ($stores as $store) {
                                        $store_arr = explode(':', $store);
                                        $insert_data[] = array(
                                            'SpecialId' => $isInsert,
                                            'RetailerId' => $this -> session -> userdata('user_retailer_id'),
                                            'StoreTypeId' => $store_arr[0],
                                            'StoreId' => $store_arr[1],
                                            'AllStoreTypes' => $all_store_types,
                                            'AllStores' => 0
                                        );
                                    }
                                    $this -> specialproductmodel -> insert_special_store_batch($insert_data);
                                }
                                else {
                                    $insert_data = [];
                                    foreach ($stores as $store) {
                                        $store_arr = explode(':', $store);
                                        $insert_data[] = array(
                                            'SpecialId' => $isInsert,
                                            'RetailerId' => $this -> session -> userdata('user_retailer_id'),
                                            'StoreTypeId' => $store_arr[0],
                                            'StoreId' => $store_arr[1],
                                            'AllStoreTypes' => $all_store_types,
                                            'AllStores' => 0
                                        );
                                    }
                                    $this -> specialproductmodel -> insert_special_store_batch($insert_data);
                                }

                                $this -> session -> set_userdata('success_message', 'Special added successfully');
                                $this -> result = 1;
                                $this -> message = 'Special added successfully';
                                redirect('/specialmanagement');
                                exit(0);
                            }
                            else {
                                $this -> session -> set_userdata('error_message', 'Failed to add special');
                                $this -> result = 0;
                                $this -> message = 'Failed to add special';
                            }
                        }
                        else {
                            $this -> session -> set_userdata('error_message', 'Failed to upload the banner');
                            $this -> result = 0;
                            $this -> message = 'Failed to upload the banner';
                        }
                    }else{                    
                        $this -> session -> set_userdata('error_message', 'Special already added for the given dates for the given stores.');
                        $this -> result = 0;
                        $this -> message = 'Special already added for the given dates for the given stores.';                        
                    }
                }
            }
        }
        $data['store_state'] = '';
        if ($this -> session -> userdata('user_type') == 6) {
            $data['store_state'] = $this -> specialproductmodel -> get_store_state($this -> session -> userdata('user_store_id'));
        }
        $data['title'] = $this -> page_title;
        $this -> breadcrumbs[0] = array('label' => 'Manage Your Specials', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => $this -> page_title, 'url' => '/specialmanagement');
        $this -> breadcrumbs[2] = array('label' => 'Add', 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['specials'] = $this -> specialproductmodel -> get_special_details();
        $data['terms'] = $this -> specialproductmodel -> get_terms_details();
        $data['states'] = $this -> statemodel -> get_states();
        $this -> template -> view('admin/special_management/add', $data);
    }

    public function edit($id) {

        $data = $this -> specialproductmodel -> get_special_details_id($id);
        
        $this -> breadcrumbs[] = array('label' => 'Edit Special', 'url' => 'specialmanagement/edit/' . $id);
        $data['terms'] = $this -> specialproductmodel -> get_terms_details();

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;
        $data['states'] = $this -> statemodel -> get_states();
        $data['selected_states'] = $this -> specialproductmodel -> get_selected_states($id);
        $data['selected_stores'] = $this -> specialproductmodel -> get_selected_stores($id);
        $data['selected_store_types'] = $this -> specialproductmodel -> get_selected_store_types($id);


        $data['all_states'] = FALSE;
        $data['all_stores'] = FALSE;
        $data['all_store_types'] = FALSE;
        $data['selected_state_list'] = '';
        $data['selected_store_list'] = '';
        $data['selected_store_type_list'] = '';
        if ($data['selected_states']) {
            if (sizeof($data['selected_states']) == 1) {
                if ($data['selected_states'][0]['AllStates'] == 1) {
                    $data['all_states'] = TRUE;
                }
                else {
                    $data['selected_state_list'][] = $data['selected_states'][0]['StateId'];
                }
            }
            else {
                foreach ($data['selected_states'] as $sel_state) {
                    $data['selected_state_list'][] = $sel_state['StateId'];
                }
            }
        }

        if ($data['selected_store_types']) {
            if (sizeof($data['selected_store_types']) == 1) {
                if ($data['selected_store_types'][0]['AllStoreTypes'] == 1) {
                    $data['all_store_types'] = TRUE;
                }
                else {
                    $data['selected_store_type_list'][] = $data['selected_store_types'][0]['StoreTypeId'];
                }
            }
            else {
                foreach ($data['selected_store_types'] as $sel_store_type) {
                    if ($sel_store_type['AllStoreTypes'] == 1) {
                        $data['all_store_types'] = TRUE;
                        break;
                    }
                    else {
                        $data['selected_store_type_list'][] = $sel_store_type['StoreTypeId'];
                    }
                }
            }
        }
        if ($data['selected_stores']) {
            if ($data['selected_stores'][0]['AllStores'] == 1) {
                $data['all_stores'] = TRUE;
            }
            foreach ($data['selected_stores'] as $sel_store) {
                $data['selected_store_list'][] = $sel_store['StoreId'];
            }
        }

        $data['stores'] = $this -> specialproductmodel -> get_state_stores($data['all_states'], $data['selected_state_list'], '', $data['all_store_types'], $data['selected_store_type_list']);
        $data['store_formats'] = $this -> specialproductmodel -> get_state_storeformats($data['all_states'], $data['selected_state_list']);
        $data['store_state'] = '';
        if ($this -> session -> userdata('user_type') == 6) {
            $data['store_state'] = $this -> specialproductmodel -> get_store_state($this -> session -> userdata('user_store_id'));
        }

        $html = $this -> load -> view('admin/special_management/edit', $data, true);
        $name = $data['SpecialName'];
        echo json_encode(array(
            'html' => $html,
            'name' => $name
        ));
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> form_validation -> set_rules('special_name', 'Special Name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this -> form_validation -> set_rules('price_from', 'price from', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('price_to', 'price to', 'trim|required|xss_clean');
            if (!$this -> form_validation -> run() == FALSE) {
                $is_regional_special     = $this -> input -> post('regional_special') > 0 ? '1' : '0' ;                
                $special_name = $this -> input -> post('special_name');
                $special_from = $this -> input -> post('price_from');
                $special_to = $this -> input -> post('price_to');
                $special_terms = $this -> input -> post('sp_t_and_c');

                $all_states = $this -> input -> post('all_states');
                $all_stores = $this -> input -> post('all_stores');

                $states = $this -> input -> post('state_special_list');
                $stores = $this -> input -> post('store_special_list');
                $all_store_types = '';
                $store_types = [];
                if ($this -> session -> userdata('user_type') == 3) {
                    $all_store_types = $this -> input -> post('all_store_formats');
                    $store_types = $this -> input -> post('store_special_format_list');
                }

                if (($all_states == 1 || !empty($states)) && ($all_stores == 1 || !empty($stores))) {
                    if (!empty($special_terms)) {
                        $special_terms = implode(',', $special_terms);
                    }
                    $file_uploaded = TRUE;
                    $image_name = '';
                    if (!$_FILES['spbanner_image']['size'] == 0) {
                        $result = $this -> do_upload('spbanner_image', 'specials', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                        if (isset($result['error'])) {
                            $file_uploaded = FALSE;
                        }
                        else {
                            $image_name = $result['upload_data']['file_name'];
                        }
                    }
                    
                    # Check if special is already exists for the given date range for the particular stores.
                    if($is_regional_special == 0){
                        $isSpecialExists = $this -> specialproductmodel -> check_special_exists($special_from,$special_to, $stores,$id,$is_regional_special);
                    }else{
                        $isSpecialExists = 0;
                    }
                    $isSpecialExists = 0; //this line added priyanka on 26th july 2017
                    
                    if ($isSpecialExists == 0 ) { 
                        
                        if ($file_uploaded) {
                            $update_data = array(
                                'SpecialName' => $special_name,
                                'SpecialFrom' => $special_from,
                                'SpecialTo' => $special_to,
                                'TermsAndConditions' => $special_terms
                            );
                            if ($image_name) {
                                $update_data['SpecialBanner'] = $image_name;
                            }
                            
                            if( $this -> session -> userdata('user_type') == 3 )
                            {
                                if($is_regional_special == 1)
                                {
                                    $update_data['IsRegional'] = $is_regional_special;
                                    $update_data['IsStore'] = 1;
                                }else{
                                    $update_data['IsRegional'] = $is_regional_special;
                                    $update_data['IsStore'] = 0;
                                }
                            }
                                    
                            $isUpdate = $this -> specialproductmodel -> update_special_data($update_data, $id);
                            $upd_dat = array(
                                'PriceAppliedFrom' => $special_from,
                                'PriceAppliedTo' => $special_to
                            );
                            $this -> specialproductmodel -> update_special_product($id, $upd_dat);
                            if ($isUpdate) {
                                $where['SpecialId'] = $id;
                                $where['RetailerId'] = $this -> session -> userdata('user_retailer_id');
                                if ($this -> session -> userdata('user_type') == 5) {
                                    $where['StoreTypeId'] = $this -> session -> userdata('user_store_format_id');
                                }
                                if ($this -> session -> userdata('user_type') == 6) {
                                    $where['StoreId'] = $this -> session -> userdata('user_store_id');
                                }

                                $is_state_delete = $this -> specialproductmodel -> delete_special_state(array('SpecialId' => $id));
                                $is_store_delete = $this -> specialproductmodel -> delete_special_store($where);
                                if ($all_states == 1) {
                                    $insert_data = array(
                                        'AllStates' => 1,
                                        'SpecialId' => $id,
                                        'StateId' => 0
                                    );
                                    $this -> specialproductmodel -> insert_special_state($insert_data);
                                }
                                else {
                                    $insert_data = [];
                                    foreach ($states as $state) {
                                        $insert_data[] = array(
                                            'SpecialId' => $id,
                                            'StateId' => $state,
                                            'AllStates' => 0
                                        );
                                    }
                                    $this -> specialproductmodel -> insert_special_state_batch($insert_data);
                                }
                                if ($all_stores == 1) {
                                    $insert_data = [];
                                    foreach ($stores as $store) {
                                        $store_arr = explode(':', $store);
                                        $insert_data[] = array(
                                            'AllStores' => 1,
                                            'RetailerId' => $this -> session -> userdata('user_retailer_id'),
                                            'SpecialId' => $id,
                                            'StoreId' => $store_arr[1],
                                            'StoreTypeId' => $store_arr[0],
                                            'AllStoreTypes' => $all_store_types
                                        );
                                    }
    
                                    $this -> specialproductmodel -> insert_special_store_batch($insert_data);
                                }
                                else {
                                    $insert_data = [];

                                    foreach ($stores as $store) {
                                        $store_arr = explode(':', $store);
                                        $insert_data[] = array(
                                            'SpecialId' => $id,
                                            'RetailerId' => $this -> session -> userdata('user_retailer_id'),
                                            'StoreId' => $store_arr[1],
                                            'AllStores' => 0,
                                            'StoreTypeId' => $store_arr[0],
                                            'AllStoreTypes' => $all_store_types
                                        );
                                    }
                                    $this -> specialproductmodel -> insert_special_store_batch($insert_data);
                                }
                                $this -> session -> set_userdata('success_message', 'Special updated successfully');
                                $this -> result = 1;
                                $this -> message = 'Special updated successfully';
                            }
                            else {
                                $this -> session -> set_userdata('error_message', 'Failed to update special');
                                $this -> result = 0;
                                $this -> message = 'Failed to update special';
                            }
                        }
                        else {
                            $this -> session -> set_userdata('error_message', 'Failed to upload the banner');
                            $this -> result = 0;
                            $this -> message = 'Failed to upload the banner';
                        }                    
                    }else{
                        $this -> session -> set_userdata('error_message', 'Special already added for the given dates for the given stores.');
                        $this -> result = 0;
                        $this -> message = 'Special already added for the given dates for the given stores.';                    
                    }
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'State and Stores are mandatory';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'There are errors in the form data. Please clear them and try again';
            }
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

    public function delete_special() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $id = $this -> input -> post('id');
            if ($id) {
                $isDeleted = $this -> specialproductmodel -> delete_special($id);
                if ($isDeleted) {
                    $this -> session -> set_userdata('success_message', 'Special deleted successfully');
                    $this -> result = 1;
                    $this -> message = 'Special deleted successfully';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to delete special';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Invalid request';
            }
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

    public function add_special_new() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            if ($this -> input -> post('special_name_sel') == '') {
                $this -> form_validation -> set_rules('special_name', 'Special Name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
                $this -> form_validation -> set_rules('price_from', 'price from', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('price_to', 'price to', 'trim|required|xss_clean');
            }
            else {
                $this -> form_validation -> set_rules('special_name_sel', 'Special', 'trim|required|numeric|xss_clean');
                $this -> form_validation -> set_rules('special_name', 'Special Name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
                $this -> form_validation -> set_rules('price_from', 'price from', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('price_to', 'price to', 'trim|required|xss_clean');
            }


            if (!$this -> form_validation -> run() == FALSE) {
                $special_sel = $this -> input -> post('special_name_sel');
                $special_name = $this -> input -> post('special_name');
                $special_from = $this -> input -> post('price_from');
                $special_to = $this -> input -> post('price_to');

                if ($special_sel) {
                    $file_uploaded = TRUE;
                    $image_name = '';
                    if (!$_FILES['spbanner_image']['size'] == 0) {
                        $result = $this -> do_upload('spbanner_image', 'specials', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                        if (isset($result['error'])) {
                            $file_uploaded = FALSE;
                        }
                        else {
                            $image_name = $result['upload_data']['file_name'];
                        }
                    }
                    if ($file_uploaded) {
                        $update_data = array(
                            'SpecialName' => $special_name,
                            'SpecialFrom' => $special_from,
                            'SpecialTo' => $special_to
                        );
                        if ($image_name) {
                            $update_data['SpecialBanner'] = $image_name;
                        }
                        $isUpdate = $this -> specialproductmodel -> update_special_data($update_data, $special_sel);
                        if ($isUpdate) {
                            $this -> session -> set_userdata('special_sel', $special_sel);
                            $this -> result = 1;
                            $this -> message = 'Special updated successfully';
                        }
                        else {
                            $this -> result = 0;
                            $this -> message = 'Failed to update special';
                        }
                    }
                    else {
                        $this -> result = 0;
                        $this -> message = 'Failed to upload the banner';
                    }
                }
                else {
                    $file_uploaded = TRUE;
                    $image_name = '';
                    if (!$_FILES['spbanner_image']['size'] == 0) {
                        $result = $this -> do_upload('spbanner_image', 'specials', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                        if (isset($result['error'])) {
                            $file_uploaded = FALSE;
                        }
                        else {
                            $image_name = $result['upload_data']['file_name'];
                        }
                    }
                    if ($file_uploaded) {
                        $insert_data = array(
                            'SpecialName' => $special_name,
                            'SpecialBanner' => $image_name,
                            'SpecialFrom' => $special_from,
                            'SpecialTo' => $special_to
                        );
                        $isInsert = $this -> specialproductmodel -> insert_special_data($insert_data);
                        if ($isInsert) {
                            $this -> session -> set_userdata('special_sel', $isInsert);
                            $this -> result = 1;
                            $this -> message = 'Special added successfully';
                        }
                        else {
                            $this -> result = 0;
                            $this -> message = 'Failed to add special';
                        }
                    }
                    else {
                        $this -> result = 0;
                        $this -> message = 'Failed to upload the banner';
                    }
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'There are errors in the form. Please clear them and submit again';
            }
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

    function validate_name($name) {
        $this -> form_validation -> set_message('validate_name', 'Name must contain contain only letters, apostrophe, spaces or dashes.');
        if (preg_match("/^[a-zA-Z0-9'\-\s]+$/", $name)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function datatable() {

        $this -> datatables -> select("a.Id as p_id,g.CategoryName as MainCategory,h.CategoryName as ParentCategory,i.CategoryName as Category,b.ProductName,d.StoreName,b.RRP,case when f.SpecialPrice IS NULL then concat('<input class=\"prod_prc\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id, '\" value=\"0.00\" />') else concat('<input class=\"prod_prc\" style=\"width:65px\" type=\"text\" id=\"product_price_',a.Id,'\" value=\"',f.SpecialPrice,'\" />') end as SpecialPrice,
            case 
                when f.SpecialQty IS NULL then " . specials_get_select(0) . "
                when f.SpecialQty = 1 then " . specials_get_select(1) . "
                when f.SpecialQty = 2 then " . specials_get_select(2) . "
                when f.SpecialQty = 3 then " . specials_get_select(3) . "
                when f.SpecialQty = 4 then " . specials_get_select(4) . "
                when f.SpecialQty = 5 then " . specials_get_select(5) . "
                when f.SpecialQty = 6 then " . specials_get_select(6) . "
                when f.SpecialQty = 7 then " . specials_get_select(7) . "
                when f.SpecialQty = 8 then " . specials_get_select(8) . "
                when f.SpecialQty = 9 then " . specials_get_select(9) . "
                when f.SpecialQty = 10 then " . specials_get_select(10) . "
                when f.SpecialQty > 10 then " . specials_get_select(11) . " end
                    as SpecialQty,
            DATE_FORMAT(f.PriceAppliedFrom,'%d/%c/%Y') as PriceAppliedFrom,DATE_FORMAT(f.PriceAppliedTo,'%d/%c/%Y') as PriceAppliedTo", false)
            -> from('storeproducts as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('retailers as c', 'c.Id = a.RetailerId')
            -> join('stores as d', 'd.Id = a.StoreId')
            -> join('storestypes as e', 'e.Id = a.StoreTypeId')
            -> join('productspecials as f', 'f.ProductId = b.Id and f.RetailerId = c.Id and f.StoreId = d.Id and f.StoreTypeId = e.Id and (now() between f.PriceAppliedFrom and f.PriceAppliedTo)', 'left')
            -> join('categories as g', 'g.Id = b.MainCategoryId', 'left')
            -> join('categories as h', 'h.Id = b.ParentCategoryId', 'left')
            -> join('categories as i', 'i.Id = b.CategoryId', 'left')
            -> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'))
            -> add_column('selectVal', '<input type="checkbox"  name="store_products[]" value="$1" />', 'p_id');

        echo $this -> datatables -> generate();
    }

    public function special_datatable($yearId =0, $monthId = 0) {
        
        $this->db->_protect_identifiers=false;
        
        if ($this -> session -> userdata('user_type') == 3) {
            
            $this -> datatables -> select("a.Id as Id,concat('<a href=\"" . front_url() . "admin/specialmanagement/managespecials/',a.Id,'\">',SpecialName,'</a>') as SpecialName,CASE WHEN a.SpecialBanner = '' OR a.SpecialBanner IS NULL THEN '' ELSE CONCAT('<img src=\"" . front_url() . SPECIAL_IMAGE_PATH . "small/',a.SpecialBanner,'\" style=\"height:45px\" />') END AS SpecialBanner,DATE_FORMAT(a.SpecialFrom,'%d/%m/%Y') as SpecialFrom,DATE_FORMAT(a.SpecialTo,'%d/%m/%Y') as SpecialTo,a.TermsAndConditions, a.IsActive as active,
                                         0 AS sum_count, 
                                         count(distinct c.StoreId) as store_count", false);
            $this -> datatables -> from('specials as a');
            $this -> datatables -> where('a.IsRemoved', 0);
            
            if($yearId > 0 )
            {
                $this -> datatables -> where('YEAR(a.SpecialFrom)', $yearId);
            }
            
            if($monthId > 0 )
            {
                $this -> datatables -> where('MONTH(a.SpecialFrom)', $monthId);
            }
                        
            $this -> datatables -> add_column('Actions', special_get_buttons('$1'), 'Id');
            $this -> datatables -> join('special_stores as c', 'c.SpecialId = a.Id and a.IsRetailer = 1 and c.RetailerId = '. $this -> session -> userdata('user_retailer_id'));
            
         }
        if ($this -> session -> userdata('user_type') == 5) {  
            $this -> datatables -> select("a.Id as Id,concat('<a href=\"" . front_url() . "admin/specialmanagement/managespecials/',a.Id,'\">',SpecialName,'</a>') as SpecialName,CASE WHEN a.SpecialBanner = '' OR a.SpecialBanner IS NULL THEN '' ELSE CONCAT('<img src=\"" . front_url() . SPECIAL_IMAGE_PATH . "small/',a.SpecialBanner,'\" style=\"height:45px\" />') END AS SpecialBanner,DATE_FORMAT(a.SpecialFrom,'%d/%m/%Y') as SpecialFrom,DATE_FORMAT(a.SpecialTo,'%d/%m/%Y') as SpecialTo,a.TermsAndConditions, a.IsActive as active,
                                          ( SELECT COUNT( distinct h.ProductId ) FROM productspecials AS h WHERE h.SpecialId = a.Id AND h.RetailerId =".$this -> session -> userdata('user_retailer_id')." and h.StoreTypeId = " . $this -> session -> userdata('user_store_format_id') .") AS sum_count, 
                                          count(distinct c.StoreId) as store_count", false);
           $this -> datatables -> from('specials as a');            
           $this -> datatables -> where('a.IsRemoved', 0);
           
           if($yearId > 0 )
            {
                $this -> datatables -> where('YEAR(a.SpecialFrom)', $yearId);
            }
            
            if($monthId > 0 )
            {
                $this -> datatables -> where('MONTH(a.SpecialFrom)', $monthId);
            }
            
           $this -> datatables -> add_column('Actions', special_get_buttons('$1'), 'Id');
            $this -> datatables -> join('special_stores as c', 'c.SpecialId = a.Id and a.IsStoreType = 1 and c.RetailerId = '. $this -> session -> userdata('user_retailer_id') . ' and c.StoreTypeId = ' . $this -> session -> userdata('user_store_format_id'));
        }
        if ($this -> session -> userdata('user_type') == 6) {            
            $this -> datatables -> select("a.Id as Id,concat('<a href=\"" . front_url() . "admin/specialmanagement/managespecials/',a.Id,'\">',SpecialName,'</a>') as SpecialName,CASE WHEN a.SpecialBanner = '' OR a.SpecialBanner IS NULL THEN '' ELSE CONCAT('<img src=\"" . front_url() . SPECIAL_IMAGE_PATH . "small/',a.SpecialBanner,'\" style=\"height:45px\" />') END AS SpecialBanner,DATE_FORMAT(a.SpecialFrom,'%d/%m/%Y') as SpecialFrom,DATE_FORMAT(a.SpecialTo,'%d/%m/%Y') as SpecialTo,a.TermsAndConditions, a.IsActive as active,
                                          ( SELECT COUNT( distinct h.ProductId ) FROM productspecials AS h WHERE h.SpecialId = a.Id AND h.RetailerId =".$this -> session -> userdata('user_retailer_id')." and h.StoreTypeId = " . $this -> session -> userdata('user_store_format_id') ." and h.StoreId = " . $this -> session -> userdata('user_store_id') .") AS sum_count, 
                                          count(distinct c.StoreId) as store_count", false);
            $this -> datatables -> from('specials as a');            
            $this -> datatables -> where('a.IsRemoved', 0);
            
            if($yearId > 0 )
            {
                $this -> datatables -> where('YEAR(a.SpecialFrom)', $yearId);
            }
            
            if($monthId > 0 )
            {
                $this -> datatables -> where('MONTH(a.SpecialFrom)', $monthId);
            }
            
            $this -> datatables -> add_column('Actions', special_get_buttons('$1'), 'Id');
            
            $this -> datatables -> join('special_stores as c', 'c.SpecialId = a.Id and a.IsStore = 1 and c.RetailerId = '. $this -> session -> userdata('user_retailer_id') . ' and c.StoreTypeId = ' . $this -> session -> userdata('user_store_format_id') . ' and c.StoreId = ' . $this -> session -> userdata('user_store_id'));
        }
        $this -> datatables -> group_by('a.Id');

        echo $this -> datatables -> generate();
    }
    
    
    public function special_datatable_working() {
        
        $this->db->_protect_identifiers=false;
        
        if ($this -> session -> userdata('user_type') == 3) {
            $this -> datatables -> select("a.Id as Id,concat('<a href=\"" . front_url() . "admin/specialmanagement/managespecials/',a.Id,'\">',SpecialName,'</a>') as SpecialName,CASE WHEN a.SpecialBanner = '' OR a.SpecialBanner IS NULL THEN '' ELSE CONCAT('<img src=\"" . front_url() . SPECIAL_IMAGE_PATH . "small/',a.SpecialBanner,'\" style=\"height:45px\" />') END AS SpecialBanner,DATE_FORMAT(a.SpecialFrom,'%d/%m/%Y') as SpecialFrom,DATE_FORMAT(a.SpecialTo,'%d/%m/%Y') as SpecialTo,a.TermsAndConditions, a.IsActive as active,
                                         ( SELECT COUNT( distinct h.ProductId ) FROM productspecials AS h WHERE h.SpecialId = a.Id AND h.RetailerId =".$this -> session -> userdata('user_retailer_id').") AS sum_count, 
                                         count(distinct c.StoreId) as store_count", false)
            -> from('specials as a')            
            -> where('a.IsRemoved', 0)
            -> add_column('Actions', special_get_buttons('$1'), 'Id');
            $this -> datatables -> join('special_stores as c', 'c.SpecialId = a.Id and a.IsRetailer = 1 and c.RetailerId = '. $this -> session -> userdata('user_retailer_id'));
         }
        if ($this -> session -> userdata('user_type') == 5) {  
            $this -> datatables -> select("a.Id as Id,concat('<a href=\"" . front_url() . "admin/specialmanagement/managespecials/',a.Id,'\">',SpecialName,'</a>') as SpecialName,CASE WHEN a.SpecialBanner = '' OR a.SpecialBanner IS NULL THEN '' ELSE CONCAT('<img src=\"" . front_url() . SPECIAL_IMAGE_PATH . "small/',a.SpecialBanner,'\" style=\"height:45px\" />') END AS SpecialBanner,DATE_FORMAT(a.SpecialFrom,'%d/%m/%Y') as SpecialFrom,DATE_FORMAT(a.SpecialTo,'%d/%m/%Y') as SpecialTo,a.TermsAndConditions, a.IsActive as active,
                                          ( SELECT COUNT( distinct h.ProductId ) FROM productspecials AS h WHERE h.SpecialId = a.Id AND h.RetailerId =".$this -> session -> userdata('user_retailer_id')." and h.StoreTypeId = " . $this -> session -> userdata('user_store_format_id') .") AS sum_count, 
                                          count(distinct c.StoreId) as store_count", false)
            -> from('specials as a')            
            -> where('a.IsRemoved', 0)
            -> add_column('Actions', special_get_buttons('$1'), 'Id');
            $this -> datatables -> join('special_stores as c', 'c.SpecialId = a.Id and a.IsStoreType = 1 and c.RetailerId = '. $this -> session -> userdata('user_retailer_id') . ' and c.StoreTypeId = ' . $this -> session -> userdata('user_store_format_id'));
        }
        if ($this -> session -> userdata('user_type') == 6) {            
            $this -> datatables -> select("a.Id as Id,concat('<a href=\"" . front_url() . "admin/specialmanagement/managespecials/',a.Id,'\">',SpecialName,'</a>') as SpecialName,CASE WHEN a.SpecialBanner = '' OR a.SpecialBanner IS NULL THEN '' ELSE CONCAT('<img src=\"" . front_url() . SPECIAL_IMAGE_PATH . "small/',a.SpecialBanner,'\" style=\"height:45px\" />') END AS SpecialBanner,DATE_FORMAT(a.SpecialFrom,'%d/%m/%Y') as SpecialFrom,DATE_FORMAT(a.SpecialTo,'%d/%m/%Y') as SpecialTo,a.TermsAndConditions, a.IsActive as active,
                                          ( SELECT COUNT( distinct h.ProductId ) FROM productspecials AS h WHERE h.SpecialId = a.Id AND h.RetailerId =".$this -> session -> userdata('user_retailer_id')." and h.StoreTypeId = " . $this -> session -> userdata('user_store_format_id') ." and h.StoreId = " . $this -> session -> userdata('user_store_id') .") AS sum_count, 
                                          count(distinct c.StoreId) as store_count", false)
            -> from('specials as a')            
            -> where('a.IsRemoved', 0)
            -> add_column('Actions', special_get_buttons('$1'), 'Id');
            $this -> datatables -> join('special_stores as c', 'c.SpecialId = a.Id and a.IsStore = 1 and c.RetailerId = '. $this -> session -> userdata('user_retailer_id') . ' and c.StoreTypeId = ' . $this -> session -> userdata('user_store_format_id') . ' and c.StoreId = ' . $this -> session -> userdata('user_store_id'));
        }
        $this -> datatables -> group_by('a.Id');

        echo $this -> datatables -> generate();
    }
    
    public function get_special_details() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_sel = $this -> input -> post('special_sel');
            $name = '';
            $from = '';
            $to = '';
            $image = '';
            if ($special_sel) {
                $special_details = $this -> specialproductmodel -> get_special_data($special_sel);
                if ($special_details) {
                    $this -> result = 1;
                    $this -> message = 'Got data';
                    $name = $special_details['SpecialName'];
                    $from = $special_details['SpecialFrom'];
                    $to = $special_details['SpecialTo'];
                    $image = $special_details['SpecialBanner'];
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'No records found';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Invalid data';
            }
        }
        else {
            $this -> result = 0;
            $this -> message = 'Invalid request';
        }
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
            'name' => $name,
            'from' => $from,
            'to' => $to,
            'image' => $image
        ));
    }

    public function get_main_categories() {
        $main_categories = $this -> specialproductmodel -> get_main_categories();
        if ($main_categories) {
            $html = '';
            foreach ($main_categories as $category) {
                $html .= '<option value="' . $category['Id'] . '">' . $category['CategoryName'] . '</option>';
            }
            echo $html;
        }
        else {
            echo '';
        }
    }

    public function get_parent_categories() {
        $main_cat = $this -> input -> post('sel_val');
        $main_categories = $this -> specialproductmodel -> get_parent_categories($main_cat);
        if ($main_categories) {
            $html = '<option value="">Select Parent Category</option>';
            foreach ($main_categories as $category) {
                $html .= '<option value="' . $category['Id'] . '">' . $category['CategoryName'] . '</option>';
            }
            echo $html;
        }
        else {
            echo '';
        }
    }

    public function add_special_product_new() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $product_details = json_decode($this -> input -> post('product_details'));
            if (!empty($product_details)) {
                $success = 0;
                $fail = 0;
                foreach ($product_details as $product_id => $product) {
                    $product_data = $this -> specialproductmodel -> get_store_product_details($product_id);
                    $special_data = $this -> specialproductmodel -> get_specials_details($this -> session -> userdata('special_sel'));
                    if ($product_data && $special_data) {
                        $validate_data = array(
                            'ProductId' => $product_data['ProductId'],
                            'RetailerId' => $product_data['RetailerId'],
                            'SpecialQty' => $product -> qty,
                            'SpecialPrice' => $product -> price,
                            'PriceAppliedFrom' => $special_data['SpecialFrom'],
                            'PriceAppliedTo' => $special_data['SpecialTo'],
                            'IsActive' => 1,
                            'IsApproved' => 0,
                        );
                        $validate_offer = $this -> specialproductmodel -> validate_offer($validate_data, $stores_list);
                        if ($validate_offer) {
                            $this -> specialproductmodel -> validate_store_product($product_data['ProductId'], $product_data['RetailerId'], $product_data['StoreId']);
                            $insert_data = array(
                                'ProductId' => $product_data['ProductId'],
                                'RetailerId' => $product_data['RetailerId'],
                                'StoreId' => $product_data['StoreId'],
                                'StoreTypeId' => $product_data['StoreTypeId'],
                                'PriceForAllStores' => '0',
                                'ActualPrice' => '0.00',
                                'SpecialQty' => $product -> qty,
                                'SpecialPrice' => $product -> price,
                                'PriceAppliedFrom' => $special_data['SpecialFrom'],
                                'PriceAppliedTo' => $special_data['SpecialTo'],
                                'CreatedBy' => $this -> session -> userdata('user_id'),
                                'CreatedOn' => date('Y-m-d H:i:s'),
                                'SpecialId' => $special_data['Id'],
                                'IsActive' => 1,
                                'IsApproved' => 0,
                                'ApprovedBy' => 0);


                            $result = $this -> specialproductmodel -> add_special_product($insert_data);
                            if ($result) {
                                $success++;
                            }
                            else {
                                $fail++;
                            }
                        }
                    }
                    else {
                        $fail++;
                    }
                }
                if ($success == 0 && $fail > 0) {
                    $this -> result = 0;
                    $this -> message = 'Failed to add specials';
                }
                elseif ($success > 0 && $fail > 0) {
                    $this -> result = 1;
                    $this -> message = 'Successfully added some products, failed to add the others.';
                    $this -> session -> set_userdata('success_message', "Successfully added some products, failed to add the others.");
                }
                elseif ($success > 0) {
                    $this -> result = 1;
                    $this -> message = 'All specials added successfully';
                    $this -> session -> set_userdata('success_message', "All specials added successfully");
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'No specials found to add.';
            }
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

    public function managespecials($special_id = '') {
        if ($special_id != '') {
            $special_details = $this -> specialproductmodel -> get_special_details_id($special_id);
            
            $specialStoreNames = "";
            $specialStores =array();
            if($special_details)
            {
                $special_stores = $this -> specialproductmodel -> get_specials_stores($special_id);
                
                foreach($special_stores as $special_store)
                {
                    $specialStores[] = $special_store['StoreName']; 
                }
                
                $specialStoreNames = implode(', ',$specialStores);
            }
            
            $data['special_name'] = $special_details['SpecialName'];
            $data['title'] = 'Edit Specials - ' . $special_details['SpecialName'];
            $this -> breadcrumbs[0] = array('label' => 'Special Management', 'url' => '/specialmanagement');
            $this -> breadcrumbs[1] = array('label' => 'Edit Specials', 'url' => '');
            $data['breadcrumbs'] = $this -> breadcrumbs;
            $data['special_id'] = $special_id;
            $data['specialStoreNames'] = $specialStoreNames;
            $data['specialStores'] = $specialStores;
            
            $this -> template -> view('admin/special_management/edit_special', $data);
        }
        else {
            redirect('/specialmanagement');
        }
    }

    public function datatable_edit($special_id = '') { 

        $selected_arr = json_decode($this -> input -> post('selected'));
        $this -> datatables -> select("a.Id as p_id, 
            f.Id as spl_id, 
            g.CategoryName as MainCategory,
            h.CategoryName as ParentCategory,
            i.CategoryName as Category,
            b.ProductName,
            d.StoreName,
            c.CompanyName,
            case when a.price IS NULL and now() between f.PriceAppliedFrom and f.PriceAppliedTo then concat('<input class=\"prod_def_prc click_sel\" style=\"width:65px\" type=\"text\" id=\"product_def_price_',f.Id, '\" value=\"0.00\" />') when a.price is not null and now() between f.PriceAppliedFrom and f.PriceAppliedTo then concat('<input class=\"prod_def_prc click_sel\" style=\"width:65px\" type=\"text\" id=\"product_def_price_',f.Id,'\" value=\"',a.price,'\" />') else a.price end as RRP, 
            case when f.SpecialPrice IS NULL and now() between f.PriceAppliedFrom and f.PriceAppliedTo then concat('<input class=\"prod_prc click_sel\" style=\"width:65px\" type=\"text\" id=\"product_price_',f.Id, '\" value=\"0.00\" />') when a.price is not null and now() between f.PriceAppliedFrom and f.PriceAppliedTo then concat('<input class=\"prod_prc click_sel\" style=\"width:65px\" type=\"text\" id=\"product_price_',f.Id,'\" value=\"',f.SpecialPrice,'\" />') else f.SpecialPrice end as SpecialPrice,
            case 
                when now() not between f.PriceAppliedFrom and f.PriceAppliedTo then f.SpecialQty
                when f.SpecialQty IS NULL then " . specials_get_select(0) . "
                when f.SpecialQty = 1 then " . specials_get_select(1) . "
                when f.SpecialQty = 2 then " . specials_get_select(2) . "
                when f.SpecialQty = 3 then " . specials_get_select(3) . "
                when f.SpecialQty = 4 then " . specials_get_select(4) . "
                when f.SpecialQty = 5 then " . specials_get_select(5) . "
                when f.SpecialQty = 6 then " . specials_get_select(6) . "
                when f.SpecialQty = 7 then " . specials_get_select(7) . "
                when f.SpecialQty = 8 then " . specials_get_select(8) . "
                when f.SpecialQty = 9 then " . specials_get_select(9) . "
                when f.SpecialQty = 10 then " . specials_get_select(10) . "
                when f.SpecialQty > 10 then " . specials_get_select(11) . " end
                    as SpecialQty,
            DATE_FORMAT(f.PriceAppliedFrom,'%d/%c/%Y') as PriceAppliedFrom,DATE_FORMAT(f.PriceAppliedTo,'%d/%c/%Y') as PriceAppliedTo", false)
            -> from('storeproducts as a')
            -> join('products as b', 'b.Id = a.ProductId')
            -> join('retailers as c', 'c.Id = a.RetailerId')
            -> join('stores as d', 'd.Id = a.StoreId')
            -> join('storestypes as e', 'e.Id = a.StoreTypeId')
            -> join('productspecials as f', 'f.ProductId = b.Id and f.RetailerId = c.Id and f.StoreId = d.Id and f.StoreTypeId = e.Id', 'left')
            -> join('categories as g', 'g.Id = b.MainCategoryId', 'left')
            -> join('categories as h', 'h.Id = b.ParentCategoryId', 'left')
            -> join('categories as i', 'i.Id = b.CategoryId', 'left')
            -> where('f.SpecialId', $special_id)
            //-> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'))
            -> add_column('selectVal', '<input type="checkbox"  name="store_products[]" value="$1" />', 'p_id')
            -> add_column('delVal', '<a title="Delete" href="javascript:void(0)" class="delete_spl" data-id="$1"><i class="fa fa-trash"></i></a>', 'spl_id');
        if ($this -> session -> userdata('user_type') == 3) {
            $this -> datatables -> where('a.RetailerId', $this -> session -> userdata('user_retailer_id'));
        }
        if ($this -> session -> userdata('user_type') == 5) {
            $this -> datatables -> where('a.StoreTypeId', $this -> session -> userdata('user_store_format_id'));
        }
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> datatables -> where('a.StoreId', $this -> session -> userdata('user_store_id'));
        }
        $this -> datatables -> group_by('a.ProductId');

        echo $this -> datatables -> generate();
    }

    public function modify_special() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $edit_data = json_decode($this -> input -> post('edit_data'));
                
            $error = 0;
            if ($edit_data) {
                foreach ($edit_data as $special_id => $edit) {
                    $special_details = $this -> specialproductmodel -> get_product_special_details_id($special_id);
                    
                     
                    
                    $where = array(
                        'ProductId' => $special_details['ProductId'],
                        'RetailerId' => $special_details['RetailerId'],
                        'SpecialId' => $special_details['SpecialId']
                    );
                    
                    $update_data = array(
                        'SpecialQty' => $edit -> qty,
                        'SpecialPrice' => $edit -> price,
                        'IsApproved' => 0
                    );
                    
                    if (!$this -> specialproductmodel -> update_special_product_information($where, $update_data)) {
                        $error++;
                    }
                    
                    $update_data = array(
                        'Price' => $edit -> price_def
                    );
                    $where = array(
                        'ProductId' => $special_details['ProductId'],
                        'RetailerId' => $special_details['RetailerId'],
                        'StoreId' => $special_details['StoreId'],
                        'StoreTypeId' => $special_details['StoreTypeId'],
                    );
                    if (!$this -> specialproductmodel -> update_store_price($where, $update_data)) {
                        $error++;
                    }
                }
                if ($error == 0) {
                    $this -> result = 1;
                    $this -> session -> set_userdata('success_message', 'Specials updated successfully');
                    $this -> message = 'Specials updated successfully';
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to update some/all specials';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'No data available to edit';
            }
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

    public function delete_special_product() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_id = $this -> input -> post('data_id');
            //$isDeleted = $this -> specialproductmodel -> delete_special_product($special_id);
            
            # Get product specials information and deleted all record of the product from all stores which are in selected specials 
            $special_details = $this -> specialproductmodel -> get_product_special_details_id($special_id);
            $productId = $special_details['ProductId'];
            $retailerId = $special_details['RetailerId'];
            $specialId = $special_details['SpecialId'];            
            
            $isDeleted = $this -> specialproductmodel -> delete_special_product_from_stores($productId, $retailerId, $specialId);
            
            if ($isDeleted) {
                $this -> result = 1;
                $this -> message = 'Special deleted successfully';
            }
            else {
                $this -> result = 0;
                $this -> message = 'Failed to delete special';
            }
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

    public function get_approve_icon() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_details = $this -> specialproductmodel -> get_special_pending_count();
            $approve_data = [];
            if ($special_details) {
                foreach ($special_details as $special) {
                    if ($special['count'] > 0) {
                        $approve_data[] = $special['Id'];
                    }
                }
            }
            if (sizeof($approve_data) > 0) {
                $this -> result = 1;
                $this -> message = json_encode(array_values($approve_data));
            }
            else {
                $this -> result = 0;
                $this -> message = '';
            }
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
	public function main_approve_all_special() {
        
        set_time_limit(0);
        ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 0);
		$update_data = array(
                'IsApproved' => 1
            );
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_id = $this -> input -> post('sel_id');       
			//$is_approved = $this -> specialproductmodel -> update_special_product($special_id, $update_data);			
			$is_approved=true; // This is temporary for continue next flow, un comment above line.
			if($is_approved){
				$special_product_data = $this -> specialproductmodel -> get_special_pending_details($special_id);
				
				//Changed on 2nd October , 2017
				$this -> specialproductmodel -> update_special_product($special_id, $update_data);
				
				$allStores = $this -> specialproductmodel -> getAllStoresOfSpecialId($special_id);
				$this->session->set_userdata('storeNumber',1);
				$product_array = [];
				$store_detail_array = [];  
				 $distinctProductIds = array();
				if ($special_product_data) {
					
					foreach ($special_product_data as $product) {
							
						if (!in_array($product['ProductId'], $distinctProductIds))
						{
							 $distinctProductIds[] = $product['ProductId'];
						}
					}
				}

				$special_product_count = sizeof($distinctProductIds);		
			}
		}
		
		
        echo json_encode(array('allStores'=>$allStores,'special_product_count'=>$special_product_count,'storeCount'=>count($allStores)));
    }

    public function approve_all_special() {
        
			set_time_limit(0);
			ini_set('memory_limit', '512M');
			ini_set('max_execution_time', 0);
			if($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $special_id = $this -> input -> post('sel_id');
			$storeId = $this -> input -> post('storeId');
			$storeNumber = $this -> input -> post('storeNumber');
			$storeCount = $this -> input -> post('storeCount');
			$special_product_count = $this -> input -> post('special_product_count');
           
		    
            $product_name = '';
            $product_id = '';
            $retailer_name = '';
            $retailer_id = '';
            $store_name = '';
            $store_id = '';
            $store_type_name = '';
            $store_type_id = '';
            $special_count = '';
            $special_price = '';
            $special_name = '';
            $store_array = [];
            $product_array = [];
            $store_detail_array = [];

            
						
			 $special_user_list = [];										
			 //Get the registered device IDs now available in DB
			
            $is_approved = TRUE;
            if ($is_approved) {

              $special_product_data = $this -> specialproductmodel -> get_special_pending_details($special_id);
			 
                if ($special_product_data) {
					
					$special_notifications = $this -> specialproductmodel -> get_special_enabled_user_by_storeId($storeId);
					if ($special_notifications) {
						foreach ($special_notifications as $special_users) {
							$special_user_list[$special_users['UserId']] = array(
								'StoreId' => $special_users['StoreId'],
								'Specials' => $special_users['Specials'],
								'PreferredStoreOnly' => $special_users['PreferredStoreOnly']
							);
						}
					}	
                    foreach ($special_product_data as $product) {

                        $product_name = $product['ProductName'];
                        $product_id = $product['ProductId'];
                        $retailer_name = $product['CompanyName'];
                        $retailer_id = $product['RetailerId'];
                        $store_name = $product['StoreName'];
                        $store_id = $product['StoreId'];
                        $store_type_name = $product['StoreType'];
                        $store_type_id = $product['StoreTypeId'];
                        $special_count = $product['SpecialQty'];
                        $special_price = $product['SpecialPrice'];
                        $special_name = $product['SpecialName'];
                        $specialFrom = $product['SpecialFrom'];
                        $specialTo = $product['SpecialTo'];
                        
                        if(!in_array($product['StoreId'], $store_array)){
                            $store_array[] = $product['StoreId'];
                        }
                        $store_detail_array[] = array(
                            'id' => $product['StoreId'],
                            'name' => $product['StoreName'],
                            'retailer' => $product['RetailerId'],
                            'retailerName' => $product['CompanyName'],
                            'storeType' => $product['StoreTypeId']
                        );
                        $product_array[] = array(
                            'id' => $product['ProductId'],
                            'name' => $product['ProductName'],
							'SpecialPrice' => $product['SpecialPrice']
                        );
						
                    }
					$user_ids = $this -> specialproductmodel -> get_device_user_ids();
					$numForParts=2;
					if(count($user_ids)/$storeCount>0){
						$numForParts=round(count($user_ids)/$storeCount);$numForParts=$numForParts+1;
					} 
					//echo 'numForParts=>'.$numForParts.' & storeNumber=>'.$storeNumber;
					
					$usersParts=array_chunk($user_ids,$numForParts);
					
					if(isset($usersParts[$storeNumber])){
						
						custom_create_push_message($product_id, $retailer_id, $store_type_id, $store_id, $product_name, $retailer_name, $store_name, $store_type_name, $special_count, $special_price, $special_name, $special_product_count, $store_array, $product_array, $store_detail_array, $specialFrom, $specialTo,$special_id,$usersParts[$storeNumber]);   
					}
						
					
                }
                
                $this -> result = 1;
                $this -> session -> set_userdata('success_message', "Specials approved successfully");
                $this -> message = 'Specials approved successfully';
            }
            else {
                $this -> result = 0;
                $this -> session -> set_userdata('success_message', "Specials approved successfully");
                $this -> message = 'Failed to approve special';
            }
        }
        else {
            $this -> result = 0;
            $this -> session -> set_userdata('success_message', "Specials approved successfully");
            $this -> message = 'Invalid request';
        }
		
		
		
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message,
			
        ));
    }

    /**
     * Function to get the stores with in a state
     */
    public function get_state_stores() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $all_states = sanitize($this -> input -> post('all_states'));
            $state_list = sanitize(json_decode($this -> input -> post('states')));
            $search_string = sanitize($this -> input -> post('search_string'));
            $sf = sanitize($this -> input -> post('sf'));
            $all_store_format = sanitize($this -> input -> post('all_store_formats'));
            $store_format_list = sanitize(json_decode($this -> input -> post('store_formats')));
            if ($all_states == 1 || !empty($state_list)) {
                if ($this -> session -> userdata('user_type') == 3) {
                    if ($sf == 1 && !empty($store_format_list)) {
                        $stores = $this -> specialproductmodel -> get_state_stores($all_states, $state_list, $search_string, $all_store_format, $store_format_list);
                        if ($stores) {
                            $html = '';
                            $cnt = 1;
                            foreach ($stores as $store) {
                                if ($cnt == 1) {
                                    $html .= '<div class="col-md-12"><div class="col-md-12">
                                        <div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="sp_st_1" name="all_stores" value="1" id="all_stores"><label>All Stores
                                                        </label>
                                                     </div>
                                                </div>
                                               </div></div>';
                                }
                                $html .= ' <div class="col-md-12">';
                                $html .= '  <div class="col-md-12"><div class="row">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" class="special_store sp_st_1" name="store_special_list[]" value="' . $store['Id'] . '"><label>' . $store['StoreName'] . '
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>';
                                $html .= ' </div>';
                                $cnt++;
                            }
                            $this -> result = 1;
                            $this -> message = $html;
                        }
                        else {
                            $this -> result = 0;
                            $this -> message = 'No stores found';
                        }
                    }
                    else {
                        $store_formats = $this -> specialproductmodel -> get_state_storeformats($all_states, $state_list);
                        if ($store_formats) {
                            $html = '';
                            $cnt = 1;
                            foreach ($store_formats as $store_format) {
                                if ($cnt == 1) {
                                    $html .= '<div class="col-md-12"><div class="col-md-12">
                                            <div class="row">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" class="sp_sf_1" name="all_store_formats" value="1" id="all_store_formats"><label>All Store Formats
                                                            </label>
                                                         </div>
                                                    </div>
                                                   </div></div>';
                                }
                                $html .= ' <div class="col-md-12">';
                                $html .= '  <div class="col-md-12"><div class="row">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" class="special_store_format sp_sf_1" name="store_special_format_list[]" value="' . $store_format['Id'] . '"><label>' . $store_format['StoreType'] . '
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>';
                                $html .= ' </div>';
                                $cnt++;
                            }
                            $this -> result = 2;
                            $this -> message = $html;
                        }
                        else {
                            $this -> result = 0;
                            $this -> message = 'No store formats found';
                        }
                    }
                }
                else {
                    $stores = $this -> specialproductmodel -> get_state_stores($all_states, $state_list, $search_string);
                    if ($stores) {
                        $html = '';
                        $cnt = 1;
                        foreach ($stores as $store) {
                            if ($cnt == 1) {
                                $html .= '<div class="col-md-12"><div class="col-md-12">
                                        <div class="row">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="sp_st_1" name="all_stores" value="1" id="all_stores"><label>All Stores
                                                        </label>
                                                     </div>
                                                </div>
                                               </div></div>';
                            }
                            $html .= ' <div class="col-md-12">';
                            $html .= '  <div class="col-md-12"><div class="row">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" class="special_store sp_st_1" name="store_special_list[]" value="' . $store['Id'] . '"><label>' . $store['StoreName'] . '
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>';
                            $html .= ' </div>';
                            $cnt++;
                        }
                        $this -> result = 1;
                        $this -> message = $html;
                    }
                    else {
                        $this -> result = 0;
                        $this -> message = 'No stores found';
                    }
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Invalid data';
            }
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

    function copy_specials_in_retailer($retailer_id, $store_id = '') {
        
    }
    
    public function change_status($id, $status) {
        $this -> specialproductmodel -> change_status_special($id, $status);
        $this -> session -> set_userdata('success_message', "Special status updated successfully");
        redirect('specialmanagement', 'refresh');
    }
    
    
    // Store Formats

    function get_special_store_count($special_id) {
        $userType = $this -> session -> userdata('user_type');
        $user_retailer_id = $this -> session -> userdata('user_retailer_id');
        $user_store_format_id = $this -> session -> userdata('user_store_format_id');
        $user_store_id = $this -> session -> userdata('user_store_id');
        
        $store_count = $this->specialproductmodel->get_special_store_count($special_id,$userType,$user_retailer_id,$user_store_format_id,$user_store_id);
        echo json_encode(array('storeCount' => $store_count));
    }
    
    
    
    
}