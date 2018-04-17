<?php

/*
 * Author:AS
 * Purpose:Special Terms and Condition Management
 * Date:13-12-2016
 * Dependency: tandcmanagement.php
 */

class Tandcmanagement extends My_Controller {

    private $result;
    private $message;

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/tandcmodel', '', TRUE);

        $this -> page_title = "Special Terms and Condition Management";
        $this -> breadcrumbs[] = array('label' => 'T & C', 'url' => '/tandcmanagement');
    }

    public function index() {
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'T & C Management', 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/tandc_management/index', $data);
    }

    public function datatable() {
        $this -> datatables -> select("Id as t_id, TermsText")
            -> unset_column('t_id')
            -> from('special_terms')
            -> where(array('IsRemoved' => 0, 'IsActive' => 1))
            -> add_column('Actions', tandc_get_buttons('$1'), 't_id');
        echo $this -> datatables -> generate();
    }

    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> form_validation -> set_rules('terms_text', 'Terms and Conditions', 'trim|required|max_length[300]|min_length[10]|xss_clean');
            if (!$this -> form_validation -> run() == FALSE) {
                $terms_and_conditions = sanitize($this -> input -> post('terms_text'));
                if (trim($terms_and_conditions) != '') {
                    $insert_data = array(
                        'TermsText' => $terms_and_conditions
                    );
                    $is_added = $this -> tandcmodel -> insert_terms($insert_data);
                    if ($is_added) {
                        $this -> session -> set_userdata('success_message', 'Terms and Conditions added successfully');
                        redirect('tandcmanagement', 'refresh');
                        exit(0);
                    }
                    else {
                        $this -> session -> set_userdata('error_message', 'Failed to add Terms and Conditions');
                        redirect('tandcmanagement/add', 'refresh');
                        exit(0);
                    }
                }
                else {
                    $this -> session -> set_userdata('error_message', 'Failed to add Terms and Conditions');
                    redirect('tandcmanagement/add', 'refresh');
                    exit(0);
                }
            }
            else {
                $this -> session -> set_userdata('error_message', 'Failed to validate the form');
                redirect('tandcmanagement/add', 'refresh');
                exit(0);
            }
        }
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'T & C Management', 'url' => '/tandcmanagement');
        $this -> breadcrumbs[1] = array('label' => 'Add', 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/tandc_management/add', $data);
    }

    public function edit($id) {
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'T & C Management', 'url' => '/tandcmanagement');
        $this -> breadcrumbs[1] = array('label' => 'Edit', 'url' => '');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['tandc'] = $this -> tandcmodel -> get_tandc_details($id);

        $html = $this -> load -> view('admin/tandc_management/edit', $data, true);

        echo json_encode(array(
            'html' => $html
        ));
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $this -> form_validation -> set_rules('terms_text', 'Terms and Conditions', 'trim|required|max_length[300]|min_length[10]|xss_clean');
            if (!$this -> form_validation -> run() == FALSE) {
                $terms_and_conditions = sanitize($this -> input -> post('terms_text'));
                if (trim($terms_and_conditions) != '') {
                    $update_data = array(
                        'TermsText' => $terms_and_conditions,
                        'ModifiedOn' => date('Y-m-d H:i:s')
                    );
                    $is_updated = $this -> tandcmodel -> update_terms($update_data, $id);
                    if ($is_updated) {
                        $this -> result = 1;
                        $this -> message = 'Terms and Conditions updated successfully';
                        $this -> session -> set_userdata('success_message', 'Terms and Conditions updated successfully');
                    }
                    else {
                        $this -> result = 0;
                        $this -> message = 'Failed to update Terms and Conditions';
                    }
                }
                else {
                    $this -> result = 0;
                    $this -> message = 'Failed to update Terms and Conditions';
                }
            }
            else {
                $this -> result = 0;
                $this -> message = 'Failed to validate the form';
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

    public function delete_tandc() {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            $id = sanitize($this -> input -> post('id'));
            $update_data = array(
                'IsRemoved' => '1',
                'ModifiedOn' => date('Y-m-d H:i:s')
            );
            $is_updated = $this -> tandcmodel -> update_terms($update_data, $id);
            if ($is_updated) {
                $this -> result = 1;
                $this -> message = 'Terms and Conditions deleted successfully';
            }
            else {
                $this -> result = 0;
                $this -> message = 'Failed to delete Terms and Conditions';
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
}