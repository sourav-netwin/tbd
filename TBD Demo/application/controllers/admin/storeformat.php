<?php

/*
 * Author:PHN
 * Purpose:Store Formats Controller
 * Date:04-09-2015
 * Dependency: storeformat.php
 */

class StoreFormat extends My_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('admin/storemodel', '', TRUE);
        $this->load->model('admin/retailermodel', '', TRUE);
        $this->load->model('admin/storeformatmodel', '', TRUE);
        $this -> load -> model('admin/storegroupmodel', '', TRUE);

        $this->page_title = "Store Format";
    }

    public function index($id = '') {

        $id = ( $id == '' ) ? $this->session->userdata('user_retailer_id') : $id;
        $data['retailer'] = $this->retailermodel->get_retailer_details($id);

        $data['title'] = $data['retailer']['CompanyName'] . " - " . $this->page_title;

        if ($this->session->userdata('user_type') != 3) {
            $this->breadcrumbs[] = array('label' => 'Stores Management', 'url' => '');
        }
        $data['retailers'] = '';
        if ($this->session->userdata('user_type') != 3) {
            $data['retailers'] = $this->retailermodel->get_retailers();
        }

        $this->breadcrumbs[] = array('label' => 'Store Format', 'url' => '/storeformat');

        $data['breadcrumbs'] = $this->breadcrumbs;
        $data['ret_id'] = $id;

        $this->template->view('admin/store_format/index', $data);
    }

    public function datatable($retailer_id = '') {

        $retailer_id = ( $retailer_id == '' ) ? $this->session->userdata('user_retailer_id') : $retailer_id;

        $this->datatables->select("storestypes.StoreType, storestypes.Id as s_id, storestypes.IsActive as active, storestypes.UserCount as UserCount,count(stores.Id) as store_count, storestypes.RetailerId as RetailerId")
                ->unset_column('s_id')
                ->unset_column('active')
                ->unset_column('store_count')
                ->unset_column('RetailerId')
                ->from('storestypes')
                ->join('stores', 'stores.StoreTypeId = storestypes.Id AND stores.IsRemoved=0', 'left')
                ->where('storestypes.RetailerId', $retailer_id)
                ->where('storestypes.IsRemoved', '0')
                ->group_by('storestypes.Id')
                ->add_column('Stores', get_store_format_buttons('$1', '$2', '$3'), "s_id,store_count,RetailerId")
                ->add_column('Users', get_user_count('$1', '$2', '$3'), 'UserCount,storeformat,s_id')
                ->add_column('Actions', get_action_buttons('$1', 'storestypes'), 's_id');

        echo $this->datatables->generate();
    }

    public function delete($id, $store_format_id) {

        $this->storeformatmodel->delete_store_format($id);
        $this->session->set_userdata('success_message', "Store Format deleted successfully");
        if ($this->session->userdata('user_type') == 3)
            redirect('storeformat/index/' . $store_format_id, 'refresh');
        else
            redirect('retailers/' . $store_format_id . '/storeformat/index/', 'refresh');
    }

    public function change_status($id, $status, $store_format_id) {

        $this->storeformatmodel->change_status($id, $status);
        $this->session->set_userdata('success_message', "Store Format status updated successfully");
        if ($this->session->userdata('user_type') == 3)
            redirect('storeformat/index/' . $store_format_id, 'refresh');
        else
            redirect('retailers/' . $store_format_id . '/storeformat/index/', 'refresh');
    }

    public function add($retailer_id = '') {
        $retailer_id = ( $retailer_id == '' ) ? $this->session->userdata('user_retailer_id') : $retailer_id;

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $result = $this->do_upload('logo', 'storeformats', $this->input->post('image-x'), $this->input->post('image-y'), $this->input->post('image-width'), $this->input->post('image-height'));
            if (!isset($result['error'])) {

                $insert_data = array('StoreType' => $this->input->post('storeformat_name'),
                    'RetailerId' => $this->input->post('retailer_id'),
                    'Logo' => $result['upload_data']['file_name'],
                    'IsActive' => 1);

                $result = $this->storeformatmodel->add_store_format($insert_data);
                
                if ($result > 0)
                {
                    # Set the store groups 
                    $groupIds = $this->input->post('groupId');
                    if(count($groupIds)> 0 )
                    {
                        $newStoreFormatId = $result;
                        $result_setGroups = $this->storeformatmodel->set_storeGroups($newStoreFormatId, $groupIds); 
                    }
                    
                    $this->session->set_userdata('success_message', "Store Format added successfully");
                }else
                {
                    $this->session->set_userdata('success_message', "Error while adding Store Format");
                }
                    

                if ($this->session->userdata('user_type') == 3)
                    redirect('storeformat/index/' . $this->input->post('retailer_id'), 'refresh');
                else
                    redirect('retailers/' . $this->input->post('retailer_id') . '/storeformat/index/', 'refresh');
            }
        }
        if ($this->session->userdata('user_type') != 3) {
            $this->breadcrumbs[] = array('label' => 'Stores Management', 'url' => '');
        }

        $this->breadcrumbs[] = array('label' => 'Store Format', 'url' => '/storeformat');

        $this->breadcrumbs[] = array('label' => 'Add Store Format', 'url' => '#');

        $data['title'] = $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['retailer_id'] = $retailer_id;
        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();
        
        $this->template->view('admin/store_format/add', $data);
    }

    public function edit($id = '', $retailer_id = '') {

        $retailer_id = ( $retailer_id == '' ) ? $this->session->userdata('user_retailer_id') : $retailer_id;
        $id;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $edit_data = array('StoreType' => $this->input->post('storeformat_name'),
                'RetailerId' => $this->input->post('retailer_id')
            );

            if (!empty($_FILES['logo']['name'])) {
                $result = $this->do_upload('logo', 'retailers', $this->input->post('image-x'), $this->input->post('image-y'), $this->input->post('image-width'), $this->input->post('image-height'));
                if (!isset($result['error'])) {
                    $edit_data['Logo'] = $result['upload_data']['file_name'];
                }
            }

            $this->storeformatmodel->update_store_format($this->input->post('storeformat_id'), $edit_data);
            
            # Set the store groups 
            $groupIds = $this->input->post('groupId');
            if(count($groupIds)> 0 )
            {
                $storeFormatId = $this->input->post('storeformat_id');
                $result_setGroups = $this->storeformatmodel->set_storeGroups($storeFormatId, $groupIds); 
            }
                    
            $this->session->set_userdata('success_message', "Store Format updated successfully");

            if ($this->session->userdata('user_type') == 3)
                redirect('storeformat/index/' . $this->input->post('retailer_id'), 'refresh');
            else
                redirect('retailers/' . $this->input->post('retailer_id') . '/storeformat/index/', 'refresh');
        }

        if ($this->session->userdata('user_type') != 3) {
            $this->breadcrumbs[] = array('label' => 'Stores Management', 'url' => '');
        }

        $this->breadcrumbs[] = array('label' => 'Store Format', 'url' => '/storeformat');

        $this->breadcrumbs[] = array('label' => 'Edit Store Format', 'url' => '#');

        $data['title'] = $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['retailer_id'] = $retailer_id;

        $data['store_format'] = $this->storeformatmodel->get_storeformat_details($id);
        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();
        $data['storeformat_storegroups'] = $this -> storeformatmodel -> get_storeformat_storegroups($id);
        
        $this->template->view('admin/store_format/edit', $data);
    }

    public function add_store_format_user($retailer_id = '') {
        $retailer_id = ( $retailer_id == '' ) ? $this->session->userdata('user_retailer_id') : $retailer_id;
        if($this -> input -> post('retailer_id')){
            $retailer_id = $this -> input -> post('retailer_id');
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            //Add Retailer
            $this->form_validation->set_rules('email', 'email', 'trim|required|callback_check_uniqueness_email');
            $this->form_validation->set_rules('first_name', 'first_name', 'trim|required|max_length[50]|xss_clean');
            $this->form_validation->set_rules('last_name', 'last_name', 'trim|required|max_length[50]|xss_clean');
            $this->form_validation->set_rules('password', 'password', 'trim|required|matches[confirm_password]');

            if (!$this->form_validation->run() == FALSE) {

                $this->load->model('admin/usermodel', '', TRUE);

                if ($this->input->post('user_type') == '1') {
                    $store_format_user = 1;
                }

                //Add Retailer User Initially
                $role = $this->usermodel->get_user_role('StoreFormat User');

                $user_data = array(
                    'FirstName' => $this->input->post('first_name'),
                    'LastName' => $this->input->post('last_name'),
                    'Email' => $this->input->post('email'),
                    'Password' => MD5($this->input->post('password')),
                    'UserRole' => $role['Id'],
                    'TelephoneFixed' => $this->input->post('contact_tel'),
                );

                $user_id = $this->usermodel->add_user($user_data);

                if ($store_format_user) {
                    //Save the user to the store admin.

                    $store_data = array(
                        'UserId' => $user_id,
                        'StoreTypeId' => $this->input->post('store_format'),
                    );

                    $this->load->model('admin/storeadminmodel', '', TRUE);
                    $result = $this->storeadminmodel->add_admin($store_data);

                    $this->storeformatmodel->update_user_count($this->input->post('store_format'));
                } else {
                    //Save the user to the store admin.

                    $store_data = array(
                        'UserId' => $user_id,
                        'StoreId' => $this->input->post('store'),
                    );

                    $this->load->model('admin/storeadminmodel', '', TRUE);
                    $result = $this->storeadminmodel->add_admin($store_data);


                    $this->storemodel->update_user_count($this->input->post('store'));

                    //Add default wizard status
                    $this->storemodel->add_store_wizard($store_data);
                }
                if ($result > 0)
                    $this->session->set_userdata('success_message', "Store Format User added successfully");
                else
                    $this->session->set_userdata('success_message', "Error while adding Store Format User");

                redirect('users', 'refresh');
            }
        }

            if ($this->session->userdata('user_type') != 3) {
                $this->breadcrumbs[] = array('label' => 'Stores Management', 'url' => '');
            }

            $this->breadcrumbs[] = array('label' => 'Store Format', 'url' => '/storeformat');

            $this->breadcrumbs[] = array('label' => 'Add User', 'url' => '#');

            $data['title'] = $this->page_title;

            $data['breadcrumbs'] = $this->breadcrumbs;

            $data['retailer_id'] = $retailer_id;

            $data['store_formats'] = $this->storeformatmodel->get_store_formats($retailer_id);

            $this->template->view('admin/store_format/add_user', $data);
    }
}
?>