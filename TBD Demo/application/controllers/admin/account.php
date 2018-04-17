<?php

/*
 * Author:PHN
 * Purpose:User Controller
 * Date:26-08-2015
 * Dependency: usermodel.php
 */

class Account extends My_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('admin/usermodel', '', TRUE);

        $this->page_title = "My Profile";
        $this->breadcrumbs[] = array('label' => 'My Profile', 'url' => '/account/profile');
    }

    public function profile() {
        $data = array();


        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            //Update the user profile

            $this->form_validation->set_rules('first_name', 'first name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            $this->form_validation->set_rules('last_name', 'last name', 'trim|required|max_length[50]|callback_validate_name|xss_clean');
            if ($this->input->post('old_photo') == '' && empty($_FILES['profile_image']['name']))
                $this->form_validation->set_rules('profile_image', 'Document', 'callback_file_selected_check');

            if (!$this->form_validation->run() == FALSE) {

                if (!empty($_FILES['profile_image']['name'])) {
                    $result = $this->do_upload('profile_image', 'users', $this->input->post('image-x'), $this->input->post('image-y'), $this->input->post('image-width'), $this->input->post('image-height'));
                    if (!isset($result['error'])) {
                        $data = array(
                            'FirstName' => $this->input->post('first_name'),
                            'LastName' => $this->input->post('last_name'),
                            'ProfileImage' => $result['upload_data']['file_name']
                        );

                        $this->session->set_userdata('user_image', $result['upload_data']['file_name']);

                        $result = $this->usermodel->update_user_profile($this->session->userdata('user_id'), $data);
                    } else {
                        // code to display error while image upload
                        $this->session->set_userdata('error_message', $result['error']);
                    }
                } else {
                    $data = array(
                        'FirstName' => $this->input->post('first_name'),
                        'LastName' => $this->input->post('last_name')
                    );

                    $result = $this->usermodel->update_user_profile($this->session->userdata('user_id'), $data);
                }


                $this->session->set_userdata('user_first_name', $this->input->post('first_name'));
                $this->session->set_userdata('user_last_name', $this->input->post('last_name'));
                $this->session->set_userdata('user_full_name', $this->input->post('first_name') . " " . $this->input->post('last_name'));

                $this->session->set_userdata('success_message', "User profile updated successfully");
            }
        }
        $data = $this->usermodel->get_user_details($this->session->userdata('user_id'));
        $this->breadcrumbs[] = array('label' => 'Edit Profile', 'url' => '/account/profile');
        $data['title'] = $this->page_title;
        $data['breadcrumbs'] = $this->breadcrumbs;

        $this->template->view('admin/account/profile', $data);
    }

    public function change_password() {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('old_password', 'old_password', 'trim|required|callback_check_database');
            $this->form_validation->set_rules('password', 'password', 'trim|required|matches[confirm_password]');
            $this->form_validation->set_rules('confirm_password', 'confirm password', 'trim|required');

            if ($this->form_validation->run() == TRUE) {

                $new_password = $this->input->post('password');

                $result = $this->usermodel->change_password($this->session->userdata('user_id'), $new_password);

                if ($result) {
                    $this->session->set_userdata('success_message', "Password changed successfully");
                }
            }
        }
        $this->breadcrumbs[] = array('label' => 'Change Password', 'url' => 'account/change_password');
        $data['title'] = $this->page_title;
        $data['breadcrumbs'] = $this->breadcrumbs;
        $this->template->view('admin/account/change_password', $data);
    }

    public function check_database($password) {

        $result = $this->usermodel->check_old_password($this->session->userdata('user_id'), $password);

        if ($result) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'The current password is invalid');
            return FALSE;
        }
    }

    function file_selected_check() {

        $this->form_validation->set_message('file_selected_check', 'Please upload user image.');
        if (empty($_FILES['profile_image']['name'])) {
            return false;
        } else {
            return true;
        }
    }

}

?>