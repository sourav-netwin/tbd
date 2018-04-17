<?php

/*
 * Author:PM
 * Purpose:Email Template Controller
 * Date:11-09-2015
 * Dependency: emailtemplatemodel.php
 */

class EmailTemplate extends My_Controller {

    private $message;
    private $result;
    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/emailtemplatemodel', '', TRUE);

        $this -> page_title = "Email Template";
        $this -> breadcrumbs[] = array('label' => 'Email Template', 'url' => '/emailtemplate');
        if ($this -> session -> userdata('user_type') == 6) {
            $this -> check_wizard_navigation();
        }
    }

    public function index() {

        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Email Template', 'url' => '/emailtemplate');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/email_template/index', $data);
    }

    public function datatable() {

        $this -> datatables -> select("Id, FromEmail, ToEmail, if( EmailTo = 1, 'User', 'Admin' ) as EmailTo, Title, IsActive as active", FALSE)
            -> unset_column('Id')
            -> unset_column('active')
            -> from('emailtemplate')
            -> add_column('Actions', get_edit_button('$1', 'emailtemplate'), 'Id');

        echo $this -> datatables -> generate();
    }

    public function edit($id) {

        $this -> breadcrumbs[] = array('label' => 'Edit Email Template', 'url' => 'emailtemplate/edit/' . $id);

        $data = $this -> emailtemplatemodel -> get_email_template_details($id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> load -> view('admin/email_template/edit', $data);
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            //Add Retailer
            $this -> form_validation -> set_rules('email_from', 'email from', 'trim|required|valid_email|xss_clean');
            $email_is = $this -> input -> post('email_is');
            if($email_is == '1'){
                $this->form_validation->set_rules('email_to', 'email to', 'trim|required|valid_email|xss_clean');
            }
            $this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('content', 'content', 'trim|required|xss_clean');

            if (!$this -> form_validation -> run() == FALSE) {

                if ($this -> input -> post('email_to')) {
                    $email_to = $this -> input -> post('email_to');
                }
                else {
                    $email_to = "-";
                }

                $edit_data = array('FromEmail' => $this -> input -> post('email_from'),
                    'ToEmail' => $email_to,
                    'Title' => $this -> input -> post('title'),
                    'Content' => $this -> input -> post('content'),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s'));

                $result = $this -> emailtemplatemodel -> update_email_template($id, $edit_data);
                $this -> session -> set_userdata('success_message', "Email template updated successfully");

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

        $this -> emailtemplatemodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Email template status updated successfully");
        redirect('emailtemplate', 'refresh');
    }
}

?>