<?php

/*
 * Author:MK
 * Purpose:Adverisments Controller
 * Date:22-05-2017
 * Dependency: adverismentsmodel.php
 */

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

class Advertisements extends My_Controller {
    private $message;
    private $result;

    function __construct() {
        parent::__construct();
        
        # Load required models
        $this -> load -> model('admin/advertisementsmodel', '', TRUE);
        $this -> load -> model('admin/categorymodel', '', TRUE);
        $this -> load -> model('admin/retailermodel', '', TRUE);
        $this -> load -> model('admin/storeformatmodel', '', TRUE);
        $this -> load -> model('admin/storemodel', '', TRUE);

        # Set default values
        $this -> page_title = "Advertisements";
        $this -> breadcrumbs[] = array('label' => 'Advertisements', 'url' => '/advertisements');

        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    /*
     * Method Name: index
     * Purpose: Shows all advertisements 
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    
    public function index() {
        # Set page title
        $data['title'] = $this -> page_title;

        # Set breadcrumbs
        $this -> breadcrumbs[0] = array('label' => 'Ads Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Advertisements', 'url' => '/advertisements');
        $data['breadcrumbs'] = $this -> breadcrumbs;
        
        $this -> template -> view('admin/advertisements/index', $data);
    }

    /*
     * Method Name: datatable
     * Purpose: Get advertisement listing
     * params:
     *      input: $brand_id
     *      output: status - FAIL / SUCCESS
     *              message - 
     */
    
    public function datatable() {
        $this -> datatables -> select("Id,AdvertisementTitle,AdvertisementImage,AdvertisementDescription,AdvertisementUrl,StartDate, EndDate, CreatedBy,ModifiedBy,CreatedOn,ModifiedOn,IsActive AS active,IsRemoved", FALSE)
            -> unset_column('AdverismentImage')
            -> unset_column('Id')
            -> unset_column('active')
            -> from('advertisements')                       
            -> add_column('Actions', get_action_buttons('$1', 'advertisements'), 'Id');

            $cond = array(
                'IsRemoved' => '0'
            );
        $this -> datatables -> where($cond);

        echo $this -> datatables -> generate();
    }
    
    
    /*
     * Method Name: add
     * Purpose: Add Advertisement products
     * params:
     *      input: PostArray
     *      output: status - FAIL / SUCCESS
     *              message - 
     */

    
    
    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            
            //Add Advertisement 
            if(!$this -> input -> post('home_page'))
            {
                $this -> form_validation -> set_rules('MainCategoryId', 'Category', 'trim|required|xss_clean');
            }              
            $this -> form_validation -> set_rules('AdvertisementTitle', 'Advertisement Title', 'trim|required|xss_clean');            
            $this -> form_validation -> set_rules('AdvertisementDescription', 'Description', 'trim|required|xss_clean');            
            $this -> form_validation -> set_rules('AdvertisementUrl', 'Advertisement Url', 'trim|required|xss_clean');           
            $this -> form_validation -> set_rules('StartDate', 'Start date', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('EndDate', 'End Date', 'trim|required|xss_clean');
            
            $client_type = $this -> input -> post('client_type');
            
            if($client_type == 'new' )
            {
                $this -> form_validation -> set_rules('CompanyName', 'Company Name', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('ClientEmail', 'Client Email', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('ContactNumber', 'Contact Number', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('ContactPerson', 'Contact Person', 'trim|required|xss_clean');
            }else if($client_type == 'existing' ){                
                $this -> form_validation -> set_rules('RetailerId', 'Retailer', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('StoreTypeId', 'Store Format', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('StoreId', 'Store', 'trim|required|xss_clean');                
            }

            if (!$this -> form_validation -> run() == FALSE) {

                $result = array();
                $image_path = "";

                //If image uploaded
                if (!empty($_FILES['AdvertisementImage']['name'])) {
                    $result = $this -> do_upload('AdvertisementImage', 'advertisements', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    $image_path = $result['upload_data']['file_name'];
                }
                if (!isset($result['error'])) {
                    $data = array(
                        'MainCategoryId' => $this -> input -> post('MainCategoryId'),
                        'AdvertisementTitle' => $this -> input -> post('AdvertisementTitle'),
                        'AdvertisementDescription' => $this -> input -> post('AdvertisementDescription'),                        
                        'AdvertisementUrl' => $this -> input -> post('AdvertisementUrl'),                        
                        'StartDate' => $this -> input -> post('StartDate'),
                        'EndDate' => $this -> input -> post('EndDate'),                        
                        'AdvertisementImage' => $image_path,
                        'home_page' => $this -> input -> post('home_page'),
                        'ClientType' => $this -> input -> post('client_type'),
                        'CompanyName' => $this -> input -> post('CompanyName'),
                        'ClientEmail' => $this -> input -> post('ClientEmail'),
                        'ContactNumber' => $this -> input -> post('ContactNumber'),
                        'ContactPerson' => $this -> input -> post('ContactPerson'),
                        'RetailerId' => $this -> input -> post('RetailerId'),
                        'StoreTypeId' => $this -> input -> post('StoreTypeId'),
                        'StoreId' => $this -> input -> post('StoreId'),
                        'CreatedBy' => $this -> session -> userdata('user_id'),
                        'CreatedOn' => date('Y-m-d H:i:s'),
                        'IsActive' => 1
                    );
                    $result = $this -> advertisementsmodel -> add_advertisement($data);
                    if ($result) {
                        $this -> session -> set_userdata('success_message', "Advertisement added successfully");
                        $this -> result = 1;
                        $this -> message = 'Advertisement added successfully';
                    }else {
                        $this -> session -> set_userdata('error_message', "Failed to add advertisement");
                        $this -> result = 0;
                        $this -> message = 'Failed to add advertisement';
                    }
                    redirect('/advertisements', 'refresh');
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

        $this -> breadcrumbs[0] = array('label' => 'Ads Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Advertisements', 'url' => '/advertisements');
        $this -> breadcrumbs[2] = array('label' => 'Add Advertisement', 'url' => 'advertisements/add');

        $data['title'] = $this -> page_title;
        $data['breadcrumbs'] = $this -> breadcrumbs;
        
        $data['main_categories'] = $data['parent_category'] = $data['sub_category'] = array();
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();
        $data['retailers'] = $this -> retailermodel -> get_retailers();
        
        $this -> template -> view('admin/advertisements/add', $data);
    }

    /*
     * Method Name: edit
     * Purpose: Get advertisement information for edit
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if advertisement not found fails / Success message
     *              advertisement - Array containing all active loyalty products.
     */
    
    public function edit($id) {
        # get advertisement deatils 
        $data = $this -> advertisementsmodel -> get_advertisement_details($id);
        
        $retailerId = $data['RetailerId'];
        $storeTypeId = $data['StoreTypeId'];
        $storeId = $data['StoreId'];        
        
        #Set values 
        $this -> breadcrumbs[] = array('label' => 'Edit Advertisement', 'url' => 'advertisements/edit/' . $id);

        $data['title'] = $this -> page_title;
        $data['breadcrumbs'] = $this -> breadcrumbs;       
        
        $data['main_categories'] = $this -> categorymodel -> get_main_categories();
        $data['retailers'] = $this -> retailermodel -> get_retailers();        
        $data['store_formats'] = $this -> storeformatmodel -> get_store_formats($retailerId);
        $data['stores'] = $this -> storemodel -> get_stores(0,$storeTypeId);
        
        $this -> load -> view('admin/advertisements/edit', $data);
    }

    /*
     * Method Name: edit_post
     * Purpose: Edit advertisement information.
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              message - The reason if advertisement not found fails / Success message
     *              advertisement - Array containing all active advertisement.
     */
    
    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit Advertisement             
            if(!$this -> input -> post('home_page'))
            {
                $this -> form_validation -> set_rules('MainCategoryId', 'Category', 'trim|required|xss_clean');
            }
            
            $this -> form_validation -> set_rules('AdvertisementTitle', 'Advertisement Title', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('AdvertisementDescription', 'Description', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('AdvertisementUrl', 'Advertisement Url', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('StartDate', 'Start date', 'trim|required|xss_clean');
            $this -> form_validation -> set_rules('EndDate', 'End Date', 'trim|required|xss_clean');
            
            $client_type = $this -> input -> post('client_type');
            
            if($client_type == 'new' )
            {
                $this -> form_validation -> set_rules('CompanyName', 'Company Name', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('ClientEmail', 'Client Email', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('ContactNumber', 'Contact Number', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('ContactPerson', 'Contact Person', 'trim|required|xss_clean');
                
                $this->form_validation->set_message('is_unique', 'The %s is already taken');
                
            }else if($client_type == 'existing' ){                
                $this -> form_validation -> set_rules('RetailerId', 'Retailer', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('StoreTypeId', 'Store Format', 'trim|required|xss_clean');
                $this -> form_validation -> set_rules('StoreId', 'Store', 'trim|required|xss_clean');                
            }
            

//            if (empty($_FILES['AdvertisementImage']['name']) && $this->input->post('old_advertisement_image') == '') {
//               $this->form_validation->set_rules('AdvertisementImage', 'Document', 'callback_file_selected_check');
//            }
                

            if (!$this -> form_validation -> run() == FALSE) {
                 
                if($client_type == 'new' )
                {   
                    $companyName = $this -> input -> post('CompanyName');
                    $clientEmail = $this -> input -> post('ClientEmail');
                    $contactNumber = $this -> input -> post('ContactNumber');
                    $contactPerson = $this -> input -> post('ContactPerson');
                    $retailerId = 0;
                    $storeTypeId = 0;
                    $storeId = 0;  
                }else if($client_type == 'existing' ){
                    $companyName = "";
                    $clientEmail = "";
                    $contactNumber = "";
                    $contactPerson = "";
                    $retailerId = $this -> input -> post('RetailerId');
                    $storeTypeId = $this -> input -> post('StoreTypeId');
                    $storeId = $this -> input -> post('StoreId');  
                }
                        
                if (!empty($_FILES['AdvertisementImage']['name'])) {
                    $result = $this -> do_upload('AdvertisementImage', 'advertisements', $this -> input -> post('image-x'), $this -> input -> post('image-y'), $this -> input -> post('image-width'), $this -> input -> post('image-height'));
                    if (!isset($result['error'])) {
                        $edit_data = array(
                            'MainCategoryId' => $this -> input -> post('MainCategoryId'),
                            'AdvertisementTitle' => $this -> input -> post('AdvertisementTitle'),
                            'AdvertisementDescription' => $this -> input -> post('AdvertisementDescription'),                                
                            'AdvertisementUrl'=> $this -> input -> post('AdvertisementUrl'),
                            'StartDate' => $this -> input -> post('StartDate'),
                            'EndDate' => $this -> input -> post('EndDate'),
                            'home_page' => $this -> input -> post('home_page'),
                            'ClientType' => $this -> input -> post('client_type'),
                            'CompanyName' => $companyName,
                            'ClientEmail' => $clientEmail,
                            'ContactNumber' => $contactNumber,
                            'ContactPerson' => $contactPerson,
                            'RetailerId' => $retailerId,
                            'StoreTypeId' => $storeTypeId,
                            'StoreId' => $storeId,
                            'AdvertisementImage' => $result['upload_data']['file_name'],
                            'ModifiedBy' => $this -> session -> userdata('user_id'),
                            'ModifiedOn' => date('Y-m-d H:i:s')
                         );
                        

                        $result = $this -> advertisementsmodel -> update_advertisement($id, $edit_data);
                        //$this -> session -> set_userdata('success_message', "Advertisement updated successfully");
                        $this -> result = 1;
                        $this -> message = 'Advertisement updated successfully';
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
                        'MainCategoryId' => $this -> input -> post('MainCategoryId'),
                        'AdvertisementTitle' => $this -> input -> post('AdvertisementTitle'),
                        'AdvertisementDescription' => $this -> input -> post('AdvertisementDescription'),
                        'AdvertisementUrl' => $this -> input -> post('AdvertisementUrl'),                                
                        'StartDate' => $this -> input -> post('StartDate'),
                        'EndDate' => $this -> input -> post('EndDate'),
                        'home_page' => $this -> input -> post('home_page'),
                        'ClientType' => $this -> input -> post('client_type'),
                        'CompanyName' => $companyName,
                        'ClientEmail' => $clientEmail,
                        'ContactNumber' => $contactNumber,
                        'ContactPerson' => $contactPerson,
                        'RetailerId' => $retailerId,
                        'StoreTypeId' => $storeTypeId,
                        'StoreId' => $storeId,
                        'ModifiedBy' => $this -> session -> userdata('user_id'),
                        'ModifiedOn' => date('Y-m-d H:i:s')
                    );

                    $result = $this -> advertisementsmodel -> update_advertisement($id, $edit_data);
                    $this -> session -> set_userdata('success_message', "Advertisement updated successfully");                    
                    $this -> result = 1;
                    $this -> message = 'Advertisement updated successfully';
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
     * Purpose: Delete Advertisement
     * params:
     *      input: $id
     *      output: status - FAIL / SUCCESS
     *              
     */
    
    public function delete($id) {        
        $this -> advertisementsmodel -> delete_advertisement($id);
        $this -> session -> set_userdata('success_message', "Advertisement deleted successfully");
        redirect('advertisements', 'refresh');
    }

    /*
     * Method Name: change_status
     * Purpose: Update advertisement status
     * params:
     *      input: $advertisement_id, $status
     *      output: status - FAIL / SUCCESS
     *              message - TRUE     
     */
    public function change_status($id, $status) {
        $this -> advertisementsmodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Advertisement status updated successfully");
        redirect('advertisements', 'refresh');
    }

    function file_selected_check() {
        $this -> form_validation -> set_message('file_selected_check', 'Please upload advertisement image.');
        if (empty($_FILES['AdvertisementImage']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }
}