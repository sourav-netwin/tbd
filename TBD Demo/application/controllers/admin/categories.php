<?php

/*
 * Author:PM
 * Purpose:Category Controller
 * Date:03-09-2015
 * Dependency: categorymodel.php
 */

class Categories extends My_Controller {

    private $result;
    private $message;
    function __construct() {
        parent::__construct();
        $this->load->model('admin/categorymodel', '', TRUE);
        $this->load->model('admin/retailermodel', '', TRUE);
        $this -> load -> model('admin/storegroupmodel', '', TRUE);

        $this->page_title = "Categories";
        $this->breadcrumbs[] = array('label' => 'Categories', 'url' => '/categories');
    }

    public function index() {

        $data['retailers'] = $this->retailermodel->get_retailers();

        $data['title'] =  $this->page_title;

        $this -> breadcrumbs[0] = array('label' => 'Product Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Categories', 'url' => '/categories');
        $data['breadcrumbs'] = $this->breadcrumbs;

        $existing_categories = $this->categorymodel->get_all_categories();
        $data['category_arr'] = $data['parent_category'] = array();
        foreach ($existing_categories as $category) {
            $data['category_arr'][$category['ParentCategory']][] = array( 'id' => $category['Id'], 'name' => $category['CategoryName'], 'active' => $category['IsActive'], 'Sequence' => $category['Sequence']);

            if( $category['ParentCategory'] == 0 )
                $data['parent_category'][] = array( 'id' => $category['Id'], 'name' => $category['CategoryName'] );
        }
        sort($data['parent_category']);
        $this->template->view('admin/categories/index', $data);
    }

    public function add( $parent_category_id = 0 ) {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
                
            //Add Category
            $this->form_validation->set_rules('category_title', 'category name', 'trim|required|xss_clean');

            $parent_category = ( $parent_category_id > 0 ) ? $this->input->post('existing_parent_id') : $this->input->post('parent_category');
            if( $parent_category == 0 )
                $this->form_validation->set_rules('category_icon', 'Document', 'callback_file_selected_check');

            if (!$this->form_validation->run() == FALSE) {
                if( $parent_category == 0 )
                {
                    $result = $this->do_upload('category_icon', 'categories',$this->input->post('image-x'),$this->input->post('image-y'),$this->input->post('image-width'),$this->input->post('image-height'));
                    if( !isset( $result['error'] ) )
                    {
                        $sequence = $this->categorymodel->get_sequence( $parent_category );

                        $insert_data = array(
                                    'CategoryName' => $this->input->post('category_title'),
                                    'CategoryDescription' => $this->input->post('category_description'),
                                    'CategoryIcon' => $result['upload_data']['file_name'],
                                    'ParentCategory' => $parent_category,
                                    'Sequence' => $sequence,
                                    'CreatedBy' => $this->session->userdata('user_id'),
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'IsActive' => 1
                                );

                        $result = $this->categorymodel->add_category( $insert_data );
                        if( $result > 0 ){
                            
                            # Set the store groups 
                            $groupIds = $this->input->post('groupId');
                            if(count($groupIds)> 0 )
                            {
                                $newCategoryId = $result;
                                $result_setGroups = $this->categorymodel->set_storeGroups($newCategoryId, $groupIds); 
                            }
                            $this->session->set_userdata ( 'success_message',"Category added successfully" );
                        }else{
                            $this->session->set_userdata ( 'error_message',"Error occurred while adding category" );
                        }
                            
                        
                            
                        redirect('categories', 'refresh');
                    }
                    else
                    {
                        // code to display error while image upload
                        $this->session->set_userdata ( 'error_message',$result['error'] );
                    }
                }
                else
                {
                    $sequence = $this->categorymodel->get_sequence( $parent_category );
                    $insert_data = array(
                                    'CategoryName' => $this->input->post('category_title'),
                                    'CategoryDescription' => $this->input->post('category_description'),
                                    'ParentCategory' => $parent_category,
                                    'Sequence' => $sequence,
                                    'CreatedBy' => $this->session->userdata('user_id'),
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'IsActive' => 1
                                );

                    $result = $this->categorymodel->add_category( $insert_data );
                    if( $result > 0 )
                        $this->session->set_userdata ( 'success_message',"Category added successfully" );
                    else
                        $this->session->set_userdata ( 'error_message',"Error occurred while adding category" );
                    redirect('categories', 'refresh');
                }
            }
        }

        $this -> breadcrumbs[0] = array('label' => 'Product Management', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Categories', 'url' => '/categories');
        $this->breadcrumbs[2] = array('label' => 'Add Category', 'url' => 'categories/add');

        $data['title'] =  $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $existing_categories = $this->categorymodel->get_all_categories();
        $data['category_arr'] = array();
        foreach ($existing_categories as $category) {
            $data['category_arr'][$category['ParentCategory']][] = array( 'id' => $category['Id'], 'name' => $category['CategoryName'] );
        }

        $data['parent_category'] = $parent_category_id;

        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();
        
        $this->template->view('admin/categories/add', $data);
    }

    public function edit($id) {

        $this->breadcrumbs[] = array('label' => 'Edit Category', 'url' => 'categories/edit');

        $data['title'] =  $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $existing_categories = $this->categorymodel->get_all_categories();
        $data['category_arr'] = array();
        foreach ($existing_categories as $category) {
            $data['category_arr'][$category['ParentCategory']][] = array( 'id' => $category['Id'], 'name' => $category['CategoryName'] );
        }

        $data['category_details'] = $this->categorymodel->get_category_details( $id );
        $data['current_parent_details'] = $this->categorymodel->get_category_details( $data['category_details']['ParentCategory'] );
        $data['store_groups'] = $this -> storegroupmodel -> get_store_groups();
        $data['categories_storegroups'] = $this -> categorymodel -> get_category_storegroups($id);
        
        $this->load->view('admin/categories/edit', $data);
    }
    
    public function edit_post($id){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            //Update Category
            $this->form_validation->set_rules('category_title', 'category name', 'trim|required|xss_clean');

//            if( empty($_FILES['category_icon']['name']) && $this->input->post('old_icon') == '' && $this->input->post('parent_category') == 0 )
//                $this->form_validation->set_rules('category_icon', 'Document', 'callback_file_selected_check');

            if (!$this->form_validation->run() == FALSE) {
                
               
               
                $category_details = $this->categorymodel->get_category_details( $id );

                //Change sequence only if parent category changed
                if( $this->input->post('parent_category') == $category_details['ParentCategory'] )
                {
                    $sequence = $category_details['Sequence'];
                }
                else
                {
                    $sequence = $this->categorymodel->get_sequence( $this->input->post('parent_category') );
                }
                if( !empty($_FILES['category_icon']['name']) )
                {
                    $result = $this->do_upload('category_icon', 'categories', $this->input->post('image-x'),$this->input->post('image-y'),$this->input->post('image-width'),$this->input->post('image-height'));
                     
                    if( !isset( $result['error'] ) )
                    {
                        $edit_data = array(
                            'CategoryName' => $this->input->post('category_title'),
                            'CategoryDescription' => $this->input->post('category_description'),
                            'CategoryIcon' => $result['upload_data']['file_name'],
                            'ParentCategory' => $this->input->post('parent_category'),
                            'Sequence' => $sequence,
                            'ModifiedBy' => $this->session->userdata('user_id'),
                            'ModifiedOn' => date('Y-m-d H:i:s')
                        );

                        $result = $this->categorymodel->update_category( $id, $edit_data );
                        
                        if($this->input->post('parent_category') == 0 )
                        {
                          # Set the store groups 
                            $groupIds = $this->input->post('groupId');
                            if(count($groupIds)> 0 )
                            {
                                $result_setGroups = $this->categorymodel->set_storeGroups($id, $groupIds); 
                            }  
                        }
                        
                            
                        $this->session->set_userdata ( 'success_message',"Category updated successfully" );
                        $this -> result = 1;
                    }
                    else
                    {
                        // code to display error while image upload
                        //$this->session->set_userdata ( 'error_message',$result['error'] );
                        $this -> result = 0;
                        $this -> message = $result['error'];
                    }
                }
                else
                {
                    $edit_data = array(
                        'CategoryName' => $this->input->post('category_title'),
                        'CategoryDescription' => $this->input->post('category_description'),
                        'ParentCategory' => $this->input->post('parent_category'),
                        'Sequence' => $sequence,
                        'ModifiedBy' => $this->session->userdata('user_id'),
                        'ModifiedOn' => date('Y-m-d H:i:s')
                    );

                    $result = $this->categorymodel->update_category( $id, $edit_data );
                    
                    if($this->input->post('parent_category') == 0 )
                    {
                        # Set the store groups 
                        $groupIds = $this->input->post('groupId');
                        if(count($groupIds)> 0 )
                        {
                            $result_setGroups = $this->categorymodel->set_storeGroups($id, $groupIds); 
                        }  
                    }
                        
                    $this->session->set_userdata ( 'success_message',"Category updated successfully" );
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

    public function delete($id) {

        $this->categorymodel->delete_category($id);
        $this->session->set_userdata ( 'success_message',"Category deleted successfully" );
        redirect('categories', 'refresh');
    }

    public function change_status($id, $status) {

        $this->categorymodel->change_status($id, $status);
        $this->session->set_userdata ( 'success_message',"Category status updated successfully" );
        redirect('categories', 'refresh');
    }

    function file_selected_check()
    {
        $this->form_validation->set_message('file_selected_check', 'Please upload category icon.');
        if (empty($_FILES['category_icon']['name'])) {
            return false;
        }else{
            return true;
        }
    }

    public function update_category_sequence()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');

        $this->categorymodel->update_category_sequence($id, $type);

        echo "success";
    }
}

?>