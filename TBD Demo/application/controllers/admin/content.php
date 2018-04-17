<?php

/*
 * Author:PM
 * Purpose:Products Controller
 * Date:07-09-2015
 * Dependency: contentmodel.php
 */

class Content extends My_Controller {

    private $message;
    private $result;
    function __construct() {
        parent::__construct();
       
        $this->load->model('admin/contentmodel', '', TRUE);

        $this->page_title = "Content";
        $this->breadcrumbs[] = array('label' => 'Content', 'url' => '/content');
    }

    public function index() {

        $data['title'] =  $this->page_title;

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Content Management', 'url' => '/content');
        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['content'] = $this->contentmodel->get_content_menus();
        
        $this->template->view('admin/content/index', $data);
    }

    public function add( $menu_id ) 
    {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            //Add Content
            $this->form_validation->set_rules('browser_title', 'browser title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('page_title', 'page title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('meta_description', 'meta description', 'trim|required|xss_clean');
            $this->form_validation->set_rules('keywords', 'keywords', 'trim|required|xss_clean');
            $this->form_validation->set_rules('content', 'content', 'trim|required');
            
            if (!$this->form_validation->run() == FALSE) {
                $data = array(
                            'MenuId' => $menu_id,
                            'BrowserTitle' => $this->input->post('browser_title'),
                            'PageTitle' => $this->input->post('page_title'),
                            'MetaDescription' => $this->input->post('meta_description'),
                            'Keywords' => $this->input->post('keywords'),
                            'Content' => $this->input->post('content'),
                            'CreatedBy' => $this->session->userdata('user_id'),
                            'CreatedOn' => date('Y-m-d H:i:s')
                        );
                $result = $this->contentmodel->add_content( $data );
                if( $result > 0 )
                    $this->session->set_userdata ( 'success_message',"Content added successfully" );
                else
                    $this->session->set_userdata ( 'success_message',"Error while adding content" );
                redirect('content', 'refresh');
            }
        }

        $this->breadcrumbs[] = array('label' => 'Add Content', 'url' => 'content/add');

        $data['title'] =  $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['content'] = $this->contentmodel->get_menu_details( $menu_id );

        $this->template->view('admin/content/add', $data);
    }

    public function edit( $menu_id ) 
    {
        
        $this->breadcrumbs[] = array('label' => 'Edit Content', 'url' => 'content/edit');

        $data['title'] =  $this->page_title;

        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['content'] = $this->contentmodel->get_menu_details( $menu_id );

        $data['content_details'] = $this->contentmodel->get_menu_content( $menu_id );

        $this->load->view('admin/content/edit', $data);
    }
    
    public function edit_post($menu_id){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            
            //Edit Content
            $this->form_validation->set_rules('browser_title', 'browser title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('page_title', 'page title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('meta_description', 'meta description', 'trim|required|xss_clean');
            $this->form_validation->set_rules('keywords', 'keywords', 'trim|required|xss_clean');
            $this->form_validation->set_rules('content', 'content', 'trim|required');
            if (!$this->form_validation->run() == FALSE) {
                $data = array(
                            'MenuId' => $menu_id,
                            'BrowserTitle' => $this->input->post('browser_title'),
                            'PageTitle' => $this->input->post('page_title'),
                            'MetaDescription' => $this->input->post('meta_description'),
                            'Keywords' => $this->input->post('keywords'),
                            'Content' => $this->input->post('content'),
                            'CreatedBy' => $this->session->userdata('user_id'),
                            'CreatedOn' => date('Y-m-d H:i:s')
                        );
                $result = $this->contentmodel->update_content( $menu_id, $data );
                $this->session->set_userdata ( 'success_message',"Content updated successfully" );
                $this -> result = 1;
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
}
?>