<?php

/*
 * Author:PM
 * Purpose:Retailer Controller
 * Date:02-09-2015
 * Dependency: retailermodel.php
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
class Retailers extends My_Controller {

    private $result;
    private $message;
    
    function __construct() {
        parent::__construct();

        $this->load->model('admin/retailermodel', '', TRUE);
        $this->load->model('admin/usermodel', '', TRUE);
        $this->load->model('admin/storeformatmodel', '', TRUE);
        $this->load->model('admin/storemodel', '', TRUE);
        $this->load->model('admin/statemodel', '', TRUE);
        $this -> load -> model('admin/storegroupmodel', '', TRUE);

        $this->page_title = "Retail Management";
        $this->breadcrumbs[] = array('label' => 'Retail Management', 'url' => '/retailers');
    }

    public function index() {

        $data['title'] = $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $this->template->view('admin/retailers/index', $data);
    }

    public function datatable() {

        $this->datatables->select("retailers.Id as Id, retailers.CompanyName as CompanyName, retailers.LogoImage as LogoImage, retailers.IsActive as active")
                ->unset_column('Id')
                ->unset_column('LogoImage')
                ->unset_column('active')
                ->from('retailers')
                ->where('retailers.IsRemoved', '0')
                ->group_by('retailers.Id')
                ->add_column('Logo', get_image('$1', 'Retailer'), 'LogoImage')
                ->add_column('Actions', get_action_buttons('$1', 'retailers'), 'Id');

        echo $this->datatables->generate();
    }

    public function add() {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            //Add Retailer
            $this->form_validation->set_rules('company_name', 'name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('company_description', 'description', 'trim|required|xss_clean');
            //$this->form_validation->set_rules('logo', 'Document', 'callback_file_selected_check');
            $this->form_validation->set_rules('email', 'email', 'trim|required|callback_check_uniqueness_email');
            $this->form_validation->set_rules('first_name', 'first_name', 'trim|required|max_length[50]|xss_clean');
            $this->form_validation->set_rules('last_name', 'last_name', 'trim|required|max_length[50]|xss_clean');
            $this->form_validation->set_rules('password', 'password', 'trim|required|matches[confirm_password]');

            if (!$this->form_validation->run() == FALSE) {
                $result = '';
                if (!empty($_FILES['logo']['name'])) {
                    $result = $this->do_upload('logo', 'retailers', $this->input->post('image-x'), $this->input->post('image-y'), $this->input->post('image-width'), $this->input->post('image-height'));
                }
                
                if (!isset($result['error'])) {
                    $file_name = isset($result['upload_data']['file_name']) ? $result['upload_data']['file_name'] : '';

                    //Add Retailer User Initially
                    $role = $this->usermodel->get_user_role('Retailers');

                    $user_data = array(
                        'FirstName' => $this->input->post('first_name'),
                        'LastName' => $this->input->post('last_name'),
                        'Email' => $this->input->post('email'),
                        'Password' => MD5($this->input->post('password')),
                        'UserRole' => $role['Id'],
                        'TelephoneFixed' => $this->input->post('contact_tel'),
                    );

                    $user_id = $this->usermodel->add_user($user_data);

                    //Set retailer data
                    $retailer_data = array(
                        'CompanyName' => $this->input->post('company_name'),
                        'CompanyDescription' => $this->input->post('company_description'),
                        'LogoImage' => $file_name,
                        'StreetAddress' => $this->input->post('street_address'),
                        'Zip' => $this->input->post('zip'),
                        'City' => $this->input->post('city'),
                        'StateId' => $this->input->post('state'),
                        'RetailerAdminId' => $user_id,
                        'CreatedBy' => $this->session->userdata('user_id'),
                        'CreatedOn' => date('Y-m-d H:i:s'),
                        'IsActive' => 1
                    );

                    $result = $this->retailermodel->add_retailer($retailer_data);
                    
                    # Set the store groups 
                    $groupIds = $this->input->post('groupId');
                    if(count($groupIds)> 0 )
                    {
                        $newRetailserId = $result;
                        $result_setGroups = $this->retailermodel->set_storeGroups($newRetailserId, $groupIds); 
                    }
                        
                    $this->session->set_userdata('success_message', "Retailer added successfully");
                    redirect('retailers', 'refresh');
                } else {
                    // code to display error while image upload
                    $this->session->set_userdata('error_message', $result['error']);
                }
            }
        }

        $this->breadcrumbs[] = array('label' => 'Add Retailer', 'url' => 'retailers/add');

        $data['title'] = $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['retailers'] = $this->usermodel->get_users_by_role('Retailers');

        $data['states'] = $this->statemodel->get_states();

        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();
        //$data['retailers_storegroups'] = $this -> retailermodel -> get_retailers_storegroups($id);
        
        $this->template->view('admin/retailers/add', $data);
    }

    public function edit($id) {
        $data = $this->retailermodel->get_retailer_details($id);

        $retailer_user = $this->usermodel->get_user_details($data['RetailerAdminId']);

        $data['FirstName'] = $retailer_user['FirstName'];
        $data['LastName'] = $retailer_user['LastName'];
        $data['Email'] = $retailer_user['Email'];
        $data['TelephoneFixed'] = $retailer_user['TelephoneFixed'];

        $this->breadcrumbs[] = array('label' => 'Edit Retailer', 'url' => 'retailers/edit/' . $id);

        $data['title'] = $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['retailers'] = $this->usermodel->get_users_by_role('Retailers');

        $data['states'] = $this->statemodel->get_states();
        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();
        $data['retailers_storegroups'] = $this -> retailermodel -> get_retailers_storegroups($id);
        
        $this->load->view('admin/retailers/edit', $data);
    }
    
    public function edit_post($id){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
                
            //Edit Retailer
            $this->form_validation->set_rules('company_name', 'name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('company_description', 'description', 'trim|required|xss_clean');
            $this->form_validation->set_rules('first_name', 'first_name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('last_name', 'last_name', 'trim|required|xss_clean');
            
            //$this->form_validation->set_rules('groupId', 'Group', 'callback_check_default');
            //$this->form_validation->set_message('check_default', 'Please select atleast one group.');
            
            $retailer_details = $this -> retailermodel -> get_retailer_details($id);
            
            $this->form_validation->set_rules('email', 'email', 'trim|required|callback_check_uniqueness_email_edit['.$retailer_details['RetailerAdminId'].']');

//            if (empty($_FILES['logo']['name']) && $this->input->post('old_logo') == '') {
//                $this->form_validation->set_rules('logo', 'Document', 'callback_file_selected_check');
//            }

            $promo_premium   = $this -> input -> post('promo_premium')   === '1' ? $this -> input -> post('promo_premium') : '0';
            $promo_concierge = $this -> input -> post('promo_concierge') === '1' ? $this -> input -> post('promo_concierge') : '0';
            $promo_messenger = $this -> input -> post('promo_messenger') === '1' ? $this -> input -> post('promo_messenger') : '0';
            $promo_admanager = $this -> input -> post('promo_admanager') === '1' ? $this -> input -> post('promo_admanager') : '0';
            
            
            if (!$this->form_validation->run() == FALSE) {
                
                if (!empty($_FILES['logo']['name'])) {
                    $result = $this->do_upload('logo', 'retailers', $this->input->post('image-x'), $this->input->post('image-y'), $this->input->post('image-width'), $this->input->post('image-height'));
                    if (!isset($result['error'])) {
                        $edit_data = array(
                            'CompanyName' => $this->input->post('company_name'),
                            'CompanyDescription' => $this->input->post('company_description'),
                            'LogoImage' => $result['upload_data']['file_name'],
                            'RetailerAdminId' => $this->input->post('user_id'),
                            'ModifiedBy' => $this->session->userdata('user_id'),
                            'ModifiedOn' => date('Y-m-d H:i:s')
                        );

                        $result = $this->retailermodel->update_retailer($id, $edit_data);
                        
                        # Set the store groups 
                        $groupIds = $this->input->post('groupId');
                        if($result && count($groupIds)> 0 )
                        {
                           $result_setGroups = $this->retailermodel->set_storeGroups($id, $groupIds); 
                        }
                        
                        
                        //Update User Details
                        $user_edit_data = array(
                            'FirstName' => $this->input->post('first_name'),
                            'LastName' => $this->input->post('last_name'),
                            'Email' => $this->input->post('email'),
                            'TelephoneFixed' => $this->input->post('contact_tel'),
                        );

                        $this->usermodel->update_user_profile($this->input->post('user_id'), $user_edit_data);
                        
                        #SaveSubscriptionTyape 
                        $this->saveSubscriptionType($id, $promo_premium, $promo_concierge, $promo_messenger, $promo_admanager);
                        

                        $this->session->set_userdata('success_message', "Retailer updated successfully");
                        $this -> result = 1;
                    } else {
                        // code to display error while image upload
                       // $this->session->set_userdata('error_message', $result['error']);
                        $this -> result = 0;
                        $this -> message = $result['error'];
                    }
                } else {
                    $edit_data = array(
                        'CompanyName' => $this->input->post('company_name'),
                        'CompanyDescription' => $this->input->post('company_description'),
                        'StreetAddress' => $this->input->post('street_address'),
                        'Zip' => $this->input->post('zip'),
                        'City' => $this->input->post('city'),
                        'StateId' => $this->input->post('state'),
                        'RetailerAdminId' => $this->input->post('user_id'),
                        'ModifiedBy' => $this->session->userdata('user_id'),
                        'ModifiedOn' => date('Y-m-d H:i:s')
                    );

                    $result = $this->retailermodel->update_retailer($id, $edit_data);
                        
                    # Set the store groups 
                    $groupIds = $this->input->post('groupId');
                    if($groupIds)
                    {
                        $result_setGroups = $this->retailermodel->set_storeGroups($id, $groupIds); 
                    }
                        
                    //Update User Details
                    $user_edit_data = array(
                        'FirstName' => $this->input->post('first_name'),
                        'LastName' => $this->input->post('last_name'),
                        'Email' => $this->input->post('email')
                    );

                    $this->usermodel->update_user_profile($this->input->post('user_id'), $user_edit_data);
                    
                    # Save Subscription Types for all stores of the retailers 
                    $this->saveSubscriptionType($id, $promo_premium, $promo_concierge, $promo_messenger, $promo_admanager);
                        
                    $this->session->set_userdata('success_message', "Retailer updated successfully");
                    $this -> result = 1;
                }
            }
            else{
                $this -> result = 0;
                $this -> message = $this -> form_validation -> error_array();
            }
        }
        else{
            $this -> result = 0;
            $this -> message = 'Invalid data';
        }
        
        echo json_encode(array(
            'result' => $this -> result,
            'message' => $this -> message
        ));
    }
    
    # Save Subscription Types for all stores 
    function saveSubscriptionType($retailerId, $promo_premium, $promo_concierge, $promo_messenger, $promo_admanager)
    {
       # Get all stores for the retailer 
        $this -> db -> select('Id, StoreName, RetailerId, StoreTypeId')
            -> from('stores')
            -> where(
                array(
                    'RetailerId' => $retailerId,
                    'IsActive' => 1,
                    'IsRemoved' => 0
                )
        );
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            $stores = $query -> result_array();
            
            foreach ( $stores as $store)
            {
                $storeId = $store['Id'];
                
                $promo_array = array(
                    'RetailerId' => 0,
                    'StoreId' => $storeId,
                    'Standard' => '1',
                    'Premium' => $promo_premium,
                    'Concierge' => $promo_concierge,
                    'Messenger' => $promo_messenger,
                    'AdManager' => $promo_admanager
                );                
                $this -> storemodel -> insert_update_promo($promo_array, $storeId);
                
            } // foreach ( $stores as $store)
        } //  if ($query -> num_rows() > 0)
        
    }
    
    function check_default($array)
    {
        $groupIds = $this->input->post("groupId");
        if( $groupIds )
        {
            return true;
           
        }else{
             return false;
        }
        
    }

    
    

    public function delete($id) {

        //Check if retailer can be deleted ( Only if it has no store formats and stores assigned )
        $store_format_data = $this->storeformatmodel->get_store_formats($id);

        if (empty($store_format_data)) {
            $this->retailermodel->delete_retailer($id);
            $this->session->set_userdata('success_message', "Retailer deleted successfully");
        }
        else
            $this->session->set_userdata('error_message', "Retailer could not be deleted, please delete related store and store formats first");

        redirect('retailers', 'refresh');
    }

    public function change_status($id, $status) {

        $this->retailermodel->change_status($id, $status);
        $this->session->set_userdata('success_message', "Retailer status updated successfully");
        redirect('retailers', 'refresh');
    }

    function file_selected_check() {
        $this->form_validation->set_message('file_selected_check', 'Please select file.');
        if (empty($_FILES['logo']['name'])) {
            return false;
        } else {
            return true;
        }
    }

    function check_uniqueness_email($email) {

        $result = $this->usermodel->check_unique_email($email);

        if ($result) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_uniqueness_email', 'Email address already exist');
            return FALSE;
        }
    }
    
    function check_uniqueness_email_edit($email,$id) {

        $result = $this->usermodel->check_unique_email_edit($email,$id);

        if ($result) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_uniqueness_email_edit', 'Email address already exist');
            return FALSE;
        }
    }

    public function assign_category($retailerId = 0) {
        $this->load->model('admin/categorymodel', '', TRUE);
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $res_array = array();

            //Assign Category
            $this->form_validation->set_rules('category', 'category', 'required|xss_clean');

            if (!$this->form_validation->run() == FALSE) {

                $this->retailermodel->delete_assigned_categories($retailerId);

                $category = $this->input->post('category');

                foreach ($category as $category) {

                    if ($category != '') {
                        //Assign retailer category data
                        $retailer_data = array(
                            'RetailerId' => $retailerId,
                            'CategoryID' => $category,
                            'CreatedBy' => $this->session->userdata('user_id'),
                            'CreatedOn' => date('Y-m-d H:i:s'),
                            'IsActive' => 1
                        );

                        $result = $this->retailermodel->assign_category($retailer_data);

                        array_push($res_array, $result);
                    }
                }

                if (!in_array(0, $res_array))
                    $this->session->set_userdata('success_message', "Category assigned to retailer successfully");
                else
                    $this->session->set_userdata('error_message', "Error occurred while assigning category to retailer");
                redirect('retailers', 'refresh');
            }
        }

        $this->breadcrumbs[] = array('label' => 'Assign Category', 'url' => 'retailers/assign_category');

        $retailer_details = $this->retailermodel->get_retailer_details($retailerId);

        $data['title'] = $retailer_details['CompanyName'] . " - Categories";

        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['categories'] = $this->categorymodel->get_main_categories();

        $data['retailer_assigned_categories'] = $this->retailermodel->get_retailer_categories($retailerId);

        $data['RetailerId'] = $retailerId;

        $this->template->view('admin/retailers/assign_category', $data);
    }

    // Store Formats

    function get_store_category_count($retailer_id) {
        $category_count = $this->retailermodel->get_category_count($retailer_id);
        $store_count = $this->retailermodel->get_store_count($retailer_id);

        $store_html = '<span class="actions">';
        $store_html .= '<a href="storeformat/index/' . $retailer_id . '"><span class="badge">' . $store_count . '</span></a>';
        $store_html .= '</span>';

        $category_html = '<span class="actions">';
        $category_html .= '<a href="retailers/assign_category/' . $retailer_id . '"><span class="badge">' . $category_count . '</span></a>';
        $category_html .= '</span>';

        echo json_encode(array('store' => $store_html, 'category' => $category_html));
    }

}

?>