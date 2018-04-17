<?php

/*
 * Author:AS
 * Purpose:Mobile Slider Controller
 * Date:06-01-2017
 * Dependency: mobilemobileslidermodel.php
 */

class Mobileslider extends My_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('admin/mobileslidermodel', '', TRUE);

        $this -> page_title = "Mobile Sliders";
        $this -> breadcrumbs[] = array('label' => 'Mobile Sliders', 'url' => '/mobilesliders');
    }

    public function index() {
        $data['title'] = $this -> page_title;

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Mobile Slider', 'url' => '/mobileslider');
        $data['breadcrumbs'] = $this -> breadcrumbs;

        $data['sliders'] = $this -> mobileslidermodel -> get_sliders();

        $data['sequence_data'] = $this -> mobileslidermodel -> get_max_min_sequence();

        $this -> template -> view('admin/mobile_slider/index', $data);
    }

    public function add() {

        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {

            // $this->form_validation->set_rules('slider_image', 'Document', 'callback_file_selected_check');
            // if (!$this->form_validation->run() == FALSE) {
            //     echo"In if";die();
            $result = $this -> do_upload_slider('mobile_slider_image', 'mobile_sliders', '', '', '540', '882');
            if (!isset($result['error'])) { 

                $data = array(
                    'Image' => $result['upload_data']['file_name'],
                    'Text' => sanitize($this -> input -> post('slider_text')),
                    'Color' => sanitize($this -> input -> post('slider_color')),
                    'BgColor' => sanitize($this -> input -> post('slider_bg_color')),
                    'Sequence' => $this -> mobileslidermodel -> get_sequence(),
                    'CreatedBy' => $this -> session -> userdata('user_id')
                );

                $result = $this -> mobileslidermodel -> add_slider($data);
                if ($result > 0)
                    $this -> session -> set_userdata('success_message', "Slider added successfully");
                else
                    $this -> session -> set_userdata('success_message', "Error while adding slider");

                redirect('mobileslider', 'refresh');
            } else {
                // code to display error while image upload
                $this -> session -> set_userdata('error_message', $result['error']);
            }
            // }
        }

        $this -> breadcrumbs[0] = array('label' => 'System Settings', 'url' => '');
        $this -> breadcrumbs[1] = array('label' => 'Mobile Slider', 'url' => '/mobileslider');
        $this -> breadcrumbs[2] = array('label' => 'Add Slider', 'url' => 'mobileslider/add');

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> template -> view('admin/mobile_slider/add', $data);
    }

    public function delete($id) {

        $this -> mobileslidermodel -> delete_slider($id);
        $this -> session -> set_userdata('success_message', "Slider deleted successfully");
        redirect('mobileslider', 'refresh');
    }

    public function change_status($id, $status) {

        $this -> mobileslidermodel -> change_status($id, $status);
        $this -> session -> set_userdata('success_message', "Slider status updated successfully");
        redirect('slider', 'refresh');
    }

    // function file_selected_check() {
    //     echo "In fsdf"; die();
    //     $this->form_validation->set_message('file_selected_check1', 'Please upload slider image.');
    //     if (empty($_FILES['slider_image']['name'])) {
    //         echo"In 1213"; die();
    //         return false;
    //     } else {
    //         echo"In 124564613"; die();
    //         return true;
    //     }
    // }

    function update_slider_sequence() {
        $id = $this -> input -> post('id');
        $type = $this -> input -> post('type');
        $update_array = json_decode($this -> input -> post('update_data'));

        $this -> mobileslidermodel -> update_slider_sequence($id, $type, $update_array);

        echo "success";
    }

    public function edit($id) {

        $data = $this -> mobileslidermodel -> get_sider_details($id);
        $this -> breadcrumbs[] = array('label' => 'Edit Slider', 'url' => 'mobileslider/edit/' . $id);

        $data['title'] = $this -> page_title;

        $data['breadcrumbs'] = $this -> breadcrumbs;

        $this -> load -> view('admin/mobile_slider/edit', $data);
    }

    public function edit_post($id) {
        if ($this -> input -> server('REQUEST_METHOD') == 'POST') {
            //Edit User

            $old_photo = sanitize($this -> input -> post('old_photo'));

            $image_name = $old_photo;
            if (!empty($_FILES['mobile_slider_image']['name'])) {
                $result = $this -> do_upload_slider('mobile_slider_image', 'mobile_sliders', '', '', '540', '882');
                if (!isset($result['error'])) {
                    $image_name = $result['upload_data']['file_name'];
                }
            }
            if (!isset($result['error'])) {
                $edit_data = array(
                    'Image' => $image_name,
                    'Text' => sanitize($this -> input -> post('slider_text')),
                    'Color' => sanitize($this -> input -> post('slider_color')),
                    'BgColor' => sanitize($this -> input -> post('slider_bg_color')),
                    'ModifiedBy' => $this -> session -> userdata('user_id'),
                    'ModifiedOn' => date('Y-m-d H:i:s')
                );
                $this -> mobileslidermodel -> update_slider_image($id, $edit_data);
                $this -> session -> set_userdata('success_message', "Slider updated successfully");

                $this -> result = 1;
            }
            else {
                // code to display error while image upload
                //$this->session->set_userdata('error_message', $result['error']);
                $this -> result = 0;
                $this -> message = $result['error'];
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

    function file_selected_check() {

        $this -> form_validation -> set_message('file_selected_check', 'Please upload slider image.');
        if (empty($_FILES['slider_image']['name'])) {
            return false;
        }
        else {
            return true;
        }
    }
}
